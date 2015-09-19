<?php

session_start() ;

if(!empty($_SESSION['login_visiteur']))
{
	unset($_SESSION['login_visiteur']) ;
	unset($_SESSION['id_visiteur']) ; 
}