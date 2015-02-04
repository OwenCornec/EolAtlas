<?php

//error_reporting(-1);
//ini_set('display_errors', 1);

class BD
{
	private $_bdd;
	
	private $_reponseRequete;


	public function __construct(){
		//$this->_bdd = new PDO('mysql:host=localhost;dbname=eolatlas', 'root', 'root');
		$this->_bdd = new PDO('mysql:host=127.0.0.1;dbname=eolatlas', 'eolatlas', '30l@tl@s');
	}

	
	
	public function query($requete)
	{
		try
		{
			$this->_reponseRequete = $this->_bdd->query($requete);
		}
		catch (Exception $e)
		{
			die('Erreur : ' . $e->getMessage());
		}
		
	}

	public function result(){
		return $_reponseRequete;
	}

	public function prepare($requete)
	{
		try
		{
			$this->_reponseRequete = $this->_bdd->prepare($requete);
		}
		catch (Exception $e)
		{
			die('Erreur : ' . $e->getMessage());
		}
		
	}

	public function execute($requete)
	{
		try
		{
			$this->_reponseRequete ->execute($requete);
			//print_r($this->_bdd->errorInfo());
		}
		catch (Exception $e)
		{
			die('Erreur : ' . $e->getMessage());
		}
		
	}
	public function fetch(){
		return ($this->_reponseRequete->fetch());
	}

	public function fetchAll(){
		return ($this->_reponseRequete->fetchAll());
	}

	public function fetch2($option)
	{
		return ($this->_reponseRequete->fetch($option));
	}
}
?>