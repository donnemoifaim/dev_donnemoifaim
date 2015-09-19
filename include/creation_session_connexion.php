<?php
// Le mot de passe correspond bien on créer la session
$_SESSION['id_visiteur'] = $id_visiteur; 
$_SESSION['login_visiteur'] = $login;
					
// Vérification d'abonnement
verification_abonnement_visiteur($abo, $date_abo, $login) ; 

$_SESSION['token_visiteur'] = uniqid(rand(), true);

// Récupération si la personne à déjà voté ou non vis à vis du plat
$resultat_jaime = recuperation_jaime_plat($_SESSION['id_image_actuelle']) ;

$deja_vote = $resultat_jaime['deja_jaime_vote']; 

// On cherche si l'utilisateur à dédjà aimé facebook ou pas et si il a twitter ou pas 
$requete_jaime_facebook = $bdd->prepare("SELECT jaime_facebook, twitter_follow FROM utilisateur WHERE login = :login"); 
$requete_jaime_facebook->execute(array(':login' => $login));

if($info_jaime_facebook = $requete_jaime_facebook->fetch())
{
	// 1 on a deja aimé facebook , 0 on ne l'a pas aimé
	$_SESSION['jaime_facebook'] = $info_jaime_facebook['jaime_facebook'] ;
	$_SESSION['twitter_follow'] = $info_jaime_facebook['twitter_follow'] ; 
}

$erreur = 0 ; 