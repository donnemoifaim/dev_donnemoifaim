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
			if(!empty($_GET['id_unique']))
			{
				// On regarde le type
				if(!empty($_GET['type']))
				{
					// On valide l'image
					if($_GET['statut'] == 'ok')
					{
						// Si c'est un validation de plat
						if($_GET['type'] == 'validation_plat')
						{
							if(!empty($_GET['statut']))
							{
								
								// Requete permettant de valider le plat 
								$requete_valide = $bdd->prepare('UPDATE plat SET etat = 1 WHERE idimage = :idimage') ; 
								$requete_valide->execute(array(':idimage' => $_GET['id_unique']));
								
										// Alimentation du sitemap 

									// 1 : on ouvre le fichier
									$monfichier = fopen('../../sitemap.xml', 'r+');
									// Replacer le curseur au bon endroit
									fseek($monfichier, -10, SEEK_END);
									// On écrit dans le fichier
fputs($monfichier, '
	<url>
		<loc>https://donnemoifaim.fr/'.$_GET['id_unique'].'.html</loc>
		<changefreq>daily</changefreq>
	</url>
</urlset>') ;
									// 3 : quand on a fini de l'utiliser, on ferme le fichier
									fclose($monfichier);
							}
							else
							{
								echo '<span class="erreur">Statut manquant, contactez le développeur.</span>' ;
							}
						}
						
						if($_GET['type'] == 'validation_news')
						{
							$requete_valide = $bdd->prepare('UPDATE news SET statut = 1 WHERE id = :id') ; 
							$requete_valide->execute(array(':id' => $_GET['id_unique']));
						}
					}
					// Sinon on la met en hors ligne sans pour autant la supprimer
					elseif($_GET['statut'] == 'non')
					{
						// Si c'est un validation de plat
						if($_GET['type'] == 'validation_plat')
						{
							// On va chercher l'idimage du plat en cour
							$requete_plat = $bdd->prepare('SELECT idimage FROM plat WHERE id = :id') ;
							$requete_plat->execute(array(':id' => $_GET['id_unique'])) ;

							if($info_plat = $requete_plat->fetch())
							{
								archivage_plat($info_plat['idimage'] , '../') ; 
							}
						}
						// Si c'est un validation de plat
						if($_GET['type'] == 'validation_news')
						{
							// 3 correspond a refuser
							$requete_valide = $bdd->prepare('UPDATE news SET statut = 3 WHERE id = :id') ; 
							$requete_valide->execute(array(':id' => $_GET['id_unique']));
						}
					}
								
					// Dans tout les cas que ce soit un refus ou non on met la tache comme faite
					if($_GET['type'] == 'erreur_site')
					{
						// On doit valider la tache aussi
						$requete_valide_tache = $bdd->prepare('UPDATE erreur_site SET statut = 1 WHERE id = :id_tache') ; 
						$requete_valide_tache->execute(array(':id_tache' => $_GET['id_tache']));
					}
					else
					{
						// On doit valider la tache aussi
						$requete_valide_tache = $bdd->prepare('UPDATE tache_admin SET statut = 1 WHERE id = :id_tache') ; 
						$requete_valide_tache->execute(array(':id_tache' => $_GET['id_tache']));
					}
				}
				else
				{
					echo '<span class="erreur">Erreur type, contactez le développeur.</span>' ;
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