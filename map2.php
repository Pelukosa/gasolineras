<?php
include 'conn/conn.php';
$sql = "SELECT LATITUD, LONGITUD, ROTULO, PRECIO_GASOLEO_A FROM gasolineras WHERE PROVINCIA IN ('VALLADOLID')";
$data = $conn->query($sql);
//print("<pre>".print_r($data,true)."</pre>");

$sql = "SELECT LATITUD, LONGITUD, ROTULO, PRECIO_GASOLEO_A FROM gasolineras WHERE PROVINCIA IN ('VALLADOLID') AND PRECIO_GASOLEO_A > 0 ORDER BY PRECIO_GASOLEO_A ASC LIMIT 1";
$lowPrice = $conn->query($sql);
$conn->close();

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Display a popup on click</title>
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no" />
    <script src="https://api.mapbox.com/mapbox-gl-js/v1.11.0/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v1.11.0/mapbox-gl.css" rel="stylesheet" />
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
    </style>
</head>

<body>
    <style>
        .mapboxgl-popup {
            max-width: 400px;
            font: 12px/20px 'Helvetica Neue', Arial, Helvetica, sans-serif;
        }
    </style>
    <div id="map"></div>
    <script>
        mapboxgl.accessToken = 'pk.eyJ1IjoicGVsdWtvc2EiLCJhIjoiY2tiZTY1OGljMGNyZjJyb28yb3VkbDFwNyJ9.9PjhHKe0aAzScu1_ELyobQ';
        var map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v11',
            center: [-77.04, 38.907],
            zoom: 11.15
        });

        map.on('load', function() {
            map.addSource('places', {
                'type': 'geojson',
                'data': {
                    'type': 'FeatureCollection',
                    'features': [
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
                                        description: '<p>Diesel: <strong><?php echo $row["PRECIO_GASOLEO_A"] ?></strong></p><p>Gasolina 95: <strong><?php echo $row["PRECIO_GASOLINA_95"] ?></strong></p>'
                                    }
                                },
                        <?php }
                        } ?>
                    ]
                }
            });
            // Add a layer showing the places.
            map.addLayer({
                'id': 'places',
                'type': 'symbol',
                'source': 'places',
                'layout': {
                    'icon-image': '{icon}-15',
                    'icon-allow-overlap': true
                }
            });

            // When a click event occurs on a feature in the places layer, open a popup at the
            // location of the feature, with description HTML from its properties.
            map.on('click', 'places', function(e) {
                var coordinates = e.features[0].geometry.coordinates.slice();
                var description = e.features[0].properties.description;

                // Ensure that if the map is zoomed out such that multiple
                // copies of the feature are visible, the popup appears
                // over the copy being pointed to.
                while (Math.abs(e.lngLat.lng - coordinates[0]) > 180) {
                    coordinates[0] += e.lngLat.lng > coordinates[0] ? 360 : -360;
                }

                new mapboxgl.Popup()
                    .setLngLat(coordinates)
                    .setHTML(description)
                    .addTo(map);
            });

            // Change the cursor to a pointer when the mouse is over the places layer.
            map.on('mouseenter', 'places', function() {
                map.getCanvas().style.cursor = 'pointer';
            });

            // Change it back to a pointer when it leaves.
            map.on('mouseleave', 'places', function() {
                map.getCanvas().style.cursor = '';
            });
        });
    </script>

</body>

</html>