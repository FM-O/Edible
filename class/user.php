<?php

class User {

	private $IdUser;
	private $PseudoUser;
	private $NameUser;
	private $MailUser;
	private $PasswordUser;
	private $ApiKeyUser;
	private $ActiveUser;
	private $Allergen;

	public function __construct() {


	}
	public function defineFromId($id) {

		global $bdd;
		$req = $bdd->prepare("SELECT * FROM User WHERE IdUser = :id LIMIT 1");
		$req->execute(array('id' => $id));
		$req=$req->fetch();

		if($req['IdUser'])
		{
			$this->IdUser = $req['IdUser'];
			$this->PseudoUser = $req['PseudoUser'];
			$this->NameUser = $req['NameUser'];
			$this->MailUser = $req['MailUser'];
			$this->PasswordUser = $req['PasswordUser'];
			$this->ApiKeyUser = $req['ApiKeyUser'];
			$this->ActiveUser = $req['ActiveUser'];

			$this->defineAllergens();

			return true;
		}
		else
		{
			return false;
		}

	}
	private function defineAllergens() {
		global $bdd;
		$req = $bdd->prepare("SELECT * FROM UserAllergen as UA, Allergen as A WHERE UA.TagAllergens = A.TagAll AND UA.IdUser = :id");
		$req->execute(array('id' => $this->IdUser));
		while($UserAllergen=$req->fetch()) {
			$this->Allergen[$UserAllergen['TagAll']]= new Allergen();
			$this->Allergen[$UserAllergen['TagAll']]->defineFromTag($UserAllergen['TagAll']);
		}
	}

	public function match($list)
	{
		$allergens = array();
		foreach ($list as $allergen) {
			if(array_key_exists($allergen->TagAll, $this->Allergen)) {
				array_push($allergens, $allergen->NomAll);
			}
		}
		return $allergens;
	}
}


?>