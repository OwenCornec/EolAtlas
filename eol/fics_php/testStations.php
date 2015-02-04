<?php
	ini_set("auto_detect_line_endings", true);

	function __autoload($class_name) {
    	include $class_name . '.php';
	}
	
	$bdd = new BD();
	$functions = new Functions($bdd);
	
	$ficStations="stationsTotales.csv";
	$ficVD="touteslesdonnees.csv";
	
	//$functions->addStations($ficStations);
	//unlink($ficStations);//supprimer le fichier des stations
	
	//$functions->addDirectionVitesse($ficVD);
	$functions->addTemperature("temperatures.csv");
	//unlink($ficVD);//supprimer le fichier des vitesses/Directions
	
?>