<?php
session_start() ; 
?>
<!DOCTYPE html>
<html>
	<head>
		<?php
		$meta_titre = 'Tous les plats - DonneMoiFaim' ; 
		$meta_description = 'Liste de tous les plats disponible en ligne sur l\'application donnemoifaim. Suivez l\'évolution de l\'application !.' ;
		$meta_keywords = 'liste, plat, annuaire, restaurant'  ;
		//Savoir si on référence la page
		$meta_robots = 'index' ; 
		include('include/en-tete.php') ;
		?>
	</head>
	<body>	
		<?php 
		include('include/header.php'); 
		?>
		<div style="text-align:center">
			<h1 class="titre">Liste de tous les plats en ligne</h1>
			<a class="choix_autre_choix" href="https://donnemoifaim.fr/menu-gourmand.html">DonneMoiFaim !</a><br />
			<br />
			<p class="texte_site">De nouveaux plats sont mis en ligne chaque jour, suivez-les sur notre page Facebook ! </p>
			<div style="overflow: hidden; text-align:center">
				<div class="fb-like" data-href="https://www.facebook.com/donnemoifaim" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>
			</div>
			<p class="texte_site">Plats en ligne : <span id="plat_selectionne"></span></p>
			<?php
			// on va chercher une liste de tout les plats en ligne
			
			$plat_selectionne = 0 ;
			
			$requete_tout_plat = $bdd->query('SELECT id, idimage, nomplat FROM plat WHERE etat = 1') ; 
			
			while($info_tout_plat = $requete_tout_plat->fetch())
			{?>
				<p class="preview_image texte_site" id="image_plat<?php echo $info_tout_plat['id'] ;?>">
					<a href="<?php echo $info_tout_plat['idimage'] ; ?>.html" title="<?php echo $info_tout_plat['nomplat'] ;?>">
						<img class="apercu_image_miniature" src="plats/miniature/<?php echo $info_tout_plat['idimage'] ; ?>.<?php echo versionning('plats/miniature/'.$info_tout_plat['idimage'].'.jpg') ?>.jpg"  />
					</a>
					<span class="texte_site"><?php echo $info_tout_plat['nomplat']; ?></span>
				</p>
			<?php
				// Nombre de plat
				$plat_selectionne++ ;
			}
			echo '<script>document.getElementById("plat_selectionne").innerHTML = '.$plat_selectionne.'</script>' ; 
			?>
		</div>
		<!-- insertion du footer -->
		<?php include('include/footer.php'); ?>
		
		<script async type="text/javascript" src="/javascript/general.<?php echo versionning('javascript/general.js'); ?>.js"></script>
	</body>
</html>