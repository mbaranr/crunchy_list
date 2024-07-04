<?php
namespace App\Controllers;

class StatisticsController
{
    
    private static $instance = null;
    
    /**
     * Private constructor to prevent multiple instances.
     *
     * This constructor is private to enforce the singleton pattern, ensuring only one instance
     * of StatisticsController exists throughout the application.
     */
    private function __construct() {}
    
    /**
     * Get the single instance of the class.
     *
     * This method ensures that only one instance of StatisticsController exists. If an instance is already
     * created, it will return that instance; otherwise, it will create a new one.
     *
     * @return StatisticsController The single instance of StatisticsController.
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new StatisticsController();
        }
        return self::$instance;
    }
    
    
    /* ----------------------------------------------------------------------------------------------------
     * Rendering
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Render the statistics page.
     *
     * This method gathers necessary data for various charts and renders the statistics view.
     */
    public function index()
    {
        $genreCount = 10; // Number of genres to display in charts
        
        // Get data for charts
        $ratesAndVotes = $this->getRatesAndVotes(); // Data for the first chart
        $genreCounts = $this->getGenreAndCount(); // Data for the second chart
        $mostPopularGenres = $this->printPopularGenres($genreCount); // Detailed list of popular genres for the second chart
        $highestDifferencePopularGenres = $this->printHighestDifferencePopularGenres($genreCount); // Information about highest difference between popular genres for the second chart
        $plotlyDataJSON = json_encode($this->getPlotlyData($this->getGenreDensityArray($this->getPopularGenresAndCount($genreCount)))); // Data for the third chart
        
        // Render the statistics view
        require_once __DIR__ . '/../views/statistics.php';
        
    }
    
    /* ----------------------------------------------------------------------------------------------------
     * Functions
     * ---------------------------------------------------------------------------------------------------- */
    
    /**
     * Retrieve average ratings and vote counts for all anime.
     *
     * Retrieves and prepares data on average ratings and vote counts for all anime entries.
     *
     * @return array Array containing pairs of average rating and vote count for each anime.
     */
    public function getRatesAndVotes()
    {
        $ac = AnimeController::getInstance();
        $animes = $ac->getAnimes();
        foreach ($animes as $anime) {
            $rate = floatval($anime->getAverageRating());
            $votes = $anime->getVoteCount();
            $data[] = array($rate, $votes);
        }
        return $data;
    }
    
    /**
     * Retrieve genre names and their respective counts from the database.
     *
     * Queries the database to retrieve the count of anime entries for each genre.
     *
     * @return array Array containing pairs of genre name and count of anime entries for each genre.
     */
    public function getGenreAndCount()
    {
        $data = array(['Genre', 'Count']);
        
        $sql = "SELECT * FROM genres";
        $genres = fetchAll($sql);
        
        foreach($genres as $genre) {
            $sql = "SELECT count(*)
                    FROM anime_genre
                    WHERE genreID = :genreid";
            $count = fetch($sql, ['genreid' => $genre['ID']]);
            $data[] = array($genre['name'], $count[0]);
        }
        
        return $data;
    }
    
    /**
     * Retrieve genre names and their respective counts as an associative array.
     *
     * Filters the result of getGenreAndCount() to return an associative array of genre names and counts.
     *
     * @return array Associative array where keys are genre names and values are counts of anime entries for each genre.
     */
    public function getGenreAndCountArray() {
        $data = array();
        foreach ($this->getGenreAndCount() as $array) {
            if (is_int($array[1])) { // Filter out unnecessary items
                $data[$array[0]] = $array[1];
            }
        }
        return $data;
    }
    
    /**
     * Retrieve an array of genre names from an associative array of genre names and counts.
     *
     * Extracts genre names from an associative array where keys are genre names.
     *
     * @param array $genresAndCount Associative array where keys are genre names and values are counts of anime entries for each genre.
     * @return array Array of genre names.
     */
    public function getGenreArray($genresAndCount) {
        $genres = array();
        foreach ($genresAndCount as $genre=>$count) {
            $genres[] = $genre;
        }
        return $genres;
    }
    
    /**
     * Calculate kernel density estimates for the ratings of each genre.
     *
     * Uses the kernel density estimation method to calculate ratings density for each genre.
     *
     * @param array $genresAndCount Associative array where keys are genre names and values are counts of anime entries for each genre.
     * @return array Associative array where keys are genre names and values are arrays of density estimates.
     */
    public function getGenreDensityArray($genresAndCount) {
        $ac = AnimeController::getInstance();
        $data = array();
        foreach($this->getGenreArray($genresAndCount) as $genre) {
            $animes = $ac->getAnimeByGenreName($genre);

            $ratings = array();
            foreach ($animes as $anime) {
                $ratings[] = $anime->getAverageRating();
            }
            
            // Calculate the kernel density estimate for the ratings of each genre
            $density = $this->calculateKernelDensity($ratings);
            
            // Add density to the main array
            $data[$genre] = $density;
        }
        return $data;
    }
    
