<?php
session_start() ; 

// On définis nous meme un niveau d'aborescence car le filetime va faire des siennes : en effet le niveau du site va etre compté comme étant à ../../ alors que en vérité il est à la racile du serveur ! 

$niveau_arborescence = '' ; 

if(!empty($_GET['type_resto']))
{
	$formate_type_resto = str_replace('-' , ' ', $_GET['type_resto']);
	
	if(!empty($_GET['ville']))
	{
		$formate_ville = str_replace('-' , ' ', $_GET['ville']);
		
		$balise_title = $formate_type_resto.' '.$formate_ville.' - DonneMoiFaim' ; 
		$balise_description = 'Liste '.$formate_type_resto.' '.$formate_ville.' disponibles sur l\'annuaire & l\'application de plat en ligne donnemoifaim' ;
		$h1_dynamique = 'Guide '.$formate_type_resto.' '.$formate_ville.' en ligne' ; 
		$keywords = $formate_type_resto.', '.$formate_ville.' , liste, restos, restaurant'  ; 
	}
	else
	{
		$balise_title = $formate_type_resto.' - DonneMoiFaim' ; 
		$balise_description = 'Liste '.$formate_type_resto.' disponibles sur l\'annuaire & l\'application de plat en ligne donnemoifaim' ;
		$h1_dynamique = 'Guide '.$formate_type_resto.' en ligne' ; 
		$keywords = $formate_type_resto.', liste, restos, restaurant'  ; 
	}
}
else
{
	// C'est que c'est directement l'annuaire
	$balise_title = 'Annuaire restaurants et réductions - DonneMoiFaim' ; 
	$balise_description = 'Notre annuaire permet de voir la liste des restos disponible par ville sur donnemoifaim et de voir ceux qui propose des réductions' ;
	$h1_dynamique = 'Annuaire des restos en ligne' ; 
	$keywords = 'annuaires, restos, restaurant,trouver' ; 
}
?>
<!DOCTYPE html>
<html>
	<head>
		<?php
		$meta_titre = $balise_title ; 
		$meta_description = $balise_description ;
		$meta_keywords = $keywords  ;
		//Savoir si on référence la page
		$meta_robots = 'index' ; 
		include('include/en-tete.php') ;
		?>
	</head>
	<body>	
		<?php 
		include('include/header.php'); 
		?>
		<div style="text-align:center">
			<h1 id="titre_annuaire_dynamique" class="titre"><?php echo $h1_dynamique ; ?></h1><br />
			<?php
			if(!empty($_GET['type_resto']))
			{?>
				<p class="texte_site_noir">Voici la liste des restos de votre recherche en ligne sur donnemoifaim. Pour affiner votre recherche, vous pouvez utiliser le moteur de recherche ci-dessous.</p>
			<?php
			}
			else
			{?>
				<p class="texte_site_noir">Vous voulez trouver un restaurant en particulier ? Tous les restaurants d'une même ville qui livrent ? Ou bien simplement ceux qui fournissent une réduction sur leurs plats ? </p>
				<p class="texte_site">Utilisez notre moteur de recherche </p>
			<?php
			}
			?>
			<br />
			<!-- formulaire recherche ville --> 
			<form id="recherche_approfondie_annuaire" onsubmit="rechercher_appronfondie_annuaire('annuaire_recherche_initiale'); return false" method="POST" action="annuaire-restaurant.html"  enctype="multipart/form-data"> 
				<label class="label_blanc" for="select_recherche_par">Je recherche : </label>
				<select id="select_recherche_par" class="input" onchange="display_formulaire_recherche_approfondie('recherche_par' , this.value) ;" name="recherche_par">
					<option value="">Choisir</option>
					<option value="tous_les_resto">Tous les restaurants</option>
					<option value="resto_type">Un seul type de restaurant (ex: Restaurant Japonais)</option>
					<option value="resto_plusieurs_type">Plusieurs types de restaurant (ex: Restaurant Chinois, Boulangerie...)</option>
					<option value="nom_resto" >Un restaurant en ligne (ex: Buffalo Grill)</option>
				</select>
				<br />
				<div style="display:none" id="fieldset_type_resto_annuaire" class="fieldset_input_display" >
					<label class="label_blanc" for="select_type_resto">Types resto disponibles : </label>
					<select id="select_type_resto" class="input"  name="type_resto" onchange="faire_apparaitre_ville_choix_annuaire()">
						<option value="">Choisir</option>
						<?php 

						$tableau_type_resto_disponible = creer_tableau_type_resto_disponible() ; 
						
						$taille_tableau = count($tableau_type_resto_disponible) ; 
				
						for($i=0 ; $i < $taille_tableau ; $i++)
						{?>
							<option value="<?php echo $tableau_type_resto_disponible[$i]; ?>" ><?php echo $tableau_type_resto_disponible[$i]; ?></option>
						<?php
						}
						?>
					</select>
				</div>
				<div style="display:none" id="fieldset_tout_type_resto_annuaire" class="fieldset_input_display" >
					<p style="color:white; font-weight:bold">Types resto disponibles : </p>
					<?php
					
					// On à déjà initir les variable nécessaire avant 
					
					for($i=0 ; $i < $taille_tableau ; $i++)
					{?>
						<div class="bloc_conteneur_plusieurs_checkbox_type_resto">
							<label  for="checkbox_tous_plusieurs_resto<?php echo $i ?>"><?php echo $tableau_type_resto_disponible[$i]; ?></label>
							<br />
							<input onchange="faire_apparaitre_ville_choix_annuaire();" id="checkbox_plusieurs_type_resto<?php echo $i ?>" type="checkbox" class="checkbox_type_resto_multiple" name="<?php echo $tableau_type_resto_disponible[$i]; ?>"/>
						</div>
					<?php
					}
					?>
				</div>
				<div style="display:none" id="fieldset_nom_resto_annuaire" class="fieldset_input_display" >
					<label class="label_blanc" for="select_nom_resto">Restos en ligne : </label>
					<select id="select_nom_resto" class="input"  name="type_resto" onchange="faire_apparaitre_ville_choix_annuaire('nom_resto')">
						<option value="">Choisir</option>
						<?php 

						// Recherche de restaurant par leur nom
						$requete_nom_resto = $bdd->query('SELECT DISTINCT nomresto FROM client WHERE statut = 1 ORDER BY nomresto') ; 
						
						while($info_nom_resto = $requete_nom_resto->fetch())
						{?>
							<option style="text-transform:capitalize" value="<?php echo $info_nom_resto['nomresto']; ?>" ><?php echo $info_nom_resto['nomresto']; ; ?></option>
						<?php
						}
						
						$requete_nom_resto->closeCursor() ; 
						?>
					</select>
				</div>
				<div style="display:none" id="fieldset_ville_annuaire" class="fieldset_input_display" >
					<label class="label_blanc" for="select_recherche_ville">Villes disponibles : </label><br />
					<select id="select_recherche_ville" class="input" onchange="faire_apparaitre_option_complementaire() ; " name="ville">
						<option value="">Choisir</option>
						<option value="">Toutes</option>
						<?php
							$requete_ville_disponible = $bdd->query('SELECT DISTINCT ville FROM client ORDER BY ville') ;
							
							while($info_ville_disponible = $requete_ville_disponible->fetch())
							{?>
								<option style="text-transform:capitalize" value="<?php echo $info_ville_disponible['ville'] ; ?>"><?php echo $info_ville_disponible['ville'] ; ?></option>
							<?php
							}
						?>
					</select>
				</div>
				<div style="display:none" id="fieldset_option_complementaire_annuaire" class="fieldset_input_display" >
					<div id="bloc_conteneur_option_reduction" class="bloc_conteneur_option_complementaire" style="height:100px">
						<img width="30px" src="/imgs/bandeau-reduction.<?php echo versionning('imgs/bandeau-reduction.png') ; ?>.png" alt="réduction ">
						<label style="width:100%" for="checkbox_voir_que_reduction">N'afficher que les restos qui proposent des réductions</label>
						<br />
						<input id="checkbox_voir_que_reduction" type="checkbox" class="input_checkbox" name="voir_que_reduction"/>
					</div>
					<div id="bloc_conteneur_option_livraison" class="bloc_conteneur_option_complementaire" style="height:100px">
						<span class="picto_attribut" style="width:20px; height:20px; margin:0">
							<img width="20px" src="/imgs/picto-livraison.<?php echo versionning('imgs/bandeau-reduction.png') ; ?>.png" alt="livraison ">
						</span>
						<label style="width:100%" for="checkbox_voir_que_livraison">N'afficher que les restos qui livrent</label>
						<br />
						<input id="checkbox_voir_que_livraison" type="checkbox" class="input_checkbox" name="voir_que_livraison"/>
					</div>
					<br />
					<input class="input_submit" type="submit" value="Rechercher" />
				</div>
			</form>
			<div id="resultat_recherche_annuaire">
				<?php
				if(!empty($_GET['type_resto']))
				{
					$recherche_par_type = $formate_type_resto ; 
					include('ajax/annuaire/recherche_approfondie.php') ; 
				}?>
			</div>
			<br /><br />
			<p class="texte_site" style="margin:0">Navigation par lien</p>
			<ul>
				<?php 
					if(!empty($_GET['type_resto']))
					{
						$where_option = 'WHERE type_resto = "'.$formate_type_resto.'"' ; 
					}
					else
					{
						$where_option = '';
					} 
					// En fonction des résultats disponible 
					$requete_type_disponible = $bdd->query('SELECT type_resto, ville FROM client '.$where_option.' GROUP BY type_resto') ;
					
					while($info_type_disponible = $requete_type_disponible->fetch())
					{
						if(!empty($_GET['type_resto']))
						{
							$lien_debut = $info_type_disponible['type_resto'] ;
							$lien_debut_formate = renomage_url_fichier($lien_debut) ;
							$lien_debut_formate = $lien_debut_formate.'/' ;
							
							$lien_second = $info_type_disponible['ville'] ;
							$lien_second_formate = renomage_url_fichier($lien_second) ;
							$lien_second_formate = $lien_second_formate.'/' ;
							
							$lien_final = '/annuaire/'.$lien_debut_formate.''.$lien_second_formate ; 
							
							$texte_lien_final = $info_type_disponible['type_resto'].' '.$info_type_disponible['ville'] ;
							
							$title_final = 'liste '.$info_type_disponible['type_resto'].' sur '.$info_type_disponible['ville'].' en ligne' ; 
						}
						else
						{
							$lien_debut = $info_type_disponible['type_resto'] ;
							$lien_formate = renomage_url_fichier($lien_debut) ;
							$lien_final = '/annuaire/'.$lien_formate.'/' ; 
							
							$texte_lien_final = $info_type_disponible['type_resto'] ; 
							
							$title_final = 'liste '.$info_type_disponible['type_resto'].' en ligne' ; 
						}
						
						?>
						<li style="display:inline-block; margin:0">
							<a class="choix_autre_choix" href="<?php echo $lien_final; ?>" title="<?php echo $title_final; ?>">		
								<?php echo $texte_lien_final ; ?>
							</a>
						</li>
					<?php
					}
				?>
			</ul>
		</div>
		<br />
		<!-- insertion du footer -->
		<?php include('include/footer.php'); ?>
		
		<script async type="text/javascript" src="/javascript/general.<?php echo versionning('javascript/general.js'); ?>.js"></script>
	</body>
</html>