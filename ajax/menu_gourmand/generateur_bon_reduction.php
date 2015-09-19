<?php
session_start() ; 

include ('../../include/configcache.php') ;
include ('../../include/fonctions.php') ;

if(!empty($_SESSION['login_visiteur']))
{
	if(!empty($_SESSION['abonnement_visiteur']) && $_SESSION['abonnement_visiteur'] != 0 || !empty($_SESSION['laisser_passer_reduction']))
	{
		// Si on à utilisé le laissé passé on le supprime
		if(!empty($_SESSION['laisser_passer_reduction']))
		{
			unset($_SESSION['laisser_passer_reduction']) ; 
		}
		
		if(!empty($_POST['id_reduction']))
		{
			// On récupère la réduction 
			$requete_reduction = $bdd->prepare("SELECT id,libelle,login,utilisateur, idimage FROM reductions WHERE id = :id_reduction ");
			$requete_reduction->execute(array(':id_reduction' => $_POST['id_reduction'])) ;

			if($donnees_reduction = $requete_reduction->fetch())
			{
				$login_resto = $donnees_reduction['login'] ; 
				
				$libelle_reduction = $donnees_reduction['libelle'] ;

				// On ajoute 1 à la réudction
				$requete_utilisateur_reduction = $bdd->prepare("UPDATE reductions SET utilisateur = utilisateur + 1 WHERE id = :id_reduction ");
				$requete_utilisateur_reduction->execute(array(':id_reduction' => $donnees_reduction['id'])) ;
				
				$bon_commande_numero = $donnees_reduction['utilisateur']  + 1 ;
				
				// On récupère les données du client tel
				$requete_resto = $bdd->prepare("SELECT nomresto,adressresto,ville FROM client WHERE login = :login_resto ");
				$requete_resto->execute(array(':login_resto' => $login_resto)) ;

				if($donnees_resto = $requete_resto->fetch())
				{
					//htmlentities sur tout les array sensible pour éviter les failles xss
					$donnees_resto = protection_array_faille_xss($donnees_resto) ;
				?>
					<div id="bloc_impression_bon_reduction" style="margin:0;position:relative; z-index:2">
						<p class="texte_site" style="margin:0;color:white; font-size:18px">
							<span style="color:white; font-size:24px" class="titre" id="titre_bon_reduction">Bon de réduction n°<?php echo $bon_commande_numero ; ?></span>
							<br /><br />
							
							<span  class="input" style="box-shadow:0 0 0 white;display:inline-block;font-size:20px; width:80%; margin:auto"><?php echo $libelle_reduction ; ?></span><br />
							
							<span id="span_reduction_valable_chez" style="font-style:italic; font-size:14px"> Valable chez <?php echo $donnees_resto['nomresto']; ?> - <?php echo $donnees_resto['adressresto']; ?></span><br />
							
							<p>Notre partenaire peut vous demander de présenter ce document lors du paiement</p>
							
							<p id="bloc_montrer_bon_reduction">
								<img src="imgs/picto-tenir-phone.png" alt="picto tenir telephone" /><br />
								<span style="font-size:12px">Version numérique</span>
							</p>
							<p id="impression_bon_commande">
								<img style="cursor:pointer" onclick="window.print();" src="imgs/picto-print.png" alt="picto print" /><br />
								<span style="font-size:12px">Version imprimable</span>
							</p>
						</p>
					</div>
				<?php
				}
				else
				{
					echo '<span class="erreur">Erreur id resto, veuillez nous contacter si le problème persiste.</span><br />' ;
				}
				
			}
			else
			{
				echo '<span class="erreur">Erreur id reduction, veuillez nous contacter si le problème persiste.</span><br />' ;
			}
		}
		else
		{
			echo '<span class="erreur">'.$erreur_interne.'</span><br />' ;
		}
	}
	else
	{
		// ecrire que la réduction n'est pas autorisé
		echo '<span class="erreur">Vous n\'êtes pas autorisé à profiter de cet réduction. <br /><br /> Veuillez vous abonner ou regarder une publicité vidéo.</span><br />' ;
	}
}
else
{
	echo '<span class="erreur">Vous devez vous connecter pour pouvoir profiter des réductions. Si le problème persiste même après votre connexion, contactez-nous.</span><br />' ;
}
