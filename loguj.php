<!DOCTYPE html>
<?php
    require_once "baza.php";
    require "processingCookies.php";
    $polaczenie = new mysqli($host, $user, $password, $database);
?>
<html>
    <head>
    <title>Rogal</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Climate+Crisis&family=Smooch+Sans:wght@100..900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
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
                $ERRORCODE = "";
                    if (!empty($_POST['login']) && !empty($_POST['password']) && !empty($_POST['loguj']))
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
                                else
                                {
                                    $ERRORCODE = "Podano niepoprawne hasło!!";
                                }

                            }

                        }
                        else
                        {
                            $ERRORCODE = "Nie znaleziono uzytkownika o podanym nicku!";
                        }
                    }
                    elseif (!empty($_POST['loguj']))
                    {
                        $ERRORCODE = "Podaj dane do wszystich wymaganych pól!";
                    }
                    if ($ERRORCODE!="")
                        echo "<b><p style='color:red;text-align:center;'>$ERRORCODE<p></b>";
                ?>
                    <div id='loginForm'>
                        <form action='loguj.php' method='post'  id='formLogin'>
                        <div id='loginHead'>Zaloguj się</div>
                        <p> Login </p>
                        <input required type='text' name='login'>
                        <p> Hasło </p>
                        <input required type='password' name='password'>
                        <input type='submit' value='Zaloguj się' name='loguj'>
                        </form>
                    </div>
            </div>
        </main>
    </div>
</body>
</html>
