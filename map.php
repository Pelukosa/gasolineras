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
        var geojson = [{
                type: 'Feature',
                geometry: {
                    type: 'Point',
                    coordinates: [-77.031952, 38.913184]
                },
                properties: {
                    'marker-color': '#3bb2d0',
                    'marker-size': 'large',
                    'marker-symbol': 'rocket'
                }
            },
            {
                type: 'Feature',
                geometry: {
                    type: 'Point',
                    coordinates: [-122.413682, 37.775408]
                },
                properties: {
                    'marker-color': '#3bb2d0',
                    'marker-size': 'large',
                    'marker-symbol': 'rocket'
                }
            }
        ];

        var mapSimple = L.mapbox.map('map_simple')
            .setView([37.8, -96], 4)
            .addLayer(L.mapbox.styleLayer('mapbox://styles/mapbox/light-v10'));
        var myLayer = L.mapbox.featureLayer().setGeoJSON(geojson).addTo(mapSimple);
        mapSimple.scrollWheelZoom.disable();
    </script>




    <!--
    <script>
        mapboxgl.accessToken = 'pk.eyJ1IjoicGVsdWtvc2EiLCJhIjoiY2s0ZWt3bmw5MDQ3MzNkbWh4OHl6Ymk0YyJ9.SjBddTn9NPdfGPGCqfv4xQ';
        var geojson = {
            'type': 'FeatureCollection',
            'features': [
                <?php if ($data->num_rows > 0) {
                    while ($row = $data->fetch_assoc()) {

                ?> {
                            'type': 'Feature',
                            'properties': {
                                'message': '<?php echo $row["ROTULO"] . ' - ' . $row["PRECIO_GASOLEO_A"] ?>',
                                'iconSize': [10, 10]
                            },
                            'geometry': {
                                'type': 'Point',
                                'coordinates': [<?php echo str_replace(",", ".", $row["LONGITUD"]) ?>, <?php echo str_replace(",", ".", $row["LATITUD"]) ?>]
                            }
                        },
                <?php }
                } ?> {

                }

            ]
        };

        var map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v11',
            center: [-4.730988, 41.637726],
            zoom: 12
        });

        // add markers to map
        geojson.features.forEach(function(marker) {
            // create a DOM element for the marker
            var el = document.createElement('div');
            el.className = 'marker';
            el.style.backgroundImage =
                'url(https://placekitten.com/g/' +
                marker.properties.iconSize.join('/') +
                '/)';
            el.style.width = marker.properties.iconSize[0] + 'px';
            el.style.height = marker.properties.iconSize[1] + 'px';

            el.addEventListener('click', function() {
                window.alert(marker.properties.message);
            });

            // add marker to map
            new mapboxgl.Marker(el)
                .setLngLat(marker.geometry.coordinates)
                .addTo(map);
        });
    </script>
    -->
</body>

</html>