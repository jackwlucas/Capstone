<?php

# Functions.php contains site-wide functionality.
include "php/functions.php";

?>

<!doctype html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">

    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- Link to jquery for post verification. -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Favicon for the platform. -->
    <link rel="shortcut icon" type="image/jpg" href="favicon.ico"/>

    <!-- main.css and normalize.css work to provide cross-browser consistency of styling and structure. -->
    <link rel="stylesheet" href="./css/main.css">
    <link rel="stylesheet" href="./css/normalize.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <!-- getTheme() grabs the current style sheet which handles styling across the platform. -->
    <?php getTheme($CONN, $UID) ?>

    <title>Home</title>

</head>
<body>

<div id="body">

    <nav>
        <ul>

            <li class="list">
                <a href="profile.php" onclick="activateLink(this)">
                    <ion-icon name="person-outline"></ion-icon>
                </a>
            </li>

            <li class="list active">
                <a href="index.php" onclick="activateLink(this)">
                    <ion-icon name="home-outline"></ion-icon>
                </a>
            </li>

            <li class="list">
                <a href="groups.php" onclick="activateLink(this)">
                    <ion-icon name="people-outline"></ion-icon>
                </a>
            </li>

            <li class="list">
                <a href="announcements.php" onclick="activateLink(this)">
                    <ion-icon name="megaphone-outline"></ion-icon>
                </a>
            </li>

            <li class="list">
                <a href="events.php" onclick="activateLink(this)">
                    <ion-icon name="calendar-outline"></ion-icon>
                </a>
            </li>

            <li class="list">
                <a href="map.php" onclick="activateLink(this)">
                    <ion-icon name="map-outline"></ion-icon>
                </a>
            </li>

            <li class="list">
                <a href="mainhealth.php" onclick="activateLink(this)">
                    <ion-icon name="hammer-outline"></ion-icon>
                </a>
            </li>

            <li class="list">
                <a href="settings.php" onclick="activateLink(this)">
                    <ion-icon name="cog-outline"></ion-icon>
                </a>
            </li>

        </ul>
    </nav>

    <div class="container">

        <h1>Home</h1>

        <div class="content">
            <div class="timeline">
                <?php displayPosts($CONN, $USERNAME, getPosts($CONN, $USERNAME, "ALL")); ?>
            </div>
        </div>

        <div id="create-button" onclick="openForm('P')">+</div>
    </div>
</div>


<!-- Window containing the two post forms. -->
<div class="post-window" id="post-window" style="display:none;">

    <div id="close-form" onclick="closeForm('P')">X</div>

    <!-- Buttons that allow us to switch between the two post types. -->
    <div class="post-type">
        <p class="post-active" id="aType" onclick="switchType(this)">Announcement</p>
        <div></div>
        <p class="post-inactive" id="eType" onclick="switchType(this)">Event</p>
    </div>

    <!-- The announcement form which is our default form. -->
    <form method="POST" action="php/form-handlers.php" class="post-form" id="post-form">

        <!-- Div contains the content that we change on button press. -->
        <div>
            <label for="announcement"></label>
            <input type="checkbox" id="announcement" name="announcement" checked hidden>

            <label for="aTitle">Title</label>
            <input type="text" placeholder="Enter Title" id="aTitle" name="aTitle" required>

            <label for="aDesc">Description</label>
            <input type="text" placeholder="Enter Description" id="aDesc" name="aDesc" required>
        </div>

        <?php groupSelection($GROUPS); ?>

        <input type="submit" value="Post" class="post-submit" id="post-submit" name="post-submit">

    </form>
</div>

<div id="delete-form"></div>

<!-- Scripts for navigation and page. -->
<script>
    function activateLink(link) {

        let links = document.querySelectorAll('a');
        let lists = document.querySelectorAll('li');

        for (let i = 0; i < links.length; i++) {

            if (links[i] === link) {

                console.log("Success: Link " + (i + 1));
                document.querySelector('.active').classList.remove('active');
                lists[i].classList.add('active');
            }
        }
    }
</script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="js/index.js"></script>


</body>
</html>
