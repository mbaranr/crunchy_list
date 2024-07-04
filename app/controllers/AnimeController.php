<?php
namespace App\Controllers;
use App\Models\Anime;

/**
 * Controller class for managing operations related to Anime entities.
 */
class AnimeController
{
    private static $instance = null;
    
    // Private constructor to prevent multiple instances
    private function __construct() {}
    
    /**
     * Get the single instance of the class.
     *
     * This method ensures that only one instance of AnimeController exists. If an instance is already
     * created, it will return that instance; otherwise, it will create a new one.
     *
     * @return AnimeController The single instance of the AnimeController.
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new AnimeController();
        }
        return self::$instance;
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Rendering
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Handle the request to show the details of an Anime.
     *
     * This function manages the logic to display the details of an Anime. It checks if an Anime ID is provided
     * in the GET parameters, attempts to retrieve the Anime by its ID, and handles errors if the ID is missing
     * or invalid. If the Anime exists, it prepares the necessary controllers for further actions and renders
     * the Anime view.
     */
    public function index()
    {
        $logger = LoggingController::getInstance();
        
        // Print error if ID is not given
        if (empty($_GET['id'])) {
            $logger->logError('Cannot show details as there is no anime id provided.');
        } else {
            // Try to get anime by ID
            $anime = $this->getAnimeById($_GET['id']);
            
            // Print error message if anime with given ID doesn't exist
            if (empty($anime)) {
                $logger->logError("The anime with the ID '" . $_GET['id'] . "' does not exist.");
            } else {
                $fc = FavoriteController::getInstance(); // Needed to check if the selected anime is a favorite of the user
                $gc = GenreController::getInstance(); // Needed to get information about anime-related genres
                
                $fc->handleToggle(); // Checks if the anime has been added or removed from favorites and saves changes to the DB
                
                require_once __DIR__ . '/../views/anime.php'; // Render the view
            }
        }
    }

    
    /* ----------------------------------------------------------------------------------------------------
     * Functions to get animes
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Retrieves a list of animes filtered by the given title.
     *
     * This function queries the database for animes that match the specified title.
     * If no title is provided, it returns all animes.
     *
     * @param string $title Optional. The title or part of the title to filter animes by. Default is an empty string, which means no filtering by title.
     * @return array An array of anime objects that match the given title filter.
     */
    public function getAnimes($title = '') {
        // Construct the base SQL query to select all animes
        $sql = "SELECT * FROM animes";
        
        // Check if a title filter is provided
        if(!empty($title)){
            // Append the WHERE clause to filter by title
            $sql .= " WHERE title like :animetitle";
            // Execute the query with the title parameter
            $results = fetchAll($sql, ['animetitle' => '%' . $title . '%']);
        } else {
            // Execute the query without any filtering
            $results = fetchAll($sql);
        }
        
        // Build and return the array of anime objects from the query results
        return $this->buildAnimes($results);
    }
    
    /**
     * Retrieves a single anime by its ID.
     *
     * This function queries the database for an anime that matches the specified ID.
     * It returns the anime object corresponding to the given ID.
     *
     * @param int $id The ID of the anime to retrieve.
     * @return object|null The anime object if found, or null if no anime with the given ID exists.
     */
    public function getAnimeById($id) {
        // Construct the SQL query to select the anime with the given ID
        $sql = "SELECT * FROM animes WHERE ID = :id";
        
        // Execute the query with the ID parameter
        $result = fetch($sql, ['id' => $id]);
        
        // Build and return the anime object from the query result
        return $this->buildAnime($result);
    }
    
