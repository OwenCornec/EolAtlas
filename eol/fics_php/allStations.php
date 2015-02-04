<?php
	function __autoload($class_name) {
    	include $class_name . '.php';
	}

	/**
	DEBUT MAIN
	**/

	$bdd = new BD();
	$functions = new Functions($bdd);

	try {


		$cpt=0;
		
		$nbStations = $functions->nbTotalStations();

		$bdd->query('SELECT nom, latitude, longitude, altitude FROM station');

		//construction du JSON
		header('Content-type: application/json');
		echo '{"stations":[';
		while($stations = $bdd->fetch2(PDO::FETCH_ASSOC)){
			$cpt++;
			if($cpt==$nbStations){
				echo json_encode($stations);
			}else{
				echo json_encode($stations).',';
			}
		}
		echo ']}';


	} catch( Exception $e ){
		echo 'Erreur de requète : ', $e->getMessage();
	}
	/**
	FIN MAIN
	**/
?>