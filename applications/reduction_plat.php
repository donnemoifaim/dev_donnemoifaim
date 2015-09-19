<!-- bandeau réduction quand il y à une réduction sur le plat -->
<img id="bandeau_reduction" style="<?php if(!empty($info_referencement_page) && $info_referencement_page['id_reduction'] == 0){echo 'display:none' ;} ?>" onclick="ouvrir_bloc_reduction();" src="imgs/bandeau-reduction.<?php echo versionning($fichier = 'imgs/bandeau-reduction.png'); ?>.png" alt="Remise sur ce plat" >

<?php
	// Récupération des infos de réduction si il y a une réduction associée au plat
	if(!empty($info_referencement_page) && $info_referencement_page['id_reduction'] != 0)
	{
		$requete_reduction = $bdd->prepare("SELECT id,libelle FROM reductions WHERE id = :id_reduction ");
		$requete_reduction->execute(array(':id_reduction' => $info_referencement_page['id_reduction'])) ;

		if($donnees_reduction = $requete_reduction->fetch())
		{
			//htmlentities sur tout les array sensible pour éviter les failles xss
			$donnees_reduction = protection_array_faille_xss($donnees_reduction) ;
				
			$reduction_libelle = $donnees_reduction['libelle'] ; 
			$reduction_id = $donnees_reduction['id'] ; 
		}
	}?>

<div class="bloc_apparait" id="bloc_reduction_plat" style="display:none">
	<p class="titre"><strong>Réduction</strong> de ce plat :</p>
		<p id="labelle_reduction" class="input" style="width:80%; margin:auto; width:600px"><?php if(!empty($reduction_libelle)) {echo $reduction_libelle;} ?></p>
		<input id="input_id_reduction" type="hidden" value="<?php if(!empty($reduction_id)) {echo $reduction_id;} ?>" />
	<br />
	<button onclick="apparaitre_bon_reduction();" class="input_submit reset_button"> J'en profite </button>
	<img class="picto_retour" onclick="cache_bloc_coulissant('#bloc_reduction_plat');" src="imgs/picto-retour.png" alt="picto retour" />
</div>

<div class="bloc_demande_choix" id="bloc_non_abo_reduction_choix" style="display:none">
	<span onclick="faire_disparaitre_bloc_non_abo_reduction_choix();" class="petite_croix_fermer_bloc">x</span>
	
	<p class="titre" style="color:white">Votre compte n'est pas en premium</p>
	<p>
		Pour accéder à la réduction veuillez choisir une des actions ci-dessous : 
	</p>
	<button class="buttonEnfonce" onclick="faire_disparaitre_bloc_non_abo_reduction_choix() ; ouverture_abonnement_compte_visiteur_premium()"> Passer en premium </button>
	<button class="buttonEnfonce" onclick="faire_disparaitre_bloc_non_abo_reduction_choix() ; load_pub_reduction() ;"> Regarder une pub vidéo *</button>
	
	<p style="font-size:12px"> * N'oubliez pas de désactiver adblock</p>
</div>

<!-- bloc qui apparait lorsque la personne à bien vu la vidéo -->
<div class="bloc_demande_choix" id="bloc_acces_reduction_gratuit" style="display:none; height:120px ;">
	<span onclick="fermer_message_access_reduction() ; " class="petite_croix_fermer_bloc">x</span>
	<br />
	Merci pour votre visionnage, vous pouvez accéder à la réduction ! 
	<br />
	<button class="buttonEnfonce" onclick="fermer_message_access_reduction(); ouvrir_bloc_reduction() ; "> Voir la réduction </button>
</div>

<div class="bloc_demande_choix" id="chargement_pub_reduction_en_cour" style="display:none; height:80px ;">
	<img src="imgs/ajax-loader-red.<?php echo versionning('imgs/ajax-loader-red.gif') ; ?>.gif" alt="chargement"/>
	<p>Chargement en cours</p>
</div>

<!-- iframe des pubs vidéos teads -->
<iframe style="display:none" id="iframe_pub_reduction" src=""></iframe>
