<?php
session_start();
    require_once "baza.php";
    $polaczenie = new mysqli($host, $user, $password, $database);
    
    if (isset($_COOKIE['likes']))
    {
        $temp = json_decode($_COOKIE['likes'], true);
        foreach ($temp as $id => $map) 
        {
            $sql = "SELECT likes FROM posts WHERE ID=$id";
            $result = $polaczenie->query($sql);
            while ($row = $result->fetch_assoc())
            {
                $liked = "";
                if ($map['liked'])
                {
                    if (substr_count($row['likes'], $map['ID']) == 0)
                    {
                        $liked = $row['likes'] . $map['ID'] . ";"; 
                    }
                    else
                    {
                        $liked = $row['likes'];
                    }
                }
                else
                {
                    if (substr_count($row['likes'], $map['ID']))
                    {
                        $liked = str_replace($row['likes'], $map['ID'] . ";", "");
                    }
                }
                $sql1 = "UPDATE posts SET likes = '$liked' WHERE ID=$id;";
                $polaczenie->query($sql1);
            }

        }

        setcookie("likes", "", -1);
    }

    if (isset($_COOKIE['likesC']))
    {
        $temp = json_decode($_COOKIE['likesC'], true);
        foreach ($temp as $id => $map) 
        {
            $sql = "SELECT likes FROM comments WHERE ID=$id";
            $result = $polaczenie->query($sql);
            while ($row = $result->fetch_assoc())
            {
                $liked = "";
                if ($map['liked'])
                {
                    if (substr_count($row['likes'], $map['ID']) == 0)
                    {
                        $liked = $row['likes'] . $map['ID'] . ";"; 
                    }
                    else
                    {
                        $liked = $row['likes'];
                    }
                }
                else
                {
                    if (substr_count($row['likes'], $map['ID']))
                    {
                        $liked = str_replace($row['likes'], $map['ID'] . ";", "");
                    }
                }
                $sql1 = "UPDATE comments SET likes = '$liked' WHERE ID=$id;";
                $polaczenie->query($sql1);
            }

        }

        setcookie("likesC", "", -1);
    }
?>