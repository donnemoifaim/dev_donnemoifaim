<?php 
session_start();

include ('../../include/fonctions.php') ;
include ('../../include/configcache.php') ;
include ('../../include/points-dmf.php') ;

if (!empty($_SESSION['login_visiteur']))
{
	// On regarde si la personne à déjà aimé facebook 
	if(!empty($_SESSION['follow_twitter']))
	{
		if($_SESSION['follow_twitter'] == 0)
		{
			$non_follow_twitter = 1 ; 
		}
	}
	else
	{
		// On recupère le facebook de la personne si il à une ancienne "version" du site 
		$req_savoir_si_jaime_facebook = $bdd->prepare('SELECT follow_twitter FROM utilisateur WHERE login = :login_visiteur') ;
		$req_savoir_si_jaime_facebook->execute(array(':login_visiteur' => $_SESSION['login_visiteur'])) ;
		
		if($info_si_jaime_facebook = $req_savoir_si_jaime_facebook->fetch())
		{
			// Si c'est 0 c'est qu'il à jamais aimé
			if($info_si_jaime_facebook['follow_twitter'] ==  0)
			{
				$non_follow_twitter = 1 ; 
			}
		}
	}
	
	if(!empty($non_follow_twitter))
	{
		$ajout_points = $point_dmf_visiteur['like_facebook'] ; 
		
		ajouter_point_utilisateur($ajout_points) ; 
		
		// On dit que l'on à déjà aimé la page facebook
		$req_jaime_facebook = $bdd->prepare('UPDATE utilisateur SET follow_twitter = 1 WHERE login = :login_visiteur') ;
		$req_jaime_facebook->execute(array(':login_visiteur' => $_SESSION['login_visiteur'])) ;
		
		// On créer la nouvelle session à 1 
		$_SESSION['follow_twitter'] = 1 ; 
		
		echo $ajout_points ; 
	}
	else
	{
		echo 'deja_follow' ;
	}
}
else
{
	echo 'non_connecte' ; 
}