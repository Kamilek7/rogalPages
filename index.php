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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
        
        <form action="index.php" class="search">
            <input type="text" placeholder="Wyszukaj..." name="search">
            <button type="submit"><i class="fa fa-search"></i></button>
        </form>
        <main>
        <?php
            if (!empty($_SESSION['login']))
                echo "<div id='addPostButton' onclick='showPostAddingWindow()'>Dodaj nowy post!</div><div id='addPostForm'><form id='formAddPost' action='addPost.php' method='post'><div style='font-size: 2.2vw; margin-top: 2vw; border-bottom: 1px solid #404040; margin-bottom:2vw; text-align:center;'> Dodaj swoj post do portalu</div><p>Tresc:</p><textarea maxlength='700' name='content' rows='10' cols='50' wrap='hard'></textarea><input type='submit' value='Wrzuc'> </form></div></div>";
            ?>

            <div id='body'>
                <?php 
                
                function processPostsAndComments($sql, $polaczenie)
                {
                    $result = $polaczenie->query($sql);
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) 
                        {   
                            $text2show = "<section><div class='post'>";
                            $text2show .= "<div style='display:flex;'><div class='avatar'><img src='avatars/".$row['avatar']."' width='50px' height='50px'> </div>";
                            $text2show .= "<div class='postUser'>" . $row['name'] . "</div></div>";
                            $text2show .= "<div class='text'>" . $row['content'] . "</div>";
                            $date = str_replace(" ", " | ",$row['datePosted']);
                            $likesinfo = $row['likes'];
                            $numLikes = substr_count($likesinfo,';');
                            $text2show .= "<div style='display:flex;'><div style='height:35px;display:flex; align-items:center;'><div class=";
                            if (!empty($_SESSION['login']))
                            {
                                if (substr_count($likesinfo, $_SESSION['login']))
                                {
                                    $text2show .= "'likesLiked' onclick='postsUnlike(".$row['postID'].",".$_SESSION['login'] .")'";
                                }
                                else
                                {
                                    $text2show .= "'likesNotLiked' onclick='postsLike(".$row['postID'].",".$_SESSION['login'].")'";
                                }
                            }
                            else
                            {
                                $text2show .= "'likesDeactivated'";
                            }
                            $text2show .= " name='" . $row['postID'] . "'";
                            $text2show .= ">❤︎</div><div id='" . $row['postID']."num'> $numLikes</div></div>";
                            $text2show .= "<div class='postInfo'>$date  | ". $row['login']. "</div></div>";
    
                            $sqlComments = "SELECT comments.ID, comments.content,comments.likes, comments.date, users.name, users.login, users.avatar FROM comments JOIN users ON comments.userID = users.ID WHERE comments.postID =".$row['postID']." ORDER BY comments.date DESC;";
                            $resultComments = $polaczenie->query($sqlComments);
                            $text2show .= "<details>";
                            $text2show .="<summary>Komentarze (". $resultComments->num_rows . ")</summary>";
                            if (!empty($_SESSION['login']))
                            $text2show.= "<div id='addCommentButton' onclick='showCommentAddingWindow(".$row['postID'].")'>Dodaj komentarz</div>";
                        
                            if ($resultComments->num_rows > 0)
                            {
                                while ($rowCom = $resultComments->fetch_assoc())
                                {
    
                                    $text2show.= "<div class='comment'>";
                                    $text2show .= "<div style='display:flex;'><div class='avatar'><img src='avatars/".$rowCom['avatar']."' width='40px' height='40px'> </div>";
                                    $text2show.= "<div class='commentUser'>" . $rowCom['name'] . "</div></div>";
                                    $text2show.= "<div class='textComment'>" . $rowCom['content'] . "</div>";
                                    $dateCom = str_replace(" ", " | ",$rowCom['date']);
                                    $likesinfo = $rowCom['likes'];
                                    $numLikes = substr_count($likesinfo,';');
                                    $text2show .= "<div style= 'display:flex;'> <div style='height:35px;display:flex; align-items:center;'><div class=";
                                    if (!empty($_SESSION['login']))
                                    {
                                        if (substr_count($likesinfo, $_SESSION['login']))
                                        {
                                            $text2show .= "'likesLiked' onclick='commentsUnlike(".$rowCom['ID'].",".$_SESSION['login'] .")'";
                                        }
                                        else
                                        {
                                            $text2show .= "'likesNotLiked' onclick='commentsLike(".$rowCom['ID'].",".$_SESSION['login'].")'";
                                        }
                                    }
                                    else
                                    {
                                        $text2show .= "'likesDeactivated'";
                                    }
                                    $text2show .= " name='" . $rowCom['ID']."com'";
                                    $text2show .= ">❤︎</div><div id='" . $rowCom['ID']."numC'> $numLikes</div></div>";
                                    $text2show.= "<div class='postInfo'>$dateCom | ". $rowCom['login']. "</div></div>";
                                }
                            }
                            $text2show .= "</details></div></section>";
                            echo $text2show;
                        }
                        return $result->num_rows;
                    }
                    else
                    {
                        return false;
                    }
                }

                $numPages = 0;
                $sql = "";
                if (empty($_GET['search']))
                {
                    $page = 1;
                    if (!empty($_GET['page']))
                    {
                        $page = $_GET['page'];
                    }
                    $page = ($page-1)*5;
                    $sql = "SELECT posts.ID as postID, posts.likes, posts.content, posts.datePosted, users.name, users.login, users.avatar
                    FROM posts JOIN users  ON posts.userID = users.ID ORDER BY posts.ID DESC LIMIT 5 OFFSET $page;";
                    
                    processPostsAndComments($sql, $polaczenie);
                    $sqlCount = "SELECT posts.ID as postID from posts";
                    $res = $polaczenie->query($sqlCount);
                    $numPosts = $res->num_rows;

                    $numPages = ceil($numPosts/5);
                    if ($numPages==1)
                        $numPages = 0;
                
                }
                else
                {
                    $page = 1;
                    if (!empty($_GET['page']))
                    {
                        $page = $_GET['page'];
                    }

                    $page = ($page-1)*5;
                    echo "<div style='font-size:5.5vh; margin-top:5vh; margin-left:auto;margin-right:auto; text-align:center;'> Wyszukane posty: </div>";
                    $sql = "SELECT posts.ID as postID, posts.likes, posts.content, posts.datePosted, users.name, users.login, users.avatar
                    FROM posts JOIN users  ON posts.userID = users.ID WHERE posts.content LIKE '%".$_GET['search']."%' ORDER BY posts.ID DESC LIMIT 5 OFFSET $page;";
                
                    processPostsAndComments($sql, $polaczenie);

                    $sqlCount = "SELECT posts.ID as postID from posts WHERE posts.content LIKE '%".$_GET['search']."%';";
                    $res = $polaczenie->query($sqlCount);
                    $numPosts = $res->num_rows;

                    $numPages = ceil($numPosts/5);
                    if ($numPages==1)
                        $numPages = 0;
                    if (!$numPosts)
                    {
                        echo "<div style='font-size:3vh; margin-top:1vh; margin-left:auto;margin-right:auto; text-align:center;'>Brak postow.</div>";
                    }
                    
                    echo "<div style='font-size:5.5vh; margin-top:5vh; margin-left:auto;margin-right:auto; text-align:center;'> Posty po uzytkownikach: </div>";
                    $sql = "SELECT posts.ID as postID, posts.likes, posts.content, posts.datePosted, users.name, users.login 
                    FROM posts JOIN users  ON posts.userID = users.ID WHERE users.name LIKE '%".$_GET['search']."%' ORDER BY posts.ID DESC;";
                
                    $numPosts = processPostsAndComments($sql, $polaczenie);
                    $numPages = ceil($numPosts/5);
                    if ($numPages==1)
                        $numPages = 0;
                    if (!$numPosts)
                    {
                        echo "<div style='font-size:3vh; margin-top:1vh; margin-left:auto;margin-right:auto; text-align:center;'>Brak postow.</div>";
                    }
                }
                
                if ($numPages>0)
                {
                    echo "<div id='pages'>";
                    $search = "";
                    $current = 1;
                    if (!empty($_GET['search']))
                    {
                        $search = $_GET['search'];
                    }
                    if (!empty($_GET['page']))
                    {
                        $current = $_GET['page'];
                    }
                    for ($i =0 ;$i<$numPages; $i++)
                    {
                        $page = $i+1;
                        $class = 'page';
                        if ($current==$page)
                            $class= 'pageCrnt';
                        echo "<a href='index.php?search=$search&page=$page'><button type='submit' class='$class'>$page</button></a>";
                    }
                    echo "</div>";
                }                
                ?>

            </div>
            <div id='addCommentForm'><form id='formAddComment' action='addComment.php' method='post'><div id='addCommentDiv'> Dodaj komentarz do wpisu</div><p>Tresc:</p><textarea maxlength='400' name='content' rows='10' cols='50' wrap='hard'></textarea><input type='submit' value='Wrzuc'><input id= 'IDIDID' type='number' value='0' name='postId' style='display:none;'> </form></div>
        </main>
    </div>
