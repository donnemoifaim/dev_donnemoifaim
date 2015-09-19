<?php 
session_start();

include ('../../include/configcache.php') ;
include ('../../include/fonctions.php') ;

if (!empty($_SESSION['login_visiteur']))
{
	// Attribution de code promo
	if(!empty($_GET['code_promo']))
	{
		// Si le code promo correspond bien a un code promo 
		if($code_promo_inscription_compte_visiteur == $_GET['code_promo'])
		{
			// On va modifier l'abonnement de la personne
			$requete_utilisateur_abo = $bdd->prepare('UPDATE utilisateur SET abo = 1, date_abo = :date_abo WHERE login = :login') ;
			$requete_utilisateur_abo->execute(array(':date_abo' => time() , ':login' => $_SESSION['login_visiteur'])) ; 
			
			echo 'valide' ; 
		}
		else
		{
			echo '<span class="erreur"> Code promo erroné.</span>' ; 
		}
	}
	else
	{
		echo '<span class="erreur"> Code promo manquant.</span>' ; 
	}
}
else
{
	echo '<span class="erreur">'.$erreur_connexion_texte.'</span>' ; 
}