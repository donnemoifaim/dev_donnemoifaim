<?php
session_start() ;

if(!empty($_SESSION['login']))
{
	if(isset($_GET['idimage']) && isset($_GET['methode'])) 
	{
		echo 'ok' ; 
		$salage_nom_idimage = $_GET['idimage'].''.$_GET['methode'] ; 
		
		// Pour éviter les beugs on check si l'id actuel existe bien 
		if(!empty($_SESSION['idimage_'.$salage_nom_idimage]))
		{
			//suppression du fichier correspondant 
			unlink('../../temporaire/'.$_SESSION['idimage_'.$salage_nom_idimage].'.jpg');
		
			// On vide la variable de l'image en question
			unset($_SESSION['idimage_'.$salage_nom_idimage]) ;
			
			// On enlève 1 a tout les id arpès l'id sup de tableau sinon ca va pas marcher + un au tableau compteur
			$_SESSION['nombre_image'.$_GET['methode']]-- ;
			
			// Si le nombre d'image est de zéro on vide le reste des variables
			if($_SESSION['nombre_image'.$_GET['methode']] == 0)
			{
				unset($_SESSION['nombre_image'.$_GET['methode']]) ; 
				unset($_SESSION['id_image_max'.$_GET['methode']]) ;
			}
			// Si l'id que l'on vient de supprimer est l'id max on l'économise en enlevant 1 egalement
			if($_SESSION['id_image_max'.$_GET['methode']] == $_GET['idimage'])
			{
				$_SESSION['id_image_max'.$_GET['methode']]-- ; 
			}
			
			// Si le nom de l'image existe on le vide aussi
			if(!empty($_SESSION['nom_image_upload'.$salage_nom_idimage]))
			{
				unset($_SESSION['nom_image_upload'.$salage_nom_idimage]) ;
			}
		}
	}
}