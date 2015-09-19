<?php
session_start() ; 
include('../include/compte_acces.php') ; 
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
		include('../include/header.php');
		include('inc/menu_compte_resto.php');		
		?>
		<!-- insertion du footer -->
		<?php include('../include/footer.php'); ?>
		
		<script ascr type="text/javascript" src="../javascript/compte_resto.<?php echo versionning('javascript/compte_resto.js'); ?>.js"></script>
		<script async type="text/javascript" src="/javascript/general.<?php echo versionning('javascript/general.js'); ?>.js"></script>
	</body>
</html>