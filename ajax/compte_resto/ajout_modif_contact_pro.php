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
	
	// Civilite
	if(!empty($_POST['civ']))
	{
		$civ = $_POST['civ'] ; 
	}
	else
	{
		$erreur .= '<span class="erreur"> - Civilité manquante</span><br />' ; 
	}
	
	// Nom 
	if(!empty($_POST['nom']))
	{
		$nom = $_POST['nom'] ; 
	}
	else
	{
		$erreur .= '<span class="erreur"> - Nom manquant</span><br />' ; 
	}
	
	// Prenom non obligatoire 
	$prenom = $_POST['prenom'] ; 
	
	// 
	if(!empty($_POST['email']))
	{	
		// Si le mail est bien  formaté 
		if(preg_match(regex_mail() , $_POST['email']))
		{
			$email = $_POST['email'] ;
		}
		else
		{
			$erreur .= '<span class="erreur">- Email mal formaté</span><br />' ; 
		}
	}
	else
	{
		$erreur .= '<span class="erreur">- Email manquant</span><br />' ; 
	}
	
	// Numéro pro
	if(!empty($_POST['tel']))
	{
		// Il faut que le numéro soit un chiffre
		if(is_numeric($_POST['tel']))
		{
			$tel = $_POST['tel'] ;
		}
		else
		{
			$erreur .= '<span class="erreur">- Numéro pro ne doit contenir que des chiffres</span><br />' ; 
		}
	}
	else
	{
		$erreur .= '<span class="erreur">- Numéro pro manquant</span><br />' ; 
	}
	
	// Poste de la personne dans l'entreprise
	if(!empty($_POST['poste']))
	{
		$poste = $_POST['poste'] ; 
	}
	else
	{
		$erreur .= '<span class="erreur">- Poste manquant</span><br />' ; 
	}

	$disponibilite = $_POST['jour_semaine'] ; 
	
}
else
{
	$erreur .= '<span class="erreur" >'.$erreur_faille_csrf.'</span>' ;
}

if(!isset($erreur))
{
	if(!empty($_POST['action']))
	{
		if($_POST['action'] == 'ajout_contact')
		{
			$requete_contact_pro = $bdd->prepare('INSERT INTO contact_pro(civ,nom,prenom,email,tel,login,poste,disponibilite,date_ajout) VALUES(:civ,:nom,:prenom,:email,:tel,:login,:poste,:disponibilite,:date_ajout) ') ; 
			$requete_contact_pro->execute(array(':civ' => $civ ,':nom' => $nom , ':prenom' => $prenom, ':email' => $email, ':tel' => $tel, ':login' => $login , ':poste' => $poste , ':disponibilite' => $disponibilite , ':date_ajout' => time())) ; 
			
			$erreur = 0 ;
		}
		elseif($_POST['action'] == 'modif_contact')
		{
			$requete_contact_pro = $bdd->prepare('UPDATE contact_pro SET civ = :civ, nom = :nom, prenom = :prenom, email = :email,tel = :tel, poste = :poste, disponibilite = :disponibilite WHERE id = :id && login = :login') ; 
			$requete_contact_pro->execute(array(':civ' => $civ ,':nom' => $nom , ':prenom' => $prenom, ':email' => $email, ':tel' => $tel, ':poste' => $poste , ':disponibilite' => $disponibilite , ':id' => $_POST['id_modif'] , ':login' => $_SESSION['login'])) ; 
			
			$erreur = 0 ;
		}
		else
		{
			$erreur .= '<span class="erreur">Element manquant : object de votre action.</span><br />' ; 
		}
	}
	else
	{
		$erreur .= '<span class="erreur">Une erreur est survenue, veuillez nous contacter.</span><br />' ; 
	}
}

$tableau_erreur = array('erreur' => $erreur);

echo json_encode($tableau_erreur) ; 	