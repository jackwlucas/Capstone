<?php

# Functions.php contains site-wide functionality.
include "php/functions.php";


# First, let's grab the relevant information about the user.
$uInfo = ($CONN -> query("SELECT fName , lName, dorm, school FROM users WHERE uName = '$USERNAME'")) -> fetch_assoc();

$dorm   = $uInfo['dorm'];
$fName  = $uInfo['fName'];
$lName  = $uInfo['lName'];
$school = $uInfo['school'];

# Next, let's grab the relevant information about the user's dorm.
$dInfo = ($CONN -> query("SELECT dorm_name, address, lat, lon FROM building WHERE dorm_name = '$dorm'")) -> fetch_assoc();

$dorm_name = $dInfo['dorm_name'];
$address   = $dInfo['address'];
$lat       = $dInfo['lat'];
$lon       = $dInfo['lon'];

function importantLocations($SCHOOL, $DORM, $ADDRESS) {

    echo '<h4>' . $DORM . '</h4><p>' . $ADDRESS . '</p><br>';

    $placeMap = array(
        'Luddy' => array(
            'Luddy Hall' => '700 N Woodlawn Ave, Bloomington, IN 47408',
            'Myles Brand Hall' => '901 E 10th St Informatics West, Bloomington, IN 47408',
            'Luddy Center for Artificial Intelligence' => '1015 E 11th St, Bloomington, IN 47408'
        ),
        'Art_architecture_design' => array(
            'Fine Arts Building'=> '1201 E Seventh Street, Bloomington, IN 47405',
            'Kirkwood Hall' =>'130 S Woodlawn Avenue, Bloomington, IN 47405',
            'Mies van der Rohe Building' => '321 N Eagleson Avenue, Bloomington, IN 47405',
            'The Republic Building' => '333 Second Street, Columbus, IN 47201',
            'Studio Arts Annex' => '802 E Thirteenth Street, Bloomington, IN 47408'
        ),
        'Kelley' => array(
            'Hodge Hall' => '1309 E 10th St, Bloomington, IN 47405',
            'Kelley Executive Partners' => '1275 E 10th St, Bloomington, IN 47405'
        ),
        'Education' => array(
                'School of Education' =>'201 N Rose Ave, Bloomington, IN 47405'
        ),
        'Global_international_studies'=> array(
            'School of Global and International Studies' => '355 N Eagleson Ave, Bloomington, IN 47405'
        ),
        'Law' => array(
            'Maurer School of Law Library' => '211 S Indiana Ave, Bloomington, IN 47405'
        ),
        'Media' =>array(
                'IU Media School' => '601 E Kirkwood Ave, Bloomington, IN 47405',
                'Radio-Television Building'=>'1229 E 7th St, Bloomington, IN 47405'
        ),
       'Medicine' => array(
           'Biology Building' => '1001 E. Third St',
           'IU Health Bloomington Hospital' => '2651 E Discovery Pkwy, Bloomington, IN 47408',
           'Ruth Lilly Medical Library' => '975 W. Walnut Street, Indianapolis, IN 46202'
        ),
        'Music' => array(
            'Bess Meshulam Simon Music Library and Recital Center' => 'E 3rd St Bess Meshulam Simon Music Library and Recital CenterBloomington, IN 47405',
            'East Studio Building' => '205 S Jordan Ave, Bloomington, IN 47405',
            'Simon Music Center' => ' 1201 E 3rd St, Bloomington, IN 47405',
            'Music Annex' => '200 S Jordan Ave, Bloomington, IN 47405',
            'Music Practice Building' => 'Music Practice BldgBloomington, IN 47401',
            'Musical Arts Center' => 'Musical Arts Center (MAC)Bloomington, IN 47406'
        ),
        'Nursing' => array(
            'School of Nursing' => '2631 E Discovery Pkwy, Bloomington, IN 47408',
            'IU Health Bloomington Hospital' => '2651 E Discovery Pkwy, Bloomington, IN 47408'
        ),
        'Optometry' => array(
               'Optometry Building' => '800 Atwater Ave, Bloomington, IN 47405'
        ),
        'Spea' => array(
                "O'Neil School of Public and Environmental Affairs" => '1315 E 10th St, Bloomington, IN 47405'
        ),
        'Public_health' => array(
                'School of Public Health' => '1025 E 7th St, Bloomington, IN 47405'
        ),
        'Social_work' => array(
                'IU School of Social Work' => '1105 East, Atwater Ave, Bloomington, IN 47401'
        )
    );

    foreach ($placeMap as $school => $locations) {

        if ($school == $SCHOOL) {

            foreach ($locations as $location => $address) {

                echo '<h4>' . $location . '</h4><p style="color: black">' . $address . '</p><br>';
            }
        }
    }
}



