<?php

session_start();

include ('../../include/configcache.php') ;
include ('../../include/fonctions.php') ; 

if(!empty($_SESSION['token']) && !empty($_POST['token']) && $_POST['token'] == $_SESSION['token'])
{
	// Si le login existe ou le login du compte qui vient juste d'etre créé
	if(!empty($_SESSION['login']))
	{
		$login = $_SESSION['login'] ;
	}
	elseif(!empty($_SESSION['compte_tout_juste_cree']))
	{
		$login = $_SESSION['compte_tout_juste_cree'] ;
	}	
	if(!empty($_POST['valeur_attribus']))
	{
		// On modifie simplement les attribus du compte associé au login
		$requete_nom_plat = $bdd->prepare('UPDATE client SET attribus = :attribus WHERE login = :login');
		$requete_nom_plat->execute(array(':attribus' => $_POST['valeur_attribus'] , ':login' => $login ));
		
		// Nouvelle session correspondant au attribus indispensable pour la modification
		if(!empty($_POST['modif']))
		{
			$_SESSION['attribus'] = $_POST['valeur_attribus'] ; 
		}
	}
	else
	{
		$erreur = '<span class="erreur">Vos attributs sont incorrects, veuillez réessayer. Si le problème persiste veuillez nous contacter.</span><br />' ;
	}
}
else
{
	$erreur = '<span class="erreur" >'.$erreur_faille_csrf.'</span>' ;
}

if(!isset($erreur))
{
	$erreur = 0 ; 
}

$tableau_nom_plat = array('erreur' => $erreur);

echo json_encode($tableau_nom_plat) ; 