<?php

session_start();

include ('../../include/configcache.php') ;
include ('../../include/fonctions.php') ; 


if(!empty($_SESSION['login']) )
{
	if(!empty($_SESSION['token']) && !empty($_POST['token']) && $_POST['token'] == $_SESSION['token'])
	{
		// Juste pour modifier le titre si on ne veut pas changer l'image
		if(!empty($_POST['idimagemodif']))
		{
			if(!empty($_POST['idimagemodif']))
			{	
				$nom_plat_url = $_POST['nomplat'] ;
				$id_image_modif = $_POST['idimagemodif'] ; 
				
				//FONCTION DE CONVERTION DU NOM POUR URL DISPO dans admin/fonctions.php
				$nom_plat_url = renomage_url_fichier($nom_plat_url) ;
				
				// Ici pour la modification récupération de l'ID et reconstition complete du nom du fichier
				$new_idimage = choix_id_image($nom_plat_url) ; 
				
				if(rename('../../plats/'.$_POST['idimage'] .'.jpg' , '../../plats/'.$new_idimage.'.jpg' ))
				{
					rename('../../plats/miniature/'.$_POST['idimage'].'.jpg' , '../../plats/miniature/'.$new_idimage.'.jpg');
					rename('../../plats/mobiles/'.$_POST['idimage'].'.jpg' , '../../plats/mobiles/'.$new_idimage.'.jpg');
					
					$nomplat = $_POST['nomplat']; 
					
					$requete_nom_plat = $bdd->prepare('UPDATE plat SET nomplat = :nomplat, idimage = :new_idimage WHERE id = :id AND login = :login');
					$requete_nom_plat->execute(array(':nomplat' => $nomplat ,':new_idimage' => $new_idimage , ':id' => $id_image_modif  , 'login' => $_SESSION['login']));
					
					// Modification du sitemap.xml
				}
				else
				{
					$erreur = '<span class="erreur">L\'image n\'existe pas.</span><br />' ;
				}
			}
			else
			{
				$erreur = '<span class="erreur">L\'id de l\'image n\'existe pas.</span><br />' ; 
			}
		}
		else
		{
			$erreur = '<span class="erreur">'.$erreur_interne.'</span><br />' ; 
		}
		
	}
	else
	{
		$erreur = '<span class="erreur">'.$erreur_faille_csrf.'</span><br />' ;
		$new_idimage = '' ; 
	}
	
	if(!isset($erreur))
	{
		$erreur = 0 ; 
	}

	$tableau_nom_plat = array('erreur' => $erreur , 'idimage_final' => $new_idimage); 

	echo json_encode($tableau_nom_plat) ;

}