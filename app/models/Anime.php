<?php
namespace App\Models;

class Anime
{
    private $id;
    private $title;
    private $url;
    private $img;
    private $seasons;
    private $episodes;
    private $weight;
    private $ratings;
    private $genres;
    
    /**
     * Constructor to initialize ratings and genres arrays.
     */
    public function __construct() {
        $this->ratings = array(0, 0, 0, 0, 0); // Initialize ratings array for stars 1 to 5
        $this->genres = array(); // Initialize genres array
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Functions
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Add a genre to the anime.
     *
     * @param mixed $genre Genre object to add.
     */
    public function addGenre($genre) {
        if(isset($genre)) {
            $this->genres[] = $genre;
        }
    }
    
    /**
     * Calculate and return the average rating of the anime.
     *
     * @param bool $round Whether to round the average rating (default true).
     * @return float Average rating of the anime.
     */
    public function getAverageRating($round = true) {
        $rat1 = $this->getRating(1);
        $rat2 = $this->getRating(2);
        $rat3 = $this->getRating(3);
        $rat4 = $this->getRating(4);
        $rat5 = $this->getRating(5);
        $sum = $rat1+$rat2+$rat3+$rat4+$rat5;
        if($sum > 0) {
            $avg = ($rat1*1 + $rat2*2 + $rat3*3 + $rat4*4 + $rat5*5)/$sum;
            if($round) {
                return round($avg, 2);
            } else {
                return $avg;
            }
        }
        return 0;
    }
    
    /**
     * Get the total count of votes for the anime.
     *
     * @return int Total count of votes.
     */
    public function getVoteCount() {
        $count = 0;
        foreach($this->getRatings() as $rating) {
            $count+=$rating;
        }
        return $count;
    }
    
    /**
     * Print a formatted representation of the anime.
     */
    public function print() {
        $genreString = '';
        $counter=1;
        foreach($this->genres as $genre) {
            $genreString .= $genre->getName();
            if($counter < count($this->genres)) {
                $genreString .= ', ';
            }
            $counter++;
        }
        echo 'id: ' . $this->id . ';
                title: ' . $this->title . ';
                url: ' . $this->url . ';
                img: ' . $this->img . ';
                seasons: ' . $this->seasons . ';
                episodes: ' . $this->episodes . ';
                weight: ' . $this->weight . ';
                ratings: ' . implode(", ", $this->ratings) . ';
                genres: ' . $genreString . ';';
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Getters
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Get anime ID.
     *
     * @return int Anime ID.
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Get anime title.
     *
     * @return string Anime title.
     */
    public function getTitle() {
        return $this->title;
    }
    
    /**
     * Get anime URL.
     *
     * @return string Anime URL.
     */
    public function getUrl() {
        return $this->url;
    }
    
    /**
     * Get anime image URL.
     *
     * @return string Anime image URL.
     */
    public function getImg() {
        return $this->img;
    }
    
    /**
     * Get number of seasons of the anime.
     *
     * @return int Number of seasons.
     */
    public function getSeasons() {
        return $this->seasons;
    }
    
    /**
     * Get number of episodes of the anime.
     *
     * @return int Number of episodes.
     */
    public function getEpisodes() {
        return $this->episodes;
    }
    
    /**
     * Get weight of the anime.
     *
     * @return int Weight of the anime.
     */
    public function getWeight() {
        return $this->weight;
    }
    
    /**
     * Get ratings array of the anime.
     *
     * @return array Ratings array (indexed from 1 to 5).
     */
    public function getRatings() {
        return $this->ratings;
    }
    
    /**
     * Get rating count for a specific star rating.
     *
     * @param int $stars Star rating (1 to 5).
     * @return int|null Count of ratings for the specified star rating, or null if stars parameter is invalid.
     */
    public function getRating($stars) {
        if(isset($stars) && is_int($stars)){
            if($stars >= 1 && $stars <= 5) {
                return $this->ratings[$stars];
            }
        }
        return null;
    }
    
    /**
     * Get genres associated with the anime.
     *
     * @return array Array of genre objects associated with the anime.
     */
    public function getGenres() {
        return $this->genres;
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Setters
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Set anime ID.
     *
     * @param int $id Anime ID.
     */
    public function setId($id) {
        if(isset($id) && is_int($id)) {
            $this->id = $id;
        }
    }
    
    /**
     * Set anime title.
     *
     * @param string $title Anime title.
     */
    public function setTitle($title) {
        if(isset($title)) {
            $this->title = $title;
        }
    }
    
    /**
     * Set anime URL.
     *
     * @param string $url Anime URL.
     */
    public function setUrl($url) {
        if(isset($url)) {
            $this->url = $url;
        }
    }
    
    /**
     * Set anime image URL.
     *
     * @param string $img Anime image URL.
     */
    public function setImg($img) {
        if(isset($img)) {
            $this->img = $img;
        }
    }
    
    /**
     * Set number of seasons of the anime.
     *
     * @param int $seasons Number of seasons.
     */
    public function setSeasons($seasons) {
        if(isset($seasons) && is_int($seasons)) {
            $this->seasons = $seasons;
        }
    }
    
    /**
     * Set number of episodes of the anime.
     *
     * @param int $episodes Number of episodes.
     */
    public function setEpisodes($episodes) {
        if(isset($episodes) && is_int($episodes)) {
            $this->episodes = $episodes;
        }
    }
    
    /**
     * Set weight of the anime.
     *
     * @param int $weight Weight of the anime.
     */
    public function setWeight($weight) {
        if(isset($weight) && is_int($weight)) {
            $this->weight = $weight;
        }
    }
    
    /**
     * Set ratings array of the anime.
     *
     * @param array $ratings Ratings array (indexed from 1 to 5).
     */
    public function setRatings($ratings) {
        if(isset($ratings)) {
            $this->ratings = $ratings;
        }
    }
    
    /**
     * Set rating count for a specific star rating.
     *
     * @param int $stars Star rating (1 to 5).
     * @param int $count Count of ratings for the specified star rating.
     */
    public function setRating($stars = 5, $count) {
        if(isset($count) && is_int($count) && is_int($stars) && $stars >= 1 && $stars <= 5) {
            $this->ratings[$stars] = $count;
        }
    }
    
    /**
     * Set genres associated with the anime.
     *
     * @param array $genres Array of genre objects associated with the anime.
     */
    public function setGenres($genres) {
        if(isset($genres)) {
            $this->genres = $genres;
        }
    }
}
