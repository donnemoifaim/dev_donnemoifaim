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
			if(!empty($_POST['commercial_commande_choix']))
			{
				// Qu'il soit vide ou pas importe peut
				if(isset($_POST['id_commande']))
				{		
					$requete_commercial_commande = $bdd->prepare('UPDATE commandes SET commercial_associe = :commercial_associe WHERE id_commande = :id_commande');
					$requete_commercial_commande->execute(array(':id_commande' => $_POST['id_commande'] , ':commercial_associe' => $_POST['commercial_commande_choix'])) ;
					
					$requete_commercial_commande->CloseCursor() ;
				}
				else
				{
					echo '<span class="erreur">Id commande manquant.</span>' ; 
				}
			}
			else
			{
				echo '<span class="erreur">Pseudo commercial manquant.</span>' ; 
			}
		}
		else
		{
			echo '<span class="erreur">'.$erreur_faille_csrf.'</span>' ; 
		}
	}
	else
	{
		echo '<span class="erreur">Rang admin nécessaire.</span>' ; 
	}
}
else
{
	echo '<span class="erreur">'.$erreur_connexion_texte.'</span>' ; 
}