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
            <div id='head'><a href='index.php'>Rogal</a></div>
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
                
                    if (!empty($_POST['login']) && !empty($_POST['password']))
                    {

                        $login = htmlentities($_POST['login'], ENT_QUOTES, 'UTF-8');
                        $result = $polaczenie->query(sprintf("SELECT password, ID FROM users WHERE login='%s';",
                        mysqli_real_escape_string($polaczenie, $login)));
                        
                        if ($result->num_rows == 1) 
                        {
                            while ($row = $result->fetch_assoc())
                            {

                                if (password_verify($_POST['password'],$row['password']))
                                {
                                    $_SESSION['login'] = $row['ID'];
                                    header("location: index.php");
                                }

                            }

                        }
                    }
                ?>
                    <div id='loginForm'>
                        <form action='loguj.php' method='post'  id='formLogin'>
                        <div id='loginHead'>Zaloguj się</div>
                        <p> Login </p>
                        <input type='text' name='login'>
                        <p> Hasło </p>
                        <input type='password' name='password'>
                        <input type='submit' value='Zaloguj się'>
                        </form>
                    </div>
            </div>
        </main>
    </div>
</body>
</html>
