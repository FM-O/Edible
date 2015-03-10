<?php

class URI
{
	private $tableURI=array();

	public function __construct()
	{
		//Recupération de l'URI
		$requestURI=$_SERVER['REQUEST_URI'];
		
		//Soustraction du dossier de l'API
		$relativeFolder=explode('/',$_SERVER['SCRIPT_NAME']);
		$relativeFolder=(explode('/'.end($relativeFolder), $_SERVER['SCRIPT_NAME']));
		
		if(trim($relativeFolder[0]) != "") {
			$requestURI=explode($relativeFolder[0].'/', $requestURI);
			$requestURI=$requestURI[1];
		}
		else {
			$requestURI = substr($requestURI, 1);
		}

		$this->tableURI=explode('/',$requestURI);
	}

	public function getElement($id)
	{
		$id--;
		if(isset($this->tableURI[$id]) && trim($this->tableURI[$id]) != "")
			return strtolower($this->tableURI[$id]);
		else
			return false;
	}
	public function checkElement($id, $element)
	{
		if($this->getElement($id) && $this->getElement($id) == strtolower($element))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

?>
