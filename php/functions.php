<?php

# --------------------------[ FUNCTIONS.PHP ]-------------------------- #
#                                                                       #
#   Functions.php handles all PHP functions across NextDorm.            #
#   The functions are separated by function descriptions and headers.   #
#                                                                       #
# --------------------------------------------------------------------- #



# -----------[ PLATFORM FUNCTIONALITY ]------------ #
#                                                   #
#   The code below contains functionality related   #
#   to connecting to the database and handling      #
#   the session across the platform.                #
#                                                   #
# ------------------------------------------------- #


# Create the connection to the database.
$CONN = new mysqli(
    "db.luddy.indiana.edu",
    "i494f21_team02",
    "my+sql=i494f21_team02",
    "i494f21_team02");

# Start the session.
session_start();

# If the session username has not been set,
# redirect the user to the login page.
if (!isset($_SESSION['user'])) {

    header("Location: ../login.html");
}

# Otherwise, take the necessary steps
# on page load.
else {

    # Set the session username and
    # get the user's ID.
    $USERNAME = $_SESSION['user'];
    $EMAIL = $USERNAME . '@iu.edu';
    $UID = getUserID($CONN, $USERNAME);

    # Grab the user's posts. groups, and comments..
    $GROUPS = getGroups($CONN, $USERNAME);
    $POSTS  = getPosts($CONN, $USERNAME);
    $COMMENTS = getComments($CONN, $POSTS);

    # Grab some additional user information.
    $DORM = ($CONN -> query("SELECT dorm FROM users WHERE uid = '$UID'")) -> fetch_assoc()['dorm'];

    if (isset($_POST['mGID'])) {
        $_SESSION['mGID'] = $_POST['mGID'];
    }

    if (isset($_SESSION['mGID'])) {
        $GID = $_SESSION['mGID'];
    }
}



# ------------[ UNIVERSAL FUNCTIONS ]------------ #
#                                                 #
#   The functions below handle a variety of       #
#   things across the platform.                   #
#                                                 #
# ----------------------------------------------- #


# getUserID : Connection, Username -> Int
# Function takes the connection information and session username,
# and returns the user's ID.
function getUserID($CONN, $USERNAME) : int {

    # Return the user's ID.
    return ($CONN -> query("SELECT uid FROM users WHERE uName='$USERNAME'")) -> fetch_assoc()['uid'];
}


