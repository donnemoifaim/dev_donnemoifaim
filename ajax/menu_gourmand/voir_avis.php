<?php
session_start();

if(!empty($_SESSION['login_visiteur']))
{
	//connection à la bdd
	include('../../include/configcache.php');
	include('../../include/fonctions.php');
	
	if(!empty($_SESSION['id_resto_actuel']))
	{
		$reponse_avis = $bdd->prepare("SELECT id,contenu_avis, login, note, date_ajout FROM avis_utilisateur WHERE id_resto = :id_resto ORDER BY date_ajout DESC ");
		$reponse_avis->execute(array(':id_resto' => $_SESSION['id_resto_actuel']));
		
		while($donnees_avis = $reponse_avis->fetch())
		{
			//htmlentities sur tout les array sensible pour éviter les failles xss
			$donnees_avis = protection_array_faille_xss($donnees_avis) ;
				
			$date_avis = date('d-m-Y', $donnees_avis['date_ajout'] );
			
			$nombre_etoile = $donnees_avis['note'] ;
			
			$affichage_etoile_avis = affichage_etoile_avis($nombre_etoile , 'affichage') ; 
		?>
		  
			<p id="bloc_avis<?php echo $donnees_avis['id']; ?>" class="avis_utilisateur">
				<span id="contenu_bloc_avis<?php echo $donnees_avis['id']; ?>" class="contenu_avis_utilisateur">
					<?php echo $donnees_avis['login'] ;?><br />
					<span id="bloc_etoile_avis<?php  echo $donnees_avis['id'] ; ?>"><?php echo $affichage_etoile_avis; ?></span>
					<?php
					// Si c'est le commentaire de la personne actuellement connecté, on lui laisse la possibilité de le supprimer
					if($donnees_avis['login'] == $_SESSION['login_visiteur'])
					{
						$avis_utilisateur_deja_poste = 1 ; 
						?>
						<input id="avis_utilisateur_deja_poste" type="hidden" value="<?php echo $donnees_avis['id']; ?>" />
						<input id="input_note_avis_ajax" type="hidden" value="<?php echo $donnees_avis['note']; ?>" />
						
						<span id="bloc_supprimer_avis" class="texte_site">
							<img onclick="supp_avis(<?php echo $donnees_avis['id']; ?>);" style="cursor:pointer"  src="../imgs/picto-supp.png" alt="picto supprimer">
						</span>
					<?php
					}
					?>
					<span class="date_avis"><?php  echo $date_avis ; ?></span><br />
					" <span class="contenu_avis" id="contenu_avis<?php echo $donnees_avis['id'] ;?>" ><?php echo $donnees_avis['contenu_avis']; ?></span> "<br />
				</span>
			</p>
		<?php
			// Si l'utilisateur n'à pas posté de message on créer un input pour le dire 0
			if(!isset($avis_utilisateur_deja_poste))
			{
				// Sinon l'utilisateur n'a pas poster de message
				?>
				<input id="avis_utilisateur_deja_poste" type="hidden" value="0" />
				<?php
			}
			if(!isset($avis_existant))
			{
				$avis_existant = 1 ; 
			}
		}
		$reponse_avis->closeCursor(); // Termine le traitement de la requête
		
		if(!isset($avis_existant))
		{?>
			<p id="bloc_aucun_avis" style="font-size:18px; text-align:center" class="texte_site">
				Aucun avis n'a été ajouté pour cet établissement. Vous avez déjà eu l'occasion de goûter leurs plats : que diriez-vous de donner votre avis ?
				<br><img src="/imgs/picto-non-plat.png" alt="aucun avis utilisateur">
			</p>
			<!-- l'utilisateur n'à donc pas posté d'avis puisqu'il n'y en à pas -->
			<input id="avis_utilisateur_deja_poste" type="hidden" value="0" />
		<?php
		}
	}
	else
	{
		echo '<span class="erreur">'.$erreur_interne.'</span><br />' ;
	}
}
else
{
	echo 'non_connecte' ; 
}
?>