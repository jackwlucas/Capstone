<?php

# Functions.php contains site-wide functionality.
include "functions.php";

function clean($VALUE) : string
{

    return trim($VALUE);
}

# ----------[ PROFILE PAGE ]---------- #

# Code block handles editing the profile.
if (isset($_POST['edit-profile-submit'])) {

    # Grab the information from the inputs.
    $f = clean($_POST["FirstName"]);
    $l = clean($_POST["LastName"]);
    $g = $_POST["Gender"];
    $s = $_POST["School"];
    $b = clean($_POST["Biography"]);

    # Create the update query.
    $query = "UPDATE users 
              SET fname = ?,
                  lName = ?,
                  gender = ?,
                  school = ?,
                  description = ?
              WHERE uid = ?";

    # Prepare the query, bind the parameters, and execute.
    $query = $CONN -> prepare($query);
    $query -> bind_param('ssssss', $f, $l, $g, $s, $b, $UID);
    $query -> execute();

    # Direct the user back to the profile.
    header("Location: ../profile.php");
}


# ----------[ INDEX PAGE ]---------- #

# Code block handles creating posts.
if (isset($_POST['post-submit'])) {

    # Get the group that is being posted to.
    $GID = intval($_POST['gSelect']);

    # Check to see if the post is an announcement.
    if (isset($_POST['announcement'])) {

        # If it is an announcement, grab the title,
        # description, and group number.
        $t = clean($_POST['aTitle']);
        $d = clean($_POST['aDesc']);

        # Set up the query to insert the data.
        $query = "INSERT INTO announcements (title, description, timestamp, uid, gid)
                   VALUES (?, ?, NOW(), ?, ?)";

        $query = $CONN -> prepare($query);
        $query -> bind_param('ssii', $t, $d, $UID, $GID);
    }

    # If it isn't an announcement, then it is an
    # event.
    else {

        # Grab the title, description, location,
        # time, date, and group.
        $t  = clean($_POST['eTitle']);
        $d  = clean($_POST['eDesc']);
        $l  = clean($_POST['eLocation']);
        $tm = $_POST['eTime'];
        $dt = $_POST['eDate'];

        # Set up the query to insert the data.
        $query = "INSERT INTO events (title, description, location, time, date, timestamp, uid, gid)
                   VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)";

        $query = $CONN -> prepare($query);
        $query -> bind_param('sssssii', $t, $d, $l, $tm, $dt, $UID, $GID);
    }

    # Run the query.
    $query -> execute();

    # Direct the user back to the index page.
    header('Location: ../index.php');
}

