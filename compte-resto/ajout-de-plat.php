<?php
session_start();
// Récupération des sessions
include('../include/compte_acces.php') ; 
?>

<!DOCTYPE html>
<html>
	<head>
		<?php
		$meta_titre = 'Ajoutez vos plats - DonneMoiFaim' ; 
		$meta_description = 'Ajoutez vos plats et composez votre menu en ligne facilement et rapidement !' ;
		$meta_keywords = 'ajout,ajouter, plat, image, photographie,menu,restaurant, buffet, gourmand'  ;
		//Savoir si on référence la page
		$meta_robots = 'no-index' ; 
		include('../include/en-tete.php') ; 
		?>
	</head>
	<body >
		<?php
		include('../include/header.php') ;
		
		include('inc/menu_compte_resto.php');
		
		// Permet de définir si c'est un ajout ou une modification de plat
		$type_upload = 'ajout-de-plat' ;
		
		include('inc/sidebar_upload.php') ; 
		?>
		<div id="bloc_ajout_de_plat" style="position:relative">
			<br />
			<h1 id="titre_ajout_plat" class="titre center">Ajoutez vos plats</h1><br />
			<div id="info_ajout_plat" style="<?php if(!empty($_SESSION['nombre_image'.$type_upload])){echo 'display:none;' ;} ?>text-align:left; ">
				<h2 class="titre">Ajoutez plusieurs plats de votre restaurant en même temps, simplement, avec notre application ! </h2><br />
				<p class="texte_site_noir" style="font-weight:bold">
					L'affichage de notre menu-gourmand est soit <span class="bold">aléatoire</span>, soit <span class="bold">géolocalisé</span> . L'ajout de nombreux plats vous permet :
					<ul style="font-size:12px">
						<li> - <strong>d'augmenter vos chances</strong> d'être vue par un plus grand nombre d'utilisateurs, dans le cas de recherches aléatoires ; <br /><br /></li>
						<li> - de pouvoir <strong>montrer l'ensemble de vos plats</strong> aux utilisateurs dans le cas de recherches géolocalisées (ce type de recherche présente tous les plats du restaurateur avant de passer à un autre établissement).<br /></li>
					</ul>
				</p>
			</div>
			<form class="formulaire_upload_image" onsubmit="terminer_envoi_upload_multiple_image('<?php echo $type_upload ; ?>'); return false; " id="formulaire<?php echo $type_upload; ?>" method="POST" enctype="multipart/form-data"> 
				<?php
				// En cas de retour , récupération des sessions !
				if(!empty($_SESSION['nombre_image'.$type_upload]))
				{
					for($i=0 ; $i <= $_SESSION['id_image_max'.$type_upload] ; $i++)
					{
						if(!empty($_SESSION['idimage_'.$i.''.$type_upload]) AND is_file('../temporaire/'.$_SESSION['idimage_'.$i.''.$type_upload].'.jpg'))
						{
							$id_complementaire = $i.''.$type_upload ; 
							?>
							<div class="bloc_gestion_image_upload" id="bloc_gestion_image_upload<?php echo $id_complementaire ; ?>">
								<span id="supp_image<?php echo $id_complementaire ; ?>" class="delete_image" onclick="supprimer_image_upload(<?php echo $i ;?>, '<?php echo $type_upload ; ?>');"> X </span>
								<p class="preview_image texte_site" id="image_<?php echo $id_complementaire ;?>">
									<img onclick="apercu_image('../temporaire/<?php echo $_SESSION['idimage_'.$id_complementaire] ;?>.jpg' ) ; " class="apercu_image_miniature" src="/temporaire/miniature/<?php echo $_SESSION['idimage_'.$id_complementaire] ; ?>.jpg"  />
								</p>
								<br />
								<div id="bloc_input_apercu_plat">
									<span class="texte_site">* Nom du plat :</span></br />
									<input id="input_nom_image<?php echo $id_complementaire ;?>" type="text" class="input reset_input" name="nomplat<?php echo $i; ?>" placeholder="Choisissez le nom du plat" value="<?php if(!empty($_SESSION['nom_image_upload'.$id_complementaire])){echo $_SESSION['nom_image_upload'.$id_complementaire] ; } ?>" required/>
								</div>
							</div>
							<?php
						}
					}
				}
				?>
				<div class="bloc_button_suivant_processus" id="bloc_button_suivant_processus<?php echo $type_upload; ?>" style="<?php if(!empty(	$_SESSION['nombre_image'.$type_upload])){} else { echo 'display:none' ;} ?>" >
					<input id="button_suivant_processus<?php echo $type_upload; ?>" type="image" src="../imgs/picto-suivant.png"  id="input_submit_ajout_plat" /><br />
					<label class="texte_site" for="button_suivant_processus">1/2 suivant</label>
				</div>
				<br /><br />
			</form>
		</div>
		<div id="bloc_choix_abonnement" style="display:none; position:relative; text-align:center">
			<br />
			<h1 id="titre_choix_abonnement" class="titre">Choix d'abonnement des plats</h1>
			<div id="bloc_offre_essai">
				<?php
				// Récupération des données nécessaires
				include('../include/offre_tarrif.php') ;

				$requete_offre = $bdd->prepare('SELECT offre_speciale FROM client WHERE login = :login');
				$requete_offre->execute(array('login' => $_SESSION['login']));
				if($info_offre = $requete_offre->fetch())
				{
					if($info_offre['offre_speciale'] == 'decouverte')
					{
						$_SESSION['offre_decouverte'] = 1 ;
					}
					else
					{
						if(!empty($_SESSION['offre_decouverte']))
						{
							unset($_SESSION['offre_decouverte']) ;
						}
					}
				} 
				
				// Offre d'essais
				if(!empty($_SESSION['offre_decouverte']))
				{
				?>
					<p class="texte_site_noir">Vous pouvez profiter de votre offre découverte valable 1 mois ! </p>
					<form action="paiement/versionessai.php" method="POST">
						<div class="bloc_offre">
							 <img width="100%" height="180px" src="../imgs/abonnement1.<?php echo versionning($fichier = 'imgs/abonnement1.png'); ?>.png"/>
							<span class="description_offre" >Offre découverte 1 mois </span><br />
							<span class="prix_abonnement">Prix : Gratuit une fois</span><br /><br />
							<input id="offre_0" class="input_submit reset_input" type="submit" name="choix_formule"  value="En profiter" />
							<br />
						</div>
					</form>
				<?php
				}?>
			</div>
			<p class="texte_site_noir">1. Veuillez choisir la durée de mise en ligne de votre/vos plat(s)</p>
			<form onsubmit="envoi_formulaire_paypal(<?php if(!empty($_SESSION['login_connexion_de_dieu_resto']) && in_array($_SESSION['login_connexion_de_dieu_resto'], $pseudo_admin))
				{echo "'godModRestoAjoutPlat'" ; }?>); return false;" name="formulaire_paiement_paypal" action="<?php if($_SERVER['HTTP_HOST'] == 'test.donnemoifaim.fr'){echo 'https://www.sandbox.paypal.com/cgi-bin/webscr' ;} else {echo 'https://www.paypal.com/cgi-bin/webscr' ; }?>" method="post" class="formulaire_offre">
				<!-- formule basique d'abonnement au mois -->
				<div class="bloc_offre" id="formulaireabonnement" style=" width:80% ; max-width:500px ;">
					<p style="height:250px; background-image: url(../imgs/option_abonnement.<?php echo versionning('imgs/option_abonnement.png'); ?>.png) ; background-size:cover; margin-top:0" ></p>
					<span class="description_offre" >Abonnement mensuel</span><br />
					<span class="prix_abonnement">Prix : <?php echo $offre_formule['abonnement'] ; ?>€ par plat <span class="nombre_plat_span"></span> chaque mois </span>
					<br /><br />
					<span class="option_detail">Offre mensuelle, sans engagement. Vous pouvez arreter l'abonnement à tous moments dans vos factures.</span>
					<br /><br />
					<input id="offre_abonnement" onclick="calcule_total('abonnement');" class="input_checkbox" type="radio" name="choix_formule" value="1" checked>
					<br />
				</div>
				<br /><br />
				<button type="button" id="button_voir_bloc_offre_sans_abonnement" onclick="apparaitre_disparaitre_bloc_offre_sans_abonnement();" class="reset_button choix_autre_choix">Voir les offres sans abonnement </button>
				<br />
				<div id="bloc_offre_sans_abonnement" style="display:none">
					<!-- formule 1 mois -->
					<div class="bloc_offre" id="formulaire1">
						<img width="100%" height="180px" src="../imgs/abonnement2.<?php echo versionning('imgs/abonnement2.png'); ?>.png"/><br />
						<span class="description_offre" >Durée en ligne : <?php echo $temps_abonnement_formule['1'] ; ?> mois </span><br />
						<span class="prix_abonnement">Prix : <?php echo $offre_formule['1'] ; ?>€ par plat <span class="nombre_plat_span"></span></span>
						<br />
						<input id="offre_1" onclick="calcule_total(1);" class="input_checkbox" type="radio" name="choix_formule" value="1">
						<br />
					</div>
					<!-- formule 6 mois -->
					<div class="bloc_offre" id="formulaire2">
						<img width="100%" height="180px" src="../imgs/abonnement3.<?php echo versionning('imgs/abonnement3.png'); ?>.png"/>
						<span class="description_offre" >Durée en ligne : <?php echo $temps_abonnement_formule['2'] ; ?> mois </span><br />
						<span class="prix_abonnement">Prix : <?php echo $offre_formule['2']  ; ?>€ par plat <span class="nombre_plat_span"></span></span>
						<br />
						<input id="offre_2" onclick="calcule_total(2);" class="input_checkbox" type="radio" name="choix_formule" value="2" />
						<br />
					</div>

					<!-- formule 12 mois -->
					<div class="bloc_offre" id="formulaire3">
						<img width="100%" height="180px" src="../imgs/abonnement4.<?php echo versionning('imgs/abonnement4.png'); ?>.png"/>
						<span class="description_offre" >Durée en ligne : <?php echo $temps_abonnement_formule['3'] ; ?> mois </span><br />
						<span class="prix_abonnement">Prix : <?php echo $offre_formule['3'] ; ?>€ par plat <span class="nombre_plat_span"></span></span>
						<br />
						<input id="offre_3" onclick="calcule_total(3);" class="input_checkbox" type="radio" name="choix_formule" value="3" />
						<br /> 
					</div>
				</div>
				<br />
				
				<!-- choix de la réduction -->
				<p class="titre">Choix de votre réduction (obligatoire)</p>
				<p class="texte_site_noir">2. Choisissez une réduction pour insiter les utilisateurs à venir chez vous.</p>
				<br />
								<div class="bloc_offre" id="formulaire4">
					<img width="100%" height="180px" src="../imgs/option_reduction.<?php echo versionning('imgs/option_reduction.png'); ?>.png"/>
					<span class="description_offre" >Réduction</span><br />
					<span class="option_detail">Ajouter une réduction utilisable par les visiteurs du site afin de les inciter à venir goûter vos délicieux plats.</span><br />
					<p class="prix_abonnement">Prix: GRATUIT</p>
					<input id="offre_reduction" onclick="affichage_champ_reduction();" class="input_checkbox" type="checkbox" required/>
					<!-- bloc de réduction qui n'apparait que quand on clique au dessus -->
					<div id="bloc_input_reduction" style="display:none">
						<div id="bloc_reduction_associe">
							<label class="texte_site_noir" for="champ_input_reduction">Associer une réduction existante : </label><br />
							<select onchange="changement_reduction_plat();" id="reduction_associe" class="input reset_input" name="reduction_associe" >
								<option value="0"></option>
								<?php
								$requete_reduction = $bdd->prepare("SELECT id,idimage,libelle,utilisateur FROM reductions WHERE login = :login ");
								$requete_reduction->execute(array(':login' => $_SESSION['login'])) ;

								while ($donnees_reduction = $requete_reduction->fetch())
								{
									//htmlentities sur tout les array sensible pour éviter les failles xss
									$donnees_reduction = protection_array_faille_xss($donnees_reduction) ;
									?>
									<option class="option_reduction_associe" value="<?php echo $donnees_reduction['id']; ?>"><?php echo $donnees_reduction['libelle']; ?></option>
									<?php
								}
								?>
							</select><br />
							<p onclick="switch_associe_nouvelle_reduction();" class="choix_autre_choix">Nouvelle réduction</p>
						</div>
						<div id="bloc_champ_input_reduction" style="display:none">
							<label class="texte_site_noir" for="champ_input_reduction">Nouvelle réduction : </label><br />
							<span style="font-size:10px">ex : - 20% sur le prix du repas</span>
							<input onchange="changement_reduction_plat();" class="input reset_input" type="text" id="champ_input_reduction"/><br />
							<p onclick="ajouter_reduction_compte('pendant-commande');" class="choix_autre_choix">Valider</p>
						</div>
						<img style="display:none" id="ok_changement_reduction" src="../imgs/okchargement.png" alt="picto ok" />
					</div>
					<script>
						if(document.getElementById('offre_reduction').checked == true )
						{
							document.getElementById('bloc_input_reduction').style.display = 'block' ;  
						}
					</script>
					<br /> 
				</div>
				
				<p class="titre">Choix d'options</p>
				<p class="texte_site_noir">3. Choisissez vos options supplémentaires</p>
				<div class="bloc_offre" id="formulaire6">
					<img width="100%" height="180px" src="../imgs/option_news.<?php echo versionning('imgs/option_news.png'); ?>.png"/>
					<span class="description_offre" >News DMF</span><br />
					<span class="option_detail">Chaque plat fera l'objet de news envoyés directement sur le compte DonneMoiFaim des utilisateurs. Permet de cibler les clients potentiels directement !</span><br />
					<p class="prix_abonnement">Prix: <?php echo $offre_formule['news'] ; ?>€ par plat <span class="nombre_plat_span"></span></p>
					<input id="offre_news" onclick="calcule_total();" class="input_checkbox" type="checkbox" />
					<br /> 
				</div>
				<div class="bloc_offre" id="formulaire5">
					<img width="100%" height="180px" src="../imgs/option_facebook.<?php echo versionning('imgs/option_facebook.png'); ?>.png"/>
					<span class="description_offre" >Facebook</span><br />
					<span class="option_detail">Un statut sera créé sur notre page facebook avec l'ensemble de vos plats ajoutés touchant ainsi une multitude d'internautes !</span><br />
					<p class="prix_abonnement">Prix: <?php echo $offre_formule['facebook'] ; ?>€ par plat <span class="nombre_plat_span"></span></p>
					<input id="offre_facebook" onclick="calcule_total();" class="input_checkbox" type="checkbox" />
					<br /> 
				</div>
				<!-- type de paiement -->
				<input id="champ_type_offre_abonnement" name="cmd" type="hidden" value="_xclick-subscriptions" />
				<!-- Lien VPN -->
				<input name="notify_url" type="hidden" value="<?php echo $protocole_site.''.$_SERVER['HTTP_HOST']; ?>/compte-resto/paiement/trait_paiement.php" />
				<!-- Code Marque -->
				<input name="bn" type="hidden" value="Donnemoifaim_AutomaticBilling_FR" />
				<!-- Prix -->
				<input id="champ_prix_remplissage" name="amount" type='hidden' value="<?php echo $_SESSION['nombre_image'] * $offre_formule['1']; ?>" />
				<!-- quantité -->
				<input id="champ_paypal_quantite" name="quantity" type='hidden' value=""/>
				<!-- Devise -->
				<input name="currency_code" type="hidden" value="EUR" />
				<!-- Taxes --> 
				<input name="tax" type="hidden" value="0.00" />
				<!-- Page de retour une fois terminé -->
				<input name="return" type="hidden" value="<?php echo $protocole_site.''.$_SERVER['HTTP_HOST']; ?>/compte-resto/paiement/finalisation.html" />
				<!-- Page de retour si on n'annule -->
				<input name="cancel_return" type="hidden" value="<?php echo $protocole_site.''.$_SERVER['HTTP_HOST']; ?>/compte-resto/ajout-de-plat.html" />
				<!-- Adresse ou envoyer l'argent -->
				<input name="business" type="hidden" value="<?php echo $adresse_mail_paiement; ?>" />
				<!-- Le nom de l'object -->
				<input id="champ_nom_remplissage" name="item_name" type="hidden" value="" />
				<!-- Pour qu'il est pas besoin de mettre de commentaire -->
				<input name="no_note" type="hidden" value="1" />
				<!-- Langue -->
				<input name="lc" type="hidden" value="FR" />
				<!-- ID de l'acheteur pour pouvoir récupérer le VPN avec séparateur pour récupérer plus vite -->
				<input id="champ_custum_remplissage" name="custom" type="hidden" value="" />
				
				<!-- pas besoin de l'adresse -->
				<input name="no_shipping" type="hidden" value="1" />
				
				<!-- |||||| inputs pour l'abonnement récurrent |||||| -->
				
				<!-- input pour une "version d'essai" qui ici n'en sera pas une -->
				<input id="champ_premier_mois_paiement" type="hidden" name="a1" value="" />
				<!-- Le temps que va durer la dites période d'essai en temps -->
				<input type="hidden" name="p1" value="1" />
				<!-- En mois la période D pour jour , W pour semaine , M pour mois et Y pour year -->
				<input type="hidden" name="t1" value="M" />
				<!-- correspond à ce que l'on va payer -->
				<input id="champ_prix_abonnement_mois" type="hidden" name="a3" value="" />
				<!-- nombre de périodes de temps entre chaque répétition en gros 1 c'est tout les 1 mois -->
				<input type="hidden" name="p3" value="1" />
				<!-- période de paiement, ici c'est tout les mois -->
				<input type="hidden" name="t3" value="M" />
				<!-- pour que le paiement se reproduise automatiquement -->
				<input type="hidden" name="src" value="1" />
				<!--<h4><a href="" target="_blank" >Voir les conditions d'achat et de réglements</a></h4>-->
				<br /><br />
				<p class="titre">Choix email facture</p>
				<p class="texte_site_noir">4. Choisissez qui doit reçevoir les factures de cette commande</p>
				<br />
				<select id="mail_qui_recoit_facture" class="input">
					<?php 
						$nombre_contact_pro = 0 ; 
						
						$requete_mail_pro_facture = $bdd->prepare('SELECT email, prenom, nom FROM contact_pro WHERE login  = :login') ;
						$requete_mail_pro_facture->execute(array(':login' => $_SESSION['login']));
						
						while($info_mail_pro = $requete_mail_pro_facture->fetch())
						{?>
							<option value="<?php echo $info_mail_pro['email']; ?>"><?php echo $info_mail_pro['email']; ?></option>
						<?php
							$nombre_contact_pro++ ; 
						}
						?>
							<option value="<?php echo $_SESSION['mail']; ?>"><?php echo $_SESSION['mail']; ?></option>
						<?php
						$requete_mail_pro_facture->CloseCursor() ; 
					?>
				</select>
				<br /><br />
				
				<button style="font-size:14px" type="submit" class="choix_menu_compte_resto reset_button">
					<img src="../imgs/picto-payer.<?php echo versionning('imgs/picto-payer.png') ; ?>.png" alt="payer" />
					<br />
					Régler ma commande
				</button>
				<img src="/imgs/chargement.gif" id="loading_envoi" alt="loading" width="20px" style="display:none; position:absolute" /><br />
				
				<button type="submit" class="reset_button bloc_affichage_numeriques" >
					Total : <span id="total_formule" >0</span> € <span class="chaque_mois_complement_abonnement"> <span class="chaque_mois_complement_abonnement"> le premier mois, puis <span class="prix_resume_abonnement_plat_unitaire"></span> € par mois</span>
				</button>
				<br />
				<!-- Permet de recuperer facilement le choix de l'abonnement-->
				<input id="stocker_choix_abonnement" type="hidden" value="1" />
				
				<br />
				<?php display_paiement_securise(); ?>
				<div id="bloc_godMod_paiement" style="margin:0">
					<?php
					// Si il existe une connexion de dieu + verif que le login est bien un admin on met un bouton pour payer spécial 
					if(!empty($_SESSION['login_connexion_de_dieu_resto']) && in_array($_SESSION['login_connexion_de_dieu_resto'], $pseudo_admin))
					{?>
						<input class="choix_autre_choix buttonAdmin" type="submit" value="Paiement de Dieu - <?php echo $_SESSION['login_connexion_de_dieu_resto'].' autorisé - '; ?> ">
						<img src="/imgs/chargement.gif" id="loading_envoi" alt="loading" width="20px" style="display:none; position:absolute" /><br />
					<?php
					}?>
				</div>
			</form>
			<div class="bloc_button_precedent_processus">
				<img onclick="retour_etape_ajout_plat('<?php echo $type_upload; ?>');" class="picto_precedent" src="../imgs/picto-precedent.png" alt="picto precedent"/><br />
				<span class="texte_site">Retour</span>
			</div>
			<br /><br />
			<input id="token_ajout_de_plat" type="hidden" value="<?php echo $_SESSION['token'] ; ?>"  />
		<script>
			// Création du tableau de prix
			prix_formule = {'1' : <?php echo $offre_formule['1'] ; ?>  , '2' : <?php echo $offre_formule['2'] ; ?>  , '3' : <?php echo $offre_formule['3'] ; ?>  , 'abonnement' : <?php echo $offre_formule['abonnement'] ; ?> ,  'facebook' : <?php echo $offre_formule['facebook'] ; ?> , 'news' : <?php echo $offre_formule['news'] ; ?>} ;
			temps_abonnement_formule = {'1' : <?php echo $temps_abonnement_formule['1'] ; ?>  , '2' : <?php echo $temps_abonnement_formule['2'] ; ?>  , '3' : <?php echo $temps_abonnement_formule['3'] ; ?> } ;
			
			login_compte_resto = "<?php echo $_SESSION['login']; ?>" ;
			
			// Si c'est une ancienne version de navigateur
			if(typeof(FormData) == 'undefined')
			{
				// On check la compatibility ajout-image
				window.onload = function()
				{
					checkCompatibilityFormData('ajout-de-plat');
				};
			}

		</script>
		</div>
		<!-- insertion du footer -->
		<?php include('../include/footer.php'); ?>
		
		<script async type="text/javascript" src="../javascript/compte_resto.<?php echo versionning($fichier = 'javascript/compte_resto.js'); ?>.js"></script>
		<script async type="text/javascript" src="../javascript/general.<?php echo versionning($fichier = 'javascript/general.js'); ?>.js"></script>
	</body>
</html>
