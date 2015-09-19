<?php
// Token pour la modification
if(!empty($_SESSION['login']))
{
	echo '<input id="token_modif_compte" type="hidden" value="'.$_SESSION['token'].'" />' ;
}?>

<p class="texte_site_noir" style="text-align:center">* champs requis</p>
<fieldset class="bloc_contenu_centre reset_fieldset" style="text-align:center; margin-bottom:10px">
	<!-- Login -->
	<?php
	if($type_formulaire == 'inscription')
	{?>
		<label  class="label_compte_resto" for="loginformulaire" >* Nom d'utilisateur :</label>
		<input class="input reset_input" onkeyup="verif_login_formulaire();" name="logincompte" id="loginformulaire" type="text" value=""  placeholder="6 caractères min alphanumériques" pattern="<?php echo $regex_pseudo = regex_pseudo(); ?>" required /> <span class="verif_champ" id="oklogin" ></span><br /><br />
	<?php
	}?>
	
	<!-- Mot de passe -->
	<label class="label_compte_resto" for="mdpformulaire" >
		<?php if($type_formulaire == 'inscription'){echo '* Mot de passe :' ;} else {echo '*Nouveau mot de passe :' ; }?>
	</label>
	<input class="input reset_input" name="mdpcompte" onkeyup="verif_champ(id_champ = this.id , taille_condition = 6) ; document.getElementById('verif_mdpformulaire').value='';document.getElementById('ok_verif_mdpformulaire').innerHTML='';" pattern=".{6,}" id="mdpformulaire" type="password" placeholder="6 caractères minimum" <?php if($type_formulaire == 'inscription'){echo 'required' ; } ?> />
	 <span id="ok_mdpformulaire" class="verif_champ"></span><br /><br />
	
	<!-- Mot de passe -->
	<label class="label_compte_resto" for="verif_mdpformulaire" >* Verif mot de passe :</label>
	<input class="input reset_input" name="verif_mdpcompte" onkeyup="verif_champ(id_champ = this.id , taille_condition = 'verif_mdp' , verif_mdp_comparaison = 'mdpformulaire') ;" maxlength="100" id="verif_mdpformulaire" type="password" pattern=".{6,}" placeholder="6 caractères minimum" <?php if($type_formulaire == 'inscription'){echo 'required' ; } ?> />
	 <span id="ok_verif_mdpformulaire" class="verif_champ"></span><br /><br />
	
	<!-- Nom du resto -->
	<label class="label_compte_resto" for="nomresto_inscription" >* Nom resto/commerce :</label>
	<input onchange="verif_champ(id_champ = this.id , taille_condition = 1) ;" class="input reset_input" maxlength="255" name="nomresto" id="nomresto_inscription" type="text" value="<?php if(!empty($_SESSION['nomresto'])){echo $_SESSION['nomresto'];} ?>" required  />
	 <span id="ok_nomresto_inscription" class="verif_champ"></span><br /><br />
	
	<!-- type du resto -->
	<label class="label_compte_resto" for="type_resto" >* Votre activité :</label>
	<select class="input reset_input" name="type_resto" id="type_resto" required >
		<?php
			// On va mettre un tableau avec tout les type restaurant et une boucle tableau_type_resto est dans include/type_resto.php
			
			$tableau_type_resto = creer_tableau_type_resto() ; 
			
			$taille_tableau = count($tableau_type_resto) ; 
			
			for($i=0 ; $i < $taille_tableau ; $i++)
			{?>
				<option <?php if(!empty($_SESSION['type']) AND $_SESSION['type'] == $tableau_type_resto[$i]){echo 'selected';} ?> value="<?php echo $tableau_type_resto[$i]; ?>" >
				<?php echo $tableau_type_resto[$i]; ?></option>
			<?php
			}
		?>
	</select>
</fieldset>
<fieldset class="bloc_contenu_centre2 reset_fieldset" style="text-align:center">
	<!-- Rue du resto -->
	<label class="label_compte_resto" for="adressresto"   >* Rue du resto/commerce : </label>
	<input onchange="verif_champ(id_champ = this.id , taille_condition = 1) ;"  class="input reset_input" name="adressresto" id="adressresto" type="text" value="<?php if(!empty($_SESSION['adressresto'])){echo $_SESSION['adressresto'];} ?>" required />
	 <span id="ok_adressresto" class="verif_champ"></span><br /><br />
	
	<!-- Ville du resto -->
	<label class="label_compte_resto" for="ville"   >* Ville du resto/commerce :</label>
	<input  onchange="verif_champ(id_champ = this.id , taille_condition = 1) ;" class="input reset_input recherche_ville" name="ville" maxlength="100"  id="ville" type="text" value="<?php if(!empty($_SESSION['ville'])){echo $_SESSION['ville'];} ?>"  required />
	 <span id="ok_ville" class="verif_champ"></span><br /><br />
	
	<!-- mail de l'utilisateur -->
	<label class="label_compte_resto" for="mail_formulaire"  >* Votre adresse email :</label>
	<input onkeyup="verif_champ(id_champ = this.id , taille_condition = 'mail') ;"  class="input reset_input" name="email"  id="mail_formulaire" value="<?php if(!empty($_SESSION['mail'])){echo $_SESSION['mail'];} ?>" type="email" placeholder="Ex : monresto@resto.fr" required  />
	 <span id="ok_mail_formulaire" class="verif_champ"></span><br /><br />
	
	<!-- telephone du resto -->
	<label class="label_compte_resto" for="telephone">Téléphone resto :</label>
	<input  class="input reset_input" name="telephone" id="telephone" type="tel" value="<?php if(!empty($_SESSION['telephone'])){echo $_SESSION['telephone'];} ?>" placeholder="facultatif"   />
	<br /><br />
	
	<!-- site du resto -->
	<label class="label_compte_resto" for="site_internet">Site internet :</label>
	<input class="input reset_input" name="site_internet" maxlength="255"  id="site_internet" type="url" value="<?php if(!empty($_SESSION['site_internet'])){echo $_SESSION['site_internet'];} ?>" placeholder="facultatif"   />
</fieldset><br />
	
<?php
if($type_formulaire == 'inscription')
{?>
	<!-- envoi du formulaire -->
	<input id="submit" class="input_submit reset_input" type="submit" value="S'inscrire"  />
<?php
}
else
{?>
	<!-- envoi du formulaire -->
	<input id="submit" class="input_submit reset_input" type="submit" value="Modifier"  />
<?php
}
?>
<img src="/imgs/chargement.<?php echo versionning('imgs/chargement.gif') ; ?>.gif" id="loading_creation_compte" alt="loading" width="20px" style="display:none; position:absolute" />