
        // Initialize and add the map
        function initMap() {
            const directionsService = new google.maps.DirectionsService();
            const directionsRenderer = new google.maps.DirectionsRenderer();
            // The location of campus
            const campus = {lat: 39.16762140414875, lng: -86.51775336283116};
            // The map, centered on IU's Campus
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 16,
                center: campus,
            });
            // The marker, positioned at campus
            const marker = new google.maps.Marker({
                position: campus,
                map: map,
            });

            function calcRoute() {
                var selectMode = document.getElementById('mode').value;
                var request = {
                    orgin: haight,
                    destination: oceanBeach,
                    travelMode: google.map.TravelMode[selectMode]
                };
                directionsService.route(request, function (response, status) {
                    if (status === 'OK') {
                        directionsRenderer.setDirections(response);
                    }
                });
            }
        }
        const list = document.querySelectorAll('.list');
        function activeLink(){
            list.forEach((item) => item.classList.remove('active'));
            this.classList.add('active');
        }
        list.forEach((item) => item.addEventListener('click',activeLink));
        $(document).ready(function(){
            $("#starting_location_type").change(function(){

                var el = $(this);

                if(el.val === "dorm"){
                $("#starting_location_building option").remove();
                $("#starting_location_building").append("<option value=\"ashton\">Ashton</option>" +
                        "<option value=\"collins\">Collins</option>" +
                        "<option value=\"eigenmann\">Eigenmann</option>" +
                        "<option value=\"hillcrest\">Hillcrest</option>" +
                        "<option value=\"teter\">Teter</option>" +
                        "<option value=\"union_st\">Union Street</option>" +
                        "<option value=\"wright\">Wright</option>" +
                        "<option value=\"campus_view\">Campus View</option>" +
                        "<option value=\"redbud\">Redbud Hill</option>" +
                        "<option value=\"tulip_tree\">Tulip Tree</option>" +
                        "<option value=\"briscoe\">Briscoe</option>" +
                        "<option value=\"foster\">Foster</option>" +
                        "<option value=\"mcnutt\">McNutt</option>" +
                        "<option value=\"walnut_grove\">Walnut Grove</option>" +
                        "<option value=\"third_union\">3rd and Union </option>" +
                        "<option value=\"forest\">Forest</option>" +
                        "<option value=\"mason\">Mason</option>" +
                        "<option value=\"read\">Read</option>" +
                        "<option value=\"spruce\">Spruce</option>" +
                        "<option value=\"university_east\">University East</option>" +
                        "<option value=\"willkie\">Willkie</option>" +
                        "<option value=\"the_avenue\">The Avenue</option>");
                }
                else if(el.val() === "dining_hall"){
                $("#starting_location_building option").remove();
                $("#starting_location_building").append("<option value=\"ballantine\">Ballantine Hall</option>" +
                        "<option value='biology_building'>Biology Building</option>"+
                        "<option value='eigenmann_hall'>Eigenmann Hall</option>"+
                        "<option value='briscoe'>Briscoe Quad</option>"+
                        "<option value='mcnutt'>McNutt Quad</option>" +
                        "<option value='union_st'>Union Street Center</option>"+
                        "<option value='Wilkie'>Wilkie Quad</option>" +
                        "<option value='wright'>Wright Quad</option>" +
                        "<option value='wells_library'>Bookmarket Eatery</option>" +
                        "<option value='forest'>Forest Quad</option>" +
                        "<option value='goodbody'>Goodbody Hall</option>");
                }
                else if(el.val() === "academic"){
                $("#starting_location_building option").remove();
                $("#starting_location_building").append("<option value=\"ballantine\">Ballantine Hall</option>" +
                        "<option value='biology_building'>Biology Building</option>"+
                        "<option value='chemistry_building'>Chemistry Building</option>");
                }
            });

        });




