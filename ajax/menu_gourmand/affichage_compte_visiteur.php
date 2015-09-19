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
		// On veut savoir si la personne est un abonné ou pas 
		$requete_user = $bdd->prepare('SELECT abo, date_abo FROM utilisateur WHERE login = :login') ; 
		$requete_user->execute(array(':login' => $_SESSION['login_visiteur'])) ;
		
		if($info_user = $requete_user->fetch())
		{
			verification_abonnement_visiteur($info_user['abo'], $info_user['date_abo'], $_SESSION['login_visiteur']) ;
		}
		
		// On récupère le temps d'abonnement pour savoir si la personne est abonnée ou pas ! 
		?>
			<input type="hidden" id="temps_abonnement_compte_visiteur_ajax" value="<?php echo $_SESSION['seconde_restant_abonnement']; ?>" />
		<?php
	
		// On regarde si la cible (le max) est vide ou pas
		
		$requete_max_news = $bdd->query('SELECT id,membre_ayant_vu FROM news WHERE date_ajout = (SELECT MAX(date_ajout) FROM news)') ;
		$info_requete_max_new = $requete_max_news->fetch() ; 
		
		
		if($info_requete_max_new['membre_ayant_vu'] == '')
		{
			$varible_concatenation = $_SESSION['id_visiteur'] ;
		}
		else
		{
			$varible_concatenation = ','.$_SESSION['id_visiteur'] ; 
			
			$tableau_id_views_news = explode(',' , $info_requete_max_new['membre_ayant_vu']) ; 
			$nombre_id = count($tableau_id_views_news) ; 
			
			for($i=0 ; $i < $nombre_id; $i++ )
			{
				// Si l'id du visiteur est déjà dans le tableau alors on ne le rajoute pas dans le tableau
				if($_SESSION['id_visiteur'] == $tableau_id_views_news[$i])
				{
					$not_ajout_id_in_array = 1 ;
				}
			}
		}
		
		$id_max_event = $info_requete_max_new['id'] ; 
		
		if(!isset($not_ajout_id_in_array))
		{
			// Ont ajoute le nom de la personne dans la news la + grande
			$requete_news_deja_vu = $bdd->prepare('UPDATE news SET membre_ayant_vu = CONCAT(membre_ayant_vu , \''.$varible_concatenation.'\')  WHERE id = :id_news') ; 
			$requete_news_deja_vu->execute(array(':id_news' => $id_max_event)) ; 
		}
	}
	
	// On récupère les news pour les afficher
	$requete_news = $bdd->query('SELECT intitule, contenu, image_news, id_unique, ville, categorie, cible, date_ajout FROM news WHERE statut = 1 ORDER BY date_ajout DESC LIMIT 0,10') ; 

	while($info_news = $requete_news->fetch()) 
	{
		if(($info_news['categorie'] == 'ajout_plat'))
		{
			$requete_plat = $bdd->prepare('SELECT nomplat, idimage FROM plat WHERE id = :id ') ;
			$requete_plat->execute(array(':id' => $info_news['id_unique'])) ; 
			
			if($info_plat = $requete_plat->fetch())
			{
				$idimage = $info_plat['idimage'] ;
				$image_news = '/plats/'.$info_plat['idimage'].'.'.versionning('../../plats/'.$info_plat['idimage'].'.jpg').'.jpg' ; 
				$intitule = $info_plat['nomplat'] ; 
			}
		}
		else
		{
			$intitule = $info_news['intitule'] ; 
			$image_news = $info_news['image_news'] ; 
		}
		
		// Convertir la date en jour mois année
		$date_publication = date('d-m-Y' , $info_news['date_ajout']) ;
		?>
		<article class="bloc_news">
			<!-- Si il existe une ville -->
			<span class="ville_news"><?php echo $info_news['ville']; ?></span>
			<span class="date_news"><?php echo $date_publication; ?></span>
			<br />
			<p class="titre"><?php echo $intitule ; ?></p>
			<div style="padding:10px" class="texte_site_noir">
				<?php echo $info_news['contenu'] ; ?>
			</div>
			<p class="conteneur_image_news">
				<?php
				if($info_news['categorie'] == 'ajout_plat')
				{?>
					<a style="cursor:pointer" href="<?php echo $idimage ?>.html" onclick="if(history.pushState){nom_image_history = <?php echo $info_news['id_unique'] ; ?> ;chargementimage(); cache_bloc_coulissant('#compte_visiteur_connecte_menu') ; return false;}">
						<img style="width:100%" src="<?php echo $image_news ; ?>" alt="illusatration de la news"/>
					</a>
				<?php
				}
				else
				{?>
					<img style="width:100%" src="<?php echo $image_news ; ?>" alt="illusatration de la news"/>
				<?php
				}?>
			</p>
		</article>
	<?php
	}
}
else
{
	echo 'non_connecte' ; 
}