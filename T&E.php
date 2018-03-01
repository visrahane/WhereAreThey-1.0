
<?php // print_r($_POST); ?>
<?php 
        //CONSTANST
        //place details api
        define("GOOGLE_GEOCODING_API","https://maps.googleapis.com/maps/api/geocode/json?");
        define("GOOGLE_KEY","AIzaSyDWBtO4XwwiZCwCDr6z2aK8rXZMuO0OTNM");
        define("GOOGLE_NEARBY_SEARCH_API","https://maps.googleapis.com/maps/api/place/nearbysearch/json?");
        define("GOOGLE_PLACES_DETAILS_API","https://maps.googleapis.com/maps/api/place/details/json?");
        define("GOOGLE_PLACES_PHOTO_API","https://maps.googleapis.com/maps/api/place/photo?");

        if(isset($_GET['place_id'])){
            function saveToFile($imageFile,$i){
                file_put_contents('./myImages/'.$_GET['place_id'].$i.'.png', $imageFile);
            }
            function getImages($placeDetailsJSON){
                //$results=$placeDetailsJSON["results"];
                for($i=0;$i<5 && $i<count($placeDetailsJSON["result"]["photos"]);$i++){
                    //print_r($photo);
                    $query = http_build_query([
                        'photo_reference' => $placeDetailsJSON["result"]["photos"][$i]["photo_reference"],
                        'maxheight' => $placeDetailsJSON["result"]["photos"][$i]["height"],
                        'maxhwidth' => $placeDetailsJSON["result"]["photos"][$i]["width"],
                        'key' => GOOGLE_KEY,                    
                    ]);
                    $image=httpGETCall(GOOGLE_PLACES_PHOTO_API,$query);
                    saveToFile($image,$i);
                }
                
                //return httpGETCall(GOOGLE_PLACES_DETAILS_API,$query);
            }

            function getPlaceDetailsAndReviews(){
                $query = http_build_query([
                    'place_id' => $_GET["place_id"],
                    'key' => GOOGLE_KEY,                    
                   ]);
                return httpGETCall(GOOGLE_PLACES_DETAILS_API,$query);
            }

            function httpGETCall($url,$query){
                $arrContextOptions=array(
                    "ssl"=>array(
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                    ),
                ); 
               
                return file_get_contents($url.$query, false, stream_context_create($arrContextOptions));           
            }
            $placeDetailsJSON=json_decode(getPlaceDetailsAndReviews(),true);
            getImages($placeDetailsJSON);
            echo json_encode($placeDetailsJSON);//return place details json back to js
        }
        //Form submit api
        if(isset($_POST['keyword'])){
            //echo("hi");
                  
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
                    'radius' => $_POST["distance"]*1609.34,   //convert to meter from miles
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
                return file_get_contents(GOOGLE_NEARBY_SEARCH_API.$query, false, stream_context_create($arrContextOptions));
            }
                       
            if(isset($_POST["location"]) && $_POST["location"]!="here"){
                //get geolocation from google
                $locationJson=json_decode(getGeoLocation(),true);
                $_POST["latitude"]=($locationJson["results"][0]["geometry"]["location"]["lat"]);
                $_POST["longitude"]=($locationJson["results"][0]["geometry"]["location"]["lng"]);
            }            
            
            $googlePlacesResponse=(json_decode(callGooglePlaces()));
            //print_r($googlePlacesResponse);
            //$responseJSON=(callGooglePlaces());
            header('Content-type: application/json');
            echo json_encode(($googlePlacesResponse));     
            exit();      
        }
?>

