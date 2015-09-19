<?php
session_start() ; 

include ('../../include/configcache.php') ;
include ('../../include/fonctions.php') ;

if(!isset($_SESSION['login_visiteur']))
{
	$connection = 'non_connecte' ;
	$abonnement = 'non_abo' ;
	$access_reduction = 'non' ;
}
else
{
	$connection = 'connecte' ;
	
	// On va checker à nouveau l'abonnement visiteur question de sécurité
	$requete_user = $bdd->prepare('SELECT abo, date_abo FROM utilisateur WHERE login = :login') ; 
	$requete_user->execute(array(':login' => $_SESSION['login_visiteur'])) ;
	
	if($info_user = $requete_user->fetch())
	{
		verification_abonnement_visiteur($info_user['abo'], $info_user['date_abo'], $_SESSION['login_visiteur']) ;
	}
	
	// Si au final la session est toujours à zéro alors pas connecté
	if($_SESSION['abonnement_visiteur'] == 0)
	{
		$abonnement = 'non_abo' ; 
	}
	else
	{
		$abonnement = 'abo' ;
	}
	
	// Si il existe un laisser passé pour les réductions 
	if(!empty($_SESSION['laisser_passer_reduction']) || $abonnement == 'abo')
	{
		// On dit que la personne peut prendre la réduction 
		$access_reduction = 'ok' ; 
	}
	else
	{
		$access_reduction = 'non' ; 
	}
}

$tableau_json = array('connection' => $connection , 'abonnement' => $abonnement , 'access_reduction' => $access_reduction) ;

echo json_encode($tableau_json) ;  