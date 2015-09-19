<?php 

session_start(); 

//connection à la bdd
include ('../../include/configcache.php') ;
include ('../../include/fonctions.php');
include('../../include/points-dmf.php') ;

if(!empty($_SESSION['login_visiteur'] ))
{
	$login_visiteur = $_SESSION['login_visiteur'] ;
	
	$req_points_affichage = $bdd->prepare("SELECT points, jaime_facebook, follow_twitter FROM utilisateur WHERE login = :login");
	$req_points_affichage->execute(array(':login' => $login_visiteur)) ;
	
	if ($info_points_affiche = $req_points_affichage->fetch())
	{?>
			<p id="bloc_contenu_mes_points" class="texte_site_noir">
			<span id="nombre_points_dmf"><?php echo $info_points_affiche['points'] ; ?></span>
			points
		</p>
		<br />
		<?php 
		// Uniquement si le bloc facebook n'a pas encore été aimé
		if($info_points_affiche['jaime_facebook'] == 0)
		{
		?>
			<div class="bloc_offre_point" id="bloc_offre_point_like_facebook">
				<p class="conteneur_image_offre">
					<img width="100%" src="/imgs/offre-like-facebook.<?php echo versionning('../../imgs/offre-like-facebook.png'); ?>.png" alt="like facebook"/>
				</p>
				<br />
				<span class="description_offre" >Like Facebook</span><br />
				<span class="option_detail">Aimez la page officiel facebook DMF pour gagner <?php echo $point_dmf_visiteur['like_facebook']; ?> points ! </span><br />
				<p class="texte_site">
					+<?php echo $point_dmf_visiteur['like_facebook']; ?>
					<img src="/imgs/picto-points-mini.<?php echo versionning('../../imgs/picto-points-mini.png') ; ?>.png" alt="points dmf" />
				</p>
				<div class="fb-like" data-href="https://www.facebook.com/donnemoifaim" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
				
				<br /><br />
			</div>
		<?php
		}?>
		<div class="bloc_offre_point" id="bloc_offre_point_like_facebook">
			<p class="conteneur_image_offre">
				<img width="100%" src="/imgs/offre-partage-facebook.<?php echo versionning('../../imgs/offre-partage-facebook.png'); ?>.png" alt="partage facebook"/>
			</p>
			<br />
			<div class="contenu_bloc_offre">
				<span class="description_offre" >Partage Facebook</span><br />
				<span class="option_detail">Partagez le plat actuel pour gagner <?php echo $point_dmf_visiteur['share_facebook']; ?> points ! Dans la limite de 200 points toutes les 24h. </span><br /><br />
				<span class="texte_site option_detail"> La page de partage peut être sous forme de pop-up.</span><br />
				<p class="texte_site">
					+<?php echo $point_dmf_visiteur['share_facebook']; ?>
					<img src="/imgs/picto-points-mini.<?php echo versionning('../../imgs/picto-points-mini.png') ; ?>.png" alt="points dmf" />
				</p>
				<button class="reset_button facebook_share_point" onclick="partageFacebookUrl(window.location.href) ; "> Partager le plat actuel </button><br />
				<p class="texte_site_noir"> ou </p>
				<button class="reset_button facebook_share_point" onclick="partageFacebookUrl('<?php echo $protocole_site.''.$_SERVER['HTTP_HOST'] ; ?>/menu-gourmand.html') ;"> Partager DonneMoiFaim </button>
				<br /><br />
			</div>
		</div>
		<?php
		// Uniquement si ca n'a pas encore été twitté 
		if($info_points_affiche['follow_twitter'] == 0)
		{
		?>
			<div class="bloc_offre_point" id="bloc_offre_point_follow_twitter">
				<p class="conteneur_image_offre">
					<img width="100%" src="/imgs/offre-follow-twitter.<?php echo versionning('../../imgs/offre-follow-twitter.png'); ?>.png" alt="like facebook"/>
				</p>
				<br />
				<span class="description_offre" >Follow Twitter</span><br />
				<span class="option_detail">Abonnez-vous à notre page officiel twitter DMF pour gagner <?php echo $point_dmf_visiteur['follow_twitter']; ?> points ! </span><br />
				<p class="texte_site">
					+<?php echo $point_dmf_visiteur['follow_twitter']; ?>
					<img src="/imgs/picto-points-mini.<?php echo versionning('../../imgs/picto-points-mini.png') ; ?>.png" alt="points dmf" />
				</p>
				<a href="https://twitter.com/donnemoifaim" class="twitter-follow-button">Follow DMF</a>    
				 <script src="//platform.twitter.com/widgets.js" type="text/javascript"></script>
				<br /><br />
			</div>
		<?php
		}?>
		<div class="bloc_offre_point">
			<p class="conteneur_image_offre">
				<img width="100%" src="/imgs/offre-point-vue.<?php echo versionning('../../imgs/offre-point-vue.png'); ?>.png" alt="vue plat"/>
			</p>
			<br />
			<span class="description_offre" >Voir plat</span><br />
			<span class="option_detail">Chaque fois que vous regardez un plat, vous gagnez <?php echo $point_dmf_visiteur['vue_plat']; ?> points.</span><br />
			<p class="texte_site">
				+<?php echo $point_dmf_visiteur['vue_plat']; ?>
				<img src="/imgs/picto-points-mini.<?php echo versionning('../../imgs/picto-points-mini.png') ; ?>.png" alt="points dmf"/>
			</p>
			<br /><br />
		</div>
		<div class="bloc_offre_point">
			<p class="conteneur_image_offre">
				<img width="100%" src="/imgs/offre-jaime-dmf.<?php echo versionning('../../imgs/offre-jaime-dmf.png'); ?>.png" alt="j'aime donnemoifaim"/>
			</p>
			<br />
			<span class="description_offre" >Aimer plat</span><br />
			<span class="option_detail">Chaque fois que vous aimez un plat, vous gagnez <?php echo $point_dmf_visiteur['aimer_plat']; ?> points.</span><br />
			<p class="texte_site">
				+<?php echo $point_dmf_visiteur['aimer_plat']; ?>
				<img src="/imgs/picto-points-mini.<?php echo versionning('../../imgs/picto-points-mini.png') ; ?>.png" alt="points dmf"/>
			</p>
			<br /><br />
		</div>
		<div class="bloc_offre_point">
			<p class="conteneur_image_offre">
				<img width="100%" src="/imgs/offre-point-rediger.<?php echo versionning('../../imgs/offre-point-rediger.png'); ?>.png" alt="redaction avis"/>
			</p>
			<br />
			<span class="description_offre" >Rédiger avis</span><br />
			<span class="option_detail">Chaque fois que vous rédigez un avis constructif sur un restaurateur (vérification), vous gagnez <?php echo $point_dmf_visiteur['redaction_avis']; ?> points.</span><br />
			<p class="texte_site">
				+<?php echo $point_dmf_visiteur['redaction_avis']; ?>
				<img src="/imgs/picto-points-mini.<?php echo versionning('../../imgs/picto-points-mini.png') ; ?>.png" alt="points dmf" />
			</p>
			<br /> 
		</div>
		<br />
	<?php
	}
}
else
{
	// Permet de dire que l'utilisateur n'est pas connecté
	echo 'non_connecte' ; 
}
