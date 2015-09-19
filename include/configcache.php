<?php
// si on est sur la version test 
if($_SERVER['HTTP_HOST'] == 'dev.donnemoifaim.fr')
{
	try
	{
	  // On se connecte à¡ySQL
	  $bdd = new PDO('mysql:host=localhost;dbname=mxxbhatw_test', 'mxxbhatw_test', 'Ai7BHHVHu,Pt', array (PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
	}
	catch(Exception $e)
	{
	  // En cas d'erreur, on affiche un message et on arrë´  le tout
	  die('Erreur : '.$e->getMessage());
	}
}
else
{
	try
	{
	  // On se connecte à¡ySQL
	  $bdd = new PDO('mysql:host=localhost;dbname=mxxbhatw_bouf', 'mxxbhatw_kevin', '$$a^-0XR;pr{', array (PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
	}
	catch(Exception $e)
	{
	  // En cas d'erreur, on affiche un message et on arrë´  le tout
	  die('Erreur : '.$e->getMessage());
	}
}

$proprietaire_site_dmf = 'Kevin Lourenço' ;
$mail_contact_site = 'contact@donnemoifaim.fr' ;
$pseudo_admin = array('kevinleader1' , 'andrea-test');
$pseudo_commercial_dmf = array('kevinleader1') ;
$rue_site = '8 chemin de la roue';
$ville_site = 'herblay';
$code_postal_site = '95220';
$region_site = 'Ile-de-France' ;
$pays_site = 'France' ; 
$pays_site = 'France' ; 
$nom_entreprise_site = 'DonneMoiFaim' ;
$numero_site = '+336 95 25 20 76';
$numero_site_francais = '06 95 25 20 76' ; 
$contact_service_client = '+336 95 25 20 76' ;
$siret_entreprise_dmf = '79481170300014' ;

// Si c'est le site test on met l'adresse de sandbox de paypal
if($_SERVER['HTTP_HOST'] == 'dev.donnemoifaim.fr')
{
	$adresse_mail_paiement = 'donnemoifaim-facilitator@gmail.com' ;
	// Si c'est la version test on ne sécurise pas la connection sinon on la sécurise
	$protocole_site = 'http://' ;
	$mail_contact_site_test = 'lourenco.network@gmail.com' ;
	
	$nom_domaine_paypal = 'sandbox.paypal.com' ; 
}
else
{
	$adresse_mail_paiement = 'donnemoifaim@gmail.com' ;
	$protocole_site = 'https://' ;
	
	$nom_domaine_paypal = 'paypal.com' ; 
}
 
$cle_api_google = 'AIzaSyCO5z-jhHxeqrid6ntghqui9jR4avQntZo' ; 

// On regarde si c'est un robot qui vient visiter le site pour faire certaine action (ex: menu-gourmand ne pas faire apparaitre le tuto)
if(preg_match('/(bot|spider|yahoo)/i', $_SERVER[ "HTTP_USER_AGENT" ] ))
{
	$crawler = 1 ; 
}

// Code promo compte dmf 
$code_promo_inscription_compte_visiteur = 'B87J8A' ;
$nombre_code_promo_inscription_compte_visiteur = 1 ; 

// Phrase toute faites 

$erreur_faille_csrf = 'Oups ! Cette action n\'est actuellement pas disponible. Veuillez vous deconnecter et reconnecter pour avoir à nouveau accès à cette fonctionnalité.' ;
$erreur_interne = 'Oups ! Une erreur interne est survenue. Rechargez la page, si le problème persiste n\'hésitez pas à nous contacter.' ; 
$erreur_connexion_texte = 'Erreur, vous n\'êtes pas connecté !' ;