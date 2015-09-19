<?php

include('../include/configcache.php') ;
include('../include/fonctions.php') ;

// Requete pour connaitre l'adresse mail à contacter
if(!empty($_GET['mail']))
{
	$requete_mail = $bdd->prepare('SELECT mail FROM client WHERE mail_crypte = :mail_crypte') ; 
	$requete_mail->execute(array(':mail_crypte' => $_GET['mail'])) ;
	
	// Si on à bien trouvé on ouvre le mailto
	if($donnees_mail = $requete_mail->fetch())
	{
		$donnees_mail['mail'] ; 
		
		header('location:mailto:'.$donnees_mail['mail']);
	}
}
?>

<script>
	// Fermer la fenetre actuelle
	self.close() ; 
</script>