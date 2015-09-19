<?php
session_start() ; 

include('../../include/fonctions.php');
include('../../include/configcache.php');
include('../../include/offre_tarrif.php') ; 

$type_upload = 'ajout-de-plat' ; 

// Reorganisation id des images -- utile pour la maintenance pour repérer facilement les sessions
reorganisation_session_image_upload($type_upload) ; 

$ensemble_id_plat = '' ; 
$ensemble_nom_plat = '' ;

// CREATION de l'ensemble des id des plat à insérer
for($i = 0 ; $i < $_SESSION['nombre_image'.$type_upload] ; $i++)
{
	// SI c'est le dernier id
	if($i == $_SESSION['nombre_image'.$type_upload] - 1)
	{
		$ensemble_id_plat = $ensemble_id_plat.''.$_SESSION['idimage_'.$i.''.$type_upload] ;
	}
	else
	{
		$ensemble_id_plat = $ensemble_id_plat.''.$_SESSION['idimage_'.$i.''.$type_upload].'|-<>-|' ; 
	}
}
// CREATION de l'ensemble des nom des plats à insérer
for($i = 0 ; $i < $_SESSION['nombre_image'.$type_upload] ; $i++)
{
	if($i == $_SESSION['nombre_image'.$type_upload] - 1)
	{
		$ensemble_nom_plat = $ensemble_nom_plat.''.$_SESSION['nom_image_upload'.$i.''.$type_upload] ;
	}
	else
	{
		$ensemble_nom_plat = $ensemble_nom_plat.''.$_SESSION['nom_image_upload'.$i.''.$type_upload].'|-<>-|' ;
	}
}

$abonnement = $_POST['abonnement'] ; 
$prix_total = $offre_formule[$abonnement] * $_SESSION['nombre_image'.$type_upload] ;

$prix_avant_option = $prix_total ; 

// verification si l'option facebook à été ajouté
if(!empty($_POST['options'])) 
{
	$tableau_options = explode('|-<>-|' , $_POST['options']) ; 
	$nombre_valeur_tableau = count($tableau_options) ; 
	
	for($i=0 ; $i < $nombre_valeur_tableau; $i++)
	{
		// Si facebook est présent dans le tableau alors on active la bette
		if($tableau_options[$i] == 'facebook')
		{
			$prix_total = $prix_total + $offre_formule['facebook'] * $_SESSION['nombre_image'.$type_upload];
		}
		// Si l'offre event est présent dans le tableau alors on active la bette
		if($tableau_options[$i] == 'news')
		{
			$prix_total = $prix_total + $offre_formule['news'] * $_SESSION['nombre_image'.$type_upload];
		}
	}
}

// Si c'est un renouvellement on note l'id du plat qui va renouveller l'ancienne valeur ainsi que le nom du fichier
if(!empty($_SESSION['idimage_renouvelle']) && !empty($_SESSION['ancienne_idimage_renouvelle']))
{
	$renouvellement = $_SESSION['idimage_renouvelle'].'|-<>-|'.$_SESSION['ancienne_idimage_renouvelle'] ; 
}
else
{
	$renouvellement = '' ; 
}

// Si l'abonnement est abonnement alors il correspond à l'id 4

if($abonnement == 'abonnement')
{
	$prix_premier_mois = $prix_total ;
	$prix_total = $prix_avant_option ; 	
}
else
{
	$prix_premier_mois = $prix_total ; 
}

// Création de l'id image
$requete_commande = $bdd->prepare('INSERT INTO commandes(login, id_plats,nom_plats, abonnement, options, mail, prix_total, prix_premier_mois, id_commande, nombre_image , renouvellement, date_ajout) VALUES (:login, :id_plats,:nom_plats, :abonnement, :options, :mail, :prix_total, :prix_premier_mois, :id_commande, :nombre_image, :renouvellement , :date_ajout )') ;
$requete_commande->execute(array(':login' => $_SESSION['login'] , ':id_plats' => $ensemble_id_plat, ':nom_plats' => $ensemble_nom_plat ,  ':abonnement' => $abonnement, ':options' =>  $_POST['options'] , ':mail' => $_POST['mail_qui_recoit_facture'], ':prix_total' => $prix_total, ':prix_premier_mois' => $prix_premier_mois ,  'id_commande' => $_POST['id_commande'], 'nombre_image' => $_SESSION['nombre_image'.$type_upload], ':renouvellement' => $renouvellement , ':date_ajout' => time() )) ;
