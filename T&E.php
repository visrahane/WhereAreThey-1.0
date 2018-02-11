<html>
    <body>
        <script>
            function enableLocationTxtBx(){
                document.getElementById("locationTxt").disabled = false;
            }
            function disableLocationTxtBx(){
                document.getElementById("locationTxt").disabled = true;
            }
        </script>
        <?php echo $_POST["location"]; ?><br>
        <?php echo $_POST["distance"]; ?>

        <h1 style="text-align: center;"><i>Travel and Entertainment Search</i></h1>
        <form method="POST" action="T&E.php" name="travelEntertainmentForm">
            <b>Keyword <input type="text"><br>
            Category 
                <select>
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
            <input type="radio" name="location" style="margin-left: 302px;" onClick="enableLocationTxtBx()"> <input placeholder="location" name="location" id="locationTxt" disabled><br>
            <input type="submit" value="Submit">
        </form>
     
    </body>
</html>