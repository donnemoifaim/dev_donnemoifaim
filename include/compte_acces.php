<?php
// Si la personne est connecté
if (!empty($_SESSION['login']))
{
	// Pour la connection compte ADMIN
	if(!empty($_SESSION['admin']))
	{
		if(!empty($_SESSION['token']) && !empty($_SESSION['date_token']))
		{
			// Si la personne n'est pas connecté depuis trop longtemps sinon on change son token
			// 20 minutes
			if(time() - $_SESSION['date_token'] > 1200)
			{
				$_SESSION['token'] = uniqid(rand(), true); 
				$_SESSION['date_token'] = time(); 
			}
		}
		else
		{
			$erreur_connexion = 1 ; 
		}
	}
	else
	{
		// Si la page requiert la connection administrateur 
		if(!empty($compte_admin_access) && $compte_admin_access == 1)
		{
			// Alors erreur de connexion
			$erreur_connexion = 1 ; 
		}
	}
}
else
{
	$erreur_connexion = 1 ; 
}

// Si il y a une erreur de connexion
if(!empty($erreur_connexion))
{
	header('location:/connection-compte.html') ; 
	die();
}
