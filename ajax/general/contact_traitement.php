<?php 
include('../../include/configcache.php') ;
include('../../include/fonctions.php') ;

$regex_mail = regex_mail() ; 

if (!empty($_POST['mailcontact'])  AND preg_match($regex_mail, $_POST['mailcontact']))
{
	$mailcontact = $_POST['mailcontact'] ;
}
else
{
	echo '<span>- Entrez une adresse email valide</span><br />' ;
	$erreur = 1 ;
}

if (!empty($_POST['naturemail']))
{
	$naturemail = $_POST['naturemail'] ;
}
else
{
	echo '<span>- Entrez le motif de votre message</span><br />' ;
	$erreur = 1 ;
}

if (!empty($_POST['choixcontact']))
{
	$choixcontact = $_POST['choixcontact'] ;
}

if (!empty($_POST['contenu_mail']))
{
	$contenu_mail = $_POST['contenu_mail'] ;
}
else
{
	echo '<span>- Entrez le contenu de votre message</span><br />' ;
	$erreur = 1 ;
}

if (!isset($erreur))
{
	// Vu que le serveur doit envoyer un message à lui meme on va définir le mail émeteur que l'on récupère en global dans la fonction 
	$mail_emeteur = $mailcontact; 
	$nom_emeteur = $_POST['prenom_nom'] ;
	
	envoi_mail($mail_contact_site, $choixcontact.' - '.$naturemail, $contenu_mail , $contenu_mail);  
}
