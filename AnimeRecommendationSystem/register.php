<?php
    include 'default_header.php'; // includes the connect.php to create the connection
?>
<h4>CREATE AN ACCOUNT</h4>

<body>
    <form action = "register.php" method = "POST">
    <div class = "register-container">
            
            <!-- create entries for specifying username, password and repeated password -->

            <div><input type = "text" name = "username" placeholder="Username"></div>
            <div><input type = "text" name = "password" placeholder="Password"></div>
            <div><input type = "text" name = "repeat_password" placeholder="Repeat password"></div>
            <div><button type = "Register" name = "register">Register</button></div>
        
    <?php

        // if the register button was pressed
        if (isset($_POST['register'])) {
            // escape special characters in a string to make it safe to use in SQL queries
            $username = mysqli_real_escape_string($conn, $_POST['username']);
            $password = mysqli_real_escape_string($conn, $_POST['password']);
            $Rpassword = mysqli_real_escape_string($conn, $_POST['repeat_password']);

            $sql = "SELECT * FROM users WHERE user = '$username'";
            
            $result = mysqli_query($conn, $sql);
            $queryResults = mysqli_num_rows($result);
            // validate username length
            if(strlen($username) > 15){
                echo "<div><p>Username can only be 15 characters long</p></div>";
                exit;
            }
            //hash the password for security
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);
            // if all the conditions are met
            if($username > 0 && (strlen($password) > 4) && ($password == $Rpassword) && $queryResults==0){
                //create new user
                $sql = "INSERT INTO users (user, password) VALUES('$username', '$password_hashed')";
                $result = mysqli_query($conn, $sql);
                header("Location: login.html");
                exit;
            }
            //notify invalid password
            if(strlen($password) < 5){
                echo "<div><p>Please enter a password that is at least 5 digits long</p></div>";
            }
            //notify invalid username
            if($username <= 0){
                echo "<div><p>Please enter valid username</p></div>";
            }
            //notify passwords not matching
            if($password != $Rpassword){
                echo "<div><p>Passwords don't match</p></div>";
            }
            //notify the previous existence of the user credentials
            if($queryResults>0){
                echo "<div><p>The username already exists, please try a new one</p></div>";
            }
        }

    ?>

    </div>
    </form>
</body>
</html>