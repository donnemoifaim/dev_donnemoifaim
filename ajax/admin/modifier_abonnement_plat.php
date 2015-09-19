<?php

session_start() ; 

include('../../include/configcache.php') ; 
include('../../include/fonctions.php') ; 

// Vérification de connexion simple
if(!empty($_SESSION['login']))
{
	// Vérif de connexion admin
	if(!empty($_SESSION['admin']))
	{
		if(!empty($_SESSION['token']) && $_SESSION['token'] == $_GET['token'])
		{
			// On a affaire à une validation de plat
			if(!empty($_GET['id_plat']))
			{
				// On regarde le type
				if(!empty($_GET['abo']) && is_numeric($_GET['abo']))
				{
					// On doit valider la tache aussi
					$requete_valide_tache = $bdd->prepare('UPDATE plat SET abo = :abo WHERE id = :id_plat') ; 
					$requete_valide_tache->execute(array(':abo' => $_GET['abo'] , ':id_plat' => $_GET['id_plat'] ));
				}
				else
				{
					echo '<span class="erreur">Erreur abo, contactez le développeur.</span>' ;
				}
			}
			else
			{
				echo '<span class="erreur">Id unique manquant, si le problème persiste va voir le développeur.</span>' ;
			}
		}
		else
		{
			echo '<span class="erreur">'.$erreur_faille_csrf.'</span>' ; 
		}
	}
	else
	{
		echo '<span class="erreur">Rang admin nécessaire, augmentez le dans la base de données, au champs rang utilisateur.</span>' ; 
	}
}
else
{
	echo '<span class="erreur">'.$erreur_connexion_texte.'</span>' ; 
}