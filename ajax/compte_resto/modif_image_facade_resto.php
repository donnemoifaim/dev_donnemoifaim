<?php
session_start();

include ('../../include/configcache.php') ;
include ('../../include/fonctions.php') ; 

if(!empty($_SESSION['login']))
{
	$login = $_SESSION['login'] ; 
}
elseif(!empty($_SESSION['compte_tout_juste_cree']))
{
	$login = $_SESSION['compte_tout_juste_cree'] ; 
}
if(!empty($login))
{
	$requete_resto_creer = $bdd->prepare('SELECT id, nomresto FROM client WHERE login = :login') ; 
	$requete_resto_creer->execute(array(':login' => $login));
	
	if($donnees_resto = $requete_resto_creer->fetch())
	{
		$nom_facade_resto = $donnees_resto['nomresto'].'-'.$donnees_resto['id'] ;
	}
	else
	{
		$erreur .= 'Votre compte resto n\'existe pas, veuillez nous contacter si le problème persiste.' ;
	}
	
	//FONCTION DE CONVERTION DU NOM POUR URL DISPO dans admin/fonctions.php
	$nom_facade_resto = renomage_url_fichier($nom_facade_resto) ;

	$requete_nom_plat = $bdd->prepare('UPDATE client SET image_facade = :image_facade WHERE login = :login');
	$requete_nom_plat->execute(array(':image_facade' => $nom_facade_resto , ':login' => $login));
	
	// transferer la session image
	if(!empty($_SESSION['facade_resto_modif']) && is_file('../../temporaire/'.$_SESSION['facade_resto_modif'].'.jpg'))
	{
		// On copie colle dans le bon dossier
		copy('../../temporaire/'.$_SESSION['facade_resto_modif'].'.jpg' , '../../compte-resto/image-resto/'.$nom_facade_resto.'.jpg') ; 
		copy('../../temporaire/miniature/'.$_SESSION['facade_resto_modif'].'.jpg' , '../../compte-resto/image-resto/miniature/'.$nom_facade_resto.'.jpg') ;
		copy('../../temporaire/mobiles/'.$_SESSION['facade_resto_modif'].'.jpg' , '../../compte-resto/image-resto/mobiles/'.$nom_facade_resto.'.jpg') ;
		
		// On ne supprime pas les fichiers au cas ou la personne clique sur précédent à la création de compte.
	}
	else
	{
		$erreur .= 'Le fichier que vous tentez d\'envoyer ne se trouve plus sur nos serveurs, veuillez répéter l\'opération. Si le problème persiste veuillez nous contacter.' ;
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

$tableau_json = array('erreur' => $erreur , 'nom_facade_resto' => $nom_facade_resto) ; 

echo json_encode($tableau_json) ; 