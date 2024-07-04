<?php
    include 'default_header.php';
?>

<?php
    session_start();

    if (isset($_POST['login'])) {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        $sql = "SELECT * FROM users WHERE user = '$username'";

        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        if ($row && password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit;
        }

        header("Location: login.html?error=1");
        exit;
    }
    
?>