<?php

# Start the session and regenerate the ID.
session_start();

# If the session's username has not been set, redirect to the login page.
if (!isset($_SESSION['user']))
{
    header("Location: ../login.html");
}

else {
    $USERNAME = $_SESSION['user'];
}

# Connection information.
$HOST = "db.luddy.indiana.edu";
$USER = "i494f21_team02";
$PASS = "my+sql=i494f21_team02";
$DATA = "i494f21_team02";

$CONN = new mysqli($HOST, $USER, $PASS,$DATA);


if (isset($_POST['new-profile-button'])) {

    # Grabbing the data from the form.
    $uName =  $_POST['username'];
    $email =  $_POST['email'];
    $fName =  $_POST['first-name'];
    $lName =  $_POST['last-name'];
    $hood =   $_POST['hood'];
    $dorm =   $_POST['dorm'];
    $school = $_POST['school'];
    $gender = $_POST['gender'];
    $bio =    $_POST['bio'];

    # Insert the user into the database.
    $insertUser = "INSERT INTO users (fName, lName, uName, email, neighborhood, dorm, school, gender, description)
                   VALUES ('$fName','$lName','$uName','$email','$hood', '$dorm', '$school', '$gender', '$bio')";
    $CONN -> query($insertUser);

    # Get the user ID.
    $uidQuery = "SELECT uid FROM users WHERE uName = '$uName'";
    $UID = intval(($CONN -> query($uidQuery)) -> fetch_assoc()['uid']);

    # Get the user's neighborhood group.
    $userHood = ucfirst($hood);
    $gidQuery = "SELECT gid FROM `groups` WHERE title = '$userHood'";
    $GID = intval(($CONN -> query($gidQuery)) -> fetch_assoc()['gid']);

    # Create the user-group relationship.
    $ugQuery = "INSERT INTO usersgroups (uid, gid, permissions) VALUES ('$UID', '$GID', 0)";
    $CONN -> query($ugQuery);

    # Also, add the user to the NextDorm group.
    $CONN -> query("INSERT INTO usersgroups (uid, gid, permissions) 
                          VALUES ('$UID', 7, 0)");

    # Set the username in the session.
    $_SESSION['user'] = $uName;

    # Once these steps have been completed, redirect the user to validate.php.
    header("Location: validate.php");
}

?>

<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">

    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- main.css and normalize.css work to provide cross-browser consistency of styling and structure. -->
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/normalize.css">

    <!-- navbar.css and styles.css are responsible for the cohesive style across our platform. -->
    <link rel="stylesheet" href="../css/default.css">

    <title>Create Profile</title>

</head>
<body>

<div class="container">

    <h1>Welcome to NextDorm</h1>

    <div class="content">

        <div class="sub-container">

            <form class="new-profile-form" method="POST">

                <h2>Create Your Profile</h2>

                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo $USERNAME ?>" readonly>

                <label for="email">Email</label>
                <input type="text" id="email" name="email" value="<?php echo $USERNAME . '@iu.edu' ?>" readonly>

                <label for="first-name">First Name</label>
                <input type="text" id="first-name" name="first-name" placeholder="First Name" required>

                <label for="last-name">Last Name</label>
                <input type="text" id="last-name" name="last-name" placeholder="Last Name" required>

                <label for="hood">Neighborhood</label>
                <select id="hood" name="hood" required>
                    <option value="central">Central</option>
                    <option value="northeast">Northeast</option>
                    <option value="northwest">Northwest</option>
                    <option value="southeast">Southeast</option>
                    <option value="off_campus">Off Campus</option>
                </select>

                <label for="dorm">Dorm</label>
                <select id="dorm" name="dorm" required>
                    <option value="Ashton">Ashton</option>
                    <option value="Collins">Collins</option>
                    <option value="Eigenmann">Eigenmann</option>
                    <option value="Hillcrest">Hillcrest</option>
                    <option value="Teter">Teter</option>
                    <option value="Union Street">Union Street</option>
                    <option value="Wright">Wright</option>
                </select>

                <label for="school">School</label>
                <select id="school" name="school" required>
                    <option value="undecided">Undecided</option>
                    <option value="art_architecture_design">IU Eskenazi School of Art, Architecture, and Design</option>
                    <option value="kelley">IU Kelley School of Business</option>
                    <option value="education">School of Education</option>
                    <option value="global_international_studies">IU Hamilton Lugar School of Global and International Studies</option>
                    <option value="luddy">IU Luddy school of Informatics, Computing, and Engineering</option>
                    <option value="law">IU Maurer School of Law</option>
                    <option value="media">The Media School</option>
                    <option value="medicine">School of Medicine</option>
                    <option value="music">IU Jacobs School of Music</option>
                    <option value="nursing">School of Nursing</option>
                    <option value="optometry">School of Optometry</option>
                    <option value="spea">IU O'Neill School of Public and Environmental Affairs</option>
                    <option value="public_health">School of Public Health-Bloomington</option>
                    <option value="social_work">School of Social Work</option>
                </select>

                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="other">Other</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="non_binary">Non-Binary</option>
                    <option value="not_identified">Choose Not To Identify</option>
                </select>

                <label for="bio">Biography</label>
                <textarea name="bio" id="bio" maxlength="256" rows="6" cols="20"></textarea>

                <input type="submit" id="new-profile-button" name="new-profile-button" value="Submit">

            </form>

        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>

    //dorm locations return based on neighborhood location
    $(document).ready(function() {
        $("#hood").change(function(){

            let el = $(this);

            if(el.val() ==="central"){

                $("#dorm option").remove();
                $("#dorm").append("<option value='Ashton'>Ashton</option>" +
                    "<option value='Collins'>Collins</option>" +
                    "<option value='Eigenmann'>Eigenmann</option>" +
                    "<option value='Hillcrest'>Hillcrest</option>" +
                    "<option value='Teter'>Teter</option>" +
                    "<option value='Union Street'>Union Street</option>" +
                    "<option value='Wright'>Wright</option>");
            }

            else if (el.val() === "northeast"){

                $("#dorm option").remove();
                $("#dorm").append("<option value='Campus View'>Campus View</option>" +
                    "<option value='Redbud Hill'>Redbud Hill</option>" +
                    "<option value='Tulip Tree'>Tulip Tree</option>");
            }

            else if (el.val() === "northwest"){

                $("#dorm option").remove();
                $("#dorm").append("<option value='Briscoe'>Briscoe</option>" +
                    "<option value='Foster'>Foster</option>" +
                    "<option value='McNutt'>McNutt</option>" +
                    "<option value='Walnut Grove'>Walnut Grove</option>");
            }

            else if (el.val() === "southeast"){

                $("#dorm option").remove();
                $("#dorm").append("<option value='3rd and Union'>3rd and Union </option>" +
                    "<option value='Forest'>Forest</option>" +
                    "<option value='Mason'>Mason</option>" +
                    "<option value='Read'>Read</option>" +
                    "<option value='Spruce'>Spruce</option>" +
                    "<option value='Wells Quad'>Wells Quad</option>" +
                    "<option value='University East'>University East</option>" +
                    "<option value='Willkie'>Willkie</option>");
            }

            else if (el.val() === "off_campus"){

                $("#dorm option").remove();
                $("#dorm").append("<option value='The Avenue'>The Avenue</option>");
            }
        });
    });
</script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</body>
</html>

