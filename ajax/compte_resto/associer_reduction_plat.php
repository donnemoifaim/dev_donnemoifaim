<?php
session_start();

include ('../../include/configcache.php') ;
include ('../../include/fonctions.php') ; 

if(!empty($_SESSION['login']))
{
	if(!empty($_SESSION['token']) && !empty($_POST['token']) && $_POST['token'] == $_SESSION['token'])
	{
		if(!empty($_POST['id_plat']))
		{
			// On check si la réduction à déjà l'id du plat en lui
			if(is_numeric($_POST['id_plat']))
			{
				if(is_numeric($_POST['id_reduction']))
				{
					$id_plat = $_POST['id_plat']; 
					$id_reduction = $_POST['id_reduction'] ;
					
					// Check si l'id du plat n'as pas déjà été rentré
					$requete_reduction = $bdd->prepare("SELECT idimage FROM reductions WHERE id = :id && login = :login ");
					$requete_reduction->execute(array(':login' => $login , ':id' => $id_reduction)) ;

					if($donnees_reduction = $requete_reduction->fetch())
					{
						// Permet de savoir si la réduction est associé à ce plat
						$tableau_idimage = explode(',' , $donnees_reduction['idimage']);
						
						// Si c'est déjà associé on empeche
						if(in_array($id_plat, $tableau_idimage))
						{
							$reduction_deja_associe = 1 ; 
						}
					}
					
					// On check si la réduction est déjà associé si elle l'est pas c'est bon
					if(!isset($reduction_deja_associe))
					{
						lie_reduction_plat($id_plat , $id_reduction) ; 
					}
					else
					{
						echo '<span>La réduction est déjà associée à ce plat</span><br />' ;
					}
					
				}
				else
				{
					echo '<span>Erreur id reduction non numérique. Si le problème persiste contactez-nous</span>' ;
				}
			}
			else
			{
				echo '<span>Erreur id du plat non numérique. Si le problème persiste contactez-nous</span>' ;
			}
		}
		else
		{
			echo '<span>Erreur, id manquant. Si le problème persiste contactez-nous</span>' ;
		}
	}
	else
	{
		echo '<span class="erreur" >'.$erreur_faille_csrf.'</span>' ;
	}
}