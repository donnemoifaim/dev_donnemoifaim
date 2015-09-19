<p id="menu_compte_admin">
	<a href="index.php" class="choix_menu_compte_resto" >
		<img src="/imgs/picto-tache.<?php echo versionning('imgs/picto-tache.png') ; ?>.png" alt="liste des taches" /><br />
		Liste des taches
	</a>
	<a href="client-resto.php" class="choix_menu_compte_resto" >
		<img src="/imgs/picto-client.<?php echo versionning('imgs/picto-client.png') ; ?>.png" alt="Liste des clients" /><br />
		clients resto
	</a>
	<a href="demarchage-commercial.php" class="choix_menu_compte_resto" >
		<img src="/imgs/picto-parcours.<?php echo versionning('imgs/picto-parcours.png') ; ?>.png" alt="Liste des clients" /><br />
		Démarcharge commercial
	</a>
	<a href="../../compte-resto/ajout-de-plat.html" class="choix_menu_compte_resto" >
	<img src="/imgs/picto-previous.<?php echo versionning('imgs/picto-previous.png') ; ?>.png" alt="Revenir au compte normal" /><br />
	retour compte normale
	</a>
	<a href="deconnexion.php" class="choix_menu_compte_resto" >
		<img src="/imgs/picto-deconnexion.<?php echo versionning('imgs/picto-deconnexion.png') ; ?>.png" alt="se deconnecter" /><br />
		Déconnexion
	</a>
</p>
<br />

<!-- insertion du token -->
<input id="id_token_admin" type="hidden" value="<?php echo $_SESSION['token']; ?>" />