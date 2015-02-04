<?php

/**
* 
*/
class Functions 
{
	private $bdd;

	function __construct($bdd)
	{
		$this->bdd=$bdd;
	}

	function nbTotalStations(){
		$this->bdd->query('SELECT Count(*) FROM station'); // compte le nombre de stations
		while($resultat = $this->bdd->fetch()){
			$nb=$resultat[0];
		}
		return $nb;
	}

	function cleanBDD(){
		echo 'DEBUT cleanBDD <BR/>';
		$this->bdd->query('TRUNCATE vent');
		$this->bdd->query('TRUNCATE infosWeibull');
		$this->bdd->query('TRUNCATE station');
		echo 'FIN cleanBDD <BR/>';
	}

	function nbInfosWeibull($idStation){
		$this->bdd->prepare('SELECT Count(*) FROM infosWeibull WHERE idStation=?');// compte le nombre de données infosWeibull
		$this->bdd->execute(array($idStation));
		while($resultat = $this->bdd->fetch()){
			$nb=$resultat[0];
		}
		return $nb;
	}

	function nbInfosVent($idStation){
		$this->bdd->prepare('SELECT Count(*) FROM vent WHERE idStation=? GROUP BY direction');// compte le nombre de données vent pour la station d'id $idStation
		$this->bdd->execute(array($idStation));
		while($resultat = $this->bdd->fetch()){
			$nb=$resultat[0];
		}
		return $nb;
	}

	

	function nbInfosVentDistinctDirection($idStation){
		$this->bdd->prepare('SELECT COUNT(distinct(direction)) FROM vent WHERE idStation=?');
		$this->bdd->execute(array($idStation));
		while($resultat = $this->bdd->fetch()){
			$nb=$resultat[0];
		}
		return $nb;
	}


	function distance($lat1, $lon1, $lat2, $lon2) { 
		$theta = $lon1 - $lon2;
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$km = $dist * 60 * 1.1515 * 1.609344;
		if(is_nan($km))
			return 0;
		return $km;
	}

/*
	function getStations(){
		global $bdd;
		return $bdd->query('SELECT * FROM station');
	}
*/
	function getStationProche($lat, $lon) {
		$this->bdd->query('SELECT * FROM station');
		$minId=-1;
		$minDistance=-1;
		//echo "Latitude : $lat, longitude : $lon <BR/>";
		// On affiche chaque entrée une à une
		while ($station = $this->bdd->fetch()){
			$stationLat = $station['latitude'];
			$stationLong = $station['longitude'];
			$stationId = $station['idStation'];
			//echo "Station (id : $stationId) latitude : $stationLat, longitude : $stationLong <BR/> ";
			$distance = $this->distance($lat, $lon , $stationLat, $stationLong);
			if($minId ==-1){
				$minId=$station['idStation'];
				$minDistance = $distance;
			}
			if(doubleval($distance)==0){
				$minId=$stationId;
				$minDistance = $distance;
				//echo "MEME COORDONNEES <BR/>";
				break;
			}
			//echo "Distance = $distance <BR/>";
			//echo "Min Distance = $minDistance <BR/>";
			if(doubleval($minDistance)>doubleval($distance)){
				//echo "Modif distance <BR/>";
				$minId=$stationId;
				$minDistance = $distance;
			}
			//echo "Distance avec ".$station['nom']." : ".$distance." kms <br/>" ;
		}
		//echo "minId : $minId";
		return $minId;//retourne id de la station de la plus proche
	}

	function afficheStation($id){
		try {
			$this->bdd->prepare('SELECT * FROM station WHERE idStation=?'); // compte le nombre de stations
			$this->bdd->execute(array($id));
			while($station = $this->bdd->fetch()){
				echo $station['idStation'].$station['nom'].'<br/>';
			}

		} catch( Exception $e ){
			echo 'Erreur de requète : ', $e->getMessage();
		}
	}

	function vitesseMoyenne($idStation){
		//echo 'DEBUT vitesseMoyenne <BR/>';
		try {
			$this->bdd->prepare('SELECT AVG(vitesse) as vMoy FROM vent WHERE idStation =?');
			$this->bdd->execute(array($idStation));
			$vMoy=0;
			while($result=$this->bdd->fetch()){
				$vMoy=$result['vMoy'];
			}
			
		} catch( Exception $e ){
			echo 'Erreur de requète : ', $e->getMessage();
		}
		//echo 'FIN vitesseMoyenne <BR/>';
		return $vMoy;
	}


