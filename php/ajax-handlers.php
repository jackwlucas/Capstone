<?php

include "functions.php";


# If the user has typed in the group search bar.
if (isset($_POST['groupSearchValue'])) {

    # If the bar is empty, display all groups.
    if ($_POST['groupSearchValue'] == '') {
        viewGroups($CONN, $UID);
    }

    # If the bar is not empty...
    else {

        # Display groups like the search value.
        viewGroups($CONN, $UID, $_POST['groupSearchValue']);
    }
}


# If the user has clicked the create post button.
if (isset($_POST['createPostButton'])) {

    # Check to see if they are in any groups.
    echo ($CONN -> query("SELECT COUNT(*) FROM usersgroups WHERE uid = '$UID'")) -> fetch_array()[0];
}
