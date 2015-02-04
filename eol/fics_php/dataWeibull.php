<?php
	function __autoload($class_name) {
    	include $class_name . '.php';
	}

	/**
	DEBUT MAIN
	**/

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
		$cpt=0;
		$nbInfosWeibull = $functions->nbInfosWeibull($id);


		$bdd->prepare('SELECT nom, latitude, longitude, altitude, facteurForme, facteurEchelle FROM station WHERE idStation=?');
		$bdd->execute(array($id));

		$nomStation = "";	
		$latitudeStation = 0;
		$longitudeStation = 0;
		$altitudeStation = 0;
		$facteurForme = 0;
		$facteurEchelle = 0;

		while($station = $bdd->fetch()){
			$nomStation = $station['nom'];
			$latitudeStation = $station['latitude'];
			$longitudeStation = $station['longitude'];
			$altitudeStation = $station['altitude'];
			$facteurForme = $station['facteurForme'];
			$facteurEchelle = $station['facteurEchelle'];
		}

		//echo "NOM : ".$nom." LATITUDE : ".$latitude." LONGITUDE : ".$longitude." <BR/>";

		$bdd->prepare('SELECT weibull FROM infosWeibull WHERE idStation=? ');
		$bdd->execute(array($id));

		//construction du JSON
		header('Content-type: application/json');
		echo '{"nom":"'.$nomStation.'","latitude":"'.$latitudeStation.'","longitude":"'.$longitudeStation.'","facteurForme":"'.$facteurForme.'","facteurEchelle":"'.$facteurEchelle.'","altitude":"'.$altitudeStation.'",';
		echo '"dataWeibull":[';
		while($infosWeibull = $bdd->fetch2(PDO::FETCH_ASSOC)){
			$cpt++;
			//echo "nbInfosWeibull : $nbInfosWeibull<BR/>";
			if($cpt==$nbInfosWeibull){
				echo json_encode($infosWeibull);
			}else{
				echo json_encode($infosWeibull).',';
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