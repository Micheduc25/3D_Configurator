<!DOCTYPE html>
<html>

<head>
	<title>Insert Page page</title>
</head>

<body>
	
		<?php
		$dbPassword = getenv("WOODZIP_DB_PASSWORD");
		$dbName = getenv("WOODZIP_DB");
		$dbUsername = getenv("WOODZIP_DB_USERNAME");
		$conn = mysqli_connect("localhost", $dbUsername, $dbPassword, $dbName);
		
		// Check connection
		if($conn === false){
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

        echo "authorization is $authorisation";
		
		//if authorization is true, we insert the data into the devis table
		
		$sql = "INSERT INTO devis(prenom,
        nom,addresse_elec,addresse,code_postal,pays,question) VALUES ('$prenom',
			'$nom','$addresse_elec','$addresse','$code_postal','$pays','$question')";
		
		if(mysqli_query($conn, $sql)){
			echo "<h3>data stored in a database successfully."
				. " Please browse your localhost php my admin"
				. " to view the updated data</h3>";

			echo nl2br("'$prenom',
			'$nome','$address_elec','$addresse','$code_postal','$pays','$question'");
		} else{
			echo "ERROR: Hush! Sorry $sql. "
				. mysqli_error($conn);
		}
		
		// Close connection
		mysqli_close($conn);
		?>
	
</body>

</html>
