<?php
session_start() ; 

$compte_admin_access = 1; 
// Récupération des sessions
include('../include/compte_acces.php') ;

?>
<!DOCTYPE html>
<html>
	<head>
		<?php
		$meta_titre = 'Client DonneMoiFaim' ; 
		$meta_description = 'Liste des clients donnemoifaim' ;
		$meta_keywords = ''  ;
		//Savoir si on référence la page
		$meta_robots = 'no-index' ; 
		include('../include/en-tete.php') ;
		?>
	</head>
	<body>	
		<?php 
		include('../include/header.php'); 
		?>
		<div id="bloc_global_donnees_resto">
			<?php
			include('inc/menu-compte-admin.php');
			?>
			<p class="titre">Liste de nos clients resto</p><br />
			<?php
			// Récupération des informations clients
			$requete_client = $bdd->query('SELECT * FROM client ORDER BY nomresto'); 
			while($info_client = $requete_client->fetch())
			{
				$image_facade = $info_client['image_facade'] ; 
			?>
				<button class="input_submit" onclick="showMoreDetailClient(<?php echo $info_client['id']; ?>);"><?php echo $info_client['nomresto']; ?></button>
				<div id="fiche_client_<?php echo $info_client['id']; ?>" class="fiche_resto" style="display:none">
					<p class="bloc_image_fiche_resto" id="facade_resto_visielle" onclick="apercu_image('../compte-resto/image-resto/<?php echo $info_client['image_facade']; ?>.<?php echo filemtime('../compte-resto/image-resto/'.$image_facade.'.jpg') ; ?>.jpg') ; " style="background:url('../compte-resto/image-resto/<?php echo $info_client['image_facade']; ?>.<?php echo filemtime('../compte-resto/image-resto/'.$image_facade.'.jpg') ; ?>.jpg') no-repeat ; background-size:cover ">
					</p>
					<p class="texte_site_noir" style="padding:10px">
						<span class="bloc_infos_comptes">Nom d'utilisateur : <span class="texte_site"><?php echo $info_client['login'] ; ?></span></span>
						<span class="bloc_infos_comptes">Mot de passe : <?php echo $info_client['mdp'] ; ?></span>
						<span class="bloc_infos_comptes">Nom resto/commerce : <span class="texte_site"><?php echo $info_client['nomresto'] ; ?></span></span>
						<span class="bloc_infos_comptes">Votre activité : <span class="texte_site"><?php echo $info_client['type_resto'] ; ?></span></span>
						<span class="bloc_infos_comptes">Rue du resto/commerce : <span class="texte_site"><?php echo $info_client['adressresto'] ; ?></span></span>
						<span class="bloc_infos_comptes">Ville du resto/commerce : <span class="texte_site"><?php echo $info_client['ville'] ; ?></span></span>
						<span class="bloc_infos_comptes">Adresse email :  <a class="texte_site" href="mailto:<?php echo $info_client['mail'] ; ?>"><?php echo $info_client['mail'] ; ?></a></span>
						<span class="bloc_infos_comptes">Téléphone : <a class="texte_site" href="tel:<?php echo $info_client['telephone'] ; ?>"><?php echo $info_client['telephone'] ; ?></a></span>
						<span class="bloc_infos_comptes">Site internet : <a  class="texte_site" href="<?php echo $info_client['site_internet'] ; ?>" target="_blank"><?php echo $info_client['site_internet'] ; ?></a></span>
						
						<!-- contact pro -->
						<div style="text-align:center">
							<p class="titre" style="margin:0">Contact pro</p>
							<?php
								afficher_liste_contact_pro($info_client['login'] , 'admin') ;
							?>
						</div>
						
						<p class="texte_site" style="border-top: 2px dashed #db302d; text-align:center"><br />Tous les plats du resto en ligne : </p>
						
						<div style="text-align:center">
							<?php 
							// Besoin de $info_referencement_page[id] doit etre a nul acar il correspond au plat que l'on ne veut pas voir car il est déjà la, mais en admin il n'y a pas de plat + le login qui cette fois correspond au login du client
							
							$info_referencement_page = array('id' => '0' , 'login' => $info_client['login']) ; 
							
							// Permet de différrencier la partie visiteur de admin pour l'affichage des plats
							$partie_admin_voir_plat_client = 1 ;
							
							include('../ajax/menu_gourmand/voir_tout_plats.php') ; ?>
						</div>
						
						<button class="input_submit" onclick="showMoreDetailClient(<?php echo $info_client['id']; ?>);"> Refermer </button>
						
						<button style="float:right;" class="choix_autre_choix buttonAdmin" onclick="ConnexionRestoDieu(<?php echo $info_client['id']; ?>);">connexion de Dieu</button>
					</p>
				</div>
			<?php
			}?>
		</div><br /><br />
		<!-- insertion du footer -->
		<?php include('../include/footer.php'); ?>
		
		<script async type="text/javascript" src="/javascript/general.<?php echo versionning('javascript/general.js'); ?>.js"></script>
		<script async type="text/javascript" src="/javascript/admin.<?php echo versionning('javascript/admin.js'); ?>.js"></script>
	</body>
</html>