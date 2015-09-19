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
		if(!empty($_SESSION['token']) && $_SESSION['token'] == $_POST['token'])
		{
			// On a besoin de l'id du resto
			if(!empty($_POST['id_resto']))
			{
				// Récupération des données utilisateurs 
				$reponse = $bdd->prepare("SELECT * FROM client WHERE id = :id"); 
				$reponse->execute(array(':id' => $_POST['id_resto']));
				
				//on vérifie si le login existe
				if ($donnees = $reponse->fetch())
				{
					// On récupère le login de la connexion de dieu que l'on conserve pour une sécurité maximal 
					$_SESSION['login_connexion_de_dieu_resto'] = $_SESSION['login'] ; 
					
					// On peut rester en connection de dieu que 30m max
					$_SESSION['date_connexion_de_dieu_resto'] = time() ; 
					
					//htmlentities sur tout les array sensible pour éviter les failles xss
					$donnees = protection_array_faille_xss($donnees) ;
					
					// On ajoute les données de l'utilisateur / le 0 correspond au mot de passe que l'on à pas besoin de toute manière
					creation_session_compte_resto($donnees , 0) ;
					
					// Par mesure de sécurité on vide la session_admin actuelle, on peut toujours créer une api plus forte pour ne pas se déconnecter mais la ce n'est pas nécessaire
					unset($_SESSION['admin']) ; 
				}
				
				$reponse->closeCursor() ; 
			}
			else
			{
				echo '<span class="erreur">Id resto manquant, si le problème persiste va voir le développeur.</span>' ;
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