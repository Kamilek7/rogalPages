<?php
    session_start();
    require_once "baza.php";
    $polaczenie = new mysqli($host, $user, $password, $database);
    if (!empty($_POST['content']))
    {

            $userID = $_SESSION['login'];
            $sql2 = "INSERT INTO posts (userID, content, likes) VALUES ('" . $userID . "', '". $_POST['content'] . "', '');";
            $result1 = $polaczenie->query($sql2);

    }
    header("location: index.php");
?>
