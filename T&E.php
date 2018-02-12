<html>
    <body onload="fetchGeoLocation()">
        <script>
            //window.onload = function() {
              //  fetchGeoLocation();                
            //};
            function fetchGeoLocation(){
                var xmlHttpReq=new XMLHttpRequest();
                xmlHttpReq.onreadystatechange = function() {
                     if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("search").disabled = false;
                        var jsonDoc = xmlHttpReq.responseText;                        
                        var locationJson=JSON.parse(xmlHttpReq.responseText);
                        console.log(locationJson);
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
        <?php print_r($_POST); ?><br>
        
        <table border="10" style="   margin-left:  auto;    margin-right:  auto;">
        <tr><td>
        <h1 style="text-align: center;"><i>Travel and Entertainment Search</i></h1><hr>
        <form method="POST" action="T&E.php" name="travelEntertainmentForm">
            <b>Keyword <input required><br>
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
            <input type="button" value="Clear" onClick="reset()" >
        </form>
        </tr>
        </table>
    </body>
</html>