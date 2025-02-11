<?php
        session_start();
        require_once "baza.php";
        $polaczenie = new mysqli($host, $user, $password, $database);
        if (!empty($_POST['content']))
        {

                $userID = $_SESSION['login'];
                $sql2 = "INSERT INTO comments (userID, content, postID, likes) VALUES ('" . $userID . "', '". $_POST['content'] ."', '".$_POST['postId'] ."', '');";
                $result1 = $polaczenie->query($sql2);
        }
        header("location: index.php");
?>
