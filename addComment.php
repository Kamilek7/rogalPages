<?php
        session_start();
        require_once "baza.php";
        $polaczenie = new mysqli($host, $user, $password, $database);
        if (!empty($_POST['content']))
        {

                $userID = $_SESSION['login'];
                $content = htmlentities($_POST['content'], ENT_QUOTES, 'UTF-8');
                $id = htmlentities($_POST['postId'], ENT_QUOTES, 'UTF-8');
                $result1 = $polaczenie->query(
                    sprintf("INSERT INTO comments (userID, content, postID, likes) VALUES ('$userID ', '%s', '$id', '');", mysqli_real_escape_string($polaczenie, $content))
                );
        }
        header("location: index.php");
?>
