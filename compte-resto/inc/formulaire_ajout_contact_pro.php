<?php
if(!isset($_SESSION['login']))
{
?>
	<div id="bloc_tout_les_contact_pro"></div><br />
<?php
}?>

<form style="text-align:center; padding:10px" onsubmit="ajouter_contact_pro('ajout_contact') ; return false ; " id="formulaire_ajout_contact_pro" class="fiche_resto">
	
	<p class="texte_site_noir" style="text-align:center">* champs requis</p>
	
	<label for="nom_contact_pro" class="label_compte_resto">* Civilité : </label>
	<select id="civ_contact_pro" class="input" required>
		<option value="1">Monsieur</option>
		<option value="2">Madame</option>
	</select><br /><br />
	
	<label for="nom_contact_pro" class="label_compte_resto">* Nom : </label>
	<input class="input reset_input" id="nom_contact_pro" onkeyup="verif_champ(id_champ = this.id , taille_condition = 1) ;" type="texte" placeholder="Ex: Dupont" required></input>
	<span id="ok_nom_contact_pro" class="verif_champ"></span><br /><br />
	 
	<label for="prenom_contact_pro" class="label_compte_resto"> Prenom : </label>
	<input class="input reset_input" id="prenom_contact_pro" onkeyup="verif_champ(id_champ = this.id , taille_condition = 1) ;" type="texte" placeholder="Ex: Jean"></input>
	<span id="ok_prenom_contact_pro" class="verif_champ"></span><br /><br />
	
	<label for="post_contact_pro" class="label_compte_resto">* Poste occupé : </label>
	<select id="post_contact_pro" class="input" required>
		<option value="Gérant">Gérant</option>
		<option value="PDG">PDG</option>
		<option value="Directeur général">Directeur général</option>
		<option value="Chef d'établissement">Chef établissement</option>
		<option value="Chef d'équipe">Chef équipe</option>
		<option value="Commercial">Commercial</option>
		<option value="Standard">Secrétaire</option>
	</select><br /><br />
	
	<label for="email_contact_pro" class="label_compte_resto">* Email pro : </label>
	<input class="input reset_input" id="email_contact_pro" onkeyup="verif_champ(id_champ = this.id , taille_condition = 'mail') ;" type="email" placeholder="Ex: donnemoifaim@gmail.com" required></input>
	<span id="ok_email_contact_pro" class="verif_champ"></span><br /><br />
	
	<label for="tel_contact_pro" class="label_compte_resto">* Numéro pro : </label>
	<input class="input reset_input" id="tel_contact_pro" onkeyup="verif_champ(id_champ = this.id , taille_condition = '10') ;" type="tel" placeholder="Ex : 06xxxxxxxx" required></input>
	<span id="ok_tel_contact_pro" class="verif_champ"></span><br /><br />
	
	<p class="texte_site">Selectionnez les jours où la personne est le plus disponible : </p>
	<br />
	

	<?php
	// Création du tableau des jours a selectionner 
	$tableau_jours = array('Lun' , 'Mar' , 'Mer' , 'Jeu' , 'Ven' , 'Sam' , 'Dim') ; 
	
	creer_dispo_bloc_jour_semaine($tableau_jours , 'disponibilite_pro' , 1) ; 
	?>
	<br /><br />
	<button id="button_valide_ajout_contact_pro" style="float:right" type="submit" class="reset_button choix_menu_compte_visiteur" >
		<img src="/imgs/ajout_contact_pro.<?php echo versionning('imgs/ajout_contact_pro.png') ; ?>.png" alt="Ajout contact pro" /><br />
		Valider ce contact
	</button>
</form><br />

<br /><br />

<?php 
// Si on est dans le compte premium
if(!empty($_SESSION['login']))
{?>
	<input id="input_id_modif_contact_pro" type="hidden" value="" />
<?php
}?>

<?php
if(!isset($_SESSION['login']))
{?>
<div class="bloc_button_suivant_processus" onclick="infos_complementaire_plus_tard('termine') ;" style="right:80px; cursor:pointer">
	<img src="../imgs/picto-suivant.png" alt="suivant" /><br>
	<span class="texte_site" >Terminer</span>
</div>
<?php
}?>