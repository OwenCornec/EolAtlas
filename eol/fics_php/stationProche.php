<?php

	function __autoload($class_name) {
    	include $class_name . '.php';
    	//echo "class_name $class_name <BR/>";
	}

	

	$bdd = new BD();
	$functions = new Functions($bdd);

	//init avec la station id 248
	$latitude=0;
	$longitude=0;
	if(isset($_GET['latitude']) && isset($_GET['longitude'])){
		$latitude = $_GET['latitude'];
		$longitude = $_GET['longitude'];
	}

	try {


		$id = $functions->getStationProche($latitude , $longitude);
		//echo "station proche id : $id !!!";
		$bdd->prepare('SELECT * FROM station WHERE idStation=?'); // compte le nombre de stations
		$bdd->execute(array($id));


		header('Content-type: application/json');
		while($station = $bdd->fetch2(PDO::FETCH_ASSOC)){
			echo json_encode($station);
		}


	} catch( Exception $e ){
		echo 'Erreur de requète : ', $e->getMessage();
	}

?>