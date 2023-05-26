<?php
    $host = 'localhost';
    $db = 'animes';
    $user = 'Test';
    $pw = '12345';
    $conn = new mysqli($host, $user, $pw, $db);
    if ($conn->connect_error) {
    die ("Connection failed: " . $conn->connect_error);
    } else {
    }
?>