<?php
function envoi_mail_supression_plat($tableau_donnees)
{
	global $protocole_site;
	global $mail_contact_site;
	
	$mail = $tableau_donnees['mail'] ;
   $idimage = $tableau_donnees['idimage'] ;
   $date = $tableau_donnees['date_'] ;
   $nomplat = $tableau_donnees['nomplat'] ;
   $login = $tableau_donnees['login'] ;
 
	//=====Déclaration des messages au format texte et au format HTML.
	$message_txt = 'Expiration de votre plat'.$nomplat.'.
	Votre image '.$nomplat.'n\'est plus affichée sur notre site car votre abonnement est parvenu à son terme.

	Vous pouvez dès à présent ajouter une nouvelle image en suivant le lien : http://donnemoifaim.fr/ajout-de-plat.php
	
	Vous pouvez également remettre votre ancien plat en ligne en un clic sur votre compte, rubrique vos plats-> plats hors ligne.

	Cordialement, l\'équipe DonneMoiFaim.' ;
	$message_html = '
	<html>
		<head>
		<style>
			strong{color:#db302d}
			p{color:#333}
		</style>
		</head>
		<body>
			<p>
				Expiration de votre plat <strong>'.$nomplat.'</strong>.<br /> Votre image n\'est plus affichée sur notre site car votre abonnement est parvenu à son terme.<br /><br />
				Vous pouvez dès à présent ajouter une nouvelle image en suivant le lien : <strong>http://donnemoifaim.fr/ajout-de-plat.php</strong><br /><br />
				Vous pouvez également remettre votre ancien plat en ligne en un clic sur votre compte, rubrique vos plats-> plats hors ligne.
				<br /><br />
				<strong>L\'équipe DonneMoiFaim.</strong>
				<a href="'.$protocole_site.''.$_SERVER['HTTP_HOST'].'"><img src="'.$protocole_site.''.$_SERVER['HTTP_HOST'].'/imgs/logo-donnemoifaim.png" alt="logo donnemoifaim" /></a>
			</p>
		</body>
	</html>';

	$sujet = 'DonneMoiFaim : Votre plat hors ligne';

	//=====Envoi de l'e-mail.
	envoi_mail($mail , $sujet, $message_txt, $message_html) ;
}

function display_paiement_securise()
{?>
	<img src="/imgs/logo_paypal.png" alt="Logo paypal" /><br />
	<button style="border:0 ; outline:none" type="submit" class="zone_paiement_securise">
		Nous utilisons Paypal pour garantir un paiement entièrement sécurisé.
		<img src="/imgs/picto-securite.<?php echo versionning('imgs/picto-securite.png') ; ?>.png" alt="picto securite" />
	</button><br /><br />
<?php
}
function archivage_plat($idimage , $chemin)
{
	global $bdd ; 
	
	// Modification du statut du plat
	$requete_archivage_plat = $bdd->prepare('UPDATE plat SET etat = 2 WHERE idimage = :idimage');
	$requete_archivage_plat->execute(array(':idimage' => $idimage));

	// Archivage
	copy('../'.$chemin.'plats/'.$idimage.'.jpg','../'.$chemin.'plats/archives/'.$idimage.'.jpg');
	copy('../'.$chemin.'plats/miniature/'.$idimage.'.jpg','../'.$chemin.'plats/archives/miniature/'.$idimage.'.jpg');
	copy('../'.$chemin.'plats/mobiles/'.$idimage.'.jpg','../'.$chemin.'plats/archives/mobiles/'.$idimage.'.jpg');

	//destruction du fichier
	unlink('../'.$chemin.'plats/'.$idimage.'.jpg') ;
	unlink('../'.$chemin.'plats/miniature/'.$idimage.'.jpg') ;
	unlink('../'.$chemin.'plats/mobiles/'.$idimage.'.jpg') ;
}
function verification_abonnement_visiteur($abo, $date_abo, $login)
{
	global $bdd; 
	
	// Si il y a bien un abonnement c'est que l'utilisateur est bien abonné
	if($abo != 0)
	{
		$temps_restant = 0 ; 
		
		if($abo == 1)
		{
			$temps_restant = $date_abo + 2629743 ; 
		}
		elseif($abo == 2)
		{
			$temps_restant = $date_abo + 15778463 ; 
		}
		elseif($abo == 3)
		{
			$temps_restant = $date_abo + 31556926 ; 
		}
		
		if($temps_restant - time() > 0)
		{
			$_SESSION['seconde_restant_abonnement'] = $temps_restant - time() ;
		}
		else
		{
			$_SESSION['seconde_restant_abonnement'] = 0 ; 
		}
		
		if($temps_restant > time())
		{
			// Si temps restant est supérieur à la date actuelle c'est que la personne est encore abonné
			$_SESSION['abonnement_visiteur'] = $abo ;
		}
		else
		{
			// Sinon on fait une requete pour mettre l'abonnement à zero
			$requete_abo = $bdd->prepare("UPDATE utilisateur SET abo = 0, date_abo = 0 WHERE login = :login"); 
			$requete_abo->execute(array(':login' => $login));
			
			// Pas d'abonnement
			$_SESSION['abonnement_visiteur'] = 0 ;
		}
	}
	else
	{
		$_SESSION['abonnement_visiteur'] = 0 ;
		$_SESSION['seconde_restant_abonnement'] = 0 ; 
	}
}
function creer_news($intitule, $contenu, $image_news,$id_unique, $ville, $categorie, $cible, $duree_alert)
{
	global $bdd ;

	
	$requete_news = $bdd->prepare('INSERT INTO news(intitule, contenu, image_news, id_unique, ville, categorie, cible, duree_alert, date_ajout) VALUES(:intitule, :contenu, :image_news, :id_unique, :ville, :categorie, :cible, :duree_alert, :date_ajout)');
	$requete_news->execute(array(':intitule' => $intitule,':contenu' => $contenu , ':image_news' => $image_news, ':id_unique' => $id_unique , ':ville' => $ville , ':categorie' => $categorie, ':cible' => $cible, ':duree_alert' => $duree_alert , ':date_ajout' => time()));
	
	return $bdd->lastInsertId(); 
}
function generate_token()
{
	return uniqid(rand(), true);
}
function ajout_tache_admin($libelle, $description, $priorite, $type , $id_unique) 
{
	global $bdd ; 
	
	$requete_admin = $bdd->prepare('INSERT INTO tache_admin(libelle, description, priorite, date_ajout, type, id_unique ) VALUES(:libelle,:description,:priorite,:date_ajout, :type, :id_unique)');
	$requete_admin->execute(array(':libelle' => $libelle, ':description' => $description , ':priorite' => $priorite , ':date_ajout' => time(), 'type' => $type, ':id_unique' => $id_unique ));
}
function mauvais_mdp_compte()
{
	// Si la connexion à été bloquer
	if(!empty($_SESSION['date_blocage_fail']) OR !empty($_COOKIE['date_blocage_fail']))
	{
		if(!empty($_COOKIE['date_blocage_fail']))
		{
			$date_blocage_fail = $_COOKIE['date_blocage_fail'] ; 
		}
		elseif(!empty($_SESSION['date_blocage_fail']))
		{
			$date_blocage_fail =  $_SESSION['date_blocage_fail']  ; 
		}
		
		// Si les 10 minutes sont écoulés
		if($date_blocage_fail  < time())
		{
			if(!empty($_SESSION['date_blocage_fail']))
			{
				unset($_SESSION['date_blocage_fail']) ;
			}
			// Le cookie lui se supprimera tout seul
			
			return '<span class="erreur" >Blocage de la connexion terminé, vous pouvez réesayer. Si le problème persiste, contactez-nous.</span><br />' ; 
		}
		else
		{
			if(!empty($_SESSION['date_blocage_fail']))
			{
				$temps_restant = round(($_SESSION['date_blocage_fail'] - time()) / 60 , 0) ;
			}
			elseif(!empty($_COOKIE['date_blocage_fail']))
			{
				$temps_restant = round(($_COOKIE['date_blocage_fail'] - time()) / 60 , 0) ;
			}
			
			if($temps_restant == 1)
			{
				$mettre_un_s = '' ; 
			}
			else
			{
				$mettre_un_s = 's' ; 
			}
			
			return '<span class="erreur" >Temps restant blocage connexion : '.$temps_restant.' minute'.$mettre_un_s.'</span><br />' ; 
		}
	}
	else
	{
		if(!empty($_SESSION['nombre_fail_connection']))
		{
			$_SESSION['nombre_fail_connection']++ ; 
		}
		else
		{
			$_SESSION['nombre_fail_connection'] = 1 ; 
		}
		// Au bout de 50 tentatives on bloque pour 10 minutes
		if($_SESSION['nombre_fail_connection'] >= 100)
		{
			unset($_SESSION['nombre_fail_connection']) ;

			$_SESSION['date_blocage_fail'] = time() + 3600 ; 

			setcookie('date_blocage_fail', time() + 3600 , time() + 3600);			
			
			return '<span class="erreur" >Sécurité : blocage de la connexion pendant 60 minutes. Si le problème persiste, contactez-nous.</span><br />' ; 
		}
		else
		{
			return '<span class="erreur" >Mauvais mot de passe</span>' ; 
		}
	}
}
function vidage_session_fail_connexion()
{
	if(!empty($_SESSION['nombre_fail_connection']))
	{
		unset($_SESSION['nombre_fail_connection']) ; 
		
		if(!empty($_SESSION['date_blocage_fail']))
		{
			unset($_SESSION['date_blocage_fail']) ; 
		}
	}
}
				
function protection_array_faille_xss($array)
{
	// Array_map permet d'appliquer une fonction à tout les élément d'un tableau
	$nouveau_array = array_map(htmlentities , $array) ; 
	
	return $nouveau_array ; 
}
function check_appareil_mobile()
{
	$mobile = 0 ; 
	
	$ua = $_SERVER['HTTP_USER_AGENT'];
	if (preg_match('/iphone/i',$ua) || preg_match('/android/i',$ua) || preg_match('/blackberry/i',$ua) || preg_match('/symb/i',$ua) || preg_match('/ipad/i',$ua) || preg_match('/ipod/i',$ua) || preg_match('/phone/i',$ua) )
	{
		$mobile = 1 ; 
	}
	
	return $mobile ; 
}
// REGEX
function regex_pseudo()
{
	return '[a-zA-Z0-9-áàâäãåçéèêëíìîïñóòôöõúùûüýÿ]{6,}' ; 
}
function regex_mail()
{
	return '#^[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*@[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*[\.]{1}[a-z]{2,6}$#' ; 
} 
function niveau_site()
{
	// Calcule du niveau du site dans l'arboresance nécessaire pour le versionning des fichiers css et js et utilisable pour les img
	$nombre_slash = explode('/' , $_SERVER['REQUEST_URI']) ;
	$nombre_entree = count($nombre_slash) ;

	// La fonction explode va couper l'espace avant le premier / il faut donc enlever 1 au resultat pour être juste
	$nombre_entree = $nombre_entree - 1 ; 

	$niveau_arborescence = '';
	// Remplissage du niveau d'arborescence
	for($i = 1; $i < $nombre_entree; $i++)
	{
		$niveau_arborescence = $niveau_arborescence.'../' ;
	}
	
	return $niveau_arborescence ;
}
function page_actuelle()
{
	// On netoie les parametres éventuellement passés
	$page_actuelle = explode('?' , $_SERVER['REQUEST_URI'] ) ; 
	
	return $page_actuelle[0] ;
}
function renomage_url_fichier($nom_fichier) 
{	
	//Création d'une variable pour pouvoir remplacer les espace par des tirets pour le référencement
	$nom_fichier = str_replace(' ', '-' ,$nom_fichier) ;
	
	// On le met en htmlentities pour pouvoir faire une convertion optimale
	$nom_fichier = htmlentities($nom_fichier) ; 
	
	// On definie les variables pour pouvoir faire la convertion
	$code_html_a = array("#&Agrave;#","#&Aacute;#","#&Acirc;#","#&Atilde;#","#&Auml;#","#&Aring;#", "#&agrave;#" , "#&aacute;#" , "#&acirc;#" , "#&atilde;#" , "#&auml;#", "#&aring;#" , "#&aelig;#") ; 
	
	$code_html_e = array("#&Egrave;#" , "#&Eacute;#" , "#&Ecirc;#" , "#&Euml;#" , "#&egrave;#" , "#&eacute;#" , "#&ecirc;#" , "#&euml;#") ; 
	
	$code_html_c = array("#&Ccedil;#" , "#&ccedil;#") ; 
	
	$code_html_o = array("#&Ograve;#" , "#&Oacute;#" , "#&Ocirc;#" , "#&Otilde;#" , "#&Ouml;#" , "#&Oslash;#" , "#&eth;#" , "#&ograve;#" , "#&oacute;#" , "#&ocirc;#" , "#&otilde;#" , "#&ouml;#", "#&oslash;#" ) ; 
	
	$code_html_u = array("#&Ugrave;#" , "#&Uacute;#" , "#&Ucirc;#" , "#&Uuml;#" , "#&ugrave;#" , "#&uacute;#" , "#&ucirc;#" , "#&uuml;#") ; 
	
	$code_html_i = array("#&iexcl;#" , "#&Igrave;#" ,  "#&Iacute;#" ,  "#&Icirc;#" , "#&Iuml;#" , "#&igrave;#" , "#&iacute;#" , "#&icirc;#" , "#&iuml;#" ) ; 
	
	$code_html_n = array("#&ntilde;#" , "#&Ntilde;#") ; 
	
	$nom_fichier = preg_replace($code_html_a, 'a', $nom_fichier) ;
	$nom_fichier = preg_replace($code_html_e, 'e', $nom_fichier) ;
	$nom_fichier = preg_replace($code_html_c, 'c', $nom_fichier) ;
	$nom_fichier = preg_replace($code_html_o, 'o', $nom_fichier) ;
	$nom_fichier = preg_replace($code_html_u, 'u', $nom_fichier) ;
	$nom_fichier = preg_replace($code_html_i, 'i', $nom_fichier) ;
	$nom_fichier = preg_replace($code_html_n, 'n', $nom_fichier) ;
	
	// Décodage pour enlever tout les autres caractères que l'on aurait pas filtrer (exemple : $ , # , " ; % ECT...)
	$nom_fichier = html_entity_decode($nom_fichier) ; 
	
	// Filtrer tout ce qui reste d'incohérent
	$nom_fichier = preg_replace('#[^a-zA-Z0-9-]#', '', $nom_fichier) ;
	
	return $nom_fichier ; 
}

function choix_id_image($nom_plat_url)
{
	// Déclaration des globals 
	global $bdd;
	
	// Supression si il y a plusieurs -- d'affilé
	$new_idimage = preg_replace('#-{2,}#', '-', $nom_plat_url) ;
	
	$ok_idimage = 0 ; 
	
	// Si le nom du plat existe déjà quelque part alors on rajoute + 1 , puis ainsi de suite
	for($i=1 ; $ok_idimage != 1; $i++)
	{
		$idimage_final = $new_idimage.'-'.$i ; 
		$idimage_final = preg_replace('#-{2,}#', '-', $idimage_final) ;
		
		$req_check_idimage = $bdd->prepare('SELECT id FROM plat WHERE idimage = :idimage');
		$req_check_idimage->execute(array(':idimage' => $idimage_final));
		
		// Si la requete trouve déjà une entrée
		if($donnees_infos = $req_check_idimage->fetch())
		{}
		else
		{
			// Permet d'arreter la boucle
			$ok_idimage = 1 ; 
		}
	}
	
	return $idimage_final ; 
}

function versionning($fichier)
{
	// On recupère le niveau de l'arborescence
	global $niveau_arborescence ; 
	
	// On récupère la version du fichier spécifié
	$version_fichier = filemtime($niveau_arborescence.''.$fichier);
	
	return $version_fichier ;
}

function ajout_visite_plat($images)
{
	global $bdd; 
	
	//statistique +1 pour l'image qui vient d'être visite
	if(!empty($_SESSION['login']))
	{
		//Evite que les stat soit influencer sur notre propre plat
		$reponse_stat = $bdd->prepare("UPDATE plat SET nombre_vue = nombre_vue + 1 WHERE idimage = :idimage AND login != :login");
		$reponse_stat->execute(array(':idimage' => $images, ':login' => $_SESSION['login'])) ;
		
		$reponse_stat->closeCursor();
	}
	else
	{
		$reponse_stat = $bdd->prepare("UPDATE statistique SET nombre_vue = nombre_vue + 1 WHERE idimage = :idimage");
		$reponse_stat->execute(array(':idimage' => $_SESSION['images'])) ;
		
		$reponse_stat->closeCursor();
		
	}
}
function recuperation_id_plat($idimage)
{
	global $bdd;
	
	// On cherche l'id du plat, qui est moins lourd que l'idimage mais moins pratique
	$req_id_plat = $bdd->prepare('SELECT id FROM plat WHERE idimage = :idimage') ;
	$req_id_plat->execute(array(':idimage' => $idimage)) ;
	
	if($info_id_plat = $req_id_plat->fetch())
	{
		$id_plat = $info_id_plat['id'] ;
	}
	
	return $id_plat ; 
}
function recuperation_jaime_plat($id_plat)
{
	global $bdd; 
	
	$nombre_jaime = 0 ; 
	
	$req_jaime_compte = $bdd->query('SELECT jaime, login FROM utilisateur') ;
	
	while($info_jaime_compte = $req_jaime_compte->fetch())
	{	
		$tableau_jaime_check = explode(',' , $info_jaime_compte['jaime']) ; 
		$taille_tableau = count($tableau_jaime_check) ; 
		
		// Check si l'id du jaime est présent dans le tableau ou pas
		for($i=0 ; $i < $taille_tableau; $i++)
		{
			if($tableau_jaime_check[$i] == $id_plat)
			{
				// Si c'est L'id de session de login on récupère également si le vote a déjà été fait 
				if(!empty($_SESSION['login_visiteur']) AND $_SESSION['login_visiteur'] == $info_jaime_compte['login'])
				{
					$deja_jaime_vote = 1 ; 
				}
				// ON rajoute 1
				$nombre_jaime++ ; 
			}
		}
	}
	if(!isset($deja_jaime_vote))
	{
		$deja_jaime_vote = '' ; 
	}
	// Construction d'un tableau pour récupérer et le nombre de vote et si le vote a été déjà éffectué
	$resultat_jaime = [
		nombre_jaime => $nombre_jaime,
		deja_jaime_vote => $deja_jaime_vote
	] ; 
	
	return $resultat_jaime ; 
	
	$req_jaime_compte->closeCursor();
}

function securise_mdp($password)
{
	// Variable de salage
	$salt = "08@!ylùd";
	
	// Double salage avec le salage renforcement de sécurité
	$mdp = sha1(sha1($password).$salt);
	
	return $mdp ; 
}
function securise_mdp_compte_resto($password)
{
	$salt = ":DKqç!*QS";
	
	// Double salage avec le salage renforcement de sécurité
	$mdp = sha1(sha1($password).$salt);
	
	return $mdp ; 
}
function generer_lien_unique($url_base, $variable_unique, $action)
{
	// On prend la variable unique et on met un sha1 dessus pour pouvoir avoir beaucoup de caractere différent unique
	$variable_unique = sha1($variable_unique) ;
	// On mélange le tout pour éviter les dictionnaires
	$variable_unique = str_shuffle($variable_unique) ; 
	// On mélange le timestamp pour etre sur a 100%
	$variable_unique = $variable_unique.''.time(); 
	
	// Il suffit de rajouter une variable $action pour pouvoir le récuprer
	$url_complete = $url_base.'?'.$action.'='.$variable_unique ;
	
	return $url_complete ; 
}
function envoi_mail($mail , $sujet, $message_txt, $message_html , $piece_jointe='')
{
	global $mail_contact_site ;
	
	// Si l'adressse de réception est $mail_contact_site alors c'est que on essai d'envoyer un mail chez nous, l'éméteur va donc etre une variable que l'on va appeler $mail_emeteur
	
	if($mail_contact_site != $mail)
	{
		$mail_emeteur = $mail_contact_site ;
		$nom_emeteur = $nom_entreprise_site ; 
	}
	else
	{
		// On doit faire en sorte que le mail_emeteur existe déjà dans ses cas là 
		global $mail_emeteur ;
		global $nom_emeteur ; 
	}
	
	if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail)) // On filtre les serveurs qui présentent des bogues.
	{
		$passage_ligne = "\r\n";
	}
	else
	{
		$passage_ligne = "\n";
	}

	
	if(!empty($piece_jointe) && $piece_jointe != '')
	{
		//=====Lecture et mise en forme de la pièce jointe.
		$fichier   = fopen($piece_jointe, "r");
		$attachement = fread($fichier, filesize($piece_jointe));
		$attachement = chunk_split(base64_encode($attachement));
		fclose($fichier);
		//==========
		
		// On va récupérer l'extention de la pièce jointe
		$tableau_piece_jointe = explode('.' , $piece_jointe) ;
		$extention_piece_jointe = strtolower($tableau_piece_jointe[1]) ;
	
		// Tableau image
		$tableau_image_type_mime = array('jpg' , 'jpeg' , 'png' , 'gif');
		
		if(in_array($extention_piece_jointe , $tableau_image_type_mime))
		{
			if($extention_piece_jointe == 'jpg')
			{
				$extention_piece_jointe = 'jpeg' ; 
			}
			
			$type_mime = 'image/'.$extention_piece_jointe.';'; 
		}
		else
		{
			$type_mime = 'application/'.$extention_piece_jointe.';';
		}
		
		// Pour les pièces jointe il est nécessaire de prévenir le client qu'il y aura des fichiers
		$content_type = 'multipart/mixed' ; 
	}
	
	// Si le content_type n'est pas défini, on le définit nous meme par défaut
	if(!isset($content_type))
	{
		$content_type = 'multipart/alternative' ; 
	}
	
	//=====Création de la boundary.
	$boundary = "-----=".md5(rand());
	//==========
	
	if($content_type == 'multipart/mixed')
	{
		$boundary_alt = "-----=".md5(rand());
	}

	//=====Création du header de l'e-mail.
	$header = "From: \"$nom_emeteur\"<$mail_emeteur>".$passage_ligne;
	$header.= "Reply-to: \"$nom_emeteur\" <$mail_emeteur>".$passage_ligne;
	$header.= "MIME-Version: 1.0".$passage_ligne;
	$header.= "Content-Type: $content_type;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne ;
	//==========

	
	//=====Création du message.
	$message = $passage_ligne."--".$boundary.$passage_ligne;
	
	if($content_type == 'multipart/mixed')
	{
		$message.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary_alt\"".$passage_ligne;
		$message.= $passage_ligne."--".$boundary_alt.$passage_ligne;
	}
	//=====Ajout du message au format texte.
	$message.= "Content-Type: text/plain; charset=\"UTF-8\"".$passage_ligne;
	$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
	$message.= $passage_ligne.$message_txt.$passage_ligne;
	//==========
	
	if($content_type == 'multipart/mixed')
	{
		$message.= $passage_ligne."--".$boundary_alt.$passage_ligne;
	}
	else
	{
		$message.= $passage_ligne."--".$boundary.$passage_ligne;
	}
	 
	//=====Ajout du message au format HTML.
	$message.= "Content-Type: text/html; charset=\"UTF-8\"".$passage_ligne;
	$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
	$message.= $passage_ligne.$message_html.$passage_ligne;
	//==========
	 
	if($content_type == 'multipart/mixed')
	{
		//=====On ferme la boundary alternative.
		$message.= $passage_ligne."--".$boundary_alt."--".$passage_ligne;
		$message.= $passage_ligne."--".$boundary.$passage_ligne;
		//==========
	}
	else
	{
		$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
		$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
	}
	
	if(!empty($piece_jointe) && $piece_jointe != '' && $content_type == 'multipart/mixed')
	{
		//=====Ajout de la pièce jointe.
		$message.= "Content-Type: $type_mime name=\"$piece_jointe\"".$passage_ligne;
		$message.= "Content-Transfer-Encoding: base64".$passage_ligne;
		$message.= "Content-Disposition: attachment; filename=\"$piece_jointe\"".$passage_ligne;
		$message.= $passage_ligne.$attachement.$passage_ligne.$passage_ligne;
		$message.= $passage_ligne."--".$boundary."--".$passage_ligne;  
		//==========
	}

	//=====Envoi de l'e-mail.
	mail($mail,$sujet,$message,$header);
}

