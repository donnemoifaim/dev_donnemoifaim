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
		$meta_titre = 'Démarcharge commercial' ; 
		$meta_description = 'Aide dans la démarche commercial sous forme de parcour à valider' ;
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
			?>
				<p class="titre">Démarcharge commercial</p>
				<button onclick="faire_disparaitre_bloc_et_apparaitre_autre_simple('.etape_commercial' , '#etape_commercial1'); " class="reset_button choix_autre_choix">Préparatifs</button>
				<button onclick="faire_disparaitre_bloc_et_apparaitre_autre_simple('.etape_commercial' , '#etape_commercial2'); " class="reset_button choix_autre_choix">Processus</button>
				<button onclick="faire_disparaitre_bloc_et_apparaitre_autre_simple('.etape_commercial' , '#etape_commercial3'); " class="reset_button choix_autre_choix">Discour commercial</button>
				<button onclick="faire_disparaitre_bloc_et_apparaitre_autre_simple('.etape_commercial' , '#etape_commercial4'); " class="reset_button choix_autre_choix">offres et options en +</button>
				
				<br /><br />
				<section id="etape_commercial1" class="etape_commercial">
					<h2 class="etape_commercial_admin" >Les préparatifs</h2><br />
					<p class="texte_site"> Avant d'allez voir les restaurateurs tu auras besoin au minimum de : <br /><br /></p>
					<p class="bloc_image_legende_responsive">
						<img src="../imgs/carte-visite-verso.<?php echo versionning('imgs/carte-visite-verso.png') ; ?>.png" alt="carte visite DMF verso" /><br />
						De cartes de visites DMF
					</p>
					<p class="bloc_image_legende_responsive">
						<img src="../imgs/nexus-5.<?php echo versionning('imgs/nexus-5.png') ; ?>.png" alt="nexus 5" /><br />
						Un téléphone CHARGE
					</p>
					<p class="bloc_image_legende_responsive">
						<img src="../imgs/chargueur-portatif.<?php echo versionning('imgs/chargueur-portatif.png') ; ?>.png" alt="batterie portative" /><br />
						Un chargueur + batterie portative si possible
					</p>
					<p class="bloc_image_legende_responsive">
						<img src="../imgs/calpin-prospection.<?php echo versionning('imgs/calpin-prospection.png') ; ?>.png" alt="Calpin" /><br />
						Un calpin (si jamais) + stylos
					</p>
					<p class="texte_site">
					<br /><br />
						Pour la tenue, smoking INTERDIT ! Notre marque de fabrique : décontracté et naturel. <br /><br />
						<img src="../imgs/model-homme-prospection.<?php echo versionning('imgs/model-homme-prospection.png') ; ?>.png" alt="model homme prospection" />
					</p>
					<br /><br />
				</section>
				<section id="etape_commercial2" class="etape_commercial" style="display:none">
					<h2 class="etape_commercial_admin" >Processus</h2><br />
					<p class="texte_site"> Voici le déroulement basique d'une prospection DMF : </p>
					<p>
						Le restaurateur à t'il déjà été fait ? Si oui, reste-il des choses à voir avec le responsable ? <br /><br />
						Va dans la <a href="client-resto.php">liste des clients</a> resto pour avoir ces renseignements.
						<br /><br />
						<img src="../imgs/mystere-client-fait.<?php echo versionning('imgs/mystere-client-fait.jpg') ; ?>.jpg" alt="Mystere client fait ou non" />
						<br /><br />
						Si le restaurateur à déjà été fait, vérifie qu'il ne reste pas des choses à faire au niveau des offres complémentaires et options. <br />
					</p>
					<p>
						<span class="texte_site">Si le restaurateur n'a pas été fait, on prend une photo de la façade avec son téléphone.</span><br /><br />
						<img src="../imgs/appareil-photo-facade-commercial.<?php echo versionning('imgs/appareil-photo-facade-commercial.jpg') ; ?>.jpg" alt="Appareil photo facade" />
					</p>
					<p>
					<br />
						<span class="texte_site">Demande à parler au responsable, si ce n'est pas lui-meme.</span><br /><br >
						Durant la discution ne dérange pas le client s'il est vraiment occupé, quitte à repasser + tard. Ne fait pas comme Bob ! 
						<br /><br />
						<img src="../imgs/bob-leponge-chieur.<?php echo versionning('imgs/bob-leponge-chieur.jpg') ; ?>.jpg" alt="bob leponge chieur" />
					</p>
					<p>
					<br />
						Impreigne toi le fameux discours commercial pour t'aider et te souvenir des principaux points à mettre en avant.
						<br /><br />
						<span class="texte_site">Reste naturel et fais le avec tes propres mots en t'adaptant à ton interlocuteur. </span><br /><br />
						<img src="../imgs/discour-commercial-dessin.<?php echo versionning('imgs/discour-commercial-dessin.jpg') ; ?>.jpg" alt="discour commercial dessin" />
					</p>
					<p>
					<br />
						En fonction de la disponibilité du restaurateur <span class="texte_site">créér son compte resto</span> si cela n'est pas encore fait en te déconnectant et créant un compte. <br /><br />
						Complète les infos complémentaires en priorité, car la photo de la façade peut prendre du temps à upload. <br /><br />
						<img src="../imgs/creer-compte-dmf-commerciaux.<?php echo versionning('imgs/creer-compte-dmf-commerciaux.png') ; ?>.png" alt="créer compte dmf" />
					</p>
					<p>
						<br />
						<span class="texte_site">Ne pas oublier de proposer éventuellement les options et offres supplémentaires. C'est la dessus que l'on peut se faire à terme beaucoup d'argent.</span><br /><br />
						<img src="../imgs/gagnerargentcommerciaux.<?php echo versionning('imgs/gagnerargentcommerciaux.png') ; ?>.png" alt="gagner de l'argent" />
					</p>
					<p>
						<br />
						Donne une carte de visite à la fin si ce n'est pas déjà fait. Sort ton plus beau sourire formule de politesse que tu préfères pour clore en beauté la discution : poli et sexy ! <br /><br />
						<img src="../imgs/sourire-bebe-commerciale.<?php echo versionning('imgs/sourire-bebe-commerciale.jpg') ; ?>.jpg" alt="sourire de bebe comemrcial" />
					</p>
					<br /><br />
				</section>
				<section id="etape_commercial3" class="etape_commercial" style="display:none">
					<h2 class="etape_commercial_admin" >Discour commercial</h2><br />
					<p class="texte_site"> Voici la liste des points essentiel à retenir : </p>
					<br />
					<ul>
						<li>- Une image gratuite pendant 6 mois (voir 1 mois celon les villes) pour qu'il test</li><br /> <br />
						<li>- + Vous avez d'image + vous aurez une visibilité et de nouveaux clients </li><br /> <br />
						<li>- Pour les personnes qui sont forcément des clients potentiels puisque c'est des personnes qui ont faim </li><br /> <br />
						<li>- Montrer les stats pour preuves à l'appuie </li><br /> <br />
						<li>- Pour les retomber qui peuvent etre énorme ce n'est pas cher dutout ! </li><br /> <br />
						<li>- l'occasion Profiter d'une présence sur le web ans pour autant avoir un site internet </li><br /><br />
						<li>- Leur dire que l'on fait également des évènements pour leur ramener un max de monde </li><br /> <br />
					</ul>
				</section>
				<section id="etape_commercial4" class="etape_commercial" style="display:none">
					<h2 class="etape_commercial_admin" >Options et complémentaire</h2><br />
					<p class="texte_site"> A ne pas oublier : </p>
					<br />
					<p class="bloc_image_legende_responsive">
						<img src="../imgs/option_reduction.<?php echo versionning('imgs/option_reduction.png') ; ?>.png" alt="bandeau réduction" /><br />
						Ajouter une réduction pour insiter les gens à venir
					</p>
					<p class="bloc_image_legende_responsive">
						<img src="../imgs/appareil-photo-facade-commercial.<?php echo versionning('imgs/appareil-photo-facade-commercial.jpg') ; ?>.jpg" alt="Appareil photo facade" />
						<br />Mettre la façade du resto 
					</p>
					<p class="bloc_image_legende_responsive">
						<img src="../imgs/formulaire-compte-resto.<?php echo versionning('imgs/formulaire-compte-resto.jpg') ; ?>.jpg" alt="check formulaire" /><br />
						Remplir les informations complémentaires resto
					</p>
					<p class="bloc_image_legende_responsive">
						<img src="../imgs/option_facebook.<?php echo versionning('imgs/option_facebook.png') ; ?>.png" alt="option facebook" /><br />
						Proposer post facebook à 5€ en + et boost publication
					</p>
					<p class="bloc_image_legende_responsive">
						<img src="../imgs/evenement-dmf-commercial.<?php echo versionning('imgs/evenement-dmf-commercial.jpg') ; ?>.jpg" alt="Evenement dmf" /><br />
						Evènement DMF
					</p>
				</section>
			</div>
			<br /><br />
		<!-- insertion du footer -->
		<?php include('../include/footer.php'); ?>
		
		<script async type="text/javascript" src="/javascript/general.<?php echo versionning('javascript/general.js'); ?>.js"></script>
		<script async type="text/javascript" src="../javascript/admin.<?php echo versionning('javascript/admin.js'); ?>.js"></script>
	</body>
</html>