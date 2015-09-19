<?php
session_start() ;

// Si le cookie du tuto n'existe pas c'est que c'est la première fois que la personne voit ca 
if(!isset($_COOKIE['deja_visite_menu_gourmand']))
{
	// On met donc le cookie mais aussi une session première connection pour différencier
	setcookie('deja_visite_menu_gourmand' , 1 , time() + 365*24*3600 , '/') ;
	$_SESSION['premiere_connexion_tuto'] = 1 ; 
}

// Spécifie que la page est la page du menu-gourmant ( sert a modifier plusieurs partie en include  ex: footer.php)
$menu_gourmant = 1 ;

$_SESSION['toute_image_precedente'] = '' ;
$_SESSION['nombre_resto_vu_suite'] = 0 ; 
$_SESSION['laisser_passer_reduction'] = 0 ; 

// On met tout le temps géocalise
$_SESSION['type_recherche'] = 'geocaliser' ;  

// Permet de voir si l'utilisateur à selectionné un plat ou non 

// On netoie les parametres éventuellement passés
$page_actuelle = explode('?' , $_SERVER['REQUEST_URI'] ) ; 

if(preg_match('#^/[a-zA-Z0-9\-]*-[0-9]*.html$#' , $page_actuelle[0] ))
{
	$image_cible_referencement = $page_actuelle[0] ;
	
	// Si il y a bien des parametres passés, on les supprimes avec l'HTML hitory si le support le permet
	if(!empty($page_actuelle[1]))
	{?>
		<script>
			if(history.pushState)
			{
				history.pushState(null, null, '<?php echo $page_actuelle[0]; ?>'); 
			}
		</script>
	<?php
	}
}
else
{
	$image_cible_referencement = 0;
}
?>

