<?php
namespace App\Controllers;

/**
 * Controller class handling operations related to favorite animes for users.
 */
class FavoriteController
{
    private static $instance = null;
    
    /**
     * Private constructor to prevent multiple instances.
     *
     * This constructor is empty to prevent creating multiple instances of the controller.
     */
    private function __construct() {}
    
    /**
     * Get the single instance of the class.
     *
     * This method ensures that only one instance of FavoriteController exists. If an instance is already
     * created, it will return that instance; otherwise, it will create a new one.
     *
     * @return FavoriteController The single instance of the FavoriteController.
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new FavoriteController();
        }
        return self::$instance;
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Rendering
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Render the index view for managing favorite animes.
     *
     * This method manages the rendering of the favorite animes page. It handles toggling of favorites,
     * applies any active filters, and renders the appropriate view based on whether there are favorite
     * animes to display or not.
     */
    public function index()
    {
        $logger = LoggingController::getInstance();
        
        $this->handleToggle(); // Checks if the anime has been removed from favorites and saves changes to the DB
        
        $filter = FilterController::getInstance();
        $filter->index(); // Prints filter and it's current settings

        // Render view if user has favorite animes
        if($this->hasFavorites($_SESSION['userID'])){
            $ac = AnimeController::getInstance();
            $animes = $this->getFavoriteAnimes(
                $_SESSION['userID'],
                $filter->getTitle(),
                $filter->getMinRatingCount(),
                $filter->getGenres(),
                $filter->getGenreFilterBehavior(),
                $filter->getOrderBy());
            
            if(count($animes) > 0) {
                require_once __DIR__ . '/../views/favorites.php';
            } else { // Prints info if filter prevents animes from being shown
                $logger->log("You have marked animes as favorites, but they are not displayed due to the filters you have set.");
            }
        } else { // Prints info if there are no favorite animes
            $logger->log("You haven't marked any anime as your favorites yet.");
        }
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Functions to get animes
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Get a list of favorite animes for the user.
     *
     * This function retrieves a list of favorite animes for the specified user, optionally filtered by title,
     * minimum rating count, genres, genre filter mode, and order.
     *
     * @param int $userID The ID of the user.
     * @param string $title Optional. Title filter for animes.
     * @param int $minRatingCount Optional. Minimum rating count filter.
     * @param array $genres Optional. Array of genre names to filter by.
     * @param string $genreFilterMode Optional. Filter mode for genres ('or' for any, 'and' for all).
     * @param string $orderBy Optional. Order by criteria ('rating-asc' or 'rating-desc').
     * @return array Array of Anime objects representing the user's favorite animes.
     */
    public function getFavoriteAnimes($userID, $title = '', $minRatingCount = 30, $genres = array(), $genreFilterMode = 'or', $orderBy = 'rating-desc') {
        $ac = AnimeController::getInstance();
        
        // Prepare SQL to select all animes which are marked as favorite by user
        $sql = "SELECT a.*
                FROM animes a
                JOIN user_anime ua ON a.ID = ua.animeID
                JOIN users u ON u.ID = ua.userID
                WHERE u.ID = :userid";
        
        // If title is set as filter option the SQL is altered
        $params = array('userid' => $userID);
        if(!empty($title)){
            $sql .= " AND a.title like :animetitle";
            $params['animetitle'] = '%'.$title.'%';
        }
        $results = fetchAll($sql, $params);
        $animes = $ac->buildAnimes($results); // Get a list with Anime objects
        
        // Filter animes by rating count
        $animes = $ac->filterByVotes($animes, $minRatingCount);
        
        // Filter animes by genres
        $animes = $ac->filterByGenres($animes, $genres, $genreFilterMode);
        
        // Sort animes
        switch ($orderBy) {
            case 'rating-asc':
                $animes = $ac->orderByRating($animes);
                break;
            case 'rating-desc':
                $animes = $ac->orderByRating($animes, false);
                break;
        }
        
        return $animes;
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Functions to toggle, add or remove favorites
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Toggle the favorite status of an anime.
     *
     * This function handles the toggling of an anime's favorite status for the current user. It checks
     * if a toggle action has been requested via POST, then determines whether to add or remove the anime
     * from the user's favorites based on its current status.
     */
    public function handleToggle() {
        if(isset($_POST['toggle-favorite'])) {
            $this->toggleFavorite($_POST['toggle-favorite'], $_SESSION['userID']);
        }
    }
    
    /**
     * Toggle the favorite status of an anime for the user.
     *
     * This function toggles the favorite status of an anime for the specified user. It checks if the anime
     * is currently a favorite and removes it, or adds it if not.
     *
     * @param mixed $anime The anime ID or object to toggle.
     * @param int $userID The ID of the user.
     */
    public function toggleFavorite($anime, $userID) {
        $logger = LoggingController::getInstance();
        $ac = AnimeController::getInstance();
        $animeID = $ac->getAnimeId($anime);
        
        $isfavorite = $this->isFavorite($animeID, $userID);
        $title = $ac->getAnimeById($animeID)->getTitle();
        
        if($animeID > 0) {
            if($isfavorite){
                if($this->removeFavorite($animeID, $userID)) {
                    $logger->logInfo('Successfully removed <span style="font-weight:bold; color: #fff; font-style: italic;">' . $title . '</span> from your favorites.');
                } else {
                    $logger->logError('Cannot remove <span style="font-weight:bold; color: #fff; font-style: italic;">' . $title . '</span> from your favorites.');
                }
            } else {
                if($this->addFavorite($animeID, $userID)) {
                    $logger->logInfo('Successfully added <span style="font-weight:bold; color: #fff; font-style: italic;">' . $title . '</span> to your favorites.');
                } else {
                    $logger->logError('Cannot add <span style="font-weight:bold; color: #fff; font-style: italic;">' . $title . '</span> to your favorites.');
                }
            }
        }
    }
    
    /**
     * Add an anime to the user's favorite list.
     *
     * This function adds an anime to the user's favorite list.
     *
     * @param mixed $anime The anime ID or object to add to favorites.
     * @param int $userID The ID of the user.
     * @return bool True if the anime was successfully added to favorites, false otherwise.
     */
    public function addFavorite($anime, $userID) {
        $ac = AnimeController::getInstance();
        $animeID = $ac->getAnimeId($anime);
        
        if($animeID > 0) {
            // Prepare sql with anime ID
            $sql = "INSERT INTO user_anime (userID, animeID)
                    VALUES (:userid, :animeid)";
            $result = execute($sql, ['userid' => $userID, 'animeid' => $animeID]);
            
            if($result > 0) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Remove an anime from the user's favorite list.
     *
     * This function removes an anime from the user's favorite list.
     *
     * @param mixed $anime The anime ID or object to remove from favorites.
     * @param int $userID The ID of the user.
     * @return bool True if the anime was successfully removed from favorites, false otherwise.
     */
    public function removeFavorite($anime, $userID) {
        $ac = AnimeController::getInstance();
        $animeID = $ac->getAnimeId($anime);
        
        if($animeID > 0) {
            // Prepare sql with anime ID
            $sql = "DELETE FROM user_anime
                    WHERE userID = :userid
                    AND animeID = :animeid";
            $result = execute($sql, ['userid' => $userID, 'animeid' => $animeID]);
            
            if($result > 0) {
                return true;
            }
        }
        return false;
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Functions to verify favorite status
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Check if a user has any favorite animes.
     *
     * This function checks if the specified user has any animes marked as favorites.
     *
     * @param int $userID The ID of the user.
     * @return bool True if the user has favorite animes, false otherwise.
     */
    public function hasFavorites($userID) {
        $sql = "SELECT count(*) as count
                FROM user_anime
                WHERE userID = :userid";
        $result = fetch($sql, ['userid' => $userID]);
        
        if($result['count'] > 0) {
            return true;
        }
        return false;
    }
    
    /**
     * Check if an anime is marked as a favorite by the user.
     *
     * This function checks if the specified anime is marked as a favorite by the specified user.
     *
     * @param mixed $anime The anime ID or object to check.
     * @param int $userID The ID of the user.
     * @return bool True if the anime is a favorite of the user, false otherwise.
     */
    public function isFavorite($anime, $userID) {
        $ac = AnimeController::getInstance();
        $animeID = $ac->getAnimeId($anime);
        
        if($animeID > 0) {
            // Prepare sql with anime ID
            $sql = "SELECT *
                    FROM user_anime
                    WHERE userID = :userid
                    AND animeID = :animeid";
            $result = execute($sql, ['userid' => $userID, 'animeid' => $animeID]);
            
            if($result > 0) {
                return true;
            }
        }
        return false;
    }

    
    /* ----------------------------------------------------------------------------------------------------
     * Other/Helper Functions
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Get IDs of all favorite animes for the user.
     *
     * This function retrieves an array of anime IDs that are marked as favorites by the specified user.
     *
     * @param int $userID The ID of the user.
     * @return array Array of anime IDs that are favorites of the user.
     */
    public function getFavoriteAnimeIDs($userID) {
        $favorites = array();
        $sql = "SELECT animeID
                FROM user_anime
                WHERE userID = :userid";
        $results = fetchAll($sql, ['userid' => $userID]);
        foreach ($results as $favorite) {
            $favorites[] = $favorite['animeID'];
        }
        return $favorites;
    }
    
}
