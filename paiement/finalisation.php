<?php
	session_start() ;
?>
<!DOCTYPE html>
<html>
	<head>
		<?php
		$meta_titre = 'Paiement finalisé' ; 
		$meta_description = '' ;
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
			<p style="text-align:center;font-size:16px">
				<img src="/imgs/felicitations.<?php echo versionning($fichier = 'imgs/felicitations.png'); ?>.png" alt="felicitation" /><br /><br />
				<span style="font-size:20px" class="texte_site">Félicitations, votre compte est passé en mode premium !</span> <br /><br />
				<span>Merci de nous faire confiance, nous espérons que cela vous plaira ! </span><br /><br />
				
				<a class="choix_autre_choix" href="../<?php echo $_SESSION['image_precedente']; ?>.html">Retour au menu gourmand</a> 
			</p>
		<!-- insertion du footer -->
		<?php include('../include/footer.php'); ?>
		
		<script async type="text/javascript" src="../javascript/general.<?php echo versionning($fichier = 'javascript/general.js'); ?>.js"></script>
	</body>
</html>