<?php
// On regarde si un avis à déjà été rédigé ou pas
$requete_avis_utilisateur = $bdd->prepare('SELECT id,contenu_avis, note FROM avis_utilisateur WHERE login = :login && id_resto = :id_resto');
$requete_avis_utilisateur->execute(array(':login' => $_SESSION['login_visiteur'] ,  ':id_resto' => $_SESSION['id_resto_actuel']));
 
if($donnees_avis = $requete_avis_utilisateur->fetch())
{
	//htmlentities sur tout les array sensible pour éviter les failles xss
	$donnees_avis = protection_array_faille_xss($donnees_avis) ;
	
	$avis_type = 'modif_avis' ;
	$id_avis = $donnees_avis['id'];
	$contenu_avis_acutelle = html_entity_decode($donnees_avis['contenu_avis']) ;
	$nombre_etoile = $donnees_avis['note']; 
}
else
{
	$avis_type = 'ajout_avis' ;
	$id_avis = 0;
	$contenu_avis_acutelle = '' ; 
	$nombre_etoile = 0 ; 
}
?>

<div id="bloc_avis" class="bloc_apparait" style="display:none; padding-left:0 ; padding-right:0">
	<div id="bloc_ensemble_avis">
		<p class="titre">Ensemble des avis</p>
		<p id="button_ouverture_form_avis" onclick="ouvrir_bloc_form_avis();" class="choix_autre_choix">
			<?php 
			if($avis_type == 'modif_avis')
			{
				echo 'Modifiez votre avis' ; 
			}
			else
			{
				echo 'Donnez votre avis !' ; 
			}?>
		</p>
		<div id="ensemble_avis">
			<!-- mettre le php adéquoit -->
		</div> 
		<!-- id a supp pour récupérer , pourrait etre mis ailleur mais pour plus de lisibilité mis ici -->
		<input id="id_avis_a_supp" type="hidden" value="<?php echo $id_avis ;  ?>" />
		<input id="token_supp_avis" type="hidden" value="<?php echo $_SESSION['token_visiteur']; ?>" />
		<br />
		<!-- bouton de fermeture de l'application -->
		<img class="picto_retour" onclick="cacher_bloc_avis_visiteur();" src="imgs/picto-retour.png" alt="picto retour" />
	</div>
	<div id="bloc_form_avis_utilisateur" style="display:none;">
		<p class="texte_site">1/ Attribuer une note au resto : </p>
		<span id="bloc_etoile_modif_ajout">
		<?php
			// Nombre d'étoile est récupéré plus haut
			$affichage_etoile_avis = affichage_etoile_avis($nombre_etoile , 'ajout-modif') ; 
			
			echo $affichage_etoile_avis ;
		?>
		</span>
		<form action="../ajax/ajout_avis.php" onsubmit="submit_avis();return false;" method="POST" id="form_avis">
			<input id="input_nombre_etoile_avis" type="hidden" name="nombre_etoile_avis" value="<?php echo $nombre_etoile ; ?>" />
			<input id="login_visiteur_avis" type="hidden" value="<?php echo $_SESSION['login_visiteur'] ; ?>"/><br />
			<label class="label_compte_visiteur">2/ Rédigez votre avis : </label><br /><br />
			<textarea  class="input reset_input textarea_non_resize"  rows="10" cols="100" id="form_avis_utilisateur" placeholder="Ex : Plat qui donne envie à premier abord, je m'y suis personnellement rendu et je l'ai trouvé vraiment bon et original. Bravo !" >
				<?php echo $contenu_avis_acutelle ; ?>
			</textarea>
			
			<p id="bloc_valider_avis_plat">
				<input id="valider_form_avis" alt="valider formulaire" type="image" src="imgs/picto_finish_form.png"/><br />
				<label class="texte_site" for="valider_form_avis">Valider</label>
			</p>
			<br />
		<!-- bouton de fermeture de l'application -->
		<img class="picto_retour" onclick="ouvrir_bloc_form_avis();" src="imgs/picto-retour.png" alt="picto retour" />
		</form>	
	</div>

</div>