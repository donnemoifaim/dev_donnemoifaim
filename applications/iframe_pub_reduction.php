<?php
	session_start() ; 
	//on va mettre une session laisser passer pour dire que la personne à le droit d'accéder à la réduction que si la personne est connecté -->
	if(!empty($_SESSION['login_visiteur']))
	{
		$_SESSION['laisser_passer_reduction'] = 1 ;
	}
?>
