<?php
session_start();

include ('../../include/configcache.php') ;
include ('../../include/fonctions.php') ;

if(!empty($_SESSION['login']))
{
	if(!empty($_SESSION['token']) && !empty($_POST['token']) && $_POST['token'] == $_SESSION['token'])
	{
		if(!empty($_POST['id_contact']))
		{
			// Supression du contact pro
			$requete_reduction = $bdd->prepare("DELETE FROM contact_pro WHERE id = :id_contact && login = :login");
			$requete_reduction->execute(array(':login' => $_SESSION['login'], ':id_contact' => $_POST['id_contact'])) ;
		}
	}
	else
	{
		echo '<span class="erreur" >'.$erreur_faille_csrf.'</span>' ;
	}
}