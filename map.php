<?php
include 'conn/conn.php';

if ($_GET["lat"] && $_GET["lng"]) {
  $lat = $_GET["lat"];
  $lng = $_GET["lng"];
  $maxLat = floatval($_GET["lat"]) + 0.1;
  $minLat = floatval($_GET["lat"]) - 0.1;
  $maxLng = floatval($_GET["lng"]) + 0.1;
  $minLng = floatval($_GET["lng"]) - 0.1;
  $auxQuery = "LATITUD < '$maxLat' AND LATITUD > '$minLat' AND LONGITUD < '$maxLng' AND LONGITUD > '$minLng'";

  $center = "$lng, $lat";
} else {
  $auxQuery = "PROVINCIA IN ('VALLADOLID')";
  $center = "-4.800243399999999, 41.6108784";
}

$sql = "SELECT * FROM gasolineras WHERE $auxQuery AND PRECIO_GASOLEO_A != '' ORDER BY PRECIO_GASOLEO_A ASC";

$data = $conn->query($sql);
//print("<pre>".print_r($result,true)."</pre>");
$gasStations = array();
$i = 1;
foreach ($data as $row) {
  $gasStations[$row["ID"]] = $row;
  $gasStations[$row["ID"]]["RANK"] = $i;

  $i++;
}

$gasStationsRank = array();
$i = 1;
foreach ($data as $row) {
  if ($i == 10) {
    break;
  }
  $gasStationsRank[$row["ID"]] = $row;
  $gasStationsRank[$row["ID"]]["RANK"] = $i;
  $i++;
}


?>


<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
  <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
  <script src='https://api.tiles.mapbox.com/mapbox-gl-js/v1.6.1/mapbox-gl.js'></script>
  <link href='https://api.tiles.mapbox.com/mapbox-gl-js/v1.6.1/mapbox-gl.css' rel='stylesheet' />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>

  <title>Document</title>
</head>
<style>
  body {
    margin: 0;
    padding: 0;
  }

  #map {
    position: absolute;
    top: 0;
    bottom: 0;
    width: 100%;
  }

  .marker {
    background-image: url('gas-station.svg');
    background-size: cover;
    width: 20px;
    height: 20px;
    cursor: pointer;
  }
  .marker-active {
    
    background-size: cover;
    width: 20px;
    height: 20px;
    cursor: pointer;
  }

  .mapboxgl-popup {
    max-width: 200px;
  }

  .mapboxgl-popup-content {
    text-align: center;
    font-family: 'Open Sans', sans-serif;
  }

  .animate {
    background-image: url('pin.svg');
    width: 50px;
    height: 50px;
    z-index: 9999;
    position: relative;
    animation: animateStation 2s cubic-bezier(0.8, 0.38, 0.45, 0.94) 0s infinite alternate;
  }
  @keyframes animateStation {
    0% {
      left: 0px;
      top: -15px;
    }

    50% {
      left: 0px;
      top: -20px;
    }

    100% {
      left: 0px;
      top: -15px;
    }
  }
</style>

<body>

  <div style="position: absolute; width: 250px; height: 100%; background: white;z-index: 9999;">
    <div style="width: 80%; margin: 0 auto;">
      <ul class="nav flex-column">
        <?php if ($data->num_rows > 0) {
          foreach ($gasStationsRank as $row) {
            echo "<li class='nav-item'>
                    <a class='nav-link ranking' href='#' id='rank-" . $row["RANK"] . "'>" . $row["RANK"] . ". " . $row["ROTULO"] . " - <strong>" . $row["PRECIO_GASOLEO_A"] . "</strong></a>
                  </li>";
          }
        }
        ?>
      </ul>
    </div>
  </div>
  <div id='map'></div>


  <script>
    mapboxgl.accessToken = 'pk.eyJ1IjoicGVsdWtvc2EiLCJhIjoiY2tiZTY1OGljMGNyZjJyb28yb3VkbDFwNyJ9.9PjhHKe0aAzScu1_ELyobQ';

    var map = new mapboxgl.Map({
      container: 'map', // container id
      style: 'mapbox://styles/mapbox/light-v10',
      center: [<?php echo $center; ?>], // starting position
      zoom: 11 // starting zoom
    });

    var geolocate = new mapboxgl.GeolocateControl();

    
    geolocate.on('geolocate', function(e) {
      var lon = e.coords.longitude;
      var lat = e.coords.latitude
      window.location.href = "http://localhost/gasolineras/map.php?lng=" + lon + "&lat=" + lat;
    });

    map.addControl(geolocate);

    var geojson = {
      type: 'FeatureCollection',
      features: [
        <?php if ($data->num_rows > 0) {
          foreach ($gasStations as $row) {
        ?> {
              type: 'Feature',
              geometry: {
                type: 'Point',
                coordinates: [<?php echo str_replace(",", ".", $row["LONGITUD"]) ?>, <?php echo str_replace(",", ".", $row["LATITUD"]) ?>]
              },
              properties: {
                title: '<?php echo $row["ROTULO"] ?>',
                description: `<p>Diesel: <strong><?php echo $row["PRECIO_GASOLEO_A"] ?></strong></p>
              <?php if ($row["PRECIO_GASOLINA_95"] != "") { ?><p>Gasolina 95: <strong><?php echo $row["PRECIO_GASOLINA_95"] ?></strong></p><?php } ?>
              <?php if ($row["PRECIO_GASOLINA_98"] != "") { ?><p>Gasolina 98: <strong><?php echo $row["PRECIO_GASOLINA_98"] ?></strong></p><?php } ?>
                              <p>Ranking: <?php echo $row["RANK"]; ?></p>`,
                rank: '<?php echo $row["RANK"]; ?>'
              }
            },
        <?php }
        } ?>
      ]
    };

    geojson.features.forEach(function(marker) {

      // create a HTML element for each feature
      var el = document.createElement('div');
      el.className = 'marker rank-' + marker.properties.rank;

      // make a marker for each feature and add to the map
      new mapboxgl.Marker(el)
        .setLngLat(marker.geometry.coordinates)
        .setPopup(new mapboxgl.Popup({
            offset: 25
          }) // add popups
          .setHTML('<h3 class>' + marker.properties.title + '</h3><p>' + marker.properties.description + '</p>'))
        .addTo(map);
    });
    $(document).ready(function() {
      $(".ranking").click(function() {
        $(".marker").removeClass("animate");
        var station = $(this).attr("id");
        var markerClass = $("." + station + "").attr("class");
        $("." + station + "").addClass("animate");
      });
    });
  </script>

</body>

</html>