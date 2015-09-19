<?php

// On ouvre le dosser à supprimer le contenu
if($dossier = opendir('../temporaire/'))
{
	// On parcour 1 à 1 les élements grace à readdir
	while($fichier = readdir($dossier))
	{
		// Ici on va faire en sorte de ne pas lire les chemins ( apparaement c'est mieux )
		if($fichier != '.' && $fichier != '..')
		{
			// On ne fait rien si c'est un dossier
			if(is_dir($fichier))
			{}
			else
			{
				// On regarde depuis combien de temps ca fait que le fichier est ici, si ca fait un mois on juge que c'est bon l'utilisateur n'en à plus besoin
				if($derniere_utilisation = filemtime('../temporaire/'.$fichier))
				{
					// Si c'est plus grand ou égale à 2629743 ( 1 mois en seconde ) c'est qu'on peut le supprimer
					if(time() - $derniere_utilisation >= 2629743 )
					{
						// detruction du chier
						unlink('../temporaire/'.$fichier) ; 
						//destruction aussi dans le miniature et mobile
						unlink('../temporaire/miniature/'.$fichier) ; 
						unlink('../temporaire/mobiles/'.$fichier) ;  
					}
				}
				else
				{
					echo 'erreur filemtime fichier : '. $fichier ; 
				}
			}
		}
	}
	
	// On ferme la connexion quand on en plus besoin
	closedir($dossier);
	
	echo 'dossier temporaire vidé.' ; 
}
else
{
	echo 'erreur chemin dossier' ; 
}