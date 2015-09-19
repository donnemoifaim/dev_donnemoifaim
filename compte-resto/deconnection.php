<?php 

session_start();

// On vide juste la variable de connection c'est suffisant 

unset($_SESSION['login']) ; 

// On enleve également le godMod pour éviter de le reproduire
if(!empty($_SESSION['login_connexion_de_dieu_resto']))
{
	unset($_SESSION['login_connexion_de_dieu_resto']) ; 
}

header('Location: ../connection-compte.html');  

?>