<!-- ,-, Austin was here ,-, -->
<?php
// Obtain the connection info
require("dbinfo.php"); 

// Opens a connection to a MS SQL server
$connection=mssql_connect ($server, $username, $password);
if (!$connection) {  die('Not connected : ' . mssql_get_last_message());} 

// Set the active MS SQL database
$db_selected = mssql_select_db($database, $connection);
if (!$db_selected) {
  die ('Can\'t use db : ' . mssql_get_last_message());
} 

// Query for Accounts with Opened Service Orders and a Valid address
$query = "SELECT AccountNumber FROM dbo.tblAccounts WHERE (Address1 IS NOT NULL AND Address1 NOT LIKE '%P%O% Box%') AND City IS NOT NULL and State IS NOT NULL AND CountOfOpenSOs IS NULL ORDER BY AccountNumber";
$result = mssql_query($query);
if (!$result) {  
  die('Invalid query: ' . mssql_get_last_message());
} 

$row = array();
while ($row = @mssql_fetch_assoc($result)){ 
	$data[] = $row['AccountNumber'];
}
?>
<!DOCTYPE html>
<html lang="en" style="height:100%">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="map, sexy, virtual, wow">
    <meta name="author" content="Austin Lane">

    <title>Ticket Map</title>
	<link rel="shortcut icon" type="image/ico" href="http://www.google.com/s2/favicons?domain=github.com"/>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-switch.css" rel="stylesheet">
    <link href="css/bootstrap-select.css" rel="stylesheet">

    <!-- Add custom CSS here -->
    <link href="css/simple-sidebar.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <!-- Custom JavaScript -->
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
  </head>
  <script>
      var gmarkers = [];
      var gicons = [];
      var map = null;

var infowindow = new google.maps.InfoWindow(
  { 
    size: new google.maps.Size(150,50)
  });


gicons["red"] = new google.maps.MarkerImage("http://google.com/mapfiles/ms/micons/red-dot.png",
      // This marker is 32 pixels wide by 32 pixels tall.
      new google.maps.Size(32, 32),
      // The origin for this image is 0,0.
      new google.maps.Point(0,0),
      // The anchor for this image is at 9,34.
      new google.maps.Point(9, 34));
  // Marker sizes are expressed as a Size of X,Y
  // where the origin of the image (0,0) is located
  // in the top left of the image.
 
  // Origins, anchor positions and coordinates of the marker
  // increase in the X direction to the right and in
  // the Y direction down.

  var iconImage = new google.maps.MarkerImage('http://google.com/mapfiles/ms/micons/red-dot.png',
      // This marker is 32 pixels wide by 32 pixels tall.
      new google.maps.Size(32, 32),
      // The origin for this image is 0,0.
      new google.maps.Point(0,0),
      // The anchor for this image is at 9,34.
      new google.maps.Point(9, 34));
      // Shapes define the clickable region of the icon.
      // The type defines an HTML &lt;area&gt; element 'poly' which
      // traces out a polygon as a series of X,Y points. The final
      // coordinate closes the poly by connecting to the first
      // coordinate.
  var iconShape = {
      coord: [9,0,6,1,4,2,2,4,0,8,0,12,1,14,2,16,5,19,7,23,8,26,9,30,9,34,11,34,11,30,12,26,13,24,14,21,16,18,18,16,20,12,20,8,18,4,16,2,15,1,13,0],
      type: 'poly'
  };

function getMarkerImage(iconColor) {
   if ((typeof(iconColor)=="undefined") || (iconColor==null)) { 
      iconColor = "red"; 
   }
   if (!gicons[iconColor]) {
      gicons[iconColor] = new google.maps.MarkerImage("images/"+ iconColor +".png",
      // This marker is 32 pixels wide by 34 pixels tall.
      new google.maps.Size(32, 37),
      // The origin for this image is 0,0.
      new google.maps.Point(0,0),
      // The anchor for this image is at 6,20.
      new google.maps.Point(9, 34));
   } 
   return gicons[iconColor];

}

function category2color(category) {
   var color = "red";
   switch(category) {
     case "vendor": color = "bank";
                break;
     case "company":    color = "office-building";
                break;
     case "residential":    color = "townhouse";
                break;
     case "personal":    color = "group";
                break;
     case "prospect":    color = "binoculars";
                break;
     default:   color = "robbery";
                break;
   }
   return color;
}

      gicons["vendor"] = getMarkerImage(category2color("vendor"));
      gicons["company"] = getMarkerImage(category2color("company"));
      gicons["residential"] = getMarkerImage(category2color("residential"));
      gicons["personal"] = getMarkerImage(category2color("personal"));
      gicons["prospect"] = getMarkerImage(category2color("prospect"));

      // A function to create the marker and set up the event window
