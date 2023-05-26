<?php
    include 'default_header.php';
?>

<h2>Favorites:</h2>   

<div class= "home-button">
    <form action = "index.php">

        <button type = "submit" name = "home">Home</button>

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

<?php

    

    if(isset($_POST['add_favorite'])){
        $anime = $_SESSION['anime'];
        $sql = "SELECT * FROM favorites WHERE user = '$username' AND anime = '$anime' ";
        $result = mysqli_query($conn, $sql);
        $queryResults = mysqli_num_rows($result);

        if($queryResults > 0){
            
        }else{

            $sql = "SELECT * FROM animes WHERE anime = '$anime' ";
            $result = mysqli_query($conn, $sql);
            $queryResults = mysqli_num_rows($result);
            $row = mysqli_fetch_assoc($result);

            $sql = "INSERT INTO favorites VALUES ('$username', '$anime', '".$row['anime_url']."', '".$row['anime_img']."', '".$row['episodes']."', '".$row['votes']."', '".$row['weight']."', '".$row['rate']."', '".$row['rate_1']."', '".$row['rate_2']."', '".$row['rate_3']."', '".$row['rate_4']."', '".$row['rate_5']."')";
            $result = mysqli_query($conn, $sql);
        }

    } 

    if(isset($_POST['rem_favorite'])){
        $anime = $_SESSION['anime'];
        $sql = "DELETE FROM favorites WHERE user = '$username' AND anime = '$anime'";
        $result = mysqli_query($conn, $sql);
    }

?>

<div class = "anime-container">

    <?php

    $sql = "SELECT * FROM favorites WHERE user = '$username' ";
    $result = mysqli_query($conn, $sql);

    while($row = mysqli_fetch_assoc($result)){

        $imgpath =  $row['anime_img'];
        echo "<div class = 'anime-box'>";
        echo "<div class = 'anime-img'>";
        echo "<a href='anime_info.php?title=".$row['anime']."'>";
        echo "<img src='".$imgpath."' alt='Image' width='200' height='300'>";
        echo "</a>";
        echo "</div>";
        echo "<h3><a href='anime_info.php?title=".$row['anime']."'>".$row['anime']."";
        echo "</a></h3>";
        echo "<p>";
        echo '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="orange" class="bi bi-star" viewBox="0 0 16 16">';
        echo '<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.565.565 0 0 0-.163-.505L1.71 6.745l4.052-.576a.525.525 0 0 0 .393-.288L8 2.223l1.847 3.658a.525.525 0 0 0 .393.288l4.052.575-2.906 2.77a.565.565 0 0 0-.163.506l.694 3.957-3.686-1.894a.503.503 0 0 0-.461 0z"/></svg>';
        echo " ".$row['rate']. "</p>";
        echo "</div>";
          
    }

    ?>

</div>

</body>
</html>
