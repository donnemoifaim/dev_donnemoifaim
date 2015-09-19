<?php

// Si les variable $id_commande existe déjà c'est que l'on à pas besoin de variable get

if(!empty($id_commande))
{}
else
{
	session_start(); 

	include ('../../include/configcache.php') ;
	include ('../../include/fonctions.php') ;
	include('../../include/offre_tarrif.php') ;
}

if($_SESSION['login'] || !empty($id_commande))
{
	// On formate en $id_commande_utilisable pour garder id_commande unique pour nos test
	if(!isset($id_commande))
	{
		$id_commande_utilisable = $_GET['id_commande'] ;
	}
	else
	{
		$id_commande_utilisable = $id_commande; 
	}
	
	if(!empty($id_commande_utilisable))
	{
		$requete_commande = $bdd->prepare('SELECT co.id , co.login, co.date_paiement, co.mail, co.prix_premier_mois, co.prix_total, co.abonnement,co.nombre_image,co.options,co.date_ajout,cli.adresse_postal,cli.nomresto, co.numero_facture FROM commandes co INNER JOIN client cli ON cli.login = co.login WHERE co.id_commande = :id_commande') ;
		
		$requete_commande->execute(array(':id_commande' => $id_commande_utilisable)) ; 
		
		// On vérifie que la commande existe bien
		if($info_commande = $requete_commande->fetch())
		{
			// Vérification qu'il s'agit bien du compte en question
			if($_SESSION['login'] == $info_commande['login'] || !empty($id_commande))
			{
				// On cherche la date actuelle 
				$date_actuelle_formate = date('d-m-Y' , time()) ; 
				
				$numero_facture = $info_commande['id'].'-'.$info_commande['numero_facture'] ;
				
				if(!empty($info_commande['date_paiement']))
				{
					$date_paiement_facture =  date('d-m-Y' , $info_commande['date_paiement']) ;
				}
				else
				{
					$date_paiement_facture = date('d-m-Y' , time()) ; 
				}
				
				// Permet de capturer dans le tampon tout ce qui suit jusqu'à ce qu'on appel ob_get_clean sans l'afficher
				ob_start() ;
				?>

				<style>
					table{width:100% ; border-collapse: collapse;}
					.titre_site{font-size:18px; font-weight:bold}
					.bold{font-weight: bold; color:#db302d}
					p{color:#333}
					
					.td_produit , .td_titre , .td_total{text-align:center; padding:5px;}
					.td_titre{font-weight:bold; background-color:#db302d;color:white ;  }
					.td_produit, .td_total{border:2px solid #db302d}
					.td_total{font-weight:bold}
					.td1{width:50%;}
					.td2{width:15%}
					.td3{width:20%}
					.td4{width:15%}
				</style>

				<page backtop="60px" backleft="60px" backright="60px" backbottom="60px">
					<page_header>
					</page_header>
					<table>
						<tr>
							<td >
								<img src="../../imgs/logo-donnemoifaim.png" alt="logo" />
								<span class="titre_site bold"><?php echo $nom_entreprise_site; ?></span>
								<br /><br />
								<p>
									<?php echo $proprietaire_site_dmf ; ?>
									<br />
									<?php echo $rue_site ; ?><br />
									<?php echo $code_postal_site.' '.strtoupper($ville_site) ; ?>
									<br />
									<?php echo $pays_site ; ?><br />
									SIRET <?php echo $siret_entreprise_dmf; ?>
									<br /><br />
									<span class="bold">Tel : <?php echo $numero_site_francais; ?></span><br />
									<span class="bold">Mail : <?php echo $mail_contact_site; ?></span>
								</p>
							</td>
							<td>
								<div style="margin-left:200px; ">
									<p>
										<span class="titre_site bold"><?php echo $info_commande['nomresto']; ?></span><br />
										<?php 
										// On à besoin de se formatage mais il faut sauter des lignes à la place des virgules
										echo str_replace(',' , '<br/>' , $info_commande['adresse_postal']) ; 
										?>
									</p>
									<p class="bold">
										<?php echo date('d-m-Y' , $info_commande['date_ajout']) ; ?>
									</p>
								</div>
							</td>
						</tr>
						<br /><br />
					</table>
					<br /><br /><br />
					<p>
						<span class="titre_site">Facture n° <?php echo $numero_facture; ?></span><br />
						<span>
							Facture créée le <?php echo $date_actuelle_formate; ?> || Réglée le <?php echo  $date_paiement_facture ;  ?>
						</span>
					</p>
					<br />
					<table>
						<tr>
							<td class="td1 td_titre">
								Désignation
							</td>
							<td class="td2 td_titre">
								Quantité
							</td>
							<td class="td3 td_titre">
								Prix unitaire
							</td>
							<td class="td4 td_titre">
								Montant
							</td>
						</tr>
						<tr>
							<td class="td_produit">
								<?php 
								if($info_commande['abonnement'] != 'abonnement')
								{?>
									Image mise en ligne pendant <?php echo $temps_abonnement_formule[$info_commande['abonnement']] ; ?> mois
								<?php
								}
								else
								{
									if($info_commande['nombre_image'] > 1)
									{
										$variable_s = 's' ; 
									}
									else
									{
										$variable_s = '' ; 
									}?>
									
									Abonnement mensuelle DMF <?php echo $info_commande['nombre_image'] ; ?> image<?php echo $variable_s ; ?>
								<?php
								}?>
							</td>
							<td class="td_produit">
								<?php
								// Si c'est un abonnement il n'y en a qu'un seul ! 
								if($info_commande['abonnement'] != 'abonnement')
								{
									$info_commande['nombre_image'] ;
								}
								else
								{
									echo 1 ; 
								}?>
							</td>
							<td class="td_produit">
								<?php echo $offre_formule[$info_commande['abonnement']] ; ?>
							</td>
							<td class="td_produit">
								<?php echo $offre_formule[$info_commande['abonnement']] * $info_commande['nombre_image'] ; ?>
							</td>
						</tr>
						<?php
						if(!empty($info_commande['options']))
						{
							// Numéro de facture doit ne doit pas etre le dexieme ou suppérieur sinon il reprend les options déjà payé
							// A faire des exceptions en cas d'options qui serait payé tout les mois
							if($info_commande['numero_facture'] < 2)
							{
								$tableau_options = explode('|-<>-|' , $info_commande['options']) ; 
								$nombre_options = count($tableau_options) ;
								
								for($i=0; $i < $nombre_options ; $i++)
								{
									// Si quand on explode on obtiens option reduction alors on fait ce qu'il faut
									$tableau_reduction = explode('|==|' , $tableau_options[$i] ) ;
									
									if($tableau_reduction[0] == 'reduction')
									{
										$nom_option = 'reduction' ;
									}
									else
									{
										$nom_option = $tableau_options[$i] ; 
									}
									?>
									<tr>
										<td class="td_produit">
											<?php echo $courte_description_options[$nom_option] ; ?>
										</td>
										<td class="td_produit">
											<?php echo $info_commande['nombre_image'] ?>
										</td>
										<td class="td_produit">
											<?php echo $offre_formule[$nom_option] ; ?>
										</td>
										<td class="td_produit">
											<?php echo $offre_formule[$nom_option] ; ?>
										</td>
									</tr>
									<?php
								}
							}
						}?>
					</table>
					<br /><br />
					<table style="margin-left:255px">
						<tr>
							<td style="width:15%" class="td_titre">
								Total
							</td>
							<td style="width:15%" class="td_titre">
								Total TTC
							</td>
							<td style="width:15%" class="td_titre">
								Déjà réglé
							</td>
							<td style="width:15%" class="td_titre">
								NET à payer
							</td>
						</tr>
						<tr>
						<?php
						// Si c'est un abonnement le prix à payer est premier mois
						if($info_commande['abonnement'] != 'abonnement')
						{
							$total = $info_commande['prix_total'] ; 
						}
						else
						{
							$total = $info_commande['prix_premier_mois'] ;
						}
						?>
							<td class="td_total">
								<?php echo $total ; ?> €
							</td>
							<td class="td_total">
								<?php echo $total ; ?> €
							</td>
							<td class="td_total">
								<?php echo $total ; ?> €
							</td>
							<td class="td_total bold">
								0 €
							</td>
						</tr>
					</table>
					<br />
					<p style="text-align:right">TVA non applicable, article 293 B du CGI</p>
					<page_footer>
					</page_footer>
				</page>

				<?php

				$contenu_pdf = ob_get_clean() ;

				include('../../html2pdf/html2pdf.class.php') ; 

				// P pour en paysage, A4 pour le format et fr pour francais
				$pdf = new HTML2PDF('p' , 'A4' , 'fr') ;
				$pdf->writeHTML($contenu_pdf) ; 
				// Permet de générer le pdf, mettre le nom voulu de la facture
				$pdf->Output($chemain_relatif_facture.'pdf/facture-'.$numero_facture.'.pdf', 'F');
				
				// Si il y à une variable de mail
				if(!empty($_GET['envoyer_mail_facture']) && $_GET['envoyer_mail_facture'] == 1 || !empty($envoyer_mail_facture) && $envoyer_mail_facture == 1)
				{
					$mail = $info_commande['mail']; // Déclaration de l'adresse de destination.
		
					$sujet = 'DonneMoiFaim : Facture '.$numero_facture.' du '.$date_paiement_facture ;
					
					
					$heure = date("H");

					//on cherche a savoir si c'est le soir ou la journée
					if($heure > 19)
					{
						$moment_journee = 'Bonsoir';
					}
					else
					{
						$moment_journee = 'Bonjour';
					}
					
					$message_txt = $moment_journee.' '.$info_commande['login'].',
					
					Voici votre facture du '.$date_paiement_facture.' de votre abonnement mensuel DMF.
					
					Vous pouvez également retrouver l\'ensemble de vos factures directement sur votre compte DonneMoiFaim. 

					L\'équipe DonneMoiFaim.' ; 

					$message_html = '
					<html>
						<head>
						<style>
							strong{color:#db302d}
							p{color:#333}
						</style>
						</head>
						<body>
							<p>
								'.$moment_journee.' '.$info_commande['login'].',<br /><br />
								Voici votre facture du '.$date_paiement_facture.' de votre abonnement mensuel DMF. <br /><br />

								Vous pouvez également retrouver l\'ensemble de vos factures directement sur votre compte DonneMoiFaim à l\'adresse : <br /><br />
								
								<a style="color:#db302d" href="'.$protocole_site.''.$_SERVER['HTTP_HOST'].'/compte-resto/factures.html">'.$protocole_site.''.$_SERVER['HTTP_HOST'].'/compte-resto/factures.html</a>
								<br /><br />
								
								<strong>L\'équipe de DonneMoiFaim.</strong>
								<a href="'.$protocole_site.''.$_SERVER['HTTP_HOST'].'"><img src="'.$protocole_site.''.$_SERVER['HTTP_HOST'].'/imgs/logo-donnemoifaim.png" alt="logo donnemoifaim" /></a>
							</p>
						</body>
					</html>';
		
					// Envoyer un mail
					envoi_mail($mail , $sujet, $message_txt, $message_html , $chemain_relatif_facture.'pdf/facture-'.$numero_facture.'.pdf') ;
				}
			}
			else
			{
				echo 'Accès facture interdit. En cas de problème contactez le support.' ; 
			}
		}
		else
		{
			echo 'Erreur id_commande' ;
		}
	}
	else
	{
		echo 'Id de la facture manquant.' ;
	}
}
else
{
	echo $erreur_connexion_texte ; 
}