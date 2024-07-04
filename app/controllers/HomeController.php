<?php
namespace App\Controllers;

class HomeController
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
     * This method ensures that only one instance of HomeController exists. If an instance is already
     * created, it will return that instance; otherwise, it will create a new one.
     *
     * @return HomeController The single instance of the HomeController.
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new HomeController();
        }
        return self::$instance;
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Rendering
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Render the home view.
     *
     * This method handles the main logic for the home page. It checks for favorite toggles,
     * processes the filter settings, retrieves the list of animes based on the filter, and
     * renders the home view.
     */
    public function index()
    {
        $fc = FavoriteController::getInstance();
        $fc->handleToggle();  // Checks if the anime has been removed from favorites and saves changes to the DB
        
        $filter = FilterController::getInstance();
        $filter->index();  // Prints filter and it's current settings
        
        $animes = $this->getAnimes(
            $filter->getTitle(),
            $filter->getMinRatingCount(),
            $filter->getGenres(),
            $filter->getGenreFilterBehavior(),
            $filter->getOrderBy()
        ); // Get animes based on the filter settings
        
        // render the home view
        require_once __DIR__ . '/../views/home.php';
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Functions to get animes
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Get array of animes.
     *
     * This method retrieves an array of animes filtered and sorted based on various parameters.
     *
     * @param string $title Optional. The title of the anime to filter by.
     * @param int $minRatingCount Optional. The minimum number of ratings an anime must have.
     * @param array $genres Optional. The genres to filter by.
     * @param string $genreFilterMode Optional. The mode to filter genres ('and' or 'or').
     * @param string $orderBy Optional. The order by which to sort the animes ('rating-asc' or 'rating-desc').
     * @return array The array of filtered and sorted animes.
     */
    public function getAnimes($title = '', $minRatingCount = 30, $genres = array(), $genreFilterMode = 'or', $orderBy = 'rating-desc') {
        $ac = AnimeController::getInstance();
        
        // Get animes filtered by title
        $animes = $ac->getAnimes($title);
        
        // Filter animes by rating count
        $animes = $ac->filterByVotes($animes, $minRatingCount);

        // Filter by genres
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
    
}
