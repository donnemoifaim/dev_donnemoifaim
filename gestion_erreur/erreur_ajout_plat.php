<?php
session_start() ; 
?>
<!DOCTYPE html>
<html>
	<head>
		<?php
		$meta_titre = '' ; 
		$meta_description = '' ;
		$meta_keywords = ''  ;
		//Savoir si on référence la page
		$meta_robots = '' ; 
		include('../include/en-tete.php') ;
		?>
	</head>
	<body>	
		<?php 
		$titre_tete = "Connection compte resto";
		include('../include/header.php'); 
		?>
			<p style="text-align:center" class="texte_site">
				<img src="../../imgs/picto-non-plat.png" alt="aucun plat en ligne"><br /><br />
				Veuillez remplir le formulaire d'ajout de plat. Si ce message persiste veuillez nous contacter.<br /><br />
				<a class="choix_menu_compte_resto" href="/compte-resto/ajout-de-plat.html">Ajout de plat</a>
				<a class="choix_menu_compte_resto" href="/index.html">Accueil</a>
			</p>
		<!-- insertion du footer -->
		<?php include('../include/footer.php'); ?>
		
		<script async type="text/javascript" src="../javascript/general.<?php echo versionning($fichier = 'javascript/general.js'); ?>.js"></script>
	</body>
</html>