<?php

include ('../../include/configcache.php') ;

if (!empty($_POST['login']) AND preg_match("#^[A-Za-z0-9-éêàûçè ]+$#", $_POST['login']) )
{
	$login = $_POST['login'] ; 
	$reponse = $bdd->prepare("SELECT login FROM utilisateur WHERE login = :login "); 
	$reponse->execute(array(':login' => $login));

	while ($donnees = $reponse->fetch())
	{
		$login_deja_utilise = $donnees['login']; 
	}
	$reponse->closeCursor(); // Termine le traitement de la requête

	if (!empty($login_deja_utilise)) 
	{?>
		<span class="texte_site">Indisponible</span> <img src="imgs/nonchargement.png" alt="champ invalide" /><?php
	}
	else if (strlen($_POST['login']) < 6)
	{?>
		<img src="imgs/nonchargement.png" alt="champ invalide" />
	<?php
	}

	else 
	{
	?>
		<img src="imgs/okchargement.png" alt="champ valide" />
	<?php
	}
}
else
{?>
	<img src="imgs/nonchargement.png" alt="champ invalide" />
<?php
}