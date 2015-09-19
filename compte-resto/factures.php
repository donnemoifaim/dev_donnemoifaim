<?php
session_start() ; 
include('../include/compte_acces.php') ; 
?>
<!DOCTYPE html>
<html>
	<head>
		<?php
		$meta_titre = 'Vos factures DMF' ; 
		$meta_description = 'Liste de vos factures aux abonnements de vos plats.' ;
		$meta_keywords = 'factures'  ;
		//Savoir si on référence la page
		$meta_robots = 'no-index' ; 
		include('../include/en-tete.php') ;
		?>
	</head>
	<body>	
		<?php 
		include('../include/header.php');
		include('inc/menu_compte_resto.php');

		// On va récupérer toute les années ou la personne à des factures
		$requete_facture_annees = $bdd->prepare('SELECT date_ajout FROM commandes WHERE login = :login') ;
		$requete_facture_annees->execute(array(':login' => $_SESSION['login'])) ;
		
		while($info_factures_annees = $requete_facture_annees->fetch())
		{
			$info_factures_annees[''] ; 
		}
		
		$requete_facture_annees->CloseCursor() ; 
		
		?>
			<div style="text-align:center">
				<p class="titre">Ensemble de vos factures DMF</p>
				<p class="text_site_noir">
					<img src="../imgs/picto-pdf.<?php echo versionning('imgs/picto-pdf.png'); ?>.png" alt="fichier pdf" />
				</p>
				<div id="bloc_facture">
					<?php
						// Récupération de toute les factures à la date donnée
						$requete_facture = $bdd->prepare('SELECT id, abonnement, nombre_image, numero_facture, date_ajout, date_paiement, statut_abonnement FROM commandes WHERE login = :login ORDER BY id DESC') ;
						$requete_facture->execute(array(':login' => $_SESSION['login'])) ; 
						
						$backgroundColorFacture = '#820F0F' ; 
						
						while($info_facture = $requete_facture->fetch())
						{
							// Si le y à bien des factures associés à la commande
							if(is_file('pdf/facture-'.$info_facture['id'].'-'.$info_facture['numero_facture'].'.pdf'))
							{
								if($backgroundColorFacture == '#820F0F')
								{
									$backgroundColorFacture = '#db302d' ;
									
									// Couleur du lien de téléchargement du pdf
									$classe_lien_pdf = 'choix_autre_choix_blanc' ; 
								}
								else
								{
									$backgroundColorFacture = '#820F0F' ;
									
									// Couleur du lien de téléchargement du pdf
									$classe_lien_pdf = 'choix_autre_choix' ; 
								}
								
								// Uniquement si le fichier en question exsite
								?>
								<div class="bloc_lien_facture" style="background-color:<?php echo $backgroundColorFacture; ?>">
									<p class="text_lien_facture">
										Commande n° 
										<?php
										echo $info_facture['id'].' - ' ; 
										
											if($info_facture['nombre_image'] > 1)
											{
												$complement_s = 's' ; 
											}
											else
											{
												$complement_s = '' ; 
											}
											
											if($info_facture['abonnement'] == 'abonnement')
											{
												echo 'Abonnement  '.$info_facture['nombre_image'].' plat'.$complement_s ; 
											}
											else
											{
												echo $info_facture['nombre_image'].' plat'.$complement_s.' en ligne' ; 
											}
											
											if($info_facture['abonnement'] == 'abonnement')
											{
												if($info_facture['statut_abonnement'] == 'active')
												{
													$color_statut_commande = 'green' ;
													$statut_commande = ' - en ligne' ;
												}
												else if($info_facture['statut_abonnement'] == 'arret')
												{
													$color_statut_commande = '#333' ;
													$statut_commande = ' - annulé' ;
												}
												else if($info_facture['statut_abonnement'] == 'attente_annulation')
												{
													$color_statut_commande = 'orange' ;
													$statut_commande = ' - annulation en cours' ;
												}
												else if($info_facture['statut_abonnement'] == 'en_attente')
												{
													$color_statut_commande = 'blue' ;
													$statut_commande = ' - en attente' ;
												}
											?>
												<span id="statut_facture<?php echo $info_facture['id']; ?>" style="color:<?php echo $color_statut_commande; ?>"><?php echo $statut_commande ; ?></span>
											<?php
											}
										?>
									</p>
									<?php
									// On va créer tant que y à de numéro de facture
									for($i=0 ; $i < $info_facture['numero_facture'] + 1 ; $i++)
									{
										// Si ce n'est pas un abonnement
										if(empty($info_facture['date_paiement']))
										{
											$date_facture = $info_facture['date_ajout'] ; 
										}
										else
										{
											// Date de paiement moins le nombre de mois séparant la transaction
											$date_facture = $info_facture['date_paiement'] - 2629743 * ($info_facture['numero_facture'] - $i)  ; 
										}
										
										$date_facture = date('d-m-Y' , $date_facture) ; 
										
										if(is_file('pdf/facture-'.$info_facture['id'].'-'.$i.'.pdf'))
										{
										?>
											<a class="<?php echo $classe_lien_pdf ; ?>" target="_blank" href="pdf/telecharger-facture.php?id_facture=<?php echo $info_facture['id'] ; ?>">Facture du <?php echo $date_facture ; ?></a>
										<?php
										}
									}
									// Si c'est un abonnement on va mettre un lien pour pouvoir annuler l'abonnement
										if($info_facture['abonnement'] == 'abonnement')
										{
											// Seulement si l'abonnement est activé
											if($info_facture['statut_abonnement'] == 'active')
											{
											?>
												<div id="bloc_button_annuler_commande<?php echo $info_facture['id']; ?>">
													<br />
													<button onclick="annuler_abonnement_plat(<?php echo $info_facture['id']; ?>); " class="reset_button" style="float:right; color:white; cursor:pointer"> Annuler l'abonnement </button>
													<p style="clear:both"></p>
												</div>
											<?php
											}
										}?>
								</div>
							<?php
							}
						}
					?>
				</div>
			</div>
			
			<input id="token_supp_abonnement" type="hidden" value="<?php echo $_SESSION['token'] ; ?>" />
		<!-- insertion du footer -->
		<?php include('../include/footer.php'); ?>
		
		<script ascr type="text/javascript" src="../javascript/compte_resto.<?php echo versionning('javascript/compte_resto.js'); ?>.js"></script>
		<script async type="text/javascript" src="/javascript/general.<?php echo versionning('javascript/general.js'); ?>.js"></script>
	</body>
</html>