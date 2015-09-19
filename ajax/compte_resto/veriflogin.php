<?php

include ('../../include/configcache.php') ;

if (!empty($_POST['loginformulaire']))
{
	if (strlen($_POST['loginformulaire']) > 5)
	{
		$login = $_POST['loginformulaire'] ; 
		$reponse_login = $bdd->prepare("SELECT login FROM client WHERE login = :login "); 
		$reponse_login->execute(array(':login' => $login));

		if($donnees_login = $reponse_login->fetch())
		{
			$login_verif = $donnees_login['login']; 
		}
		$reponse_login->closeCursor(); // Termine le traitement de la requête
		
		// Si le login existe déjà
		if (!empty($login_verif)) 
		{
			echo ' <span class="texte_site"> Indisponible <img src="imgs/nonchargement.png" /></span>' ; 
		}
		else
		{
			echo ' <span><img src="imgs/okchargement.png" /></span>' ; 
		}
	}
	else
	{
		echo ' <span><img src="imgs/nonchargement.png" /></span>' ;  
	}
}
else
{
	echo ' <span><img src="imgs/nonchargement.png" /></span>' ; 
}