# getGroups : Connection, Username -> Array
# Function takes the connection information and session username,
# and returns the user's groups.
function getGroups($CONN, $USERNAME) : array {

    # First, grab the user ID.
    $UID = getUserID($CONN, $USERNAME);

    # Grab the user's groups.
    $userGroups = $CONN -> query("SELECT * FROM `groups` AS g
                                  JOIN usersgroups u ON g.gid = u.gid
                                  WHERE u.uid = '$UID'");

    # Initialize an array to store the group information.
    $gList = array();

    # Iterate through the groups.
    foreach ($userGroups as $ug) {

        $gList[] = [$ug['gid'], $ug['title']];
    }

    # Finally, return the list.
    return $gList;
}


# getTheme : Connection, User ID, Steps -> ECHO
# Function takes the connection information and session username,
# and echos a stylesheet relative to the inputted steps that
# represents the distance from CSS file.
function getTheme($CONN, $UID, $STEPS = 0) {

    # Grab the user's theme and initialize an empty style variable.
    $theme = ($CONN -> query("SELECT theme FROM users WHERE uid = '$UID'")) -> fetch_assoc()['theme'];
    $style = '';

    switch (intval($STEPS)) {
        case 0: $style .= '<link rel="stylesheet" href="./css/'; break;
        case 1: $style .= '<link rel="stylesheet" href="../css/'; break;
    }

    # Select the stylesheet relative to the user's theme.
    switch (intval($theme)) {
        case 0: $style .= 'default.css">'; break;
        case 1: $style .= 'ocean-mint.css">'; break;
        case 2: $style .= 'waterfall.css">'; break;
    }

    # Echo the theme.
    echo $style;
}



# ---------------- #
#   PROFILE PAGE   #
# ---------------- #


# getProfileInfo : Connection, Username -> ECHO
# Function takes a connection and the session username
# and displays the user's profile information.
function getProfileInfo($CONN, $USERNAME) {

    # Grab the user's information from the database.
    $userInfo = ($CONN -> query("SELECT * FROM users 
                                 WHERE uName = '$USERNAME'")) -> fetch_assoc();

    # Grab the important pieces of data.
    $n = $userInfo['fName'] . " " . $userInfo['lName'];
    $u = $userInfo['uName'];
    $e = $userInfo['email'];

    $d = $userInfo['dorm'];
    $b = $userInfo['description'];

    $g = ucfirst($userInfo['gender']);
    $s = $userInfo['school'];

    # Loop through the school name to format the output.
    for ($i = 0; $i < strlen($s); $i++) {

        # Capitalize the first character.
        if ($i == 0) {
            $s[$i] = ucfirst($s[$i]);
        }

        # If the current character is an underscore,
        # replace it with a space.
        if ($s[$i] == '_') {
            $s[$i] = ' ';

            # Also, capitalize the beginning of the
            # next word.
            $s[$i + 1] = ucfirst($s[$i + 1]);
        }
    }

    # Build an array with the data.
    $profileInfo = array(
        'Username'  => $u,
        'Email'     => $e,
        'Full Name' => $n,
        'Gender'    => $g,
        'School'    => $s,
        'Dorm'      => $d,
        'Biography' => $b
    );

    # Initialize an empty variable to store
    # the output for the page.
    $HTML = '';

    # For each piece of data...
    foreach ($profileInfo as $key => $value) {

        # Append a row containing the information
        # to the output.
        $HTML .= '<div>
                       <h3>' . $key   . '</h3>
                       <p>'  . $value . '</p>
                   </div>
                    
                   <hr>';
    }

    # Finally, echo the output.
    echo $HTML;
}


# displayProfileForm : Connection, Username -> ECHO
# Function takes a connection and the session username
# and builds a pre-populated form for editing the profile.
function displayProfileForm($CONN, $USERNAME) {

    # Grab the user's current selections for the form.
    $formInfo = ($CONN -> query("SELECT fName, lName, gender, school, `description` 
                                 FROM users 
                                 WHERE uName = '$USERNAME'")) -> fetch_assoc();

    # Set and format the data.
    $f = $formInfo['fName'];
    $l = $formInfo['lName'];
    $g = ucfirst($formInfo['gender']);
    $s = $formInfo['school'];
    $d = $formInfo['description'];

    # Initialize empty strings for the gender
    # options and school options.
    $gOptions = '';
    $sOptions = '';

    # Store the gender options in an associative array.
    $genderOptions = array(
        'Male' => 'Male',
        'Female' => 'Female',
        'Non-Binary' => 'Non-Binary',
        'Not Identified' => 'Prefer Not to Identify',
        'Other' => 'Other'
    );

    # Store the school options in an associative array.
    $schoolOptions = array(
        'undecided' => 'Undecided',
        'arts_and_sciences' => 'College of Arts and Sciences',
        'art_architecture_design' => 'IU Eskenazi School of Art, Architecture, and Design',
        'kelley' => 'IU Kelley School of Business',
        'education' => 'School of Education',
        'global_international_studies' => 'IU Hamilton Lugar School of Global and International Studies',
        'hutton_honors' => 'IU Hutton Honors College',
        'luddy' => 'IU Luddy school of Informatics, Computing, and Engineering',
        'law' => 'IU Maurer School of Law',
        'media' => 'The Media School',
        'medicine' => 'School of Medicine',
        'music' => 'IU Jacobs School of Music',
        'nursing' => 'School of Nursing',
        'optometry' => 'School of Optometry',
        'spea' => 'IU O\'Neill School of Public and Environmental Affairs',
        'public_health' => 'School of Public Health-Bloomington',
        'social_work' => 'School of Social Work'
    );

    # Iterate through the gender options.
    foreach ($genderOptions as $key => $value) {

        # If the current key is equal to
        # the user's gender.
        if ($key == $g) {

            # Make this gender the selected option in the form.
            $gOptions .= '<option value="' . $key . '" selected>' . $value . '</option>';
        }

        # Otherwise, add the option as normal.
        else {

            $gOptions .= '<option value="' . $key . '">' . $value . '</option>';
        }
    }

    # Iterate through the school options.
    foreach ($schoolOptions as $key => $value) {

        # If the current key is equal to
        # the user's school.
        if ($key == $s) {

            # Make this school the selected option in the form.
            $sOptions .= '<option value="' . $key . '" selected>' . $value . '</option>';
        }

        # Otherwise, add the option as normal.
        else {

            $sOptions .= '<option value="' . $key . '">' . $value . '</option>';
        }
    }

    # Build the form with the relevant data.
    $form = '<form  method="POST" action="php/form-handlers.php" class="profile-form" id="profile-form" style="display: none">
                <div id="close-form" onclick="closeForm()">X</div>
                
                <h2>Edit Profile</h2>

                <label for="FirstName">First Name</label>
                <input type="text" id="FirstName" name="FirstName" value="' . $f . '" required>

                <label for="LastName">Last Name</label>
                <input type="text" id="LastName" name="LastName"  value=' . $l . ' required>
                
                <label for="Gender">Gender</label>
                <select id="Gender" name="Gender" required>
                ' . $gOptions . '    
                </select>
                
                <label for="School">School</label>
                <select id="School" name="School" required>
                ' . $sOptions . '  
                </select>

                <label for="Biography">Biography</label>
                <textarea id="Biography" name="Biography" 
                          maxlength="256" rows="5" cols="15" required>' . $d . '</textarea>

                <input type="submit" name="edit-profile-submit" value="Update" onclick="closeForm()">
        
             </form>';

    echo $form;
}



# -------------- #
#   INDEX PAGE   #
# -------------- #


# getPosts : Connection, Username -> Array
# Function gets all posts connected to the user's groups.
# Option can be : ALL, ANNOUNCEMENTS, EVENTS
function getPosts($CONN, $USERNAME, $OPTION = "ALL") : array {

    # First, we initialize an empty array to store
    # the posts as we iterate through them.
    $posts = array();

    # Get the current user's ID.
    $UID = getUserID($CONN, $USERNAME);

    # Get all groups that the user is in.
    $userGroups = $CONN -> query("SELECT g.gid FROM `groups` AS g
                                  JOIN usersgroups ug ON g.gid = ug.gid
                                  JOIN users u ON ug.uid = u.uid
                                  WHERE u.uid = '$UID'");

    # The next step is two-fold.
    # As we iterate through the user's groups, we must get all
    # the posts connected to said group.
    foreach ($userGroups as $ug) {

        # Get the GID and title of the current group.
        $GID = $ug['gid'];
        $pGroup = ($CONN -> query("SELECT title FROM `groups` WHERE gid='$GID'")) -> fetch_assoc()['title'];

        if ($OPTION == "ALL" || $OPTION == "ANNOUNCEMENTS") {

            # Query will be used to get all announcements from the group.
            $aQuery = "SELECT a.aid, a.title, a.description, a.gid, a.uid, a.timestamp 
                       FROM announcements AS a
                       JOIN `groups` g on a.gid = g.gid
                       WHERE a.gid = '$GID'";

            # In this case, only get the announcements from the group.
            $aPosts = $CONN -> query($aQuery);

            # If there are any announcements in the group.
            if ($aPosts -> num_rows > 0 ) {

                # For each announcement, grab the relevant data.
                foreach ($aPosts as $a) {

                    # First, we want to get the post ID.
                    $pAID = $a['aid'];

                    # Next, we want to get the poster's full name.
                    $pUID  = $a['uid'];
                    $pName = ($CONN -> query("SELECT fName, lName FROM users WHERE uid = '$pUID'")) -> fetch_assoc();
                    $pName = $pName['fName'] . ' ' . $pName['lName'];

                    # Then, we want to grab the relevant post information.
                    $pTitle = $a['title'];
                    $pDesc  = $a['description'];
                    $pTimeS = $a['timestamp'];

                    # Finally, let's grab the likes for the post.
                    $pLikes = ($CONN -> query("SELECT COUNT(1) 
                                               FROM `post-likes` 
                                               WHERE pid = '$pAID' AND type = 'Announcement'")) -> fetch_array()[0];

                    # Now that we have the post's information,
                    # let's build an array to store it and add it to the posts array.
                    # [ Post Type, Post ID, Poster ID, Poster Name, Poster Group, Title, Description, Post Likes, Timestamp ]
                    $posts[] = array("Announcement", $pAID, $pUID, $pName, $pGroup, $pTitle, $pDesc, $pLikes, $pTimeS);
                }
            }

        }

        if ($OPTION == "ALL" || $OPTION == "EVENTS") {

            # Query will be used to get all events from the group.
            $eQuery = "SELECT e.eid, e.title, e.description, e.location, e.time, e.date, e.gid, e.uid, e.timestamp 
                       FROM events AS e
                       JOIN `groups` g on e.gid = g.gid
                       WHERE e.gid = '$GID'";

            # In this case, only get the events from the group.
            $ePosts = $CONN -> query($eQuery);

            # If there are any events in the group.
            if ($ePosts -> num_rows > 0) {

                # For each event, grab the relevant data.
                foreach ($ePosts as $e) {

                    # First, we want to get the post ID.
                    $pEID = $e['eid'];

                    # Next, we want to get the poster's full name.
                    $pUID  = $e['uid'];
                    $pName = ($CONN -> query("SELECT fName, lName FROM users WHERE uid = '$pUID'")) -> fetch_assoc();
                    $pName = $pName['fName'] . ' ' . $pName['lName'];

                    # Then, we want to grab the relevant post information.
                    $pTitle = $e['title'];
                    $pDesc  = $e['description'];
                    $pLoc   = $e['location'];
                    $pTimeS = $e['timestamp'];

                    # Retrieve and format the date. (January 1, 2022)
                    $pDate = date( "F j, Y", strtotime($e['date']));

                    # Retrieve and format the time. (12:30 PM)
                    $pTime = date("g:i A", strtotime($e['time']));

                    # Finally, let's grab the likes for the post.
                    $pLikes = ($CONN -> query("SELECT COUNT(1) 
                                               FROM `post-likes` 
                                               WHERE pid = '$pEID' AND type = 'Event'")) -> fetch_array()[0];

                    # Now that we have the post's information,
                    # let's build an array to store it and add it to the posts array.
                    # [ Post Type, Post ID, Poster ID, Poster Name, Poster Group, Title,
                    #   Description, Event Date, Event Time, Post Likes, Post Timestamp ]
                    $posts[] = array("Event", $pEID, $pUID, $pName, $pGroup, $pTitle,
                        $pDesc, $pLoc, $pDate, $pTime, $pLikes, $pTimeS);
                }
            }
        }
    }

    # Finally, let's sort the posts and return the array.
    usort($posts, function($p1, $p2) {

        # Get the timestamps of the two elements that we want
        # to compare.
        $t1 = strtotime(end($p1));
        $t2 = strtotime(end($p2));

        # Return the time difference for comparison.
        return $t2 - $t1;
    });

    return $posts;
}


# displayPosts : array -> ECHO
# Function takes an array of posts and echos the contents
# to the page.
function displayPosts($CONN, $USERNAME, $POSTS) {

    # First, let's grab the current user's ID.
    $UID = getUserID($CONN, $USERNAME);

    # Iterate through the posts in the array.
    foreach ($POSTS as $p) {

        # First, get the post type and post ID.
        $pType = $p[0];
        $pID   = $p[1];

        # Combine them into a string containing the
        # both pieces of information.
        $pInfo = $pType . '-' . $pID;

        # Next, let's check to see if the user has liked the post.
        $pLike = ($CONN -> query("SELECT COUNT(1) 
                                  FROM `post-likes` 
                                  WHERE pid  = '$pID' 
                                    AND type = '$pType' 
                                    AND uid  = '$UID'")) -> fetch_array()[0];

        # Now that we have the information that we need, let's initialize
        # an empty variable to store the HTML we create.
        $HTML = '';

        # First, let's check to see what kind of post it is.
        if ($p[0] == "Announcement") {

            $HTML .=
                '<div class="timeline-post" id="timeline-post-' . $p[1] . '">
                    <div class="post-info">
                            
                        <div class="post-user-info">
                             <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="12" cy="7" r="4" />
                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                             </svg>
                             <p class="post-user">' . $p[3] . '</p>
                        </div>
                            
                        <div class="post-group-info">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="9" cy="7" r="4" />
                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                            </svg>
                            <p class="post-group">' . $p[4] . '</p>  
                        </div>
                        
                    </div>
                        
                    <div class="post-contents">
                        <h3 class="post-title">' . $p[5] . '</h3>
                        <p class="post-desc">'   . $p[6] . '</p>
                    </div>';

            # If the current user has liked the post,
            # represent that with the input and class.
            if ($pLike == 1) {

                $HTML .=
                    '<div class="post-footer">
                        <form method="POST" action="php/form-handlers.php" class="post-like-form" id="post-like-form" name="post-like-form">
                             <input type="hidden" id="post-like-status" name="post-like-status" value="True">
                                
                             <button type="button" class="post-like-active post-hearts" onclick="likePost(this, `Post`)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-heart" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="white"/>
                                    <path d="M19.5 13.572l-7.5 7.428l-7.5 -7.428m0 0a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" />
                                </svg>
                                <p>' . $p[7] . '</p>
                             </button>
                             <input type="submit" id="post-like-button" name="post-like-button" style="display: none">
                        </form>';
            }

            # If they have not liked the current post,
            # give them the default heart.
            else {

                $HTML .=
                    '<div class="post-footer">
                        <form method="POST" action="php/form-handlers.php" class="post-like-form" id="post-like-form" name="post-like-form">
                             <input type="hidden" id="post-like-status" name="post-like-status" value="False">
                                
                             <button type="button" class="post-like-inactive post-hearts" onclick="likePost(this, `Post`)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-heart" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="white"/>
                                    <path d="M19.5 13.572l-7.5 7.428l-7.5 -7.428m0 0a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" />
                                </svg>
                                <p>' . $p[7] . '</p>
                             </button>
                             <input type="submit" id="post-like-button" name="post-like-button" style="display: none">
                        </form>';
            }
        }

        # If the current post is an event.
        elseif ($p[0] == "Event") {

            $HTML .=
                '<div class="timeline-post" id="timeline-post-' . $p[1] . '">
                    <div class="post-info">
                        
                        <div class="post-user-info">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="12" cy="7" r="4" />
                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                            </svg>
                            <p class="post-user">' . $p[3] . '</p>
                        </div>
                        
                        <div class="post-group-info">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="9" cy="7" r="4" />
                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                            </svg>
                            <p class="post-group">' . $p[4] . '</p>
                        </div>
                    
                    </div>
                    
                    <div class="post-contents">
                        <h3 class="post-title">'   . $p[5] . '</h3>
                        <p class="post-desc">'     . $p[6] . '</p>
                        <br>
                        <h4>Event Details</h4>
                        <p class="post-location">' . $p[7] . '</p>
                        <p class="post-date">'     . $p[8] . '</p>
                        <p class="post-time">'     . $p[9] . '</p>
                    </div>';

            # If the current user has liked the post,
            # represent that with the input and class.
            if ($pLike == 1) {

                $HTML .=
                    '<div class="post-footer">
                        <form method="POST" action="php/form-handlers.php" class="post-like-form" id="post-like-form" name="post-like-form">
                             <input type="hidden" id="post-like-status" name="post-like-status" value="True">
                                
                             <button type="button" class="post-like-active post-hearts" onclick="likePost(this, `Post`)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-heart" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="white"/>
                                    <path d="M19.5 13.572l-7.5 7.428l-7.5 -7.428m0 0a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" />
                                </svg>
                                <p>' . $p[10] . '</p>
                             </button>
                             <input type="submit" id="post-like-button" name="post-like-button" style="display: none">
                        </form>';
            }

            # If they have not liked the current post,
            # give them the default heart.
            else {

                $HTML .=
                    '<div class="post-footer">
                        <form method="POST" action="php/form-handlers.php" class="post-like-form" id="post-like-form" name="post-like-form">
                             <input type="hidden" id="post-like-status" name="post-like-status" value="False">
                                
                             <button type="button" class="post-like-inactive post-hearts" onclick="likePost(this, `Post`)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-heart" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="white"/>
                                    <path d="M19.5 13.572l-7.5 7.428l-7.5 -7.428m0 0a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" />
                                </svg>
                                <p>' . $p[10] . '</p>
                             </button>
                             <input type="submit" id="post-like-button" name="post-like-button" style="display: none">
                        </form>';
            }
        }

        # This block checks to see if the current user posted
        # the current post. If so, then give the post the
        # delete button.
        if ($p[2] == $UID) {

            $HTML .= '<button type="button" class="post-reply" onclick="replyPost(this)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-message-circle-2" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M3 20l1.3 -3.9a9 8 0 1 1 3.4 2.9l-4.7 1" />
                                <line x1="12" y1="12" x2="12" y2="12.01" />
                                <line x1="8" y1="12" x2="8" y2="12.01" />
                                <line x1="16" y1="12" x2="16" y2="12.01" />
                            </svg>
                      </button>
                      <button class="post-delete" id="post-delete" onclick="deletePost(this, `Post`)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-dots" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <circle cx="5" cy="12" r="1" />
                            <circle cx="12" cy="12" r="1" />
                            <circle cx="19" cy="12" r="1" />
                        </svg>        
                      </button>
                      </div>
                      </div>';
        }

        # If the current user is not the poster of the
        # current post, don't give them the button.
        else {

            $HTML .= '<button type="button" class="post-reply" onclick="replyPost(this)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-message-circle-2" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M3 20l1.3 -3.9a9 8 0 1 1 3.4 2.9l-4.7 1" />
                                <line x1="12" y1="12" x2="12" y2="12.01" />
                                <line x1="8" y1="12" x2="8" y2="12.01" />
                                <line x1="16" y1="12" x2="16" y2="12.01" />
                            </svg>
                      </button>
                      <div class="post-footer-hidden"></div>
                      </div>
                      </div>';
        }

        $HTML .= displayComments($CONN, intval($p[1]), $p[0], $UID);

        # Finally, echo the built HTML to the page.
        echo $HTML;
    }
}


# displayComments : Connection, Posts -> Array
# Function gets all comments from all posts on the page.
function getComments($CONN, $POSTS) : array {

    # Initialize an empty array to store the comments.
    $commentsList = array();

    foreach ($POSTS as $p) {

        # Set the post ID and type.
        $PID = $p[1];
        $PTYPE = $p[0];

        # Check to see if there are any comments for the post.
        $count = ($CONN -> query("SELECT COUNT(1) 
                              FROM `post-comments` 
                              WHERE pid = '$PID' AND type = '$PTYPE'")) -> fetch_array()[0];

        if ($count > 0) {

            # Get all the comments for the post.
            $comments = $CONN -> query("SELECT * FROM `post-comments`
                                        WHERE pid = '$PID' AND type = '$PTYPE'");

            foreach ($comments as $c) {

                # Add the comment ID to the array.
                $commentsList[] = $c['cid'];
            }
        }
    }

    # Return the comments.
    return $commentsList;
}


# displayComments : Connection, Post -> ECHO
# Function gets the comments for the current post and
# displays them below the post.
function displayComments($CONN, $PID, $PTYPE, $UID) : string {

    # First, we need to count and see if there are any comments.
    $count = ($CONN -> query("SELECT COUNT(1) 
                              FROM `post-comments` 
                              WHERE pid = '$PID' AND type = '$PTYPE'")) -> fetch_array()[0];

    # Initialize this variable to store the content.
    $HTML = '';

    if ($count > 0) {

        $HTML .= '<div class="comments">';

        # Get all the comments for the post.
        $comments = $CONN -> query("SELECT * FROM `post-comments`
                                    WHERE pid = '$PID' AND type = '$PTYPE'");

        foreach ($comments as $c) {

            $cUID = $c['uid'];
            $cCID = $c['cid'];
            $cComm = $c['comment'];

            $cName = ($CONN -> query("SELECT fName, lName FROM users WHERE uid = '$cUID'")) -> fetch_assoc();
            $cName = $cName['fName'] . ' ' . $cName['lName'];

            $HTML .= '<div class="post-comment">

                        <div class="comment-user-info">
                             <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="12" cy="7" r="4" />
                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                             </svg>
                             <p class="comment-user">' . $cName . '</p>
                        </div>
                        
                        <p class="comment-content">' . $cComm . '</p>';

            # Then, let's get how many likes the comment has.
            $cLikes = ($CONN -> query("SELECT COUNT(1) 
                                  FROM `post-likes` 
                                  WHERE pid  = '$cCID' 
                                    AND type = 'Comment'")) -> fetch_array()[0];

            # Next, let's check to see if the user has liked the post.
            $uLiked = ($CONN -> query("SELECT COUNT(1) 
                                  FROM `post-likes` 
                                  WHERE pid  = '$cCID' 
                                    AND type = 'Comment' 
                                    AND uid  = '$UID'")) -> fetch_array()[0];

            # If the current user has liked the post,
            # represent that with the input and class.
            if ($uLiked == 1) {

                $HTML .=
                    '<div class="post-footer">
                        <form method="POST" action="php/form-handlers.php" class="comment-like-form" id="comment-like-form" name="comment-like-form">
                             <input type="hidden" id="post-like-status" name="post-like-status" value="True">
                                
                             <button type="button" class="post-like-active comment-hearts" onclick="likePost(this, `Comment`)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-heart" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="white"/>
                                    <path d="M19.5 13.572l-7.5 7.428l-7.5 -7.428m0 0a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" />
                                </svg>
                                <p>' . $cLikes . '</p>
                             </button>
                             <input type="submit" id="comment-like-button" name="comment-like-button" style="display: none">
                        </form>';
            }

            # If they have not liked the current post,
            # give them the default heart.
            else {

                $HTML .=
                    '<div class="post-footer">
                        <form method="POST" action="php/form-handlers.php" class="comment-like-form" id="comment-like-form" name="comment-like-form">
                             <input type="hidden" id="post-like-status" name="post-like-status" value="False">
                                
                             <button type="button" class="post-like-inactive comment-hearts" onclick="likePost(this, `Comment`)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-heart" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="white"/>
                                    <path d="M19.5 13.572l-7.5 7.428l-7.5 -7.428m0 0a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" />
                                </svg>
                                <p>' . $cLikes . '</p>
                             </button>
                             <input type="submit" id="comment-like-button" name="comment-like-button" style="display: none">
                        </form>';
            }

            # This block checks to see if the current user posted
            # the current post. If so, then give the post the
            # delete button.
            if ($cUID == $UID) {

                $HTML .= '<button class="comment-delete" id="comment-delete" onclick="deletePost(this, `Comment`)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-dots" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="5" cy="12" r="1" />
                                <circle cx="12" cy="12" r="1" />
                                <circle cx="19" cy="12" r="1" />
                            </svg>        
                          </button>
                          </div>
                          </div>';
            }

            # If the current user is not the poster of the
            # current post, don't give them the button.
            else {

                $HTML .= '<div class="post-footer-hidden"></div>
                          </div>
                          </div>';
            }
        }

        # Close the comments section.
        $HTML .= '</div>';
    }

    return $HTML;
}



# --------------- #
#   GROUPS PAGE   #
# --------------- #


# groupSelection : List of Groups -> ECHO
# Function takes the list of the user's groups
# and builds a dropdown containing the groups.
function groupSelection($GROUPS) {

    $select = '<label for="gSelect">Group</label>
               <select id="gSelect" name="gSelect">';

    foreach ($GROUPS as $g) {

        $select .= '<option value="' . $g[0] . '">' . $g[1] . '</option>';
    }

    $select .= '</select>';

    echo $select;
}


# userGroups : Connection, Username -> ECHO
# Function takes the connection and username
# and displays a list of the user's groups.
function userGroups($CONN, $USERNAME) {

    # Get all groups connected to the user via the usersgroups
    # table.
    $query = "SELECT g.gid, g.title, g.description FROM `groups` AS g
                            JOIN usersgroups ug ON g.gid = ug.gid
                            JOIN users u ON ug.uid = u.uid
                            WHERE u.uName = '$USERNAME'";

    # Run the query.
    $userGroupsList = $CONN -> query($query);

    # Echo the results.
    foreach ($userGroupsList as $g) {

        echo '<form class="user-group" method="POST" action="php/manage-group.php">
                <input type="hidden" id="mGID" name="mGID" value=' . $g['gid'] .'>
                <h4>' . $g['title'] . '</h4>
                <p>'  . $g['description'] . '</p>
              </form>';
    }
}


# viewGroups : Connection, UID, Value -> ECHO
# Function takes the connection and username
# and displays a list of the platform's groups
# that the user is not currently in.
# The value argument allows for search
# functionality.
function viewGroups($CONN, $UID, $VALUE = null) {

    # First, we need to get the user's groups.
    $userGroups = ($CONN -> query("SELECT uid, gid 
                                   FROM usersgroups 
                                   WHERE uid = '$UID'"));

    # Initialize an array to store the list
    # of the user's groups.
    $ugList = array();

    # Add each group ID to the list.
    foreach ($userGroups as $ug) {
        $ugList[] = $ug['gid'];
    }

    if ($VALUE != null) {

        $viewGroups = $CONN -> query("SELECT * FROM `groups`
                                      WHERE gid > 6 
                                        AND title LIKE '{$VALUE}%'
                                        AND status != 0
                                      LIMIT 10");
    }

    else {

        # Grab a list of the groups on the platform.
        $viewGroups = $CONN -> query("SELECT * FROM `groups` 
                                      WHERE gid > 6 
                                        AND status != 0 
                                      LIMIT 10");
    }

    # Iterate through that list.
    foreach ($viewGroups as $vg) {

        # Set the ID of the current group.
        $gid = $vg['gid'];

        # Check to see if the current group is in the user's list.
        if (!in_array($gid, $ugList)) {

            # Let's grab the count of members in the group.
            $c = ($CONN -> query("SELECT COUNT(*) FROM usersgroups WHERE gid = '$gid'")) -> fetch_array()[0];

            # Echo the element.
            echo '<div class="view-group" onclick="viewGroup(this)">
                    <h4>' . $vg['title'] . '</h4>
                    <p>'  . $vg['description'] . '</p>
                    <input type="hidden" value=' . $vg['gid'] .'>
                    <input type="hidden" value=' . $vg['status'] .'>
                    <input type="hidden" value=' . $c . '/' . $vg['limit'] .'>
                  </div>';
        }
    }
}


# groupForm : Connection, UID, GID -> Echo
# Function takes the connection, the user's ID,
# and the group's ID and displays a pre-populated
# form that allows the user to update the group
# information relative to their permissions.
function groupForm($CONN, $UID, $GID) {

    # Grab the group information and the user's permissions.
    $gInfo = ($CONN -> query("SELECT title, description 
                                  FROM `groups` 
                                  WHERE gid='$GID'")) -> fetch_assoc();

    $uPerm = ($CONN -> query("SELECT permissions 
                                  FROM usersgroups 
                                  WHERE uid = '$UID' AND gid = '$GID'")) -> fetch_assoc();

    # Create the name and description variables so that we can populate the form.
    $gName = $gInfo['title'];
    $gDesc = $gInfo['description'];

    # Set the user permissions.
    $uPerm = intval($uPerm['permissions']);

    if ($uPerm == 2) {

        # Echo the edit group form with the relevant information.
        # In the case that the user is more than a member, they can
        # edit this form.
        echo '<form method="POST" action="form-handlers.php" class="manage-group-info">
                
                <h2>Edit Group</h2>
    
                <label for="group-name">Name</label>
                <input type="text" class="manage-group-input" 
                       name="group-name" id="group-name" value="' . $gName . '" required>
    
                <label for="group-desc">Description</label>
                <input type="text" class="manage-group-input" 
                       name="group-desc"  id="group-desc" value="' . $gDesc . '" required>
                
                <input type="number" name="group-id" id="group-id" value="' . $GID . '" hidden>
    
                <div class="manage-group-buttons">
                    <input type="button" id="delete-button" name="delete-button" value="Delete Group" onclick="openForm(`D`)">
                    <input type="submit" id="submit-button" name="submit-button" value="Update Group">
                </div>
                
              </form>';
    }

    else if ($uPerm == 1) {

        # Echo the edit group form with the relevant information.
        # In the case that the user is more than a member, they can
        # edit this form.
        echo '<form method="POST" action="form-handlers.php" class="manage-group-info">
                
                <h2>Edit Group</h2>
    
                <label for="group-name">Name</label>
                <input type="text" class="manage-group-input" 
                       name="group-name" id="group-name" value="' . $gName . '" required>
    
                <label for="group-desc">Description</label>
                <input type="text" class="manage-group-input" 
                       name="group-desc"  id="group-desc" value="' . $gDesc . '" required>
                
                <input type="number" name="group-id" id="group-id" value="' . $GID . '" hidden>
    
                <div class="manage-group-buttons">
                    <input type="submit" id="submit-button" name="submit-button" value="Update Group">
                    <input type="button" id="leave-button" name="leave-button" value="Leave Group" onclick="openForm(`L`)">
                </div>
                
              </form>';
    }

    else {

        # In the case that the user is a moderator or admin,
        # they get a form that they can edit.
        echo '<form method="POST" action="form-handlers.php" class="manage-group-info">
                
                <h2>Group</h2>
    
                <label for="group-name">Name</label>
                <input type="text" class="manage-group-input" 
                       name="group-name" id="group-name" value="' . $gName . '" readonly>
    
                <label for="group-desc">Description</label>
                <input type="text" class="manage-group-input" 
                       name="group-esc"  id="group-desc" value="' . $gDesc . '" readonly>
                       
                <div class="manage-group-buttons">
                    <input type="button" id="leave-button" name="leave-button" value="Leave Group" onclick="openForm(`L`)">
                </div>

              </form>';

    }
}


# memberForm : Connection, UID, GID -> Echo
# Function takes the connection, the user's ID,
# and the group's ID and displays a pre-populated
# form that allows the user to update the group
# members relative to their permissions.
function memberForm($CONN, $UID, $GID) {

    # Grab all the UIDs and permissions from the group.
    $members = $CONN -> query("SELECT uid, permissions 
                               FROM usersgroups 
                               WHERE gid = '$GID' 
                               ORDER BY permissions DESC ");

    # Grab the current user's permissions as well.
    $userPerms = ($CONN -> query("SELECT permissions 
                                  FROM usersgroups 
                                  WHERE uid = '$UID' AND gid = '$GID'")) -> fetch_assoc();

    # Set the user permissions.
    $uPerm = intval($userPerms['permissions']);

    if ($uPerm > 0) {

        # Begin the element.
        $form = '<form method="POST" action="form-handlers.php" class="manage-group-members">
                 <h2>Edit Members</h2>

                 <input type="text" class="manage-group-members-search" id="manage-group-members-search"
                  placeholder="Search for Members (Case Sensitive)" autocomplete="off">
                 <div class="manage-group-members-list" id="manage-group-members-list">';
    }

    else {

        # Begin the element.
        $form = '<form method="POST" action="form-handlers.php" class="manage-group-members">
                 <h2>Members</h2>

                 <input type="text" class="manage-group-members-search" id="manage-group-members-search"
                  placeholder="Search for Members (Case Sensitive)" autocomplete="off">
                 <div class="manage-group-members-list" id="manage-group-members-list">';
    }

    # For each member, create their permission
    # control.
    foreach ($members as $m) {

        # Get the userID and their permissions.
        # Use the index to create input names and IDs.
        $uid = $m['uid'];
        $prm = $m['permissions'];
        $usr = 'user-' . $uid;

        # Query their first and last name and create a name
        # for display.
        $user = ($CONN -> query("SELECT fName, lName FROM users WHERE uid = '$uid'")) -> fetch_array();
        $name = $user['fName'] . ' ' . $user['lName'];

        if ($uPerm > 0) {

            # Begin the construction of the form containing the group
            # member's permissions.
            $row = '<div class="manage-group-member">
                    <div class="check-user">
                        <input type="checkbox" name="' . $usr . '" id="' . $usr . '">
                        <label for="' . $usr . '">' . $name . '</label>
                    </div>';

            # If the user is an owner of the group, give them the ability
            # to remove members and modify permissions.
            if ($uPerm == 2) {

                # If the user is an owner of the group, create a select box with their
                # user number permission and set the default to owner.
                if ($prm == 2) {
                    $row .= '<select name="' . $usr . '-permission" id="' . $usr . '-permission">
                                <option value="2" selected>Owner</option>
                                <option value="1">Moderator</option>
                                <option value="0">Member</option>
                             </select>
                             </div>';
                }

                # If the user is an owner of the group, create a select box with their
                # user number permission and set the default to moderator.
                if ($prm == 1) {
                    $row .= '<select name="' . $usr . '-permission" id="' . $usr . '-permission">
                                <option value="2">Owner</option>
                                <option value="1" selected>Moderator</option>
                                <option value="0">Member</option>
                             </select>
                             </div>';
                }

                # If the user is an owner of the group, create a select box with their
                # user number permission and set the default to member.
                if ($prm == 0) {
                    $row .= '<select name="' . $usr . '-permission" id="' . $usr . '-permission">
                                <option value="2">Owner</option>
                                <option value="1">Moderator</option>
                                <option value="0" selected>Member</option>
                             </select>
                             </div>';
                }
            }

            # If the user is a moderator, give them the ability to
            # remove users but not change permissions.
            else {

                # If the user is an owner of the group, create a select box with their
                # user number permission and set the default to owner.
                if ($prm == 2) {
                    $row .= '<select name="' . $usr . '-permission" id="' . $usr . '-permission" disabled>
                                <option value="2" selected>Owner</option>
                                <option value="1">Moderator</option>
                                <option value="0">Member</option>
                             </select>
                             </div>';
                }

                # If the user is an owner of the group, create a select box with their
                # user number permission and set the default to moderator.
                if ($prm == 1) {
                    $row .= '<select name="' . $usr . '-permission" id="' . $usr . '-permission" disabled>
                                <option value="2">Owner</option>
                                <option value="1" selected>Moderator</option>
                                <option value="0">Member</option>
                             </select>
                             </div>';
                }

                # If the user is an owner of the group, create a select box with their
                # user number permission and set the default to member.
                if ($prm == 0) {
                    $row .= '<select name="' . $usr . '-permission" id="' . $usr . '-permission" disabled>
                                <option value="2">Owner</option>
                                <option value="1">Moderator</option>
                                <option value="0" selected>Member</option>
                             </select>
                             </div>';
                }
            }

            # Add the permission control to the form and iterate the counter.
            $form .= $row;
        }

        # If the user is a member, give them the same inputs but do not
        # allow them to change anything.
        else {

            # Begin the construction of the form containing the group
            # member's permissions.
            $row = '<div class="manage-group-member">
                        <div class="check-user">
                            <input type="checkbox" name="' . $usr . '" id="' . $usr . '" disabled>
                            <label for="' . $usr . '">' . $name . '</label>
                        </div>';

            # If the user is
            if ($prm == 2) {
                $row .= '<select name="' . $usr . '-permission" id="' . $usr . '-permission" disabled>
                            <option value="2" selected>Owner</option>
                            <option value="1">Moderator</option>
                            <option value="0">Member</option>
                         </select>
                         </div>';
            }

            # If the user is an owner of the group, create a select box with their
            # user number permission and set the default to moderator.
            if ($prm == 1) {
                $row .= '<select name="' . $usr . '-permission" id="' . $usr . '-permission" disabled>
                            <option value="2">Owner</option>
                            <option value="1" selected>Moderator</option>
                            <option value="0">Member</option>
                         </select>
                         </div>';
            }

            # If the user is an owner of the group, create a select box with their
            # user number permission and set the default to member.
            if ($prm == 0) {
                $row .= '<select name="' . $usr . '-permission" id="' . $usr . '-permission" disabled>
                            <option value="2">Owner</option>
                            <option value="1">Moderator</option>
                            <option value="0" selected>Member</option>
                         </select>
                         </div>';
            }

            # Add the permission control to the form and iterate the counter.
            $form .= $row;
        }
    }

    # If the user is an owner, give them both buttons.
    if ($uPerm == 2) {

        # Close off the form with the buttons.
        $form .= '</div>
                  <div class="manage-group-buttons">
                     <input type="submit" id="remove-button"  name="remove-button"  value="Remove Members">
                     <input type="submit" id="promote-button" name="promote-button" value="Change Permissions">
                  </div>
                  </form>';
    }

    # If the user is a moderator, only give them the remove button.
    else if ($uPerm == 1) {

        # Close off the form with the buttons.
        $form .= '</div>
                  <div class="manage-group-buttons">
                     <input type="submit" id="remove-button"  name="remove-button"  value="Remove Members">
                  </div>
                  </form>';
    }

    else {

        $form .= '</div>
                  </form>';
    }

    # Display the form to the user.
    echo $form;
}


# settingsForm : Connection, UID, GID -> Echo
# Function takes the connection, the user's ID,
# and the group's ID and displays a pre-populated
# form that allows the user to update the group
# settings relative to their permissions.
function settingsForm($CONN, $UID, $GID) {

    $gSettings = ($CONN -> query("SELECT `limit`, `status` FROM `groups` WHERE gid = '$GID'")) -> fetch_assoc();

    $gLimit = $gSettings['limit'];
    $gStatus = $gSettings['status'];

    # Grab the current user's permissions as well.
    $userPerms = ($CONN -> query("SELECT permissions 
                                  FROM usersgroups 
                                  WHERE uid = '$UID' AND gid = '$GID'")) -> fetch_assoc();

    # Set the user permissions.
    $uPerm = intval($userPerms['permissions']);

    $form = '<div id="manage-group-settings-form" style="display: none">
             <form method="POST" action="form-handlers.php" class="manage-group-settings-form">
             <div id="close-form" onclick="closeForm(`S`)">X</div>
             <h2>Settings</h2>';

    if ($uPerm == 2) {

        $form .= '<label for="group-limit">Member Limit</label>
                  <input type="number" id="group-limit" name="group-limit" value="' . $gLimit . '">
                  <p>This setting allows you to adjust the maximum number of members in the group.<br>
                   (1 - 10,000)</p>';

        if ($gStatus == 1) {

            $form .= '<label for="group-status">Group Status</label>
                        <select id="group-status" name="group-status">
                            <option value="public" selected>Public</option>
                            <option value="private">Private</option>
                        </select>
                        <p>This setting allows you to adjust whether outside users are able to join the group.</p>';
        }

        else {

            $form .= '<label for="group-status">Group Status</label>
                        <select id="group-status" name="group-status">
                            <option value="public">Public</option>
                            <option value="private" selected>Private</option>
                        </select>
                        <p>This setting allows you to adjust whether outside users are able to join the group.</p>';
        }

        $form .= '<div class="manage-group-settings-buttons">
                    <input type="button" id="cancel-button"  name="cancel-button"  value="Cancel" onclick="closeForm(`S`)">
                    <input type="submit" id="update-button"  name="update-button"  value="Update Settings">
                  </div>
                  
                  </form>
                  </div>';
    }

    else {

        $form .= '<label for="group-limit">Member Limit</label>
                  <input type="number" id="group-limit" name="group-limit" value="' . $gLimit . '" readonly>
                  <p>This setting allows you to adjust the maximum number of members in the group.<br>
                   (1 - 10,000)</p>';

        if ($gStatus == 1) {

            $form .= '<label for="group-status">Group Status</label>
                        <select id="group-status" name="group-status" disabled>
                            <option value="public" selected>Public</option>
                            <option value="private">Private</option>
                        </select>
                        <p>This setting allows you to adjust whether outside users are able to join the group.</p>';
        }

        else {

            $form .= '<label for="group-status">Group Status</label>
                        <select id="group-status" name="group-status" disabled>
                            <option value="public">Public</option>
                            <option value="private" selected>Private</option>
                        </select>
                        <p>This setting allows you to adjust whether outside users are able to join the group.</p>';
        }

        $form .= '</form>
                  </div>';
    }

    echo $form;
}



# ------------------------------- #
#   HEALTH AND MAINTENANCE PAGE   #
# ------------------------------- #


# getTickets : Connection, Username, Option -> List of Tickets
# Function takes a connection, username, and an option
# and returns an array of the user's maintenance and health tickets.
function getTickets($CONN, $USERNAME, $OPTION) : array {

    # Get the user's ID and initialize an empty array.
    $UID = getUserID($CONN, $USERNAME);
    $ticketArray = array();

    # If the maintenance option was passed.
    if ($OPTION == "MAINTENANCE") {

        # Get the user's maintenance tickets.
        $query = "SELECT * FROM maintenance WHERE uid = ?";
        $query = $CONN -> prepare($query);
        $query -> bind_param('i', $UID);
        $query -> execute();
        $tickets = $query -> get_result();

        # Iterate through the maintenance tickets.
        foreach ($tickets as $t) {

            $id = $t['mid'];
            $is = $t['issue'];
            $de = $t['description'];
            $do = $t['dorm'];
            $fl = $t['floor'];
            $ro = $t['room'];
            $rs = $t['resolved'];
            $ts = $t['timestamp'];

            # Add the information to the ticket array.
            $ticketArray[] = [$id, $is, $de, $do, $fl, $ro, $rs, $ts];
        }
    }

    # If the health option was passed.
    if ($OPTION == "HEALTH") {

        # Get the user's health tickets.
        $query = "SELECT * FROM health WHERE uid = ?";
        $query = $CONN -> prepare($query);
        $query -> bind_param('i', $UID);
        $query -> execute();
        $tickets = $query -> get_result();

        # Iterate through the tickets.
        foreach ($tickets as $t) {

            $id = $t['hid'];
            $is = $t['issue'];
            $de = $t['description'];
            $rs = $t['resolved'];
            $ts = $t['timestamp'];

            # Add the information to the ticket array.
            $ticketArray[] = [$id, $is, $de, $rs, $ts];
        }
    }

    # Sort the tickets based upon the timestamp.
    usort($ticketArray, function($t1, $t2) {

        # Compare the timestamps of the two current tickets.
        $t1 = strtotime(end($t1));
        $t2 = strtotime(end($t2));

        # Return the time difference for comparison.
        return $t2 - $t1;
    });

    # After sorting based upon the time, also sort
    # the tickets based upon their resolved value.
    usort($ticketArray, function($t1, $t2) {

        # Compare the resolved values.
        $t1 = $t1[count($t1) - 2];
        $t2 = $t2[count($t2) - 2];

        # Return the difference of values.
        return $t1 - $t2;
    });

    # If the array is empty, add a string
    # that will later be displayed to the page.
    if (empty($ticketArray)) {
        $ticketArray[] = "No tickets found!";
    }

    # Finally, return the ticket array.
    return $ticketArray;
}


# displayTickets : List of Tickets, Option -> ECHO
# Function takes a list of the user's tickets and
# displays them on the page.
function displayTickets($TICKETS, $OPTION) {

    if ($TICKETS[0] === "No tickets found!") {

        echo "<p class='no-tickets'>No active tickets...<br>
              Click the button below to create a new one!</p>";
    }

    else {

        # If the maintenance option was passed.
        if ($OPTION == "MAINTENANCE") {

            # Iterate through the tickets.
            foreach ($TICKETS as $t) {

                # If the ticket has not been resolved,
                # echo the ticket with a form attached.
                if ($t[6] == 0) {

                    $HTML = '
                    <div class="main-ticket" onclick="resolveTicket(this)">
                        <input type="hidden" value="' . $t[0] . '">
                        
                        <h3>' . $t[1] . '</h3>
                        <p>' . $t[2] . '</p>
    
                        <div class="main-ticket-info">
                            <h4>Information</h4>
                            <p>' . $t[3] . '</p>
                            <p>Floor ' . $t[4] . '</p>
                            <p>Room  ' . $t[5] . '</p>
                            
                            <p class="ticket-time">' . date("F j, Y @ g:i A", strtotime($t[7])) . '</p>
                        </div>
                    </div>';
                } # If the ticket has been resolved.
                else {

                    $HTML = '
                    <div class="main-ticket resolved-ticket">
                        <p class="resolved-text">Resolved</p>
                        
                        <h3>' . $t[1] . '</h3>
                        <p>' . $t[2] . '</p>
    
                        <div class="main-ticket-info">
                            <h4>Information</h4>
                            <p>' . $t[3] . '</p>
                            <p>Floor ' . $t[4] . '</p>
                            <p>Room  ' . $t[5] . '</p>
                            
                            <p class="ticket-time">' . date("F j, Y @ g:i A", strtotime($t[7])) . '</p>
                        </div>
                    </div>';
                }

                # Echo the ticket.
                echo $HTML;
            }
        }

        # If the health option was passed.
        if ($OPTION == "HEALTH") {

            # Iterate through the tickets.
            foreach ($TICKETS as $t) {

                # If the ticket has not been resolved.
                if ($t[3] == 0) {

                    $HTML = '
                    <div class="health-ticket" onclick="resolveTicket(this)">
                        <input type="hidden" value="' . $t[0] . '">
                        
                        <h3>' . $t[1] . '</h3>
                        <p>' . $t[2] . '</p>
                         
                        <p class="ticket-time">' . date("F j, Y @ g:i A", strtotime($t[4])) . '</p>
                    </div>';
                } # If the ticket has been resolved.
                else {

                    $HTML = '
                    <div class="health-ticket resolved-ticket">
                        <p class="resolved-text">Resolved</p>
                        
                        <h3>' . $t[1] . '</h3>
                        <p>' . $t[2] . '</p>
                         
                        <p class="ticket-time">' . date("F j, Y @ g:i A", strtotime($t[4])) . '</p>
                    </div>';
                }

                # Finally, echo the ticket.
                echo $HTML;
            }
        }
    }
}


# ----------------- #
#   SETTINGS PAGE   #
# ----------------- #


function themeSelection($CONN, $USERNAME) {

    # Get the user's current theme.
    $theme = ($CONN -> query("SELECT theme FROM users WHERE uname = '$USERNAME'")) -> fetch_array()[0];

    $themesArray = array("NextDorm", "Ocean Mint", "Waterfall");

    $HTML = '<select id="theme" name="theme">';

    for ($i = 0; $i < count($themesArray); $i++) {

        if ($i == $theme) {

            $HTML .= '<option value="' . $i . '" selected>' . $themesArray[$i] . '</option>';
        }

        else {

            $HTML .= '<option value="' . $i . '">' . $themesArray[$i] . '</option>';
        }
    }

    $HTML .= '</select>';

    echo $HTML;
}
