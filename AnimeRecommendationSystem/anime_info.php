<?php
    include 'default_header.php'; // includes the connect.php to create the connection
   
?>
<h2>Anime Info</h2>

<div class= "home-button">
    <form action = "index.php">

        <button type = "submit" name = "home">Home</button>

    </form>
</div>


<div class="animeinfo-container">
    <?php
    session_start();
    $title = mysqli_real_escape_string($conn, $_GET['title']);
    $_SESSION['anime'] = $title;
    $sql = "SELECT * FROM animes WHERE anime LIKE '$title' ORDER BY rate DESC ";
    $result = mysqli_query($conn, $sql);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $imgpath = $row['anime_img'];
    ?>
        <div class="animebig-img">
            <img src="<?php echo $imgpath; ?>" alt="Image" width="600" height="900">
        </div>
        <div class="animeinfo-box">
            <h1><a href="<?php echo $row['anime_url']; ?>" target="_blank"><?php echo $row['anime']; ?></a></h1>
            <p>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="orange" class="bi bi-star" viewBox="0 0 16 16">
                    <path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.565.565 0 0 0-.163-.505L1.71 6.745l4.052-.576a.525.525 0 0 0 .393-.288L8 2.223l1.847 3.658a.525.525 0 0 0 .393.288l4.052.575-2.906 2.77a.565.565 0 0 0-.163.506l.694 3.957-3.686-1.894a.503.503 0 0 0-.461 0z"/>
                </svg>
                <?php echo $row['rate']; ?>
            </p>
            <p>Episodes: <?php echo $row['episodes']; ?></p>
            <p>Votes: <?php echo $row['votes']; ?></p>
            <p>Weight: <?php echo $row['weight']; ?></p>
            <p><a href="<?php echo $row['anime_url']; ?>" target="_blank">Watch me!</a></p>
            <div class="graph">
                <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                <script type="text/javascript">
                    google.charts.load('current', { packages: ['corechart'] });
                    google.charts.setOnLoadCallback(drawChart1);
                    function drawChart1() {
                        var data = google.visualization.arrayToDataTable([
                            ['# of ratings', 'votes'],
                            ['rate_1', <?php echo $row['rate_1']; ?>],
                            ['rate_2', <?php echo $row['rate_2']; ?>],
                            ['rate_3', <?php echo $row['rate_3']; ?>],
                            ['rate_4', <?php echo $row['rate_4']; ?>],
                            ['rate_5', <?php echo $row['rate_5']; ?>]
                        ]);
                        var options = {
                            title: 'Number of votes per star',
                            chartArea: { width: '50%' },
                            hAxis: {
                                title: 'Total',
                                minValue: 0
                            },
                            colors: ['#FFA500', '#FFA500']
                        };
                        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div1'));
                        chart.draw(data, options);
                    }
                </script>
                <div id="chart_div1" style="width: 1200px; height: 600px;"></div>
            </div>
        </div>
    <?php
    }
    ?>
    <div class="fav-button">
        <form action="favorite_list.php" method="POST">
            <button type="submit" name="add_favorite">Add to Favorites</button>
            <button type="submit" name="rem_favorite">Remove from Favorites</button>
        </form>
    </div>
</div>


</body>
</html>

