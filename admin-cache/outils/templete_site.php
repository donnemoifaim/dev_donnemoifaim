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
		include('include/en-tete.php') ;
		?>
	</head>
	<body>	
		<?php 
		include('include/header.php'); 
		?>
		<!-- insertion du footer -->
		<?php include('include/footer.php'); ?>
		
		<script async type="text/javascript" src="/javascript/general.<?php echo versionning('javascript/general.js'); ?>.js"></script>
	</body>
</html>