<html>
    <head>
        <style>
            #map {
                height: 320px;
                width: 80%;
            }
            #directionFrame{
                position: relative;
                margin-top: -320px;
                background-color: lightgrey;
                width: 25%;
            }
            #walk,#bike,#drive{
                margin-left: 3%;
            }
            #walk:hover,#bike:hover,#drive:hover{
                background-color: gray; 
            }
            #searchTable{
                border: 1px solid black;
                margin-left: auto;
                margin-right: auto;
                width: 60%;
                display:none;
            }
        </style>
    </head>
    <body>
        <!-- PHP -->
        <!-- HTML Form -->    
        <table border="10" style="   margin-left:  auto;    margin-right:  auto; width:45%">
        <tr><td>
        <h1 style="text-align: center;"><i>Travel and Entertainment Search</i></h1><hr>
        <form name="travelEntertainmentForm" onsubmit="event.preventDefault();submitForm()" method="post">
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
            <input type="submit" value="Search" id="search" disabled> 
            <input type="button" value="Clear" onClick="resetValues()" >
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude" >
        </form>
        </tr>
        </table>
        <br>
        <br>
        <table id="searchTable">
        </table>  
        <div id="mapFrame" style="display:none;" >
            <div id="map" >  </div>
            <div id="directionFrame" >
                <div id="walk" onClick="getDirections('WALKING');">Walk There</div>
                <div id="bike" onClick="getDirections('BICYCLING')" >Bike There</div>
                <div id="drive" onClick="getDirections('DRIVING')" >Drive There</div>
            </div>    
            
        </div>
        <!-- Javascript style="display:none;"-->
        <script> 
            window.onload = function() {
                fetchGeoLocation();                
            };  
            function getDirections(mode){                
                calculateAndDisplayRoute(directionsService, directionsDisplay,mode);
            }                   
            function submitForm(){
                if(!travelEntertainmentForm.distance.value){
                    travelEntertainmentForm.distance.value=10;
                }
                var xmlHttpReq=new XMLHttpRequest();
                xmlHttpReq.onreadystatechange = function() {
                     if (this.readyState == 4 && this.status == 200) {
                        //handle response from php
                        var jsonDoc = (xmlHttpReq.responseText); 
                        //alert(jsonDoc);
                        parseJSON(jsonDoc);
                    }
                };
                var queryStr="location="+travelEntertainmentForm.location.value
                                +"&latitude="+travelEntertainmentForm.latitude.value
                                +"&longitude="+travelEntertainmentForm.longitude.value
                                +"&distance="+travelEntertainmentForm.distance.value
                                +"&category="+travelEntertainmentForm.category.value
                                +"&keyword="+travelEntertainmentForm.keyword.value
                xmlHttpReq.open("POST","T&E.php",true);
                xmlHttpReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xmlHttpReq.send(queryStr);                
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
                xmlHttpReq.open("GET","http://ip-api.com/json",false);
                xmlHttpReq.send();                
            }
            function enableLocationTxtBx(){
                document.getElementById("locationTxt").disabled = false;
            }
            function disableLocationTxtBx(){
                document.getElementById("locationTxt").disabled = true;
            }
            function resetValues(){
                travelEntertainmentForm.reset(); 
                document.getElementById("searchTable").style.display="none";
                document.getElementById("mapFrame").style.display="none";
            }
            function parseJSON(jsonDoc){
                var jsonObj=JSON.parse(jsonDoc);
                //alert(jsonObj.results[0].name);
                var body=document.body;
                var table=document.getElementById("searchTable");
                table.style.display="block";
                //table rows
                var row1=document.createElement("tr");
                
                var resultsArray=jsonObj.results;
                if(resultsArray.length==0){
                    row1.style.textAlign="center";
                    row1col1=createCol("No records has been found","td"); 
                    row1.appendChild(row1col1);
                }
                else{
                    row1col1=createCol("Category","th");
                    row1col2=createCol("Name","th");
                    row1col3=createCol("Address","th");
                    row1.appendChild(row1col1);row1.appendChild(row1col2);row1.appendChild(row1col3);                    
                }
                table.appendChild(row1);
                for(var i=0;i<resultsArray.length;i++){
                    //icon
                    var col1=createCol("","td");
                    var imgTag=document.createElement("img");
					imgTag.setAttribute("src",resultsArray[i].icon);
					col1.appendChild(imgTag);
				    //name
                    var col2=createCol("","td");	
                    var anchorTag=document.createElement("a");
					anchorTag.setAttribute("href","T&E.php?place_id="+resultsArray[i].place_id);//call php
					anchorTag.appendChild(document.createTextNode(resultsArray[i].name));
                    col2.appendChild(anchorTag);
                    //address
					var col3=createCol("","td");//resultsArray[i].vicinity
                    var divTag = document.createElement("div");
                    var text=document.createTextNode(resultsArray[i].vicinity);                                 
                    divTag.onclick = displayMap; 
                    divTag.value=resultsArray[i].name;
                    divTag.appendChild(text);
                    col3.appendChild(divTag);

                    var row=document.createElement("tr");
					row.appendChild(col1);row.appendChild(col2);
					row.appendChild(col3);
                    table.appendChild(row);
                }
                body.appendChild(table);
                //initMap();
            }
            function displayMap(event){  
                this.appendChild(document.getElementById("mapFrame"));
                var mapFrame=document.getElementById("mapFrame");
                mapFrame.style.display="block";
                mapFrame.value=this.value;                
            }

            function createCol(colText,rowType){
				var col=document.createElement(rowType);
				col.style.border="1px solid black";	
				var text=document.createTextNode(colText);
				col.appendChild(text);
				return col;
			}

            function initMap() {
                directionsService = new google.maps.DirectionsService;
                directionsDisplay = new google.maps.DirectionsRenderer;
                if(!document.getElementById("latitude").value){
                    fetchGeoLocation();
                }     
                
                var latitude=parseFloat(document.getElementById("latitude").value);
                var longitude=  parseFloat(document.getElementById("longitude").value);    
                var uluru = {lat:latitude, lng:longitude };
                var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 4,
                center: uluru   
                });
                var marker = new google.maps.Marker({
                position: uluru,
                map: map
                });
                directionsDisplay.setMap(map);
                
            }
            function calculateAndDisplayRoute(directionsService, directionsDisplay,selectedMode) {
      	                      
                var latitude=parseFloat(document.getElementById("latitude").value);
                var longitude=  parseFloat(document.getElementById("longitude").value);    
                var ori = {lat:latitude, lng:longitude };
                //alert(ori+" "+document.getElementById("mapFrame").value);
                directionsService.route({
		        //current loc, can be latlan obj - new google.maps.LatLng(41.850033, -87.6500523);
                origin: ori,
		        //get it from row, use placeId for eg
                destination: document.getElementById("mapFrame").value,
		        //get travel mode from btn click
                travelMode: google.maps.TravelMode[selectedMode]

                }, function(response, status) {
                if (status === 'OK') {
                directionsDisplay.setDirections(response);
                } else {
                window.alert('Directions request failed due to ' + status);
                }
                });
            }
                        
        </script>
        <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDWBtO4XwwiZCwCDr6z2aK8rXZMuO0OTNM&callback=initMap">
        </script>

    </body>
</html>

