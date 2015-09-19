		<div id="autre_action" class="bloc_apparait" style="display:none">
				<!-- menu footer -->
				<div id="menufooter">
					<p class="titre">+ d'options</p><br /><br />
					<ul>
						<li class="choix_autre_choix" onclick="contact_footer();">Contactez-nous</li>
						<li><a class="choix_autre_choix" href="/compte-resto/vos-plats.php"  id="votrecompte" >Votre compte</a></li>
						<li class="choix_autre_choix" onclick="partenaire_footer();">Partenaires</li>
						<li class="choix_autre_choix" onclick="remerciement_footer();">Remerciements</li>
						<li class="choix_autre_choix" onclick="conditions_generales_footer();">Mentions légales</li>
					</ul>
					<img class="picto_retour" onclick="cache_bloc_coulissant(id_bloc = '#autre_action');" src="/imgs/picto-retour.png" alt="picto retour" />
				</div>
			<!-- conteneur principale qui ne change pas directement -->
			<div id="contenufooter">
				<!-- contact -->
				<div id="contact_footer" style="display:none">
					<p class="titre">Formulaire de contact</p>
					<br />
					<form id="formulaire_contact_envoi_mail_footer" onsubmit="envoi_mail(); return false;" method="POST" class="form-control" action="/ajax/envoi_mail/contact_traitement.php">
					
						<label for="prenom_nom_contact_footer" class="label_compte_visiteur">Nom & prénom :</label>
						<input class="input" id="prenom_nom_contact_footer" name="prenom_nom_contact_footer" type="text" />
						<br />
						
						<label for="mailcontact" class="label_compte_visiteur">Votre adresse email :</label>
						<input class="input" id="mailcontact" onkeyup="bonmail(mail_check = this.value);" name="mailcontact" type="email" />
						<span id="email_check"></span><br />
						
						<label  for="naturemail" class="label_compte_visiteur">Sujet :</label>
						<input class="input" id="naturemail" type="text" placeholder="Sujet du mail" name="naturemail" />
						<br />
						
						<label for="choixcontact" class="label_compte_visiteur">Type de demande :</label>
						<select class="input" name="choixcontact" id="choixcontact">
							<option value="probleme">Questions</option>
							<option value="service">Service client</option>
							<option value="probleme">Problème</option>
							<option value="partenariat">Partenariat</option>
							<option value="autre">Autres</option>
						</select><br /> <br /><br />
						
						<label for="contenu_mail" class="label_compte_visiteur">Contenu :</label>
						<br />
						<textarea style="width:80%; max-width:600px; text-align:left" class="input" id="contenu_mail" name="contenu_mail" placeholder="" rows="8" cols="100" ></textarea><br /><br />
						
						<input class="input_submit reset_input" type="submit" id="submitcontact" value="envoyer" >
					</form>
					<br /><br />
				</div>
				<!-- conditions d'utilisations -->
				<div class="texte_site" id="block_conditions_generales" style="display:none">
					<p class="titre" >Mentions légales</p>
					
					<!-- condition générale d'utilisation -->
					<p style="border-bottom:2px dashed #db302d">
						<a href="/legales/CGU.html" >Conditions générales d'utilisation</a><br />
						<a href="/legales/privacypolicy.htm">Privacy policy</a>
						<br /><br />
					</p>
					<p itemscope itemtype="http://data-vocabulary.org/Person">
						<span style="font-weight:bold">Lien du site</span><br />
						DonneMoiFaim : <span itemprop="url">http://<?php echo $_SERVER['HTTP_HOST']; ?></span>
						<br /><br />
						<span style="font-weight:bold">Concepteur</span><br />
						<span itemprop="name">Lourenco Kevin</span><br />
						<span itemprop="title">Application web</span> chez <?php echo $nom_entreprise_site ; ?><br />
						<span itemprop="address" itemscope itemtype="http://data-vocabulary.org/Address">
							<?php echo '<span itemprop="street-address">'.$rue_site.'</span> <span itemprop="locality">'.$ville_site.'</span>  <span itemprop="postal-code">'.$code_postal_site.'</span> <span itemprop="region">'.$region_site.'</span> <span itemprop="country-name">'.$pays_site.'</span>' ; ?><br />
						</span>
						<?php echo $numero_site ; ?><br /><br />
						<span style="font-weight:bold">Hebergeur</span><br />
						Planethoster<br />
						0176604143<br />
						4416 Louis B. Mayer Laval (Grand Montréal), Quebec H7P 0G1 Canada<br /><br />
						Numéro : <?php echo $numero_site; ?>
					</p>
					<!-- script permetant de dire a google notre numéro de téléphone, pratique pour resortir les résultats -->
					<script type="application/ld+json">
					{ "@context" : "http://schema.org",
					"@type" : "Organization",
					"url" : "http://<?php echo $_SERVER['HTTP_HOST']; ?>",
					"logo" : "http://<?php echo $_SERVER['HTTP_HOST']; ?>/imgs/logo-donnemoifaim.png",
					"contactPoint" : [
					{ "@type" : "ContactPoint",
					"telephone" : "<?php echo $contact_service_client ; ?>",
					"contactType" : "customer support"
					} ] }
					</script>
				</div>
				<!-- partenaires -->
				<div id="block_partenaire" style="display:none;">
					<p class="titre" >Liste de nos partenaires </p><br />
					
					<div class="bloc_partenaire">
						<a target="_blank" href="http://fr.freepik.com/photos-vecteurs-libre/fond"><img src="/imgs/partenaires/logo_freepick.png" alt="Vecteur de Fond conçu par Freepik" /></a>
					</div>
					<div class="bloc_partenaire">
						<a target="_blank" href="http://www.planethoster.net/fr/?a_aid=55edc6e063b17&amp;a_bid=cf0901ea"><img src="/imgs/partenaires/planethoster.gif" border="0" alt="Hébergé par PlanetHoster" title="Hébergé par PlanetHoster" width="234" height="60" /></a>
					</div>
				</div>
				<br />
				<!-- page de remerciement -->
				<div id="bloc_remerciement_footer">
					<?php include('remerciement.php') ; ?>
				</div>
				<img class="picto_retour" onclick="retour_menu();" src="/imgs/picto-retour.png" alt="picto retour" />
			</div>
		</div>
		<!-- autre div présente dans le header (main > wrapper) -->
	</div>
