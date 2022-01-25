<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/apple-touch-icon.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon-16x16.png" />
    <link rel="manifest" href="assets/site.webmanifest" />
    <link rel="stylesheet" href="css/devis.css">
    <link rel="stylesheet" href="css/utils.css">



    <title>WOODZIP Devis</title>
</head>

<body>
    
    <div class="message-toast"></div>

    <script src="./js/utils.js"></script>

    <?php
    require_once './config.php';
    define('SERVER_URL', 'https://3d.woodzip.com');


    //takes a base64 encoded image and saves it to the server and returns the url to the image
    function saveImageToServer($img)
    {
        $sep = DIRECTORY_SEPARATOR;

        $_UPLOAD_DIR = "assets" . $sep . "saved_images" . $sep;

        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $id = uniqid();
        $file = $_UPLOAD_DIR . $id . '.png';

        if (file_exists($file)) return;

        $myFile =  fopen($file, 'w') or die("Can't create file'");
        $success = fwrite($myFile, $data);

        fclose($myFile);

        if ($success) {
            return SERVER_URL . '/assets/saved_images/' . $id . '.png';
        } else return;
    }


    //anything which is not in the fixed keys is an option
    $fixed_keys  = array(
        'prenom',
        'nom', 'addresse_elec', 'addresse', 'telephone', 'code_postal', 'pays',
        'question', 'bardages', 'enduits', 'totalCostWithTVA', 'totalCostWithoutTVA',
        'mapImage', 'frontView', 'backView', 'location'
    );

    $form_keys = array(
        'prenom',
        'nom', 'addresse_elec', 'addresse', 'telephone', 'code_postal', 'pays', 'question', 'authorisation', 'submit'
    );

    $cost_keys = array('totalCostWithTVA', 'totalCostWithoutTVA');

    if (isset($_POST['submit'])) {

        $conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

        // Check connection
        if ($conn === false) {
            die("ERROR: Could not connect. "
                . mysqli_connect_error());
        }


        // collect values from form
        $prenom = $_REQUEST['prenom'];
        $nom = $_REQUEST['nom'];
        $addresse_elec = $_REQUEST['addresse_elec'];
        $addresse = $_REQUEST['addresse'];
        $code_postal = $_REQUEST['code_postal'];
        $pays = $_REQUEST['pays'];
        $question = $_REQUEST['question'];
        $authorisation = $_REQUEST['authorisation'];
        $telephone = $_REQUEST['telephone'];

        $bardages = $_REQUEST['bardages'];
        $enduits = $_REQUEST['enduits'];

        $totalCost = $_REQUEST['totalCostWithoutTVA'];
        $totalCostTVA = $_REQUEST['totalCostWithTVA'];

        $mapImage =  $_REQUEST['mapImage'];
        $frontView = $_REQUEST['frontView'];
        $backView = $_REQUEST['backView'];
        $location = $_REQUEST['location'];

        if (isset($mapImage)) {
            $mapImage = saveImageToServer($mapImage);
        }
        if (isset($frontView)) {
            $frontView = saveImageToServer($frontView);
        }
        if (isset($backView)) {
            $backView = saveImageToServer($backView);
        }



        $options = "";

        foreach ($_REQUEST as $key => $value) {
            if (
                array_search($key, $fixed_keys) == false && $value == "Oui"
            ) {
                $options .= str_replace('_', ' ', $key) . ',';
            }
        }

        $options = substr($options, 0, strlen($options) - 1);


        //if authorization is true, we insert the data into the devis table

        if ($authorisation === "on") {


            $sql = "INSERT INTO devis(prenom,
                nom,addresse_elec,addresse,telephone,code_postal,pays,question,bardages,enduits,options) VALUES ('$prenom',
                    '$nom','$addresse_elec','$addresse','$telephone','$code_postal','$pays','$question','$bardages','$enduits','$options')";


            if (mysqli_query($conn, $sql)) {

                $to = MAIL_TO;
                $subject = "Devis WOODZIP";

                $message = '<html><body>';
                $message .= '<h1 style="margin-bottom:35px;">Devis Woodzip</h1>';


                if (isset($frontView) || isset($backView)) {
                    $message .= '<h3 style="margin-bottom:25px;">Appercu de la maison</h3>';
                }

                if (isset($frontView)) {
                    $message .= "<div style=\"margin-bottom:25px;\"> <img src=\"$frontView\" /> </div>";
                }
                if (isset($backView)) {

                    $message .= "<div style=\"margin-bottom:25px;\"> <img src=\"$backView\" /> </div>";
                }
                $message .= '<table rules="all" style="border-color: #666; margin-bottom:35px;" cellpadding="10">';
                $message .= "<tr style='background: #eee;'><td><strong>Nom et prenoms</strong> </td><td>" . strip_tags($nom) . " " . strip_tags($prenom) . "</td></tr>";
                $message .= "<tr><td><strong>Email:</strong> </td><td>" . strip_tags($addresse_elec) . "</td></tr>";
                $message .= "<tr><td><strong>Adresse:</strong> </td><td>" . strip_tags($addresse) . "</td></tr>";
                $message .= "<tr><td><strong>Numéro de téléphone:</strong> </td><td>" . strval(strip_tags($telephone)) . "</td></tr>";
                $message .= "<tr><td><strong>Code postal:</strong> </td><td>" . strip_tags($code_postal) . "</td></tr>";
                $message .= "<tr><td><strong>Pays:</strong> </td><td>" . strip_tags($pays) . "</td></tr>";
                $message .= "<tr><td><strong>Question/Remarques:</strong> </td><td>" . htmlentities($question) . "</td></tr>";
                $message .= "<tr><td><strong>Revêtemement principal:</strong> </td><td> Enduit " . $enduits . "</td></tr>";
                $message .= "<tr><td><strong>Revêtemement secondaire:</strong> </td><td> Bardage " . $bardages . "</td></tr>";

                foreach ($_REQUEST as $key => $value) {
                    if (array_search($key, $fixed_keys) == false) {
                        $keyname = str_replace('_', ' ', $key);
                        $message .= "<tr><td><strong>" . $keyname . ":</strong> </td><td>" . $value . "</td></tr>";
                    }
                }

                $message .= "<tr><td><strong>Coût total (T.V.A excl):</strong> </td><td><strong>€ " .  strval($totalCost) . "</strong></td></tr>";
                $message .= "<tr><td><strong>Coût total (T.V.A incl):</strong> </td><td><strong>€ " . strval($totalCostTVA) . "</strong></td></tr>";

                $message .= "</table>";
                if (isset($mapImage)) {

                    $message .= "<h3 style=\"margin-bottom:25px;\"><strong>Emplacement de la maison</strong></h3>";
                    $message .= "<div style=\"margin-bottom:25px;\"><img src=\"$mapImage\" /></div>";
                    $message .= "<div style=\"margin-bottom:25px;\"><strong>$location</strong></div>";
                }
                $message .= "</body></html>";


                $header = "From:contact@woodzip.com \r\n";
                $header .= "Cc:contact@woodzip.com \r\n";
                $header .= "MIME-Version: 1.0\r\n";
                $header .= "Content-Type: text/html;\r\n";

                $header .= "Organization: WOODZIP\r\n";
                $header .= "X-Priority: 3\r\n";
                $header .= "X-Mailer: PHP" . phpversion() . "\r\n";

                $retval = mail($to, $subject, $message, $header);


                echo `<script>toggleBarrage(false)</script>`;
                if ($retval == true) {
                    echo "<script>
                         toggleToast(true, 'Votre demande a été soumise avec succès ');

                         setTimeout(()=>{
                            toggleToast(false);
                            window.location = 'https://www.woodzip.com/';

                         },2500);
                    </script>";
                } else {
                   echo "<script>
                             toggleToast(true, 'Une erreur s\'est produite lors du traitement de votre demande');
    
                             setTimeout(()=>{
                                toggleToast(false);
    
                             },4000);
                        </script>
                    ";
                }
                // header("Location: https://www.woodzip.com/");
                // exit();
            } else {

                echo "<script>
                         toggleToast(true, 'Une erreur s\'est produite lors du traitement de votre demande ',true);

                         setTimeout(()=>{
                            toggleToast(false);

                         },4000);
                    </script>";
            }
        } else {
            echo "<script>
                     toggleToast(true, 'Vous n\'avez pas authorisé cet action',true);

                     setTimeout(()=>{
                        toggleToast(false);

                     },3000);
                </script>";
        }


        // Close connection
        mysqli_close($conn);
    }
    ?>

    <header class="devis-header">

        <a href="./index.html">
            <img src="assets/logo.jpg" alt="woozip logo">
        </a>

    </header>

    <main class="main-content">
        <!-- Loader div -->
        <div class="barrage">
            <div class="lds-dual-ring"></div>
        </div>

        <form class="devis-form" method="post" action="<?= htmlentities($_SERVER['PHP_SELF']); ?>">



            <h1>Demander un devis</h1>

            <h2 style="margin-bottom:20px;">
                Vous avez choisi le configuration suivante pour votre maison:
            </h2>

            <?php
            foreach ($_POST as $key => $value) {
                if (
                    isset($value) &&
                    array_search($key, $form_keys) === false &&
                    array_search($key, $cost_keys) === false &&
                    $key !== 'frontView' && $key !== 'backView' &&
                    $key !== 'mapImage' && $key !== 'location'

                ) {
                    echo '<p class="config-vals">' . str_replace('_', ' ', $key) . ': <strong>' . $value . '</strong> </p>';
                }
            }
            ?>


            <p>
                <label class="required" for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" placeholder="Entrez votre prénom" required>
            </p>
            <p>
                <label class="required" for="nom">Nom de famille</label>
                <input type="text" id="nom" name="nom" placeholder="Entrez votre nom" required>
            </p>
            <p>
                <label class="required" for="addresse_elec">Email</label>
                <input type="text" id="addresse_elec" name="addresse_elec" placeholder="Entrez votre Addresse Electronique" required>
            </p>
            <p>
                <label class="required" for="addresse">Adresse</label>
                <input type="text" id="addresse" name="addresse" placeholder="Rue, numéro et lieu" required>
            </p>
            <p>
                <label class="required" for="telephone">Téléphone</label>
                <input type="tel" id="telephone" name="telephone" placeholder="Numéro de téléphone" required>
            </p>
            <p>
                <label class="required" for="code_postal">Code postal</label>
                <input type="text" id="code_postal" name="code_postal" placeholder="" required>
            </p>
            <p>
                <label class="required" for="pays">Pays</label>
                <select name="pays" id="pays">
                    <option value="france">France</option>
                    <option value="canada">Canada</option>
                    <option value="Suisse">Suisse</option>
                </select>
            </p>

            <p>
                <label class="required" for="question">Question/remarques?</label>
                <textarea type="text" id="question" name="question" cols="30" rows="10" required></textarea>
            </p>

            <p>
                <label for="authorisation" class="authorisation">
                    <input type="checkbox" title="Authorisation de stockage des données" name="authorisation" id="authorisation" required>
                    Par la présente, j'autorise le stockage de mes données
                </label>
            </p>

            <p>
                <input id="submit" title="Soumettre le formulaire" type="submit" name="submit" value="Soumettre">
            </p>

            <?php


            foreach ($_POST as $key => $value) {

                if (isset($value)) {
                    echo '<input hidden type="text" name="' . "$key" . '" value="' . strval($value) . '"/>';
                }
            }

            ?>

        </form>


        
        <script type="text/javascript">
            const submit = document.getElementById('submit');
            submit.addEventListener('click', function() {
                toggleBarrage(true);
            });
        </script>



    </main>

</body>

</html>