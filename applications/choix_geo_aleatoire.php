<?php
// Si le choix de l'utilisateur est le système aléatoire, hop c'est aléatoire
if(!empty($_SESSION['type_recherche']) AND $_SESSION['type_recherche'] == 'aleatoire')
{
	$picto_aleatoire = 'display:none' ; 
	$picto_aleatoire2 = '' ; 
	
	$picto_cible = '' ;
	$picto_cible2 = 'display:none' ;
}
else if(!empty($_SESSION['type_recherche']) AND $_SESSION['type_recherche'] == 'geocaliser')
{
	$picto_aleatoire  = '' ; 
	$picto_aleatoire2 = 'display:none' ; 
	
	$picto_cible2 = '' ;
	$picto_cible = 'display:none' ;
}?>

<div id="bloc_geo_aleatoire">
	<button id="button_cible" class="reset_button">Le plus proche</button>
	<img onclick="chargementimage('revenir_au_debut_geolocalisation') ; " style="<?php echo $picto_cible2; ?> ; cursor:pointer" id="picto_cible2" src="imgs/picto-cible.png" alt="cible de géolocalisation gps" />

	<img style="<?php echo $picto_cible; ?>" id="picto_cible" onclick=" if(history.pushState){connaitre_type_recherche(type = 'geocaliser');} else {<?php if(!empty($info_referencement_page)){ ?> window.location.href='menu-gourmand.php?geocalisation=active' ; <?php }else{ ?> connaitre_type_recherche(type = 'geocaliser'); <?php } ?> }" src="imgs/picto-cible2.png" onmouseover="this.src='imgs/picto-cible.png';" onmouseout="this.src='imgs/picto-cible2.png';" alt="cible de géolocalisation gps" />
	
	<!-- picto en cas d'attende de la géolocalisation -->
	<img onclick="faire_apparaitre_title_picto('picto_geocalisation_attente');" style="display:none" title="En attente de confirmation d'utiliser votre position pour la géolocalisation." id="picto_en_attente_geo" src="imgs/picto_attente.png" alt="en attente" />
	<span id="title_picto_geocalisation_attente" style="display:none; position:absolute; top:-30px; right:0px; width:40%" class="title_picto">En attente de confirmation d'utiliser votre position pour la géolocalisation.</span>
	
	<!-- picto en cas de blocage de la géolocalisation -->
	<img onclick="faire_apparaitre_title_picto('picto_geocalisation_non');" style="display:none" title="Pour utiliser cette fonctionnalité, vous devez accepter la géolocalisation dans votre navigateur et posséder un navigateur récent." id="picto_cible_hs" src="imgs/picto_stop.png" alt="stop" />
	<span id="title_picto_geocalisation_non" style="display:none; position:absolute; top:-100px; right:0px; width:40%" class="title_picto">Pour utiliser cette fonctionnalité, vous devez accepter la géolocalisation dans votre navigateur et posséder un navigateur récent.</span>
	
	<!-- séparateur entre les deux -->
	<span style="width:10px; display:inline-block"></span>

	<button id="button_aleatoire" class="reset_button">Aléatoire</button>
	<img style="<?php echo $picto_aleatoire2; ?>" id="picto_aleatoire2" src="imgs/picto_aleatoire.png" alt="dé à jouer aléatoire" />

	<img style="<?php echo $picto_aleatoire; ?>" id="picto_aleatoire" onclick="connaitre_type_recherche(type = 'aleatoire' , appel_chargement_image = 1);" src="imgs/picto_aleatoire2.png" onmouseover="this.src='imgs/picto_aleatoire.png';" onmouseout="this.src='imgs/picto_aleatoire2.png';" alt="dés à jouer aléatoire" />
</div>