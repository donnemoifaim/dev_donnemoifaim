<?php

// On remplace le cron qui est un dossier en trop ! les taches cron ne peuvent que fonctionner avec des chemin serveur

// Attention, le fichier pointé est bien dans public_html ! 
include ('/home/mxxbhatw/public_html/include/configcache.php');
include ('/home/mxxbhatw/public_html/include/fonctions.php');
 
// Différent de l'etat 2 qui correspond à l'état de renouvellement
$requete_delete = $bdd->query('SELECT  c.mail mail, p.idimage idimage, p.login, p.nomplat, p.date_ FROM plat p INNER JOIN client c ON p.login = c.login  WHERE date_ < '.time().' - 2629743  AND p.abo = 1 && etat != 2 OR date_ < '.time().' - 15778463  AND p.abo = 2 && etat != 2 OR date_ < '.time().' - 31556926  AND p.abo = 3 && etat != 2');
$nbsup = 0 ; 

while ($donnees = $requete_delete->fetch())
{
	envoi_mail_supression_plat($donnees) ; 
	
	// On archive le plat
	archivage_plat($idimage , '') ; 

$nbsup++;

}

echo ($nbsup.' images supprimées ! ') ; 

?>
