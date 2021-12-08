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
    if (isset($_POST['submit'])) {


        /*putenv("WOODZIP_DB_USERNAME=3d_woodzip_com");
        putenv("WOODZIP_DB_PASSWORD=gMEb7sR8P1ZRoBJW");
        putenv("WOODZIP_DB=3d_woodzip_com"); */

        /*$newPass = $_ENV["WOODZIP_DB_PASSWORD"];
        
		$dbPassword = getenv("WOODZIP_DB_PASSWORD",true);
		$dbName = getenv("WOODZIP_DB");
		$dbUsername = getenv("WOODZIP_DB_USERNAME",true);
   
		$conn = mysqli_connect("localhost", "3d_woodzip_com", "gMEb7sR8P1ZRoBJW", "3d_woodzip_com");
   
        print_r("$dbPassword  $dbName $dbUsername are the values and new pass = $newPass");
        */

        $conn = mysqli_connect("localhost", "root", "", "devis_woodzip");

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

        $bardages = $_REQUEST['bardages'];
        $enduits = $_REQUEST['enduits'];
        $terrasse = $_REQUEST['terrasse'];
        $garage = $_REQUEST['garage'];

        $terrasse_val = $terrasse === 'true' ? 1 : 0;
        $garage_val = $garage === 'true' ? 1 : 0;

        //if authorization is true, we insert the data into the devis table

        if ($authorisation === "on") {

            $sql = "INSERT INTO devis(prenom,
                nom,addresse_elec,addresse,code_postal,pays,question,bardages,enduits,garage,terrasse) VALUES ('$prenom',
                    '$nom','$addresse_elec','$addresse','$code_postal','$pays','$question','$bardages','$enduits',$garage_val,$terrasse_val)";

            if (mysqli_query($conn, $sql)) {
                echo '<div class="alert-popup success"> Votre demande a été soumise avec succès </div>';
                header("Location: https://www.woodzip.com/");
                exit();
            } else {
                echo '<div class="alert-popup failed"> Une erreur s\'est produite lors du traitement de votre demande </div>';
            }
        } else {
            echo "You have not authorized this action";
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

            <p class="config-vals">
                Revêtement principal : <strong>
                     <?php
                     if(isset($_POST['enduits'])){
                      echo 'Enduit '.$_POST['enduits'] ;
                    }
                      
                      ?> 
                    </strong>
            </p>
            <p class="config-vals">
                Revêtemement secondaire : <strong> <?php if(isset($_POST['bardages'])) echo 'Bardage '.$_POST['bardages'] ?> </strong>
            </p>
            <p class="config-vals">
                Options choisies :  <strong>
                     <?php if( isset($_POST['terrasse']) && $_POST['terrasse']==='true') echo "Terrasse" ?> 
                     <?php if (isset($_POST['garage']) && $_POST['garage']==='true') echo ' et Garage' ?>
                </strong>
            </p>


            <p>
                <label class="required" for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" placeholder="Entrez votre prénom" required>
            </p>
            <p>
                <label class="required" for="nom">Nom de famille</label>
                <input type="text" id="nom" name="nom" placeholder="Entrez votre nom" required>
            </p>
            <p>
                <label class="required" for="addresse_elec">Addresse électronique</label>
                <input type="text" id="addresse_elec" name="addresse_elec" placeholder="Entrez votre Addresse Electronique" required>
            </p>
            <p>
                <label class="required" for="addresse">Addresse</label>
                <input type="text" id="addresse" name="addresse" placeholder="Rue, numéro et lieu" required>
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
                <label for="authorisation">
                    <input type="checkbox" title="Authorisation de stockage des données" name="authorisation" id="authorisation" required>
                    Par la présente, j'autorise le stockage de mes données
                </label>
            </p>

            <p>
                <input title="Soumettre le formulaire" type="submit" name="submit" value="Soumettre">
            </p>

            <?php
            $bardages = $_POST['bardages'];
            $enduits = $_POST['enduits'];
            $terrasse = $_POST['terrasse'];
            $garage = $_POST['garage'];

            if (isset($bardages)) {
                echo '<input hidden type="text" name="bardages" value="' . $bardages . '"/>';
            }
            if (isset($enduits)) {
                echo '<input hidden type="text" name="enduits" value="' . $enduits . '"/>';
            }
            if (isset($terrasse)) {
                echo '<input hidden type="text" name="terrasse" value="' . $terrasse . '"/>';
            }
            if (isset($garage)) {
                echo '<input hidden type="text" name="garage" value="' . $garage . '"/>';
            }


            ?>

        </form>

    </main>

</body>

</html>