<!DOCTYPE html>
<html lang="fr" >
	<head>
		<?php 
		// Ajout des plugings facebook et twitter 
		$reseaux_sociaux_plugins = 1 ; 
		
		// Référencer les liens direct
		if(!empty($image_cible_referencement))
		{
			include('include/configcache.php');
			include('include/fonctions.php');
			
			$fonction_deja_include = 1 ;
			$config_deja_include = 1 ;
			
			$nom_de_page = str_replace('/','', $_SERVER['REQUEST_URI']);
			$nom_de_page = explode('.' ,  $nom_de_page) ;
			$idimage_page = $nom_de_page[0] ;
			
			$requete_referencement_page = $bdd->prepare("SELECT c.id id_client , c.login login, p.id id, p.idimage idimage, p.nomplat nomplat,p.etat etat, p.id_reduction id_reduction , c.site_internet site_internet, c.nomresto nomresto, c.adressresto adressresto, c.ville ville, c.type_resto type_resto, c.mail mail,c.telephone telephone, c.mail_crypte mail_crypte, c.coordonnees_latitude coordonnees_latitude, c.coordonnees_longitude coordonnees_longitude, c.image_facade image_facade, c.attribus attribus  FROM plat p INNER JOIN client c ON p.login = c.login WHERE p.idimage = :images && etat != 2");
			$requete_referencement_page->execute(array(':images' => $idimage_page)) ;
			
			if ($info_referencement_page = $requete_referencement_page->fetch())
			{
				// Si l'état du plat est bien en ligne et que ce n'est pas le profil de la personne qui regarde
				if(!empty($_SESSION['login']) && $_SESSION['login'] == $info_referencement_page['login'])
				{
					$faire_apparaitre_plat = 1 ; 
				}
				if($info_referencement_page['etat'] == 1 || !empty($faire_apparaitre_plat) && $faire_apparaitre_plat == 1)
				{
					//htmlentities sur tout les array sensible pour éviter les failles xss
					$info_referencement_page = protection_array_faille_xss($info_referencement_page) ;
					
					// Titre optimisé
					$meta_titre = $info_referencement_page['type_resto'].' '.$info_referencement_page['ville'].' - '.$info_referencement_page['nomplat'].' - '.$info_referencement_page['nomresto'] ;
					
					// Si il y a bien une réduction 
					if(!empty($info_referencement_page['id_reduction']))
					{
						$reduction_plat_description = 'et profitez de leur réduction spéciale DMF ! ' ; 
					}
					else
					{
						$reduction_plat_description = '' ; 
					}
					
					// A mettre ici une description du plat
					$meta_description = 'Découvrez sur '.$info_referencement_page['ville'].' : '.$info_referencement_page['nomplat'].' offert par : '.$info_referencement_page['type_resto'].' '.$info_referencement_page['nomresto'].' '.$reduction_plat_description ;
					//Savoir si on référence la page
					$meta_robots = 'index' ; 
					
					// On créer une session image précédente pour dire que l'on à déjà vu ce plat
					$_SESSION['image_precedente'] = $info_referencement_page['idimage'] ; 
					
					// On créer une session de l'image actuelle
					$_SESSION['image_actuelle'] = $info_referencement_page['id'] ;
					
					// Récupération du client actuellement visité
					$_SESSION['id_resto_actuel'] = $info_referencement_page['id_client']; 
					
					$_SESSION['login_client_actuel'] = $info_referencement_page['login']; 
					
					// On créer une session de l'id de l'image actuelle
					$_SESSION['id_image_actuelle'] = $info_referencement_page['id'] ; 
					
					// Permet d'etre récupéré dans l'ajax pour pas resortir cette image dès le début de l'application
					$_SESSION['images'] = $info_referencement_page['id'] ; 
					
					// Récupération des latitudes et longitude propre au resto & également le state_idimage pour l'html history
					echo 
					'<script>
						coordonnees_latitude = '.$info_referencement_page['coordonnees_latitude'].' ;
						coordonnees_longitude = '.$info_referencement_page['coordonnees_longitude'].' ;
						state_idimage = ["'.$info_referencement_page['id'].'"];
					</script>' ;
				}
				else
				{?>
					<script>
						window.addEventListener('load', plat_non_valide, true) ;
						
						function plat_non_valide()
						{
							ouverture_alert(alert_basic = 'Oups, le plat que vous essayez de visualiser n\'est pas encore validé par l\'équipe DonneMoiFaim. Seul son propriétaire peut y accéder. Nous vous avons redirigé vers un autre plat du menu gourmand.') ;
							
							type_recherche = 'aleatoire' ; 
							chargementimage() ;
						}
					</script>
				<?php
				}
			}
			else
			{?>
				<script>
					window.addEventListener('load', plat_plus_disponible, true) ;
					
					function plat_plus_disponible()
					{
						ouverture_alert(alert_basic = 'Oups, le plat que vous essayez de visualiser n\'existe plus. Nous vous avons redirigé vers un autre plat du menu gourmand.') ;
					}
				</script>
			<?php
			}
		}
		else
		{
			$meta_titre = ' Diaporama plats et réductions DMF' ; 
			$meta_description = 'Découvrez notre diaporama de délicieux plats en ligne géolocalisés ou aléatoire issus de nos partenaires restaurateur. Profitez également de leurs réductions misent en ligne ! ' ;
			$meta_keywords = 'plat, réductions, aléatoire, geolocaliser, menu, faim, plat, Plats, restaurant,menu,restaurant, buffet, gourmand'  ;
			//Savoir si on référence la page
			$meta_robots = 'index' ; 
		}
		include('include/en-tete.php') ;
		
		// Si on cherche une image à référencer 
		if(!empty($info_referencement_page))
		{
			// Récupération du nombre de j'aime
			$resultat_jaime = recuperation_jaime_plat($info_referencement_page['id']) ; 
			
			$nombre_jaime = $resultat_jaime['nombre_jaime'] ;
		}		
		?>
		<!--Retirer le padding-bottom du div main original de la structure du site-->
		<style>
			#main{padding:0}
		</style>
	</head>
	<body style="overflow:hidden; background-color:rgb(228, 228, 228)">
		<?php include('include/header.php') ?>
		
		<!-- Si le cookie du tuto n'existe pas c'est que c'est la première fois que la personne voit ca  -- >
		<?php
		if(!isset($_COOKIE['deja_visite_menu_gourmand']))
		{?>
			<!-- petit bloc permetant de savoir si la personne veut regarder le tuto ou non, pour le référencement et également pour ne pas géner l'utilisateur -->
			<div id="bloc_demande_tuto_nouveau" style="display:none" >
				<span style="top:20px" onclick="display_bloc_demande_tuto() ; " class="petite_croix_fermer_bloc">x</span>
				<span style="font-style:italic; font-size:12px; font-weight:bold;"> Nouveau sur DMF ? </span><br />
				<button onclick="premiere_visite();" class="buttonEnfonce">Regardez le tuto</button>
			</div>
			
			<!-- tutoriel de la première fois que le site est visité -->
			<div id="tuto_premiere_visite" style="display:none">
				<div id="contenu_tuto_premiere_visite">
					<p id="texte_tuto_premiere_visite"></p>
					<p style="text-align:center">
						<br />
						<img style="display:none" id="icone_dmf_tuto" src="imgs/button_donnemoifaim.png" alt="donnemoifaim" />
					</p>
					<img id="picto_suivant_tuto" style="cursor:pointer; display:none" src="imgs/picto-tuto-suivant.<?php echo versionning('imgs/picto-tuto-suivant.png'); ?>.png" alt="picto suivant tuto" /><br />
					<p id="bloc_button_tuto" style="display:none">
						<button id="button_tuto0" class="button_tuto reset_button"> </button>
						<button id="button_tuto1" class="button_tuto reset_button"> </button>
						<button id="button_tuto2" class="button_tuto reset_button"> </button>
						<button id="button_tuto3" class="button_tuto reset_button"> </button>
						<button id="button_tuto4" class="button_tuto reset_button"> </button>
						<button id="button_tuto5" class="button_tuto reset_button"> </button>
					</p>
				</div>
			</div>
		<?php
		}?>
		
		<!-- bloc de l'image -->
		<div id="bloc_image_plat">
			<?php
				// Si il y a bien une image
				if(!empty($info_referencement_page))
				{
					// Si c'est pour les mobiles
					if(!empty($mobile_device))
					{
						$chemin_image = 'plats/mobiles/'.$info_referencement_page['idimage'].'.'.filemtime('plats/'.$info_referencement_page['idimage'].'.jpg').'.jpg' ;
					}
					else
					{
						$chemin_image = 'plats/'.$info_referencement_page['idimage'].'.'.filemtime('plats/'.$info_referencement_page['idimage'].'.jpg').'.jpg' ;
					}
				}
				else
				{
					$chemin_image = ''; 
				}
				
				?>
			<img style="transform:scale(1);" oncontextmenu="protection_image();" onclick="voir_photo_entier();" id="image_plat" src="<?php echo $chemin_image; ?>" alt="<?php if(!empty($info_referencement_page))
			{
				echo $info_referencement_page['nomplat'] ;
			} ?>"/>
		</div>
		
		<!-- reception des données info et image du plat dans cette session grace au js-->
		<aside id="bloc_info_plat">
			<!-- Nom du plat remplit en AJAX/JSON -->
			<h1 id="nomplat" class="info_plats"><?php if (!empty($info_referencement_page)){ echo $info_referencement_page['nomplat'] ;} ?></h1>
			<br />
			<div id="infos_complementaires">
				<!-- Nom du restaurent/type remplit en AJAX/JSON -->
				<h2 id="nomresto" style="margin:0;">
					<?php 
					if(!empty($info_referencement_page))
					{
						echo $info_referencement_page['nomresto'] ; 
					 } ?>
				</h2>
				<!-- adresse du restaurent remplit en AJAX/JSON - display inline pour que l'image suive tranquillement -->
				<h3 id="adressresto" class="info_plats" style="cursor:pointer; display:inline" onclick="google_map();">
					<?php if (!empty($info_referencement_page)){ echo $info_referencement_page['adressresto'].' <br /> '. $info_referencement_page['ville'] ;} ?>
				</h3>
				<img onclick="google_map();" src="imgs/picto-geocalisation-mini.png" />
			</div>
		</aside>
		<!-- bloc info en plus  -->
		<div id="bloc_info_coulissant" style="display:none">
			<img onclick="info_image_en_plus();" class="button_fleche_haut" id='button_fleche_haut' src="imgs/button_fleche_haut.png" alt="button fleche en bas" />
			<!-- donnée remplies en ajax ou au clic sur le bouton de coulissement -->
				
				<?php
				// Optimisation lorsque l'on est sur mobile
				if(!empty($info_referencement_page['image_facade']))
				{
					if(!empty($mobile_device))
					{
						$mobile_chemain = 'mobiles/' ; 
					}
					else
					{
						$mobile_chemain = '' ; 
					}
					?>
					<p id="bloc_image_fiche_resto" class="bloc_image_fiche_resto" onclick="apercu_image('compte-resto/image-resto/<?php echo $mobile_chemain; ?><?php echo $info_referencement_page['image_facade']; ?>.<?php echo versionning('compte-resto/image-resto/'.$info_referencement_page['image_facade'].'.jpg') ; ?>.jpg') ; " style="background:url('compte-resto/image-resto/<?php echo $mobile_chemain; ?><?php echo $info_referencement_page['image_facade']; ?>.<?php echo versionning('compte-resto/image-resto/'.$info_referencement_page['image_facade'].'.jpg') ; ?>.jpg') no-repeat center; background-size:cover "></p>
				<?php
				}
				else
				{?>
					<p id="bloc_image_fiche_resto" class="bloc_image_fiche_resto" onclick="" style="background-size:cover "></p>
				<?php
				}?>
				
				<p class="center" style="margin:0">
					<!-- petit menu d'accès rapide -->
					<span onclick="scroll_to('#bloc_info_coulissant', '#bloc_infos_complementaire')" class="choix_autre_choix">Avis</span>
					<span onclick="scroll_to('#bloc_info_coulissant', '#bloc_adresse')" class="choix_autre_choix">Adresse</span>
					<span onclick="scroll_to('#bloc_info_coulissant', '#bloc_attribus_resto')" class="choix_autre_choix">Attributs</span>
					<span onclick="scroll_to('#bloc_info_coulissant', '#bloc_tout_plats')" class="choix_autre_choix">Tous les plats</span>
				<p>
				
				<div class="bloc_infos_complementaire"> 
				<br />
				<p class="texte_site_noir" itemscope itemtype="http://data-vocabulary.org/Review-aggregate">
					<?php
						if(!empty($info_referencement_page))
						{
							$tableau_note = recup_note_resto($_SESSION['id_resto_actuel']) ;
							
							// La note final
							$nombre_etoile = $tableau_note['note_finale'] ;
							
							// Le nombre de vote en tout
							$nombre_vote = $tableau_note['nombre_vote'] ; 
								
							// Nombre d'étoile est récupéré plus haut
							$affichage_etoile_avis = affichage_etoile_avis($nombre_etoile, 'affichage') ; 
							
							// Si le nombre de vote est de zero alors on affiche pas 
							if($nombre_vote == 0)
							{
								$nombre_vote = '' ;
								$nombre_etoile = '' ;
							}
						}
					?>
					<span id="nom_resto_info_complementaire" class="titre" itemprop="itemreviewed"><?php if(!empty($info_referencement_page)){ echo $info_referencement_page['nomresto'] ; } ?></span>
					<br /><br />
					
					<span id="bloc_englobans_number_vote" <?php if($nombre_vote == 0){echo 'style="display:none" ; ' ;} ?> >
						<span  class="texte_site_noir" id="nombre_vote_avis_resto" itemprop="votes"><?php echo $nombre_vote; ?></span>
						<Span  class="texte_site_noir" id="denomination_votant_avis_resto"> votant<?php if($nombre_vote > 1 ){echo 's' ;} ?></span><br />
					</span>
					<span id="bloc_note_resto" onclick="voir_avis();"><?php echo $affichage_etoile_avis ; ?></span>
					<!-- Vu que pour mettre un commentaire il faut forcement voter -->
					<span style="display:none" id="nombre_vote_avis_resto_count" itemprop="count"><?php echo $nombre_vote; ?></span> 
					<!-- microdata sur les notes étoiles-->
					<span  itemprop="rating" itemscope itemtype="http://data-vocabulary.org/Rating">
						<span id="bloc_englobans_rating_texte" style="position:absolute ; <?php if($nombre_vote == 0){echo 'display:none ; ' ;} ?>">
							<span  class="texte_site_noir"  id="note_texte_global_resto" itemprop="average"><?php echo $nombre_etoile ; ?></span><span class="texte_site_noir">/</span> <span  class="texte_site_noir" itemprop="best">5</span>
						</span>
					</span>
					<br />
				</p>
				<div class="texte_site_noir">
					<p style="display:inline-block; vertical-align:top; margin:5px">
						Voir les avis<br />
						<img onclick="voir_avis();" class="picto_avis" src="imgs/picto-avis.<?php echo versionning('imgs/picto-avis.png'); ?>.png" alt="Voir les avis" />
					</p>
					<p style="display:inline-block; vertical-align:top; margin:5px;">
						Donner son avis<br />
						<img onclick="voir_avis(); ouvrir_bloc_form_avis();" class="picto_avis" src="imgs/picto-edit-avis.<?php echo versionning('imgs/picto-edit-avis.png'); ?>.png" alt="écrire un avis" />
					</p>
				</div>
				<br />
			</div>
			<div id="bloc_adresse" class="bloc_infos_complementaire">
				<p class="titre_info_complementaire">L'adresse :</p><br />
				<img class="picto_google_map" onclick="google_map();" src="imgs/picto_google_map.png" alt="géolocaliser le plat" />
				<span class="texte_site" id="adressresto_info_complementaire"><?php if(!empty($info_referencement_page)){ echo $info_referencement_page['adressresto'].' - '.$info_referencement_page['ville']  ; } ?></span><br />
			</div>
			<div id="bloc_attribus_resto" class="bloc_infos_complementaire">
				<p class="titre_info_complementaire">Attributs resto :</p><br />
				<div id="bloc_attribus_resto_ajax_modif">
					<?php 
					if(!empty($info_referencement_page['attribus']))
					{
						$attribus_resto = $info_referencement_page['attribus'] ; 
		
						// inclusion des attribus resto
						include('compte-resto/inc/attribus_resto_affichage.php') ; 
					}
					?>
				</div>
			</div>
			<div id="bloc_tout_plats" class="bloc_infos_complementaire">
				<p class="titre_info_complementaire">Du même resto : </p><br />
				<div id="bloc_image_resto">
					<div id="ensemble_plat_resto" style="margin:0;">
						<?php
						// Si c'est une page d'un plat, le référencement doit se faire donc injection en PHP pour le référencement
						if(!empty($info_referencement_page))
						{
							// Inclusion de l'ajax qui le fait très bien
							include('ajax/menu_gourmand/voir_tout_plats.php') ; 
						}?>
					</div>
				</div>
			</div>
		</div>
		
		<!-- bouton pour changer de plat tout en vérifiant si le navigateur supporte html history-->
		<a style="display:none" id="button_donnemoifaim" class="reset_button" href="menu-gourmand.html" onclick="if(history.pushState){chargementimage(); return false;} else {<?php if(preg_match('#^/[a-zA-Z0-9\-]*-[0-9]*.html$#' , $_SERVER['REQUEST_URI'])){}else{ echo 'chargementimage(); return false;'; }?>}" >
			<img src="imgs/picto-suivant.<?php echo versionning('imgs/picto-suivant.png'); ?>.png" alt="Trouvez LE plat" />
		</a> 
		
		<!-- button pour ouvir les infos en plus et les applications -->
		<img onclick="info_image_en_plus();" id='button_fleche_bas' src="imgs/button_fleche_bas.<?php echo versionning('imgs/button_fleche_bas.png'); ?>.png" alt="button fleche en bas" />
		
		<div id="footer_menu_gourmand">
			<!--button permet de passer le resto si jamais la personne à + que 5 plats ca peut devenir chaud après pour la géocalisation, on le met ici pour qu'il suive le footer -->
			<p onclick="passer_resto();" id="bloc_passer_resto" style="display:none">Ne plus voir ce resto</p>
			<?php 
				include('applications/button_jaime_partage.php') ; 
				include('applications/choix_geo_aleatoire.php') ;
			?>
		</div>
		
		<!-- token visiteur contre les failles csrf -->
		<input type="hidden" id="token_visiteur" value="<?php if(!empty($_SESSION['token_visiteur'])){echo $_SESSION['token_visiteur'] ; } ?>" />
		<!-- input login pour récupérer le login de l'utilisateur des fois qu'on en est besoin -->
		<input type="hidden" id="login_visiteur_recuperer" value="<?php if(!empty($_SESSION['login_visiteur'])){echo $_SESSION['login_visiteur'] ; } ?>" />
		
		<div id="bloc_publicite_apparante" class="bloc_apparait" style="display:none;"></div>
		
		<!--bloc background color rouge quand on clique sur le button fleche -->
		<div id="fond_info_complementaire" onclick='info_image_en_plus();' style="display:none"></div>
		
		<!-- div invisible permet de stocker le nom du plat possibilité de le récupérer facilement --> 
		<div id="div_id_plat_invisible"></div>
		
		<?php 
		include ('applications/partage_url.php') ;
		include ('applications/compte_visiteur.php') ;
		include ('applications/googlemap.php') ;
		include ('applications/reduction_plat.php');
		include ('applications/avis_visiteur.php') ; 
		include ('applications/abonnement_visiteur.php') ;
		include('include/footer.php'); 
		?>
		
		<!-- on verifie s'il existe une image à charger spécifique -->
		<?php 
		// Si une recherche à été faites depuis une page extérieur
		if(!empty($_GET['ville']))
		{?>
				<script  type="text/javascript">
					document.getElementById('recherche').value = '<?php echo $_GET['ville'] ; ?>' ;
				</script>
		<?php
		}?>
		<script>
			// Création de l'idimage premier pour éviter que ca beug 
			<?php if(!empty($info_referencement_page))
			{?>
				id_plat_actuelle = '<?php echo $_SESSION['images'] ; ?>' ;  
			<?php
			}?>
			// Lancement des applications a la fin du chargement de la page
			window.onload = function()
			{
				chargement_bloc_action() ; 
				
				// Initialisation de google map
				loadScriptGoogleMap('geocalisation_user'); 
				
				// C'est le menu-gourmant donc on change l'opacity des bloc_apparait on les réduit un peu
				$('.bloc_apparait').css('background-color' , 'rgba(255,255,255,0.9)') ;
				
				bloc_apparait_responsive() ;
				// Si une rénitialisation de mdp a été demandé on ouvre le bloc de connection
				<?php if(!empty($_GET['renitialiser_mdp']))
				{?>
					voir_bloc_coulissant(id_bloc = '#compte_visiteur'); 
				<?php
				}?>
				//si c'est une image le type est image_unique
				<?php 
				if(!empty($info_referencement_page))
				{?>
					// Le type est nécessaire, donc le type c'est image unique tout simplement
					connaitre_type_recherche(type = '<?php echo $_SESSION['type_recherche']; ?>' , 0) ; 
				<?php
				}
				else if(!empty($_GET['geocalisation']) || $_SESSION['type_recherche'] == 'geocaliser')
				{?>
					// Par défaut le type de recherche est aléatoire
					connaitre_type_recherche(type = 'geocaliser' , appel_chargement_image = 1) ;
				<?php
				}
				else
				{?>
					// Par défaut le type de recherche est aléatoire
					connaitre_type_recherche(type = 'aleatoire' , appel_chargement_image = 1) ;
				<?php
				}?>
				
				chargement_bloc_action() ; 
				
				// Tout est operationnel on peut mettre le buton suivant
				$('#button_donnemoifaim').fadeIn(300) ; 
				
				<?php
				// Si c'est la première connection mais également que ce ne soit pas un robot
				if(!empty($_SESSION['premiere_connexion_tuto']) && !isset($crawler))
				{
					// Si c'est la première visite
					echo 'display_bloc_demande_tuto() ; ' ;	
					// On vide la variable $_SESSION['premiere_connexion_tuto'] pour pas recharger le tuto
					unset($_SESSION['premiere_connexion_tuto']) ;
					
					// En générale si c'est la première fois, la personne n'à pas accepté la géolocalisation donc on lance un petit message
					echo 'message_premiere_connexion() ; ' ; 
				}
				?>
				
				// Voir si il y à des nouvelles notifications 
				<?php
				if($notification_compte != 0)
				{
					if(!isset($_SESSION['alert_notification_deja_fait']) || $_SESSION['alert_notification_deja_fait'] < $notification_compte)
					{?>
						notification_compte_visiteur('<?php echo $notification_compte; ?>') ;
						<?php
						$_SESSION['alert_notification_deja_fait'] = $notification_compte; 
					}
				}
				
				// Si il existe une variable get avec ouverture_compte_visiteur alors on ouvre son compte
				if(!empty($_GET['ouverture_compte_visiteur']) && $_GET['ouverture_compte_visiteur'] == 1)
				{?>
					affichage_compte_visiteur();
				<?php
				}?>
			}
		</script>

		<script async type="text/javascript" src="javascript/menu-gourmand.<?php echo versionning('javascript/menu-gourmand.js'); ?>.js"></script>
		<script async type="text/javascript" src="javascript/general.<?php echo versionning('javascript/general.js'); ?>.js"></script>
	</body> 
</html>