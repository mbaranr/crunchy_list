<?php
namespace App\Controllers;
use App\Models\Genre;

class GenreController
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
     * This method ensures that only one instance of GenreController exists. If an instance is already
     * created, it will return that instance; otherwise, it will create a new one.
     *
     * @return GenreController The single instance of the GenreController.
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new GenreController();
        }
        return self::$instance;
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Functions to get genres
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Return genres listed in the database.
     *
     * This method retrieves all genres from the database and builds Genre objects for each result.
     *
     * @return array The array of Genre objects representing all genres in the database.
     */
    public function getGenres() {
        $genres = array();
        $sql = "SELECT *
                FROM genres";
        $results = fetchAll($sql);
        return $this->buildGenres($results);
    }
    
    /**
     * Get Genre by ID.
     *
     * This method retrieves a genre from the database by its ID and builds a Genre object for the result.
     *
     * @param int $id The ID of the genre to be retrieved.
     * @return Genre|null The Genre object representing the genre, or null if not found.
     */
    public function getGenreById($id) {
        $sql = "SELECT *
                From genres
                WHERE ID = :id";
        $result = fetch($sql, ['id' => $id]);
        return $this->buildGenre($result);
    }
    
    /**
     * Return all genres related to an anime.
     *
     * This method retrieves and returns all genres associated with a given anime. It fetches the anime's ID,
     * then queries the database for genres linked to that ID, and builds Genre objects for each result.
     *
     * @param mixed $anime The Anime object, ID, or string/int representing the ID.
     * @return array The array of Genre objects associated with the given anime.
     */
    public function getGenresByAnime($anime) {
        $animeID =  AnimeController::getInstance()->getAnimeId($anime);
        $genres = array();
        
        if($animeID > 0) {
            // prepare sql with anime ID
            $sql = "SELECT g.*
                FROM genres g
                JOIN anime_genre ag ON g.ID = ag.genreID
                JOIN animes a ON a.ID = ag.animeID
                WHERE a.ID = :animeid";
            $results = fetchAll($sql, ['animeid' => $animeID]);
            foreach($results as $result) {
                $genre = new Genre();
                $genre->setId($result['ID']);
                $genre->setName($result['name']);
                $genres[] = $genre;
            }
        }
        return $genres;
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Functions to build anime objects
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Build Genre objects from database results.
     *
     * This method takes an array of database results and converts them into an array of Genre objects.
     *
     * @param array $results The array of database results.
     * @return array The array of Genre objects.
     */
    public function buildGenres($results) {
        $genres = array();
        foreach($results as $result) {
            $genre = $this->buildGenre($result);
            if($genre != null) {
                $genres[] = $genre;
            }
        }
        return $genres;
    }
    
    /**
     * Build a Genre object from a database result.
     *
     * This method takes a single database result and converts it into a Genre object.
     *
     * @param array $result The database result to be converted.
     * @return Genre|null The Genre object, or null if the result is not valid.
     */
    public function buildGenre($result) {
        if(is_array($result)) {
            $genre = new Genre();
            $genre->setId($result['ID']);
            $genre->setName($result['name']);
            return $genre;
        }
        return null;
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Functions to sort genre lists
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Return genres sorted by ID.
     *
     * This method sorts an array of Genre objects by their ID in ascending or descending order.
     *
     * @param array $genres The array of Genre objects to be sorted.
     * @param bool $ASC Optional. Determines the sort order. True for ascending, false for descending.
     * @return array The sorted array of Genre objects.
     */
    public function sortGenresById($genres, $ASC = true) {
        if($ASC) {
            usort($genres, function ($a, $b) {
                return $a->getId() < $b->getId();
            });
        } else {
            usort($genres, function ($a, $b) {
                return $a->getId() > $b->getId();
            });
        }
        return $genres;
    }
    
    /**
     * Return genres sorted by Name.
     *
     * This method sorts an array of Genre objects by their name in ascending or descending order.
     *
     * @param array $genres The array of Genre objects to be sorted.
     * @param bool $ASC Optional. Determines the sort order. True for ascending, false for descending.
     * @return array The sorted array of Genre objects.
     */
    public function sortGenresByName($genres, $ASC = true) {
        if($ASC) {
            usort($genres, function ($a, $b) {
                return $a->getName() > $b->getName();
            });
        } else {
            usort($genres, function ($a, $b) {
                return $a->getName() < $b->getName();
            });
        }
        return $genres;
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Other/Helper Functions
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Return genres as printable text.
     *
     * This method converts an array of Genre objects into a readable string, with genres separated by commas
     * and the last two genres separated by 'and'.
     *
     * @param array $genres The array of Genre objects to be converted into text.
     * @return string A readable string representing the genres.
     */
    public function getGenresAsText($genres) {
        $genresCount = count($genres);
        $genresText = '-';
        if($genresCount > 0) {
            $genresText = '';
            $counter = 1;
            foreach($genres as $genre) {
                $genresText .= $genre->getName();
                if($counter < $genresCount-1) {
                    $genresText .= ', ';
                } elseif($counter < $genresCount) {
                    $genresText .= ' and ';
                }
                $counter++;
            }
        }
        return $genresText;
    }
    
    /**
     * Check if an array of genres contains a genre with a specific ID.
     *
     * This method checks whether a given genre ID exists within an array of Genre objects.
     *
     * @param array $genres The array of Genre objects to search.
     * @param int $genreID The ID of the genre to search for.
     * @return bool True if the genre ID is found, false otherwise.
     */
    public function ifGenreIdInArray($genres, $genreID) {
        foreach ($genres as $genre) {
            if ($genre->getId() === $genreID) {
                return true;
            }
        }
        return false;
    }
}
