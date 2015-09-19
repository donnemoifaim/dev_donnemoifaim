<?php

session_start(); 


// CONFIG: Enable debug mode. This means we'll log requests into 'ipn.log' in the same directory.
// Especially useful if you encounter network errors or other intermittent problems with IPN (validation).
// Set this to 0 once you go live or don't require logging.

// Si c'est le site test on met l'adresse de sandbox de paypal

//connection à la bdd
include ('../../include/configcache.php') ;
include ('../../include/fonctions.php') ; 
// INITATION DES DIFFERENTE OFFRE
include('../../include/offre_tarrif.php') ;

// Si le godMod est activé on laisse passer tout
if(!empty($_SESSION['login_connexion_de_dieu_resto']))
{
	if(!empty($_SESSION['date_connexion_de_dieu_resto']))
	{
		// Il faut que la session ne soit pas supérieur à 30 minutes
		if($_SESSION['date_connexion_de_dieu_resto'] > time() - 1800)
		{
			if(!empty($_SESSION['token']) && $_SESSION['token'] == $_POST['token'])
			{
				if(!empty($_POST['id_commande']))
				{
					// On va rechercher dans la commande l'abonnement
					$requete_abonnement_check = $bdd->prepare('SELECT abonnement FROM commandes WHERE id_commande = :id_commande') ;
					$requete_abonnement_check->execute(array(':id_commande' => $_POST['id_commande'])) ; 
					
					// Il faut également que le type d'abonnement ne soit pas abonnement ! 
					if($info_abonnement_check = $requete_abonnement_check->fetch())
					{
						if($info_abonnement_check['abonnement'] == 'abonnement')
						{
							erreur_site('Erreur GOD connexion' , 'Paiement plat' , 'Impossible d\'utiliser le GOD connexion avec les abonnnements' , 1 ) ; 
						}
						else
						{
							$GoodPassAutorise = 1 ;
						}
					}
				}
				else
				{
					erreur_site('Erreur GOD connexion' , 'Paiement plat' , 'Id de la commande manquant ' , 1 ) ; 
				}
			}
			else
			{
				erreur_site('Erreur God connexion' , 'Paiement plat' , 'Quelqun à essayer de briser votre sécurité et envoyer la commande :'.$id_commande , 1 ) ; 
			}
		}
		else
		{
			erreur_site('Erreur God connexion' , 'Paiement plat' , 'temps session de dieu écoulé' , 1 ) ; 
			die() ; 
		}
	}
	else
	{
		erreur_site('Erreur God connexion' , 'Paiement plat' , 'Date de connection de dieu manquante' , 1 ) ; 
	}
	
	// On supprime $_SESSION['login_connexion_de_dieu_resto'] qui doit etre recharger à nouveau si on veut l'utiliser
	unset($_SESSION['login_connexion_de_dieu_resto']) ; 
}

