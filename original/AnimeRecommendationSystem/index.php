<?php
    include 'default_header.php'; // includes the connect.php to create the connection
   
?>

<!-- buttons to go the favorites page or the stats of the database -->

<div class= "goto-favorites">

    <form action = "favorite_list.php">

        <button type = "submit" name = "favorites">Favorites</button>
        
    </form>
    <form action = "database_stats.php">

        <button type = "submit" name = "stats">Anime Stats</button>
    
    </form>
</div>



<div class = "logged-container">
    <?php
    
        session_start(); // Start the session

        // Check if the username is set in the session
        if (isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
            
            echo "Logged in as: " .$username;

        } else {
            // Redirect to the login page if the username is not set
            header("Location: login.html");
            exit;
        }


    ?>
</div>

<!-- Search bar for addressing an anime by its name directly -->

<div class = "UI-container">
    <div class = "search-container">
        <form action = "search.php" method = "POST">

            <input type = "text" name = "search" placeholder="Search">
            <button type = "submit" name = "submit-search">Search</button>

        
    </div>

<!-- Checkboxes for each genre -->

    <div class = "checkbox-container">
        <p>
            <input type="checkbox" id="genre1" name="genres[]" value="genre_action">Action
            <input type="checkbox" id="genre2" name="genres[]" value="genre_adventure">Adventure
            <input type="checkbox" id="genre3" name="genres[]" value="genre_comedy">Comedy
            <input type="checkbox" id="genre4" name="genres[]" value="genre_drama">Drama
            <input type="checkbox" id="genre5" name="genres[]" value="genre_family">Family
            <input type="checkbox" id="genre6" name="genres[]" value="genre_fantasy">Fantasy
            <input type="checkbox" id="genre7" name="genres[]" value="genre_food">Food
            <input type="checkbox" id="genre8" name="genres[]" value="genre_harem">Harem
            <input type="checkbox" id="genre9" name="genres[]" value="genre_historical">Historical
            <input type="checkbox" id="genre10" name="genres[]" value="genre_horror">Horror
            <input type="checkbox" id="genre11" name="genres[]" value="genre_idols">Idols
            <input type="checkbox" id="genre12" name="genres[]" value="genre_isekai">Isekai
            <input type="checkbox" id="genre13" name="genres[]" value="genre_music">Music
            <input type="checkbox" id="genre14" name="genres[]" value="genre_mecha">Mecha
            <input type="checkbox" id="genre15" name="genres[]" value="genre_mystery">Mystery
            <input type="checkbox" id="genre16" name="genres[]" value="genre_romance">Romance
            <input type="checkbox" id="genre17" name="genres[]" value="genre_thriller">Thriller
            <input type="checkbox" id="genre18" name="genres[]" value="genre_scifi">Scifi
        </p>
        </form>
    </div>
</div>



<div class = "anime-container">

    <?php

    //select every anime by descending rate order

    $sql = "SELECT * FROM animes WHERE votes > 30 ORDER BY rate DESC ";
    $result = mysqli_query($conn, $sql);

    while($row = mysqli_fetch_assoc($result)){

        $imgpath =  $row['anime_img'];
        echo "<div class = 'anime-box'>";
        echo "<div class = 'anime-img'>";
        echo "<a href='anime_info.php?title=".$row['anime']."'>";   //show title
        echo "<img src='".$imgpath."' alt='Image' width='200' height='300'>"; //image
        echo "</a>";
        echo "</div>";
        echo "<h3><a href='anime_info.php?title=".$row['anime']."'>".$row['anime'].""; //put the the title and picture as a hyperlink the info page of that anime
        echo "</a></h3>";
        echo "<p>";
        echo '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="orange" class="bi bi-star" viewBox="0 0 16 16">';
        echo '<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.565.565 0 0 0-.163-.505L1.71 6.745l4.052-.576a.525.525 0 0 0 .393-.288L8 2.223l1.847 3.658a.525.525 0 0 0 .393.288l4.052.575-2.906 2.77a.565.565 0 0 0-.163.506l.694 3.957-3.686-1.894a.503.503 0 0 0-.461 0z"/></svg>';
        echo " ".$row['rate']. "</p>"; // rate
        echo "</div>";
          
    }

    ?>

</div>

<div class = "info-box">
    <h1>How to use the page?</h1>
        <ul>
            <li>Step 1: Enter the name of an anime in the search bar.</li>
            <li>Step 2: Optionally, select the genres you're interested in.</li>
            <li>Step 3: Click the "Search" button to find matching anime.</li>
            <li>Step 4: Browse through the results and click on an anime to view its details.</li>
            <li>Step 5: Add or remove the anime from your favorites list using the "Add to favorites" or "Remove from favorites" button.</li>
            <li>Step 6: Use the "Favorites" button to access your favorite anime list.</li>
            <li>Step 7: Check out the anime statistics using the "Anime Stats" button.</li>
            <li>Step 8: You can always come back by using the "Home" button.</li>
            <li>Step 9: Have fun organizing you anime list!</li>
        </ul>
</div>

</body>
</html>

