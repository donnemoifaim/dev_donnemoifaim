<?php
// CONFIG: Enable debug mode. This means we'll log requests into 'ipn.log' in the same directory.
// Especially useful if you encounter network errors or other intermittent problems with IPN (validation).
// Set this to 0 once you go live or don't require logging.

// Si c'est le site test on met l'adresse de sandbox de paypal

//connection à la bdd
include ('../include/configcache.php') ;
include ('../include/fonctions.php') ; 
// INITATION DES DIFFERENTE OFFRE
include('../include/offre_tarrif.php') ;

if($_SERVER['HTTP_HOST'] == 'test.donnemoifaim.fr')
{
	define("DEBUG", 1);
	// Set to 0 once you're ready to go live
	define("USE_SANDBOX", 1);
	
	define("LOG_FILE", "../logs/ipn_sandbox.log");
	
	$sandbox = 1 ; 
}
else
{
	define("DEBUG", 0);
	// Set to 0 once you're ready to go live
	define("USE_SANDBOX", 0);
	
	$sandbox = 0 ; 
}
// Read POST data
// reading posted data directly from $_POST causes serialization
// issues with array data in POST. Reading raw POST data from input stream instead.
$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();
foreach ($raw_post_array as $keyval) {
	$keyval = explode ('=', $keyval);
	if (count($keyval) == 2)
		$myPost[$keyval[0]] = urldecode($keyval[1]);
}
// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
if(function_exists('get_magic_quotes_gpc')) {
	$get_magic_quotes_exists = true;
}
foreach ($myPost as $key => $value) {
	if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
		$value = urlencode(stripslashes($value));
	} else {
		$value = urlencode($value);
	}
	$req .= "&$key=$value";
}
// Post IPN data back to PayPal to validate the IPN data is genuine
// Without this step anyone can fake IPN data
if(USE_SANDBOX == true) {
	$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
} else {
	$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
}
$ch = curl_init($paypal_url);
if ($ch == FALSE) {
	return FALSE;
}
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
if(DEBUG == true) {
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
}
// CONFIG: Optional proxy configuration
//curl_setopt($ch, CURLOPT_PROXY, $proxy);
//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
// Set TCP timeout to 30 seconds
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
// CONFIG: Please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
// of the certificate as shown below. Ensure the file is readable by the webserver.
// This is mandatory for some environments.
//$cert = __DIR__ . "/certificat/cacert.pem";
//curl_setopt($ch, CURLOPT_CAINFO, $cert);
$res = curl_exec($ch);

if (curl_errno($ch) != 0) // cURL error
	{
	if(DEBUG == true) {	
		//error_log(date('[Y-m-d H:i e] '). "Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL, 3, LOG_FILE);
	}
	erreur_site('Erreur paypal' , 'paypal' , 'probleme de connection vpn paypal vérifier les logs' , 1 ) ; 
	curl_close($ch);
	exit;
} else {
		// Log the entire HTTP response if debug is switched on.
		if(DEBUG == true) {
			//error_log(date('[Y-m-d H:i e] '). "HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req" . PHP_EOL, 3, LOG_FILE);
			//error_log(date('[Y-m-d H:i e] '). "HTTP response of validation request: $res" . PHP_EOL, 3, LOG_FILE);
		}
		curl_close($ch);
}
// Inspect IPN validation result and act accordingly
// Split response headers and payload, a better way for strcmp
$tokens = explode("\r\n\r\n", trim($res));
$res = trim(end($tokens));
if (strcmp ($res, "VERIFIED") == 0) {

	// assign posted variables to local variables
	$quantity = $_POST['quantity'] ;
	$item_name = $_POST['item_name'];
	$item_number = $_POST['item_number'];
	$payment_status = $_POST['payment_status'];
	$payment_amount = $_POST['mc_gross'];
	$txn_id = $_POST['txn_id'];
	$receiver_email = $_POST['receiver_email'];
	$payer_email = $_POST['payer_email'];
	$custom_paypal = $_POST['custom'];
	
	////////////////////DEBUT DES VERIFICATION //////////////////
	
	if ($payment_status == "Completed" || $sandbox == 1)
	{
		//Si le nombre de plat est égale a la quantité ! 
		if($quantity == 1)
		{
			// vérifier que receiver_email est votre adresse email PayPal principale
			if ($adresse_mail_paiement == $receiver_email)
			{
				// On regarde la personne et le type d'abonnement demandé 
				$tableau_infos = explode('-||-' , $custom_paypal) ;
				
				$login = $tableau_infos[0] ;
				$abo = $tableau_infos[1] ;
				
				// On met l'abonnement pour laquelle la personne à payé et pas pour ce qu'il a pris
				$payment_amount = floatval($payment_amount) ;

				if($offre_formule_visiteur['0'] == $payment_amount)
				{
					$abonnement_reel = 0 ; 
				}
				else if($offre_formule_visiteur['1'] == $payment_amount)
				{
					$abonnement_reel = 1 ; 
				}
				else if($offre_formule_visiteur['2'] == $payment_amount)
				{
					$abonnement_reel = 2 ; 
				}
				else if($offre_formule_visiteur['3'] == $payment_amount)
				{
					$abonnement_reel = 3 ; 
				}
				else
				{
					$abonnement_reel = 0 ; 
				}
				
				if($abonnement_reel != 0)
				{
					// Maintenant il y à plus cas changer l'abonnement du compte associé en ne pas oubliant de mettre la date actuel
					$requete_premium = $bdd->prepare('UPDATE utilisateur SET abo = :abo, date_abo = :date_abo WHERE login = :login') ; 
					$requete_premium->execute(array(':login' => $login , ':abo' => $abonnement_reel , ':date_abo' => time()))	;
				}
				else
				{
					erreur_site('Erreur paypal visiteur premium' , 'paypal' , 'payer moins que prévue payé = '.$payment_amount.' à la place de = '.$offre_formule_visiteur[$abo] , $custom_paypal , 1 ) ;
				}
			}
			else
			{
				erreur_site('Erreur paypal adresse visiteur premium' , 'paypal' , 'l\'adresse mail de reception du paiement est éronné : mail valide : '.$adresse_mail_paiement.' contre '.$receiver_email , $custom_paypal , 1 ) ;
			}
		}
		else
		{
			erreur_site('Erreur paypal quantite visiteur premium' , 'paypal' , 'La quantite ne correspond pas' , $custom_paypal, 1 ) ; 
		}
	}
	else
	{
		erreur_site('Erreur paypal paiement visiteur premium' , 'paypal' , 'paiement incomplet ==> statut : '.$payment_status , $custom_paypal , 1 ) ;
	}

// ecrire le code a vérifier
	
	if(DEBUG == true) {
		//error_log(date('[Y-m-d H:i e] '). "Verified IPN: $req ". PHP_EOL, 3, LOG_FILE);
	}
} else if (strcmp ($res, "INVALID") == 0) {
	// log for manual investigation
	// Add business logic here which deals with invalid IPN messages
	if(DEBUG == true) {
		//error_log(date('[Y-m-d H:i e] '). "Invalid IPN: $req" . PHP_EOL, 3, LOG_FILE);
	}
	erreur_site('Erreur paypal utilisateur' , 'paypal' , 'probleme de connection vpn', '' , 1 ) ; 
}
?>