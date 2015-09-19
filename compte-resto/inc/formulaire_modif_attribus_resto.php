<fieldset class="reset_fieldset" style="text-align:center; margin-bottom:10px">
	<p class="texte_site">Cochez vos infos complémentaires : </p>
	
	<p class="texte_site_noir">Comment manger : </p>
	
	<p class="bloc_attribus bloc_offre">
		<span class="picto_attribut">
			<img src="/imgs/picto-sur-place.<?php echo versionning('imgs/picto-sur-place.png') ; ?>.png" alt="picto sur place" />
		</span>
		<label class="label_compte_resto" for="attribus_resto_sur_place">Sur place</label><br />
		<input <?php if(!empty($tableau_attribus_checked) && in_array('sur-place' , $tableau_attribus_checked)){echo 'checked' ;} ?> class="input_checkbox_attribus" id="attribus_resto_sur_place" type="checkbox" name="sur-place" />
	</p>
	<p class="bloc_attribus bloc_offre">
		<span class="picto_attribut">
			<img src="/imgs/picto-a-emporter.<?php echo versionning('imgs/picto-a-emporter.png') ; ?>.png" alt="picto à emporter" />
		</span>
		<label class="label_compte_resto" for="attribus_resto_emporter">A emporter</label><br />
		<input <?php if(!empty($tableau_attribus_checked) && in_array('a-emporter' , $tableau_attribus_checked)){echo 'checked' ;} ?>  class="input_checkbox_attribus" id="attribus_resto_emporter" type="checkbox" name="a-emporter" />
	</p>
	<p class="bloc_attribus bloc_offre">
		<span class="picto_attribut">
			<img src="/imgs/picto-livraison.<?php echo versionning('imgs/picto-livraison.png') ; ?>.png" alt="picto livraison" />
		</span>
		<label class="label_compte_resto" for="attribus_resto_livraison">Livraison</label><br />
		<input <?php if(!empty($tableau_attribus_checked) && in_array('livraison' , $tableau_attribus_checked)){echo 'checked' ;} ?> class="input_checkbox_attribus" id="attribus_resto_livraison" type="checkbox" name="livraison" />
	</p>
	
	<p class="texte_site_noir">Caractéristiques nourriture : </p>
	
	<p class="bloc_attribus bloc_offre">
		<span class="picto_attribut">
			<img src="/imgs/picto-fait-maison.<?php echo versionning('imgs/picto-fait-maison.png') ; ?>.png" alt="picto fait maison" />
		</span>
		<label class="label_compte_resto" for="attribus_resto_fait_maison">Fait maison</label><br />
		<input <?php if(!empty($tableau_attribus_checked) && in_array('fait-maison' , $tableau_attribus_checked)){echo 'checked' ;} ?> class="input_checkbox_attribus" id="attribus_resto_fait_maison" type="checkbox" name="fait-maison" />
	</p>
	<p class="bloc_attribus bloc_offre">
		<span class="picto_attribut">
			<img src="/imgs/picto-a-volonte.<?php echo versionning('imgs/picto-a-volonte.png') ; ?>.png" alt="picto à volonté" />
		</span>
		<label class="label_compte_resto" for="attribus_resto_a_volonte">A volonté</label><br />
		<input <?php if(!empty($tableau_attribus_checked) && in_array('a-volonte' , $tableau_attribus_checked)){echo 'checked' ;} ?> class="input_checkbox_attribus" id="attribus_resto_a_volonte" type="checkbox" name="a-volonte" />
	</p>
	<!-- bio le label est l'image puisque c'est écrit bio dessus-->
	<p class="bloc_attribus bloc_offre">
		<span class="picto_attribut">
			<img src="/imgs/picto-bio.<?php echo versionning('imgs/picto-bio.png') ; ?>.png" alt="picto bio" />
		</span>
		<label class="label_compte_resto" for="attribus_resto_bio">Nourriture bio</label ><br />
		<input <?php if(!empty($tableau_attribus_checked) && in_array('bio' , $tableau_attribus_checked)){echo 'checked' ;} ?> class="input_checkbox_attribus" id="attribus_resto_bio" type="checkbox" name="bio" />
	</p>
	<p class="bloc_attribus bloc_offre">
		<span class="picto_attribut">
			<img src="/imgs/picto-halal.<?php echo versionning('imgs/picto-halal.png') ; ?>.png" alt="picto halal" />
		</span>
		<label class="label_compte_resto" for="attribus_resto_halal">Halal</label><br />
		<input <?php if(!empty($tableau_attribus_checked) && in_array('halal' , $tableau_attribus_checked)){echo 'checked' ;} ?> class="input_checkbox_attribus" id="attribus_resto_halal" type="checkbox" name="halal" />
	</p>
	
	<p class="texte_site_noir">Prix moyen : </p>
	
	<p class="bloc_attribus bloc_offre">
		<img src="/imgs/picto-prix.<?php echo versionning('imgs/picto-prix.png') ; ?>.png" alt="picto prix" />
		<img src="/imgs/picto-non-prix.<?php echo versionning('imgs/picto-non-prix.png') ; ?>.png" alt="picto non prix" />
		<img src="/imgs/picto-non-prix.<?php echo versionning('imgs/picto-non-prix.png') ; ?>.png" alt="picto non prix" />
		<label class="label_compte_resto" for="attribus_resto_prix_mini" >Moins de 10€</label><br />
		<input <?php if(!empty($tableau_attribus['attribus_prix']) && $tableau_attribus['attribus_prix'] == 1){echo 'checked' ;} ?> class="input_checkbox_attribus" id="attribus_resto_prix_mini" type="radio" name="prix" value="1" />
	</p>
	<p class="bloc_attribus bloc_offre">
		<img src="/imgs/picto-prix.<?php echo versionning('imgs/picto-prix.png') ; ?>.png" alt="picto prix" />
		<img src="/imgs/picto-prix.<?php echo versionning('imgs/picto-prix.png') ; ?>.png" alt="picto prix" />
		<img src="/imgs/picto-non-prix.<?php echo versionning('imgs/picto-non-prix.png') ; ?>.png" alt="picto non prix" />
		<label class="label_compte_resto" for="attribus_resto_prix_moyen">Entre 10€ et 25€</label><br />
		<input <?php if(!empty($tableau_attribus['attribus_prix']) && $tableau_attribus['attribus_prix'] == 2){echo 'checked' ;} ?> class="input_checkbox_attribus" id="attribus_resto_prix_moyen" type="radio" name="prix" value="2" /> 
	</p>
	<p class="bloc_attribus bloc_offre">
		<img src="/imgs/picto-prix.<?php echo versionning('imgs/picto-prix.png') ; ?>.png" alt="picto prix" />
		<img src="/imgs/picto-prix.<?php echo versionning('imgs/picto-prix.png') ; ?>.png" alt="picto prix" />
		<img src="/imgs/picto-prix.<?php echo versionning('imgs/picto-prix.png') ; ?>.png" alt="picto prix" />
		<label class="label_compte_resto" for="attribus_resto_prix_fort">Plus de 25€</label><br />
		<input <?php if(!empty($tableau_attribus['attribus_prix']) && $tableau_attribus['attribus_prix'] == 3){echo 'checked' ;} ?> class="input_checkbox_attribus" id="attribus_resto_prix_fort" type="radio" name="prix" value="3" />
	</p>
	
	<p class="texte_site_noir">Multimédia : </p>
	
	<p class="bloc_attribus bloc_offre">
		<span class="picto_attribut">
			<img src="/imgs/picto-wifi.<?php echo versionning('imgs/picto-wifi.png') ; ?>.png" alt="picto wifi" />
		</span>
		<label class="label_compte_resto" for="attribus_resto_wifi">Wifi</label><br />
		<input <?php if(!empty($tableau_attribus_checked) && in_array('wifi' , $tableau_attribus_checked)){echo 'checked' ;} ?> class="input_checkbox_attribus" id="attribus_resto_wifi" type="checkbox" name="wifi" />
	</p>
	
	<br />
	<p class="texte_site_noir">Contact </p>
	
	<p class="bloc_attribus bloc_offre">
		<img src="/imgs/picto-phone.<?php echo versionning('imgs/picto-phone.png') ; ?>.png" alt="picto phone" /><br />
		<label class="label_compte_resto" for="attribus_resto_numero">Faire apparaitre le numéro resto pour les visiteurs</label><br />
		<input <?php if(!empty($tableau_attribus_checked) && in_array('numero' , $tableau_attribus_checked)){echo 'checked' ;} ?> class="input_checkbox_attribus" id="attribus_resto_numero" type="checkbox" name="numero" />
	</p>
	<p class="bloc_attribus bloc_offre">
		<img src="/imgs/picto-contact.<?php echo versionning('imgs/picto-contact.png') ; ?>.png" alt="picto contact" /><br />
		<label class="label_compte_resto" for="attribus_resto_mail">Faire apparaitre l'email resto pour les visiteurs</label><br />
		<input <?php if(!empty($tableau_attribus_checked) && in_array('contact' , $tableau_attribus_checked)){echo 'checked' ;} ?> class="input_checkbox_attribus" id="attribus_resto_mail" type="checkbox" name="contact" />
	</p>
	<p class="bloc_attribus bloc_offre">
		<img src="/imgs/picto-site-internet.<?php echo versionning('imgs/picto-site-internet.png') ; ?>.png" alt="picto site internet" /><br />
		<label class="label_compte_resto" for="attribus_site_internet">Faire apparaitre mon site internet pour les visiteurs</label><br />
		<input <?php if(!empty($tableau_attribus_checked) && in_array('site-internet' , $tableau_attribus_checked)){echo 'checked' ;} ?> class="input_checkbox_attribus" id="attribus_site_internet" type="checkbox" name="site-internet" />
	</p>
	
	
</fieldset>
<br />
<div class="bloc_button_suivant_processus" id="bloc_button_suivant_processus2" style="right:80px">
	<input id="button_suivant_processus2" type="image" src="/imgs/picto-suivant.png" alt="suivant" /><br>
	<label class="texte_site" for="button_suivant_processus">Valider</label>
</div>
<br /><br />