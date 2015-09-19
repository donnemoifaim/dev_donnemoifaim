<?php 

session_start(); 

//connection à la bdd
include ('../../include/configcache.php') ;
include ('../../include/fonctions.php');

if(!empty($_SESSION['login_visiteur'] ))
{
	$login_visiteur = $_SESSION['login_visiteur'] ;
	
	$req_jaime_affichage = $bdd->prepare("SELECT jaime FROM utilisateur WHERE login = :login");
	$req_jaime_affichage->execute(array(':login' => $login_visiteur)) ;
	?>
	<ul id="ul_mes_jaimes">
	<?php
		if ($info_jaime_affiche = $req_jaime_affichage->fetch())
		{
			$tableau_jaime_check = explode(',' , $info_jaime_affiche['jaime']) ; 
			$taille_tableau = count($tableau_jaime_check) ; 
			
			// Affichage de chaque id des jaimes
			for($i=0 ; $i < $taille_tableau; $i++)
			{
				$id_plat_jaime = $tableau_jaime_check[$i] ;
				
				$req_jaime_id_plat = $bdd->prepare("SELECT p.id id, p.idimage idimage, p.nomplat nomplat, c.ville ville FROM plat p INNER JOIN client c ON p.login = c.login WHERE p.id = :id_plat_jaime ORDER BY c.ville");
				$req_jaime_id_plat->execute(array(':id_plat_jaime' => $id_plat_jaime)) ;
				
				if($donnees_plats = $req_jaime_id_plat->fetch())
				{
					//htmlentities sur tout les array sensible pour éviter les failles xss
					$donnees_plats = protection_array_faille_xss($donnees_plats) ;
	
					$idimage = $donnees_plats['idimage'] ;
					$id_plat = $donnees_plats['id'];
				?>
					<div id="jaime_plat<?php echo $id_plat ;?>" class="bloc_view_jaime_plat">
						<li class="preview_plat preview_plat_jaime" >
							<a href="<?php echo $idimage ?>.html" onclick="if(history.pushState){nom_image_history = <?php echo $id_plat ; ?> ;chargementimage(); return false;}">
								<img width="100%" height="100%" src="plats/miniature/<?php if(file_exists('../../plats/'.$idimage.'.jpg')){ echo $idimage.'.'.versionning($fichier = '../../plats/'.$idimage.'.jpg') ;} else {echo 'image_supp';} ?>.jpg"/>
							</a>
						</li>
						<span class="texte_site_noir contenu_infos_text_jaime"><?php echo $donnees_plats['nomplat'].' - '.$donnees_plats['ville'] ; ?></span>
						<img style="position:absolute;top:10px; right:10px" onclick="sup_jaime_plat(idimage = '<?php echo $id_plat ; ?>');" class="picto_supp" src="imgs/picto-supp.png" alt="j'aime plus" />
					</div>
			<?php
				$req_jaime_id_plat->closeCursor() ; 
				}
			}
			$req_jaime_affichage->closeCursor() ; 
			
			$au_moins_un_jaime = 1 ; 
		}?> 
	</ul>
	<?php
	//Si il n'y a pas de plat en favoris
	if(!isset($au_moins_un_jaime))
	{?>
		<div style="text-align:center">
			<p>Vous ne possédez aucun plat en favoris 
				<br /><img src="imgs/non_favoris.png" alt="explication favoris">
			</p>
		</div>
	<?php
	}
}
else
{
	// Permet de dire que l'utilisateur n'est pas connecté
	echo 'non_connecte' ; 
}
