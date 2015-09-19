<?php

// En gros si c'est un appel en ajax
if(!empty($_POST['id_plat_actuelle']))
{
	session_start();
	
	//connection à la bdd
	include('../../include/configcache.php');
	include('../../include/fonctions.php'); 
	
	$plat_actuel = $_SESSION['image_actuelle'] ; 
	$login_resto_actuel = $_SESSION['login_client_actuel'] ;
}
elseif(!empty($info_referencement_page))
{
	$plat_actuel = $info_referencement_page['id'] ; 
	$login_resto_actuel = $info_referencement_page['login']; 
}
?>
	<ul>
		<?php
		$reponse_tous_plat = $bdd->prepare('SELECT idimage,id, nomplat,abo, date_  FROM plat WHERE login = :login && id != :id && etat = 1 ORDER BY date_ DESC');
		$reponse_tous_plat->execute(array(':login' =>  $login_resto_actuel , ':id' => $plat_actuel)) ;

		while ($donnees_tout_plat = $reponse_tous_plat->fetch())
		{
			//htmlentities sur tout les array sensible pour éviter les failles xss
			$donnees_tout_plat = protection_array_faille_xss($donnees_tout_plat) ;
			
			$idimage = $donnees_tout_plat['idimage'] ;
			$id_plat = $donnees_tout_plat['id'] ;
			$nomplat = $donnees_tout_plat['nomplat'] ;
			$date_favoris = date('d-m-Y' , $donnees_tout_plat['date_']) ;
			
			if(!empty($partie_admin_voir_plat_client))
			{
				//on inclus les tarrifs pour pouvoir changer les abonnements des plats
				include('../include/offre_tarrif.php') ;
				?>
				<a style="background: url('/plats/miniature/<?php echo $idimage ;?>.jpg') no-repeat; background-size:cover" title="<?php echo $nomplat; ?>" class="apercu_autre_plat" href="/<?php echo $idimage?>.html" target="_blank" ></a>
				<!-- on met un select pour mettre les abonnement que l'on veut -->
				<select style="width:100px" class="input" id="select_abonnement_formule_admin" onchange="changerAbonnementPlat(<?php echo $donnees_tout_plat['id']; ?> , this.value);">
					<?php
						for($i=1 ; $i <= $nombre_abonnement_formule; $i++)
						{?>
							<option <?php if($donnees_tout_plat['abo'] == $i){echo 'selected' ;} ; ?> value="<?php echo $i; ?>"><?php echo $temps_abonnement_formule[$i] ; ?> mois</option> 
						<?php
						}
					?>
				</select>
				<img style="display: none;" id="ok_changement_abonnement<?php echo $donnees_tout_plat['id']; ?>" src="../imgs/okchargement.png" alt="picto ok">
				<br />
			<?php
			}
			else
			{?>
				<a style="background: url('/plats/miniature/<?php echo $idimage ;?>.jpg') no-repeat; background-size:cover" title="<?php echo $nomplat; ?>" class="apercu_autre_plat" href="<?php echo $idimage?>.html" onclick="if(history.pushState){nom_image_history ='<?php echo $id_plat ; ?>' ;chargementimage(); return false;}"></a>
			<?php
			}
		}
		?>
	</ul> 