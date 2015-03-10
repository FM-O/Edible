<?php

class Allergen {

	public $TagAll;
	public $NomAll;

	public function defineFromTag($tag)
	{
		global $bdd;
		$req = $bdd->prepare("SELECT * FROM Allergen WHERE TagAll = :tag LIMIT 1");
		$req->execute(array('tag' => $tag));
		$req=$req->fetch();

		if($req['TagAll'])
		{
			$this->TagAll = $req['TagAll'];
			$this->NomAll = $req['NomAll'];

			return true;
		}
		else
		{
			return false;
		}
	}
}

?>