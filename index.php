<?php session_start() ; ?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php
		$meta_titre = 'Trouver un restaurant - réductions resto - DMF' ; 
		$meta_description = 'Trouvez le restaurant qui fera votre bonheur en découvrant de délicieux plats géolocalisés issus de restaurateurs divers et variés. Profitez également de réduction sur la plupart d\'entre eux !' ;
		$meta_keywords = 'faim, plat, Plats, restaurant,aléatoire,restaurant, menu, buffet, gourmand'  ;
		//Savoir si on référence la page
		$meta_robots = 'index' ;
		include('include/en-tete.php') ; 
		?>
		<!-- google vérification que c'est notre site -->
		<meta name="google-site-verification" content="ACyn_SsTPcmQTNFI_tdJv2cMUa7_C796_gRCeCuU5Us" />
	</head> 
	<body style="overflow:hidden;">
			<?php 
			include('include/header.php')  ;  
			?>
			<div id="conteneur_puce">
				<button id="puce_1" style="color:white; background-color:#db302d" class="puce_menu reset_button" onclick="button_scroll_page(cible_scroll = 1);">1</button>
				<button id="puce_2" class="puce_menu reset_button" onclick="button_scroll_page(cible_scroll = 2);">2</button>
				<button id="puce_3" class="puce_menu reset_button" onclick="button_scroll_page(cible_scroll = 3);">3</button>
				<button id="puce_4" class="puce_menu reset_button" onclick="button_scroll_page(cible_scroll = 4);">4</button>
				<button id="puce_5" class="puce_menu reset_button" onclick="button_scroll_page(cible_scroll = 5);">5</button>
			</div>
			
			<?php
				// On va créer un tableau que l'on va trier aléatoirement pour que les images s'affiche dans un ordre aléatoire
				$tableau_image_fond = array('photo_acceuil_1' , 'photo_acceuil_2' , 'photo_acceuil_3' , 'photo_acceuil_4') ;
				
				// On tri aléatoirement le tableau
				shuffle($tableau_image_fond) ;  
			?>
			 <!-- fonctionnalité 1 -->
			<section id="bloc_page_1" class="fonctionnalite" style="background-image:url('imgs/<?php echo $tableau_image_fond[0] ; ?>.<?php echo versionning('imgs/'.$tableau_image_fond[0].'.jpg') ; ?>.jpg'); filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='imgs/<?php echo $tableau_image_fond[0] ; ?>.<?php echo versionning('imgs/'.$tableau_image_fond[0].'.jpg'); ?>.jpg',sizingMethod='scale'); padding-top:60px">
			<!-- on met le principal titre ici -->
				<div id="div_contenu_index_1" class="div_contenu_index_visible">
					<h1 class="titre_fonctionnalite" >
						<strong>Trouver un restaurant</strong> qui donne faim  et profitez de <strong>reductions</strong> !
					</h1>
					 <p class="complement_titre_index">
						Notre application génère des photos de <strong>repas aléatoires ou géocalisés</strong> issus de <strong>restaurants</strong>, commerçants divers et variés.
					 </p>
					 <p>
						Viens l'heure de la <span class="bold">pause déjeuner</span> vous ne savez pas <strong>quoi manger</strong> ? Vous voulez varier les plaisirs et tester des <strong>plats originaux </strong> ? DonneMoiFaim a pour principal objectif de vous faire découvrir de <strong>bons plats</strong> à proximité.<br />
					 </p>
					 <a  id="lien_fonctionnalite_index_1" class="donnemoifaim_button_index" href="menu-gourmand.html">Accéder aux plats</a>
				</div>
			</section>
			<!-- fonctionnalité 2 -->
			<section id="bloc_page_2" class="fonctionnalite" style="background-image:url('imgs/<?php echo $tableau_image_fond[1] ; ?>.<?php echo versionning('imgs/'.$tableau_image_fond[1].'.jpg') ; ?>.jpg'); filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='imgs/<?php echo $tableau_image_fond[1] ; ?>.<?php echo versionning( 'imgs/'.$tableau_image_fond[1].'.jpg'); ?>.jpg',sizingMethod='scale');">
				 <div id="div_contenu_index_2" class="div_contenu_index_visible">
					<h2 class="titre_fonctionnalite">
						Trouver un <strong>restaurant original</strong> et de qualité
					</h2>
					<p class="complement_titre_index">
						Se donner faim est une chose, encore faut-il <span class="bold">la combler</span> !
					</p>
					<p>
						DonneMoiFaim vous propose de trouver un <strong>restaurant dans le coin</strong> : de l'italien au japonais en passant par le restaurant traditionnel, les <strong>restos</strong> partenaires sont très variés ! 
					</p>
					<a id="lien_fonctionnalite_index_2" style="display:none" class="donnemoifaim_button_index" href="menu-gourmand.html">Rechercher un resto</a>
				</div>
			</section>
			<!-- fonctionnalité 3 -->
			<section id="bloc_page_3" class="fonctionnalite" style="background-image:url('imgs/<?php echo $tableau_image_fond[2] ; ?>.<?php echo versionning('imgs/'.$tableau_image_fond[2].'.jpg') ; ?>.jpg'); filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='imgs/<?php echo $tableau_image_fond[2]; ?>.<?php echo versionning( 'imgs/'.$tableau_image_fond[2].'.jpg'); ?>.jpg',sizingMethod='scale');">
				 <div id="div_contenu_index_3" class="div_contenu_index_visible">
					<h2 class="titre_fonctionnalite">
						<strong>Réduction restaurant</strong> : manger malin
					</h2>
					<p class="complement_titre_index">
						La devise : <span class="bold">manger mieux et moins cher</span> ! En utilisant pleinement DonneMoiFaim, vous aurez accès aux <strong>réductions DMF</strong> et à une carte spéciale <span class="bold">1 repas acheté, 1 repas offert.</span>
					</p>
					<p>
						L'accès aux réductions requiert un <span class="bold">compte visiteur premium</span>. Les réductions sont souvent très avantageuses (remise sur l'addition, apéritif offert,etc...) et vite rentabilisés !
					</p>
					<a id="lien_fonctionnalite_index_3" style="display:none" class="donnemoifaim_button_index" href="annuaire/">Explorer les réductions</a>
				 </div>
			</section>
			<!-- fonctionnalité 4 -->
			<section id="bloc_page_4"  class="fonctionnalite" style="background-image:url('imgs/<?php echo $tableau_image_fond[3] ; ?>.<?php echo versionning('imgs/'.$tableau_image_fond[3].'.jpg') ; ?>.jpg'); filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='imgs/<?php echo $tableau_image_fond[3]; ?>.<?php echo versionning('imgs/'.$tableau_image_fond[3].'.jpg'); ?>.jpg',sizingMethod='scale');">
				 <div id="div_contenu_index_4" class="div_contenu_index_visible">
					<h2 class="titre_fonctionnalite"> 
						Restaurants et commerçants : pouvoir profiter d'une clientèle fidèle et qui a faim !
					</h2>
					<p class="complement_titre_index">
						Vous pouvez mettre en ligne, puis gérer les photographies des préparations qui font la fierté de votre <strong>restaurant</strong>  ou de votre <span class="bold">commerce,</span> facilement et rapidement. 
					</p>
					<p>
						Votre première image est <span class="bold">offerte</span> pour chaque nouveau compte resto créé : <span class="bold">profitez-en</span> ! 
					</p>
					<a id="lien_fonctionnalite_index_4" style="display:none" class="donnemoifaim_button_index" href="connection-compte.html">Inscription compte resto</a>
				 </div>
			</section>
			<div id="bloc_page_5"></div>
				<!-- insertion du footer -->
				<?php include('include/footer.php'); ?>				
		<!-- insertion du javascript nécessaire -->
		<script async src="javascript/general.<?php echo versionning($fichier = 'javascript/general.js'); ?>.js"></script>
		<script async src="javascript/index.<?php echo versionning($fichier = 'javascript/index.js'); ?>.js"></script>
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			ga('create', 'UA-48386004-1', 'donnemoifaim.fr');
			ga('send', 'pageview');
		</script>
	</body>
</html>