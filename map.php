<?php
include 'conn/conn.php';
$sql = "SELECT * FROM gasolineras WHERE PROVINCIA IN ('VALLADOLID')";
$data = $conn->query($sql);
//print("<pre>".print_r($,true)."</pre>");


$sql = "SELECT * FROM gasolineras WHERE PROVINCIA IN ('VALLADOLID') AND PRECIO_GASOLEO_A > 0 ORDER BY PRECIO_GASOLEO_A ASC LIMIT 1";
$lowPrice = $conn->query($sql);
$conn->close();

?>

<?php 
$check = array();
if ($data->num_rows > 0) {
  foreach ($data as $v) {
    $horario = $row["HORARIO"];
    $horario = str_replace("-", " a ", $horario);
    $horario = str_replace("L", "Lunes", $horario);
    $horario = str_replace("M", "Martes", $horario);
    $horario = str_replace("M", "Miercoles", $horario);
    $horario = str_replace("J", "Jueves", $horario);
    $horario = str_replace("V", "Viernes", $horario);
    $horario = str_replace("S", "Sabado", $horario);
    $horario = str_replace("D", "Domingo", $horario);
    $row["HORARIO"] = $horario;
  }
} 
//$check = json_encode($check, JSON_PRETTY_PRINT);
echo '<pre>',print_r($check,1),'</pre>';
exit;
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

  .mapboxgl-popup {
    max-width: 200px;
  }

  .mapboxgl-popup-content {
    text-align: center;
    font-family: 'Open Sans', sans-serif;
  }
</style>

<body>

  <?php /*
    foreach ($lowPrice as $low){
        print("<pre>".print_r($low["PRECIO_GASOLEO_A"],true)."</pre>");
    }
    exit;
    */
  ?>
  <div id='map'></div>


  <script>
    mapboxgl.accessToken = 'pk.eyJ1IjoicGVsdWtvc2EiLCJhIjoiY2s0ZWt3bmw5MDQ3MzNkbWh4OHl6Ymk0YyJ9.SjBddTn9NPdfGPGCqfv4xQ';

    var map = new mapboxgl.Map({
      container: 'map', // container id
      style: 'mapbox://styles/mapbox/light-v10',
      center: [-4.7285413, 41.6522966], // starting position
      zoom: 10 // starting zoom
    });

    map.addControl(
      new mapboxgl.GeolocateControl({
        positionOptions: {
          enableHighAccuracy: true
        },
        trackUserLocation: true
      })
    );

    var geojson = {
      type: 'FeatureCollection',
      features: [
        <?php if ($data->num_rows > 0) {
          foreach ($data as $row) {
        ?> {
              type: 'Feature',
              geometry: {
                type: 'Point',
                coordinates: [<?php echo str_replace(",", ".", $row["LONGITUD"]) ?>, <?php echo str_replace(",", ".", $row["LATITUD"]) ?>]
              },
              properties: {
                title: '<?php echo $row["ROTULO"] ?>',
                description: '<p>Diesel: <strong><?php echo $row["PRECIO_GASOLEO_A"] ?></strong></p><p>Horario: <strong><?php echo $row["HORARIO"] ?></strong></p>'
              }
            },
        <?php }
        } ?>
      ]
    };

    geojson.features.forEach(function(marker) {

      // create a HTML element for each feature
      var el = document.createElement('div');
      el.className = 'marker';

      // make a marker for each feature and add to the map
      new mapboxgl.Marker(el)
        .setLngLat(marker.geometry.coordinates)
        .setPopup(new mapboxgl.Popup({
            offset: 25
          }) // add popups
          .setHTML('<h3>' + marker.properties.title + '</h3><p>' + marker.properties.description + '</p>'))
        .addTo(map);
    });
  </script>

</body>

</html>