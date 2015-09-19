<?php
session_start() ;

// Skip these two lines if you're using Composer
define('FACEBOOK_SDK_V4_SRC_DIR', 'facebook-php-sdk-v4/src/Facebook/');
require 'facebook-php-sdk-v4/autoload.php';

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\GraphObject;
use Facebook\GraphLocation;
use Facebook\FacebookRequestException;
use Facebook\FacebookJavaScriptLoginHelper;


FacebookSession::setDefaultApplication('1462284764045188', 'a2857a66a445a1b0eaffc3edab808b92');

include('../../include/configcache.php') ;
include('../../include/fonctions.php') ;

if(!isset($_SESSION['login_visiteur']))
{
	$helper = new FacebookJavaScriptLoginHelper();
	try {
	  $session = $helper->getSession();
	} catch(FacebookRequestException $ex) {
	  // When Facebook returns an error
	} catch(\Exception $ex) {
	  // When validation fails or other local issues
	}

	if($session) {

	  try {
		$user_profile = (new FacebookRequest(
		  $session, 'GET', '/me'
		))->execute()->getGraphObject(GraphUser::className());

		$name_user = $user_profile->getName();
		$id_user = $user_profile->getId(); 
		$email_user = $user_profile->getEmail() ;
		$location = $user_profile->getLocation();

	  } catch(FacebookRequestException $e) {

		echo "Exception occured, code: " . $e->getCode();
		echo " with message: " . $e->getMessage();

	  }
	}
 	
	// Si l'id de l'user à été trouvé
	if(!empty($id_user))
	{	
		$requete_user = $bdd->prepare('SELECT id,login,abo,date_abo FROM utilisateur WHERE id_facebook = :id_facebook_user ') ;
		$requete_user->execute(array(':id_facebook_user' => $id_user)) ; 
		 
		if($info_user = $requete_user->fetch())
		{
			$id_visiteur = $info_user['id'] ; 
			$login = $info_user['login'] ;
			$abo = $info_user['abo'] ;
			$date_abo = $info_user['date_abo'] ;
			
			// Ce n'est pas une inscription 
			$inscription_fb = 0 ; 
			
			// Si l'utilisateur existe très bien on récupère simplement les données
			include('../../include/creation_session_connexion.php') ; 
		}
		// Seulement si on à demandé la connection avant, vu qu'on à pas de compte on le créer
		elseif($_GET['type'] == 'connect')
		{	
			// variable pour arreter la boucle
			$arret_boucle = 0 ; 
			
			// On créer une boucle qui va checker les utilisateurs si il existe déjà
			for($i=0 ; $arret_boucle != 1 ; $i++ )
			{
				// Si c'est la première instance on reprend le nom normal 
				if($i == 0)
				{
					$login_user = $name_user;
				}
				else
				{
					$login_user = $name_user.''.$i ; 
				}
				
				// Check si le login qu'on va mettre n'est pas déjà pris
				$requete_user_check = $bdd->prepare('SELECT login FROM utilisateur WHERE login = :login_user') ;
				$requete_user_check->execute(array(':login_user' => $login_user));
				
				if($info_user_check = $requete_user_check->fetch())
				{
					// Si y en a un on continu la boucle pour trouver un autre login
				}
				else
				{	
					// On a trouvé on arrete donc la boucle
					$arret_boucle = 1 ;
				}
			}
			// Sinon on créer un nouvelle utilisateur en prenant bien en compte les autres utilisateurs
			$requete_ajout_user = $bdd->prepare('INSERT INTO utilisateur(id_facebook, login,email,  date_utilisateur) VALUES (:id_facebook, :login, :email, :date_utilisateur)') ;
			$requete_ajout_user->execute(array(':id_facebook' => $id_user, ':login' => $login_user, ':email'=> $email_user, ':date_utilisateur' => time())) ; 

			// Création du login de connection
			$id_visiteur = $bdd->lastInsertId() ; 
			$login = $login_user ; 
			$abo = 0 ;
			
			// Si l'utilisateur existe très bien on récupère simplement les données
			include('../../include/creation_session_connexion.php') ;
			
			// C'est une inscription donc $inscription_fb
			$inscription_fb = 1 ; 
			
			$texte_a_afficher = '<span>Vous êtes maintenant bien connecté via Facebook.</span><br />' ;
		}
		
		// Si le token existe c'est que l'on à bien réussis a ce logg
		if(!empty($_SESSION['login_visiteur']))
		{
			if(!isset($texte_a_afficher))
			{
				$texte_a_afficher = '' ; 
			}
			
			if(!isset($deja_vote))
			{
				$deja_vote = 0 ; 
			}
			
			$tableau_json = array('login' => $login, 'token_visiteur' => $_SESSION['token_visiteur'],  'texte_a_afficher' => $texte_a_afficher , 'deja_vote' => $deja_vote, 'deja_connecte' => 0 , 'inscription' => $inscription_fb) ; 
			echo json_encode($tableau_json) ;
		}
	}
}
else
{
	// IL faut voir si on il y a deja un vote sur le plat que l'on visionne actuellement pour éviter les erreurs 
	
	$tableau_json = array('login' => $_SESSION['login_visiteur'], 'token_visiteur' => $_SESSION['token_visiteur'],  'texte_a_afficher' => '' , 'deja_vote' => 0, 'deja_connecte' => 1 , 'inscription' => 0) ; 
	echo json_encode($tableau_json) ;
}