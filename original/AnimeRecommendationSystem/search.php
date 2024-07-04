<?php
    include 'default_header.php'; // includes the connect.php to create the connection
?>

<h2>Search page</h2>

<div class = "anime-container">

<div class= "home-button">
    <form action = "index.php">

        <button type = "submit" name = "home">Home</button>

    </form>
</div>

<?php

if (isset($_POST['submit-search'])) {
    $search = mysqli_real_escape_string($conn, $_POST['search']);

    // Check if any genres are selected
    if (isset($_POST['genres'])) {
        $genres = $_POST['genres'];
        $genreConditions = array();

        // Loop through selected genres and add them to the query conditions
        foreach ($genres as $genre) {
            $safeGenre = mysqli_real_escape_string($conn, $genre);
            $genreConditions[] = "$safeGenre = 1";
        }

        // Construct the SQL query with genre conditions
        $genreQuery = implode(" AND ", $genreConditions);
        $sql = "SELECT * FROM animes WHERE $genreQuery AND votes > 30 AND anime LIKE '%$search%' ORDER BY rate DESC";
       

    } else {
        // If no genres are selected, only search by the anime name

        $sql = "SELECT * FROM animes WHERE votes > 30 AND anime LIKE '%$search%' ORDER BY rate DESC";
    }
        
        $result = mysqli_query($conn, $sql);
        $queryResults = mysqli_num_rows($result);

        
        // if there are results for the search criteria, show them
        if($queryResults > 0){
            while($row = mysqli_fetch_assoc($result)){
                $imgpath =  $row['anime_img'];
                echo "<div class = 'anime-box'>"; 
                echo "<div class = 'anime-img'>";
                echo "<a href='anime_info.php?title=".$row['anime']."'>"; //name of the anime
                echo "<img src='".$imgpath."' alt='Image' width='200' height='300'>"; //image of the anime
                echo "</a>";
                echo "</div>";
                echo "<h3><a href='anime_info.php?title=".$row['anime']."'>".$row['anime'].""; //put the the title and picture as a hyperlink the info page of that anime
                echo "</a></h3>";
                echo "<p>";
                echo '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="orange" class="bi bi-star" viewBox="0 0 16 16">';
                echo '<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.565.565 0 0 0-.163-.505L1.71 6.745l4.052-.576a.525.525 0 0 0 .393-.288L8 2.223l1.847 3.658a.525.525 0 0 0 .393.288l4.052.575-2.906 2.77a.565.565 0 0 0-.163.506l.694 3.957-3.686-1.894a.503.503 0 0 0-.461 0z"/></svg>';
                echo " ".$row['rate']. "</p>"; //rate of the anime
                echo "</div>";
            }
        // if there are no results, notify the user
        }else{

            echo "There are no results matching your search";
        }
    }

?>