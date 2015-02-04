<?php
	ini_set("auto_detect_line_endings", true);

	function __autoload($class_name) {
    	include $class_name . '.php';
	}
	
	$bdd = new BD();
	$functions = new Functions($bdd);
	/** 
		DEBUT MAIN 
	**/

	echo 'NOM = '.$_FILES['ficStations']['name'].'<BR/>'.'TYPE = '.$_FILES['ficStations']['type'].'<BR/>'.'SIZE = '.$_FILES['ficStations']['size'].'<BR/>'.'DIR TEMP = '.$_FILES['ficStations']['tmp_name'].'<BR/>';
	echo '<BR/>';
	echo 'NOM = '.$_FILES['ficVitDir']['name'].'<BR/>'.'TYPE = '.$_FILES['ficVitDir']['type'].'<BR/>'.'SIZE = '.$_FILES['ficVitDir']['size'].'<BR/>'.'DIR TEMP = '.$_FILES['ficVitDir']['tmp_name'].'<BR/>';

	$infosErreur=null;

	if ($_FILES['ficStations']['error'] > 0){
		$infosErreur = "Erreur lors du transfert du fichier ".$_FILES['ficStations']['name']."<BR/>";
	}
	else if ($_FILES['ficVitDir']['error'] > 0){
		$infosErreur = "Erreur lors du transfert du fichier ".$_FILES['ficVitDir']['name']."<BR/>";
	}else{
		$infosErreur = "<BR/> Transferts OK <BR/>";
	}
	echo $infosErreur;

	//si les fichiers ont bien été transférés
	if(!($_FILES['ficStations']['error'] > 0) && !($_FILES['ficVitDir']['error'] > 0)){


		if(isset($_FILES['ficStations']) && isset($_FILES['ficVitDir'])){
			echo 'EN TRAITEMENT...';
			
			$dossier = 'upload/';
			$fichier1 = basename($_FILES['ficStations']['name']);
			if(move_uploaded_file($_FILES['ficStations']['tmp_name'], $dossier.$fichier1)){
				echo 'COPIES OK';
			}
			else{
				echo 'Error copie : '.$fichier1.'<BR/>';
			}
			$fichier2 = basename($_FILES['ficVitDir']['name']);
			if(move_uploaded_file($_FILES['ficVitDir']['tmp_name'], $dossier.$fichier2)){
				echo 'COPIES OK';
			}
			else{
				echo 'Error copie : '.$fichier2.'<BR/>';
			}


			
			$functions->cleanBDD();
			//on ajoute de nouvelles stations
			if(isset($_FILES['ficStations'])){
				$functions->addStations($fichier1);
				unlink($dossier.$fichier1);//supprimer le fichier des stations
			}
			//on ajoute les vitesses directions/vitesses quand il y en a
			if(isset($_FILES['ficVitDir'])){
				$functions->addDirectionVitesse($fichier2);
				unlink($dossier.$fichier2);//supprimer le fichier des vitesses/Directions
			}
			echo "<script type='text/javascript'>
					alert(\"SUCCESS !!!!!!!!!!!\");
					//document.location.replace('../../admin/index.php');
				</script>";

		}
	}else{

		echo "<script type='text/javascript'>
				alert(\"RECOMMENCEZ !!!!!!!!!!!\");
				document.location.replace('aaaaa.php');
			</script>";

	}



	//$functions->cleanBDD();
	//$functions->addStations("Liste-stations.csv");
	//$functions->addDirectionVitesse("directionVitesse.csv");

	//$functions->testWeibull();

	/** 
		FIN MAIN 
	**/
?>

