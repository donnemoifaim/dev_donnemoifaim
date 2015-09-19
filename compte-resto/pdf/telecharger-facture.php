<?php
session_start() ; 

include('../../include/configcache.php') ; 
include('../../include/fonctions.php') ; 

// Vérification de connexion simple
if(!empty($_SESSION['login']))
{
	if(!empty($_GET['id_facture']))
	{
		if(is_numeric($_GET['id_facture']))
		{
			// On vérifie bien que la personne est bien la propriétaire de la facture
			$requete_id_facture = $bdd->prepare('SELECT id, numero_facture FROM commandes WHERE id = :id && login = :login') ; 
			$requete_id_facture->execute(array(':id' => $_GET['id_facture'] , ':login' => $_SESSION['login'])) ;
			
			// C 'est que la facture appartient bien à la personne en question
			if($info_id_facture = $requete_id_facture->fetch())
			{
				$fichier = 'facture-'.$info_id_facture['id'].'-'.$info_id_facture['numero_facture'].'.pdf' ; 
				$poids = 10000 ; 
				$nom = '';
				
				header('Content-Type: application/octet-stream');
				header('Content-Length: '. $poids);
				header('Content-disposition: attachment; filename='. $fichier);
				header('Pragma: no-cache');
				header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
				header('Expires: 0');
	
				// On affiche simplement la facture que l'on veut 
				readfile($fichier) ; 
				
				exit() ; 
			}
			else
			{
				echo 'Accès facture interdit à cette facture pour '.$_SESSION['login'] ;
			}
		}
		else
		{
			echo 'Erreur format facture.' ;
		}
	}
	else
	{
		echo 'Id facture manquant.' ;
	}
}
else
{
	echo '<span class="erreur">'.$erreur_connexion_texte.'</span>' ; 
}