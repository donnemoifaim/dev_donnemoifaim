<?php 
if($code_valide_securite == 'masterwx10warcraft10$')
{
	$ensemble_nom_plat_text = '';
	$ensemble_nom_plat_html = '';
	
	// Si c'est une offre d'essai, il n'y a pas de $id_commande
	if(!empty($offre_version_essais) AND $offre_version_essais == 1)
	{
		$id_commande = 0 ; 
	}
	
	for($i = 0 ; $i < $nombre_plat ; $i++)
	{	
		//Création d'une variable pour pouvoir remplacer les espace par des tirets pour le référencement
		$nom_plat = $nom_plats[$i] ; 
		$idimage_plat = $id_plats[$i] ; 
		
		$nom_plat_url = $nom_plat ;

		
		//FONCTION DE CONVERTION DU NOM POUR URL DISPO dans admin/fonctions.php
		$nom_plat_url = renomage_url_fichier($nom_plat_url) ;
		
		// Ici pour la modification récupération de l'ID et reconstition complete du nom du fichier
		$new_idimage = choix_id_image($nom_plat_url) ; 
		
		// Si c'est l'id propre qui renouvelle
		if(!empty($renouvellement[0]) && $renouvellement[0] == $idimage_plat)
		{
			// insertion du plat // Bien mettre a zero pour demande de le valider quoi 
			$req_ajout_plat = $bdd->prepare('UPDATE plat SET nomplat = :nomplat, idimage = :idimage, abo = :abo , date_ = :date_ , etat = 0, id_commande WHERE idimage = :idimage_ancien') ;
			$req_ajout_plat->execute(array(':idimage_ancien' => $renouvellement[1] , ':nomplat' => $nom_plat , ':idimage' => $new_idimage, ':abo' => $abonnement , 'date_' => time() , ':id_commande' => $id_commande));
		}
		else
		{
			// insertion du plat
			$req_ajout_plat = $bdd->prepare('INSERT INTO plat(nomplat, login, idimage, abo, date_ , id_commande) VALUES(?,?,?,?, ?, ?)');
			$req_ajout_plat->execute(array($nom_plat, $login, $new_idimage, $abonnement , time() , $id_commande));
		}
		
		// Récupération de l'id du plat
		if(!empty($renouvellement[0]) && $renouvellement[0] == $idimage_plat)
		{
			$id_plat = $renouvellement[1] ;
		}
		else
		{
			// Récupération de l'id de ce plat qui vient d'etre ajouté
			$id_plat = $bdd->lastInsertId();
		}
				
		// Si il y à une réduction attribué aux images
		if(!empty($id_reduction))
		{
			// On vérifie que id_plat soit bien un numéro
			if(is_numeric($id_plat) AND is_numeric($id_reduction))
			{
				// On rajoute cette réduction à la table des réductions
				lie_reduction_plat($id_plat , $id_reduction) ; 
			}
		}
		
		// Variable pour la tache
		$libelle = 'Validation plat '.$new_idimage ; 
		$description = 'Ajout du nouveau plat : '.$new_idimage.' à valider' ;
		
		// insertion de la tache admin pour valider le plat
		ajout_tache_admin($libelle, $description, 3, 'validation_plat' , $new_idimage) ;
		
		// On récupère le nom du resto 
		$requete_resto = $bdd->prepare('SELECT nomresto, ville FROM client WHERE login = :login') ;
		$requete_resto->execute(array(':login' => $login)) ; 
		
		if($info_resto = $requete_resto->fetch())
		{
			$nomresto = $info_resto['nomresto'] ;
			$ville = $info_resto['ville'] ; 
		}
		
		// Si l'option news est valide
		if(!empty($option_news_ok))
		{
			$intitule = $nom_plat ;
			$contenu = $nomresto.' vous présente son nouveau délice : <br /><br />' ;
			$image_news = '/plats/miniature/'.$new_idimage.'.jpg' ;
			$id_unique = $id_plat;	
			$categorie = 'ajout_plat' ;
			$cible = '*' ;
			$duree_alert = 604800 ; 
			
			// On créer la news pour l'envoyer dans le fil d'actualité
			$id_news = creer_news($intitule, $contenu, $image_news,$id_unique, $ville, $categorie,$cible, $duree_alert) ; 
			
			// insertion de la tache admin pour valider le plat
			ajout_tache_admin('Valider news', 'Valider la news suivante : ', 3, 'validation_news' , $id_news) ;
		}
		
		//on copie colle l'image du temporaire aux images
		copy('../../temporaire/'.$idimage_plat.'.jpg','../../plats/'.$new_idimage.'.jpg');
		//destruction du fichier
		unlink('../../temporaire/'.$idimage_plat.'.jpg') ; 

		//Création de la miniature
		//on copie colle l'image du temporaire aux images
		copy('../../temporaire/miniature/'.$idimage_plat.'.jpg','../../plats/miniature/'.$new_idimage.'.jpg');
		//destruction du fichier
		unlink('../../temporaire/miniature/'.$idimage_plat.'.jpg') ;
		
		//Création de la version mobiles
		//on copie colle l'image du temporaire aux images
		copy('../../temporaire/mobiles/'.$idimage_plat.'.jpg','../../plats/mobiles/'.$new_idimage.'.jpg');
		//destruction du fichier
		unlink('../../temporaire/mobiles/'.$idimage_plat.'.jpg') ;

		//sauvegarde écrite dans un fichier texte 
		$sauvegarde_base = '<table name="plat"><column name="nomplat">'.$nom_plat.'</column><column name="date_">'.time().'</column><column name="idimage">'.$new_idimage.'</column><column name="abo">'.$abonnement.'</column><column name="login">'.$login.'</column></table>';

		$base_donnees_plat= fopen('../../logs/base_donnees_plat.txt', 'a');
		fputs($base_donnees_plat, $sauvegarde_base);
		fclose($base_donnees_plat);
		
		$ensemble_nom_plat_text .= '
		- '.$nom_plat.'

		'; 
		
		$ensemble_nom_plat_html .= '- '.$nom_plat.'<br />' ;

		
		
	}
	// Si c'était la version d'essais on la supprime
	if(!empty($offre_version_essais) AND $offre_version_essais == 1)
	{
		//on enlève la version d'essais qui ne doit être qu'une fois !
		$requete_etat = $bdd->prepare('UPDATE client SET offre_speciale = :offre_speciale WHERE login = :login ');
		$requete_etat->execute(array('offre_speciale' => 0 , 'login' => $_SESSION['login']));

		$requete_statistique = $bdd->prepare('INSERT INTO statistique(idimage,login) VALUES (:idimage, :login)');
		$requete_statistique->execute(array(':idimage' => $_SESSION['idimage_0'], ':login' => $_SESSION['login']));

		unset($_SESSION['offre_decouverte']) ;
		
		// Supression de toute les session de plat qui ont été créer
		if(!empty($_SESSION['nombre_image']))
		{ 
			for($i=0 ; $i < $_SESSION['nombre_image'] ; $i++)
			{
				unset($_SESSION['nomplat'.$i]) ;
				unset($_SESSION['idimage_'.$i]) ;
			}
			// supression de la variable qui compte le nombre de plat
			unset($_SESSION['nombre_image']);
		}
	}
	else
	{
		// Sinon on met que le statut de la commande à été réglé et également la date
		$requete_commande_statut = $bdd->prepare('UPDATE commandes SET statut = :statut WHERE id_commande = :id_commande') ;
		$requete_commande_statut->execute(array('id_commande' => $id_commande , ':statut' => 'payé')) ;
	}
	
	// Si le mail n'existe pas, sinon il est déjà défini dans la commande
	if(!isset($mail))
	{
		// Récupération du mail client
		$requete_mail = $bdd->prepare('SELECT mail FROM client WHERE login = :login') ; 
		$requete_mail->execute(array(':login' => $login)) ; 
		
		// Si il existe
		if ($info_mail = $requete_mail->fetch())
		{
			$mail = $info_mail['mail']; // Déclaration de l'adresse de destination.
		}
		// Sinon probleme
		else
		{
			erreur_site('Erreur creation image' , 'creation_commande' , 'ADRESSE EMAIL ERONNE CLIENT', 'impossible d\'envoyer un mail de confirmation des plats à un client son adresse est vide ou erronée : '.$login , $id_commande , 1 ) ;
		}
	}
	
	$sujet = 'DonneMoiFaim : ajout de vos plats';
	
	// Inclusion du mail à envoyer ( on ne le met pas ici pour un soucis de lisibilité)
	include('model_mail_ajout_plat.php') ;
	
	envoi_mail($mail , $sujet, $message_txt, $message_html) ; 
	
	// Si le serveur est celui de test 
	
	if($_SERVER['HTTP_HOST'] == 'test.donnemoifaim.fr')
	{
		// On envoi un mail pour faire ce qu'il y a à faire coté admin
		envoi_mail($mail_contact_site_test , 'copie : '.$sujet, $message_txt, $message_html) ;
	}
	else
	{
		envoi_mail($mail_contact_site , 'copie : '.$sujet, $message_txt, $message_html) ;
	}
}
else
{
	erreur_site('Erreur creation image' , 'creation_commande' , 'Le mot de passe interne de sécurité est éronné celui affiché : '.$code_valide_securite , $id_commande , 1 ) ;
}