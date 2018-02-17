<html>
    <body onload="fetchGeoLocation()">

        <!-- HTML Form -->    
        <table border="10" style="   margin-left:  auto;    margin-right:  auto;">
        <tr><td>
        <h1 style="text-align: center;"><i>Travel and Entertainment Search</i></h1><hr>
        <form method="POST" action="T&E.php" name="travelEntertainmentForm">
            <b>Keyword <input name=keyword required><br>
            Category 
                <select name="category">
                    <!-- cafe,bakery, restaurant, beauty  salon, casino,  movie  theater,  lodging,  airport,  train  station,
                     subway  station,  bus  station -->
                    <option value="default">Default</option>
                    <option value="cafe">Cafe</option>
                    <option value="bakery">Bakery</option>
                    <option value="restaurant">Restaurant</option>
                    <option value="beautySalon">Beauty Salon</option>
                    <option value="casino">Casino</option>
                    <option value="movieTheater">Movie Theater</option>
                    <option value="lodging">Lodging</option>
                    <option value="airport">Airport</option>
                    <option value="trainStation">Train Station</option>
                    <option value="subwayStation">Subway Station</option>
                    <option value="busStation">Bus Station</option>
                </select>    
            <br>    
            Distance(miles) <input type="text" placeholder="10" name="distance"> from <input type="radio" name="location" value="here" checked onClick="disableLocationTxtBx()"> Here <br>
            <input type="radio" name="location" style="margin-left: 303px;" onClick="enableLocationTxtBx()"> <input placeholder="location" name="location" id="locationTxt" disabled required><br>
            <input type="submit" value="Search" id="search" onClick="submitForm()" disabled> 
            <input type="button" value="Clear" onClick="reset()" >
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude" >
        </form>
        </tr>
        </table>

        <!-- PHP -->
        <?php print_r($_POST); ?><br>
        <?php 
            //CONSTANST
            define("GOOGLE_GEOCODING_API","https://maps.googleapis.com/maps/api/geocode/json?");
            define("GOOGLE_KEY","AIzaSyDWBtO4XwwiZCwCDr6z2aK8rXZMuO0OTNM");
            define("GOOGLE_NEARBY_SEARCH","https://maps.googleapis.com/maps/api/place/nearbysearch/json?");
            function getGeoLocation(){
                $query = http_build_query([
                    'address' => $_POST["location"],
                    'key' => GOOGLE_KEY,                    
                   ]);
                $arrContextOptions=array(
                    "ssl"=>array(
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                    ),
                );  
                return file_get_contents(GOOGLE_GEOCODING_API.$query, false, stream_context_create($arrContextOptions));
            }

            function callGooglePlaces(){
                $query = http_build_query([
                    'location' => $_POST["latitude"].",".$_POST["longitude"],
                    'radius' => $_POST["distance"]*1609.34,   //convert to m from miles
                    'type'=> $_POST["category"],
                    'keyword'=>$_POST["keyword"],
                    'key'=>GOOGLE_KEY,
                   ]);
                $arrContextOptions=array(
                    "ssl"=>array(
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                    ),
                );  
                return file_get_contents(GOOGLE_NEARBY_SEARCH.$query, false, stream_context_create($arrContextOptions));
            }
                       
            if(isset($_POST["location"]) && $_POST["location"]!="here"){
                //echo("USE google loc");
                //get geolocation from google
                $locationJson=json_decode(getGeoLocation(),true);
                $_POST["latitude"]=($locationJson["results"][0]["geometry"]["location"]["lat"]);
                $_POST["longitude"]=($locationJson["results"][0]["geometry"]["location"]["lng"]);

            }            
            $googlePlacesResponse=json_decode(callGooglePlaces());
            print_r($googlePlacesResponse);
            print_r($_POST["latitude"]);
            print_r($_POST["longitude"])
            

        ?>

        <!-- Javascript -->
        <script>
            //window.onload = function() {
              //  fetchGeoLocation();                
            //};
            function submitForm(){
                if(!travelEntertainmentForm.distance.value){
                    travelEntertainmentForm.distance.value=10;
                }
                travelEntertainmentForm.submit();
            }
            function fetchGeoLocation(){
                var xmlHttpReq=new XMLHttpRequest();
                xmlHttpReq.onreadystatechange = function() {
                     if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("search").disabled = false;
                        var jsonDoc = xmlHttpReq.responseText;                        
                        var locationJson=JSON.parse(xmlHttpReq.responseText);
                        document.getElementById("latitude").value =locationJson.lat;
                        document.getElementById("longitude").value =(locationJson.lon);
                    }
                };
                xmlHttpReq.open("GET","http://ip-api.com/json",true);
                xmlHttpReq.send();
                
            }
            function enableLocationTxtBx(){
                document.getElementById("locationTxt").disabled = false;
            }
            function disableLocationTxtBx(){
                document.getElementById("locationTxt").disabled = true;
            }
            function reset(){
                document.getElementById("travelEntertainmentForm").reset(); 
            }
        </script>
    </body>
</html>