    /**
     * Retrieves animes by genre name.
     *
     * This function queries the database to find all animes associated with a specified genre name.
     * It returns an array of anime objects that belong to the given genre.
     *
     * @param string $genrename The name of the genre to filter animes by.
     * @return array An array of anime objects that belong to the specified genre.
     */
    public function getAnimeByGenreName($genreName) {
        // Construct the SQL query to select animes associated with the given genre name
        $sql = "SELECT a.*
                FROM animes a
                JOIN anime_genre ag ON a.ID = ag.animeID
                JOIN genres g ON ag.genreID = g.ID
                WHERE g.name = :genrename";
        
        // Execute the query with the genre name parameter
        $results = fetchAll($sql, ['genrename' => $genreName]);
        
        // Build and return the array of anime objects from the query results
        return $this->buildAnimes($results);
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Functions to build anime objects
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Builds an array of Anime objects from database results.
     *
     * This function takes an array of database results and constructs an array
     * of Anime objects. It iterates through each result, builds an Anime object
     * using the buildAnime() method, and adds it to the array if it is not null.
     *
     * @param array $results The array of database results.
     * @return array An array of Anime objects.
     */
    public function buildAnimes($results) {
        $animes = array();
        foreach ($results as $result) {
            $anime = $this->buildAnime($result);
            if ($anime != null) {
                $animes[] = $anime;
            }
        }
        return $animes;
    }
    
    /**
     * Builds an Anime object from a database result.
     *
     * This function takes a single database result (as an associative array) and
     * constructs an Anime object from it. It sets various properties of the Anime
     * object using the values from the database result.
     *
     * @param array $result The database result as an associative array.
     * @return Anime|null The constructed Anime object or null if the input is not an array.
     */
    public function buildAnime($result) {
        if (is_array($result)) {
            $anime = new Anime();
            $anime->setId($result['ID']);
            $anime->setTitle($result['title']);
            $anime->setUrl($result['url']);
            $anime->setImg($result['img']);
            $anime->setEpisodes($result['episodes']);
            $anime->setWeight($result['weight']);
            $anime->setRatings(array(
                1 => $result['rate_1'],
                2 => $result['rate_2'],
                3 => $result['rate_3'],
                4 => $result['rate_4'],
                5 => $result['rate_5']
            ));
            $anime->setGenres(GenreController::getInstance()->getGenresByAnime(intval($result['ID'])));
            return $anime;
        }
        return null;
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Functions to sort anime lists
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Sorts an array of Anime objects by their average rating.
     *
     * This function sorts an array of Anime objects based on their average rating.
     * It uses the usort function to perform the sorting. The sorting can be done
     * in ascending or descending order based on the provided flag.
     *
     * @param array $animes The array of Anime objects to be sorted.
     * @param bool $ASC Optional. If true, sorts in ascending order; otherwise, sorts in descending order. Default is true.
     * @return array The sorted array of Anime objects.
     */
    public function orderByRating($animes, $ASC = true) {
        if ($ASC) {
            usort($animes, function ($a, $b) {
                return $a->getAverageRating() > $b->getAverageRating();
            });
        } else {
            usort($animes, function ($a, $b) {
                return $a->getAverageRating() < $b->getAverageRating();
            });
        }
        return $animes;
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Functions to filter anime lists
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Filters a list of Anime objects by a minimum vote count.
     *
     * This function filters an array of Anime objects based on a specified minimum
     * vote count. Animes with a vote count less than the specified value are excluded.
     *
     * @param array $animes The array of Anime objects to be filtered.
     * @param int $votes The minimum vote count required for an Anime to be included in the result.
     * @return array The filtered array of Anime objects.
     */
    public function filterByVotes($animes, $votes) {
        // No vote count given -> no filter set
        if ($votes <= 0) {
            return $animes;
        }
        
        $filteredAnimes = array();
        
        foreach ($animes as $anime) {
            if ($anime->getVoteCount() >= $votes) {
                $filteredAnimes[] = $anime;
            }
        }
        return $filteredAnimes;
    }
    
    /**
     * Filters a list of Anime objects by genres.
     *
     * This function filters an array of Anime objects based on specified genres.
     * It can filter Animes that have at least one of the given genres (mode: 'or')
     * or all of the given genres (mode: 'and').
     *
     * @param array $animes The array of Anime objects to be filtered.
     * @param array $genres The array of genres to filter by.
     * @param string $mode Optional. The filter mode ('or' for at least one genre, 'and' for all genres). Default is 'or'.
     * @return array The filtered array of Anime objects.
     */
    public function filterByGenres($animes, $genres, $mode = 'or') {
        // No genres given -> no filter set
        if (empty($genres)) {
            return $animes;
        }
        
        $filteredAnimes = array();
        
        foreach ($animes as $anime) {
            if ($this->hasGenres($anime, $genres, $mode)) {
                $filteredAnimes[] = $anime;
            }
        }
        
        return $filteredAnimes;
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Other/Helper Functions
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Get the ID of an Anime.
     *
     * This function retrieves the ID of an Anime object. It accepts different types of input:
     * an integer (already an ID), an instance of the Anime class, or a string/int that can be
     * converted to an integer.
     *
     * @param mixed $anime The Anime object, ID, or string/int representing the ID.
     * @return int The ID of the Anime.
     */
    public function getAnimeId($anime) {
        $animeID = 0; // Failsafe
        
        // $anime is already an integer
        if (is_int($anime)) {
            $animeID = $anime;
        } elseif ($anime instanceof Anime) { // Is instance of Anime
            $animeID = $anime->getId();
        } elseif (!is_object($anime)) {
            $animeID = intval($anime);
        }
        
        return $animeID;
    }
    
    /**
     * Check if an Anime has a specific genre.
     *
     * This function checks whether an Anime object has a specific genre in its list of genres.
     *
     * @param Anime $anime The Anime object to be checked.
     * @param Genre $genre The Genre object to check for.
     * @return bool True if the Anime has the genre, false otherwise.
     */
    public function hasGenre($anime, $genre) {
        if (in_array($genre, $anime->getGenres())) {
            return true;
        }
        return false;
    }
    
    /**
     * Check if an Anime has certain genres.
     *
     * This function checks whether an Anime object has specific genres. It can operate in two modes:
     * 'or' to check if the Anime has at least one of the specified genres, and 'and' to check if the
     * Anime has all of the specified genres.
     *
     * @param Anime $anime The Anime object to be checked.
     * @param array $genres The array of genres to check for.
     * @param string $mode Optional. The check mode ('or' for at least one genre, 'and' for all genres). Default is 'or'.
     * @return bool True if the Anime meets the genre criteria, false otherwise.
     */
    public function hasGenres($anime, $genres, $mode = 'or') {
        if ($mode == 'or') {
            foreach ($genres as $genre) {
                if ($this->hasGenre($anime, $genre)) {
                    return true;
                }
            }
            return false;
        } elseif ($mode == 'and') {
            foreach ($genres as $genre) {
                if (!$this->hasGenre($anime, $genre)) {
                    return false;
                }
            }
            return true;
        }
    }
    
}
