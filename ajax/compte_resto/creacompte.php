<?php
session_start();

include ('../../include/configcache.php') ;
include ('../../include/fonctions.php') ;

$erreur = '' ; 

if (!empty($_POST['loginformulaire']))
{
	if(strlen($_POST['loginformulaire']) > 5)
	{	
		// Check voir si le login existe déjà dans la base client
		$reponse_login = $bdd->prepare("SELECT login FROM client WHERE login = :login "); 
		$reponse_login->execute(array(':login' => $_POST['loginformulaire']));

		if($donnees = $reponse_login->fetch())
		{
			$login_indispo = $donnees['login']; 
		}
		
		$reponse_login->closeCursor(); // Termine le traitement de la requête
		
		// Si le login existe
		if (!empty($login_indispo)) 
		{
			$erreur .= '<span class="erreur">- Nom d\'utilisateur indisponible</span><br />' ; 
		}
		else
		{
			// Si le nom d'utilisateur est mal formaté
			$regex_pseudo = regex_pseudo() ; 
			if (preg_match('#^'.$regex_pseudo.'$#', strtolower($_POST['loginformulaire'])))
			{
				$login = $_POST['loginformulaire'] ; 
			}
			else
			{
				$erreur .= '<span class="erreur">- Nom d\'utilisateur mal formaté (lettres/chiffres uniquement)</span><br />' ; 
			}
		}
	}
	else
	{
		$erreur .= '<span class="erreur">- Le nom d\'utilisateur est trop court</span><br />' ; 
	}
}
else
{
	// On génère pas d'erreur si c'est une modification
	if(!empty($_POST['type_formulaire']) && $_POST['type_formulaire'] == 'modif')
	{}
	else
	{
		$erreur .= '<span class="erreur">- Nom d\'utilisateur manquant</span><br />' ; 
	}
}
// Vérification mot de passe
if (!empty($_POST['mdpformulaire'])) 
{
	if(strlen($_POST['mdpformulaire']) > 5)
	{
		// Verif du mot de passe
		if(!empty($_POST['verif_mdp'])) 
		{
			// Si le champ vérif de mot de passe est bien égale au champ mot de passe
			if($_POST['verif_mdp'] ==  $_POST['mdpformulaire'])
			{
				$mdp = securise_mdp_compte_resto($_POST['mdpformulaire']) ;
			}
			else
			{
				$erreur .= '<span class="erreur">- Vérifcation du mot de passe et mot de passe non similaire</span><br />' ; 
			}
		}
		else
		{
			$erreur .= '<span class="erreur">- Vérifcation du mot de passe manquante</span><br />' ; 	
		}
	}
	else
	{
		$erreur .= '<span class="erreur">- Mot de passe trop court</span><br />' ; 
	}
}
else
{
	// On génère pas d'erreur si c'est une modification
	if(!empty($_POST['type_formulaire']))
	{
		// On récupère simplement le mot de passe
		$reponse_mdp = $bdd->prepare("SELECT mdp FROM client WHERE login = :login "); 
		$reponse_mdp->execute(array(':login' => $_SESSION['login']));

		if($donnees_mdp = $reponse_mdp->fetch())
		{
			$mdp = $donnees_mdp['mdp']; 
		}
	}
	else
	{
		$erreur .= '<span class="erreur">- Mot de passe manquant</span><br />' ; 
	}
}
if(!empty($_POST ['nomresto']))
{
	// Remplacement des caractères éventuel qui feront tout beugger
	$nomresto  = $_POST['nomresto'] ; 
}
else
{
	$erreur .= '<span class="erreur">- Nom du resto manquant</span><br />' ; 
}
if(!empty($_POST ['adressresto']))
{
	$adressresto  = $_POST ['adressresto'] ;
}
else
{
	$erreur .= '<span class="erreur">- Adresse resto manquante</span><br />' ; 
}
if(!empty($_POST ['ville']))
{
	$ville  = $_POST ['ville'] ;
}
else
{
	$erreur .= '<span class="erreur">- Ville manquante</span><br />' ; 
}
if(!empty($_POST ['mail']))
{
	$regex_mail = regex_mail();
	
	if(preg_match($regex_mail, $_POST['mail']))
	{
		// Check si l'email est déjà utilisée								
		$reponse_mail = $bdd->prepare("SELECT login,mail FROM client WHERE mail = :mailverif "); 
		$reponse_mail->execute(array(':mailverif' => $_POST['mail'] )) ;
		
		// Si l'adresse existe
		if ($donnees = $reponse_mail->fetch())
		{
			if(!empty($_POST['type_formulaire']))
			{
				// Si c'est une modification on va simplement vérifier que le mail est bien au client si il existe
				if(!empty($_SESSION['login']) && $_SESSION['login'] == $donnees['login'])
				{
					$mail  = $_POST ['mail'] ;
					$mail_crypte = securise_mdp($mail) ; 
				}
				else
				{
					$erreur .= '<span class="erreur">- Adresse email déjà utilisée</span><br />' ;
				}
			}
			else
			{
				$erreur .= '<span class="erreur">- Adresse email déjà utilisée</span><br />' ;
			}
		}
		else
		{
			$mail  = $_POST ['mail'] ;
			$mail_crypte = securise_mdp($mail) ; 
		}
		
		$reponse_mail->closeCursor(); // Termine le traitement de la requête
	}
	else
	{
		$erreur .= '<span class="erreur">- Adresse email erronée</span><br />' ; 
	}
}
else
{
	// On génère pas d'erreur si c'est une modification
	if(!empty($_POST['type_formulaire']))
	{}
	else
	{
		$erreur .= '<span class="erreur">- Adresse email manquante</span><br />' ; 
	}
}
// Si il n 'y à aucune erreur on créer le compte	
if($erreur == '')
{
	// Si les coordonnées sont bien des numériques
	if(is_numeric($_POST['coordonnees_latitude']) AND is_numeric($_POST['coordonnees_longitude']))
	{
		
		if(!empty($_POST['type_formulaire']) AND $_POST['type_formulaire'] == 'modif' AND !empty($_SESSION['login']))
		{
			// Si on avait bien demandé une modification
			if(!empty($_SESSION['token']) && !empty($_POST['token']) && $_POST['token'] == $_SESSION['token'])
			{
				$nomresto_formate = renomage_url_fichier($nomresto) ; 
				
				$nouvelle_image_facade = $nomresto_formate.'-'.$_SESSION['id_client']; 
				
				// On va renomer le nom du fichier de la facade si il existe dans la base de données
				$requete_nom_facade = $bdd->prepare('SELECT image_facade FROM client WHERE login = :login') ;
				$requete_nom_facade->execute(array(':login' => $_SESSION['login'])) ; 
				
				if($donnees_facade = $requete_nom_facade->fetch())
				{
					$image_facade = $donnees_facade['image_facade'] ; 
					
					rename('../../compte-resto/image-resto/'.$image_facade.'.jpg' , '../../compte-resto/image-resto/'.$nouvelle_image_facade.'.jpg') ;
					rename('../../compte-resto/image-resto/miniature/'.$image_facade.'.jpg', '../../compte-resto/image-resto//miniature/'.$nouvelle_image_facade.'.jpg') ;
					rename('../../compte-resto/image-resto/mobiles/'.$image_facade.'.jpg', '../../compte-resto/image-resto/mobiles/'.$nouvelle_image_facade.'.jpg') ; 
				}
				
				// On créer une autre requete si c'est une modification
				$reponse_modification = $bdd->prepare('UPDATE client SET mdp = :mdp, nomresto = :nomresto,adressresto = :adressresto, ville = :ville, mail = :mail, mail_crypte = :mail_crypte, type_resto = :type_resto, telephone = :telephone, site_internet = :site_internet,image_facade = :image_facade, adresse_postal = :adresse_postal, coordonnees_latitude = :coordonnees_latitude, coordonnees_longitude = :coordonnees_longitude WHERE login = :login');
				$reponse_modification->execute(array(':mdp' => $mdp, ':nomresto' => $nomresto ,':adressresto' => $adressresto, ':ville' => $ville, ':mail' => $mail, ':mail_crypte' =>$mail_crypte , ':type_resto' => $_POST['type_resto'] , ':telephone' => $_POST['telephone'] , ':site_internet' => $_POST['site_internet'] , ':image_facade' => $nouvelle_image_facade , ':adresse_postal' => $_POST['adresse_postal'] ,  ':coordonnees_latitude' => $_POST['coordonnees_latitude'] ,':coordonnees_longitude' => $_POST['coordonnees_longitude'] , ':login' => $_SESSION['login']));
				
				// On réécrit les sessions 
				$_SESSION['mdp'] = $mdp;
				$_SESSION['nomresto'] = $nomresto;
				$_SESSION['adressresto'] = $adressresto;
				$_SESSION['ville'] = $ville;
				$_SESSION['mail'] = $mail;
				$_SESSION['telephone'] = $_POST['telephone'];
				$_SESSION['site_internet'] = $_POST['site_internet'];
				$_SESSION['type'] = $_POST['type_resto'] ; 
				$_SESSION['image_facade'] = $nouvelle_image_facade;
				
				//htmlentities sur tout les array sensible pour éviter les failles xss
				$_SESSION = protection_array_faille_xss($_SESSION) ;
			}
			else
			{
				$erreur = '<span class="erreur">'.$erreur_faille_csrf.'</span><br />' ;
			}
		}
		else
		{
			$reponse_creation = $bdd->prepare('INSERT INTO client(login, mdp,nomresto, adressresto, ville,mail, mail_crypte, type_resto, telephone, site_internet,adresse_postal , coordonnees_latitude, coordonnees_longitude) VALUES(?,?,?,?,?,?,?,?,?, ?, ?, ?, ?)');
			$reponse_creation->execute(array($login, $mdp , $nomresto ,$adressresto, $ville, $mail, $mail_crypte,  $_POST['type_resto'] , $_POST['telephone'] , $_POST['site_internet'] , $_POST['adresse_postal'], $_POST['coordonnees_latitude'] , $_POST['coordonnees_longitude']));
			
			// Pour la facade du resto on doit récupérer l'id donc on peut pas le mettre en meme temps
			$id_compte = $bdd->lastInsertId() ;
		
			$nomresto_formate = renomage_url_fichier($nomresto) ; 
			$image_facade = $nomresto_formate.'-'.$id_compte;
			
			// On met la nouvelle facade dans la base de donnée
			$update_image_facade = $bdd->prepare('UPDATE client SET image_facade = :image_facade WHERE id = :id');
			$update_image_facade->execute(array(':image_facade' => $image_facade , ':id' => $id_compte)) ; 
			
			$_SESSION['compte_tout_juste_cree'] = $login ;
			
			// On met la photo de la facade prise par google
			$url = 'https://maps.googleapis.com/maps/api/streetview?size=1000x1000&location='.$_POST['coordonnees_latitude'].','.$_POST['coordonnees_longitude'].'&key='.$cle_api_google;
			$img = '../../compte-resto/image-resto/'.$image_facade.'.jpg' ;
			$img_miniature = '../../compte-resto/image-resto/miniature/'.$image_facade.'.jpg' ;
			$img_mobile = '../../compte-resto/image-resto/mobiles/'.$image_facade.'.jpg' ;
			
			// On sauvegarde le tout 
			file_put_contents($img, file_get_contents($url));
			file_put_contents($img_miniature, file_get_contents($url));
			file_put_contents($img_mobile, file_get_contents($url));
			
			// Création d'un token pour le remplissage des attribus
			$_SESSION['token'] = generate_token() ; 
			$_SESSION['date_token'] = time();
			
			// Création d'un beau mail pour dire ce que peux faire l'utilisateur etc.. très utile pour les restaurant que l'on rentre nous meme !
			
			$sujet = 'DonneMoiFaim : Votre compte '.$login.' validé !' ; 
			
			$message_txt = 'Félicitations ! Votre compte '.$login.' vient d\'être créé et validé avec succès. Vous pouvez dors et déjà ajouter vos plats en toute simplicité. 
			
			Pour modifier vos informations personnelles (informations complémentaires, contact, façade commerce, etc..) il suffit de vous connecter à cette adresse : '.$protocole_site.''.$_SERVER['HTTP_HOST'].'/connection-compte.html
			
			Si votre compte a été créé par un administrateur : 
			
			- Votre nom d\'utilisateur est le suivant :  '.$login.'
			- Votre mot de passe est le suivant : '.$_POST['mdpformulaire'].'
			
			Nous restons à votre disposition en cas de besoin et vous remercions pour votre confiance.
			
			L\'équipe DonneMoiFaim.' ;
			
			$message_html = 'Félicitations ! Votre <strong>compte DonneMoiFaim</strong> vient d\'être créé et validé avec succès. Vous pouvez dors et déjà <strong>ajouter vos plats</strong> en toute simplicité.<br /><br /> 
			
			Pour <strong>modifier vos informations personnelles</strong> (informations complémentaires, contact, façade commerce, etc..) il suffit de vous connecter à cette adresse : <a href="'.$protocole_site.''.$_SERVER['HTTP_HOST'].'/connection-compte.html">se connecter</a><br /><br />
			
			Si votre compte a été créé par un administrateur : <br /><br />
			
			- Votre nom d\'utilisateur est le suivant :  <strong>'.$login.'</strong><br />
			- Votre mot de passe est le suivant : <strong>'.$_POST['mdpformulaire'].'</strong> <br /><br />
			
			Nous restons à votre disposition en cas de besoin et vous remercions pour votre confiance.<br /><br />
			
			L\'équipe DonneMoiFaim.' ;
			
			envoi_mail($mail , $sujet, $message_txt, $message_html) ; 
		}
	}
	else
	{
		if($_POST['coordonnees_latitude'] == 'erreur' AND $_POST['coordonnees_longitude'] == 'erreur')
		{
			// Si google n'est pas parvenue à trouver l'adresse
			$erreur .= '<span class="erreur"> L\'adresse resto semble éronnée, vérifiez que votre adresse soit valide :
			<br />- adresse resto à vérifier<br />- Ville resto à vérifier<br /><br /> Si le problème persiste, contactez-nous.</span><br />' ; 
		}
		else
		{
			// Sinon il y a une erreur interne ou bien un hacker essait d'accéder au site 
			$erreur .= '<span class="erreur">'.$erreur_interne.'</span><br />' ; 
		}
	}
}

if($erreur == '')
{
	$erreur = 0 ; 
}
if(!empty($_SESSION['token']))
{
	$token = $_SESSION['token'] ; 
}
else
{
	$token = 0 ; 
}

$tableau_json = array('erreur' => $erreur , 'coordonnees_latitude' => $_POST['coordonnees_latitude'] , 'coordonnees_longitude' => $_POST['coordonnees_longitude'] , 'token_compte_juste_cree' => $token) ; 

echo json_encode($tableau_json) ; 