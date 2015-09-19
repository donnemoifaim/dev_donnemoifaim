<?php 
session_start();

include ('../../include/configcache.php') ;
include ('../../include/fonctions.php') ;

if(!empty($_SESSION['token_visiteur']) && !empty($_GET['token_visiteur']) && $_GET['token_visiteur'] == $_SESSION['token_visiteur'])
{
	if (!empty($_SESSION['login_visiteur']))
	{
		if(!empty($_GET['id_supp']))
		{
			// Suppression de l'avis que l'on veut supprimer
			$req_jaime_update = $bdd->prepare('DELETE FROM avis_utilisateur WHERE login = :login && id = :id') ;
			$req_jaime_update->execute(array(':login' => $_SESSION['login_visiteur'] , ':id' => $_GET['id_supp']));
			
			$req_jaime_update->closeCursor();
		}
		else
		{
			echo '<span>'.$erreur_interne.'</span><br />' ; 
		}
	}
}
else
{
	echo '<span class="erreur" >'.$erreur_faille_csrf.'</span>' ;
}