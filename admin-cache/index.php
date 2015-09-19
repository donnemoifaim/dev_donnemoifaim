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
		$meta_titre = 'Accueil admin' ; 
		$meta_description = 'Voici l\'interface dédiée à l\'administration.' ;
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
				
				$requete_erreur = $bdd->query('SELECT * FROM erreur_site WHERE statut != 1 ORDER BY date_ajout') ;
				
				while($donnees_erreur = $requete_erreur->fetch())
				{
					$donnees_erreur = protection_array_faille_xss($donnees_erreur) ; 
					
					$date_ajout = date('d-m-Y' , $donnees_erreur['date_ajout']);
				?>
					<div id="bloc_tache<?php echo $donnees_erreur['id']; ?>" class="list_erreur texte_site_noir">
						<span class="titre_admin_erreur"><?php echo $donnees_erreur['intitule']; ?></span>
						<span class="date_avis"><?php echo $date_ajout ?></span><br /><br />
						<span><?php echo $donnees_erreur['description']; ?></span><br /><br />
						<span><?php echo $donnees_erreur['ip_entrant']; ?></span><br /><br />
						<span><?php echo $donnees_erreur['identifiant_unique']; ?></span><br /><br />
						<span><?php echo $donnees_erreur['type']; ?></span><br /><br />
						
						<p onclick="valider_tache(1 , <?php echo $donnees_erreur['id']; ?> , 'erreur_site')" style="cursor:pointer; display:inline-block" class="texte_site_admin text_admin_erreur">
							<img src="../imgs/picto-detective.<?php echo versionning('imgs/picto-detective.png'); ?>.png" alt="picto detective" /><br />
							Valider 
						</p>
					</div>
				<?php
				}
			
				// View des taches
				$requete_tache = $bdd->query('SELECT * FROM tache_admin WHERE statut != 1 ORDER BY priorite') ;
				
				while($donnees = $requete_tache->fetch())
				{
					$donnees = protection_array_faille_xss($donnees) ; 
					
					$date_ajout = date('d-m-Y' , $donnees['date_ajout']);
				?>
					<div id="bloc_tache<?php echo $donnees['id']; ?>" class="list_tache texte_site_noir">
						<span class="titre_admin"><?php echo $donnees['libelle']; ?></span>
						<span class="date_avis"><?php echo $date_ajout ?></span><br /><br />
						<span><?php echo $donnees['description']; ?></span><br /><br />
						<?php 
						// Si c'est une validation de plat on montre un aperçu du plat 
						if($donnees['type'] == 'validation_plat')
						{?>
							<p class="preview_image preview_plat_valide texte_site">
								<img onclick="apercu_image('../plats/<?php echo $donnees['id_unique']; ?>.<?php echo versionning('plats/miniature/'.$donnees['id_unique'].'.jpg'); ?>.jpg' ) ;"  src="../plats/miniature/<?php echo $donnees['id_unique']; ?>.<?php echo versionning('plats/miniature/'.$donnees['id_unique'].'.jpg'); ?>.jpg" class="apercu_image_miniature" title="<?php echo $donnees['id_unique']; ?>" alt="<?php echo $donnees['id_unique']; ?> à vérifier" />
							</p>
							<br />
						<?php
						}
						// Si c'est facebook
						if($donnees['type'] == 'option_facebook_ajout')
						{?>
							<img class="picto_partage_reseaux" src="/imgs/picto-mini-facebook-partage.<?php echo versionning('imgs/picto-mini-facebook-partage.png'); ?>.png" alt="picto de partage facebook">
						</p>
						<?php
						}
						if($donnees['type'] == 'validation_news')
						{
							$requete_news = $bdd->prepare('SELECT intitule, contenu, image_news FROM news WHERE id = :id') ; 
							$requete_news->execute(array(':id' => $donnees['id_unique'])) ; 
							
							if($info_news = $requete_news->fetch())
							{?>
								<p><?php echo $info_news['intitule']; ?></p><br />
								<p><?php echo $info_news['contenu']; ?></p>
								<p class="conteneur_image_news"><img style="width:100%" src="<?php echo $info_news['image_news'] ; ?>" alt="illusatration de la news"/></p>
							<?php
							}
						}
						
						// Si c'est un choix du commercial associé 
						if($donnees['type'] == 'choix_commercial_commande')
						{
							// On va récupérer les infos de la commandes
							$requete_info_commande = $bdd->prepare('SELECT id, nom_plats, abonnement, options, mail, prix_total, prix_premier_mois, nombre_image, date_ajout, commercial_associe FROM commandes WHERE id_commande = :id_commande') ; 
							$requete_info_commande->execute(array(':id_commande' => $donnees['id_unique'])) ; 
							
							if($infos_info_commande = $requete_info_commande->fetch())
							{
							?>
								<p>
									Commande n° <span  class="texte_site"><?php echo $infos_info_commande['id'] ; ?></span>
									<br />
									Type abonnement : <span  class="texte_site"><?php echo $infos_info_commande['abonnement']; ?></span>
									<br />
									Nom plat : <span  class="texte_site"><?php echo $infos_info_commande['nom_plats']; ?></span>
									<br />
									Nombre image : <span  class="texte_site"><?php echo $infos_info_commande['nombre_image']; ?></span>
									<br />
									Options : <span  class="texte_site"><?php echo $infos_info_commande['options']; ?></span>
									<br />
									Mail : <span  class="texte_site"><?php echo $infos_info_commande['mail']; ?></span>
									<br />
									Prix total : <span  class="texte_site"><?php echo $infos_info_commande['prix_total']; ?></span>
									<br />
									Prix Premier mois : <span  class="texte_site"><?php echo $infos_info_commande['prix_premier_mois']; ?></span>
									<br /><br />
									
									Ajouté le : <span class="texte_site"><?php echo date('d-m-Y' ,$infos_info_commande['date_ajout']) ; ?></span> à <span class="texte_site"><?php echo date('h:m' ,$infos_info_commande['date_ajout']) ; ?></span>
									<br />
									
									<?php
									// On va directement récupérer des visuels images
									$requete_visuel_image_commande = $bdd->prepare('SELECT idimage FROM plat WHERE id_commande = :id_commande') ;
									$requete_visuel_image_commande->execute(array(':id_commande' => $donnees['id_unique'])) ; 
									
									while($info_visuel_image_commande = $requete_visuel_image_commande->fetch())
									{
									?>
										<p class="preview_image preview_image_miniature texte_site">
											<img onclick="apercu_image('../plats/<?php echo $info_visuel_image_commande['idimage'] ; ?>.<?php echo versionning('plats/miniature/'.$info_visuel_image_commande['idimage'].'.jpg'); ?>.jpg' ) ;"  src="../plats/miniature/<?php echo $info_visuel_image_commande['idimage']; ?>.<?php echo versionning('plats/miniature/'.$info_visuel_image_commande['idimage'].'.jpg'); ?>.jpg" class="apercu_image_miniature" title="<?php echo $info_visuel_image_commande['idimage']; ?>" alt="miniature plat" />
										</p>
									<?php
									}
									?>
								</p>
								<select id="select_commercial_commande_choix<?php echo $donnees['id']; ?>" class="input reset_input" onchange="modifier_commercial_associe_commande('<?php echo $donnees['id_unique']; ?>' ,  <?php echo $donnees['id']; ?> , '<?php echo $donnees['type']; ?>') ;" >
									<option value=""> Aucun </option>
									<?php
										// Connaitre le nombre d'entrée dans le $pseudo_commercial_dmf
										$nombre_commerciaux_dmf = count($pseudo_commercial_dmf) ;
										
										for($i=0 ; $i < $nombre_commerciaux_dmf; $i++)
										{
											// Si c'est déjà le commercial dans la base
											if($infos_info_commande['commercial_associe'] == $pseudo_commercial_dmf[$i])
											{
												$selected_commercial = 'selected'; 
											}
											else
											{
												$selected_commercial = ''; 
											}?>
											<option <?php echo $selected_commercial ; ?> value="<?php echo $pseudo_commercial_dmf[$i]; ?>"><?php echo $pseudo_commercial_dmf[$i]; ?></option>
										<?php
										}
									?>
								</select>
								<img id="ok_attribution_commercial_commande<?php echo $donnees['id']; ?>" style="position:absolute ; display:none " src="/imgs/okchargement.png" alt="ok" />
								<br /><br />
							<?php
							}
							
							$requete_info_commande->CloseCursor() ; 
						}
						
						// Si c'est une desinscription
						if($donnees['type'] == 'annulation_abonnement')
						{
							// On va récupérer l'id de l'abonnement pour savoir de lequel on parle 
							$requete_id_abonnement = $bdd->prepare('SELECT id_abonnement FROM commandes WHERE id = :id') ; 
							$requete_id_abonnement->execute(array(':id' => $donnees['id_unique'])) ; 
							
							if($infos_id_abonnement = $requete_id_abonnement->fetch())
							{
							?>
								<a target="_blank" class="choix_autre_choix" href="https://www.<?php echo $nom_domaine_paypal; ?>/us/cgi-bin/webscr?cmd=_profile-recurring-payments&encrypted_profile_id=<?php echo $infos_id_abonnement['id_abonnement']; ?>">
									Voir l'abonnement paypal
								</a>
								<br /><br />
							<?php
							}
						}?>
						
						<p onclick="valider_tache('<?php echo $donnees['id_unique'] ; ?>' , <?php echo $donnees['id']; ?>  , '<?php echo $donnees['type'] ; ?>' , 'ok')" style="cursor:pointer; display:inline-block" class="texte_site_admin">
							<img src="../imgs/picto-detective.<?php echo versionning('imgs/picto-detective.png'); ?>.png" alt="picto detective" /><br />
							Valider 
						</p>
						<p onclick="valider_tache('<?php echo $donnees['id_unique'] ; ?>' , <?php echo $donnees['id']; ?>  , '<?php echo $donnees['type'] ; ?>' , 'non')" style="cursor:pointer; display:inline-block; margin-left:10px; " class="texte_site_admin text_admin_erreur">
							<img src="../imgs/picto-annuler.<?php echo versionning('imgs/picto-annuler.png'); ?>.png" alt="picto annuler" /><br />
							refuser 
						</p><br />
							
						<span class="texte_site_admin" style="position:absolute ; right:10px; bottom:10px">Priorité : <?php echo $donnees['priorite']; ?></span>
					</div>
				<?php
				}
			?>
			</div>
			<br />
			
		<!-- insertion du footer -->
		<?php include('../include/footer.php'); ?>
		
		<script async type="text/javascript" src="../javascript/general.<?php echo versionning('javascript/general.js'); ?>.js"></script>
		<script async type="text/javascript" src="../javascript/admin.<?php echo versionning('javascript/admin.js'); ?>.js"></script>
	</body>
</html>