	function getNbVitesseByIdStation($idStation){
		try {
			$this->bdd->prepare('SELECT COUNT(*) as nbVitesse FROM vent WHERE idStation =?');
			$this->bdd->execute(array($idStation));
			$nb=0;
			while($result=$this->bdd->fetch()){
				$nb=$result['nbVitesse'];
			}
			return $nb;
		} catch( Exception $e ){
			echo 'Erreur de requète : ', $e->getMessage();
		}
	}

	
	function ecartType($vMoy,$nbVitesse,$idStation){
		//echo 'DEBUT ecartType <BR/>';
		//$vMoy = $this->vitesseMoyenne($idStation);

		//$nbVitesse = $this->getNbVitesseByIdStation($idStation);
		//echo 'NB Vitesse = '.$nbVitesse.'<BR/>';
		//echo "VMOY = ".$vMoy.'<BR/>';
		
		try {
			$this->bdd->prepare('SELECT vitesse FROM vent WHERE idStation =?');
			$this->bdd->execute(array($idStation));
			$v=0;
			$sum=0;
			while($result=$this->bdd->fetch()){
				$v=$result['vitesse'];

				$sum += (($v-$vMoy)*($v-$vMoy));
				//echo ' val :'.$v.' : '.$sum.'<BR/>';
			}
		} catch( Exception $e ){
			echo 'Erreur de requète : ', $e->getMessage();
		}
		//echo '<BR/>'.$sum;
		$resultEcartType=0;
		if($nbVitesse!=0 && $sum!=0){
			$resultEcartType = sqrt($sum/$nbVitesse);
		}
		//echo 'ECART TYPE = '.$resultEcartType.'<BR/>';
		//echo 'FIN ecartType <BR/>';
		return $resultEcartType;
		//echo '<BR/> resultEcartType :'.$resultEcartType;

	}

	function getFacteurForme($ecartType,$vMoy){
		//echo 'DEBUT getFacteurForme <BR/>';
		//return pow(((0.9874)/($this->ecartType($idStation)/5.27)),1.0983);
		$facteurForme=0;
		if($ecartType!=0 && $vMoy!=0){
			$facteurForme = pow(((0.9874)/($ecartType/$vMoy)),1.0983);
		}
		//echo 'Facteur forme = '.$facteurForme.'<BR/>';
		//echo 'FIN getFacteurForme <BR/>';
		return $facteurForme;

	}

	function getFacteurEchelle($vMoy,$k){
		//echo 'DEBUT getFacteurEchelle <BR/>';
		$param=0;
		if($k!=0){
			$param = 1+(1/$k);
		}
		//echo "1+(1/k) = ".$param.'<BR/>';
		$gammaEuler = $this->gammaEuler($param);
		//echo "Gamma euler (1+(1/k)) = ".$gammaEuler.'<BR/>';
		$facteurEchelle=0;
		if($gammaEuler!=0 && $vMoy!=0){
			$facteurEchelle = $vMoy/($gammaEuler);
		}
		//echo 'Facteur echelle = '.$facteurEchelle.'<BR/>'; 
		//echo 'FIN getFacteurEchelle <BR/>';
		return $facteurEchelle;
	}

	function gammaEuler($X){
		//Calcul du gamma d'Euler avec l'approximation de Stirling$X=1+(1/$k);
		$gamma=0;
		if($X!=0){
			$gamma=((pow($X,$X-0.5))*(exp(-$X))*(sqrt(2*pi()))*(1+(1/(12*$X))+(1/(288*pow($X,2)))-(139/(51840*(pow($X,3))))-(571/(2488320*(pow($X,4))))+(163879/(209018880*(pow($X,5)))))); //Gamma d'Euler;
		}
		return $gamma;
	}

	function testWeibull(){
		for($i=0;$i<=30;$i++){
			$wb = $this->weibull($i, 1.48388,3.12339);
			echo "VITESSE : ".$i." W : ".$wb."<BR/>";
		}
	}

