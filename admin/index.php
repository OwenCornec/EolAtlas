
<html>
<head>
	<meta charset="utf-8">
	<title>INIT - Admin</title>
	<link rel="stylesheet" href="../eol/stylesheet.css">
</head>
	<body>
	
	<?php echo realpath('.htpasswd'); ?>
		<form method="post" action="../eol/fics_php/init.php" enctype="multipart/form-data">
			<label for="ficStations">Fichier avec les stations :</label><br/>
			<input type="file" name="ficStations" id="ficStations" /><br/>

			<label for="ficVitDir">Fichier vitesse/direction :</label><br/>
			<input type="file" name="ficVitDir" id="ficVitDir" /><br/>

			<input type="submit" name="submit" value="Envoyer" />
		</form>


	</body>
</html>