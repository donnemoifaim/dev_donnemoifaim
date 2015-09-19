<?php
session_start() ;

include ('../../include/configcache.php') ;
include ('../../include/fonctions.php') ;

if(!isset($_SESSION['login_visiteur']))
{
	// Si on la pas encore vu de la journée
	if(!isset($_SESSION['creer_compte_visiteur_deja_vu']))
	{
	?>
		<br />
		<p class="titre">Comtpe DonneMoiFaim</p>
		<p class="texte_site_noir">Vous aimez DonneMoiFaim ? Créez votre compte pour bénéficier de nombreux avantages : </p>
		
		<button style="background-color:#db302d; color:white" onclick="voir_bloc_coulissant('#compte_visiteur') ; affichage_creation_compte() ; " class="reset_button buttonEnfonce">S'inscrire</button> &nbsp;
		<button style="background-color:#db302d; color:white" onclick="voir_bloc_coulissant('#compte_visiteur') ; " class="reset_button buttonEnfonce">Se connecter</button> 
		<br /><br />
		<p class="bloc_image_legende_responsive">
			<img src="/imgs/picto-news.png" alt="News DMF" /><br />
			Connaitre les nouveautés
		</p>
		<p class="bloc_image_legende_responsive">
			<img src="/imgs/picto-mes-jaimes.png" alt="j'aimes DMF" /><br />
			Sauvegarder les plats
		</p>
		<p class="bloc_image_legende_responsive">
			<img src="/imgs/picto-evenement.png" alt="Evenement DMF" /><br />
			Participer aux évènements
		</p>
		<p class="bloc_image_legende_responsive">
			<img src="imgs/picto-avis.png" alt="Avis DMF" /><br />
			Accèder aux avis
		</p>
		<p class="bloc_image_legende_responsive">
			<img style="width:40px;" src="/imgs/bandeau-reduction.png" alt="Reduction DMF" /><br />
			Accèder aux réductions en premium
		</p>
		<p class="bloc_image_legende_responsive">
			<img src="/imgs/logo-restopolitan.png" alt="Logo restopolitan" /><br />
			Tirage au sort et concours
		</p>
		<br /><br />
		<!-- bouton de fermeture de l'application -->
		<img class="picto_retour" onclick="cache_bloc_coulissant('#bloc_publicite_apparante');" src="imgs/picto-retour.png" alt="picto retour" />
		
		<?php
		// Pour éviter de revoir ce bloc si on l'a déjà vu
		$_SESSION['creer_compte_visiteur_deja_vu'] = 1 ; 
	}
	else
	{
		echo 'no-pub' ;
	}
}
else
{
	echo 'no-pub' ; 
}