function supp_session_image($type_upload)
{
	if(!empty($_SESSION['nombre_image'.$type_upload]))
	{ 
		for($i=0 ; $i < $_SESSION['nombre_image'.$type_upload] ; $i++)
		{
			unset($_SESSION['nomplat'.$i.''.$type_upload]) ;
			unset($_SESSION['nom_image_upload'.$i.''.$type_upload]) ;
		}
		// supression de la variable qui compte le nombre de plat
		unset($_SESSION['nombre_image'.$type_upload]);
		unset($_SESSION['id_image_max'.$type_upload]);
	}
	// Si c'était un renouvellement
	if(!empty($_SESSION['idimage_renouvelle']) && !empty($_SESSION['ancienne_idimage_renouvelle']))
	{
		unset($_SESSION['idimage_renouvelle']);
		unset($_SESSION['ancienne_idimage_renouvelle']);
	}
}

function erreur_site($intitule , $type , $description, $identifiant_unique = '' ,  $ip_entrant = 0)
{
	global $bdd;
	
	if($ip_entrant == 1)
	{
		$ip_entrant = $_SERVER['REMOTE_ADDR'] ; 
	}
	
	$requete_ajout_erreur = $bdd->prepare('INSERT INTO erreur_site(intitule, type, identifiant_unique, ip_entrant, description, date_ajout) VALUES(:intitule, :type, :identifiant_unique , :ip_entrant ,:description, :date_ajout)') ;
	$requete_ajout_erreur->execute(array('intitule' => $intitule , ':type' => $type , ':identifiant_unique' => $identifiant_unique ,  ':ip_entrant' => $ip_entrant , ':description' => $description, ':date_ajout' => time())) ; 
	
	$id_erreur = $bdd->lastInsertId();
	
	// Si c'est une erreur paypal on met également le statut de la commande en erreur
	if($type =='paypal')
	{
		// Si l'identifiant unique de la commande existe
		if(!empty($identifiant_unique))
		{
			$requete_commande_statut = $bdd->prepare('UPDATE commandes SET statut = :statut WHERE id_commande = :id_commande') ;
			$requete_commande_statut->execute(array('id_commande' => $identifiant_unique , ':statut' => 'erreur_'.$id_erreur)) ;
		}
	}
}
function lie_reduction_plat($id_plat , $id_reduction)
{
	global $bdd ;
	
	// Check si l'id du plat est déjà rentré pour le remplacer
	$requete_reduction_replacement = $bdd->prepare("SELECT id,idimage FROM reductions WHERE login = :login ");
	$requete_reduction_replacement->execute(array(':login' => $_SESSION['login'])) ;

	while($donnees_reduction = $requete_reduction_replacement->fetch())
	{
		$tableau_idimage = explode(',' , $donnees_reduction['idimage']);
		
		if(in_array($id_plat , $tableau_idimage))
		{
			// Si jaimais y a une virgule avant ou après
			$id_plats_nouveau = str_replace( ','.$id_plat , '' , $donnees_reduction['idimage']) ; 
			$id_plats_nouveau = str_replace( $id_plat.',' , '' , $id_plats_nouveau) ; 
			// Sinon on enleve simplement
			$id_plats_nouveau = str_replace( $id_plat , '' , $id_plats_nouveau) ; 
			
			$requete_modification_reduction = $bdd->prepare('UPDATE reductions SET idimage = :idimage_nouveau WHERE id = :id_reduction_ancienne');
			$requete_modification_reduction->execute(array(':id_reduction_ancienne' => $donnees_reduction['id'] , ':idimage_nouveau' => $id_plats_nouveau));
		}
	}
	
	// On met l'id du plat dans la nouvelle réduction
	$requete_modification_reduction = $bdd->query('UPDATE reductions SET idimage = CASE idimage WHEN \'\' THEN CONCAT(idimage ,'.$id_plat.') ELSE CONCAT(idimage , "," ,'.$id_plat.') END WHERE id = '.$id_reduction);
	
	$requete_plat_reduction = $bdd->prepare('UPDATE plat SET id_reduction = :id_reduction WHERE id = :id_plat');
	$requete_plat_reduction->execute(array(':id_plat' => $id_plat , ':id_reduction' => $id_reduction));
}
function ajout_reduction($login , $libelle)
{
	global $bdd ; 
	
	// On ajoute la nouvelle réduction dans la base de donnée
	$requete_reduction = $bdd->prepare("INSERT INTO reductions(libelle, login) VALUES(:libelle, :login)");
	$requete_reduction->execute(array(':login' => $login , ':libelle' => $libelle)) ;
}
function affichage_etoile_avis($nombre_etoile, $type)
{
	$affichage_etoile_avis = '' ; 
	
	// Si c'est un affichage et que y à pas de vote
	if($nombre_etoile == 0 && $type == "affichage")
	{
		// Si il n'y à aucun avis on le dit
		$affichage_etoile_avis .= '<span class="texte_site_noir" >Aucun vote pour le moment.</span><br /><img style="cursor:pointer" onclick="voir_avis();ouvrir_bloc_form_avis();" src="imgs/picto-non-plat.png" width="30px" alt="aucun avis utilisateur">' ;
	}
	else
	{
		// On créer une boucle qui va passer 5 fois exactement.
		for($i =1 ; $i < 6; $i++)
		{
			if($i <= $nombre_etoile)
			{
				// Si c'est juste un affichage il y à pas besoin de mettre de onclick pour changer les étoiles
				if($type == 'affichage')
				{
					$etoile_pleine = '<img src="imgs/picto-etoile-pleine.png" alt="etoile pleine" class="etoile_avis" />' ; 
				}
				// On rajoute le onclick pour les modif et ajout d'avis
				else if($type == 'ajout-modif')
				{
					$etoile_pleine = '<img id="etoile_avis'.$i.'" src="imgs/picto-etoile-pleine.png" onclick="etoile_avis_remplissage('.$i.');" alt="etoile pleine" class="etoile_avis etoile_cliquable" />' ;  
				}
				
				$affichage_etoile_avis .= $etoile_pleine ; 
			}
			else
			{
				// Cela veut dire que le nombre d'étoile est décimale et donc que ce doit etre pour la moyenne du plats
				if($i - $nombre_etoile <= 0.8)
				{
					$etoile_decimale = '<img src="imgs/picto-etoile-presque-vide.png" alt="etoile presque vide" class="etoile_avis" />' ;
					
					// Si la moyenne est a la moitie
					if($i - $nombre_etoile <= 0.5)
					{
						$etoile_decimale = '<img src="imgs/picto-etoile-moitie-pleine.png" alt="etoile moitie pleine" class="etoile_avis" />' ;
						
						// Si la moyenne est au 4 cinquieme
						if($i - $nombre_etoile <= 0.2)
						{
							$etoile_decimale = '<img src="imgs/picto-etoile-presque-pleine.png" alt="etoile presque pleine" class="etoile_avis" />' ;
						}
					}
				
					// à la fin on met la variable etoile decimale dans l'affichage
					$affichage_etoile_avis .= $etoile_decimale;
				}
				else
				{
					//Si c'est juste un affichage il y à pas besoin de mettre de onclick pour changer les étoiles
					if($type == 'affichage')
					{
						$etoile_vide = '<img src="imgs/picto-etoile-vide.png" alt="etoile vide" class="etoile_avis" />' ; 
					}
					// On rajoute le onclick pour les modif et ajout d'avis
					else if($type == 'ajout-modif')
					{
						$etoile_vide = '<img id="etoile_avis'.$i.'" src="imgs/picto-etoile-vide.png" onclick="etoile_avis_remplissage('.$i.');" alt="etoile vide" class="etoile_avis etoile_cliquable"  />' ;  
					}
					
					$affichage_etoile_avis .= $etoile_vide ;
				}
			}
		}
	}
	// On retourne l'ensemble du contenu 
	return $affichage_etoile_avis ; 
}
function recup_note_resto($id_client)
{
	global $bdd ; 
	
	// recuperation de tout les avis des utilisateurs lié a ce client
	$requete_note_resto = $bdd->prepare('SELECT note FROM avis_utilisateur WHERE id_resto = :id_resto') ; 
	$requete_note_resto->execute(array(':id_resto' => $id_client)) ;
	
	$note_globale = 0 ;
	$nombre_vote = 0;
	
	while($donnees_note_resto = $requete_note_resto->fetch())
	{
		$note_globale = $donnees_note_resto['note'] + $note_globale; 
		$nombre_vote++ ; 
	}
	// Si il y à bien eu un vote
	if($nombre_vote != 0)
	{
		// On divise par le nombre totale de note
		$note_globale = $note_globale / $nombre_vote ;
		
		$note_globale = round($note_globale , 2) ;  
		
		$tableau_note = array('note_finale' => $note_globale , 'nombre_vote' => $nombre_vote) ;
	}
	else
	{
		$tableau_note = array('note_finale' => 0 , 'nombre_vote' => 0) ;
	}
	
	return $tableau_note ; 
}
function affichage_attribut_resto($attribus)
{
	$affichage_attribus = '' ;
	$attribus_checked = '' ;
	$tableau_title_attribus = array('sur-place' => 'Sur place' , 'a-emporter' => 'A emporter' , 'livraison' => 'livraison' , 'fait-maison' => 'Fait maison' , 'a-volonte' => 'A volonté' , 'bio' => 'Bio' , 'halal' => 'Halal' , 'wifi' => 'Wifi'); 
	
	// On explode les attribus par le séparateur choisit --
	$tableau_attribus = explode('--' , $attribus) ; 
	
	// On compte le nombre d'attribus en tout pour parcourir avec le tableau 
	$nombre_attribus_tableau = count($tableau_attribus) ; 
	
	// Création de la boucle de parcours
	for($i=0 ; $i < $nombre_attribus_tableau ; $i++)
	{
		// On fait un dexième explode pour récupérer le résultat de l'attribus et son nom par le =
		$tableau_attribus_valeur = explode('=' , $tableau_attribus[$i]) ; 
			
		// On sait que n'y a que 2 valeurs pas besoin de boucle le 1 correspond au name et le 2 a la valeur
		$nom_attribus = $tableau_attribus_valeur[0]; 
		
		$valeur_attribus = $tableau_attribus_valeur[1]; 
		
		// Si L'attribus est le prix on prend directement sa valeur 
		if($nom_attribus == 'prix')
		{
			$attribus_prix = $valeur_attribus ;
		}
		else
		{
			//Si la variable valeur_attribus est égale à 1 on la met dans affichage_attribus sinon elle n'y apparait pas
			if($valeur_attribus == 1)
			{
				// Pour le numéro et le contact pas besoin de créer de picto
				if($nom_attribus == 'numero')
				{
					$attribus_numero = $valeur_attribus ; 
				}
				else if($nom_attribus == 'contact')
				{
					$attribus_contact = $valeur_attribus ;
				}
				else if($nom_attribus == 'site-internet')
				{
					$attribus_site_internet = $valeur_attribus ;
				}
				else
				{
					$timestamp = versionning('imgs/picto-'.$nom_attribus.'.png') ; 
				
					$chemin_attribus = 'imgs/picto-'.$nom_attribus.'.'.$timestamp.'.png' ;
					
					// Title des attribus 
					$title_attribus = $tableau_title_attribus[$nom_attribus] ;
				
					$affichage_attribus .= '<div class="bloc_picto_attribus"><span class="picto_attribut" onclick="faire_apparaitre_title_picto(\'picto_attribus'.$nom_attribus.'\')"><img id="picto_attribus'.$nom_attribus.'" src="/'.$chemin_attribus.'" title="'.$title_attribus.'" alt="picto '.$title_attribus.'"></span><br /><span id="title_picto_attribus'.$nom_attribus.'"  class="title_picto" style="display:none">'.$title_attribus.'</span></div>' ;
				}
				// On créer une chaine de caractère attribus checked
				// Si il n'y à encore aucune entré
				if( $attribus_checked == '')
				{
					$attribus_checked = $nom_attribus; 
				}
				else
				{
					$attribus_checked = $attribus_checked.','.$nom_attribus; 
				}
			}
		}
	}
	
	if(!isset($attribus_prix))
	{
		$attribus_prix = 0 ; 
	}
	
	// Tableau d'attrius, un pour l'affichage l'autre pour récupérer les attribus checked
	$tableau_attribus = array('affichage_attribus' => $affichage_attribus , 'attribus_checked' => $attribus_checked , 'attribus_prix' => $attribus_prix , 'attribus_numero' => $attribus_numero , 'attribus_contact' => $attribus_contact , 'attribus_site_internet' => $attribus_site_internet) ; 
	
	return $tableau_attribus ; 
}

