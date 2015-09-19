<?php
session_start() ;

include('../../include/compte_acces.php') ; 

?>
<!DOCTYPE html>
<html>
	<head>
		<?php
		$meta_titre = 'Paiement finalisé' ; 
		$meta_description = 'Félicitations, vos plats ont été ajoutés avec succès sur nos serveurs ! ' ;
		$meta_keywords = ''  ;
		//Savoir si on référence la page
		$meta_robots = 'no-index' ; 
		include('../../include/en-tete.php') ;
		?>
	</head>
	<body>	
		<?php 
		$type_upload = 'ajout-de-plat' ;
		// Supression de toute les session de plat qui ont été créé
		supp_session_image($type_upload); 
		include('../../include/header.php'); 
		?>
			<p style="text-align:center;font-size:16px">
				<img src="/imgs/felicitations.<?php echo versionning('imgs/felicitations.png'); ?>.png" alt="felicitation" /><br /><br />
				<span style="font-size:20px" class="texte_site">Vos plats ont bien été ajoutés et liés à votre compte resto. </span><br /><br />
				
				<span class="texte_site">Une fois validés par l'équipe, vous pourrez les retrouver dans notre application 'menu-gourmand' (maximum dans les 24h). En cas de délais plus longs, n'hésitez pas à nous contacter.</span><br />
				
				Vous allez recevoir un mail de confirmation. Vos plats seront alors visibles sur votre compte resto dès la réception de ce mail.<br /><br />
				<a class="choix_autre_choix" href="../vos-plats.html">Vos plats</a> 
				<a class="choix_autre_choix" href="/compte-resto/">Accueil</a>
			</p>
		<!-- insertion du footer -->
		<?php include('../../include/footer.php'); ?>
		
		<script async type="text/javascript" src="../../javascript/general.<?php echo versionning($fichier = 'javascript/general.js'); ?>.js"></script>
	</body>
</html>