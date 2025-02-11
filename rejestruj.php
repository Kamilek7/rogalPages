<!DOCTYPE html>
<?php
    require_once "baza.php";
    require "processingCookies.php";
    $polaczenie = new mysqli($host, $user, $password, $database);
?>
<html>
    <head>
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Climate+Crisis&family=Smooch+Sans:wght@100..900&display=swap" rel="stylesheet">
    </head>
<body>

    <div id="main">
        <header>
            <div id='head'>Rogal</div>
            <?php
            if (empty($_SESSION['login']))
                echo "<div id='panel'><a href='loguj.php'>Zaloguj się</a><a href='rejestruj.php'>Zarejestruj się</a></div>";
            else
                echo "<div id='panel'><a href='wyloguj.php'>Wyloguj się</a></div>";
            ?>
        </header>
        <main>
            <div id='body'>
                <?php 
                
                    if (!empty($_POST['login']) && !empty($_POST['password']) && !empty($_POST['name']))
                    {
                        $sql = "SELECT * FROM users WHERE login='".$_POST['login']."';";
                        $result = $polaczenie->query($sql);
                        if ($result->num_rows == 0) 
                        {
                            $avatar = "anon.png";
                            if (!empty($_FILES["profilePic"]["name"]))
                            {
                                $avatar= basename($_FILES["profilePic"]["name"]);
                                $target_dir = "avatars/";
                                $target_file = $target_dir . $avatar;
                                $uploadOk = 1;
                                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    
                                move_uploaded_file($_FILES["profilePic"]["tmp_name"], $target_file);
                            }
                            $sql = "INSERT INTO users (login, name, password, avatar) VALUES ('" . $_POST['login']."', '".$_POST['name']."', '".password_hash($_POST['password'], PASSWORD_DEFAULT)."', '$avatar');";
                            $result = $polaczenie->query($sql);
                            $sql = "SELECT ID FROM users WHERE login='".$_POST['login']."';";
                            $result = $polaczenie->query($sql);



                            while ($row = $result->fetch_assoc())
                            {
                                $_SESSION['login'] = $row['ID'];
                            }
                            
                            header("location: index.php");
                        }
                    }
                ?>
                    <div id='loginForm'>
                        <form enctype="multipart/form-data" id='formLogin' action='rejestruj.php' method='post'>
                        <div id='loginHead'>Zarejestruj się</div>
                        <p> Wyświetlana nazwa </p>
                        <input type='text' name='name'>
                        <p> Login </p>
                        <input type='text' name='login'>
                        <p> Hasło </p>
                        <input type='password' name='password'>
                        <input type='file' id='profilePic' name='profilePic' accept=".png, .jpg, .jpeg">

                        <input type='submit' value='Zarejestruj się'>
                        </form>
                    </div>
            </div>
        </main>
    </div>
</body>
</html>
