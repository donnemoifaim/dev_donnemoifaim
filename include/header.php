<!-- bloc si on veut faire un message ou une action en pleine ecran-->
<div id="bloc_full_screen" style="display:none">
	<div id="contenu_bloc_full_screen">
	</div>
</div>
<!-- bloc full pour pouvoir fermer une div ouverte de class -->
<div id="bloc_full_click" onclick="faire_disparaitre_bloc_apparait()" style="display:none"></div>

<div id="wrap">
	<a id="logo" href="<?php if( page_actuelle() == '/index.html'){echo  '/menu-gourmand.html' ; } else{echo '/index.html' ;} ?>"><img src="/imgs/logo-donnemoifaim.<?php echo versionning('imgs/logo-donnemoifaim.png') ; ?>.png" alt="DonneMoiFaim" /></a>
	<!-- le header en  fixed, pour calculer toutes les tailles correctement on simule un bloc de la meme hauteur -->
	<div id="bloc_page_0" style="height:50px"></div>
	<header style="overflow:hidden; height:50px">
		<nav id="bloc_contenu_header" style="min-height:50px">
			<img onclick="apparaitre_menu_responsive();" id="button_menu_responsive" src="/imgs/button_menu_responsive.png" alt="menu_responsive" />
			<!-- menu gourmant si c'est deja le menu gourmant on enleve ce lien -->
			<?php
			if(page_actuelle() == '/menu-gourmand.html' || !empty($image_cible_referencement) || !empty($_GET['ouverture_compte_visiteur']))
			{?>
				<li id="lien_vers_compte_visiteur">
					<a onclick="affichage_compte_visiteur(); return false;" class="menu_header"> 
						<span id="nom_proprietaire_compte_visiteur"><?php if(!empty($_SESSION['login_visiteur'])){echo $_SESSION['login_visiteur']; } else { echo 'Visiteur' ; } ?></span>
						<img src="/imgs/picto-compte-visiteur.<?php echo versionning('imgs/picto-compte-visiteur.png') ; ?>.png" class="picto_header" alt="Compte visiteur" />
						
						<?php
						$notification_compte = compter_nombre_notification_compte_visiteur() ;

						if($notification_compte != 0)
						{
						?>
							<span id="bulle_notification_compte_visiteur" class="notification_compte"><?php echo $notification_compte; ?></span>
						<?php
						}?>
					</a> 
				</li>
			<?php
			}
			else
			{?>
				<li id="lien_menu_gourmant">
					<a href="/menu-gourmand.html" class="menu_header"> DonneMoiFaim 
						<img src="/imgs/picto-couvercle-plat.png" class="picto_header" alt="DonneMoiFaim" />
					</a>
				</li>
			<?php
			}
			?>
			<!-- Ajouter plats -->
			<li id="lien_ajout">
				<a href="/annuaire/"> Restos/réductions 
					<img src="/imgs/picto-ajout.png" class="picto_header" alt="Ajoutez vos plats" />
				</a>
			</li>
			
			<!-- Lien contact -->
			<li id="lien_contact">
				<span onclick="footer_apparaitre();contact_footer(); ">Contact
					<img src="/imgs/picto-contact.png" class="picto_header" alt="Nous contacter" />
				</span>
			</li>
			<li id="formulaire_recherche_ville">
				<!-- formulaire recherche ville --> 
				<form style="margin:0" onsubmit="chargementimage(); <?php if(page_actuelle() == '/menu-gourmand.html' || !empty($image_cible_referencement)){ echo 'return false' ;} ?>;" method="GET" action="/menu-gourmand.php"  enctype="multipart/form-data"> 
					<input class="recherche_ville" style="border-radius:3px" id="recherche" placeholder="Recherche par ville" name="ville"  value="<?php if (!empty($_GET['ville'])) { echo htmlspecialchars($_GET['ville']); }?>" />
					<input class="submit_recherche"  id="submit_recherche" type="image" src="/imgs/loupe_recherche.png" alt="Recherche approfondie" />
					<img id="supp_recherche_valide" onclick="supp_recherche();" src="/imgs/bouton_fermer.png" style="display:none;" alt="fermer la recherche" /><!--
					--><span onclick="supp_recherche();" type="button" id="recherche_en_cours" style="display:none" class="recherche_valide" ></span>
				</form>
			</li>
			<!-- compte resto -->
			<li id="lien_connection">
				<?php
				if(!empty($_SESSION['login']))
				{?>
				<a href="/connection-compte.html" class="menu_header"><?php echo $_SESSION['login']; ?>
					<img src="/imgs/picto-compte-resto.<?php echo versionning('imgs/picto-compte-resto.png'); ?>.png" class="picto_header" alt="compte resto" />
				</a>
				<?php
				}
				else
				{?>
					<a href="/connection-compte.html" class="menu_header">Compte resto
						<img src="/imgs/picto-compte-resto.<?php echo versionning('imgs/picto-compte-resto.png'); ?>.png" class="picto_header" alt="compte resto" />
					</a>
				<?php
				}?>
			</li>
		</nav>
	</header>
	<script>
		// Si on recherche une image alors on fait en sorte que le clique aille dans menu-gourmand si hmlt history existe pas
		if(history.pushState)
		{
			// Impossible implémenter dans le html directement a cause de la variable type..
			document.getElementById('submit_recherche').onclick = function(){connaitre_type_recherche(type = 'aleatoire' , appel_chargement_image = 1 ); return false;};
		}
		else
		{
			<?php
			if(!empty($image_cible_referencement))
			{}
			else
			{?>
				// Impossible implémenter dans le html directement a cause de la variable type..
				document.getElementById('submit_recherche').onclick = function(){connaitre_type_recherche(type = 'aleatoire' , appel_chargement_image = 1 ); return false;};
			<?php
			}?>
		}
	</script>
	
	<!-- pour la compatibilite ie il faut pas que ce soit le noscript qui est stylisé -->
	<noscript>
		<p id="noscript">Javascript est désactivé, veuillez l'activer pour faire fonctionner l'application.</p>
	</noscript>
	<script>
		// Si c'est un vieux navigateur style ie8 on éssais de faire la compatibilite mais on demande à faire une mise à jour quand meme
		if(!history.pushState)
		{
			document.write('<span id="navigateur_ancien_non_compatible">Votre navigateur est assez ancien, nous vous conseillons de faire une mise à jour pour profiter de l\'ensemble des fonctionnalités et corriger les beugs ! </span>') ;
		}
	</script>
	<div id="main" style="<?php if(page_actuelle() == '/' OR page_actuelle() == '/index.html'){echo 'padding:0' ; } ?>">
	
	<!-- chargement quand il n 'y a pas d'image -->
	<div style="display:none" id="bloc_chargement_action"></div>