function creation_session_compte_resto($array_donneees_resto , $mdp)
{	
	// Création de toute les sessions requises
	$_SESSION['token'] = generate_token() ;  
	$_SESSION['date_token'] = time(); 
	$_SESSION['id_client'] = $array_donneees_resto['id'];
	$_SESSION['login'] = $array_donneees_resto['login'];
	$_SESSION['mdp'] = $mdp;
	$_SESSION['nomresto'] = $array_donneees_resto['nomresto'];
	$_SESSION['adressresto'] = $array_donneees_resto['adressresto'];
	$_SESSION['ville'] = $array_donneees_resto['ville'];
	$_SESSION['mail'] = $array_donneees_resto['mail'];
	$_SESSION['mail_crypte'] = $array_donneees_resto['mail_crypte']; 
	$_SESSION['type'] = $array_donneees_resto['type_resto'];
	$_SESSION['telephone'] = $array_donneees_resto['telephone'];
	$_SESSION['site_internet'] = $array_donneees_resto['site_internet'];
	$_SESSION['image_facade'] = $array_donneees_resto['image_facade'];
	$_SESSION['attribus'] = $array_donneees_resto['attribus'];
	$_SESSION['offre_speciale'] = $array_donneees_resto['offre_speciale'];

	// Vidage de la variable fail connection si l'utilisateur c'est trompé
	vidage_session_fail_connexion() ;
}
function compter_nombre_notification_compte_visiteur()
{
	global $bdd;

	if(!empty($_SESSION['id_visiteur']))
	{
		if(is_numeric($_SESSION['id_visiteur']) )
		{
			// On va rechercher l'id max de la news ou l'utilisateur se trouve
			$requete_id_max_news_user = $bdd->query('SELECT membre_ayant_vu,date_ajout FROM news WHERE statut = 1 ORDER BY date_ajout DESC') ;
			
			while($info_id_max_news_user = $requete_id_max_news_user->fetch()) 
			{
				// On check si l'utilisateur n'est pas dedans 
				$tableau_id_max_news_user = explode(',' , $info_id_max_news_user['membre_ayant_vu']) ;
				
				$nombre_id = count($tableau_id_max_news_user) ; 
				
				for($i=0 ; $i < $nombre_id; $i++)
				{					
					// Si l'id de l'user s'y trouve on arrete tout et on prend celui-ci 
					if($_SESSION['id_visiteur'] == $tableau_id_max_news_user[$i])
					{
						$date_ajout_max_user = $info_id_max_news_user['date_ajout'] ; 
						// Arreter les boucles, ici le numéro de boucle permet de savoir combien de structure emboité à supprimer cad 2 ! 
						break 2;
					}
				}
			}
			
			// Si $date_ajout_max_user n'as pas été créer car aucune news ne comporte l'id de l'utilisateur, on le met à zero 
			if(!isset($date_ajout_max_user))
			{
				$date_ajout_max_user = 0 ; 
			}
			
			// Sinon on ne montre que les news qu'il doit voir c a d + au niveau de la date que celle qui sont al 
			$requete_news = $bdd->prepare('SELECT COUNT(id) FROM news WHERE statut = 1 AND date_ajout > :date_ajout_max_user') ;
			$requete_news->execute(array(':date_ajout_max_user' => $date_ajout_max_user)) ;
		}
	}
	else
	{
		$requete_news = $bdd->query('SELECT COUNT(id) FROM news WHERE date_ajout + duree_alert > '.time().' AND statut = 1') ; 
	}
		
	if($infos_news = $requete_news->fetch())
	{
		$notification_compte = $infos_news['COUNT(id)'] ; 
	}
	
	return $notification_compte ; 
}

