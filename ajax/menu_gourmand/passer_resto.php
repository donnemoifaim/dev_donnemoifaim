<?php 
session_start();

include ('../../include/configcache.php') ;

// Trouvons la session login du resto pour reprendre tous ses plats et les enlever
$login_client_actuel = $_SESSION['login_client_actuel'] ;

$requete_tout_plat_client = $bdd->prepare('SELECT id FROM plat WHERE login = :login') ;
$requete_tout_plat_client->execute(array(':login' => $login_client_actuel)) ;

$image_a_passer = '' ; 

// On se prend pas la tete on fait tout passer directement dans la boucle
while($info_tout_plat = $requete_tout_plat_client->fetch())
{
	$_SESSION['toute_image_precedente'] = $_SESSION['toute_image_precedente'].',\''.$info_tout_plat['id'].'\'';
}