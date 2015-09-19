<div id="compte_visiteur" class="bloc_apparait" style="display:none">
	<div id="bloc_connection_compte_visiteur" style="<?php if(!empty($_GET['renitialiser_mdp']))
		{echo 'display:none' ; } ?>" >
	<br />
		<p class="titre" >Connexion visiteur</p><br />
		<div class="bloc_contenu_centre">
			<div id="formconnection">
				<form onsubmit="connection_compte_visiteur();return false;" id="formulaire_compte_visiteur_connexion" action="ajax/compte_visiteur/connection_compte.php" method="POST">
					<button class="mdp_oublie" type="button" onclick="affichage_mdp_oublier();" >Mot de passe oublié</button>
					
					<label>Nom d'utilisateur ou adresse email :</label><br />
					<input class="input" id="login_compte_visiteur" type="text" placeholder="nom d'utilisateur" required/><br />
					
					<label>Mot de passe :</label><br />
					<input class="input" id="mdp_compte_visiteur" type="password" placeholder="mot de passe" required/><br /><br />
					
					<input id="connection_compte_visiteur_click" class="input_submit reset_input" type="submit" value="Se connecter" />
					
					<fb:login-button id="facebook_button_connection" style="float:right;" scope="public_profile,email" onlogin="checkLoginState('connect');"></fb:login-button>
				</form>
				<p class="bloc_creercompte">
					<button type="button" id="button_creation_compte" class="creercompte reset_button" onclick="affichage_creation_compte(); return false;" >Créez votre compte visiteur</button>
				</p>
				<br />
			</div>
		</div>
		<!-- bouton de fermeture de l'application -->
		<img class="picto_retour" onclick="cache_bloc_coulissant('#compte_visiteur');" src="imgs/picto-retour.png" alt="picto retour" />
	</div>
	<div id="bloc_creation_compte_visiteur" style="display:none">
		<br />
		<fb:login-button id="facebook_button_connection" size="large" scope="public_profile,email" onlogin="checkLoginState('connect');"> Inscription via facebook </fb:login-button>
		<p class="titre" >Création compte visiteur</p>

		<p class="texte_site_noir" style="text-align:center">* champs requis</p>
		<form onsubmit="creation_compte();return false" id="formulaire_compte_visiteur" action="ajax/compte_visiteur/verif_pseudo_visiteur.php" method="POST">
			<br />
			<label class="label_compte_visiteur" for="form_login_compte_visiteur">* Nom d'utilisateur : </label>
			<input onkeyup="verif_pseudo_utilisateur();" class="input" id="form_login_compte_visiteur" type="text" placeholder="6 caractères min alphanum" pattern="<?php echo $regex_pseudo = regex_pseudo(); ?>" required /> <span  class="verif_champ" id="verif_login_utilisateur"></span><br />
			
			<label class="label_compte_visiteur" for="form_mdp_compte_visiteur">* Mot de passe : </label>
			<input onkeyup="verif_champ(id_champ = this.id , taille_condition = 6) ;" class="input" id="form_mdp_compte_visiteur" type="password" pattern=".{6,}" placeholder="6 caractères min" required /> <span class="verif_champ" id="ok_form_mdp_compte_visiteur"></span><br />
			
			<label class="label_compte_visiteur" for="form_mdp_verif_compte_visiteur">* Vérif mot de passe : </label>
			<input onkeyup="verif_champ(id_champ = this.id , taille_condition = 'verif_mdp' , verif_mdp_comparaison = 'form_mdp_compte_visiteur') ;" class="input" id="form_mdp_verif_compte_visiteur" type="password" placeholder="6 caractères min" pattern=".{6,}" /> <span  class="verif_champ" id="ok_form_mdp_verif_compte_visiteur" ></span><br />
			
			<label class="label_compte_visiteur" for="form_ville_compte_visiteur">* Ville : </label>
			<input onkeyup="verif_champ(id_champ = this.id , taille_condition = '1') ;" class="input ui-autocomplete-input" id="form_ville_compte_visiteur" type="texte" placeholder="Votre ville" required /> <span  class="verif_champ" id="ok_form_ville_compte_visiteur"></span><br />
			
			<label class="label_compte_visiteur" for="form_mail_compte_visiteur">* Email : </label>
			<input onkeyup="verif_champ(id_champ = this.id , taille_condition = 'mail') ;" class="input" id="form_mail_compte_visiteur" type="email" placeholder="Votre mail" required /> <span  class="verif_champ" id="ok_form_mail_compte_visiteur"></span><br />
			
			<label class="label_compte_visiteur" for="form_code_promo_compte_visiteur"> Code promo (<?php echo $nombre_code_promo_inscription_compte_visiteur; ?> actifs) : </label>
			<input class="input" id="form_code_promo_compte_visiteur" type="text" placeholder="Ex: 77U7JK" /><br /><br />
			
			<label for="form_capatch_compte_visiteur" class="label_compte_visiteur">* Que représente l'image ci-contre ? </label>
			
			<?php
				// Tableau des valeurs possible du captacha
				$tableau_captacha = array('banane' , 'orange' , 'fraise' , 'cerise' , 'ananas', 'pomme') ; 
				// Création de la session du captcha
				$chiffre_aléatoire = rand(0 , 5) ;
				$_SESSION['captacha_compte_visiteur'] = $tableau_captacha[$chiffre_aléatoire] ; 
			?>
			<img src="imgs/capatch-verif-<?php echo $_SESSION['captacha_compte_visiteur']; ?>.png" alt="capatch" />
			<select id="form_capatch_compte_visiteur" class="input">
				<option value="">Choissisez</option>
				<option value="banane">Banane</option>
				<option value="orange">Orange</option>
				<option value="fraise">Fraise</option>
				<option value="cerise">Cerise</option>
				<option value="ananas">Ananas</option>
				<option value="pomme">Pomme</option>
			</select><br /><br />
			
			<label style="width:90%;" class="label_compte_visiteur" for="label_newsletter_visiteur">J'autorise DonneMoiFaim à m'avertir par email 1 fois par mois des réductions et nouvelles offres proches de ma ville et lors de certains évènements propres à DonneMoiFaim ( ex : concours, invitations, promotions de fête, etc.. ).</label><img src="imgs/picto-newsletter.<?php echo versionning('imgs/picto-newsletter.png') ; ?>.png" alt="picto newsletter" /><br />
			<input class="input_checkbox" id="label_newsletter_visiteur" type="checkbox" name="newsletter_check" checked />
			<br /><br /><br />
			
			<input class="input_submit reset_input" type="submit" value="Valider" />
			
		</form><br />
		<!-- bouton de fermeture de l'application -->
		<img class="picto_retour" onclick="retour_connection_compte();" src="imgs/picto-retour.png" alt="picto retour" />
	</div>
	<!-- bloc mot de passe oublié -->
	<div id="bloc_mdp_compte_visiteur" style="display:none" >
		<form onsubmit="envoyer_mdp_oublier() ; return false;" action="envoi_mdp_visiteur.php" method="GET">
			<p class="titre">Mot de passe oublié</p>
			<p class="texte_site_noir" >Saisissez votre email ci-dessous, un mail vous sera envoyé afin de réinitialiser votre mot de passe<br /></p>
			<input id="oubli_adresse_mail_visiteur" type="email" class="input" required /><br /><br />
			<input  id="envoimail" type="submit" class="input_submit reset_input" value="valider" /><br />
		</form>
		<!-- bouton de fermeture de l'application -->
		<img class="picto_retour" onclick="retour_connection_compte();" src="imgs/picto-retour.png" alt="picto retour" />
	</div>
	<!-- bloc rénitialisation mot de passe -->
	<div id="bloc_renitialisation_mdp" >
		<?php 
		if(!empty($_GET['renitialiser_mdp']))
		{?>
			<form onsubmit="renitialisation_mdp_visiteur(); return false" action="../menu-gourmand/renitialiser_mpd_visiteur.php" method="GET">
				<p class="titre">Réinitialiser votre mot de passe</p>
				<p class="texte_site_noir" >Saisissez votre nouveau mot de passe<br /></p>
				
				<label class="label_compte_visiteur" for="nouveau_mdp_visiteur">Mot de passe : </label>
				<input onkeyup="verif_champ(id_champ = this.id , taille_condition = 6) ;" class="input" id="nouveau_mdp_visiteur" type="password" placeholder="6 caractères min" required /> <span class="verif_formulaire_compte" id="ok_nouveau_mdp_visiteur"></span><br /><br />
				
				<input id="lien_unique_renitialisation" type="hidden" value="<?php echo $protocole_site.''.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ; ?>" />
				<input type="submit" class="input_submit reset_input" value="valider" /><br />
			</form>
			<!-- bouton de fermeture de l'application -->
			<img class="picto_retour" onclick="retour_connection_compte();" src="imgs/picto-retour.png" alt="picto retour" />
		<?php
		}?>
	</div>