</body>

<script>
        function setCookie(name, value, exp) 
        {
            const d = new Date();
            d.setTime(d.getTime() + (exp*24*60*60*1000));
            let expires = "expires="+ d.toUTCString();
            document.cookie = name + "=" + value + ";" + expires + ";path=/";
        }
        function getCookie(cname) 
        {
            let name = cname + "=";
            let ca = document.cookie.split(';');
            for(let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) == ' ') {
                c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
                }
            }
            return "";
        }

        function showPostAddingWindow()
        {
            let form = document.getElementById("addPostForm");
            form.id = "addPostFormActivated";
        }
        function showCommentAddingWindow(postID)
        {
            console.log(postID);
            form = document.getElementById("addCommentForm");
            form.id = "addPostFormActivated";
            form1 = document.getElementById("addCommentDiv");
            form1.id = "addCommentDivActivated";
            document.getElementsByName("postId")[0].setAttribute('value', postID);

        }
        function postsLike(id, who)
        {
            document.getElementsByName(id)[0].className = "likesLiked";
            document.getElementsByName(id)[0].setAttribute('onclick', "postsUnlike(" + id +"," +who+ ")");
            document.getElementById(id+"num").innerHTML = parseInt(document.getElementById(id+"num").innerHTML) + 1;
            let likes = getCookie("likes");

            console.log(likes);
            if (likes=="" || likes=="temp" || !likes)
            {
                likes = {};
                likes["" + id] = {ID: who, liked: true};
            }
            else
            {
                likes = JSON.parse(likes);
                likes["" + id] = {ID: who, liked: true};
            }
            console.log(likes);
            setCookie("likes", "", -1);

            setCookie("likes", JSON.stringify(likes), 5000000);
        }
        function postsUnlike(id, who)
        {
            document.getElementsByName(id)[0].className = "likesNotLiked";
            document.getElementsByName(id)[0].setAttribute('onclick', "postsLike(" + id +"," + who+ ")");
            document.getElementById(id+"num").innerHTML = parseInt(document.getElementById(id+"num").innerHTML) - 1;

            
            let likes = getCookie("likes");
            console.log(likes);
            if (likes=="" || likes=="temp")
            {
                likes = {};
                likes["" + id] = {ID: who, liked: false};
            }
            else
            {
                likes = JSON.parse(likes);
                likes["" + id] = {ID: who, liked: false};
            }
            console.log(likes);
            setCookie("likes", "", -1);
            setCookie("likes", JSON.stringify(likes), 5000000);
        }
        function commentsLike(id, who)
        {
            document.getElementsByName(id+"com")[0].className = "likesLiked";
            document.getElementsByName(id+ "com")[0].setAttribute('onclick', "commentsUnlike(" + id +"," +who+")");
            document.getElementById(id+"numC").innerHTML = parseInt(document.getElementById(id+"numC").innerHTML) + 1;
            let likes = getCookie("likesC");

            console.log(likes);
            if (likes=="" || likes=="temp" || !likes)
            {
                likes = {};
                likes["" + id] = {ID: who, liked: true};
            }
            else
            {
                likes = JSON.parse(likes);
                likes["" + id] = {ID: who, liked: true};
            }
            console.log(likes);
            setCookie("likesC", "", -1);

            setCookie("likesC", JSON.stringify(likes), 5000000);
        }
        function commentsUnlike(id, who)
        {
            document.getElementsByName(id+"com")[0].className = "likesNotLiked";
            document.getElementsByName(id+ "com")[0].setAttribute('onclick', "commentsLike(" + id +"," +who+")");
            document.getElementById(id+"numC").innerHTML = parseInt(document.getElementById(id+"numC").innerHTML) - 1;
            
            let likes = getCookie("likesC");
            console.log(likes);
            if (likes=="" || likes=="temp")
            {
                likes = {};
                likes["" + id] = {ID: who, liked: false};
            }
            else
            {
                likes = JSON.parse(likes);
                likes["" + id] = {ID: who, liked: false};
            }
            console.log(likes);
            setCookie("likesC", "", -1);
            setCookie("likesC", JSON.stringify(likes), 5000000);
        }
    </script>
</html>