	function weibull($v, $k, $a){
		$weibull=0;
		if($v!=0 && $k!=0 && $a!=0){
			$weibull = ($k/$a)*pow(($v/$a),($k-1))*exp(-pow(($v/$a),$k));
		}
		//echo 'weibull = '.$weibull.'<BR/>';
		return $weibull*100;
	}

	function addInfosWeibull(){
		echo 'DEBUT addInfosWeibull <BR/>';
		$this->bdd->query('SELECT idStation,facteurForme,facteurEchelle FROM station');
		
		$createFic = fopen("insertInfosWeibull.sql", "a");
		if($createFic==false)
			die("La création du fichier a échoué");
		
		foreach ($this->bdd->fetchAll() as $result) {
			$idStation=$result['idStation'];


			for($i=0;$i<=30;$i++){
				$wb = $this->weibull($i, $result['facteurForme'],$result['facteurEchelle']);
				$this->bdd->prepare('INSERT INTO infosWeibull (idStation, vitesse, weibull) VALUES (?,?,?) ');
				$this->bdd->execute(array($idStation,$i,$wb));
				fwrite($createFic,'INSERT INTO infosWeibull (idStation, vitesse, weibull) VALUES ('.$idStation.','.$i.','.$wb.');');
				
			}
		}
		fclose($createFic);
		echo 'FIN addInfosWeibull <BR/>';
	}

	function addStations($ficStations){
		echo 'DEBUT addStations <BR/>';
		$fic=fopen('upload/'.$ficStations, "r");
		while(!feof($fic)){
			$ligne=fgets($fic);
			if(!feof($fic)){
				
				$createFicstation = fopen("insertStations.sql", "a");
				if($createFicstation==false)
					die("La création du fichier a échoué");
				
				$tab = explode(";",$ligne);
				if(!empty($tab[0]) && !empty($tab[1]) && !empty($tab[2]) && !empty($tab[3]) && !empty($tab[4])){
					$numStation = $tab[0];
					$nomStation = $tab[1];
					$latStation = str_replace(',','.',$tab[2]);
					$longStation = str_replace(',','.',$tab[3]);
					$altitudeStation = str_replace(',','.',$tab[4]);

					//echo 'INSERT NUM :'.$numStation.' NOM :'.$nomStation.' LAT :'.$latStation.' LONG :'.$longStation.' ALT :'.$altitudeStation.'<BR/>';
					$this->bdd->prepare('INSERT INTO station (idStation, nom, latitude, longitude, altitude) VALUES (?,?,?,?,?)');
		    		$this->bdd->execute(array($numStation,$nomStation,$latStation,$longStation,$altitudeStation));
		    		fwrite($createFicstation,'INSERT INTO station (idStation, nom, latitude, longitude, altitude) VALUES ('.$numStation.',"'.$nomStation.'",'.$latStation.','.$longStation.','.$altitudeStation.');');
		    		
		    		
				}
				fclose($createFicstation);

			}
		}
		
		fclose($fic);
		echo 'FIN addStations <BR/>';
	}

	function addTemperature($ficTemperature){
		echo 'DEBUT addTemperature <BR/>';
		$fic=fopen('upload/'.$ficTemperature, "r");
		while(!feof($fic)){
			$ligne=fgets($fic);
			if(!feof($fic)){
	
				$createFic = fopen("updateTemperature.sql", "a");
				if($createFic==false)
					die("La création du fichier a échoué");
	
				$tab = explode(";",$ligne);
				if(!empty($tab[0]) && !empty($tab[1])){
					$numStation = $tab[0];
					$temperature = $tab[1];
	
					//echo 'INSERT NUM :'.$numStation.' NOM :'.$nomStation.' LAT :'.$latStation.' LONG :'.$longStation.' ALT :'.$altitudeStation.'<BR/>';
					//$this->bdd->prepare('UPDATE station SET temperature=? WHERE idStation=?');
					//$this->bdd->execute(array($temperature,$numStation));
					fwrite($createFic,'UPDATE station SET temperature='.$temperature.' WHERE idStation='.$numStation.';');
	
	
				}
				fclose($createFic);
	
			}
		}
	
		fclose($fic);
		echo 'FIN addTemperature <BR/>';
	}
	
