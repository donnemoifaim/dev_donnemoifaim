<?php
session_start() ; 

include('../../include/configcache.php') ; 
include('../../include/fonctions.php') ; 

// V�rification de connexion simple
if(!empty($_SESSION['login']))
{
	// V�rif de connexion admin
	if(!empty($_SESSION['admin']))
	{
		if(!empty($_SESSION['token']) && $_SESSION['token'] == $_POST['token'])
		{
			// On a besoin de l'id du resto
			if(!empty($_POST['id_resto']))
			{
				// R�cup�ration des donn�es utilisateurs 
				$reponse = $bdd->prepare("SELECT * FROM client WHERE id = :id"); 
				$reponse->execute(array(':id' => $_POST['id_resto']));
				
				//on v�rifie si le login existe
				if ($donnees = $reponse->fetch())
				{
					// On r�cup�re le login de la connexion de dieu que l'on conserve pour une s�curit� maximal 
					$_SESSION['login_connexion_de_dieu_resto'] = $_SESSION['login'] ; 
					
					// On peut rester en connection de dieu que 30m max
					$_SESSION['date_connexion_de_dieu_resto'] = time() ; 
					
					//htmlentities sur tout les array sensible pour �viter les failles xss
					$donnees = protection_array_faille_xss($donnees) ;
					
					// On ajoute les donn�es de l'utilisateur / le 0 correspond au mot de passe que l'on � pas besoin de toute mani�re
					creation_session_compte_resto($donnees , 0) ;
					
					// Par mesure de s�curit� on vide la session_admin actuelle, on peut toujours cr�er une api plus forte pour ne pas se d�connecter mais la ce n'est pas n�cessaire
					unset($_SESSION['admin']) ; 
				}
				
				$reponse->closeCursor() ; 
			}
			else
			{
				echo '<span class="erreur">Id resto manquant, si le probl�me persiste va voir le d�veloppeur.</span>' ;
			}
		}
		else
		{
			echo '<span class="erreur">'.$erreur_faille_csrf.'</span>' ; 
		}
	}
	else
	{
		echo '<span class="erreur">Rang admin n�cessaire, augmentez le dans la base de donn�es, au champs rang utilisateur.</span>' ; 
	}
}
else
{
	echo '<span class="erreur">'.$erreur_connexion_texte.'</span>' ; 
}