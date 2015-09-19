<!-- inclusion du temps d'abonnement -->
<?php
include('include/offre_tarrif.php') ;
?>
<div id="bloc_abonnement_visiteur" class="bloc_apparait" style="display:none">
	<form target="_blank" name="formulaire_paiement_paypal" action="<?php if($_SERVER['HTTP_HOST'] == 'test.donnemoifaim.fr'){echo 'https://www.sandbox.paypal.com/cgi-bin/webscr' ;} else {echo 'https://www.paypal.com/cgi-bin/webscr' ; }?>" method="post" class="formulaire_offre">
		<p class="titre">Compte visiteur premium</p>
		
		<div class="bloc_offre" id="formulaire_premium1">
			<img width="100%" height="180px" src="imgs/abonnement-visiteur1.<?php echo versionning($fichier = 'imgs/abonnement-visiteur1.png'); ?>.png"/>
			<span class="description_offre" >Durée : <?php echo $temps_abonnement_formule['1'] ; ?> mois </span><br />
			<span class="prix_abonnement">Prix : <?php echo $offre_formule_visiteur['1'] ; ?>€</span>
			<br />
			<input id="offre_premium1" onchange="calcule_total_premium(1);" class="input_checkbox" type="radio" name="choix_formule" value="<?php echo $offre_formule_visiteur['1'] ; ?>" />
			<br /><br />
			<ul class="abonnement_visiteur_details">
				<li style="display:block" class="texte_site_noir">- Zéro Publicité ! <br /><img style="width:40px" src="/imgs/picto-class.<?php echo versionning('imgs/picto-class.png'); ?>.png" alt="picto reduction"></li><br />
				<li style="display:block" class="texte_site_noir">- Pouvoir accéder aux réductions DonneMoiFaim attribuées par les restaurateurs <br /><img style="width:40px" src="/imgs/bandeau-reduction.<?php echo versionning('imgs/bandeau-reduction.png'); ?>.png" alt="picto reduction"></li><br />
			</ul>
		</div>
	
		
		<div class="bloc_offre" id="formulaire_premium2">
			<img width="100%" height="180px" src="imgs/abonnement-visiteur2.<?php echo versionning($fichier = 'imgs/abonnement-visiteur2.png'); ?>.png"/>
			<span class="description_offre" >Durée : <?php echo $temps_abonnement_formule['2'] ; ?> mois </span><br />
			<span class="prix_abonnement">Prix : <?php echo $offre_formule_visiteur['2'] ; ?>€ </span>
			<br />
			<input id="offre_premium2" onchange="calcule_total_premium(2);" class="input_checkbox" type="radio" name="choix_formule" value="<?php echo $offre_formule_visiteur['2'] ; ?>" />
			<br /><br />
			<ul class="abonnement_visiteur_details">
				<li style="display:block" class="texte_site_noir">- Zéro Publicité ! <br /><img style="width:40px" src="/imgs/picto-class.<?php echo versionning('imgs/picto-class.png'); ?>.png" alt="picto reduction"></li><br />
				<li style="display:block" class="texte_site_noir">- Pouvoir accéder aux réductions DonneMoiFaim attribuées par les restaurateurs <br /><img style="width:40px" src="/imgs/bandeau-reduction.<?php echo versionning('imgs/bandeau-reduction.png'); ?>.png" alt="picto reduction"></li><br />
				<li style="display:block" class="texte_site_noir">- Privilèges lors des évènements DonneMoiFaim <br /><img src="/imgs/picto-evenement.<?php echo versionning('imgs/picto-evenement.png'); ?>.png" alt="picto plat"></li><br />
			</ul>
		</div>
		
		<div class="bloc_offre" id="formulaire_premium3">
			<img width="100%" height="180px" src="imgs/abonnement-visiteur3.<?php echo versionning($fichier = 'imgs/abonnement-visiteur3.png'); ?>.png"/>
			<span class="description_offre" >Durée : <?php echo $temps_abonnement_formule['3'] ; ?> mois </span><br />
			<span class="prix_abonnement">Prix : <?php echo $offre_formule_visiteur['3'] ; ?>€ </span>
			<br />
			<input id="offre_premium3" onchange="calcule_total_premium(3);" class="input_checkbox" type="radio" name="choix_formule" value="<?php echo $offre_formule_visiteur['3'] ; ?>" />
			<br /><br />
			<ul class="abonnement_visiteur_details">
				<li style="display:block" class="texte_site_noir">- Zéro Publicité ! <br /><img style="width:40px" src="/imgs/picto-class.<?php echo versionning('imgs/picto-class.png'); ?>.png" alt="picto reduction"></li><br />
				<li style="display:block" class="texte_site_noir">- Pouvoir accéder aux réductions DonneMoiFaim attribuées par les restaurateurs <br /><img style="width:40px" src="/imgs/bandeau-reduction.<?php echo versionning('imgs/bandeau-reduction.png'); ?>.png" alt="picto reduction"></li><br />
				<li style="display:block" class="texte_site_noir">- Privilèges lors des évènements DonneMoiFaim <br /><img src="/imgs/picto-evenement.<?php echo versionning('imgs/picto-evenement.png'); ?>.png" alt="picto plat"></li><br />
			</ul>
		</div>
		
		<!-- type de paiement -->
		<input name="cmd" type="hidden" value="_xclick" />
		<!-- Lien VPN -->
		<input name="notify_url" type="hidden" value="<?php echo $protocole_site.''.$_SERVER['HTTP_HOST']; ?>/paiement/trait_paiement.php" />
		<!-- Code Marque -->
		<input name="bn" type="hidden" value="Donnemoifaim_AutomaticBilling_FR" />
		<!-- Prix -->
		<input id="champ_prix_remplissage" name="amount" type='hidden' value="<?php echo $offre_formule_visiteur['1'] ;  ?>" />
		<!-- quantité -->
		<input id="champ_paypal_quantite" name="quantity" type='hidden' value="1"/>
		<!-- Devise -->
		<input name="currency_code" type="hidden" value="EUR" />
		<!-- Taxes --> 
		<input name="tax" type="hidden" value="0.00" />
		<!-- Page de retour une fois terminé -->
		<input name="return" type="hidden" value="<?php echo $protocole_site.''.$_SERVER['HTTP_HOST']; ?>/paiement/finalisation.html" />
		<!-- Page de retour si on n'annule -->
		<input name="cancel_return" type="hidden" value="<?php echo $protocole_site.''.$_SERVER['HTTP_HOST']; ?>/menu-gourmand.html" />
		<!-- Adresse ou envoyer l'argent -->
		<input name="business" type="hidden" value="<?php echo $adresse_mail_paiement; ?>" />
		<!-- Le nom de l'object -->
		<input id="champ_nom_remplissage" name="item_name" type="hidden" value="" />
		<!-- Pour qu'il est pas besoin de mettre de commentaire -->
		<input name="no_note" type="hidden" value="1" />
		<!-- Langue -->
		<input name="lc" type="hidden" value="FR" />
		<!-- ID de l'acheteur pour pouvoir récupérer le VPN avec séparateur pour récupérer plus vite -->
		<input id="champ_custum_remplissage" name="custom" type="hidden" value="" />
		<!--<h4><a href="" target="_blank" >Voir les conditions d'achat et de réglements</a></h4>-->
		<br /><br /><br />
		
		<div id="bloc_input_submit_form_visiteur_premium">
			<input class="input_submit reset_input" type="submit" value="Passer en premium">
			<img src="/imgs/chargement.gif" id="loading_envoi" alt="loading" width="20px" style="display:none; position:absolute" /><br />
			<br />
			<?php
				display_paiement_securise(); 
			?>
		</div>
	</form>
			
	<!-- bouton de fermeture de l'application -->
	<img id="retour_abonnement_visiteur" class="picto_retour" onclick="cache_bloc_coulissant('#bloc_abonnement_visiteur');" src="imgs/picto-retour.png" alt="picto retour" />
</div>