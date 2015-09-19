<?php
session_start();

if(!empty($_SESSION['login']))
{
	for($i = 0 ; $i <= $_SESSION['id_image_max'.$_POST['methode']] ; $i++)
	{
		if(!empty($_POST['nom_image_upload'.$i.''.$_POST['methode']]))
		{
			$_SESSION['nom_image_upload'.$i.''.$_POST['methode']] = str_replace('"' , '\'' , $_POST['nom_image_upload'.$i.''.$_POST['methode']]) ; 
		}
	}
}