	function addDirectionVitesse($fic){

		echo 'DEBUT addDirectionVitesse <BR/>';
		$ficD=fopen('upload/'.$fic, "r");
		while(!feof($ficD)){
			$ligne=fgets($ficD);
			if(!feof($ficD)){
				
				$createFic = fopen("insertDirectionVitesses.sql", "a");
				if($createFic==false)
					die("La création du fichier a échoué");
				
				$tab = explode(";",$ligne);
				//		idStation 		|	date 		|				vitesse 	| 		direction
				if(!empty($tab[0]) && !empty($tab[1]) && !empty($tab[2]) && !empty($tab[3])){
					$numStation = $tab[0];
					//echo 'tab[1][0] : '.$tab[1][0].$tab[1][1];
					$date = $tab[1][0].$tab[1][1].$tab[1][2].$tab[1][3].'-'.$tab[1][4].$tab[1][5].'-'.$tab[1][6].$tab[1][7].' '.$tab[1][8].$tab[1][9].':00:00';
					//$date = $tab[1];//+$tabDir[1][1]+$tabDir[1][2]+$tabDir[1][3]+'/'+$tabDir[1][4]+$tabDir[1][5]+'/'+$tabDir[1][6]+$tabDir[1][7];
					$vitesse = str_replace(',','.',$tab[2]);
					$direction = str_replace(',','.',$tab[3]);

					//echo 'INSERT NUM :'.$numStation.' DATE :'.$date.' DIR :'.$direction.'<BR/>';
					$this->bdd->prepare('INSERT INTO vent (idStation, date, vitesse, direction) VALUES (?,?,?,?)');
		    		$this->bdd->execute(array($numStation,$date,$vitesse,$direction));
		    		fwrite($createFic,'INSERT INTO vent (idStation, date, vitesse, direction) VALUES ('.$numStation.','.$date.','.$vitesse.','.$direction.');');
		    		
				}
				fclose($createFic);
			}
		}
		$this->majFacteurs();
		$this->addInfosWeibull();
		fclose($ficD);
		echo 'FIN addDirectionVitesse <BR/>';
	}

	function majFacteurs(){
		echo 'DEBUT majFacteurs <BR/>';
		$this->bdd->query('SELECT idStation FROM station');

		$createFic = fopen("updateFacteurs.sql", "a");
		if($createFic==false)
			die("La création du fichier a échoué");
		
		//while($result=$this->bdd->fetch())
		foreach ($this->bdd->fetchAll() as $result) {
			$idStation=$result['idStation'];
			//echo 'idStation = '.$idStation.'<BR/>';

			//$idStation=2320001;
			$vMoy = $this->vitesseMoyenne($idStation);
			//echo 'vMoy = '.$vMoy.'<BR/>';
			$nbVitesse = $this->getNbVitesseByIdStation($idStation);
			//echo 'nbVitesse = '.$nbVitesse.'<BR/>';
			$ecart = $this->ecartType($vMoy,$nbVitesse,$idStation);
			//echo 'ecart = '.$ecart.'<BR/>';
			$k = $this->getFacteurForme($ecart, $vMoy);
			//echo 'k = '.$k.'<BR/>';
			$a = $this->getFacteurEchelle($vMoy, $k);
			//echo 'a = '.$a.'<BR/>';

		    $this->bdd->prepare("UPDATE station SET facteurForme=?,facteurEchelle=? WHERE idStation=?");
		    $this->bdd->execute(array($k,$a,$idStation));
		    fwrite($createFic,'UPDATE station SET facteurForme='.$k.',facteurEchelle='.$a.' WHERE idStation='.$idStation.';');
		    
			//echo '<BR/>';
		}
		fclose($createFic);
		echo 'FIN majFacteurs <BR/>';
	}

	function majFacteurs2(){

		$idStation=56069001;
		echo 'idStation = '.$idStation.'<BR/>';
		$vMoy = $this->vitesseMoyenne($idStation);
		echo 'vMoy = '.$vMoy.'<BR/>';
		$nbVitesse = $this->getNbVitesseByIdStation($idStation);
		echo 'nbVitesse = '.$nbVitesse.'<BR/>';
		$ecart = $this->ecartType($vMoy,$nbVitesse,$idStation);
		echo 'ecart = '.$ecart.'<BR/>';
		$k = $this->getFacteurForme($ecart, $vMoy);
		echo 'k = '.$k.'<BR/>';
		$a = $this->getFacteurEchelle($vMoy, $k);
		echo 'a = '.$a.'<BR/>';

	}


}

?>