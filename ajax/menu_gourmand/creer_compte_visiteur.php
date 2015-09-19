<?php 

session_start();

include ('../../include/configcache.php') ;
include ('../../include/fonctions.php') ;

if (!empty($_POST['login']))
{
	if(strlen($_POST['login']) > 5)
	{
		// Si le login existe on arrete tout direct
		$reponse_login = $bdd->prepare("SELECT login FROM utilisateur WHERE login = :login"); 
		$reponse_login->execute(array(':login' => $_POST['login']));

		if($donnees_login = $reponse_login->fetch())
		{
			$erreur .= '<span class="erreur">- Login déjà utilisé</span><br />' ; 
		}
		else
		{
			$regex_pseudo = regex_pseudo() ;
			if( preg_match('#^'.$regex_pseudo.'$#', strtolower( $_POST['login'])))
			{
				$login = $_POST['login'] ;
			}
			else
			{
				$erreur .= '<span class="erreur">- Login : caractères interdits (a-z0-9 uniquement)</span><br />' ;
				echo $regex_pseudo ; 
			}
		}
	}
	else
	{
		$erreur .= '<span class="erreur">- Login trop court</span><br />' ; 
	}
}
else
{
	$erreur .= '<span class="erreur">- Login manquant</span><br />' ; 
}
if (!empty($_POST['mdp']))
{
	if(strlen($_POST['mdp']) > 5)
	{
		$mdp = securise_mdp($_POST['mdp']) ; 
	}
	else
	{
		$erreur .= '<span class="erreur">- Mot de passe trop court</span><br />' ;
	}
}
else
{
	$erreur .= '<span class="erreur">- Mot de passe manquant</span><br />' ;
}

if (!empty($_POST['mdp_check']))
{
	if($_POST['mdp_check'] == $_POST['mdp'])
	{}
	else
	{
		$erreur .= '<span class="erreur">- Verification mot de passe et mot de passe non similaire</span><br />' ;
	}
}
else
{
	$erreur = '<span class="erreur">- Verification mot de passe manquante</span><br />' ;
}
if(!empty($_POST['ville']))
{
	$ville = $_POST['ville'] ; 
}
else
{
	$erreur .= '<span class="erreur">- Ville erronée</span><br />' ;
}
if (!empty($_POST['email']))
{
	// Check voir si le login existe déjà dans la base client
	$reponse_mail = $bdd->prepare("SELECT email FROM utilisateur WHERE email = :email "); 
	$reponse_mail->execute(array(':email' => $_POST['email']));

	if($donnees = $reponse_mail->fetch())
	{
		$erreur .= '<span class="erreur">- Adresse email déjà utilisée</span><br />' ; 
	}
	else
	{	
		$regex_mail = regex_mail() ; 
		if(preg_match($regex_mail, $_POST['email']))
		{
			$email = $_POST['email'] ;
		}
		else
		{
			$erreur .= '<span class="erreur">- Email erroné</span><br />' ;
		}
	}
	
	$reponse_mail->closeCursor(); // Termine le traitement de la requête
}
else
{
	$erreur .= '<span class="erreur">- Email manquant</span><br />' ;
}

// Analyse du code promo si il y en a un 
if(!empty($_POST['code_promo']))
{
	// Suppression des espaces et mise en majuscule
	$code_promo = strtoupper(trim($_POST['code_promo']));
	
	if($code_promo == $code_promo_inscription_compte_visiteur)
	{
		// Si c'est le bon code le compte à le droit à 1 mois d'abonnement premium offert
		$abo = 1 ; 
		$date_abo = time() ; 
	}
	else
	{
		$abo = 0 ; 
		$date_abo = 0 ; 
	}
}
else
{
	$abo = 0 ; 
	$date_abo = 0 ; 
}

if (!empty($_POST['capatch']))
{
	if($_POST['capatch'] == $_SESSION['captacha_compte_visiteur'])
	{}
	else
	{
		$erreur .= '<span class="erreur">- Réponse incorrecte : '.$_POST['capatch'].'</span><br />' ;
	}
}
else
{
	$erreur .= '<span class="erreur">- Captcha manquant</span>' ; 
}

if(isset($_POST['newsletter']) && is_numeric($_POST['newsletter']))
{
	$newsletter = $_POST['newsletter'] ; 
}
else
{
	$erreur .= '<span class="erreur">- Erreur interne, veuillez nous contacter si le problème persiste.</span>' ;
}

if(!isset($erreur))
{
	$reponse_creation_compte = $bdd->prepare('INSERT INTO utilisateur (login,mdp,ville,email, abo,date_abo, newsletter, date_utilisateur) VALUES (:login, :mdp , :ville, :email , :abo,:date_abo, :newsletter , :date_utilisateur) ') ;	
	$reponse_creation_compte->execute(array(':login' => $login , ':mdp' => $mdp, ':ville' => $ville, ':email' => $email , ':abo' => $abo, ':date_abo' => $date_abo, ':newsletter' => $newsletter, ':date_utilisateur' => time())); 
	$reponse_creation_compte->closeCursor(); // Termine le traitement de la requête
	
	// Id visiteur == id qui vient d'etre inséré 
	$id_visiteur = $bdd->lastInsertId() ; 
	$abo = 0 ;
	
	// Si la personne à bien réussis à créer son compte izi 
	include('../../include/creation_session_connexion.php') ; 
					
	// Vidage de la variable fail connection si l'utilisateur c'est trompé
	vidage_session_fail_connexion() ; 
}

if(!isset($erreur))
{
	$erreur = '' ; 
}

$tableau_json = array('erreur' => $erreur , 'login' => $login, 'token_visiteur' => $_SESSION['token_visiteur']) ; 

echo json_encode($tableau_json) ; 

