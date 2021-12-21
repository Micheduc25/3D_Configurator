<!DOCTYPE html>
<html lang="en">

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
    <?php

    //anything which is not in the fixed keys is an option
    $fixed_keys  = array(
        'prenom',
        'nom', 'addresse_elec', 'addresse', 'telephone', 'code_postal', 'pays', 'question', 'bardages', 'enduits', 'totalCostWithTVA', 'totalCostWithoutTVA'
    );

    $form_keys = array('prenom',
    'nom', 'addresse_elec', 'addresse', 'telephone', 'code_postal', 'pays', 'question', 'authorisation','submit');

    $cost_keys = array('totalCostWithTVA','totalCostWithoutTVA');

    if (isset($_POST['submit'])) {


        /*putenv("WOODZIP_DB_USERNAME=3d_woodzip_com");
        putenv("WOODZIP_DB_PASSWORD=gMEb7sR8P1ZRoBJW");
        putenv("WOODZIP_DB=3d_woodzip_com"); */

        /*$newPass = $_ENV["WOODZIP_DB_PASSWORD"];
        
		$dbPassword = getenv("WOODZIP_DB_PASSWORD",true);
		$dbName = getenv("WOODZIP_DB");
		$dbUsername = getenv("WOODZIP_DB_USERNAME",true); */

        // $conn = mysqli_connect("localhost", "3d_woodzip_com", "gMEb7sR8P1ZRoBJW", "3d_woodzip_com");

        //print_r("$dbPassword  $dbName $dbUsername are the values and new pass = $newPass");


        $conn = mysqli_connect("localhost", "root", "", "3d_woodzip_com");

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
        // $terrasse = $_REQUEST['terrasse'];
        // $garage = $_REQUEST['garage'];
        $totalCost = $_REQUEST['totalCostWithoutTVA'];
        $totalCostTVA = $_REQUEST['totalCostWithTVA'];

        
        
        $options = "";

        foreach($_REQUEST as $key => $value){
            if(array_search($key, $fixed_keys)==false && $value=="Oui"){
                $options .= str_replace('_', ' ',$key). ',';
            }
        }

        $options = substr($options,0, strlen($options)-1);



        // $terrasse_val = $terrasse === 'true' ? 1 : 0;
        // $garage_val = $garage === 'true' ? 1 : 0;

        //if authorization is true, we insert the data into the devis table

        if ($authorisation === "on") {


            $sql = "INSERT INTO devis(prenom,
                nom,addresse_elec,addresse,telephone,code_postal,pays,question,bardages,enduits,options) VALUES ('$prenom',
                    '$nom','$addresse_elec','$addresse','$telephone','$code_postal','$pays','$question','$bardages','$enduits','$options')";

            echo "<script type='text/javascript'> console.log(`$sql`) </script>";
            

            if (mysqli_query($conn, $sql)) {


                echo '<div class="alert-popup success"> Votre demande a été soumise avec succès </div>';
               
                // $to = "lionel.chouraqui@meolia.fr";
                $to = "ndjockjunior@gmail.com";
                $subject = "Devis WOODZIP";

                $message = '<html><body>';
                $message .= '<h1 style="margin-bottom:25px;">Devis Woodzip</h1>';
                $message .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
                $message .= "<tr style='background: #eee;'><td><strong>Nom et prenoms</strong> </td><td>" . $nom . " " . $prenom . "</td></tr>";
                $message .= "<tr><td><strong>Adresse electronique:</strong> </td><td>" . $addresse_elec . "</td></tr>";
                $message .= "<tr><td><strong>Adresse:</strong> </td><td>" . $addresse . "</td></tr>";
                $message .= "<tr><td><strong>Numéro de téléphone:</strong> </td><td>" . strval($telephone) . "</td></tr>";
                $message .= "<tr><td><strong>Code postal:</strong> </td><td>" . $code_postal . "</td></tr>";
                $message .= "<tr><td><strong>Pays:</strong> </td><td>" . $pays . "</td></tr>";
                $message .= "<tr><td><strong>Question/Remarques:</strong> </td><td>" . htmlentities($question) . "</td></tr>";
                $message .= "<tr><td><strong>Revêtemement principal:</strong> </td><td> Enduit " . $enduits . "</td></tr>";
                $message .= "<tr><td><strong>Revêtemement secondaire:</strong> </td><td> Bardage " . $bardages . "</td></tr>";

                foreach ($_REQUEST as $key => $value) {
                    if (array_search($key, $fixed_keys) == false) {
                        $keyname = str_replace('_', ' ', $key);
                        $message .= "<tr><td><strong>" . $keyname . ":</strong> </td><td>" . $value . "</td></tr>";
                    }
                }
                // $message .= "<tr><td><strong>Terrasse:</strong> </td><td>" . $terrasse_text . "</td></tr>";
                // $message .= "<tr><td><strong>Garage:</strong> </td><td>" . $garage_text . "</td></tr>";
                $message .= "<tr><td><strong>Coût total (T.V.A excl):</strong> </td><td><strong>€ " .  strval($totalCost) . "</strong></td></tr>";
                $message .= "<tr><td><strong>Coût total (T.V.A incl):</strong> </td><td><strong>€ " . strval($totalCostTVA) . "</strong></td></tr>";

                $message .= "</table>";
                $message .= "</body></html>";


                $header = "From:ndjockjunior@gmail.com \r\n";
                $header .= "Cc:ndjockjunior@gmail.com \r\n";
                $header .= "MIME-Version: 1.0\r\n";
                $header .= "Content-type: text/html\r\n";

                $header .= "Organization: WOODZIP\r\n";
                $header .= "X-Priority: 3\r\n";
                $header .= "X-Mailer: PHP" . phpversion() . "\r\n";

                $retval = mail($to, $subject, $message, $header);

                echo "<script type='text/javascript'> console.log('sending mail....') </script>";


                if ($retval == true) {
                    echo "<script type='text/javascript'> console.log('mail correctly sent') </script>";

                    echo "Message envoyé avec succès...";
                } else {
                    echo "Le message n'a pas pu etre envoyé...";
                }
                // header("Location: https://www.woodzip.com/");
                // exit();
            } else {

                echo '<div class="alert-popup failed"> Une erreur s\'est produite lors du traitement de votre demande </div>';
            }
        } else {
            echo '<div class="alert-popup failed"> Vous n\'avez pas authorisé cet action </div>';
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

        <form class="devis-form" method="post" action="<?= htmlentities($_SERVER['PHP_SELF']); ?>">



            <h1>Demander un devis</h1>

            <h2 style="margin-bottom:20px;">
                Vous avez choisi le configuration suivante pour votre maison:
            </h2>

            <?php
            foreach ($_POST as $key => $value) {
                if (isset($value) && array_search($key,$form_keys)===false && array_search($key,$cost_keys)===false) {
                    echo '<p class="config-vals">' . str_replace('_', ' ', $key) . ': <strong>' . $value . '</strong> </p>';
                }
            }
            ?>
            <!-- <p class="config-vals">
                Revêtement principal : <strong>
                    <?php
                    if (isset($_POST['enduits'])) {
                        echo 'Enduit ' . $_POST['enduits'];
                    }

                    ?>
                </strong>
            </p>
            <p class="config-vals">
                Revêtemement secondaire : <strong> <?php if (isset($_POST['bardages'])) echo 'Bardage ' . $_POST['bardages'] ?> </strong>
            </p>
            <p class="config-vals">
                Options choisies : <strong>
                    <?php if (isset($_POST['terrasse']) && $_POST['terrasse'] === 'true') echo "Terrasse" ?>
                    <?php if (isset($_POST['garage']) && $_POST['garage'] === 'true') echo ' et Garage' ?>
                </strong>
            </p> -->


            <p>
                <label class="required" for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" placeholder="Entrez votre prénom" required>
            </p>
            <p>
                <label class="required" for="nom">Nom de famille</label>
                <input type="text" id="nom" name="nom" placeholder="Entrez votre nom" required>
            </p>
            <p>
                <label class="required" for="addresse_elec">Adresse électronique</label>
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
                <input title="Soumettre le formulaire" type="submit" name="submit" value="Soumettre">
            </p>

            <?php
            // $bardages = $_POST['bardages'];
            // $enduits = $_POST['enduits'];
            // $terrasse = $_POST['terrasse'];
            // $garage = $_POST['garage'];
            // $totalCost = $_POST['totalCost'];
            // $totalCostTVA = $_POST['totalCostTVA'];

            // if (isset($bardages)) {
            //     echo '<input hidden type="text" name="bardages" value="' . $bardages . '"/>';
            // }
            // if (isset($enduits)) {
            //     echo '<input hidden type="text" name="enduits" value="' . $enduits . '"/>';
            // }
            // if (isset($terrasse)) {
            //     echo '<input hidden type="text" name="terrasse" value="' . $terrasse . '"/>';
            // }
            // if (isset($garage)) {
            //     echo '<input hidden type="text" name="garage" value="' . $garage . '"/>';
            // }
            // if (isset($totalCost)) {
            //     echo '<input hidden type="text" name="totalCost" value="' . strval($totalCost) . '"/>';
            // }
            // if (isset($totalCostTVA)) {
            //     echo '<input hidden type="text" name="totalCostTVA" value="' . strval($totalCostTVA) . '"/>';
            // }

            foreach ($_POST as $key => $value) {

                if (isset($value)) {
                    echo '<input hidden type="text" name="' . "$key" . '" value="' . strval($value) . '"/>';
                }
            }

            ?>

        </form>

    </main>

</body>

</html>