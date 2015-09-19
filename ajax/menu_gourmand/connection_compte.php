<?php
// On démarre la session AVANT d'écrire du code HTML
session_start();

include ('../../include/configcache.php') ;
include ('../../include/fonctions.php') ;

//on récupère les données fournis pour la connection
if (!empty($_POST['login']) and !empty($_POST['mdp']))
{
	$login = $_POST['login'] ; 
	$reponse = $bdd->prepare("SELECT id,login, id_facebook, mdp, ancien_mdp, abo, date_abo FROM utilisateur WHERE login = :login OR email = :login"); 
	$reponse->execute(array(':login' => $login));
	
	//on vérifie si le login existe
	if ($donnees = $reponse->fetch())
	{
		// Si ce n'est pas un compte facebook securite vu qu'il n'y a pas de mdp dans un compte facebook
		if($donnees['id_facebook'] == 0)
		{
			$login_ok = $donnees['login']; 
			//verif en plus pour voir si la taille est correct
			if ((!empty($login_ok)) and strlen($login_ok) > 5)
			{
				$id_visiteur = $donnees['id'] ; 
				$login =  $donnees['login'];
				$mdp = $donnees['mdp'];
				$ancien_mdp = $donnees['ancien_mdp'] ;
				$abo = $donnees['abo'] ; 
				$date_abo = $donnees['date_abo'] ; 			
				
				// Fonction sécurisé d'hachage des mots de passes
				$mdp_visiteur = securise_mdp($_POST['mdp']) ;  

				//verif si le mdp dans la base est correct et si le mdp est équivalent
				if($mdp_visiteur == $mdp && !isset($_SESSION['date_blocage_fail']) && !isset($_COOKIE['date_blocage_fail']))
				{
					include('../../include/creation_session_connexion.php') ; 
					
					// Vidage de la variable fail connection si l'utilisateur c'est trompé
					vidage_session_fail_connexion() ; 
				}
				//si pas les même mots de passe
				else
				{ 
					if($mdp_visiteur == $ancien_mdp)
					{
						$erreur = '<span class="erreur">Ceci est votre ancien mot de passe, vous l\'avez changé pour un nouveau. Contactez-nous si vous rencontrez des soucis pour vous connecter.</span><br />' ;
					}
					else
					{
						$erreur = mauvais_mdp_compte() ;
					}
				}	
			}
			else
			{
				$erreur = '<span class="erreur">Nom d\'utilisateur éronné.</span><br />' ;
			}
		}
		else
		{
			$erreur = '<span class="erreur">Ce compte est associé à un compte Facebook, contactez-nous en cas de problème de connexion via Facebook.</span><br />' ;
		}
	}
	//si login existe pas on le dit
	else
	{
		$erreur = '<span class="erreur">Le pseudo ou l\'adresse email n\'existe pas</span><br />' ;
	}
	
$reponse->closeCursor(); // Termine le traitement de la requête	
}
else
{
	$erreur = '<span class="erreur">Login ou mot de passe manquant</span><br />' ;
}
if(!isset($deja_vote))
{
	// On a donc pas récupéré si on avait déjà voté
	$deja_vote = '' ; 
}
// Token visiteur
if(!empty($_SESSION['token_visiteur']))
{
	$token_visiteur = $_SESSION['token_visiteur'] ; 
}
else
{
	$token_visiteur = '' ; 
}

$tableau_json = array('erreur' => $erreur , 'login' => $_SESSION['login_visiteur'], 'deja_vote' => $deja_vote , 'token_visiteur' => $token_visiteur) ; 

echo json_encode($tableau_json); 