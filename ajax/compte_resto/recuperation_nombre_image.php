<?php
session_start();

if(!empty($_SESSION['login']))
{
	$tableau_json = array('nombre_image' => $_SESSION['nombre_image'.$_GET['methode']] , 'id_image_max' => $_SESSION['id_image_max'.$_GET['methode']]) ;
}

echo json_encode($tableau_json) ; 
