<?php

session_start() ; 

//connection à la bdd
include ('../../include/configcache.php') ;
include ('../../include/fonctions.php');

// On vérifie si il est connecté 
if(!empty($_SESSION['login_visiteur']))
{
	if(!empty($_SESSION['id_visiteur']) && is_numeric($_SESSION['id_visiteur']))
	{
		// On récupère les events pour les afficher
		$requete_events = $bdd->query('SELECT intitule, contenu, image_event, url_coupon, ville, date_ajout FROM events WHERE statut = 1') ; 

		while($info_events = $requete_events->fetch()) 
		{
			// Convertir la date en jour mois année
			$date_publication = date('d-m-Y' , $info_events['date_ajout']) ;
			?>
			<article class="bloc_events">
				<!-- Si il existe une ville -->
				<span class="ville_news"><?php echo $info_events['ville']; ?></span>
				<span class="date_news"><?php echo $date_publication; ?></span>
				<br />
				<p class="titre"><?php echo $info_events['intitule'] ; ?></p>
				<div style="padding:10px" class="texte_site_noir">
					<p class="text_site_noir"><?php echo $info_events['contenu'] ; ?></p>
					<br />
					<?php
					// Si il y à un coupon à télécharger on le précise
					if(!empty($info_events['url_coupon']))
					{?>
						<a download="<?php echo $info_events['url_coupon']; ?>" href="<?php echo $info_events['url_coupon']; ?>" class="choix_menu_compte_visiteur">
							<img src="/imgs/picto-telechargement.png" alt="telechargement coupon dmf" /><br />
							Télécharger le coupon de l'évent
						</a>
					<?php
					}?>
				</div>
				<p class="conteneur_image_events">
					<img style="width:100%" src="<?php echo $info_events['image_event'] ; ?>" alt="illusatration de la news"/>
				</p>
			</article>
		<?php
		}
	}
}
else
{
	echo 'non_connecte' ; 
}