<?php 

if(!isset($recherche_par_type))
{
	session_start(); 
	
	//connection à la bdd
	include ('../../include/configcache.php') ;
	include ('../../include/fonctions.php');
}

if(!isset($recherche_par_type))
{
	// Pour que le versionning fonctionne correctemment
	$niveau_arborescence = niveau_site() ;
}

// Résermé : Tous resto -> ville -> options complémentaires || Un type -> ville -> options complémentaire || idem plusieurs || Un restaurant en ligne => nom du resto => ville

if(!empty($_POST['recherche_par']) || !empty($recherche_par_type))
{
	if($_POST['recherche_par'] == 'tous_les_resto')
	{
		$requete_where = '' ; 
	}
	if($_POST['recherche_par'] == 'resto_type' || !empty($recherche_par_type))
	{
		if(!empty($_POST['resto_type']))
		{
			// Recherche par type basique
			$requete_where = 'type_resto = "'.$_POST['resto_type'].'"' ;
		}
		else if(!empty($recherche_par_type))
		{
			if(!empty($formate_ville))
			{
				$requete_where = 'type_resto = "'.$recherche_par_type.'" AND ville = "'.$formate_ville.'"' ;
			}
			else
			{
				$requete_where = 'type_resto = "'.$recherche_par_type.'"' ;
			}
		}
		else
		{
			$erreur .= '<span class="erreur">- Vous n\'avez sélectionné aucun type de restaurant</span>' ;
		}
	}
	if($_POST['recherche_par'] == 'resto_plusieurs_type')
	{
		if(!empty($_POST['resto_plusieur_type']))
		{
			// Entre parenthese pour pouvoir mettre les opérateur OR mis en js tranquillement
			$requete_where = '('.$_POST['resto_plusieur_type'].')' ;
		}
		else
		{
			$erreur .= '<span class="erreur">- Vous n\'avez sélectionné aucun type de restaurant</span>' ;
		}
	}
	// Sélection par nom du restaurant
	if($_POST['recherche_par'] == 'nom_resto')
	{
		if(!empty($_POST['nom_resto']))
		{
			$requete_where = 'nomresto = "'.$_POST['nom_resto'].'"' ; 
		}
		else
		{
			$erreur .= '<span class="erreur">- Vous n\'avez entré aucun restaurant</span>' ;
		}
	}
	
	// On ajoute une ville si il y en à une
	if(!empty($_POST['ville']))
	{
		// Recherche la ville 
		if($requete_where == '')
		{
			$requete_where .= 'ville = "'.$_POST['ville'].'"' ;
		}
		else
		{
			$requete_where .= ' && ville = "'.$_POST['ville'].'"' ;
		} 
	}
	
	// On rajoute que l'on veut que le client soit un client qui puisse etre sur l'annuaire
	if($requete_where != '')
	{
		$requete_where .= ' && statut = 1' ;
	}
	else
	{
		$requete_where = ' statut = 1' ;
	}
	
	// Si il n'y à pas d'order by 
	if(!isset($requete_order_by))
	{
		$requete_order_by = 'ville' ; 
	}
	
	// On va sécuriser la requete_where un maximum // double requete a faire après 
	$requete_where = str_replace('SELECT' ,  '' , $requete_where) ;
	$requete_where = str_replace('UPDATE' ,  '' , $requete_where) ;
	$requete_where = str_replace('DELETE' ,  '' , $requete_where) ;
	$requete_where = str_replace(';' ,  '' , $requete_where) ;
	
	$requete_resto = $bdd->query('SELECT login,id,nomresto, image_facade, type_resto, ville, adressresto FROM client WHERE '.$requete_where.' ORDER BY '.$requete_order_by) ;
	
	$nombre_resto = 0 ; 
	
	?>
	<p id="nombre_resultat_annnuaire" class="texte_site" style="margin-top:20px;"></p>
	<?php
	
	while($info_resto = $requete_resto->fetch())
	{
		// Réfénrence aux options complémentaire qui se base sur les attribus
		if(!empty($_POST['voir_attribus']))
		{
			$attribus_ok = 0 ; 
			
			$requete_attribus = $bdd->prepare('SELECT attribus FROM client WHERE id = :id_client') ; 
			$requete_attribus->execute(array(':id_client' => $info_resto['id'])) ; 
			
			// On va récupérer les attribus pour voir si cela sont activé
			if($info_attribus = $requete_attribus->fetch())
			{
				$tableau_attribus = explode('--' , $info_attribus['attribus']) ;
				
				$nombre_attribus = count($tableau_attribus) ; 
				
				for($i=0 ; $i < $nombre_attribus; $i++)
				{
					// C'est =1 parce que j'ai la fleimme de refaire un autre explode 
					
					if($tableau_attribus[$i] ==  $_POST['voir_attribus'].'=1')
					{
						$attribus_ok = 1 ;
					}
				}
			} 
			
			if($attribus_ok == 1)
			{
				// On affiche le resto si l'attribus est présent
				$afficher_resto = 1 ; 
			}
			else
			{
				// Si l'attribus n'est pas présent on affiche pas ! 
				$afficher_resto = 0 ; 
			}
		}
		else
		{
			// Si on ne veut pas voir la livraison
			$afficher_resto = 1 ; 
		}
		// Il faut que le premier afficher soit à 1 sinon cça ne sert à rien de continuer
		if(!empty($_POST['voir_reduction']) && $afficher_resto == 1)
		{
			// Il ne faut mettre que celui qui à vu les réductions 
			$requete_reduction = $bdd->prepare('SELECT idimage FROM reductions WHERE login = :login') ; 
			$requete_reduction->execute(array(':login' => $info_resto['login'])) ;
			
			if($info_reduction = $requete_reduction->fetch())
			{
				$tableau_idimage_reduction = $info_reduction['idimage'] ;
				
				if($tableau_idimage_reduction != '')
				{
					$afficher_resto = 1 ;
				}
				else
				{
					$afficher_resto = 0;
				}
			}
			else
			{
				$afficher_resto = 0;
			}
		}
		
		if(!isset($afficher_resto))
		{
			// Si il n'y à aucune contrainte on affiche le resto
			$afficher_resto = 1;
		}
		
		// Si il à le droit d'etre affiché
		if($afficher_resto == 1)
		{
		?>
			
			<div class="fiche_resto" style="text-align:center">
				<p style="overflow:hidden; height:100px; cursor:pointer;">
					<img style="margin-top:-70px; width:100%" onclick="apercu_image('/compte-resto/image-resto/<?php echo $info_resto['image_facade']; ?>.<?php echo versionning('compte-resto/image-resto/'.$info_resto['image_facade'].'.jpg') ; ?>.jpg') ; " src="/compte-resto/image-resto/<?php echo $info_resto['image_facade']; ?>.<?php echo versionning('compte-resto/image-resto/'.$info_resto['image_facade'].'.jpg') ; ?>.jpg" alt="<?php echo $info_resto['type_resto'].' '.$info_resto['nomresto'] .' '.$info_resto['ville']?> ">
				</p>
				<div style="padding-left:10px; padding-right:10px;">
					<p class="titre"><?php echo $info_resto['nomresto'] ; ?> - <?php echo $info_resto['type_resto'] ; ?></p>
					<p class="texte_site_noir"><?php echo $info_resto['adressresto'] ; ?> - <?php echo $info_resto['ville'] ; ?></p>
				</div>
				<br />
				<?php
					// Afficher les plats du resto
					$requete_tout_plat_resto = $bdd->prepare('SELECT id, idimage, nomplat FROM plat WHERE etat = 1 && login = :login') ; 
					$requete_tout_plat_resto->execute(array(':login' => $info_resto['login'])) ; 
			
					while($info_tout_plat_resto = $requete_tout_plat_resto->fetch())
					{?>
						<p style="width:100px; height:100px" class="preview_image texte_site" id="image_plat<?php echo $info_tout_plat_resto['id'] ;?>">
							<a href="/<?php echo $info_tout_plat_resto['idimage'] ; ?>.html" title="<?php echo $info_tout_plat_resto['nomplat'] ;?>">
								<img class="apercu_image_miniature" src="/plats/miniature/<?php echo $info_tout_plat_resto['idimage'] ; ?>.<?php echo versionning('plats/miniature/'.$info_tout_plat_resto['idimage'].'.jpg') ?>.jpg" />
							</a>
							<span class="texte_site"><?php echo $info_tout_plat_resto['nomplat']; ?></span>
						</p>
					<?php
					}
				?>
				<br /><br />
			</div>
	<?php
		}
		// Seulement si le client resto à été affiché
		if($afficher_resto == 1)
		{
			$nombre_resto++ ;
		}
	}
	if(!empty($recherche_par_type))
	{
	?>
		<script>
			// En code normal cela va s'activer tout seul
			document.getElementById('nombre_resultat_annnuaire').innerHTML = 'Nombre de résultats : <?php echo $nombre_resto; ?>' ; 
		</script>
	<?php
	}
	else
	{?>
		<input id="input_nombre_resultat_annuaire" type="hidden" value="Nombre de résultats : <?php echo $nombre_resto; ?>" />
	<?php
	}
}
	
?>