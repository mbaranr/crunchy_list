<?php
    include 'default_header.php';
?>
<h4>CREATE AN ACCOUNT</h4>

<body>
    <form action = "register.php" method = "POST">
    <div class = "register-container">
       
            <div><input type = "text" name = "username" placeholder="Username"></div>
            <div><input type = "text" name = "password" placeholder="Password"></div>
            <div><input type = "text" name = "repeat_password" placeholder="Repeat password"></div>
            <div><button type = "Register" name = "register">Register</button></div>
        
    <?php

        if (isset($_POST['register'])) {
            $username = mysqli_real_escape_string($conn, $_POST['username']);
            $password = mysqli_real_escape_string($conn, $_POST['password']);
            $Rpassword = mysqli_real_escape_string($conn, $_POST['repeat_password']);

            $sql = "SELECT * FROM users WHERE user = '$username'";
            
            $result = mysqli_query($conn, $sql);
            $queryResults = mysqli_num_rows($result);

            if(strlen($username) > 15){
                echo "<div><p>Username can only be 15 characters long</p></div>";
                exit;
            }

            $password_hashed = password_hash($password, PASSWORD_DEFAULT);

            if($username > 0 && ($password > 4) && ($password == $Rpassword) && $queryResults==0){
        
                $sql = "INSERT INTO users (user, password) VALUES('$username', '$password_hashed')";
                $result = mysqli_query($conn, $sql);
                header("Location: login.html");
                exit;
            }

            if($password < 5){
                echo "<div><p>Please enter a password that is at least 5 digits long</p></div>";
            }

            if($username <= 0){
                echo "<div><p>Please enter valid username</p></div>";
            }

            if($password != $Rpassword){
                echo "<div><p>Passwords don't match</p></div>";
            }

            if($queryResults>0){
                echo "<div><p>The username already exists, please try a new one</p></div>";
            }
        }

    ?>

    </div>
    </form>
</body>
</html>