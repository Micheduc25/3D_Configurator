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



    <title>DEMANDER UN DEVIS WOODZIP</title>
</head>

<body>

    <div class="message-toast"></div>
    <!-- Loader div -->
    <div class="barrage">
        <div class="lds-dual-ring"></div>
    </div>

    <script src="./js/utils.js"></script>

    <?php
    require_once './config.php';

    $formErrors = array("prenom" => "", "nom" => "", "telephone" => "", "email" => "");


    //takes a base64 encoded image and saves it to the server and returns the url to the image
    function saveImageToServer($img, $isMapImage = false)
    {
        $sep = DIRECTORY_SEPARATOR;

        $_UPLOAD_DIR = "assets" . $sep . "saved_images" . $sep;

        $img = str_replace(!$isMapImage ? 'data:image/png;base64,' : 'data:image/jpeg;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $id = uniqid();
        $file = $_UPLOAD_DIR . $id . ($isMapImage ? '.jpeg' : '.png');

        if (file_exists($file)) return;

        $myFile =  fopen($file, 'w') or die("Can't create file'");
        $success = fwrite($myFile, $data);

        fclose($myFile);

        if ($success) {
            return SERVER_URL . '/assets/saved_images/' . $id . ($isMapImage ? '.jpeg' : '.png');
        } else return;
    }


    //anything which is not in the fixed keys is an option
    $fixed_keys  = array(
        'prenom',
        'nom', 'email', 'addresse', 'telephone', 'code_postal', 'pays',
        'question', 'bardages', 'enduits', 'totalCostWithTVA', 'totalCostWithoutTVA',
        'mapImage', 'frontView', 'backView', 'location'
    );

    $form_keys = array(
        'prenom',
        'nom', 'email', 'addresse', 'telephone', 'code_postal', 'pays', 'question', 'authorisation', 'submit'
    );

    $model_config_keys = array(
        'bardages', 'enduits', 'totalCostWithoutTVA',
        'totalCostWithTVA', 'mapImage', 'frontView', 'backView', 'location'
    );


    $cost_keys = array('totalCostWithTVA', 'totalCostWithoutTVA');

    $insertableFields = array(
        'prenom',
        'nom', 'email', 'addresse', 'telephone', 'code_postal', 'pays', 'question', 'bardages', 'enduits', 'options'
    );

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {

        //we check if given inputs have valid values
        $prenom = $_REQUEST['prenom'];
        $nom = $_REQUEST['nom'];
        $email = $_REQUEST['email'];
        $telephone = $_REQUEST['telephone'];

        $isValidationSuccess = true;
        if (!isset($prenom) || strlen($prenom) === 0) {
            $formErrors['prenom'] = "Ce champ ne doit pas etre vide";
            if ($isValidationSuccess) $isValidationSuccess = false;
        } else if (!preg_match("/^[a-zA-Z-' ]*$/", $prenom)) {
            $formErrors['prenom'] = "Seule les lettres et les espaces sont acceptés pour ce champ";
            if ($isValidationSuccess) $isValidationSuccess = false;
        }

        if (!isset($nom) || strlen($nom) === 0) {
            $formErrors['nom'] = "Ce champ ne doit pas etre vide";
            if ($isValidationSuccess) $isValidationSuccess = false;
        } else if (!preg_match("/^[a-zA-Z-' ]*$/", $nom)) {
            $formErrors['nom'] = "Seule les lettres et les espaces sont acceptés pour ce champ";
            if ($isValidationSuccess) $isValidationSuccess = false;
        }


        if (!isset($email) || strlen($email) === 0) {
            $formErrors['email'] = "Ce champs ne doit pas etre vide";
            if ($isValidationSuccess) $isValidationSuccess = false;
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $formErrors['email'] = "L'adresse e-mail est invalide";
            if ($isValidationSuccess) $isValidationSuccess = false;
        }

        if (!isset($telephone) || strlen($telephone) === 0) {
            $formErrors['telephone'] = "Ce champ ne doit pas etre vide";
            if ($isValidationSuccess) $isValidationSuccess = false;
        }


        if ($isValidationSuccess) {

            echo "<script type=\"text/javascript\">toggleBarrage(true);</script>";

            $conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

            // Check connection
            if ($conn === false) {
                die("ERREUR: Impossible d'établir une connexion avec la base de données. "
                    . mysqli_connect_error());
            }


            // collect values from form
            $prenom = $_REQUEST['prenom'];
            $nom = $_REQUEST['nom'];
            $email = $_REQUEST['email'];
            $addresse = $_REQUEST['addresse'];
            $code_postal = $_REQUEST['code_postal'];
            $pays = $_REQUEST['pays'];
            $question = $_REQUEST['question'];
            $authorisation = $_REQUEST['authorisation']??null;
            $telephone = $_REQUEST['telephone'];

            $bardages = $_REQUEST['bardages'];
            $enduits = $_REQUEST['enduits'];

            $totalCost = $_REQUEST['totalCostWithoutTVA'];
            $totalCostTVA = $_REQUEST['totalCostWithTVA'];

            $mapImage =  $_REQUEST['mapImage']??null;
            $frontView = $_REQUEST['frontView']??null;
            $backView = $_REQUEST['backView']??null;
            $location = $_REQUEST['location']??null;




            if (isset($mapImage)) {
                $mapImage = saveImageToServer($mapImage, true);
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


            //if authorization is on, we insert the data into the devis table

            if ($authorisation === "on") {

                $fieldsToInsert = array();
                $fieldValues = array();
                foreach ($insertableFields as $field) {
                    if (!empty($_POST[$field])) {
                        array_push($fieldsToInsert, $field);
                        $val = "'".strip_tags($_POST[$field])."'" ;
                        array_push($fieldValues, $val);
                    }
                }

                array_push($fieldsToInsert, "options");
                array_push($fieldValues, "'$options'");

                $jointFields = implode(",", $fieldsToInsert);
                $jointValues = implode(",", $fieldValues);
                $sql = "INSERT INTO devis($jointFields) VALUES ($jointValues)";



                if (mysqli_query($conn, $sql)) {

                    $to = MAIL_TO;
                    $subject = "Devis WOODZIP";

                    $message = '<html><body>';
                    $message .= '<h1 style="margin-bottom:35px;">Devis Woodzip</h1>';


                    if (isset($frontView) || isset($backView)) {
                        $message .= '<h3 style="margin-bottom:25px;">Appercu de la maison</h3>';
                    }

                    if (isset($frontView)) {
                        $message .= "<div style=\"margin-bottom:25px; display:flex; width:70%; \"> <img src=\"$frontView\" style=\"width:100%;\" /> </div>";
                    }
                    if (isset($backView)) {

                        $message .= "<div style=\"margin-bottom:25px; display:flex; width:70%;\"> <img src=\"$backView\" style=\"width:100%;\" /> </div>";
                    }
                    $message .= '<table rules="all" style="border-color: #666; margin-bottom:35px;" cellpadding="10">';
                    $message .= "<tr style='background: #eee;'><td><strong>Nom et prenoms</strong> </td><td>" . strip_tags($nom) . " " . strip_tags($prenom) . "</td></tr>";
                    $message .= "<tr><td><strong>Email:</strong> </td><td>" . strip_tags($email) . "</td></tr>";

                    if (!empty($addresse)) {
                        $message .= "<tr><td><strong>Adresse:</strong> </td><td>" . strip_tags($addresse) . "</td></tr>";
                    }

                    $message .= "<tr><td><strong>Numéro de téléphone:</strong> </td><td>" . strval(strip_tags($telephone)) . "</td></tr>";

                    if (!empty($code_postal)) {
                        $message .= "<tr><td><strong>Code postal:</strong> </td><td>" . strip_tags($code_postal) . "</td></tr>";
                    }

                    if (!empty($pays)) {
                        $message .= "<tr><td><strong>Pays:</strong> </td><td>" . strip_tags($pays) . "</td></tr>";
                    }

                    if (!empty($question)) {
                        $message .= "<tr><td><strong>Question/Remarques:</strong> </td><td>" . strip_tags($question) . "</td></tr>";
                    }


                    $message .= "<tr><td><strong>Revêtemement principal:</strong> </td><td> Enduit " . $enduits . "</td></tr>";
                    $message .= "<tr><td><strong>Revêtemement secondaire:</strong> </td><td> Bardage " . $bardages . "</td></tr>";

                    foreach ($_REQUEST as $key => $value) {
                        if (array_search($key, $fixed_keys) == false && $key!=='authorisation'&& $key!=='submit' && $key!=='prenom') {
                            $keyname = str_replace('_', ' ', $key);
                            $message .= "<tr><td><strong>" . $keyname . ":</strong> </td><td>" . $value . "</td></tr>";
                        }
                    }

                    $message .= "<tr><td><strong>Coût total (T.V.A excl):</strong> </td><td><strong>€ " .  strval($totalCost) . "</strong></td></tr>";
                    $message .= "<tr><td><strong>Coût total (T.V.A incl):</strong> </td><td><strong>€ " . strval($totalCostTVA) . "</strong></td></tr>";

                    $message .= "</table>";
                    if (isset($mapImage)) {

                        $message .= "<h3 style=\"margin-bottom:25px;\"><strong>Emplacement de la maison</strong></h3>";
                        $message .= "<div style=\"margin-bottom:25px; display:flex; width:70%;\"><img src=\"$mapImage\" style=\"width:100%;\" /></div>";
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

                    echo $message;

                    $retval = mail($to, $subject, $message, $header);


                    if ($retval == true) {
                        echo "<script>
                         toggleToast(true, 'Votre demande a été soumise avec succès ');

                         setTimeout(()=>{
                            toggleToast(false);
                            toggleBarrage(false);
                            window.location = 'https://www.woodzip.fr/';

                         },2500);
                    </script>";
                    } else {
                        echo "<script type=\"text/javascript\">
                             toggleToast(true, 'Une erreur s\'est produite lors de l'envoi du mail',true);
    
                             setTimeout(()=>{
                                toggleToast(false);
                                toggleBarrage(false);}, 4000);
                        </script>";
                    }
                    // header("Location: https://www.woodzip.com/");
                    // exit();
                } else {
                    $err = strval(mysqli_error($conn));
                    echo $err;
                    echo "<script>
                         toggleToast(true, 'Une erreur s\'est produite lors du traitement de votre demande ',true);
                         setTimeout(()=>{
                            toggleToast(false);
                            toggleBarrage(false);


                         },4000);
                    </script>";
                }
            } else {
                echo "<script>
                     toggleToast(true, 'Vous n\'avez pas authorisé cet action',true);

                     setTimeout(()=>{
                        toggleToast(false);
                        toggleBarrage(false);


                     },3000);
                </script>";
            }

            // Close connection
            mysqli_close($conn);
        }
    }
    ?>

    <header class="devis-header">

        <a href="./index.html">
            <img src="assets/logo.jpg" alt="woozip logo">
        </a>

    </header>

    <main class="main-content">

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
                <input type="text" id="prenom" name="prenom" placeholder="Entrez votre prénom" value="<?= $_POST['prenom'] ?? '' ?>">
                <?php if (strlen($formErrors["prenom"]) > 0) echo "<span class='input-error'>" . $formErrors['prenom'] . "</span>"; ?>
            </p>
            <p>
                <label class="required" for="nom">Nom de famille</label>
                <input type="text" id="nom" name="nom" placeholder="Entrez votre nom" value="<?= $_POST['nom'] ?? '' ?>">
                <?php if (strlen($formErrors["nom"]) > 0) echo "<span class='input-error'>" . $formErrors['nom'] . "</span>"; ?>
            </p>
            <p>
                <label class="required" for="email">Email</label>
                <input type="text" id="email" name="email" placeholder="Entrez votre Addresse Electronique" value="<?= $_POST['email'] ?? '' ?>">
                <?php if (strlen($formErrors["email"]) > 0) echo "<span class='input-error'>" . $formErrors['email'] . "</span>"; ?>

            </p>
            <p>
                <label for="addresse">Adresse</label>
                <input type="text" id="addresse" name="addresse" placeholder="Rue, numéro et lieu" value="<?= $_POST['addresse'] ?? '' ?>">
            </p>
            <p>
                <label class="required" for="telephone">Téléphone</label>
                <input type="tel" id="telephone" name="telephone" placeholder="Numéro de téléphone" value="<?= $_POST['telephone'] ?? '' ?>">
                <?php if (strlen($formErrors["telephone"]) > 0) echo "<span class='input-error'>" . $formErrors['telephone'] . "</span>"; ?>

            </p>
            <p>
                <label for="code_postal">Code postal</label>
                <input type="text" id="code_postal" name="code_postal" placeholder="Code Postal" value="<?= $_POST['code_postal'] ?? '' ?>">
            </p>
            <p>
                <label for="pays">Pays</label>
                <select name="pays" id="pays" value="<?= $_POST['pays'] ?? '' ?>">
                    <option value=""></option>
                    <option value="france">France</option>
                    <option value="canada">Canada</option>
                    <option value="Suisse">Suisse</option>
                </select>
            </p>

            <p>
                <label for="question">Question/remarques?</label>
                <textarea type="text" id="question" name="question" cols="30" rows="10" value="<?= $_POST['question'] ?? '' ?>"></textarea>
            </p>

            <p>
                <label for="authorisation" class="authorisation">
                    <input type="checkbox" title="Authorisation de stockage des données" name="authorisation" id="authorisation">
                    Par la présente, j'autorise le stockage de mes données
                </label>
            </p>

            <p>
                <input id="submit" title="Soumettre le formulaire" type="submit" name="submit" value="Soumettre" <?= isset($_POST['submit'])?"disabled":"" ?> >
            </p>

            <?php


            foreach ($_POST as $key => $value) {

                if (
                    (array_search($key, $fixed_keys) == false) ||
                    (array_search($key, $model_config_keys) !== false && isset($value))
                ) {
                    echo '<input hidden type="text" name="' . "$key" . '" value="' . strval($value) . '"/>';
                }
            }

            ?>

        </form>


    </main>
            
</body>

</html>