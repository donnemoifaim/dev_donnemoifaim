<?php 
session_start();

include ('../../include/configcache.php') ;
include ('../../include/fonctions.php') ;
include ('../../include/points-dmf.php') ;

if (!empty($_SESSION['login_visiteur']))
{
	if(!empty($_SESSION['token_visiteur']) && !empty($_GET['token_visiteur']) && $_GET['token_visiteur'] == $_SESSION['token_visiteur'])
	{
		$id_plat = $_SESSION['images'] ;
		
		// On récupère les j'aime actuels
		$req_jaime_check = $bdd->prepare('SELECT jaime FROM utilisateur WHERE login = :login_visiteur') ;
		$req_jaime_check->execute(array(':login_visiteur' => $_SESSION['login_visiteur'])) ;
		
		if($info_jaime_check = $req_jaime_check->fetch())
		{
			$tableau_jaime_check = explode(',' , $info_jaime_check['jaime']) ; 
			
			$taille_tableau = count($tableau_jaime_check) ; 
			
			// Check si l'id du jaime est présent dans le tableau ou pas
			for($i=0 ; $i < $taille_tableau; $i++)
			{
				if($tableau_jaime_check[$i] == $id_plat)
				{
					$erreur = 1 ; 
				}
			}
			
			if(!isset($erreur))
			{
				// Si aucune erreur on rajoute le j'aime
				
				$id_plat = ','.$id_plat ;
				
				$req_jaime = $bdd->prepare('UPDATE utilisateur SET jaime = CONCAT(jaime ,  \''.$id_plat.'\') WHERE login = :login_visiteur') ;
				$req_jaime->execute(array(':login_visiteur' => $_SESSION['login_visiteur'])) ;
				
				$req_jaime->closeCursor();
				
				// On lui donne des points pour avoir aimé le plat
				$ajout_points = $point_dmf_visiteur['aimer_plat'] ; 
		
				ajouter_point_utilisateur($ajout_points) ; 
				
				echo 'ok_vote' ; 
			}
			else
			{
				// La ca veut dire que la personne aime déjà le plat
				echo 'ok_vote' ; 
			}
		}
	}
	else
	{
		echo '<span class="erreur" >'.$erreur_faille_csrf.'</span>' ;
	}
} 
else
{ 
	echo 'non_connecte' ; 
}
