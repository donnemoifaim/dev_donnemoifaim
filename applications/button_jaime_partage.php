<div id="bloc_jaime_partage">
	<p id="bloc_partage_plat_categorie">
		<button id="texte_partage_button" class="reset_button" >Partager</button>
		<img id="picto_partager_plat" onclick="voir_bloc_coulissant(id_bloc = '#bloc_partage_reseaux_sociaux');" class="picto_partage" src="imgs/picto-partage.<?php echo versionning('imgs/picto-partage.png') ; ?>.png" alt="button de partage" />
	</p>
	
	
	<?php
		if(!empty($resultat_jaime['deja_jaime_vote']))
		{
			$display_jaime = 'none' ; 
			$display_deja_vote_jaime = 'inline-block' ;
		}
		else
		{
			$display_jaime = 'inline-block' ; 
			$display_deja_vote_jaime = 'none' ;
		}
	?>
	
	<p id="bloc_jaime_plat_categorie">
		<button id="button_jaime_plat" class="reset_button">J'aime</button>
		<img id="picto_jaime_plat" style="display:<?php echo $display_jaime; ?>" onclick="jaime_plat();" class="picto_jaime" src="imgs/picto-jaime.<?php echo versionning('imgs/picto-jaime.png') ; ?>.png" alt="button j'aime" />
		<img style="display:<?php echo $display_deja_vote_jaime; ?>" class="picto_jaime_deja_vote" src="imgs/picto-jaime.<?php echo versionning('imgs/picto-jaime.png') ; ?>.png" alt="button j'aime" />
		<span class="texte_site" id="nombre_jaime_plat" style="width:50px"><?php  if (!empty($info_referencement_page)){ echo $nombre_jaime;} ?></span>
	</p>
	<!-- permet de récupérer le résultat de l'ajax savoir si le compte est co ou pas -->
	<div id="retour_ajax_jaime" style="display:none"></div>
</div>
