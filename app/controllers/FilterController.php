<?php
namespace App\Controllers;

class FilterController
{
    private static $instance = null;
    private $title;
    private $orderBy;
    private $minRatingCount;
    private $genres;
    private $genreFilterBehavior;
    
    /**
     * Private constructor to prevent multiple instances.
     *
     * This constructor initializes the filter properties with default values.
     */
    private function __construct() {
        $this->title = '';
        $this->orderBy = 'rating-desc';
        $this->minRatingCount = 30;
        $this->genres = array();
        $this->genreFilterBehavior = 'or';
    }
    
    /**
     * Get the single instance of the class.
     *
     * This method ensures that only one instance of FilterController exists. If an instance is already
     * created and stored in the session, it will return that instance; otherwise, it will create a new one.
     *
     * @return FilterController The single instance of the FilterController.
     */
    public static function getInstance() {
        if (self::$instance === null) {
            if(isset($_SESSION['filter'])){
                return unserialize($_SESSION['filter']);
            }
            self::$instance = new FilterController();
        }
        return self::$instance;
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Rendering
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Handle filter form submission and render the filter view.
     *
     * This method processes the filter form if it has been submitted, updates the filter values accordingly,
     * and then renders the filter view.
     */
    public function index()
    {
        if(isset($_POST['filter-form'])) {
            $this->updateFilter();
        }
        
        $filter = $this->getInstance();
        $gc = GenreController::getInstance();
        $genres = $gc->getGenres();
        
        // Render the filter view
        require_once __DIR__ . '/../views/filter.php';
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Other/Helper Functions
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Update filter values based on the submitted form data.
     *
     * This method retrieves the values for the title, order by, minimum rating count, genres, and genre filter behavior
     * from the submitted form data and updates the corresponding properties. It then saves the updated filter settings
     * to the session.
     */
    private function updateFilter() {
        $gc = GenreController::getInstance();
        
        // Update title if necessary
        if(isset($_POST['anime-title'])) {
            $this->setTitle($_POST['anime-title']);
        }
        
        // Set order by
        $this->setOrderBy($_POST['order-by']);
        
        // Set minimal rating/voting count
        $this->setMinRatingCount($_POST['min-rating-count']);
        
        // Set genres
        $genres = array();
        if(isset($_POST['genres']) && count($_POST['genres']) > 0) {
            foreach($_POST['genres'] as $genreid) {
                $genre = $gc->getGenreById($genreid);
                if($genre != null) {
                    $genres[] = $genre;
                }
            }
        }
        $this->setGenres($genres);
        
        // Sets the filter behavior
        $this->setGenreFilterBehavior($_POST['genre-filter-behavior']);
        
        // Save filter to session
        $_SESSION['filter'] = serialize($this);
    }
    
    /* ----------------------------------------------------------------------------------------------------
     * Getter / Setter
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Get the current title filter.
     *
     * @return string The current title filter.
     */
    public function getTitle() {
        return $this->title;
    }
    
    /**
     * Get the current order by filter.
     *
     * @return string The current order by filter (e.g., 'rating-asc' or 'rating-desc').
     */
    public function getOrderBy() {
        return $this->orderBy;
    }
    
    /**
     * Get the current minimum rating count filter.
     *
     * @return int The current minimum rating count filter.
     */
    public function getMinRatingCount() {
        return $this->minRatingCount;
    }
    
    /**
     * Get the current genres filter.
     *
     * @return array The current genres filter.
     */
    public function getGenres() {
        return $this->genres;
    }
    
    /**
     * Get the current genre filter behavior.
     *
     * @return string The current genre filter behavior ('and' or 'or').
     */
    public function getGenreFilterBehavior() {
        return $this->genreFilterBehavior;
    }
    
    /**
     * Set the title filter.
     *
     * This method updates the title property with the provided value.
     *
     * @param string $title The title to filter by.
     */
    public function setTitle($title) {
        $this->title = $title;
    }
    
    /**
     * Set the order by filter with validation.
     *
     * This method updates the order by property if the provided value is valid (either 'rating-asc' or 'rating-desc').
     *
     * @param string $orderBy The order by criteria (must be 'rating-asc' or 'rating-desc').
     */
    public function setOrderBy($orderBy) {
        $whitelist = array("rating-asc", "rating-desc");
        if(in_array($orderBy, $whitelist)) {
            $this->orderBy = $orderBy;
        }
    }
    
    /**
     * Set the minimum rating count filter with validation.
     *
     * This method updates the minimum rating count property if the provided value is non-negative.
     *
     * @param int $mrc The minimum rating count (must be non-negative).
     */
    public function setMinRatingCount($mrc) {
        if($mrc >= 0) {
            $this->minRatingCount = $mrc;
        }
    }
    
    /**
     * Set the genres filter.
     *
     * This method updates the genres property with the provided array of genres.
     *
     * @param array $genres The genres to filter by.
     */
    public function setGenres($genres) {
        if(is_array($genres)) {
            $this->genres = $genres;
        }
    }
    
    /**
     * Set the genre filter behavior with validation.
     *
     * This method updates the genre filter behavior property if the provided value is valid (either 'and' or 'or').
     *
     * @param string $behavior The genre filter behavior (must be 'and' or 'or').
     */
    public function setGenreFilterBehavior($behavior) {
        $whitelist = array("and", "or");
        if(in_array($behavior, $whitelist)) {
            $this->genreFilterBehavior = $behavior;
        }
    }
}
