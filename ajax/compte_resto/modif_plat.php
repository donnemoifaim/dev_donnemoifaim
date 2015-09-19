<?php
session_start();

include ('../../include/configcache.php') ;
include ('../../include/fonctions.php') ; 

if(!empty($_SESSION['login']))
{
	if(!empty($_POST['idimagemodif']))
	{
		if(!empty($_POST['idimage']))
		{
			$id_image_modif = $_POST['idimagemodif'] ;
			
			// transferer la session image
			if(!empty($_SESSION['idimage_a_modif']))
			{
				// On copie colle dans le bon dossier
				copy('../../temporaire/'.$_SESSION['idimage_a_modif'].'.jpg' , '../../plats/'.$_POST['idimage'].'.jpg') ; 
				copy('../../temporaire/miniature/'.$_SESSION['idimage_a_modif'].'.jpg' , '../../plats/miniature/'.$_POST['idimage'].'.jpg') ;
				copy('../../temporaire/mobiles/'.$_SESSION['idimage_a_modif'].'.jpg' , '../../plats/mobiles/'.$_POST['idimage'].'.jpg') ;
				
				// On supprime du dossier temporaire
				unlink('../../temporaire/'.$_SESSION['idimage_a_modif'].'.jpg') ;
				unlink('../../temporaire/miniature/'.$_SESSION['idimage_a_modif'].'.jpg') ;
				unlink('../../temporaire/mobiles/'.$_SESSION['idimage_a_modif'].'.jpg') ;
				
				// On va valider la modif mais créer une tache pour voir la modification quand meme voir si il n 'y a pas de probleme
				$libelle = 'Validation modification '.$_POST['idimage'] ; 
				$description = 'modification du plat '.$_POST['idimage'].' à check' ; 
				$priorite = 3 ; 
				$type = 'validation_plat' ;  
				$id_unique = $_POST['idimage'] ; 
				
				ajout_tache_admin($libelle, $description, $priorite, $type , $id_unique)  ; 
			}
			else
			{
				$erreur = '<span class="erreur">Nouvelle image introuvable, rechargez la page. Si le problème persiste, contactez-nous.</span><br />' ;
			}
		}
		else
		{
			$erreur = '<span class="erreur">Image à modifier manquante. Si le problème persiste, contactez-nous.</span><br />' ;
		}
	}
	else
	{
		$erreur = '<span class="erreur">Image à modifier manquante. Si le problème persiste, contactez-nous.</span><br />' ;
	}
}
else
{
	$erreur = '<span class="erreur">'.$erreur_connexion_texte.'</span><br />' ;
}

if(!isset($erreur))
{
	$erreur = 0 ;
}

$tableau_json = array('erreur' => $erreur , 'idimage_final' => $_POST['idimage']) ; 

echo json_encode($tableau_json) ; 