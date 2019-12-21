<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src='https://api.mapbox.com/mapbox-gl-js/v1.4.1/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v1.4.1/mapbox-gl.css' rel='stylesheet' />

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

    ;

    .marker {
        background-image: url('mapbox-icon.png');
        background-size: cover;
        width: 50px;
        height: 50px;
        border-radius: 50%;
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
<?php
$servername = "127.0.0.1:3307";
$username = "root";
$password = "";
$dbname = "gasolineras";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT LATITUD, LONGITUD, ROTULO, PRECIO_GASOLEO_A FROM gasolineras WHERE PROVINCIA = 'VALLADOLID'";
$data = $conn->query($sql);
//print("<pre>".print_r($result,true)."</pre>");


$conn->close();

?>

<body>
    <div id='map'></div>

    <script>
        mapboxgl.accessToken = 'pk.eyJ1IjoicGVsdWtvc2EiLCJhIjoiY2s0ZWt3bmw5MDQ3MzNkbWh4OHl6Ymk0YyJ9.SjBddTn9NPdfGPGCqfv4xQ';

        var map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/light-v10',
            center: [-96, 37.8],
            zoom: 3
        });


        var geojson = {
            type: 'FeatureCollection',
            features: [
                { <?php if ($data->num_rows > 0) { while ($row = $data->fetch_assoc()) { ?>
                            type: 'Feature',
                            geometry: {
                                type: 'Point',
                                coordinates: [<?php echo str_replace(",", ".", $row["LONGITUD"]) ?>, <?php echo str_replace(",", ".", $row["LATITUD"]) ?>]
                            },
                            properties: {
                                title: '<?php echo $row["ROTULO"] ?>',
                                description: '<?php echo $row["PRECIO_GASOLEO_A"] ?>'
                            }
                }, <?php } } ?> 
            {
                type: 'Feature',
                geometry: {
                    type: 'Point',
                    coordinates: [-122.414, 37.776]
                },
                properties: {
                    title: 'Mapbox',
                    description: 'San Francisco, California'
                }
            }
            ]
        };

        geojson.features.forEach(function(marker) {

            // create a HTML element for each feature
            var el = document.createElement('div');
            el.className = 'marker';

            // make a marker for each feature and add to the map
            new mapboxgl.Marker(el)
                .setLngLat(marker.geometry.coordinates)
                .addTo(map);
        });
    </script>
</body>

</html>