function ajouter_point_utilisateur($point_en_plus)
{
	global $bdd ; 
	
	$requete_point_utilisateur = $bdd->prepare('UPDATE utilisateur SET points = points + :point_en_plus WHERE login = :login') ; 
	$requete_point_utilisateur->execute(array(':point_en_plus' => $point_en_plus, ':login' => $_SESSION['login_visiteur'] )) ; 
}

function creer_dispo_bloc_jour_semaine($jour_semaine , $libelle_id , $select_option) 
{
	//On compte le nombre d'éleément dans le tableau 
	$nombre_element = count($jour_semaine) ;
	
	for($i=0 ; $i < $nombre_element; $i++)
	{?>
		<div class="bloc_contenu_jour_semaine">
			<span onclick="select_jour_semaine('<?php echo  $jour_semaine[$i] ; ?>' , '<?php echo  $libelle_id ; ?>' , <?php echo $select_option; ?>) ;" id="<?php echo  $jour_semaine[$i].''.$libelle_id ; ?>" class="bulle_jour_semaine_off"> <?php echo  $jour_semaine[$i] ; ?> </span>
			<br />
			<?php
			// Uniquement si l'option des selects à été activé
			if($select_option == 1)
			{?>
				<select style="display:none" class="input select_disponibilite_semaine" id="select<?php echo  $jour_semaine[$i].''.$libelle_id ; ?>">
					<option value="0" >Entier</option>
					<option value="1">Matin</option>
					<option value="2">Aprem</option>
					<option value="3">Soir</option>
				</select>
			<?php
			}?>
		</div>
	<?php
	}
}

