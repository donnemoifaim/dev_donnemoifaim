<?php
session_start();

include ('../../include/configcache.php') ;
include ('../../include/fonctions.php') ; 

if(!empty($_SESSION['login']) OR !empty($_SESSION['compte_tout_juste_cree']))
{
	// Si c'est une remise en ligne c'est POST
	if(!empty($_POST['remettre_en_ligne']) && $_POST['remettre_en_ligne'] == 1)
	{
		if(!empty($_POST['idimage']))
		{
			$idimage_renouvelle = $_POST['idimage'] ; 
		}
		else
		{
			$erreur .= '<span class="erreur">Erreur idimage à renouvellé manquant.</span><br />' ; 
		}
	}
	// Testons si le fichier a bien été envoyé et s'il n'y a pas d'erreur
	if (!empty($idimage_renouvelle) || isset($_FILES['monfichier']) AND $_FILES['monfichier']['error'] == 0)
	{
		// Testons si le fichier n'est pas trop lourd < 12 MO
		if ($_FILES['monfichier']['size'] <= 12485760 || !empty($idimage_renouvelle))
		{
			if(!empty($idimage_renouvelle))
			{
				$extension_upload = 'jpg' ;  
			}
			else
			{
				// Testons si l'extension est autorisé
				$infosfichier = pathinfo($_FILES['monfichier']['name']);
				$extension_upload = $infosfichier['extension'];
			}
			
			$extensions_autorisees = array('jpg', 'jpeg', 'gif', 'png', 'JPG', 'JPEG', 'PNG', 'GIF');
			
			if (in_array($extension_upload, $extensions_autorisees))
			{
				if(!empty($idimage_renouvelle))
				{
					$cheminImageChoisie = '../../plats/archives/'.$idimage_renouvelle.'.jpg';
					$TailleImageChoisie = getimagesize($cheminImageChoisie);
				}
				else
				{
					$cheminImageChoisie = $_FILES['monfichier']['tmp_name'];
					$TailleImageChoisie = getimagesize($cheminImageChoisie);
				}
				$NouvelleLargeurImage = 1600;
				$NouvelleTailleImage = $NouvelleLargeurImage / $TailleImageChoisie[0] * $TailleImageChoisie[1];
			
				//Selection de l'image choisie	
				if (strtolower($extension_upload) == 'jpg' or strtolower($extension_upload) == 'jpeg')
				{
					$ImageChoisie = imagecreatefromjpeg($cheminImageChoisie);
				}
				elseif (strtolower($extension_upload) == 'png') 
				{
					$ImageChoisie = imagecreatefrompng($cheminImageChoisie);
				}
				elseif (strtolower($extension_upload == 'gif'))
				{
					$ImageChoisie = imagecreatefromgif($cheminImageChoisie);
				}
				$NouvelleImage = imagecreatetruecolor( $NouvelleLargeurImage, $NouvelleTailleImage ) or die ("Erreur");
				
				if ($extension_upload == 'png' or $extension_upload == 'PNG' or $extension_upload = 'gif' OR $extension_upload = 'GIF') 
				{
					// Si c'est un png permet d'avoir un fonc blanc
					$color = imagecolorallocate($NouvelleImage, 255, 255, 255);
					imagefill($NouvelleImage, 0, 0, $color);
				}
				
				//On copie et on reechantillonne l'image de départ
				imagecopyresampled($NouvelleImage , $ImageChoisie  , 0,0, 0,0, $NouvelleLargeurImage , $NouvelleTailleImage, $TailleImageChoisie[0],$TailleImageChoisie[1] );
				
				//On détruit l'image de base qui ne sert plus à rien
				imagedestroy($ImageChoisie);
				
				
				//On cherche la taille de l'image et on définit dors et déjà les futures dimensions
				$source = imagecreatefrompng("../logo_copyright.png"); // Le logo est la source
				$destination = $NouvelleImage; // La photo est la destination

				// Les fonctions imagesx et imagesy renvoient la largeur et la hauteur d'une image
				$largeur_source = imagesx($source);
				$hauteur_source = imagesy($source);
				$largeur_destination = imagesx($destination);
				$hauteur_destination = imagesy($destination);

				// On veut placer le logo en haut à droite, on calcule les coordonnées où on doit placer le logo sur la photo
				$destination_x = $largeur_destination - $largeur_source;
				$destination_y =  0;
				
				imagealphablending( $source, false );
				imagesavealpha( $source, true );
				
				// On met le logo (source) dans l'image de destination (la photo)
				imagecopymerge($destination, $source, $destination_x, $destination_y, 0, 0, $largeur_source, $hauteur_source, 60);

				// Salage en cas de modif de plat juste pour écrire a la fin modif
				if(!empty($_POST['idimagemodif']))
				{
					$id_image_salage = 'modif_image' ; 
					
					$name_final = $_SESSION['login'] .'-'.time().'-'.$id_image_salage;
				}
				elseif(!empty($_POST['facade_resto']))
				{
					$id_image_salage = 'facade_resto' ; 
					
					if(!empty($_SESSION['login']))
					{
						$name_final = $_SESSION['login'] .'-'.time().'-'.$id_image_salage;
					}
					elseif(!empty($_SESSION['compte_tout_juste_cree']))
					{
						$name_final = $_SESSION['compte_tout_juste_cree'] .'-'.time().'-'.$id_image_salage;
					}
				}
				else if(!empty($_POST['envoi_multiple']))
				{
					if(!empty($_SESSION['nombre_image'.$_POST['envoi_multiple']]))
					{
						$id_image_salage = $_POST['envoi_multiple'].'-'.$_SESSION['nombre_image'.$_POST['envoi_multiple']]; 
					}
					else
					{
						$id_image_salage = $_POST['envoi_multiple'].'-0' ;
					}
					
					$name_final = $_SESSION['login'] .'-'.time().'-'.$id_image_salage;
					
				}
				
				// Définit la destination du dossier a envoyé
				$destination_dossier = 'temporaire' ; 
				
				//Création de la nouvelle image en jpg qualité 90 dans tout les cas
				imagejpeg($destination , '../../'.$destination_dossier.'/'.$name_final.'.jpg', 80);
				
				// Création pour mobile d'image moins grande pour les versions mobiles
				$TailleImageChoisie_miniature = getimagesize('../../'.$destination_dossier.'/'.$name_final.'.jpg');
				$NouvelleLargeurImage_miniature = 1000;
				$NouvelleTailleImage_miniature = $NouvelleLargeurImage_miniature / $TailleImageChoisie_miniature[0] * $TailleImageChoisie_miniature[1]; 

				//Selection de l'image choisie				
				$ImageChoisie_miniature = imagecreatefromjpeg('../../'.$destination_dossier.'/'.$name_final.'.jpg'); 
				$NouvelleImage_miniature = imagecreatetruecolor( $NouvelleLargeurImage_miniature, $NouvelleTailleImage_miniature ) or die ("Erreur");
				
				//On copie et on reechantillonne l'image de départ
				imagecopyresampled($NouvelleImage_miniature , $ImageChoisie_miniature  , 0,0, 0,0, $NouvelleLargeurImage_miniature , $NouvelleTailleImage_miniature, $TailleImageChoisie_miniature[0],$TailleImageChoisie_miniature[1] );
				
				$destination_miniature = $NouvelleImage_miniature;
				//Création de la nouvelle image en jpg qualité 90
				$name_final_miniature = $name_final;
				imagejpeg($destination_miniature , '../../'.$destination_dossier.'/mobiles/'.$name_final_miniature.'.jpg', 80); 
				
				//Création d'une miniature
				$TailleImageChoisie_miniature = getimagesize('../../'.$destination_dossier.'/'.$name_final.'.jpg');
				$NouvelleLargeurImage_miniature = 360;
				$NouvelleTailleImage_miniature = $NouvelleLargeurImage_miniature / $TailleImageChoisie_miniature[0] * $TailleImageChoisie_miniature[1]; 
			
				//Selection de l'image choisie					
				$ImageChoisie_miniature = imagecreatefromjpeg('../../'.$destination_dossier.'/'.$name_final.'.jpg'); 
				$NouvelleImage_miniature = imagecreatetruecolor( $NouvelleLargeurImage_miniature, $NouvelleTailleImage_miniature ) or die ("Erreur");
				
				//On copie et on reechantillonne l'image de départ
				imagecopyresampled($NouvelleImage_miniature , $ImageChoisie_miniature  , 0,0, 0,0, $NouvelleLargeurImage_miniature , $NouvelleTailleImage_miniature, $TailleImageChoisie_miniature[0],$TailleImageChoisie_miniature[1] );
				
				$destination_miniature = $NouvelleImage_miniature;
				//Création de la nouvelle image en jpg qualité 90
				$name_final_miniature = $name_final;
				imagejpeg($destination_miniature , '../../'.$destination_dossier.'/miniature/'.$name_final_miniature.'.jpg', 80);
				
				// Si c'est une modification de fichier
				if(!empty($_POST['idimagemodif']))
				{
					// On créer juste une session image modif que l'on récupéra pour connaitre le nom de l'image
					$_SESSION['idimage_a_modif']  = $name_final ;
					
					// Pour récupération à la fin
					$nom_image = $name_final ;
				}
				elseif(!empty($_POST['facade_resto']))
				{
					// On créer juste une session image modif que l'on récupéra pour connaitre le nom de l'image
					$_SESSION['facade_resto_modif']  = $name_final ;
					
					// Pour récupération à la fin
					$nom_image = $name_final ;
				}
				else
				{
					//Création des sessions afin de pouvoir accéder au paiement ( multiple pour envoi multiple qui contient le type)
					if(!empty($_SESSION['nombre_image'.$_POST['envoi_multiple']]))
					{
						// On augmente de 1 et l'image nombre image et l'id_image_max
						$_SESSION['nombre_image'.$_POST['envoi_multiple']]++ ;
						$_SESSION['id_image_max'.$_POST['envoi_multiple']]++ ; 
						
						$id_image = 'idimage_'.$_SESSION['id_image_max'.$_POST['envoi_multiple']].''.$_POST['envoi_multiple'] ;
						$_SESSION[$id_image] = $name_final;
						
						if(!empty($idimage_renouvelle))
						{
							$_SESSION['nom_image_upload'.$_SESSION['id_image_max'.$_POST['envoi_multiple']].''.$_POST['envoi_multiple']] = $_POST['nomplat'] ; 
						}
					}
					else
					{
						$_SESSION['id_image_max'.$_POST['envoi_multiple']] = 0 ; 
						$_SESSION['nombre_image'.$_POST['envoi_multiple']] = 1 ; 
						$_SESSION['idimage_0'.$_POST['envoi_multiple']] = $name_final; 
						
						if(!empty($idimage_renouvelle))
						{
							$_SESSION['nom_image_upload0'.$_POST['envoi_multiple']] = $_POST['nomplat'] ; 
						}
					}
				}
			}
			else
			{ 
				$erreur .= '<span>- Fichier interdit : choisissez un fichier autorisé (PNG, JPG, GIF) </span><br />';
			}
		}		
		else
		{
			$erreur .= '<span>- Votre fichier est trop volumineux (poid max = 12 MO) : redimenssionnez le pour qu\'il soit plus petit.</span><br />';
		}
	}
	else
	{
		$erreur .= '<span class="erreur">'.$erreur_interne.'</span><br />' ; 
	}
}
else
{
	$erreur .= '<span class="erreur">'.$erreur_connexion_texte.'</span><br />' ;
}	

// Si il n 'y à pas d'erreur très bien on met les erreurs à 0 
if(!isset($erreur))
{
	$erreur = 0 ;
}

// On le met dans une session pour récupérer directement à la création de l'image
if(!empty($idimage_renouvelle))
{
	$_SESSION['idimage_renouvelle'] = $name_final  ;
	$_SESSION['ancienne_idimage_renouvelle'] = $idimage_renouvelle ; 
} 
// Si c'est l'iframe d'upload
if(!empty($_POST['iframe_upload']))
{
	$envoi_ok = $name_final  ;
}
else
{
	// Si l'image n'existe pas ou plus 
	$tableau_json = array('nom_image' => $name_final ,'erreur' => $erreur); 

	echo json_encode($tableau_json) ;
}		
?>