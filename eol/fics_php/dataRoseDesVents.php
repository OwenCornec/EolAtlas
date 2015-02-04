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

		//echo "ID : ".$id."<BR/>";

		$cpt=0;
		$nbVent = $functions->nbInfosVentDistinctDirection($id);

		//echo "INFOS VENT : ".$nbVent."<BR/>";

		$bdd->prepare('SELECT nom, latitude, longitude, altitude FROM station WHERE idStation=?');
		$bdd->execute(array($id));

		$nomStation = "";
		$latitudeStation = 0;
		$longitudeStation = 0;
		$altitudeStation = 0;

		while($station = $bdd->fetch()){
			$nomStation = $station['nom'];
			$latitudeStation = $station['latitude'];
			$longitudeStation = $station['longitude'];
			$altitudeStation = $station['altitude'];

		}

		//echo "NOM : ".$nom." LATITUDE : ".$latitude." LONGITUDE : ".$longitude." <BR/>";

		$bdd->prepare('SELECT AVG(vitesse) as vitesseMoy, COUNT(*)/(SELECT COUNT(*) from vent where idStation=?)*100 as pourcent ,direction from vent where idStation=? group by direction');
		$bdd->execute(array($id,$id));

/*
		while($vent = $bdd->fetch()){
			echo "VENT :".$vent['vitesse']." DIRECTION :".$vent['direction']."<BR/>";
		}
*/
		//construction du JSON
		header('Content-type: application/json');
		echo '{"nom":"'.$nomStation.'","latitude":"'.$latitudeStation.'","longitude":"'.$longitudeStation.'","altitude":"'.$altitudeStation.'",';
		echo '"dataVent":[';
		while($infosVent = $bdd->fetch2(PDO::FETCH_ASSOC)){
			$cpt++;
			//echo "nbInfosWeibull : $nbInfosWeibull<BR/>";
			if($cpt==$nbVent){
				echo json_encode($infosVent);
			}else{
				echo json_encode($infosVent).',';
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