function afficher_liste_contact_pro($login , $type)
{
	global $bdd ; 
	
	$requete_liste_contact = $bdd->prepare('SELECT * FROM contact_pro WHERE login = :login ORDER BY nom') ; 
	$requete_liste_contact->execute(array(':login' => $login)) ; 
	
	while($info_liste_contact = $requete_liste_contact->fetch())
	{
		if($info_liste_contact['civ'] == 1)
		{
			$civ = 'monsieur' ; 
		}
		else
		{
			$civ = 'madame' ; 
		}?>
		
		<div id="bloc_contact_pro<?php echo $info_liste_contact['id']; ?>" class="bloc_affichage_contact_pro" style="background-color:white; width:250px">
			
			<?php
			// On échappe pour éviter les ' passés en javascript
			$nom = addslashes($info_liste_contact['nom']) ; 
			$prenom = addslashes($info_liste_contact['prenom']) ; 
			
			if($type == 'admin')
			{}
			else
			{
			?>
				<div style="display:none" id="bloc_actions_contact_pro<?php echo $info_liste_contact['id']; ?>">
					<button onclick="voir_bloc_modif_resto('bloc_contact_pro_modif_ajout'); remplissage_formulaire_modif_contact_pro(<?php echo $info_liste_contact['id']; ?> , <?php echo $info_liste_contact['civ']; ?> , '<?php echo $nom; ?>', '<?php echo $prenom; ?>', '<?php echo $info_liste_contact['poste']; ?>', '<?php echo $info_liste_contact['email']; ?>', '<?php echo $info_liste_contact['tel']; ?>', '<?php echo $info_liste_contact['disponibilite']; ?>') ; " class="reset_button choix_menu_compte_resto" >
						<img src="/imgs/picto-parametres.<?php echo versionning('imgs/picto-parametres.png') ; ?>.png" alt="Modifier contact pro" /><br />
						Modifier
					</button>
					
					<button class="reset_button choix_menu_compte_resto" onclick="supprimer_contact_pro(<?php echo $info_liste_contact['id']; ?>) ; " >
						<img src="/imgs/picto-supp.<?php echo versionning('imgs/picto-supp.png') ; ?>.png" alt="Supprimer contact pro" /><br />
						Supprimer
					</button>
				</div>
			<?php
			}?>
			
			<div style="cursor:pointer" onclick="affichage_contenu_contact_pro(<?php echo $info_liste_contact['id']; ?>) ; ">
				<p style="background-color:#db302d; color:white ; border-radius:5px ; padding:2px; font-weight:bold"><?php echo $info_liste_contact['poste'] ; ?></p>
				<p>
					<span class="texte_site"><?php echo $info_liste_contact['nom'].' '.$info_liste_contact['prenom']; ?></span><br />
					<span class="texte_site_noir"><?php echo $civ ; ?></span>
				</p>
				<img src="/imgs/picto-<?php echo $civ ; ?>.<?php echo versionning('imgs/picto-'.$civ.'.png'); ?>.png" alt="picto <?php echo $civ ; ?>" /><br />
			</div>
			<div style="display:none" id="bloc_infos_complementaire_contact_pro<?php echo $info_liste_contact['id']; ?>">
				<p style="border-bottom:2px dashed #db302d; "></p>
				
				<p>
					<span class="texte_site">Contact : </span><br />
					<br />
					<a class="texte_site_noir" href="tel:<?php echo $info_liste_contact['tel'] ; ?>">
						<img src="/imgs/picto-phone.<?php echo versionning('imgs/picto-phone.png'); ?>.png" alt="picto telephone" /><br>
						<?php echo $info_liste_contact['tel'] ; ?>
					</a><br /><br />
					
					<a class="texte_site_noir" href="mailto:<?php echo $info_liste_contact['email'] ; ?>">
						<img src="../imgs/picto-contact.<?php echo versionning('imgs/picto-contact.png'); ?>.png" alt="picto mail" /><br>
						<?php echo $info_liste_contact['email'] ; ?>
					</a><br /><br />
				</p>
			
				<p style="border-bottom:2px dashed #db302d; "></p>
				
				<span class="texte_site">Disponibilité : </span><br /><br />
					<?php 
					if(!empty($info_liste_contact['disponibilite']))
					{
						// On explode par les -- 
						$tableau_jour_dispo = explode('--' , $info_liste_contact['disponibilite']) ; 
						
						// On compte le nombre d'occurence pour toute les faires
						$nombre_jour_dispo = count($tableau_jour_dispo) ; 
						
						for($i=0; $i < $nombre_jour_dispo ; $i++)
						{
							$jour_dispo_horraire = explode('-||-' , $tableau_jour_dispo[$i]) ; ?>
							
							<div class="bloc_contenu_jour_semaine">
								<span class="bulle_jour_semaine_on_affichage" style="cursor:default"> <?php echo  $jour_dispo_horraire[0] ; ?> </span>
								<?php
								// Si le dexieme $jour_dispo_horraire n'est pas vide, c'est que il y a une plage de préférence
								if($jour_dispo_horraire[1] == 0 || !isset($jour_dispo_horraire[1]))
								{
									$dispo_contact = 'Entier' ; 
								}
								elseif($jour_dispo_horraire[1] == 1)
								{
									$dispo_contact = 'Matin' ;
								}
								elseif($jour_dispo_horraire[1] == 2)
								{
									$dispo_contact = 'Midi' ;
								}
								elseif($jour_dispo_horraire[1] == 3)
								{
									$dispo_contact = 'Soir' ;
								}
									?>
									<br />
									<span class="dispo_contact"> <?php echo $dispo_contact; ?> </span>
							</div>
							<?php
						}
					}
					?>
				</div>
			</div>
		<?php
	}
}

