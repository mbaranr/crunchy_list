<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
<script type="text/javascript">
    // Load the pertinent google chart library
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('number', 'Rate');
        data.addColumn('number', 'Votes');
        data.addRows(<?php echo json_encode($ratesAndVotes); ?>);

        var options = {
            title: 'Rate vs Votes',
            hAxis: { title: 'Rate' },
            vAxis: { title: 'Votes', scaleType: 'log' } // Set logarithmic scale for y-axis
        };

        var chart = new google.visualization.ScatterChart(document.getElementById('scatter-chart'));
        chart.draw(data, options);
    }
	// Add event listener to redraw chart on window resize
    window.addEventListener('resize', drawChart);
</script>
<script>
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
        var data = google.visualization.arrayToDataTable(<?php echo json_encode($genreCounts); ?>);
        var options = {
            title: 'Anime Genre Counts',
            legend: { position: 'none' },
            hAxis: { title: 'Genre' },
            vAxis: { title: 'Count' }
        };
        var chart = new google.visualization.ColumnChart(document.getElementById('column-chart'));
        chart.draw(data, options);
    }
 	// Add event listener to redraw chart on window resize
    window.addEventListener('resize', drawChart);
</script>

<div class="container white-bg black-text round-border padding-v-15"><br>
    <div id="scatter-chart" class="chart"></div>
    <p>This scatter plot compares the rates with the amount of votes. It's important to say that the votes are presented on a
    logarithmic scale to display the dots more evenly throughout the plane. As one can observe, ratings seem pretty high,
    with the majority being distributed around 4 or 5 stars.</p>
    
    <div id="column-chart" class="chart"></div>
    <p>This column chart compares each genre with the count of animes in the database that show this genre tag. By doing this,
    we can have a better understanding of which are the most popular genres.<br>
    <?php echo $mostPopularGenres; ?></p>
    <p><?php echo $highestDifferencePopularGenres; ?></p>
    
    <div id="kernell-genres"  class="chart"></div>
    <script>
        // Parse the plotly.js data from php
        var plotlyData = <?php echo $plotlyDataJSON; ?>;
        
        // Function to draw the plot
        function drawPlot() {
            Plotly.newPlot('kernell-genres', plotlyData, {}, {responsive: true});
        }
    
        // Initial plot drawing
        drawPlot();
        
        // Redraw the plot on window resize
        window.addEventListener('resize', drawPlot);
    </script>
    
    <?php //TODO The genre closest to 5 stars should be found by code ?>
    <p>This last kernell density diagram shows the probability density for each genre to get a certain rate estimated using KDE.
    This graph gives an idea of how distributions between the top <?php echo $genreCount; ?> genres differ from each other.
    To begin with, the peaks in the different curves indicate clusters within the data. So, we can infer that there is no
    preference over any genre, as they all lay near the same rating, with drama being the closest one to 5 stars.
    Furthermore, we can see that the curves are asymmetric, negatively skewed, as the peaks are shifted towards the right. This
    also confirms what we saw on the scatter plot, as the majority of observations tend to be larger or higher in magnitude
    compared to the few extreme or lower values.</p>
</div>