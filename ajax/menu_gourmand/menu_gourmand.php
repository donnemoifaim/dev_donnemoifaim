<?php 

session_start(); 

//connection à la bdd
include ('../../include/configcache.php') ;
include ('../../include/fonctions.php');

// Si jamais il y avait un laissé passé pour la réduction précédente on le supprime 
if(!empty($_SESSION['laisser_passer_reduction']))
{
	unset($_SESSION['laisser_passer_reduction']) ; 
}

if(!empty($_SESSION['images']))
{
	// Pour retenir l'ancienne image et ne pas la recharger 2 fois de suite ou pour récupérer la dernière
	$image_precedente = $_SESSION['images'] ;
	
	// On retiens la dernière fois que l'on à recherché une image 
	if(!empty($_SESSION['time_derniere_recherche']))
	{
		// Si la différence entre maintenant et est de 2h alors on rénitialise les images pour éviter de masquer des images
		if(time() - $_SESSION['time_derniere_recherche'] >= 86400)
		{
			// On met quand meme la derniere image pour éviter de la recharger
			$_SESSION['toute_image_precedente'] = '\''.$image_precedente.'\'' ;
			
			// On remet le time initiale 
			$_SESSION['time_derniere_recherche'] = time() ; 
		}
	}
	else
	{
		$_SESSION['time_derniere_recherche'] = time() ; 
	}
	
	// Si c'est de la géocalisation rechargée depuis le début
	if(!empty($_GET['revenir_au_debut_geolocalisation']))
	{
		// On met aucune image déjà vu 
		$_SESSION['toute_image_precedente'] = '' ;
	}
	
	//Pour éviter de recharger les autres images 
	if(!empty($_SESSION['toute_image_precedente']))
	{
		$_SESSION['toute_image_precedente'] = $_SESSION['toute_image_precedente'].',\''.$image_precedente.'\'';
	}
	else
	{
		$_SESSION['toute_image_precedente'] = '\''.$image_precedente.'\'' ;
	}
}
// Si aucune image n'est chargé par l'HTML HISTORY
if(!isset($_GET['idimage']))
{
	//éviter que la meme image soit répété 2 fois tout le temps jusqu'a ce que le tableau soit finit
	if(!empty($_SESSION['toute_image_precedente']))
	{
		if(!empty($_GET['recherche']))
		{
			$reponseimage = $bdd->prepare("SELECT p.id id FROM plat p INNER JOIN client c ON p.login = c.login WHERE c.ville = :villeformulaire AND p.id NOT IN (".$_SESSION['toute_image_precedente'].") AND p.etat = 1  ORDER BY RAND() ");
			$reponseimage->execute(array(':villeformulaire' => $_GET['recherche'])) ;
		}
		// Sinon c'est une recherche par géocalisation
		elseif(!empty($_GET['position_user_latitude']) AND !empty($_GET['position_user_longitude']))
		{
			$reponseimage = $bdd->prepare('SELECT p.id id FROM plat p INNER JOIN client c ON p.login = c.login WHERE p.id NOT IN ('.$_SESSION['toute_image_precedente'].') AND p.etat = 1 ORDER BY ABS(c.coordonnees_latitude - :position_user_latitude + c.coordonnees_longitude - :position_user_longitude) ASC LIMIT 1');
			
			$reponseimage->execute(array(':position_user_latitude' => $_GET['position_user_latitude'] , ':position_user_longitude' => $_GET['position_user_longitude'])) ;
			
			// On définit le nouveau type de recherche à géocalise
			$recherche_geocalise = 1;
		}
		else
		{
			// on cherche les ids et on en prend une au hasard
			$reponseimage = $bdd->query('SELECT id FROM plat WHERE id NOT IN ('.$_SESSION['toute_image_precedente'].') AND etat = 1 ORDER BY RAND()');
		}
	}
	else
	{
		if(!empty($_GET['recherche']))
		{
			$reponseimage = $bdd->prepare("SELECT p.id id FROM plat p INNER JOIN client c ON p.login = c.login WHERE c.ville = :villeformulaire AND p.etat = 1 ORDER BY RAND() ");
			$reponseimage->execute(array(':villeformulaire' => $_GET['recherche'])) ;
		}
		// Sinon c'est une recherche par géocalisation
		elseif(!empty($_GET['position_user_latitude']) AND !empty($_GET['position_user_longitude']))
		{
			// Permet de trouver la position la plus proche de notre position
			
			$reponseimage = $bdd->prepare('SELECT p.id id FROM plat p INNER JOIN client c ON p.login = c.login WHERE p.etat = 1 ORDER BY ABS(c.coordonnees_latitude - :position_user_latitude + c.coordonnees_longitude - :position_user_longitude) ASC LIMIT 1');
			
			$reponseimage->execute(array(':position_user_latitude' => $_GET['position_user_latitude'] , ':position_user_longitude' => $_GET['position_user_longitude'])) ;
			
			// On définit le nouveau type de recherche à géocalise
			$recherche_geocalise = 1;
			
		}
		else
		{
			// on cherche les ids et on en prend un au hassard
			$reponseimage = $bdd->query('SELECT id FROM plat WHERE etat = 1 ORDER BY RAND()');
		}
	}

	$image = $reponseimage->fetch() ;  

	// Si il existe un id ( que c'est bien la bonne image )
	if(!empty($image['id']))
	{
		//initialisation de la session images afin de la récupéré plus tard dans url image
		$_SESSION['images'] = $image['id'];
		//iniatialisation de $images pour la requête suivante
		$images = $image['id'];
	}
	// Sinon on en retrouve un autre tout en supprimant les sessions des images précédentes et en ne reprenant pas la derniere image !!
	else
	{
		// On fait bien attention aussi vu que c'était la dernière a remettre $_SESSION['nombre_resto_vu_suite'] à 1, sinon ce sera la loose, ca beuggera si jamais c'étais la dernière 
		$_SESSION['nombre_resto_vu_suite'] = 1 ; 
		
		if(!empty($_SESSION['toute_image_precedente']) AND !isset($_GET['recherche']) AND !isset($_GET['position_user_latitude']))
		{
			// le basique et ne reprend juste pas la derniere image pour etre sur 
			$reponseimage = $bdd->prepare('SELECT id FROM plat WHERE id != :id AND etat = 1 ORDER BY RAND()');
			$reponseimage->execute(array(':id' => $image_precedente )) ; 
			
			// On vide la variable des tableaux pour recommencer
			unset($_SESSION['toute_image_precedente']) ; 
		}
		elseif(!empty($_SESSION['toute_image_precedente']) AND !empty($_GET['recherche']))
		{
			$reponseimage = $bdd->prepare("SELECT p.id id FROM plat p INNER JOIN client c ON p.login = c.login WHERE c.ville = :villeformulaire && p.id != :id AND p.etat = 1 ORDER BY RAND() ");
			$reponseimage->execute(array(':villeformulaire' => $_GET['recherche'] , ':id' => $image_precedente)) ;
				
			// On vide la variable des tableaux pour recommencer
			unset($_SESSION['toute_image_precedente']) ; 
			 
		}
		// Pas besoin d'éviter de resortir le dernier puisque ce ne pourra jamais l'etre
		elseif(!empty($_SESSION['toute_image_precedente']) AND !empty($_GET['position_user_latitude']))
		{ 
			$reponseimage = $bdd->prepare('SELECT p.id id FROM plat p INNER JOIN client c ON p.login = c.login WHERE p.etat = 1 ORDER BY ABS(c.coordonnees_latitude - :position_user_latitude + c.coordonnees_longitude - :position_user_longitude) ASC LIMIT 1');
			
			$reponseimage->execute(array(':position_user_latitude' => $_GET['position_user_latitude'] , ':position_user_longitude' => $_GET['position_user_longitude'])) ;
			
			// On définit le nouveau type de recherche à géocalise
			$recherche_geocalise = 1;
				
			// On vide la variable des tableaux pour recommencer
			unset($_SESSION['toute_image_precedente']) ; 
			
		}
		// Reprise de l'image 
		if ($image = $reponseimage->fetch()) 
		{
			//initialisation de la session images afin de la récupéré plus tard dans url image
			$_SESSION['images'] = $image['id'];
			//iniatialisation de $images pour la requête suivante
			$images = $image['id'];
		}
	}
}
// Sinon chargement de l'image spécifié
else
{
	$images =  $_GET['idimage'] ;
}
// Si c'est une recherche géocalisé on change la méthode de recherche
if(!empty($recherche_geocalise))
{
	$_SESSION['type_recherche'] = 'geocaliser' ; 
}
else
{
	// Sinon c'est alétoire
	$_SESSION['type_recherche'] = 'aleatoire' ;
}
if(!empty($images))
{
	// Ajoute une visite au plat pour les statistiques
	ajout_visite_plat($images) ; 

	//Requête qui lie 2 tables celle client et celle plat afin de pouvoir afficher les données de l'image en fonction du client
	$reponseinfo = $bdd->prepare("SELECT c.id id_client ,c.login login,p.id id, p.idimage idimage, p.nomplat nomplat,p.etat etat,p.id_reduction id_reduction,  c.nomresto nomresto, c.adressresto adressresto, c.ville ville, c.type_resto type_resto, c.mail mail, c.site_internet site_internet, c.coordonnees_latitude coordonnees_latitude, c.coordonnees_longitude coordonnees_longitude, c.attribus attribus , c.image_facade image_facade FROM plat p INNER JOIN client c ON p.login = c.login WHERE p.id = :id ");
	$reponseinfo->execute(array(':id' => $images)) ;

	if ($info = $reponseinfo->fetch())
	{
		if(!empty($_SESSION['login']) AND in_array($_SESSION['login'], $pseudo_admin))
		{
			$requete_stats_admin = $bdd->prepare('SELECT visite FROM statistique WHERE id = :id') ;
			$requete_stats_admin->execute(array(':id' => $images)) ;
			
			$info_stats_admin = $requete_stats_admin->fetch() ; 
			$visite_image = $info_stats_admin['visite'] ;
			$requete_stats_admin->closeCursor();
		}		
		// Si le site à un site internet
		if(!empty($info['site_internet']))
		{
			$tableau_site_internet = explode('://' , $info['site_internet']) ; 
			$nombre_entree = count($tableau_site_internet) ;
			// Si y a pas de http://
			if($nombre_entree == 1)
			{
				$site_internet = 'http://'.$info['site_internet']; 
			}
			else
			{
				$site_internet = $info['site_internet'];
			}
		}
		else
		{
			$site_internet = "non_site" ;
		}
		
		// On ajoute 1 au nombre de vue du plat à condition qu'il s'agit pas de notre plat
		if(!empty($_SESSION['login']) && $_SESSION['login'] == $info['login'])
		{}
		else
		{
			$id_plat = $info['id'] ; 
			
			$requete_vue = $bdd->prepare('UPDATE plat SET nombre_vue = nombre_vue + 1 WHERE id = :id');
			$requete_vue->execute(array(':id' => $id_plat)) ;
		}
		
		// Récupération du nouvelle id
		$id_plat = recuperation_id_plat($_SESSION['images']);
		
		// Nombre de j'aime 
		$resultat_jaime = recuperation_jaime_plat($info['id']) ;
		
		$nombre_jaime = $resultat_jaime['nombre_jaime']; 
		$deja_jaime_vote = $resultat_jaime['deja_jaime_vote'] ; 
		
		// Si une réduciton est associé au plat
		
		if($info['id_reduction'] != 0)
		{
			$requete_reduction = $bdd->prepare("SELECT id,libelle FROM reductions WHERE id = :id_reduction ");
			$requete_reduction->execute(array(':id_reduction' => $info['id_reduction'])) ;

			if($donnees_reduction = $requete_reduction->fetch())
			{
				$reduction_libelle = $donnees_reduction['libelle'] ; 
				$reduction_id = $donnees_reduction['id'] ; 
			}
		}
		else
		{
			$reduction_libelle = '';
			$reduction_id = '';
		}
		
		// Nombre de fois que le resto à déjà été vu a la suite
		
		// Si on à déjà fais une recherche avant 
		if(!empty($_SESSION['login_client_actuel']))
		{
			// Si la précédente recherche et l'image actuelle sont du meme resto
			if($_SESSION['login_client_actuel'] == $info['login'])
			{
				if(!empty($_SESSION['nombre_resto_vu_suite']))
				{
					$_SESSION['nombre_resto_vu_suite']++ ; 
				}
				else
				{
					$_SESSION['nombre_resto_vu_suite'] = 1 ; 
					
				}
			}
			else
			{
				$_SESSION['nombre_resto_vu_suite'] = 1 ;
			}
		}
		else
		{
			$_SESSION['nombre_resto_vu_suite'] = 1 ;
		}
		
		// Ajout de point pour la personne si elle est connecté 
		if(!empty($_SESSION['login_visiteur']))
		{
			include ('../../include/points-dmf.php');
			
			$ajout_point = $point_dmf_visiteur['vue_plat'] ; 
			ajouter_point_utilisateur($ajout_point) ; 
		}
		
		$nombre_resto_vu_suite = $_SESSION['nombre_resto_vu_suite'] ; 
		
		// On créer une session de l'image actuelle
		$_SESSION['image_actuelle'] = $info['id'] ; 
		
		// On créer un login actuel qui est indispensable pour le voir tout les plats
		$_SESSION['login_client_actuel'] = $info['login'] ; 
		
		// On créer une session image précédente pour dire que l'on à déjà vu ce plat
		$_SESSION['image_precedente'] = $info['idimage'] ;
		
		// On créer une session de l'id de l'image actuelle
		$_SESSION['id_image_actuelle'] = $info['id'] ; 
		
		// On récupère l'id du resto actuel
		$_SESSION['id_resto_actuel'] = $info['id_client']; 

		$id_resto = $info['id_client'] ; 
		$nomplat = $info['nomplat']	 ;	
		$nomresto = $info['nomresto'] ; 
		$type_resto = $info['type_resto'] ; 
		$adresse_resto = $info['adressresto'] ; 
		$ville = $info['ville'] ; 
		$idimage =  $info['idimage']; 
		$id_plat = $info['id']  ;
		$versionning = versionning('../../plats/'.$info['idimage'].'.jpg') ; 
		$coordonnees_latitude = $info['coordonnees_latitude'] ;
		$coordonnees_longitude = $info['coordonnees_longitude'] ; 
		$attribus_resto = $info['attribus']; 
		$url_image = $_SERVER['HTTP_HOST'].'/'.$info['idimage'].'.html' ; 
		// Récupération de la note totale de ce restaurant
		$note_avis_resto = recup_note_resto($info['id_client']) ; 
		
		// On regarde si le client est un mobile ou pas
		$mobile_device = check_appareil_mobile();
		
		// Si c'est un mobile c'est 1 sinon c'est 0
		if(!empty($mobile_device))
		{
			$image_facade = 'compte-resto/image-resto/mobiles/'.$info['image_facade'].'.'.versionning('../../compte-resto/image-resto/mobiles/'.$info['image_facade'].'.jpg').'.jpg' ;
		}
		else
		{
			$image_facade = 'compte-resto/image-resto/'.$info['image_facade'].'.'.versionning('../../compte-resto/image-resto/mobiles/'.$info['image_facade'].'.jpg').'.jpg' ;
		}
		
		$note_finale = $note_avis_resto['note_finale'] ;
		$nombre_avis_vote = $note_avis_resto['nombre_vote'] ;
		
		$history_api = 1  ;
	}
	else
	{
		// Lorsque le plat n'hesite pas ou plus
		$id_resto = '' ;
		$nomplat = '' ;	
		$nomresto = '' ; 
		$type_resto = '' ; 
		$adresse_resto = '' ; 
		$ville = '' ; 
		$idimage =  'admin/plat-inexistant' ; 
		$id_plat = 0 ;
		$versionning = versionning('../../plats/admin/image-non-ville.jpg'); 
		$coordonnees_latitude = '0' ;
		$coordonnees_longitude = '0' ;
		$attribus_resto = ''; 
		$url_image = 'http://test.donnemoifaim.fr/menu-gourmand.html' ; 
		// Récupération de la note totale de ce restaurant
		$note_avis_resto = 0 ; 
		$note_finale = 0 ;
		$nombre_avis_vote = 0 ;
		$nombre_jaime = 0 ;
		$deja_jaime_vote= 0;
		$site_internet='' ; 
		$reduction_libelle = '';
		$reduction_id = 0 ;
		$history_api= 0;
		$image_facade = '' ; 
		$nombre_resto_vu_suite = '' ; 
	}
	
	$reponseinfo->closeCursor();
}
else
{
	// Lorsque le plat n'hesite pas ou plus
		$id_resto = '' ;
		$nomplat = 'Ville non trouvé' ;	
		$nomresto = 'Auncun plat associé à la ville désiré' ; 
		$type_resto = '' ; 
		$adresse_resto = '' ; 
		$ville = '' ; 
		$idimage =  'admin/image-non-ville' ; 
		$versionning = versionning('../../plats/admin/image-non-ville.jpg'); 
		$coordonnees_latitude = 0 ;
		$coordonnees_longitude = 0 ; 
		$attribus_resto = ''; 
		$url_image = '' ; 
		// Récupération de la note totale de ce restaurant
		$note_avis_resto = 0 ; 
		$id_plat = 0 ; 
		$note_finale = 0 ;
		$nombre_avis_vote = 0 ;
		$nombre_jaime = 0 ;
		$deja_jaime_vote= 0;
		$site_internet='' ; 
		$reduction_libelle = '';
		$reduction_id = 0 ;
		$history_api = 0;
		$image_facade = '' ; 
		$nombre_resto_vu_suite = '' ;

}
 
 
 // Envoi des données au format JSON
$tableau_json = array('' , 'id_resto' => $id_resto,'nomplat' => $nomplat , 'nomresto' => $nomresto, 'type_resto' => $type_resto,'adressresto' =>$adresse_resto.' <br /> '. $ville, 'image_plat' => $id_plat ,'idimage' => $idimage,'versionning' => $versionning,'nombre_jaime' => $nombre_jaime , 'deja_jaime_vote' => $deja_jaime_vote , 'site_internet' => $site_internet , 'reduction_libelle' => $reduction_libelle , 'reduction_id' => $reduction_id, 'note_avis_resto' => $note_finale, 'nombre_vote_avis_resto' => $nombre_avis_vote,  'coordonnees_latitude' => $coordonnees_latitude,'coordonnees_longitude' => $coordonnees_longitude , 'attribus_resto' => $attribus_resto, 'url_image' => $url_image, 'history_api' => $history_api , 'image_facade' => $image_facade, 'nombre_resto_vu_suite' => $nombre_resto_vu_suite); 

echo json_encode($tableau_json) ; 