</div>

<div id="notification_compte_visiteur" class="rappel_notification_compte texte_site_noir" style="display:none" >
	<span id="nombre_notification_compte_visiteur"></span><span id="texte_a_ecrire_bulle_notification"></span><br />
	<button onclick="$('#notification_compte_visiteur').fadeOut() ; " class="reset_button buttonEnfonce"> Ok </button>
</div>

<!-- div du compte courant -->
<div id="compte_visiteur_connecte_menu" class="bloc_apparait" style="display:none">	
	
	<p class="titre"> Compte visiteur </p>
	<!-- si la personne à un compte premium on ne fait pas apparaitre le bandeau --> 
	<p id="bandeau_compte_visiteur_statut_off" class="bandeau_promo" style="display:none">
		<span onclick="refermer_bandeau_promo('bandeau_compte_visiteur_statut');" class="petite_croix_fermer_bloc button_refermer_bandeau_promo"> x </span>
		<button style="color:white; cursor:pointer" class="reset_button" onclick="ouverture_abonnement_compte_visiteur_premium(function()
		{
			affichage_compte_visiteur();
		}) ;">
			Passez en premium<br />
			<img class="picto" src="/imgs/picto-premium-white.<?php echo versionning('imgs/picto-premium-white.png') ; ?>.png" alt="Premium" />
			<br />
			<span class="bloc_affichage_numeriques" style="padding:2px; font-size:20px" > Retour investissement garentie !</span>
		</button>
		<br />
	</p>
	<!-- bandeau dire qu'il a un compte premium -->
	<p id="bandeau_compte_visiteur_statut_on" class="bandeau_promo" style="display:none">
		<span onclick="refermer_bandeau_promo('bandeau_compte_visiteur_statut_on');" class="petite_croix_fermer_bloc button_refermer_bandeau_promo"> x </span>
			Vous êtes en premium !<br />
		<img src="/imgs/picto-premium-white.<?php echo versionning('imgs/picto-premium-white.png') ; ?>.png" alt="Premium" />
		<span class="bloc_affichage_numeriques" id="temps_abonnement_compte_visiteur_restant"></span>
	</p>
	<br />
	<ul>
		<li onclick="affichage_compte_visiteur();" class="choix_menu_compte_visiteur" >
			<img src="/imgs/picto-news.<?php echo versionning('imgs/picto-news.png') ; ?>.png" alt="Nouveauté site" /><br />
			Actus
		</li>
		<li onclick="affichage_mes_jaimes();" class="choix_menu_compte_visiteur">
			<img src="/imgs/picto-mes-jaimes.<?php echo versionning('imgs/picto-mes-jaimes.png') ; ?>.png" alt="Mes jaimes" /><br />
			Mes j'aimes
		</li>
		<li onclick="affichage_events();" class="choix_menu_compte_visiteur" >
			<img src="/imgs/picto-evenement.<?php echo versionning('imgs/picto-evenement.png') ; ?>.png" alt="Event DMF" /><br />
			Events
		</li>
		<li onclick="affichage_mes_points() ; " class="choix_menu_compte_visiteur" >
			<img src="/imgs/picto-points.<?php echo versionning('imgs/picto-points.png') ; ?>.png" alt="Mes points DMF" /><br />
			Mes points
		</li>
		<li onclick="deconnexion_compte_visiteur();"  class="choix_menu_compte_visiteur" >
			<img src="/imgs/picto-deconnexion.<?php echo versionning('imgs/picto-deconnexion.png') ; ?>.png" alt="Se deconnexion" /><br />
			Déconnexion
		</li>
	</ul>
	
	<div id="bloc_actu_site" class="bloc_apparait_menu_visiteur"></div>
	
	<!-- bloc pour voir les jaimes -->
	<div id="bloc_mes_jaimes" class="bloc_apparait_menu_visiteur" style="display:none"></div>
	
	<div id="bloc_event_dmf" class="bloc_apparait_menu_visiteur"></div>
	
	<!-- bloc pour voir les points -->
	<div id="bloc_mes_points" class="bloc_apparait_menu_visiteur" style="display:none"></div>
	
	<br /><br />
	<img class="picto_retour" onclick="cache_bloc_coulissant('#compte_visiteur_connecte_menu');" src="imgs/picto-retour.png" alt="picto retour" />
</div>

<!-- inscripton fb ajout de code promo -->

<div id="bloc_ajout_promo" class="bloc_demande_choix" style="display:none" >
	<span onclick="$('#bloc_ajout_promo').fadeOut() ; affichage_compte_visiteur(); " class="petite_croix_fermer_bloc">x</span>
	<br />
	<p style="color:white" class="titre"> Ajout code promo </p>
	<p>Si vous avez un code promo pour votre compte visiteur, saisissez le : </p>
	
	<input id="champ_input_ajout_code_promo" onkeyup="ajout_code_promo_visiteur(this.value); " type="text" class="input" placeholder="XXXXXX" maxlength="6" />
</div>