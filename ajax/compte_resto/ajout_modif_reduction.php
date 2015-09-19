<?php
session_start();

include ('../../include/configcache.php') ;
include ('../../include/fonctions.php') ;

if(!empty($_SESSION['login']))
{
	if(!empty($_SESSION['token']) && !empty($_POST['token']) && $_POST['token'] == $_SESSION['token'])
	{
		if(!empty($_POST['intitule_reduction']))
		{
			$login = $_SESSION['login'] ; 
			$libelle = $_POST['intitule_reduction'] ;
			
			// On check si l'entrée que l'on va inséré n'existe pas déjà
			$requete_reduction = $bdd->prepare("SELECT id,libelle FROM reductions WHERE login = :login && libelle = :libelle");
			$requete_reduction->execute(array(':login' => $login , ':libelle' => $libelle)) ;
			
			// Si l'intitulé existe on lance une erreur
			if($donnees_reduction = $requete_reduction->fetch())
			{
				// Si c'est le meme que l'on essais de modifier alors on ne fait rien
				if(!empty($_POST['id_modif_reduction']) && $donnees_reduction['id'] == $_POST['id_modif_reduction'])
				{}
				else
				{
					$erreur = '<span class="erreur">Réduction déjà existante<span><br />' ; 
				}
			}
			else
			{
				// Si c'est un ajout
				if(!isset($_POST['id_modif_reduction']))
				{
					ajout_reduction($login , $libelle) ;
					
					// On récupère l'id de la réduction
					$id_reduction = $bdd->lastInsertId() ; 
				}
				else
				{
					// On modifie la réduction existante
					$requete_reduction = $bdd->prepare("UPDATE reductions SET libelle = :libelle WHERE id = :id_reduction && login = :login");
					$requete_reduction->execute(array(':id_reduction' => $_POST['id_modif_reduction'] , ':libelle' => $libelle, ':login' => $login)) ;
				}
			}
		}
		else
		{
			$erreur = '<span class="erreur">Intitulé manquant<span><br />' ; 
		}
	}
	else
	{
		$erreur = '<span class="erreur" >'.$erreur_faille_csrf.'</span>' ;
	}
}
if(!isset($erreur))
{
	// Si il n'y à pas d'erreur on met la variale à 0
	$erreur = 0;
}
else
{
	// Sinon on met celle de la réduction à null
	$id_reduction = '' ; 
}

$tableau_json = array('erreur' => $erreur , 'id_reduction' => $id_reduction) ; 

echo json_encode($tableau_json) ; 