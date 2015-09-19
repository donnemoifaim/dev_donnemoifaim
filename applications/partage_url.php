<div id="bloc_partage_reseaux_sociaux" class="bloc_apparait" style="display:none" >
	<div class="center">
		<p class="titre">Partager ce plat</p>
			<a id="lien_partage_facebook" href="https://www.facebook.com/dialog/feed?app_id=<?php echo $meta_id_app_facebook ; ?> &amp;link=<?php if(!empty($info_referencement_page)){ echo 'http://'.$_SERVER['HTTP_HOST'].'/'.$info_referencement_page['idimage'].'.html' ; } ; ?> &amp;redirect_uri=<?php if(!empty($info_referencement_page)){ echo 'http://'.$_SERVER['HTTP_HOST'].'/'.$info_referencement_page['idimage'].'.html' ; } ; ?>" <?php if($mobile_device != 1) { echo'onclick="window.open(this.href) ; return false"' ; } ?>>
				<img class="picto_partage_reseaux" src="imgs/picto-facebook-partage.<?php echo versionning('imgs/picto-facebook-partage.png'); ?>.png" alt="picto de partage facebook" />
			</a>
			<a id="lien_partage_twitter" href="
			http://twitter.com/share?text=<?php if(!empty($info_referencement_page)){ echo $info_referencement_page['nomplat'].' - '.$info_referencement_page['nomresto'] ; } ?>&amp;url=<?php if(!empty($info_referencement_page)){ echo 'http://'.$_SERVER['HTTP_HOST'].'/'.$info_referencement_page['idimage'].'.html' ; } ; ?>&amp;hashtags=donnemoifaim" <?php if($mobile_device != 1) { echo'onclick="window.open(this.href) ; return false"' ; } ?>>
				<img class="picto_partage_reseaux" src="imgs/picto-twitter-partage.<?php echo versionning('imgs/picto-twitter-partage.png'); ?>.png" alt="picto de partage twitter" />
			</a>
			<a id="lien_partage_google_plus" href="https://plus.google.com/share?url=<?php if(!empty($info_referencement_page)){ echo 'http://'.$_SERVER['HTTP_HOST'].'/'.$info_referencement_page['idimage'].'.html' ; } ; ?>" <?php if($mobile_device != 1) { echo'onclick="window.open(this.href) ; return false"' ; } ?>>
				<img class="picto_partage_reseaux" src="/imgs/picto-google-plus-partage.<?php echo versionning('imgs/picto-google-plus-partage.png'); ?>.png" alt="picto de partage google plus" />
			</a>
		<br />
		<p class="titre">Ou</p>
		<input onclick="this.select();" class="input" id="url_select" type="text" value="<?php if(!empty($info_referencement_page['idimage'])){echo $_SERVER['HTTP_HOST'].'/'.$info_referencement_page['idimage'].'.html' ;} ?>" readonly /> 
		<br /><br />
		<!-- bouton de fermeture de l'application -->
		<img class="picto_retour" onclick="cache_bloc_coulissant(id_bloc = '#bloc_partage_reseaux_sociaux');" src="imgs/picto-retour.png" alt="picto retour" />
	</div>
</div> 