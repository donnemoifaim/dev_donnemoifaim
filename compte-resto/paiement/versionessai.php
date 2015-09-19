<?php
session_start();

include('../../include/configcache.php') ; 
include('../../include/fonctions.php') ; 
include('../../include/compte_acces.php') ; 
include('../../include/offre_tarrif.php') ;

// Réécriture
// Réorganisation des nom des images et idimage , pas indispensable mais en cas de maintenance permet de repérer plus facilement les sessions
$nouvelle_id_image = 0 ;

$type_upload = 'ajout-de-plat' ; 

reorganisation_session_image_upload($type_upload) ; 

if(!empty($_SESSION['nombre_image'.$type_upload]) AND !empty($_SESSION['idimage_0'.$type_upload])) 
{

}
else
{
	header('location:../../gestion_erreur/erreur_ajout_plat.php') ;
	die();
}	
//Partis admin pour pouvoir rajouter des plats sans payer
if(!empty($_SESSION['login']) AND in_array($_SESSION['login'], $pseudo_admin))
{
	if($_SESSION['nombre_image'.$type_upload] == 1)
	{
		$_SESSION['offre_decouverte'] = 1 ;
		$_SESSION['offre_decouverte_admin'] = 1 ; 
	}
}
elseif($_SESSION['nombre_image'.$type_upload] == 1)
{
	$requete_offre = $bdd->prepare('SELECT offre_speciale FROM client WHERE login = :login');
	$requete_offre->execute(array('login' => $_SESSION['login']));
	if($info_offre = $requete_offre->fetch())
	{
		if($info_offre['offre_speciale'] == 'decouverte')
		{
			$_SESSION['offre_decouverte'] = 1 ;
		}
		else
		{
			if(!empty($_SESSION['offre_decouverte']))
			{
				unset($_SESSION['offre_decouverte']) ;
			}
		}
	} 
}
else
{
	if(!empty($_SESSION['offre_decouverte']))
	{
		unset($_SESSION['offre_decouverte']) ;
	}
}
// Offre d'essais
if(!empty($_SESSION['offre_decouverte']))
{
	$login = $_SESSION['login'] ;
	$mail = $_SESSION['mail'] ;
	$nombre_plat = 1  ;
	$id_plats = explode('|-<>-|' , $_SESSION['idimage_0'.$type_upload]) ;
	$nom_plats = explode('|-<>-|' , $_SESSION['nom_image_upload0'.$type_upload]) ;
	$offre_version_essais = 1 ; 
	if(!empty($_SESSION['offre_decouverte_admin']))
	{
		// Si on l'ajoute nous même le plat est valable 1 ans 
		$abonnement  = $_SESSION['abo'] = 3 ;
	}
	else
	{
		// Sinon il est valable 1 mois
		$abonnement  = $_SESSION['abo'] = 1 ;
	}
	
	// On met la sessiona actuelle d'offre découverte à 0 pour que l'offre n'apparaisse plus
	$_SESSION['offre_speciale'] = 0 ; 
	
	// Code de vérification de sécurité
	$code_valide_securite = 'masterwx10warcraft10$' ;
	include('inc/creation_image.php') ;
	
	// Supression de toute les session de plat qui ont été créé
	supp_session_image($type_upload); 

	header('location:finalisation.php') ; 
}
?>