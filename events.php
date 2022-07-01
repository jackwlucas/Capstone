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

    <!-- Favicon for the platform. -->
    <link rel="shortcut icon" type="image/jpg" href="favicon.ico"/>

    <!-- main.css and normalize.css work to provide cross-browser consistency of styling and structure. -->
    <link rel="stylesheet" href="./css/main.css">
    <link rel="stylesheet" href="./css/normalize.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <!-- getTheme() grabs the current style sheet which handles styling across the platform. -->
    <?php getTheme($CONN, $UID) ?>

    <title>Events</title>

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

            <li class="list">
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

            <li class="list active">
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

        <h1>Events</h1>

        <div class="content">

            <div class="timeline">

                <?php displayPosts($CONN, $USERNAME, getPosts($CONN, $USERNAME, "EVENTS")); ?>

            </div>
        </div>
    </div>
</div>

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

    // Disable the buttons across the page.
    let footers = document.querySelectorAll('.post-footer');
    footers.forEach((footer) => {
        footer.innerHTML = '';
    })
</script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</body>
</html>
