<?php
session_start();

include ('../../include/configcache.php') ;
include ('../../include/fonctions.php') ;

if(!empty($_SESSION['login']))
{
	if(!empty($_SESSION['token']) && !empty($_POST['token']) && $_POST['token'] == $_SESSION['token'])
	{
		if(!empty($_POST['id_supp_reduction']))
		{	
			$requete_reduction_plat = $bdd->prepare("SELECT id,idimage FROM reductions WHERE id = :id_reduction && login = :login");
			$requete_reduction_plat->execute(array(':login' => $_SESSION['login'] , ':id_reduction' => $_POST['id_supp_reduction'])) ;
			
			// On enleve tout les id_reduction présent sur les plats
			if($donnees_reduction = $requete_reduction_plat->fetch())
			{
				// Permet de savoir si la réduction est associé à ce plat
				$tableau_idimage = explode(',' , $donnees_reduction['idimage']);
				$taille_tableau = count($tableau_idimage) ; 
				
				for($i=0; $i < $taille_tableau; $i++)
				{
					$requete_plat_reduction = $bdd->prepare('UPDATE plat SET id_reduction = :id_reduction WHERE id = :id_plat');
					$requete_plat_reduction->execute(array(':id_plat' => $tableau_idimage[$i] , ':id_reduction' => 0));
				}
			}
			
			// Supression de la réduction
			$requete_reduction = $bdd->prepare("DELETE FROM reductions WHERE id = :id_reduction && login = :login");
			$requete_reduction->execute(array(':login' => $_SESSION['login'], ':id_reduction' => $_POST['id_supp_reduction'])) ;
		}
	}
	else
	{
		echo '<span class="erreur" >'.$erreur_faille_csrf.'</span>' ;
	}
}