</div>
<?php
// Si c'est la page du menu gourmant on cache le gros footer
if(!empty($menu_gourmant))
{}
else
{
?>
	<footer>
		<br />
		<ul id="menu_footer_option">
			<li onclick="footer_apparaitre();contact_footer(); ">Contact - </li>
			<li onclick="footer_apparaitre();conditions_generales_footer(); ">Mentions légales - </li>
			<li onclick="footer_apparaitre();partenaire_footer(); ">Partenaires - </li>
			<li onclick="footer_apparaitre();remerciement_footer(); ">Remerciements</li>
		</ul>
		<a class="reset_input choix_autre_choix" href="/tous-les-plats.php">Tous les plats</a>
		<button id="button_footer_option" class="reset_input choix_autre_choix" onclick="footer_apparaitre();"> + d'options </button>
		<br /><br />
		<a href="https://www.facebook.com/donnemoifaim" target="_blank">
			<img class="picto_partage_reseaux" src="/imgs/picto-mini-facebook-partage.<?php echo versionning('imgs/picto-facebook-partage.png'); ?>.png" alt="Partage facebook DMF" />
		</a>
		<a href="https://twitter.com/donnemoifaim" target="_blank">
			<img class="picto_partage_reseaux" src="/imgs/picto-mini-twitter-partage.<?php echo versionning($fichier = 'imgs/picto-twitter-partage.png'); ?>.png" alt="Partage twitter DMF" />
		</a>
		<a href="https://plus.google.com/u/0/112168710991364953496" target="_blank">
			<img class="picto_partage_reseaux" src="/imgs/picto-mini-google-plus-partage.<?php echo versionning('imgs/picto-google-plus-partage.png'); ?>.png" alt="Partage google + DMF" />
		</a><br />
		<p id="donnees_organisation" itemscope itemtype="http://data-vocabulary.org/Organization">
			<span itemprop="name"><?php echo $nom_entreprise_site; ?></span> 
			<span itemprop="address" itemscope 
			itemtype="http://data-vocabulary.org/Address">
			<span itemprop="street-address"><?php echo $rue_site ; ?></span>, 
			<span itemprop="locality"><?php echo $ville_site ; ?></span>, 
			<span itemprop="region"><?php echo $region_site ; ?></span>.
			</span>
			<span itemprop="tel"><?php echo $contact_service_client ; ?></span>.
			<span itemprop="url">http://<?php echo $_SERVER['HTTP_HOST']; ?></span>.
		</p><br />
		<a href="http://www.planethoster.net/fr/?a_aid=55edc6e063b17&amp;a_bid=de9a8992"><img src="/imgs/partenaires/planethoster-miniature.gif" border="0" alt="Hébergé par PlanetHoster" title="Hébergé par PlanetHoster" width="88" height="31" /></a>
		<span id="copyright">&copy;DonneMoiFaim <?php echo date('Y' , time()); ?> Tous droits réservés.</span>
	</footer>
<?php
}
// requête recherchant les villes pour avoir un tableau utilisable en jquery
$reponse_autocompletion = $bdd->query("SELECT c.ville ville FROM client c INNER JOIN plat p ON p.login = c.login GROUP BY c.ville");

while ($donnees = $reponse_autocompletion->fetch())
{

	$ville_auto[] = '\''.$donnees['ville'].'\','; 

}
$reponse_autocompletion->closeCursor(); // Termine le traitement de la requête
?>

<!-- insertion du code jquery -->
<script async src="/javascript/jquery.<?php echo versionning('javascript/jquery.js'); ?>.js"></script>

<script>

// Quand tout est chargé
if(typeof(addEventListener) != 'undefined')
{
	window.addEventListener("load", loadGlabalJqueryUi, false) ;
}
else
{
	window.attachEvent("load", loadGlabalJqueryUi) ;
}

function loadGlabalJqueryUi()
{
	loadJqueryUi() ; 
		
	// Asyncronous laod
	function loadJqueryUi()
	{
		// Chargement de jquery ui
		loadScript('/javascript/jquery-ui-1.10.4.custom.min.js', loadAutocompletion) ;
	}

	// Quand jquery ui est chargé on peut balancer ses applications
	function loadAutocompletion()
	{
		var availableTags = [
			<?php foreach($ville_auto as $ville_auto) { echo $ville_auto ; }?>

		];
		$( ".recherche_ville" ).autocomplete({
			source: availableTags
		})
	} ;
}
</script> 

<!-- pour faire fonctionner facebook -->
<div id="fb-root"></div>