# Code block handles liking posts.
if (isset($_POST['post-like-button'])) {

    # Get the index and position from the form.
    $i = $_POST['index'];

    # Grab the post type and ID from the POSTS array.
    $postType = $POSTS[$i][0];
    $postID   = $POSTS[$i][1];

    # Check to see if the user has liked the post.
    $pLike = ($CONN -> query("SELECT COUNT(1) 
                                    FROM `post-likes` 
                                    WHERE pid  = '$postID' 
                                      AND type = '$postType' 
                                      AND uid  = '$UID'")) -> fetch_array()[0];

    # If the user has liked the post, remove the
    # like from the table.
    if ($pLike == 1) {

        $CONN -> query("DELETE FROM `post-likes` 
                              WHERE pid = '$postID' 
                                AND type = '$postType' 
                                AND uid = '$UID'");
    }

    # If the user has not liked the post, add the like
    # to the table.
    else {

        $CONN->query("INSERT INTO `post-likes` (pid, type, uid) 
                            VALUES ('$postID', '$postType', '$UID')");
    }

    # Direct the user back to the index page.
    header("Location: ../index.php");
}

# Code block handles replying to a post.
if (isset($_POST['post-reply-button'])) {

    # Grab the index position and comment.
    $i = $_POST['index'];
    $comment = clean($_POST['comment']);

    # Grab the post type and ID from the POSTS array.
    $postType = $POSTS[$i][0];
    $postID   = $POSTS[$i][1];

    # Insert the data into the post-comments table.
    $query = "INSERT INTO `post-comments` (pid, type, comment, timestamp, uid)
              VALUES (?, ?, ?, NOW(), ?)";

    # Prepare the query, bind the parameters, and execute.
    $query = $CONN -> prepare($query);
    $query -> bind_param('issi', $postID, $postType, $comment, $UID);
    $query -> execute();

    # Finally, redirect the user to the page.
    header("Location: ../index.php");
}

# Code block handles deleting posts.
if (isset($_POST['post-delete-button'])) {

    # Grab the index position of the post.
    $i = intval($_POST['index']);

    # Grab the post type and ID from the POSTS array.
    $postType = $POSTS[$i][0];
    $postID   = $POSTS[$i][1];

    # Check the type of the post.
    switch($postType) {

        # If it is an announcement, remove the announcement.
        case "Announcement" :
            $CONN -> query("DELETE FROM announcements 
                                  WHERE aid = '$postID' AND uid = '$UID'");
            break;

        # If it is an event, remove the event.
        case "Event" :
            $CONN -> query("DELETE FROM events 
                                  WHERE eid = '$postID' AND uid");
            break;
    }

    # Direct the user back to the index page.
    header("Location: ../index.php");
}

# Code block handles liking comments.
if (isset($_POST['comment-like-button'])) {

    # Get the index and position from the form.
    $i = $_POST['index'];

    # Grab the post type and ID from the POSTS array.
    $CID  = $COMMENTS[$i];

    # Check to see if the user has liked the post.
    $cLike = ($CONN -> query("SELECT COUNT(1) 
                                    FROM `post-likes` 
                                    WHERE pid  = '$CID' 
                                      AND type = 'Comment' 
                                      AND uid  = '$UID'")) -> fetch_array()[0];

    # If they have already liked the comment, remove the like.
    if ($cLike == 1) {

        $CONN -> query("DELETE FROM `post-likes` 
                              WHERE pid = '$CID' 
                                AND type = 'Comment' 
                                AND uid = '$UID'");
    }

    # Else, add a like.
    else {

        $CONN->query("INSERT INTO `post-likes` (pid, type, uid) 
                            VALUES ('$CID', 'Comment', '$UID')");
    }

    # Direct the user back to the index page.
    header("Location: ../index.php");
}

# Code block handles deleting comments.
if (isset($_POST['comment-delete-button'])) {

    # Grab the index from the form.
    $i = intval($_POST['index']);

    # Grab the CID from the array.
    $CID = $COMMENTS[$i];

    # Remove the comment from the comments table.
    $CONN -> query("DELETE FROM `post-comments`
                          WHERE cid = '$CID' AND uid = '$UID'");

    # Direct the user back to the index page.
    header("Location: ../index.php");
}


# ----------[ GROUP PAGE ]---------- #

# Code block handles the creation of new groups.
if (isset($_POST['group-submit'])) {

    # Grab the name and description from the form.
    # Clean the data.
    $name = clean($_POST["group-name"]);
    $desc = clean($_POST["group-desc"]);
    $limit = 10000;
    $status = 1;
    $perms = 2;

    $query = "INSERT INTO `groups` (title, `description`, `limit`, `status`)
              VALUES (?, ?, ?, ?)";
    $query = $CONN -> prepare($query);
    $query -> bind_param('ssii', $name, $desc, $limit, $status);
    $query -> execute();

    $newGroup = ($CONN -> query("SELECT gid FROM `groups` WHERE title = '$name'")) -> fetch_array()[0];

    $insert = "INSERT INTO usersgroups (uid, gid, permissions) 
              VALUES (?, ?, ?)";
    $insert = $CONN -> prepare($insert);
    $insert -> bind_param('iii', $UID, $newGroup, $perms);
    $insert -> execute();

    # Direct the user back to the groups page.
    header("Location: ../groups.php");
}

# Code block handles joining of new groups.
if (isset($_POST['join-group-submit'])) {

    # Grab the GID.
    $GID = $_POST['gid'];
    $gName = $_POST['gName'];

    # Add the entry to usersgroups.
    $CONN -> query("INSERT INTO usersgroups (uid, gid, permissions) 
                          VALUES ('$UID', '$GID', 0)");

    # Redirect the user to groups.
    header("Location: ../groups.php");
}


# ----------[ MANAGE GROUP PAGE ]---------- #

# Code block handles updating the group information.
if (isset($_POST['submit-button'])) {

    # If the user clicks submit, grab the entries
    # in the input boxes.
    $gName = clean($_POST['group-name']);
    $gDesc = clean($_POST['group-desc']);
    $GID = $_POST['group-id'];

    # Then, run the update query.
    $query = "UPDATE `groups`
              SET 
                  title = ?,
                  description = ?
              WHERE gid = ?";

    # Prepare the query, bind the parameters, and execute.
    $query = $CONN -> prepare($query);
    $query -> bind_param('ssi', $gName, $gDesc, $GID);
    $query -> execute();

    # Finally, redirect them to the groups page.
    header("Location: manage-group.php");
}

# Code block handles removing group members.
if (isset($_POST['remove-button'])) {

    # Grab the user IDs from the usersgroups table.
    $members = $CONN -> query("SELECT uid FROM usersgroups WHERE gid = '$GID'");

    # Iterate through the members of the group.
    foreach ($members as $m) {

        # Grab the member's ID and create
        # the user string.
        $uid = $m['uid'];
        $usr = 'user-' . $uid;

        # If the user string has been selected in the form.
        if (isset($_POST[$usr])) {

            # Remove the user from the group.
            $CONN -> query("DELETE FROM usersgroups WHERE uid = '$uid'");
        }
    }

    # Upon removing users, redirect them back to this page.
    header("Location: manage-group.php");
}

# Code block handles updating group member permissions.
if (isset($_POST['promote-button'])) {

    # Grab the user IDs from the usersgroups table.
    $members = $CONN -> query("SELECT uid, permissions FROM usersgroups WHERE gid = '$GID'");

    # Iterate through the members.
    foreach ($members as $m) {

        # Grab the user's ID
        $uid = $m['uid'];

        # Get their current permissions.
        # Create the permission string.
        # Grab the value of their new permission from the page.
        $oldPrm = $m['permissions'];
        $usr = 'user-' . $uid . '-permission';
        $newPrm = intval($_POST[$usr]);

        # If their old permission does not equal their
        # new permission value.
        if ($oldPrm != $newPrm) {

            # Update their permissions in the table.
            $CONN -> query("UPDATE usersgroups
                                  SET
                                    permissions = '$newPrm'
                                  WHERE uid = '$uid'");
        }
    }

    # Upon permission change, redirect them back to this page.
    header("Location: manage-group.php");
}

# Code block handles updating the settings of the group.
if (isset($_POST['update-button'])) {

    # Get the limit and status from the form.
    $gLimit = $_POST['group-limit'];
    $gStatus = $_POST['group-status'];

    # If their status is private, set
    # the status to 0.
    if ($gStatus == "private") {
        $gStatus = 0;
    }

    # Else, set their status to 1.
    else {
        $gStatus = 1;
    }

    # Update the group with the new limit and status.
    $CONN -> query("UPDATE `groups` 
                          SET
                              `limit` = '$gLimit',
                              status = '$gStatus'
                          WHERE gid = '$GID'");

    # Upon the update, redirect them back to the manage-group page.
    header("Location: manage-group.php");
}

# Code block handles group deletion and users leaving the group.
if (isset($_POST['delete-button']) || isset($_POST['leave-button'])) {

    # If the user selected to delete the group.
    if (isset($_POST["delete-button"])) {

        # Delete the group.
        $CONN -> query("DELETE FROM `groups` WHERE gid = '$GID'");
    }

    # If the user selected to leave the group.
    if (isset($_POST["leave-button"])) {

        # Remove the user from the group.
        $CONN -> query("DELETE FROM usersgroups WHERE uid='$UID' AND gid='$GID'");
    }

    # Return the user to the groups page.
    header("Location: ../groups.php");
}


# ----------[ MAINTENANCE AND HEALTH PAGE ]---------- #

# Code block handles submitting maintenance and health tickets.
if (isset($_POST['ticket-submit'])) {

    # If a maintenance ticket was submitted.
    if (isset($_POST['maintenance'])) {

        # Get the user's input.
        $do = $_POST['dorm'];
        $fl = $_POST['floor'];
        $ro = $_POST['room'];
        $is = clean($_POST['issue']);
        $de = clean($_POST['description']);

        # Insert it into the table.
        $query = "INSERT INTO maintenance (dorm, floor, room, issue, description, timestamp, uid) 
                  VALUES (?, ?, ?, ?, ?, NOW(), ?)";

        # Prepare the query, bind the parameters, and execute.
        $query = $CONN -> prepare($query);
        $query -> bind_param('siissi', $do, $fl, $ro, $is, $de, $UID);
    }

    # If a health ticket was submitted.
    if (isset($_POST['health'])) {

        # Get the user's inputs.
        $em = $_POST['email'];
        $is = clean($_POST['issue']);
        $de = clean($_POST['description']);

        # Insert it into the table.
        $query = "INSERT INTO health (email, issue, description, timestamp, uid) 
                  VALUES (?, ?, ?, NOW(), ?)";

        # Prepare the query, bind the parameters, and execute.
        $query = $CONN -> prepare($query);
        $query -> bind_param('sssi', $em, $is, $de, $UID);
    }

    $query -> execute();

    # Direct the user back to the maintenance and health page.
    header("Location: ../mainhealth.php");
}


#
if (isset($_POST['resolve-ticket-submit'])) {

    # Grab the ticket type and ID.
    $type = $_POST['ticket-Type'];
    $id = $_POST['ticket-ID'];

    # If the ticket is a maintenance ticket.
    if ($type == "main-ticket") {

        # Update the resolved boolean in the table.
        $CONN -> query("UPDATE maintenance
                              SET resolved = 1
                              WHERE mid = '$id'");
    }

    # If the ticket is a health ticket.
    if ($type == "health-ticket") {

        # Update the resolved boolean in the table.
        $CONN -> query("UPDATE health
                              SET resolved = 1
                              WHERE hid = '$id'");
    }

    # Direct the user back to the page.
    header("Location: ../mainhealth.php");
}


# ----------[ SETTINGS PAGE ]---------- #

# Code block handles updating the user's theme.
if (isset($_POST['theme-button'])) {

    # Grab the theme from the form.
    $theme = intval($_POST['theme']);

    # Update the theme in the table.
    $CONN -> query("UPDATE users SET theme = '$theme' WHERE uid = '$UID'");

    # Direct the user back to the settings page.
    header("Location: ../settings.php");
}
