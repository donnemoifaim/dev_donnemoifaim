<?php 
session_start();

include ('../../include/configcache.php') ;
include ('../../include/fonctions.php') ;

if (!empty($_SESSION['login_visiteur']) AND $_POST['idimage'])
{
	if(!empty($_SESSION['token_visiteur']) && !empty($_POST['token_visiteur']) && $_POST['token_visiteur'] == $_SESSION['token_visiteur'])
	{
		// On récupère l'id de l'image à supprimer
		$id_jaime_supp = $_POST['idimage'] ; 
		
		// On resort tout les jaimes pour pouvoir supprimer le spécifique
		$req_jaime_compte = $bdd->prepare('SELECT jaime FROM utilisateur WHERE login = :login') ;
		$req_jaime_compte->execute(array(':login' => $_SESSION['login_visiteur']));
		
		if($info_jaime_compte = $req_jaime_compte->fetch())
		{
			$tableau_jaime_check = explode(',' , $info_jaime_compte['jaime']) ; 
			$taille_tableau = count($tableau_jaime_check) ; 
			
			// Check si l'id du jaime est présent dans le tableau ou pas
			for($i=0 ; $i < $taille_tableau; $i++)
			{
				// Si l'id du jaime que l'on veut supprimé est la alors on le vide
				if($tableau_jaime_check[$i] == $id_jaime_supp)
				{
					unset($tableau_jaime_check[$i]) ;
				}
			}
			
			// On recréer le nouveau tableau sans l'ancienne valeur
			$tableau_nouveau_jaime = implode(',' , $tableau_jaime_check) ; 
		}
		
		$req_jaime_compte->closeCursor();
		
		// On rentre les nouveaux j'aimes 
		$req_jaime_update = $bdd->prepare('UPDATE utilisateur SET jaime = :jaime WHERE login = :login') ;
		$req_jaime_update->execute(array(':login' => $_SESSION['login_visiteur'] , ':jaime' => $tableau_nouveau_jaime));
		
		$req_jaime_update->closeCursor();
	}
	else
	{
		echo '<span class="erreur" >'.$erreur_faille_csrf.'</span>' ;
	}
}
else
{
	echo '<span class="erreur">'.$erreur_interne.'</span>' ;
}