<p id="menu_compte_resto" class="menu_compte_resto" >
	<!-- Si il existe une session admin -->
	<?php
	if(!empty($_SESSION['admin']) && $_SESSION['admin'] == $_SESSION['login'])
	{
		// Compter le nombre de tache à faire
		$requete_tache = $bdd->query('SELECT COUNT(id) FROM tache_admin WHERE statut != 1 ORDER BY priorite') ;
				
		if($donnees = $requete_tache->fetch())
		{
			$tache_restante = $donnees['COUNT(id)'] ; 
		}
		
		// Voir le nombre d'erreur sur le site
		$requete_erreur = $bdd->query('SELECT COUNT(id) FROM erreur_site WHERE statut != 1 ORDER BY date_ajout') ;
		
		if($donnees_erreur = $requete_erreur->fetch())
		{
			$tache_restante = $tache_restante + $donnees_erreur['COUNT(id)'] ; 
		}
		?>
		<a href="../admin-cache/" class="choix_menu_compte_resto" >
			<img src="/imgs/picto-access-securise.<?php echo versionning('imgs/picto-access-securise.png') ; ?>.png" alt="Accès admin" />
			<?php 
			if(!empty($tache_restante))
			{
				echo '<span class="alertTache">'.$tache_restante.'</span>' ; 
			}?>
			<br />
			Administration
		</a>
	<?php
	}
	// Offre spécial ici c'est l'offre découverte
	if(!empty($_SESSION['offre_speciale']) && $_SESSION['offre_speciale'] != '' OR $_SESSION['offre_speciale'] != 0)
	{?>
		<span title="<?php echo $_SESSION['offre_speciale']; ?>" onclick="faire_apparaitre_offre_special('decouverte');" style="margin:0; cursor:pointer">
			<img style="cursor:pointer" src="../imgs/picto-offre-special.<?php echo versionning('imgs/picto-offre-special.png'); ?>.png" alt="picto offre special" />
			<?php
			if($_SESSION['offre_speciale'] == 'decouverte')
			{// Si c'est une offre découverte 
			?>
				<img style="cursor:pointer" src="../imgs/picto-decouverte.<?php echo versionning('imgs/picto-decouverte.png'); ?>.png" alt="picto decouverte" />
			<?php
			}?>
			<br />
			<span id="clique_voir_offre_special" class="texte_site">Cliquez pour voir l'offre spéciale</span>
		</span>
		<?php
		if($_SESSION['offre_speciale'] == 'decouverte')
		{?>
			<span style="display:none" id="offre_special_decouverte" class="texte_site" style="cursor:pointer" >Offre découverte : ajoutez votre premier plat gratuitement en ligne pendant 1 mois (dans la limite d'1 plat)</span><br />
		<?php
		}
		echo '<br />' ; 
	}
	?>
	<a href="ajout-de-plat.html" class="choix_menu_compte_resto" >
		<img src="/imgs/picto-ajouter.<?php echo versionning('imgs/picto-ajouter.png') ; ?>.png" alt="ajouter vos plats" /><br />
		Ajout plats
	</a>
	<a href="vos-plats.html" class="choix_menu_compte_resto" >
		<img src="/imgs/picto-categorie.<?php echo versionning('imgs/picto-categorie.png') ; ?>.png" alt="Voir vos plats" /><br />
		Vos plats
	</a>
	<a href="statistiques-plats.html" class="choix_menu_compte_resto" >
		<img src="/imgs/picto-statistiques.<?php echo versionning('imgs/picto-statistiques.png') ; ?>.png" alt="Voir les statistiques" /><br />
		Statistiques plats
	</a>
	<a href="factures.html" class="choix_menu_compte_resto">
		<img src="/imgs/picto-facture.<?php echo versionning('imgs/picto-facture.png') ; ?>.png" alt="Vos factures personnelles" /><br />
		Vos factures
	</a>
	<a href="parametres-compte.html" class="choix_menu_compte_resto" >
		<img src="/imgs/picto-parametres.<?php echo versionning('imgs/picto-parametres.png') ; ?>.png" alt="Vos données personnelles" /><br />
		Paramètres & contacts
	</a>
	<a href="deconnection.html" class="choix_menu_compte_resto" >
		<img src="/imgs/picto-deconnexion.<?php echo versionning('imgs/picto-deconnexion.png') ; ?>.png" alt="se deconnecter" /><br />
		Déconnexion
	</a>
</p>