function creer_tableau_type_resto()
{
	// Restaurant type 

	$tableau_type_resto = array('Restaurant Américain' ,'Bar' ,'Boulangerie' , 'Brasserie' , 'Brésilien' , 'Café' ,  'Restaurant Chinois' , 'Couscous' , 'Chocolatier' , 'Restaurant Créole' , 'Crêperie' , 'Restaurant Espagnol' , 'Fromagerie', 'Glacier' , 'Restaurant Grec' , 'Restaurant Indien' , 'Restaurant Italien' , 'Restaurant Japonais' , 'Kebab' , 'Restaurant Libanais' , 'Restaurant Mexicain' , 'Restaurant Pakistannais' , 'Pizzaria' , 'Pâtissier' , 'Poisonnier' , 'Pub', 'Restaurant' , 'Restaurant Gastronomique' , 'Restaurant Régional', 'Restaurant Traditionnel', 'Restauration Rapide' , 'Restaurant Burger' , 'Sandwicherie' , 'Restaurant Thailandais' , 'Restaurant Turc' , 'Restaurant Végétarien' , 'Restaurant Vietnamien' , 'Salon de thé' , 'Boucherie' , 'Primeur' , 'Fast-food', 'Bar lounge', 'Traiteur' , 'Restaurant Portugais' ) ;
	
	// Trier du plus petit au plus grand donc dans ce cas par ordre alphabetique
	sort($tableau_type_resto) ; 
	
	return $tableau_type_resto ; 
}

