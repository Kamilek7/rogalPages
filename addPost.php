<?php
    session_start();
    require_once "baza.php";
    $polaczenie = new mysqli($host, $user, $password, $database);
    if (!empty($_POST['content']))
    {
            $content = htmlentities($_POST['content'], ENT_QUOTES, 'UTF-8');
            $userID = htmlentities($_SESSION['login'], ENT_QUOTES, 'UTF-8');

            $result1 = $polaczenie->query(
                sprintf("INSERT INTO posts (userID, content, likes) VALUES ('$userID', '%s', '');",
                mysqli_real_escape_string($polaczenie, $content))
            );

    }
    header("location: index.php");
?>
