<?php

# Functions.php contains site-wide functionality.
include "functions.php";

?>

<!doctype html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">

    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- Favicon for the platform. -->
    <link rel="shortcut icon" type="image/jpg" href="../favicon.ico"/>

    <!-- main.css and normalize.css work to provide cross-browser consistency of styling and structure. -->
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <!-- getTheme() grabs the current style sheet which handles styling across the platform. -->
    <?php getTheme($CONN, $UID, 1) ?>

    <title>Manage Group</title>

</head>
<body>

<div id="body">

    <nav>
        <ul>

            <li class="list">
                <a href="../profile.php" onclick="activateLink(this)">
                    <ion-icon name="person-outline"></ion-icon>
                </a>
            </li>

            <li class="list">
                <a href="../index.php" onclick="activateLink(this)">
                    <ion-icon name="home-outline"></ion-icon>
                </a>
            </li>

            <li class="list active">
                <a href="../groups.php" onclick="activateLink(this)">
                    <ion-icon name="people-outline"></ion-icon>
                </a>
            </li>

            <li class="list">
                <a href="../announcements.php" onclick="activateLink(this)">
                    <ion-icon name="megaphone-outline"></ion-icon>
                </a>
            </li>

            <li class="list">
                <a href="../events.php" onclick="activateLink(this)">
                    <ion-icon name="calendar-outline"></ion-icon>
                </a>
            </li>

            <li class="list">
                <a href="../map.php" onclick="activateLink(this)">
                    <ion-icon name="map-outline"></ion-icon>
                </a>
            </li>

            <li class="list">
                <a href="../mainhealth.php" onclick="activateLink(this)">
                    <ion-icon name="hammer-outline"></ion-icon>
                </a>
            </li>

            <li class="list">
                <a href="../settings.php" onclick="activateLink(this)">
                    <ion-icon name="cog-outline"></ion-icon>
                </a>
            </li>

        </ul>
    </nav>

    <div class="container">

        <h1>Manage Group</h1>

        <div class="content">

            <div class="sub-container">
                <?php groupForm($CONN, $UID, $GID); ?>
            </div>

            <div class="sub-container">
                <?php memberForm($CONN, $UID, $GID); ?>
            </div>

        </div>

        <div id="manage-group-settings" class="manage-group-settings" onclick="openForm(`S`)">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings" width="0" height="0" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="" stroke-linecap="round" stroke-linejoin="round">
                <path fill="white" stroke="" d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                <circle fill="white" cx="12" cy="12" r="3" />
            </svg>
        </div>

    </div>

</div>

<?php settingsForm($CONN, $UID, $GID); ?>

<div id="manage-group-delete-form" style="display: none;">
    <form method="POST" action="form-handlers.php" class="manage-group-delete-form">
        <h2>Delete Group</h2>

        <h4>Are you sure you want to delete this group?<br>
            <span>This cannot be undone.</span></h4>

        <div>
            <input type="button" id="cancel-button" name="cancel-button" value="Cancel" onclick="closeForm('D')">
            <input type="submit" id="delete-button" name="delete-button" value="Delete Group">
        </div>
    </form>
</div>

<div id="manage-group-leave-form" style="display: none">
    <form method="POST" action="form-handlers.php" class="manage-group-leave-form">
        <h2>Leave Group</h2>

        <h4>Are you sure you want to leave this group?<br>
            <span>This cannot be undone.</span></h4>

        <div>
            <input type="button" id="cancel-button" name="cancel-button" value="Cancel" onclick="closeForm('L')">
            <input type="submit" id="leave-button" name="leave-button" value="Leave Group">
        </div>
    </form>
</div>

<script src="../js/manage-group.js"></script>
<script>

    const list = document.querySelectorAll('.list');

    function activeLink() {
        list.forEach((item) => item.classList.remove('active'));
        this.classList.add('active');
    }

    list.forEach((item) => item.addEventListener('click',activeLink));

</script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</body>
</html>
