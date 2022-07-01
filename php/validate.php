<?php

session_start();

# Connection information.
$HOST = "db.luddy.indiana.edu";
$USER = "i494f21_team02";
$PASS = "my+sql=i494f21_team02";
$DATA = "i494f21_team02";

$CONN = new mysqli($HOST, $USER, $PASS,$DATA);

# Sign-In Authentication
if (isset($_GET["ticket"])) {

    # Get the ticket from the URL.
    $ticket = $_GET['ticket'];
    # echo $ticket;

    # Set up the URL for validation with CAS.
    $VAL = "https://idp.login.iu.edu/idp/profile/cas/serviceValidate?ticket=";
    $SVC = "&service=https://cgi.luddy.indiana.edu/~team02/php/validate.php";
    $URL = $VAL . $ticket . $SVC;
    # echo $url;

    # After validation, grab the username.
    $contents = file_get_contents($URL);
    # echo $username;

    $dom = new DOMDocument();
    $dom->loadXML($contents);
    $xpath = new DOMXPath($dom);
    $node = $xpath->query("//cas:user");
    $username = $node[0]->textContent;

    # Check to see if user is in database yet.
    $query = "SELECT 1 FROM users WHERE uName = '$username'";
    $check = $CONN->query($query);

    # Set the session username.
    $_SESSION['user'] = $username;

    # Check to see if the username is in our users table.
    if (is_null(mysqli_fetch_row($check))) {

        # If they aren't in the database, send them
        # to new profile creation.
        header("Location: new-profile.php");
    }

    else {

        # Else, send them to the index page.
        header( "Location: ../index.php" );
    }
}

# On new profile creation, redirect the user to the index.
else if (isset($_SESSION['user'])) {

    header("Location: ../index.php");
}

# Redirect the user back to login if they have not been authenticated or
# made a new profile.
else {
    header("Location: ../login.html");
}