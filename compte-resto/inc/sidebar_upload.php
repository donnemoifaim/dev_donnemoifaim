<div class="bloc_upload" id="bloc_upload<?php echo $type_upload; ?>" style="display:none; top:-100% ; opacity:0">
	<!-- upload si le navigateur est récent -->
	<div id="navigateur_recent<?php echo $type_upload; ?>" >
		<!-- Buton file windows -->
		<?php
		if($type_upload == 'ajout-de-plat')
		{
			$texte_input = 'Ajouter mes images' ;
			$upload_multiple = 'multiple' ;
		}
		else if($type_upload == 'carte_resto')
		{
			$texte_input = 'Ajouter carte resto' ;
			$upload_multiple = 'multiple' ;			
		}
		else if($type_upload == 'modif-de-plat')
		{
			$texte_input = 'Modif plat' ; 
			$upload_multiple = '' ;
		}
		else if($type_upload == 'facade_resto')
		{
			$texte_input = 'Modif façade' ; 
			$upload_multiple = '' ;
		}
		
		// Permet de savoir combien y ) déjà d'images upload si y en avait déjà 
		if(!empty($_SESSION['id_image_max'.$type_upload]))
		{
			// On rajoute un + 1 pour le prochain élément
			$id_image_max = $_SESSION['id_image_max'.$type_upload] + 1 ; 
		}
		else
		{
			$id_image_max = 0 ; 
		}
		?>
		
		<input class="input_file_original" id="fichier_upload<?php echo $type_upload; ?>" type="file" name="monfichier" onchange="upload_image(<?php echo $id_image_max; ?>, '<?php echo $type_upload; ?>' , '<?php echo $upload_multiple; ?>');" style="z-index:20;position:absolute; height:50px;width:100%; max-width:180px;opacity:0;"  accept=".jpg,.jpeg,.png,.gif" <?php echo $upload_multiple; ?> />
		<!-- button input par dessus le button file de windows -->
		<input type="submit" class="input_file" value="<?php echo $texte_input; ?>" onclick="return false;" /><br />
		
		<!-- barre de progression -->
		<progress id="progress<?php echo $type_upload; ?>" value="0" style="width:180px;"></progress><br />
		<!-- pourcentage de progression jolie -->
		<p id="bloc_pourcentage_fichier<?php echo $type_upload; ?>" class="bloc_pourcentage_fichier" style="display:none">
			Fichier : <span id="telechargement_en_cour<?php echo $type_upload; ?>" class="texte_site_noir"></span>
			<br />
			<input id="progresse_upload<?php echo $type_upload; ?>" class="reset_input progresse_upload" value="0" readonly /><span class="pourcentage_signe" id="pourcentage_signe<?php echo $type_upload; ?>"> %</span><br />
			<img id="annuler_upload<?php echo $type_upload; ?>" style="display:none;float:right" class="picto_annuler" onclick="annuler_upload('<?php echo $type_upload; ?>');" src="/imgs/picto-annuler.png" alt="picto annuler" />
		</p>
		<p class="center">
			<img onclick="document.getElementById('fichier_upload<?php echo $type_upload; ?>').click() ; " id="picto-upload<?php echo $type_upload; ?>" src="../imgs/picto-upload.png" alt="picto upload" /><br />
			<!-- picto pour annuler l'uplaod -->
		</p>
		
		<p class="erreur" id="bloc_erreur<?php echo $type_upload; ?>" style="display:none"></p>
	</div>
	<!-- bloc pour les navigateur ancien -->
	<div id="navigateur_ancien<?php echo $type_upload; ?>" style=""></div>
	<br />
	<?php
	if($type_upload == 'ajout-de-plat')
	{?>
		<div id="bloc_recap_image<?php echo $type_upload; ?>" style="display:none; margin:0" class="center" >
			<ul id="recap_image<?php echo $type_upload; ?>">
				<?php
				if(!empty($_SESSION['nombre_image'.$type_upload]))
				{
					for($i=0 ; $i <= $_SESSION['id_image_max'.$type_upload] ; $i++)
					{ 
						if(!empty($_SESSION['idimage_'.$i.''.$type_upload]) AND is_file('../temporaire/'.$_SESSION['idimage_'.$i.''.$type_upload].'.jpg'))
						{?>
							<li id="recap_image_plat<?php echo $i ;?>">
								<p class="preview_image_miniature texte_site">
									<img onclick="apercu_image(idimage = '/temporaire/<?php echo $_SESSION['idimage_'.$i.''.$type_upload] ;?>.jpg' ) ; " class="apercu_image_miniature" src="/temporaire/miniature/<?php echo $_SESSION['idimage_'.$i.''.$type_upload] ;?>.jpg"  />
								</p>
							</li>
						<?php
						}
					}
				}?>
				<br />
			</ul>
			<p class="bloc_affichage_numeriques">
				<span id="prix_resume_abonnement_plat"></span> € <span class="chaque_mois_complement_abonnement"> le premier mois, puis <span class="prix_resume_abonnement_plat_unitaire"></span> € par mois</span>
			</p>
		</div>
		<script>
			// Dès que l'ajout des plats est chargé on fait apparaitre l'upload tranquillement
			if(typeof(addEventListener) != 'undefined')
			{
				window.addEventListener("load", function()
				{
					display_bloc_upload('<?php echo $type_upload; ?>' , function()
					{
						var position = getvalueCSS(document.getElementById('bloc_uploadajout-de-plat') , 'position') ; 
		
						// Si c'est différent de static c'est que c'est pas sur mobile
						if(position != 'static')
						{
							// On déale le menu comtpte pour bien le voir
							$('#menu_compte_resto').animate({marginLeft : '150px'}) ; 
						}
					}) ;
				} , false) ;
			}
			else
			{
				loadEventIEcompatible(function()
				{
					display_bloc_upload('<?php echo $type_upload; ?>' , function()
					{
						var position = getvalueCSS(document.getElementById('bloc_uploadajout-de-plat') , 'position') ; 
		
						// Si c'est différent de static c'est que c'est pas sur mobile
						if(position != 'static')
						{
							// On déale le menu comtpte pour bien le voir
							$('#menu_compte_resto').animate({marginLeft : '150px'}) ; 
						}
					}) ;
				}) ; 
			}
		</script>
	<?php
	}?>
</div>