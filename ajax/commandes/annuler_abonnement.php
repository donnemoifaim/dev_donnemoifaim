<?php
session_start();


include ('../../include/configcache.php') ;
include ('../../include/fonctions.php') ;

if(!empty($_SESSION['login']))
{
	if(!empty($_SESSION['token']) && !empty($_POST['token']) && $_POST['token'] == $_SESSION['token'])
	{
		if(!empty($_POST['id_abonnement']))
		{ 
			$id_commande = $_POST['id_abonnement'] ; 
			
			$requete_statut_commande = $bdd->prepare("UPDATE commandes SET statut_abonnement = :statut WHERE id = :id_commande && login = :login");
			$requete_statut_commande->execute(array(':statut' => 'attente_annulation' , ':login' => $_SESSION['login'] , ':id_commande' => $id_commande)) ;
			
			// Envoi du mail pour la tache de supression de l'abonnement
			$sujet = 'Annulation abonnement - '.$_SESSION['login'] ;
			
			$message_txt = 'Le client '.$_SESSION['login'].' à annulé son abonnement mensuel.
			
			L\'abonnement correspond à la commande '.$id_commande ; 
			
			$message_html = 'Le client <span style="color:#db302d">'.$_SESSION['login'].'</span> à annulé son abonnement mensuel.<br /><br />
			
			L\'abonnement correspond à la commande <span style="color:#db302d">'.$id_commande.'</span> ' ; 
			
			if($_SERVER['HTTP_HOST'] == 'test.donnemoifaim.fr')
			{
				// On envoi un mail pour faire ce qu'il y a à faire coté admin
				envoi_mail($mail_contact_site_test , $sujet, $message_txt, $message_html) ;
			}
			else
			{
				envoi_mail($mail_contact_site , $sujet, $message_txt, $message_html) ;
			}
			
			
			// Création de la tache de suppression de la commande 
			ajout_tache_admin($sujet, $message_txt, 1 , 'annulation_abonnement' , $_POST['id_abonnement']) ; 
		}
	}
	else
	{
		echo '<span class="erreur" >'.$erreur_faille_csrf.'</span>' ;
	}
}