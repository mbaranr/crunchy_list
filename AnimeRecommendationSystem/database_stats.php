<?php
    include 'default_header.php'; // includes the connect.php to create the connection
   
?>
<h2>Database Stats</h2>

<div class= "home-button">
    <form action = "index.php">

        <button type = "submit" name = "home">Home</button>

    </form>
</div>


<div class="stats-container">
    
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <div id="scatter-chart" style="width: 1200px; height: 600px"></div>

    <?php
    // Include the default header file to establish the database connection
    include 'default_header.php';

    // Fetch the data from the database
    $sql = "SELECT rate, votes FROM animes";
    $result = mysqli_query($conn, $sql);

    // Prepare an array to hold the data
    $data = array();

    // Fetch each row and add it to the data array
    while ($row = mysqli_fetch_assoc($result)) {
        $rate = floatval($row['rate']);
        $votes = intval($row['votes']);
        $data[] = array($rate, $votes);
    }
    ?>

    <script type="text/javascript">
        // Load the Google Charts library
        google.charts.load('current', { 'packages': ['corechart'] });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('number', 'Rate');
            data.addColumn('number', 'Votes');
            data.addRows(<?php echo json_encode($data); ?>);

            var options = {
                title: 'Rate vs Votes',
                hAxis: { title: 'Rate' },
                vAxis: { title: 'Votes', scaleType: 'log' } // Set logarithmic scale for y-axis
            };

            var chart = new google.visualization.ScatterChart(document.getElementById('scatter-chart'));
            chart.draw(data, options);
        }
    </script>
    <p>This scatter plot compares the rates with the amount of votes. It's important to say that the votes are presented on a logarithmic scale to display the dots more evenly throughout the plane. As one can observe, ratings seem pretty high, with the majority being distributed around 4 or 5 stars. 
    </p>
    <?php
        $genres = array(
            'Action',
            'Adventure',
            'Comedy',
            'Drama',
            'Family',
            'Fantasy',
            'Food',
            'Harem',
            'Historical',
            'Horror',
            'Idols',
            'Isekai',
            'Music',
            'Mecha',
            'Mystery',
            'Romance',
            'Thriller',
            'SciFi'
        );
        $genreCounts = array();
        
        // Fetch the genre counts from the database
        foreach ($genres as $genre) {
            $columnName = 'genre_' . strtolower(str_replace(' ', '_', $genre));
            $sql = "SELECT COUNT(*) AS count FROM animes WHERE $columnName = 1";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            $count = (int) $row['count'];
            $genreCounts[$genre] = $count;
        }
        
        // Prepare the data for Google Charts
        $data = array(
            ['Genre', 'Count']
        );
        foreach ($genreCounts as $genre => $count) {
            $data[] = array($genre, $count);
        }
        $dataJson = json_encode($data);
    ?>

    <div id="column-chart" style="width: 1200px; height: 600px"></div>

    <script>

    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable(<?php echo $dataJson; ?>);

        var options = {
            title: 'Anime Genre Counts',
            legend: { position: 'none' },
            hAxis: { title: 'Genre' },
            vAxis: { title: 'Count' }
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('column-chart'));
        chart.draw(data, options);
    }
</script>

<p>This column chart compares each genre with the count of animes in the database that show this genre tag. By doing this, we can have a better understanding of which are the most popular genres. The top 10 being: Comedy, Action, Drama, Fantasy, Romance, Adventure, SciFi, Harem, Mecha and Mystery. The last 3 being very far from the other 7, with 132 count difference. </p>

<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>

<?php
    function calculateKernelDensity($data, $bandwidth = 0.5) {
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

?>


<?php
$topGenres = array(
    'genre_action',
    'genre_adventure',
    'genre_comedy',
    'genre_drama',
    'genre_romance',
    'genre_fantasy',
    'genre_scifi',
    'genre_harem',
    'genre_mecha',
    'genre_mystery'
);

// Prepare the data array
$data = array();

// Fetch rates data for each genre
foreach ($topGenres as $genre) {
    $columnName = mysqli_real_escape_string($conn, $genre);
    $sql = "SELECT `rate` FROM `animes` WHERE `$columnName` = 1";
    $result = mysqli_query($conn, $sql);

    // Collect rates data for the genre
    $rates = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $rates[] = floatval($row['rate']);
    }

    // Calculate the kernel density estimate for the rates data
    $density = calculateKernelDensity($rates);

    // Add the genre's density data to the main data array
    $data[$genre] = $density;
}

// Prepare the data for Plotly.js
$plotlyData = array();
foreach ($data as $genre => $density) {
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

// Convert the data to JSON
$plotlyDataJson = json_encode($plotlyData);
?>
<div id="kernell-genres" style="width: 1200px; height: 600px"></div>

<!-- Add the JavaScript code to generate the plot -->
<script>
    // Parse the Plotly.js data from PHP
    var plotlyData = <?php echo $plotlyDataJson; ?>;

    // Create the kernel density plot
    Plotly.newPlot('kernell-genres', plotlyData);
</script>

<p>This last kernell density diagram shows the probability density for each genre to get a certain rate estimated using KDE. This graph gives an idea of how distributions between the top 10 genres differ from each other. To begin with, the peaks in the different curves indicate clusters within the data. So, we can infer that there is no preference over any genre, as they all lay near the same rating, with drama being the closest one to 5 stars.
Furthermore, we can see that the curves are asymmetric, negatively skewed, as the peaks are shifted towards the right. This also confirms what we saw on the scatter plot, as the majority of observations tend to be larger or higher in magnitude compared to the few extreme or lower values.</p>

</div>