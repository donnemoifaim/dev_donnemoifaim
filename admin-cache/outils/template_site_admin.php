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
		$meta_titre = '' ; 
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
			<div style="text-align:center">
			<?php 
				include('inc/menu-compte-admin.php');
			?>
			</div>
		<!-- insertion du footer -->
		<?php include('../include/footer.php'); ?>
		
		<script async type="text/javascript" src="/javascript/general.<?php echo versionning('javascript/general.js'); ?>.js"></script>
		<script async type="text/javascript" src="../javascript/admin.<?php echo versionning('javascript/admin.js'); ?>.js"></script>
	</body>
</html>