if($_SERVER['HTTP_HOST'] == 'test.donnemoifaim.fr')
{
	define("DEBUG", 1);
	// Set to 0 once you're ready to go live
	define("USE_SANDBOX", 1);
	
	define("LOG_FILE", "../../logs/ipn_sandbox.log");
	
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
if (strcmp ($res, "VERIFIED") == 0 OR !empty($GoodPassAutorise)) {

	// On met juste l'id_commande pour le good 
	if(!empty($GoodPassAutorise))
	{
		$id_commande = $_POST['id_commande'];
	}
	else
	{
		// assign posted variables to local variables
		$quantity = $_POST['quantity'] ;
		$item_name = $_POST['item_name'];
		$item_number = $_POST['item_number'];
		$payment_status = $_POST['payment_status'];
		$payment_amount = $_POST['mc_gross'];
		$txn_id = $_POST['txn_id'];
		$receiver_email = $_POST['receiver_email'];
		$payer_email = $_POST['payer_email'];
		$id_commande = $_POST['custom'];
		
		////////////////// PARTIE ABONNEMENT 
		
		if(!empty($_POST['txn_type']))
		{
			if(empty($payment_amount))
			{
				$payment_amount = $_POST['mc_amount1']; 
			}
			
			if(!empty($_POST['subscr_id']))
			{
				$id_abonnement = $_POST['subscr_id'] ; 
			}
			
			// Création de l'abonnement
			if($_POST['txn_type'] == 'subscr_signup')
			{
				// On met l'id de l'abonnement dans la commande pour savoir par exemple quelle image supprimer par la suite
				$requete_commande = $bdd->prepare('UPDATE commandes SET statut_abonnement = :statut_abonnement,  id_abonnement = :id_abonnement WHERE id_commande = :id_commande') ; 
				$requete_commande->execute(array(':id_commande' => $id_commande , ':statut_abonnement' => 'active', ':id_abonnement' => $id_abonnement)) ;
				
				$requete_commande->closeCursor() ; 
			}
			
			// Paiement d'un abonnement 
			if($_POST['txn_type'] == 'subscr_payment')
			{
				// On met le numéro de facture à + 1 et on change la date de paiement
				
				$requete_commande = $bdd->prepare('UPDATE commandes SET  date_paiement = :date_paiement, numero_facture = numero_facture + 1  WHERE id_commande = :id_commande') ; 
				$requete_commande->execute(array(':id_commande' => $id_commande , ':date_paiement' => time())) ;
				
				$requete_commande->closeCursor() ;
				
				// Dire que l'on veut envoyer un mail après la facture
				$envoyer_mail_facture = 1 ; 
				
				// Pour savoir ou doit allez le pdf
				$chemain_relatif_facture = '../' ; 
				
				// Créer le pdf 
				include("../pdf/generer-factures.php") ;
				
				die();
			}
			// Annulation de l'abonnement le premier et le dexième c'est la fin d'un abonnement
			if($_POST['txn_type'] == 'subscr_cancel' || $_POST['txn_type']  == 'subscr_eot')
			{
				// On va chercher l'id de la commande à partir de l'id unique d'abonnement envoyé par paypal
				$requete_id_commande = $bdd->prepare('SELECT id,id_commande, login, mail FROM commandes WHERE id_abonnement = :id_abonnement') ;
				$requete_id_commande->execute(array(':id_abonnement' => $id_abonnement)) ; 
				
				// Il n'y en à qu'un seul
				if($info_id_commande = $requete_id_commande->fetch())
				{
					// Récupération des idimages des plats pour pouvoir les mettre en archivage
					$requete_archivage_plat = $bdd->prepare('SELECT  c.mail mail, p.idimage idimage, p.login, p.nomplat, p.date_ FROM plat p INNER JOIN client c ON p.login = c.login  WHERE p.id_commande = :id_commande') ;
					$requete_archivage_plat->execute(array(':id_commande' => $info_id_commande['id_commande'])) ; 
					
					while($info_archivage_plat = $requete_archivage_plat->fetch())
					{
						// Envoi d'un email pour dire que le plat à été supprimé
						envoi_mail_supression_plat($info_archivage_plat) ;
						
						// Archivage des plats
						archivage_plat($info_archivage_plat['idimage'] , '../') ; 
					}
					
					$requete_archivage_plat->CloseCursor() ; 
					
					// On fait une requete également pour dire que l'abonnement est arreté
					$requete_commande_arret = $bdd->prepare('UPDATE commandes SET statut_abonnement = :statut_abonnement , statut = :statut WHERE id_commande = :id_commande');
					$requete_commande_arret->execute(array(':id_commande' => $info_id_commande['id_commande'] , ':statut_abonnement' => 'arret' , ':statut' => 'arret')) ; 
					
					// Envoi d'un mail pour prévenir la personne
					$mail = $info_id_commande['mail']; // Déclaration de l'adresse de destination.
	
					$sujet = 'DonneMoiFaim : Arrêt abonnement n° '.$info_id_commande['id'] ;
					
					$heure = date("H");

					//on cherche a savoir si c'est le soir ou la journée
					if($heure > 19)
					{
						$moment_journee = 'Bonsoir';
					}
					else
					{
						$moment_journee = 'Bonjour';
					}
					
					$message_txt = $moment_journee.' '.$info_id_commande['login'].',
					
					Votre abonnement n° '.$info_id_commande['id'].' a bien été annulé correctement.
					
					N\'hésitez pas à nous contacter si vous avez rencontré des problèmes avec ce dernier afin d\'améliorer votre expérience client.

					L\'équipe DonneMoiFaim.' ;

					$message_html = '
					<html>
						<head>
						<style>
							strong{color:#db302d}
							p{color:#333}
						</style>
						</head>
						<body>
							<p>
								'.$moment_journee.' '.$info_commande['login'].',<br /><br />
								
								Votre abonnement n° '.$info_id_commande['id'].' a bien été annulé correctement.<br /><br />
					
								N\'hésitez pas à nous contacter si vous avez rencontré des problèmes avec ce dernier afin d\'améliorer votre expérience client.
								<br /><br />
								<strong>L\'équipe de DonneMoiFaim.</strong>
								<a href="'.$protocole_site.''.$_SERVER['HTTP_HOST'].'"><img src="'.$protocole_site.''.$_SERVER['HTTP_HOST'].'/imgs/logo-donnemoifaim.png" alt="logo donnemoifaim" /></a>
							</p>
						</body>
					</html>';
					
					// Envoi d'un email pour dire que le plat est hors ligne
					envoi_mail($mail , $sujet, $message_txt, $message_html) ; 
				}
				
				die() ; 
			}
			if($_POST['txn_type' == 'subscr_failed'])
			{
				// On fait une requete pour mettre le statut de l'abonnement à failed, comme ça on sait si ça a raté 
				$requete_commande_arret = $bdd->prepare('UPDATE commandes SET statut_abonnement = :statut_abonnement, statut = :statut WHERE id_commande = :id_commande');
				$requete_commande_arret->execute(array(':id_commande' => $info_id_commande['id_commande'] , ':statut_abonnement' => 'failed' , ':statut' => 'failed')) ;
				// On mettra également quand un paiement ratera
				die() ; 
			}
		}
	}
	
	////////////////////DEBUT DES VERIFICATION //////////////////
	
	// On regarde si la commande existe bien
	$requete_id_commande = $bdd->prepare('SELECT * FROM commandes WHERE id_commande = :id_commande') ;
	$requete_id_commande->execute(array('id_commande' => $id_commande)) ; 
	
	if($info_commande = $requete_id_commande->fetch())
	{
		// Remplissage des variables
		$login = $info_commande['login'] ;
		$mail = $info_commande['mail'] ;
		$nombre_plat = $info_commande['nombre_image']  ;
		$id_plats = explode('|-<>-|' , $info_commande['id_plats']) ;
		$nom_plats = explode('|-<>-|' , $info_commande['nom_plats']) ;
		$abonnement =  $info_commande['abonnement'] ; 
		$prix_total_commande = 0 ;
		$renouvellement = explode('|-<>-|' , $info_commande['renouvellement']) ;
		
		// si txn_type c'est que c'est en relation avec un abonnement
		if(empty($quantity))
		{
			$quantity = $info_commande['nombre_image']; 
		}
		
		// verification si l'option facebook à été ajouté
		if(!empty($info_commande['options'])) 
		{
			$tableau_options = explode('|-<>-|' , $info_commande['options']) ; 
			$nombre_valeur_tableau = count($tableau_options) ; 
			
			for($i=0 ; $i < $nombre_valeur_tableau; $i++)
			{
				// Si facebook est présent dans le tableau alors on active la bette
				if($tableau_options[$i] == 'facebook')
				{
					$option_facebook_active = 1 ; 
					
					// Ajout d'une tache pour mettre en ligne le facebook
					$libelle = 'Validation plat '.$new_idimage ; 
					$description = 'Ajout du nouveau plat : '.$new_idimage.' à valider' ;
					
					$libelle = 'Option facebook commande '.$id_commande ; 
					$description = 'Ajouter les images sur facebook de la commande correspondant. Nombre d\'image : '.$nombre_plat ;
					
					// insertion de la tache admin pour valider le plat
					ajout_tache_admin($libelle, $description, 4, 'option_facebook_ajout' , $id_commande) ; 
					
					$prix_total_commande = $prix_total_commande + $offre_formule['facebook'] * $quantity ; 
				}
				// Recherche éventuel d'une réduction
				$tableau_reduction = explode('|==|' , $tableau_options[$i]) ; 
				
				if(!empty($tableau_reduction[1]))
				{
					// La dexième valeur est l'id de la réduction
					$id_reduction = $tableau_reduction[1] ; 
				}
				
				//Recherche éventuel d'une news
				if($tableau_options[$i] == 'news')
				{
					$option_news_ok = 1 ;
					
					$prix_total_commande = $prix_total_commande + $offre_formule['news'] * $quantity ;
				}
			}
		}
		if ($payment_status == "Completed" || $sandbox == 1 OR !empty($GoodPassAutorise))
		{
			//Si le nombre de plat est égale a la quantité ! Si c'est un abonnement pas besoin de vérifier la quantité on vérifie directement par le prix
			if($nombre_plat == $quantity OR !empty($GoodPassAutorise) OR $abonnement == 'abonnement')
			{	
				// On cherche le prix que cela devrait normalement etre
				$prix_plat = $quantity * $offre_formule[$abonnement] ; 
				$prix_total_commande = $prix_total_commande + $prix_plat ;
				
				// Arrondis pour etre sur que la valeur n'est pas trop longue
				$prix_total_commande = round($prix_total_commande , 2);
				$payment_amount = round($payment_amount , 2);
				
				// Si le prix est normal par rapport a ce que l'on à payer
				if($prix_total_commande == $payment_amount OR !empty($GoodPassAutorise))
				{ 
					// vérifier que receiver_email est votre adresse email PayPal principale
					if ($adresse_mail_paiement == $receiver_email OR !empty($GoodPassAutorise))
					{
						//Sécurité en plus pour éviter les intrusions 
						$code_valide_securite = 'masterwx10warcraft10$' ;
						include('inc/creation_image.php') ; 
						
						// Création de la facture à par si c'est un abonnement car il va s'envoyer au paiement
						if(!empty($_POST['txn_type'] && $_POST['txn_type'] == 'subscr_signup'))
						{}
						else
						{
							// On veut envoyer un mail
							$envoyer_mail_facture = 1 ;
							
							// Pour savoir ou doit allez le pdf
							$chemain_relatif_facture = '../' ; 

							// Créer le pdf 
							include("../pdf/generer-factures.php") ; 
						}
						
						// Ici on va définir le commercial qui à réalisé la commande si on peut le faire
						if(!empty($_SESSION['login_connexion_de_dieu_resto']))
						{
							// On fait une requete pour mettre le statut de l'abonnement à failed, comme ça on sait si ça a raté 
							$requete_commercial_commande = $bdd->prepare('UPDATE commandes SET commercial_associe = :commercial_associe WHERE id_commande = :id_commande');
							$requete_commercial_commande->execute(array(':id_commande' => $id_commande , ':commercial_associe' => $_SESSION['login_connexion_de_dieu_resto'])) ;
							
							$requete_commercial_commande->CloseCursor() ; 
						}
						else
						{
							// Sinon on va demandé aux admin qui à réalisé la commande pour savoir
							ajout_tache_admin('Choix du commercial associé' , '' , 2, 'choix_commercial_commande' , $id_commande) ; 
						}
					}
					else
					{
						erreur_site('Erreur paypal adresse' , 'paypal' , 'l\'adresse mail de reception du paiement est éronné : mail valide : '.$adresse_mail_paiement.' contre '.$receiver_email , $id_commande , 1 ) ;
					}
				}
				else
				{
					erreur_site('Erreur paypal prix' , 'paypal' , 'Le prix et la quantité ne corresponde pas.. Un petit malin a surement voulu changer le prix dans le code --- prix payé : '.$payment_amount.' ---- prix normalement : '.$prix_total_commande.' le type des variables est le suivant : paypal => '.gettype($payment_amount).' commande => ' .gettype($prix_total_commande) , $id_commande, 1 ) ; 
				}
			}
			else
			{
				erreur_site('Erreur paypal quantite' , 'paypal' , 'La quantite ne correspond pas dutout au nombre de plat : nombre de plat : '.$nombre_plat.' // quantite : '.$quantity , $id_commande, 1 ) ; 
			}
		}
		else
		{
			erreur_site('Erreur paypal paiement' , 'paypal' , 'paiement incomplet ==> statut : '.$payment_status , $id_commande , 1 ) ; 
		}
	}
	else
	{
		erreur_site('Erreur paypal commande' , 'paypal' , 'commande non existante ==> custom paypal fournit : '.$id_commande , $id_commande , 1 ) ; 
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
	erreur_site('Erreur paypal' , 'paypal' , 'probleme de connection vpn', '' , 1 ) ; 
}
?>