<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/carte.css">

    <!-- mapbox cdn -->
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.css' rel='stylesheet' />
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.js'></script>

    <!--mapbox geocoder cdn -->
    <!-- Load the `mapbox-gl-geocoder` plugin. -->
    <script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.2/mapbox-gl-geocoder.min.js"></script>
    <link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.2/mapbox-gl-geocoder.css" type="text/css">

    <title>Document</title>

    
</head>
<body>
    <div id='map' style='width: 100%; height: 100%;'></div>

    <script>
        mapboxgl.accessToken = 'pk.eyJ1IjoibWljaGVkdWMyNSIsImEiOiJja3h4bGhwcGUxczQ0Mm5vemowOTRsNXdrIn0.CPAQumBkBT2qIEjbceHeSA';
        const map = new mapboxgl.Map({
        container: 'map', // container ID
        style: 'mapbox://styles/mapbox/streets-v11', // style URL
        center: [12.550343, 55.665957], // starting position
        zoom: 9 // starting zoom
        });
        
        // Add zoom and rotation controls to the map.
        // map.addControl(new mapboxgl.NavigationControl());

        // Create a default Marker, colored black, rotated 45 degrees.
        const marker2 = new mapboxgl.Marker({ color: 'black', rotation: 45 })
        .setLngLat([12.65147, 55.608166])
        .addTo(map);


        // Add the control to the map. 
        //geocoder to search for places
        map.addControl(
        new MapboxGeocoder({
            accessToken: mapboxgl.accessToken,
            mapboxgl: mapboxgl
        })
        );
    </script>
</body>
</html>