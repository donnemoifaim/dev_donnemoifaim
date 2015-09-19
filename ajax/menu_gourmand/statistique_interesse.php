<?php 
session_start();

include ('../../include/configcache.php') ;

// Si le login compte resto est le meme que celui du compte courant alors ça ne sert a rien de regarder

if($_SESSION['login_deja_stat_interesse'] == $_SESSION['login'])
{}
else
{
	// On regarde si on à déjà comptabilisé ou pas cette stat 
	if(!isset($_SESSION['login_deja_stat_interesse']))
	{
		$_SESSION['login_deja_stat_interesse'] = array($_SESSION['login_client_actuel']) ; 
	}
	else
	{
		if(in_array($_SESSION['login_client_actuel'] , $_SESSION['login_deja_stat_interesse']))
		{
			$ne_pas_compter = 1 ; 
		}
		else
		{
			// on à juste à rentrer la nouvelle valeur 
			array_push($_SESSION['login_deja_stat_interesse'] , $_SESSION['login_client_actuel']) ; 
		}
	}
	// On rajoute 1 au stat
	if(!isset($ne_pas_compter))
	{
		$req_statistique = $bdd->prepare('UPDATE plat SET nombre_interesse = nombre_interesse + 1 WHERE id = :id') ;
		$req_statistique->execute(array(':id' => $_SESSION['image_actuelle']));

		$req_statistique->closeCursor();
	}
}