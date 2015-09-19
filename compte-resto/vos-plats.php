<?php
session_start();

include('../include/compte_acces.php') ; 
?>
<!DOCTYPE html>
<html>
<head>
	<?php
	$meta_titre = 'Vos plats - DonneMoiFaim' ; 
	$meta_description = 'Affichage de l\'ensemble de vos plats avec la possibilité de les modifier' ;
	$meta_keywords = ''  ;
	//Savoir si on référence la page
	$meta_robots = 'noindex' ; 
	include('../include/en-tete.php') ; 
	?>
</head>


<body >
	<?php
	include('../include/header.php');
	include('inc/menu_compte_resto.php'); 
	?>
	<!--bouton d'ouverture des réductions-->
	<p  class="center">
		<img onclick="voir_bloc_coulissant('#bloc_gestion_reduction') ; " style="cursor:pointer;" src="../imgs/bandeau-reduction.<?php echo versionning($fichier = 'imgs/bandeau-reduction.png'); ?>.png" alt="bandeau de réduction" ><br />
		<span onclick="voir_bloc_coulissant('#bloc_gestion_reduction') ; " style="cursor:pointer;" class="texte_site_noir">Vos réductions</span>
	</p>
	<!--titre chargement en premier-->
	<h2 class="titre center" /> Vos plats en ligne</h2><br />
	<!-- affichage des plats des utilisateurs -->
	<ul class="center">
		<?php
		// Récupération des infos des plats
		$login = $_SESSION['login'] ;
		$reponse_plat = $bdd->prepare("SELECT id,idimage,abo,nomplat, date_, nombre_vue  FROM plat WHERE login = :login && etat = 1 || login = :login && etat = 0");
		$reponse_plat->execute(array(':login' => $login)) ;

		while ($donnees_plat = $reponse_plat->fetch())
		{
			//htmlentities sur tout les array sensible pour éviter les failles xss
			$donnees_plat = protection_array_faille_xss($donnees_plat) ;
			
			$id_plat = $donnees_plat['id'] ;
			$nom_plat = $donnees_plat['nomplat'];
			$nom_plat_formate = str_replace('\'', '\\\'' , $donnees_plat['nomplat']) ; 
			$idimage = $donnees_plat['idimage'] ;
			$abo = $donnees_plat['abo'] ;
			$dateDepart = $donnees_plat['date_'] ;
			$nombre_vue = $donnees_plat['nombre_vue'] ; 
			
			if($abo != 'abonnement')
			{
				if ($abo == 1)
				{
					$temps_abonnement = 2629743 ; 
				}
				if ($abo == 2)
				{
					$temps_abonnement = 15778463 ; 
				}
				if ($abo == 3)
				{
					$temps_abonnement = 31556926 ; 
				}
				// 43200 correspond au nombre de seconde dans une journee
				$nombre_jour_restant =  round((($dateDepart + $temps_abonnement) - time())/ 86400 , 0) ;
				
				if($nombre_jour_restant > 1)
				{
					$mettre_un_s = 's' ; 
				}
				else
				{
					$mettre_un_s = '' ; 
				}
				
				$texte_abonnement = 'Encore '.$nombre_jour_restant.' jour'.$mettre_un_s.' en ligne' ; 
			}
			else
			{
				$texte_abonnement = ' Abonnement mensuel ' ; 
			}
		?>
			<li class="preview_liste_plats_compte" >
				<a id="image<?php echo $id_plat; ?>" class="lien_image" style="background-image:url('/plats/miniature/<?php echo $idimage; ?>.<?php echo versionning('/plats/miniature/'.$idimage.'.jpg'); ?>.jpg');" target="_blank" href="../<?php echo $idimage ; ?>.html"></a><br />
				<span id="nomplat<?php echo $id_plat; ?>" class="texte_site"><?php echo $nom_plat;?></span>
				<img id="modification_image<?php echo $id_plat; ?>" onclick="modification_nom_plat(<?php echo $id_plat; ?>) ;" style="cursor:pointer" src="../imgs/picto-crayon.png" alt="picto crayon" />
				<form onsubmit="modification_nom_plat(<?php echo $id_plat; ?>,'<?php echo $idimage; ?>'); return false" style="margin:0; display:none" id="bloc_modif_nom<?php echo $id_plat ; ?>">
					<input id="input_modif_nom<?php echo $id_plat ; ?>" type="text" class="input reset_input" value="<?php echo $nom_plat ; ?>" />
					<input type="submit" class="input_submit reset_input" value="Modifier" />
				</form>
				<br /><br />
				<span class="texte_site_noir nombre_jour_restant_plat"><?php echo $texte_abonnement ; ?></span><br />
				<span style="cursor:default;" title="<? echo $nombre_vue ; ?> fois vue<?php if ($nombre_vue > 1 ){echo 's' ;} ?>" class="texte_site_noir"><? echo $nombre_vue ; ?> <img style="margin-top:5px" src="../imgs/picto-voir.<?php echo versionning('imgs/picto-voir.png'); ?>.png" alt="picto voir" /></span><br /><br />
				<a class="input_submit" onclick="ouvrir_bloc_modif(<?php echo $id_plat ; ?>);" >Modifier cette image</a><br />
				<p style="border-top:2px dashed #db302d"></p>
				<label style="margin-bottom:5px" class="label_compte_resto" for="reduction_associe">Réduction :</label>
				<select onchange="associer_reduction_plat(<?php echo $id_plat; ?>); " id="select_reduction<?php echo $id_plat ; ?>" class="input reset_input" name="reduction_associe" >
					<option value="0">Aucune</option>
					<?php
					$requete_reduction = $bdd->prepare("SELECT id,idimage,libelle,utilisateur FROM reductions WHERE login = :login ");
					$requete_reduction->execute(array(':login' => $login)) ;

					while ($donnees_reduction = $requete_reduction->fetch())
					{
						//htmlentities sur tout les array sensible pour éviter les failles xss
						$donnees_reduction = protection_array_faille_xss($donnees_reduction) ;
						
						// Permet de savoir si la réduction est associé à ce plat
						$tableau_idimage = explode(',' , $donnees_reduction['idimage']);
						?>
						<option class="option_reduction_associe" <?php if(in_array($id_plat , $tableau_idimage)){echo "selected='selected'" ; }?> value="<?php echo $donnees_reduction['id']; ?>"><?php echo $donnees_reduction['libelle']; ?></option>
						<?php
					}?>
				</select>
				<img style="display:none" id="ok_changement_reduction<?php echo $id_plat; ?>" src="../imgs/okchargement.png" alt="picto ok" /><br />
				<input id="input_hidden_idimage<?php echo $id_plat; ?>" type="hidden" value="<?php echo $idimage; ?>" />
			<br />
			</li>
			<!-- token resto -->
			<input id="token_modif_plat" type="hidden" name="token"  value="<?php echo $_SESSION['token']; ?>" />
			<?php
			$ensemble_plat = 1 ; 
		}?>
	</ul><br />
	<?php
	if(!isset($ensemble_plat))
	{?>
		<div class="center">
			<p style="text-align:center" class="texte_site" >Vous n'avez aucun plat en ligne actuellement. 
				<br /><br /><span class="texte_site_noir">Si vous avez passé une commande, vos plats sont en attente de validation par notre équipe.</span>
				<br /><img src="../imgs/picto-non-plat.png" alt="aucun plat en ligne">
			</p>
			<p class="texte_site_noir">
				Pour ajouter vos plats, suivez le lien : <a class="texte_site" href="ajout-de-plat.html" >Ajoutez vos plats</a>
				<br /><br />
			</p>
		</div>
	<?php
	}?>
	<!-- modifié votre plats -->
	<div id="modifimageformulaire" class="bloc_apparait" style="display:none">
		<form onsubmit="modification_plat(); return false;" action="../ajax/compte_resto/ajout_plat.php"  style="text-align:center" method="POST" enctype="multipart/form-data">
			<?php
				$type_upload = 'modif-de-plat' ;
				include('inc/sidebar_upload.php') ; 
			?>
			<div id="bloc_apercu_modif_plat" class="center">
				<p class="titre">Modification de plat</p><br />
				<div class="bloc_gestion_image_upload">
					<span class="texte_site label_compte_resto">Image actuelle</span><br />
					<p class="preview_image texte_site" id="image_plat_ancienne"></p>
					<br />
				</div>
				
				<div class="bloc_gestion_image_upload">
					<span class="texte_site label_compte_resto">Nouvelle image</span><br />
					<p class="preview_image texte_site" id="image_plat_nouvelle">
						<img src="../imgs/picto-new.png" alt="picto new" />
					</p>
					<br />
				</div><br />
				
				<div id="div_valide_modification" class="bloc_button_suivant_processus" style="display:none">
					<input  type="image" src="../imgs/picto-modifier.png" id="input_valide_modification" value="Changer l'image"/><br />
					<label class="label_compte_resto" for="input_submit reset_input">Valider</label>
				</div>
			</div>
			
			<!--id à modif en cours -->
			<input type="hidden" id="input_plat_a_modif" />
			<input type="hidden" id="input_idimage_a_modif" />
			<br />
		</form>
		<!-- bouton de fermeture de l'application -->
		<img class="picto_retour" onclick="cache_bloc_coulissant('#modifimageformulaire') ; " src="../imgs/picto-retour.png" alt="picto retour" />
	</div>
	
	<!-- gestion des réductions  -->
	<div id="bloc_gestion_reduction" class="bloc_apparait" style="display:none">
		<div class="center">
			<br />
			<p id="bloc_ajouter_une_reduction" onclick="faire_apparaitre_ajout_reduction();" class="choix_autre_choix">Ajouter une réduction</p><br />
			<form style="display:none; position:relative ; left:-100%" id="formulaire_ajout_reduction" onsubmit="ajouter_reduction_compte();return false" action="" method="GET">
				<br />
				<label class="label_compte_resto" for="intitule_reduction"> Intitulé de la réduction : </label><br />
				<input id="intitule_reduction" name="intitule_reduction" type="text" class="input reset_input" style="350px"/><br /><br />
				<input name="envoi_reduction" type="submit" class="input_submit reset_input" />
			</form>
			<div id="ensemble_reduction">
				<?php
				$requete_reduction = $bdd->prepare("SELECT id,libelle,utilisateur FROM reductions WHERE login = :login ");
				$requete_reduction->execute(array(':login' => $login)) ;

				while ($donnees_reduction = $requete_reduction->fetch())
				{
					//htmlentities sur tout les array sensible pour éviter les failles xss
					$donnees_reduction = protection_array_faille_xss($donnees_reduction) ;
					?>
					<div class="bloc_reduction_global" id="bloc_reduction<?php echo $donnees_reduction['id']; ?>">
						<div class="bloc_reduction">
							<p class="texte_bloc_reduction">
								<textarea rows="7" cols="20" id="textarea_texte_bloc_reduction<?php echo $donnees_reduction['id']; ?>" style="color:white" class="textarea_non_resize reset_input" type="text" readonly="true" ><?php echo $donnees_reduction['libelle'] ; ?></textarea>
								<!-- permet de récupérer la value du textarea en cas d'erreur pour pouvoir restaurer -->
								<input type="hidden" id="recup_value_reduction<?php echo $donnees_reduction['id']; ?>" />
							</p>
							<p style="display:inline-block">
								<img id="crayon_reduction<?php echo $donnees_reduction['id']; ?>" onclick="focus_textarea_reduction(<?php echo $donnees_reduction['id']; ?>);" style="cursor:pointer" src="../imgs/picto-crayon.png" alt="picto crayon" />
								<br /><br />
								<img id="supp_reduction<?php echo $donnees_reduction['id']; ?>" onclick="supp_reduction(<?php echo $donnees_reduction['id']; ?>);" style="cursor:pointer" src="../imgs/picto-supp.png" alt="picto supprimer" />
							</p>
							<p class="texte_site">Nombre de fois demandé : <?php echo $donnees_reduction['utilisateur'] ;?></p>
						</div>
					</div>
				<?php
					if(!isset($ok_reduction))
					{
						$ok_reduction = 1 ; 
					}
				}
				// Si il y à aucune réduction on le dit
				if(!isset($ok_reduction))
				{?>
					<p id="bloc_aucune_reduction" style="font-size:18px; text-align:center" class="texte_site" >Vous n'avez aucune réduction ajoutée sur votre compte.
						<br /><img src="../imgs/picto-non-plat.png" alt="aucune réduction en ligne">
					</p>
				<?php
				}?>
				<br /><br />
			</div>
		</div>
		<!-- bouton de fermeture de l'application -->
		<img class="picto_retour" onclick="cache_bloc_coulissant('#bloc_gestion_reduction') ; " src="../imgs/picto-retour.png" alt="picto retour" />
	</div>
	
	<!-- mes plats hors ligne -->
	<h2 id="bloc_plat_hors_ligne" class="titre center" /> Vos plats hors ligne</h2><br />
	
	<ul class="center">
		<?php
		// L'etat 2 correspond à hors ligne
		$reponse_plat = $bdd->prepare("SELECT id,idimage,nomplat FROM plat WHERE login = :login && etat = 2");
		$reponse_plat->execute(array(':login' => $login)) ;

		while ($donnees_plat = $reponse_plat->fetch())
		{
			$id_plat = $donnees_plat['id'] ;
			$nom_plat = $donnees_plat['nomplat'];
			$nom_plat_formate = str_replace('\'', '\\\'' , $donnees_plat['nomplat']) ; 
			$idimage = $donnees_plat['idimage'] ;
			?>
			<li class="preview_liste_plats_compte" >
				<a onclick="apercu_image('/plats/archives/<?php echo $idimage; ?>.<?php echo versionning('plats/archives/'.$idimage.'.jpg'); ?>.jpg') ; return false" id="image<?php echo $id_plat; ?>" class="lien_image" style="background-image:url('/plats/archives/miniature/<?php echo $idimage; ?>.<?php echo versionning('plats/archives/miniature/'.$idimage.'.jpg'); ?>.jpg');" target="_blank" href="../<?php echo $idimage ; ?>.html"></a><br />
				<span id="nomplat<?php echo $id_plat; ?>" class="texte_site"><?php echo $nom_plat;?></span><br /><br />
				<a class="input_submit" onclick="remettreEnLignePlat('<?php echo $donnees_plat['idimage']; ?>' , document.getElementById('nomplat<?php echo $id_plat; ?>').innerHTML)" >Remettre en ligne</a><br />
			</li>
			<?php
		}?>
	</ul><br />
	
	<?php include ('../include/footer.php') ; ?>
	<script>
		window.onload = function()
		{
			bloc_apparait_responsive();
		}
	</script>
	<script ascr type="text/javascript" src="../javascript/compte_resto.<?php echo versionning($fichier = 'javascript/compte_resto.js'); ?>.js"></script>
	<script async type="text/javascript" src="../javascript/general.<?php echo versionning($fichier = 'javascript/general.js'); ?>.js"></script>
</body>
</html>