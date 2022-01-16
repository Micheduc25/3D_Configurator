<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/carte.css">
    <link rel="stylesheet" href="./css/utils.css">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/apple-touch-icon.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon-16x16.png" />
    <link rel="manifest" href="assets/site.webmanifest" />

    <!-- mapbox cdn -->
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.css' rel='stylesheet' />

    <!--mapbox geocoder cdn -->
    <!-- Load the `mapbox-gl-geocoder` plugin. -->
    <link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.2/mapbox-gl-geocoder.css" type="text/css">

    <title>Carte WOODZIP</title>


</head>

<body>

    <div class="wz_map">
        <div class="image-to-snap" style="width: 100%; height: 100vh;">
            <div id='map' style='width: 100%; height: 100%;'></div>
        </div>
        <div class="error-message"></div>

        <!-- l'icone de chargement ici --->
        <div class="barrage show">
            <div class="lds-dual-ring"></div>
        </div>

        <button id="save-button" style="display:none;" class="save-button">Sauvegarder</button>

    </div>

    <script src='https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.js'></script>
    <script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.2/mapbox-gl-geocoder.min.js"></script>
    <script src="./js/html2canvas.min.js">
    </script>

    <script type="text/javascript" src="./js/carte.js"></script>
</body>

</html>