function creer_tableau_type_resto_disponible()
{
	global $bdd ;
	
	$tableau_type_resto_disponible = array() ; 
	
	$requete_type_disponible = $bdd->query('SELECT DISTINCT type_resto FROM client ORDER BY type_resto') ;
	
	while($info_type_disponible = $requete_type_disponible->fetch()) 
	{
		array_push($tableau_type_resto_disponible, $info_type_disponible['type_resto']) ; 
	}
	
	return $tableau_type_resto_disponible ;
}
function reorganisation_session_image_upload($type_upload)
{
	$nouvelle_id_image = 0 ;

	for($i = 0 ; $i <= $_SESSION['id_image_max'.$type_upload] ; $i++)
	{
		if(!empty($_SESSION['idimage_'.$i.''.$type_upload]))
		{
			// Récupération des noms et id
			$id_plat = $_SESSION['idimage_'.$i.''.$type_upload] ; 
			$nom_plat = $_SESSION['nom_image_upload'.$i.''.$type_upload] ; 
			
			// Suppression des anciens id 
			unset($_SESSION['idimage_'.$i.''.$type_upload]) ; 
			unset($_SESSION['nom_image_upload'.$i.''.$type_upload]) ; 
			
			// Réorganisation des id plats
			$_SESSION['idimage_'.$nouvelle_id_image.''.$type_upload] = $id_plat ;
			// Réorganisation des noms de plats
			$_SESSION['nom_image_upload'.$nouvelle_id_image.''.$type_upload] = $nom_plat ;
			
			$nouvelle_id_image++ ; 
		}
	}
}