    /**
     * Retrieve the most popular genres and their respective counts.
     *
     * Retrieves the most popular genres based on the count of anime entries and their respective counts.
     *
     * @param int $count Number of top genres to retrieve.
     * @return array Associative array where keys are genre names and values are counts of anime entries for each genre.
     */
    public function getPopularGenresAndCount($count) {
        $data = $this->getGenreAndCountArray();
        arsort($data);
        return array_slice($data, 0 ,$count);
    }
    
    /**
     * Retrieve the least popular genres and their respective counts.
     *
     * Retrieves the least popular genres based on the count of anime entries and their respective counts.
     *
     * @param int $count Number of bottom genres to retrieve.
     * @return array Associative array where keys are genre names and values are counts of anime entries for each genre.
     */
    public function getUnpopularGenres($count) {    
        $data = $this->getGenreAndCountArray();
        asort($data);
        return array_slice($data, 0 ,$count);
    }
    
    /**
     * Generate a formatted list of popular genres.
     *
     * Generates an ordered list of popular genres and their respective counts.
     *
     * @param int $count Number of top genres to include in the list.
     * @return string Formatted HTML list of popular genres.
     */
    public function printPopularGenres($count) {
        if (is_int($count) && $count > 1) {
            $text = 'Top ' . $count . ':<ol>';
            foreach($this->getPopularGenresAndCount($count) as $genre=>$count) {
                $text .= '<li>'.$genre . ' (' . $count . ')</li>';
            }
            $text .= '</ol>';
            return $text;
        }
        return '';
    }
    
    /**
     * Generate information about the highest difference between popular genres.
     *
     * Analyzes the difference in popularity counts between top genres and identifies the highest difference.
     *
     * @param int $amount Number of top genres to consider for analysis.
     * @return string Information about the highest difference between popular genres.
     */
    public function printHighestDifferencePopularGenres($amount) {
        if (is_int($amount) && $amount > 1) {
            $mpg = $this->getPopularGenresAndCount($amount);
            $iterator = $amount;
            $highestDifference = 0;
            $rank = 0;
            foreach($mpg as $genre=>$count) {
                if(!isset($prevCount)){
                    $prevCount = $count;
                }
                if($highestDifference < ($prevCount-$count)) {
                    $highestDifference = ($prevCount-$count);
                    $prevCount = $count;
                    $rank = $amount - $iterator;
                }
                $iterator--;
            }
            return 'The highest difference is between ranks ' . $rank . ' and ' . $rank+1 . ' with a difference of ' . $highestDifference . '.';
        }
        return '';
    }
    
    /**
     * Calculate the kernel density estimation for a given set of data.
     *
     * Uses the kernel density estimation method to estimate the density of data points.
     *
     * @param array $data Array of numeric data points.
     * @param float $bandwidth Bandwidth parameter for the kernel density estimation.
     * @return array Array of density estimates for each data point.
     */
    public function calculateKernelDensity($data, $bandwidth = 0.5) {
        $density = array();
        $min = min($data);
        $max = max($data);
        
        // Generate x-axis values
        $x = range($min, $max, 0.01);
        
        foreach ($x as $value) {
            $sum = 0;
            foreach ($data as $dataValue) {
                $diff = ($value - $dataValue) / $bandwidth;
                $weight = 1 / sqrt(2 * pi()) * exp(-0.5 * pow($diff, 2));
                $sum += $weight;
            }
            $density[] = array($value, $sum / (count($data) * $bandwidth));
        }
        return $density;
    }
    
    /**
     * Prepare data in Plotly.js compatible format.
     *
     * Prepares data from kernel density estimates for use with Plotly.js.
     *
     * @param array $genreDensityArray Associative array where keys are genre names and values are arrays of density estimates.
     * @return array Formatted data array for Plotly.js.
     */
    public function getPlotlyData($genreDensityArray) {
        $plotlyData = array();
        foreach ($genreDensityArray as $genre => $density) {
            $x = array_column($density, 0);
            $y = array_column($density, 1);
            
            $trace = array(
                'x' => $x,
                'y' => $y,
                'fill' => 'tozeroy',
                'name' => $genre,
                'type' => 'scatter',
                'mode' => 'lines',
                'line' => array('shape' => 'spline')
            );
            $plotlyData[] = $trace;
        }
        return $plotlyData;
    }
}
