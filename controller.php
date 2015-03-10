<?php
error_reporting(E_ALL);
ini_set('display_errors','On');

header('Content-Type: application/json');
if(isset($_SERVER['HTTP_ORIGIN'])) $host=$_SERVER['HTTP_ORIGIN'];
else $host=$_SERVER['HTTP_HOST'];
header("Access-Control-Allow-Origin: ".$host);
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: GET,PUT,POST,DELETE");

require_once(dirname(__FILE__).'/class/uri.php');
require_once(dirname(__FILE__).'/class/bdd.php');
require_once(dirname(__FILE__).'/class/user.php');
require_once(dirname(__FILE__).'/class/allergen.php');

$uri=new URI();


//Instanciation des informations retournées
$return = array(
	"success"=>false,
	"result"=>"API Error",
	);

//Récupération de l'API
if($uri->checkElement(1, "ean"))
{
	$return['success']=true;
}
elseif($uri->checkElement(1, "match") && $uri->getElement(2) && $uri->getElement(3))
{	
	$url="http://fr.openfoodfacts.org/api/v0/produit/".$uri->getElement(2).".json";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); //2sec

	$OFFdata = curl_exec($ch);
	$OFFinfos=curl_getinfo($ch);
	$OFFerror=curl_errno($ch);

	curl_close($ch);

	// print_r($OFFinfos);
	if($OFFinfos['http_code'] == "200" && $OFFerror == 0) {

		$OFFdata=json_decode($OFFdata);

		if($OFFdata->status == 1) {		

			$bdd = new BDD();

			$user = new User();
			if($user->defineFromId($uri->getElement(3))) {

				//creation de la fiche produit
				if(isset($OFFdata->product->code))
				 	$eanProd=$OFFdata->product->code;
				elseif(isset($OFFdata->product->_id))
					$eanProd=$OFFdata->product->_id;
				else
					$eanProd=null;

				if(isset($OFFdata->product->product_name))
				 	$nameProd=$OFFdata->product->product_name;
				elseif(isset($OFFdata->product->generic_name))
					$nameProd=$OFFdata->product->generic_name;
				else
					$nameProd=null;

				if(isset($OFFdata->product->last_modified_t))
				 	$lastMajProd=$OFFdata->product->last_modified_t;
				elseif(isset($OFFdata->product->completed_t))
					$lastMajProd=$OFFdata->product->completed_t;
				elseif(isset($OFFdata->product->created_t))
					$lastMajProd=$OFFdata->product->created_t;
				else
					$lastMajProd=null;

				if(isset($OFFdata->product->image_url))
				 	$imageProd=$OFFdata->product->image_url;
				else
					$imageProd=null;

				if(isset($OFFdata->product->traces_tags)) {
				 	$tracesProd=$OFFdata->product->traces_tags;
				 	$tempTraces=array();
				 	foreach ($tracesProd as $traces) {
				 		$tempTraces[$traces]=new Allergen();
				 		$tempTraces[$traces]->defineFromTag($traces);
				 	}
				 	$tracesProd=$tempTraces;
				}
				else
					$tracesProd=array();

				if(isset($OFFdata->product->allergens_hierarchy))
				 	$allergensProd=$OFFdata->product->allergens_hierarchy;
				else
					$allergensProd=array();

				if(isset($OFFdata->product->ingredients_text_with_allergens))
				 	$ingredientsProd=$OFFdata->product->ingredients_text_with_allergens;
				elseif(isset($OFFdata->product->ingredients_text))
					$ingredientsProd=$OFFdata->product->ingredients_text;
				else
					$ingredientsProd=null;


				$traces = $user->match($tracesProd);
				$allergies = $user->match($allergensProd);


				$return['success']=true;

				$return['result']=array(
					"matching"=>array(
						"traces"=>$traces,
						"allergens"=>$allergies,
					),
					"product"=>array(
						"ean"=>$eanProd,
						"name"=>$nameProd,
						"image"=>$imageProd,
						"last_maj"=>$lastMajProd,
						"traces"=>$tracesProd,
						"allergens"=>$allergensProd,
						"ingredients_text"=>$ingredientsProd,
					),
				);
			}
			else
			{
				$return['result']="L'utilisateur n'existe pas.";
			}
		}
		else
		{
			$return['result']="Le produit recherché n'a pas été trouvé.";
		}
	}
	else
	{
		$return['result']="Poblème de connexion à l'API OpenFoodFacts";
	}
}
else
{
	$return['success']=false;
	$return['result']="Unknow API";
}
//Encodage de l'API et affichage
echo(json_encode($return));
?>