?>

<html lang="en">
    <head>

        <meta charset="UTF-8">
        <title>Neighborhoods - Next Dorm</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">
        <!-- main.css and normalize.css work to provide cross-browser consistency of styling and structure. -->
        <link rel="stylesheet" href="./css/main.css">
        <link rel="stylesheet" href="./css/normalize.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

        <!-- navbar.css and styles.css are responsible for the cohesive style across our platform. -->
        <!--<link rel="stylesheet" href="./css/navbar.css">-->
        <?php getTheme($CONN, $UID) ?>
        <link rel="stylesheet" href="./css/navbar.css">

        <style>
            #map {
            /* The height is 400 pixels */
                width: 100%;
                border-radius: 2em;
                height: 600px;
            }
            /* The width is the width of the web page */
            }

            #origin-input,
            #destination-input {
                background-color: #fff;
                font-size: 15px;
                font-weight: 300;
                margin-left: 12px;
                padding: 0 11px 0 13px;
                text-overflow: ellipsis;
                width: 200px;
            }

            #origin-input:focus,
            #destination-input:focus {
                border-color: rgb(220,20,60);
            }

            #mode-selector {
                color: #fff;
                background-color: rgb(34,35,39);
                margin-left: 12px;
                padding: 5px 11px 0 11px;
            }

            #mode-selector label {
                font-size: 13px;
                font-weight: 300;
            }

            input {
                width: 45%;
                margin: 1.5em 2em 0 2em;
            }
        </style>
        <script>
            let map;
            function initMap() {
                const latlng = new google.maps.LatLng(<?php echo $lat?>, <?php echo $lon?>);
                let mapOptions = {
                    zoom: 16,
                    center: latlng,
                    mapId: '6317b245768b029f',
                    mapTypeControl: false,
                    fullscreenControl: false,
                    streetViewControl: false
                }
                map = new google.maps.Map(document.getElementById('map'), mapOptions);
                //Custom Dorm Marker
                const user_dorm = new google.maps.Marker({
                    position: {lat: <?php echo $lat?>, lng: <?php echo $lon?>},
                    map,
                    animation: google.maps.Animation.DROP,
                    title: '<?php echo $dorm_name?>',
                    icon: {
                        url: "./img/home_map_icon.png",
                        scaledSize: new google.maps.Size(20, 20)
                    }
                });
                //Explaining what the dorm marker is
                const infowindow = new google.maps.InfoWindow({
                    content:
                        "<h4>Your Dorm Address</h4>" +
                        "<?php echo $address?>"
                });
                //event listener to pop up when dorm icon is clicked
                user_dorm.addListener("click", () => {
                    infowindow.open({
                        anchor: user_dorm,
                        map,
                        shouldFocus: false,
                    });
                });
                new AutocompleteDirectionsHandler(map);
            }
                class AutocompleteDirectionsHandler {
                    map;
                    originPlaceId;
                    destinationPlaceId;
                    travelMode;
                    directionsService;
                    directionsRenderer;
                    constructor(map) {
                        this.map = map;
                        this.originPlaceId = "";
                        this.destinationPlaceId = "";
                        this.travelMode = google.maps.TravelMode.WALKING;
                        this.directionsService = new google.maps.DirectionsService();
                        this.directionsRenderer = new google.maps.DirectionsRenderer();
                        this.directionsRenderer.setMap(map);

                        const originInput = document.getElementById("origin-input");
                        const destinationInput = document.getElementById("destination-input");
                        const modeSelector = document.getElementById("mode-selector");
                        // Specify just the place data fields that you need.
                        const originAutocomplete = new google.maps.places.Autocomplete(
                            originInput,
                            { fields: ["place_id"] }
                        );
                        // Specify just the place data fields that you need.
                        const destinationAutocomplete = new google.maps.places.Autocomplete(
                            destinationInput,
                            { fields: ["place_id"] }
                        );

                        this.setupPlaceChangedListener(originAutocomplete, "ORIG");
                        this.setupPlaceChangedListener(destinationAutocomplete, "DEST");
                        this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(originInput);
                        this.map.controls[google.maps.ControlPosition.TOP_RIGHT].push(
                            destinationInput
                        );
                        this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(modeSelector);
                    }
                    // Sets a listener on a radio button to change the filter type on Places
                    // Autocomplete.
                    setupClickListener(id, mode) {
                        const radioButton = document.getElementById(id);

                        radioButton.addEventListener("click", () => {
                            this.travelMode = mode;
                            this.route();
                        });
                    }
                    setupPlaceChangedListener(autocomplete, mode) {
                        autocomplete.bindTo("bounds", this.map);
                        autocomplete.addListener("place_changed", () => {
                            const place = autocomplete.getPlace();

                            if (!place.place_id) {
                                window.alert("Please select an option from the dropdown list.");
                                return;
                            }

                            if (mode === "ORIG") {
                                this.originPlaceId = place.place_id;
                            } else {
                                this.destinationPlaceId = place.place_id;
                            }

                            this.route();
                        });
                    }
                    route() {
                        if (!this.originPlaceId || !this.destinationPlaceId) {
                            return;
                        }

                        const me = this;

                        this.directionsService.route(
                            {
                                origin: { placeId: this.originPlaceId },
                                destination: { placeId: this.destinationPlaceId },
                                travelMode: this.travelMode,
                            },
                            (response, status) => {
                                if (status === "OK") {
                                    me.directionsRenderer.setDirections(response);
                                } else {
                                    window.alert("Directions request failed due to " + status);
                                }
                            }
                        );
                    }
                }
        </script>
    </head>
    <body>
    <input type="hidden" value="<?php echo $school?>" id="school">

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

            <li class="list">
                <a href="events.php" onclick="activateLink(this)">
                    <ion-icon name="calendar-outline"></ion-icon>
                </a>
            </li>

            <li class="list active">
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

    <div style="display: none;">
        <label for="origin-input" style="left: 50px; top: 25px; width: 23vw;">From:</label>
        <input
                id="origin-input"
                type="text"
                placeholder="Starting Location"
        />

        <label for="destination-input" style="left:400px; top: 25px; width: 33%;"> To: </label>
        <input
                id="destination-input"
                type="text"
                placeholder="Ending Location"
        />
    </div>
        <div class="container">

            <h1>Neighborhoods</h1>
            <div class="map" id="map"></div>
            <br>
            <div class="content">


                <script>
                    function important_buildings() {
                        let school = document.getElementById('school').value;
                        console.log(school);
                    }
                </script>

                <div class="profile-info sub-container" id="pac-card" style="width: 100%;" onload="important_buildings()">
                    <h4 style="text-align:center;">My Important Buildings</h4>

                    <?php importantLocations(ucfirst($school), $dorm_name, $address); ?>
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
    </script>
        <!-- Google Maps API -->
        <script
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBiwSjzeDp_h7IotnPigmQTtqvgjXPNhlU&map_ids=6317b245768b029f&callback=initMap&libraries=places&v=weekly"
                async
        ></script>

        <!-- Icons for Nav Bar -->

        <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    </body>
</html>
