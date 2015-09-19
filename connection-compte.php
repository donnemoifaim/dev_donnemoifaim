<?php
session_start() ; 

// Si la personne est connecté
if (!empty($_SESSION['login']))
{
	header('location:/compte-resto/ajout-de-plat.html') ;
	die();
}
else
{}
?>
<!DOCTYPE html>
<html>
	<head>
		<?php
		$meta_titre = 'Connexion compte restaurant - DMF' ; 
		$meta_description = 'Page d\'inscription et de connexion pour les comptes restaurants afin de gérer leurs plats et infos en ligne sur donnemoifaim.' ;
		$meta_keywords = 'connexion, DMF, compte, restaurant, resto'  ;
		//Savoir si on référence la page
		$meta_robots = 'index' ;
		$titre_tete = "Connexion compte resto";
		
		include('include/en-tete.php') ;
		?>
	</head>
	<body>
		<?php include('include/header.php') ; ?> 
		<!-- connection -->
		<div style="text-align:center;">
			<h1 class="titre">Connexion compte resto</h1><br /><br />
			<div class="bloc_contenu_centre">
				<form onsubmit="connection_compte() ; return false" id="formconnection" action="/compte-resto/ajout-de-plat.html"  method="POST" >
					<button id="mdpoublie" class="mdp_oublie" type="button" onclick="voir_bloc_coulissant(id_bloc = '#bloc_mdp_compte_resto'); " >Mot de passe oublié</button>
					<br />
					<label for="login">Pseudo ou adresse email<br /></label>
					<input class="input" type="text" name="login" id="login" required/>
					<br /><br />
					<label for="mdp">Mot de passe</label><br />
					<input class="input" type="password" name="mdp" id="mdp" required/><br /><br />
					
					<img style="float:right" src="imgs/picto-compte-resto-grand.png" alt="compte resto" /><br />
					<input type="submit" value="connexion" class="input_submit reset_input">
					<p style="clear:both"></p>
					<p class="bloc_creercompte">
						<button type="button" id="creercompte" class="creercompte reset_button" onclick="voir_bloc_coulissant('#enregistrement') ; return false" >Créez votre compte resto</button>
					</p>
					<br />
				</form>
			</div>
			<!-- bloc mot de passe oublié -->
			<div id="bloc_mdp_compte_resto" class="bloc_apparait" style="display:none">
				<form onsubmit="envoyer_mdp_oublier() ; return false;" action="ajax/compte_resto/envoi_mdp_compte_resto.php" method="GET">
					<p class="titre">Mot de passe oublié</p>
					<p class="texte_site_noir" >Saisissez votre email ci-dessous, un mail vous sera envoyé afin de réinitialiser votre mot de passe.<br /></p>
					<input id="oubli_adresse_mail_resto" type="email" class="input" /><br /><br />
					<input  id="envoimail" type="submit" class="input_submit reset_input" value="valider" /><br />
				</form>
				<!-- bouton de fermeture de l'application -->
				<img class="picto_retour" onclick="cache_bloc_coulissant(id_bloc = '#bloc_mdp_compte_resto');" src="imgs/picto-retour.png" alt="picto retour" />
			</div>
			<div id="bloc_renitialisation_mdp_compte_resto" class="bloc_apparait" style="display:none" >
				<?php 
				if(!empty($_GET['renitialiser_mdp']))
				{?>
					<form onsubmit="renitialisation_mdp_compte_resto(); return false" action="../compte_resto/renitialiser_mpd_compte_resto.php" method="GET">
						<p class="titre">Réinitialiser mot de passe</p>
						<p class="texte_site_noir" >Saisissez votre nouveau mot de passe<br /></p>
						
						<label class="label_compte_visiteur" for="nouveau_mdp_compte_resto">Mot de passe : </label>
						<input onkeyup="verif_champ(id_champ = this.id , taille_condition = 6) ;" class="input" id="nouveau_mdp_compte_resto" type="password" placeholder="6 caractères min" /> <span class="verif_formulaire_compte" id="ok_nouveau_mdp_compte_resto"></span><br /><br />
						
						<input id="lien_unique_renitialisation" type="hidden" value="<?php echo $protocole_site.''.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ; ?>" />
						<input type="submit" class="input_submit reset_input" value="valider" /><br />
					</form>
					<!-- bouton de fermeture de l'application -->
					<img class="picto_retour" onclick="cache_bloc_coulissant(id_bloc = '#bloc_renitialisation_mdp_compte_resto');" src="imgs/picto-retour.png" alt="picto retour" />
				<?php
				}?>
			</div>
			<div class="bloc_contenu_centre2" style="width:90%; max-width:600px">
				<h2 style="font-size:16px" class="texte_site">Créez votre compte resto afin :</h2><br />
				<ul>
					<li style="display:block" class="texte_site_noir">- D'ajouter vos plats en ligne et pouvoir les gérer facilement <img src="/imgs/picto-couvercle-plat.<?php echo versionning('imgs/picto-couvercle-plat.png'); ?>.png" alt="plats"></li><br />
					<li style="display:block" class="texte_site_noir">- Avoir un réel suivi de vos abonnements <img src="/imgs/picto-calendrier.<?php echo versionning('imgs/picto-calendrier.png'); ?>.png" alt="suivez vos abonnements"></li><br />
					<li style="display:block" class="texte_site_noir">- Pouvoir participer à des évènements organisés par DonneMoiFaim <img src="/imgs/picto-evenement.<?php echo versionning('imgs/picto-evenement.png'); ?>.png" alt="Evènements DMF"></li><br />
					<li style="display:block" class="texte_site_noir">- Profiter d'un service clientèle à votre écoute <img src="/imgs/picto-service-client.<?php echo versionning('imgs/picto-service-client.png'); ?>.png" alt="Service clientèle"></li><br />
				</ul><br />
				<br />
				<span class="texte_site" >Offre découverte : une fois votre compte créé, vous pourrez ajouter votre premier plat gratuitement en ligne pendant 1 mois (dans la limite d'un plat).</span><br />
				<p class="center">
					<img style="cursor:pointer" src="imgs/picto-decouverte.<?php echo versionning('imgs/picto-decouverte.png'); ?>.png" alt="offre decouverte" />
				</p>
			</div>

		<!-- création d'un nouveau compte --> 
			<div id="enregistrement" class="bloc_apparait" style="display:none;">
				<!-- formulaire du création de compte -->
				<form id="formulaire_enregistrement" onsubmit="envoi_formulaire_compte() ; return false ; " method="POST" >
					<br />
					<p class="titre">Inscription compte resto</p>
					<?php 
					$type_formulaire = 'inscription';
					include('compte-resto/inc/formulaire_donnees_compte.php') ;
					?>
					<img class="picto_retour" onclick="cache_bloc_coulissant('#enregistrement');" src="imgs/picto-retour.png" alt="picto retour" />
				</form>
				<!-- formulaire d'ajout d'une facade resto --> 
				<form style="display:none" onsubmit="modif_image_facade_resto() ; return false;" id="bloc_ajout_facade_complementaire" method="POST" enctype="multipart/form-data"  >
					<br />
					<p class="titre">Informations complémentaires 1/3 <span onclick="infos_complementaire_plus_tard('suivant');" class="choix_autre_choix">Compléter plus tard</span></p>
					<?php
					$type_upload = 'facade_resto' ;
					include('compte-resto/inc/sidebar_upload.php') ;
					include('compte-resto/inc/formulaire_modif_image_facade_resto.php') ; 
					?>
					<img class="picto_retour" onclick="cache_bloc_coulissant('#enregistrement');" src="imgs/picto-retour.png" alt="picto retour" />
				</form>
				<!-- formulaire d'ajout des caractéristique resto -->
				<form style="display:none" onsubmit="modif_attribut_resto('ajout_attribus') ; return false;" id="bloc_ajout_attribus_complementaire" method="POST" >
					<br />
					<p class="titre">Informations complémentaires 2/3 <span onclick="infos_complementaire_plus_tard('suivant_contact_pro');" class="choix_autre_choix">Compléter plus tard</span></p>
					<?php
					include('compte-resto/inc/formulaire_modif_attribus_resto.php') ; 
					?>
					<img class="picto_retour" onclick="voir_cache_facade_resto();" src="imgs/picto-retour.png" alt="picto retour" />
				</form>
				<!-- formulaire d'ajout des caractéristique professionnel pour faciliter le contact --> 
				<div style="display:none" id="bloc_ajout_contact_pro">
					<br />
					<p class="titre" id="titre_ajout_contact_pro">Ajout contact pro 3/3 <span onclick="infos_complementaire_plus_tard('termine');" class="choix_autre_choix">Compléter plus tard</span></p>
					<p class="texte_site"> Afin de vous servir au mieux nous vous conseillons d'ajouter au moins 1 contact.</p>
					<button onclick="affichage_formulaire_ajout_contact_pro_creation_compte(); " class="reset_button choix_menu_compte_visiteur" >
						<img src="../../imgs/picto-ajouter.<?php echo versionning('imgs/picto-ajouter.png') ; ?>.png" alt="Ajouter un contact pro" /><br />
						Ajoutez un contact pro
					</button>
					<?php
					include('compte-resto/inc/formulaire_ajout_contact_pro.php') ; 
					?>
					<img class="picto_retour" onclick="voir_cache_contact_pro();" src="imgs/picto-retour.png" alt="picto retour" />
				</div>
				<br />
			</div>
		</div> 
		<input type="hidden" id="token_compte_juste_cree" >
		<script>
			window.onload = function()
			{
				// chargement de google map
				loadScriptGoogleMap('ok_calback_google_map') ; 
				
				// On ouvre le bloc quand tout est chargé
				bloc_apparait_responsive() ;
				
				<?php if(!empty($_GET['renitialiser_mdp']))
				{?>
					voir_bloc_coulissant(id_bloc = '#bloc_renitialisation_mdp_compte_resto');
				<?php
				}?>
			};
		</script>
		
		<script async type="text/javascript" src="javascript/compte_resto.<?php echo versionning($fichier = 'javascript/compte_resto.js'); ?>.js"></script>
		<script async type="text/javascript" src="javascript/general.<?php echo versionning($fichier = 'javascript/general.js'); ?>.js"></script>
		
		<!-- insertion du footer -->
		<?php include('include/footer.php'); ?>
	</body>
</html>