<?php
// On démarre la session AVANT d'écrire du code HTML
session_start();

include ('../../include/configcache.php') ;
include ('../../include/fonctions.php') ;

//on récupère les données fournis pour la connection
if (!empty($_POST['login']))
{
	if(!empty($_POST['mdp']))
	{
		$login = $_POST['login'] ; 
		
		$reponse = $bdd->prepare("SELECT * FROM client WHERE login = :login OR mail = :login"); 
		$reponse->execute(array(':login' => $login));
		
		//on vérifie si le login existe
		if ($donnees = $reponse->fetch())
		{
			//htmlentities sur tout les array sensible pour éviter les failles xss
			$donnees = protection_array_faille_xss($donnees) ;
			
			$login_ok = $donnees['login']; 
			$mail_ok = $donnees['mail'] ;
			
			$mdp = $donnees['mdp'] ; 
			
			$mdp_check = securise_mdp_compte_resto($_POST['mdp']) ; 
			
			//verif si le mdp dans la base est correct et si le mdp est équivalent
			if ($mdp == $mdp_check && !isset($_SESSION['date_blocage_fail']) && !isset($_COOKIE['date_blocage_fail'])) 	  
			{
				// On ajoute les données + le mdp que l'on récupère
				creation_session_compte_resto($donnees , $mdp) ;
				
				// Création de la session spéciale admin 
				if(in_array($_SESSION['login'] , $pseudo_admin))
				{
					$_SESSION['admin'] = $donnees['login']; 
				}
			}
			//si pas les même mots de passe
			else
			{
				echo mauvais_mdp_compte() ; 
			}
		}
		//si login existe pas on le dit
		else
		{
			echo '<span class="erreur" >Le pseudo ou l\'adresse mail n\'existe pas</span><br />' ; 
		}
		
	$reponse->closeCursor(); // Termine le traitement de la requête	
	}
	else
	{
		echo '<span class="erreur">Mot de passe manquant</span><br />' ; 
	}
}
else
{
	echo '<span class="erreur" >Pseudo manquant</span><br />' ;
}