function createMarker(latlng,name,html,category) {
    var contentString = html;
    var marker = new google.maps.Marker({
        position: latlng,
        icon: gicons[category],
        map: map,
        title: name,
        zIndex: Math.round(latlng.lat()*-100000)<<5
        });
        // === Store the category and name info as a marker properties ===
        marker.mycategory = category;                                 
        marker.myname = name;
        gmarkers.push(marker);
    google.maps.event.addListener(marker, 'click', function() {
        infowindow.setContent(contentString); 
        infowindow.open(map,marker);
        });
}

      // == shows all markers of a particular category, and ensures the checkbox is checked ==
      function show(category) {
        for (var i=0; i<gmarkers.length; i++) {
          if (gmarkers[i].mycategory == category) {
            gmarkers[i].setVisible(true);
          }
        }
        // == check the checkbox ==
        document.getElementById(category+"box").checked = true;
      }

      // == hides all markers of a particular category, and ensures the checkbox is cleared ==
      function hide(category) {
        for (var i=0; i<gmarkers.length; i++) {
          if (gmarkers[i].mycategory == category) {
            gmarkers[i].setVisible(false);
          }
        }
        // == clear the checkbox ==
        document.getElementById(category+"box").checked = false;
        // == close the info window, in case its open on a marker that we just hid
        infowindow.close();
      }

      // == a checkbox has been clicked ==
      function boxclick(box,category) {
        if (box.checked) {
          show(category);
        } else {
          hide(category);
        }
      }

      function myclick(i) {
        google.maps.event.trigger(gmarkers[i],"click");
      }

  function initialize() {
    var myOptions = {
      zoom: 10,
      center: new google.maps.LatLng(30.846667,-83.283056),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);


    google.maps.event.addListener(map, 'click', function() {
        infowindow.close();
        });



      // Read the data
	var json = (function () { 
		var json = null; 
		$.ajax({ 
			'async': false, 
			'global': false, 
			'url': "geocoding/codes.json", 
			'dataType': "json", 
			'success': function (data) {
				 json = data; 
			 }
		});
		return json;
	})();

	// Get a list of keys (the Account Number) from the json file as an array
	var json_strings = Object.keys(json);
	// Convert the json key to integers.
	var json_int = json_strings.map(function (x) { 
		return parseInt(x, 10); 
	});
	// Get a list of Accounts (by account number) with Opened Service Orders from PHP/MSSQL
    var phpson = $.parseJSON('<?php echo json_encode($data); ?>');
	// Compare the array of accounts in the cache to the list of accounts with opened service orders.
	var diff = $(json_int).not(phpson).get();// Outputs a list of accounts that are in the cache (from JSON) and have opened tickets (from PHP/MSSQL)

        for (var key in json) {
          // obtain the attributes of each marker
          var lat = parseFloat(json[key]["lat"]);
          var lng = parseFloat(json[key]["lng"]);
          var point = new google.maps.LatLng(lat,lng);
          var address = json[key]["address"];
          var name = json[key]["name"];
          var category = json[key]["type"].toLowerCase();
		  // Used as contentstring in the Info Box (above the map marker)
          var html = "<b>"+name+"<\/b><p>Acc #: "+key+"<\/b><p>"+address;
          // create the marker
		    // if the key value (account number) from json is a value in the "diff" array, create a marker for that account.
			if($.inArray(parseInt(key), diff) != -1) {
				console.log(key+" has opened tickets");
				var marker = createMarker(point,name,html,category);
			} else if(category == "vendor"){
				console.log(key+" is a vendor");
				var marker = createMarker(point,name,html,category);
			} else {
				console.log(key+" does not have opened tickets");
			} 
		}
        // == show or hide the categories initially ==
        hide("vendor");
        show("company");
        hide("residential");
        hide("personal");
        hide("prospect");
  }
  </script>
  <body style="height:100%" onload="initialize();">
      
      <!-- Sidebar -->
    <div id="wrapper" class="active" style="height:100%">
      
      <!-- Sidebar -->
            <!-- Sidebar -->
      <div id="sidebar-wrapper">
      <ul id="sidebar_menu" class="sidebar-nav">
           <li class="sidebar-brand"><a id="menu-toggle" href="#">Menu<span id="main_icon" class="glyphicon glyphicon-map-marker" style="color:#FC6355"></span></a></li>
      </ul>
        <ul class="sidebar-nav" id="sidebar">     
          <li><a>Vendor<span class="sub_icon glyphicon glyphicon-usd" style="color:#38A61C"></span></a></li>
			<input type="checkbox" id="vendorbox">
		  <li><a>Company<span class="sub_icon glyphicon glyphicon-briefcase" style="color:#3875D7"></span></a></li>
			<input type="checkbox" id="companybox" checked>
          <li><a>Resi<span class="sub_icon glyphicon glyphicon-home" style="color:#FFC11F"></span></a></li>
			<input type="checkbox" id="residentialbox">
          <li><a>Personal<span class="sub_icon glyphicon glyphicon-user" style="color:#DC1EE6"></span></a></li>
			<input type="checkbox" id="personalbox">
          <li><a>Prospect<span class="sub_icon glyphicon glyphicon-eye-open" style="color:#5EC8BD"></span></a></li>
			<input type="checkbox" id="prospectbox">
        </ul>
       <ul class="sidebar-nav sidebar-bottom" id="sidebar">    
		  <!-- Invoke python script to generate new codes -->
          <li><a href='#' onclick="$.get( 'geocoding/invoke.php' );">Refresh<span class="sub_icon glyphicon glyphicon-refresh"></span></a></li>
          <li><a data-toggle="modal" data-target="#help">Help<span class="sub_icon glyphicon glyphicon-question-sign"></span></a></li>
        </ul>
      </div>
          
      <!-- Google Map in the main area -->
      <div id="map-canvas" style="height:100%"></div>
	  <div id="littlebox" style="position: absolute; bottom:4px; right:2px;">
		<form name="tickettype">
		<select class="selectpicker" name="ticketbox"
			OnChange="alert(tickettype.ticketbox.options[selectedIndex].value)">
			<option selected> All
			<option value="Appointment Scheduled"> Appointment Scheduled
			<option value="Pending Delivery"> Pending Delivery
			<option value="Pending Schedule"> Pending Schedule
			<option value="Pickup Waiting"> Pickup Waiting
			<option value="Travel"> Travel
		</select>
	  </div>
    </div>
      
	<!-- Help Modal -->
	<div class="modal fade" id="help" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-warning-sign"></span> HELP ME!</h4>
		  </div>
		  <div class="modal-body">
			This fancy web application is fairly self explanatory, but since you asked, here's how it works!<br>
			<blockquote>Click on a filter on the left sidebar, this will control the potential clients that will be searched through in the database.<br>
			Markers should populate based upon your selected filter!
			</blockquote>
			If you have any questions, or want some strange feature added, just ask <strong>Austin</strong>!<br>
			Hangouts: vidplace7@gmail.com
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>
	  </div>
	</div>

    <!-- JavaScript -->
    <script src="js/jquery-1.10.2.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/bootstrap-switch.js"></script>
    <script src="js/bootstrap-select.js"></script>

    <!-- Custom JavaScript for the Menu Toggle -->
    <script>
   $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("active");
});
    </script>
	<script>
	$.fn.bootstrapSwitch.defaults.size = 'small';
	$("[id='vendorbox']").bootstrapSwitch();
	$("[id='companybox']").bootstrapSwitch();
	$("[id='residentialbox']").bootstrapSwitch();
	$("[id='personalbox']").bootstrapSwitch();
	$("[id='prospectbox']").bootstrapSwitch();
	$('input[id="vendorbox"]').on('switchChange.bootstrapSwitch', function(event, state) {boxclick(this,'vendor')});
	$('input[id="companybox"]').on('switchChange.bootstrapSwitch', function(event, state) {boxclick(this,'company')});
	$('input[id="residentialbox"]').on('switchChange.bootstrapSwitch', function(event, state) {boxclick(this,'residential')});
	$('input[id="personalbox"]').on('switchChange.bootstrapSwitch', function(event, state) {boxclick(this,'personal')});
	$('input[id="prospectbox"]').on('switchChange.bootstrapSwitch', function(event, state) {boxclick(this,'prospect')});
	$('.selectpicker').selectpicker();
	</script>
  </body>
</html>