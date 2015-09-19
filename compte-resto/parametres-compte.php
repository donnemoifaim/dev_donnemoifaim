<?php
session_start();
include('../include/compte_acces.php') ; 
?>

<!DOCTYPE html>
<html>
<head>
		<?php
		$meta_titre = 'Modifiez vos données personnelles - DonneMoiFaim' ; 
		$meta_description = 'Modifiez les données relatives à votre compte en toute sécurité' ;
		$meta_keywords = ''  ;
		//Savoir si on référence la page
		$meta_robots = 'noindex' ; 
		include('../include/en-tete.php') ; 
		?>
</head>

<body >
	<?php
	include('../include/header.php');
	include('inc/menu_compte_resto.php');	?> 
	
	<div id="bloc_global_donnees_resto">
		<div class="fiche_resto">
			<?php
				$image_facade = $_SESSION['image_facade'] ; 
			?>
			<p class="bloc_image_fiche_resto" id="facade_resto_visielle" onclick="apercu_image('image-resto/<?php echo $_SESSION['image_facade']; ?>.<?php echo filemtime('image-resto/'.$image_facade.'.jpg') ; ?>.jpg') ; " style="background:url('image-resto/<?php echo $_SESSION['image_facade']; ?>.<?php echo filemtime('image-resto/'.$image_facade.'.jpg') ; ?>.jpg') no-repeat ; background-size:cover ">
			</p>
			<span style="position:absolute; right:5px; top:5px" onclick="voir_bloc_modif_resto('bloc_ajout_facade_complementaire');" class="choix_autre_choix">Modifier l'image</span>
			<p class="texte_site_noir" style="padding:10px">
				<span class="bloc_infos_comptes">Nom d'utilisateur : <span class="texte_site"><?php echo $_SESSION['login'] ; ?></span></span>
				<span class="bloc_infos_comptes">Mot de passe : <img src="/imgs/picto-password.<?php echo versionning('imgs/picto-password.png') ; ?>.png" alt="picto password"/></span>
				<span class="bloc_infos_comptes">Nom resto/commerce : <span class="texte_site"><?php echo $_SESSION['nomresto'] ; ?></span></span>
				<span class="bloc_infos_comptes">Votre activité : <span class="texte_site"><?php echo $_SESSION['type'] ; ?></span></span>
				<span class="bloc_infos_comptes">Rue du resto/commerce : <span class="texte_site"><?php echo $_SESSION['adressresto'] ; ?></span></span>
				<span class="bloc_infos_comptes">Ville du resto/commerce : <span class="texte_site"><?php echo $_SESSION['ville'] ; ?></span></span>
				<span class="bloc_infos_comptes">Votre adresse email : <span class="texte_site"><?php echo $_SESSION['mail'] ; ?></span></span>
				<span class="bloc_infos_comptes">Téléphone : <span class="texte_site"><?php echo $_SESSION['telephone'] ; ?></span></span>
				<span class="bloc_infos_comptes">Site internet : <span class="texte_site"><?php echo $_SESSION['site_internet'] ; ?></span></span>
			</p>
			<p style="text-align:center;">
				<span onclick="voir_bloc_modif_resto('form_modif_donnees_compte_resto');" style="margin-bottom:" class="choix_autre_choix">Modifiez vos données personnelles</span>
			</p>
		</div>
		<div id="bloc_conteneur_infos_et_contact_pro">
			<div class="fiche_resto" style="text-align:center">
				<p class="titre">Vos infos complémentaires<br /><span class="texte_site_noir" style="font-size:12px">visibles par les utilisateurs</span></p>
				<?php
					$attribus_resto = $_SESSION['attribus'] ; 
					
					// Affichage des pictos attributs 
					include('inc/attribus_resto_affichage.php') ;
					
					// Transformation de la chaine de caractère $tableau_attribus['attribus_checked'] pour pouvoir l'utiliser comme un tableau
					$tableau_attribus_checked = explode(',' , $tableau_attribus['attribus_checked']) ; 
				?>
				<br />
				<span onclick="voir_bloc_modif_resto('bloc_ajout_attribus_complementaire');" style="margin-bottom:" class="choix_autre_choix">Ajouter ou modifier</span>
			</div>
			<div class="fiche_resto" style="text-align:center">
				<p class="titre">Contacts pro</p> 
				<button onclick="document.getElementById('input_id_modif_contact_pro').value='' ; voir_bloc_modif_resto('bloc_contact_pro_modif_ajout');" class="reset_button choix_menu_compte_visiteur" >
					<img src="../../imgs/picto-ajouter.<?php echo versionning('imgs/picto-ajouter.png') ; ?>.png" alt="Ajouter un contact pro" /><br />
					Ajoutez un contact pro
				</button>
				<br />
				<?php
					afficher_liste_contact_pro($_SESSION['login'] , 'parametre_compte') ; 
				?>
				<br />
			</div>
		</div>
	</div>
	<br />
	<!-- formulaire de modification du compte ainsi que son conteneur -->
	<div id="bloc_modif_compte_resto" class="bloc_apparait" style="display:none">
		<form id="form_modif_donnees_compte_resto" class="form_modif_compte_resto" style="display:none" onsubmit="envoi_formulaire_compte('modif_formulaire');return false;" method="POST" action="../ajax/modif_donnees/modif_donnees.php">
			<div style="text-align:center">
				<br />
				<?php include('inc/formulaire_donnees_compte.php') ; ?>
			</div>
		</form>
		<form style="display:none" class="form_modif_compte_resto" onsubmit="modif_attribut_resto('modif_compte') ; return false;" id="bloc_ajout_attribus_complementaire" method="POST" >
			<?php
			include('inc/formulaire_modif_attribus_resto.php') ; 
			?>
		</form>
		<!-- formulaire d'ajout d'une facade resto --> 
		<form style="display:none" class="form_modif_compte_resto" onsubmit="modif_image_facade_resto('compte') ; return false;" id="bloc_ajout_facade_complementaire" method="POST" enctype="multipart/form-data"  >
			<br />
			<?php
			$type_upload = 'facade_resto' ;
			include('inc/sidebar_upload.php') ;
			include('inc/formulaire_modif_image_facade_resto.php') ; 
			?>
		</form>
		<div class="form_modif_compte_resto" id="bloc_contact_pro_modif_ajout" style="display:none">
			<p class="titre">Modification contact</p><br />
			<?php 
				include('inc/formulaire_ajout_contact_pro.php') ;
			?>
			<br /><br />
		</div>
		<br />
		<img class="picto_retour" onclick="cache_bloc_coulissant('#bloc_modif_compte_resto') ;" src="../imgs/picto-retour.png" alt="picto retour" />
	</div>
	<?php include ('../include/footer.php') ; ?>
	
	<script ascr src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
	<script ascr type="text/javascript" src="../javascript/compte_resto.<?php echo versionning('javascript/compte_resto.js'); ?>.js"></script>
	<script async type="text/javascript" src="../javascript/general.<?php echo versionning('javascript/general.js'); ?>.js"></script>
</body>
</html>