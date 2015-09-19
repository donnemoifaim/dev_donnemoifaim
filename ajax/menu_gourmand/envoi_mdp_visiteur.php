<?php
include ('../../include/configcache.php') ;
include ('../../include/fonctions.php') ;

if (!empty($_POST['envoimail'])) 
{
	$envoimail = $_POST['envoimail'] ;
	
	// On check si le mail existe dans la base de donnée sinon ca ne sert a rien
	$reponse = $bdd->prepare('SELECT login, mdp FROM utilisateur WHERE email = :mail');
	$reponse->execute(array(':mail' => $envoimail)) ;
	
	if ($donnees = $reponse->fetch())
	{
		$mail = $envoimail; // Déclaration de l'adresse de destination.
		$login = $donnees['login']; 
		
		// Génère un lien unique pour l'utilisateur de rénitialisation de son mot de passe
		
		$url_base = $protocole_site.''.$_SERVER['HTTP_HOST'].'/menu-gourmand.html'; 
		
		$url_unique = generer_lien_unique($url_base , $login, $action = 'renitialiser_mdp' ) ; 
		
		// On met l'url dans le champs ancien mot de passe 
		$requete_ancien_mdp = $bdd->prepare('UPDATE utilisateur SET ancien_mdp = :ancien_mdp WHERE login = :login') ;
		$requete_ancien_mdp->execute(array(':ancien_mdp' =>  $url_unique , ':login' => $donnees['login'] )) ;
		
		
		if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail)) // On filtre les serveurs qui rencontrent des bogues.
		{
			$passage_ligne = "\r\n";
		}
		else
		{
			$passage_ligne = "\n";
		}
		$heure = date("H");
		
		//on cherche a savoir si c'est le soir ou la journée
		if($heure > 19)
		{
			$moment_journee = 'Bonsoir';
			$aurevoir = 'soirée';
		}
		else
		{
			$moment_journee = 'Bonjour';
			$aurevoir = 'journée';
		}
		
		//=====Déclaration des messages au format texte et au format HTML.
		$message_txt = $moment_journee.' '.$login.',
		
		Afin de réinitialisation votre mot de passe veuillez accéder au lien suivant : '.$url_unique.'
		
		Si vous rencontrez des problèmes lors de la procédure, n\'hésitez pas à nous contacter.

		Nous vous souhaitons une agréable '.$aurevoir.'.
		
		L\'équipe DonneMoiFaim.' ; 
		$message_html = '
		<html>
			<head>
			<style>
				b{font-weight:bold; color:#db302d}
				p{color:#333;}
			</style>
			</head>
			<body>
				<p>
					'.$moment_journee.' '.$login.',<br /><br />
					
					Afin de réinitialisation votre mot de passe veuillez accéder au lien suivant : <a href="'.$url_unique.'">'.$url_unique.'</a><br /><br /><br />
					
					Si vous rencontrez des problèmes lors de la procédure, n\'hésitez pas à nous contacter.<br /><br />

					Nous vous souhaitons une agréable '.$aurevoir.'.<br /><br />
					
					<strong>L\'equipe de DonneMoiFaim.</strong><br />
					<a href="'.$protocole_site.''.$_SERVER['HTTP_HOST'].'"><img src="'.$protocole_site.''.$_SERVER['HTTP_HOST'].'/imgs/logo-donnemoifaim.png" alt="logo donnemoifaim" /></a>
				</p>
			</body>
		</html>';
		//==========

		//=====Création de la boundary 
		$boundary = "-----=".md5(rand());
		//==========

		//=====Définition du sujet.
		$sujet = 'DonneMoiFaim : Renitialiser mot de passe';
		//========= 

		//=====Création du header de l'e-mail.
		$header = "From: \"DonneMoiFaim\"<contact@donnemoifaim.fr>".$passage_ligne;
		$header.= "Reply-to: \"DonneMoiFaim\" <contact@donnemoifaim.fr>".$passage_ligne;
		$header.= "MIME-Version: 1.0".$passage_ligne;
		$header.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;
		//==========

		//=====Création du message.
		$message = $passage_ligne."--".$boundary.$passage_ligne;
		//=====Ajout du message au format texte.
		$message.= "Content-Type: text/plain; charset=\"UTF-8\"".$passage_ligne;
		$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
		$message.= $passage_ligne.$message_txt.$passage_ligne;
		//==========
		$message.= $passage_ligne."--".$boundary.$passage_ligne;
		//=====Ajout du message au format HTML
		$message.= "Content-Type: text/html; charset=\"UTF-8\"".$passage_ligne;
		$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
		$message.= $passage_ligne.$message_html.$passage_ligne;
		//==========
		$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
		$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
		//==========

		//=====Envoi de l'e-mail.
		mail($mail,$sujet,$message,$header);
	}
	else
	{
		echo '<span class="erreur">Aucun compte n\'est associé à l\'adresse mail spécifiée.</span><br />' ; 
	} 
}
else
{
	echo '<span class="erreur">L\'adresse mail n\'est pas spécifiée.</span><br />' ; 
} 