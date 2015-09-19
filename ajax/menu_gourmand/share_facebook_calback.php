<?php 
session_start();

include ('../../include/fonctions.php') ;
include ('../../include/configcache.php') ;
include ('../../include/points-dmf.php') ;

if (!empty($_SESSION['login_visiteur']))
{
	// On recup�re le nombre de post et la derni�re fois que �a � �t� fait, c'est simple chaque jour on � le droit de faire 2 posts
	$req_savoir_si_post_facebook = $bdd->prepare('SELECT post_facebook FROM utilisateur WHERE login = :login_visiteur') ;
	$req_savoir_si_post_facebook->execute(array(':login_visiteur' => $_SESSION['login_visiteur'])) ;
	
	if($info_si_post_facebook = $req_savoir_si_post_facebook->fetch())
	{
		// Si le post facebook est vide c'est que aucun post n'a encore �t� mis donc on peut y allez 
		if($info_si_post_facebook['post_facebook'] ==  '')
		{
			$ok_pour_post = 1 ; 
		}
		else
		{
			// On explode pour savoir le nombre de post qui ont �t� r�alis�
			$nombre_post = explode('||' , $info_si_post_facebook['post_facebook']) ;
			
			// Si le nombre est d�j� de 2 alors il faut voir si correspond au jour suivant 
			if($nombre_post[0] < 2)
			{
				$ok_pour_post = 1 ;
			}
			else
			{
				// On regarde si c'est bien 24h apr�s, 86 400 correspondant au nombre de seconde dans une journ�e
				if(time() - $nombre_post[1] >= 86400)
				{
					$ok_pour_post = 1 ; 
				}
			}
		}
	}
	
	if(!empty($ok_pour_post))
	{
		$ajout_points = $point_dmf_visiteur['share_facebook'] ; 
		
		ajouter_point_utilisateur($ajout_points) ; 
		
		// ON regarde le nombre de post 
		if(!empty($nombre_post[0]))
		{
			if($nombre_post[0] >= 2)
			{
				$nombre_post_facebook = 1 ; 
			}
			else
			{
				$nombre_post_facebook = $nombre_post[0] + 1 ;
			}
		}
		else
		{
			$nombre_post_facebook = 1 ; 
		}
		
		$post_facebook = $nombre_post_facebook.'||'.time() ; 
		
		// On dit que l'on � d�j� aim� la page facebook
		$req_post_facebook = $bdd->prepare('UPDATE utilisateur SET post_facebook = :post_facebook WHERE login = :login_visiteur') ;
		$req_post_facebook->execute(array(':login_visiteur' => $_SESSION['login_visiteur'] , ':post_facebook' => $post_facebook)) ;
		
		// On cr�er la nouvelle session � 1 
		$_SESSION['jaime_facebook'] = 1 ; 
		
		echo $ajout_points ; 
	}
	else
	{
		echo 'deja_partage' ;
	}
}
else
{
	echo 'non_connecte' ; 
}