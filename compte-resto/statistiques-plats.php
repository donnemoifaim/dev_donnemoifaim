<?php
session_start();
// Récupération des sessions
include('../include/compte_acces.php') ; 
?>

<!DOCTYPE html>

<html>
	<head>
		<?php
		$meta_titre = 'Statistiques plats - DonneMoiFaim' ; 
		$meta_description = 'Retrouver l\'ensemble des statistiques disponibles pour comparer les plats qui fonctionnent le mieux' ;
		$meta_keywords = 'statistiques,plat, camember, analyse, pourcentage'  ;
		//Savoir si on référence la page
		$meta_robots = 'no-index' ; 
		include('../include/en-tete.php') ; 
		?>
	</head>

	<body>
		<?php
		include('../include/header.php') ;
		include('inc/menu_compte_resto.php');
		
		
			// On calcule le nombre de fois qu'un plat total du restaurant à été visionné
			$requete_nombre_total_resto = $bdd->prepare('SELECT SUM(nombre_vue) , SUM(nombre_interesse) FROM plat WHERE login = :login') ;
			$requete_nombre_total_resto->execute(array(':login' => $_SESSION['login'])) ;
			
			if($info_nombre_total_resto = $requete_nombre_total_resto->fetch())
			{
				$nombre_vue_totale_resto = $info_nombre_total_resto['SUM(nombre_vue)'] ; 
				$nombre_interesse_totale_resto = $info_nombre_total_resto['SUM(nombre_interesse)'] ; 
			} 
			
			// On calcule le nombre de fois qu'un plat à été visionné ormis ce plat
			$requete_nombre_total_site = $bdd->prepare('SELECT SUM(nombre_vue) , SUM(nombre_interesse) FROM plat WHERE login != :login') ;
			$requete_nombre_total_site->execute(array(':login' => $_SESSION['login'])) ;
			
			if($info_nombre_restant = $requete_nombre_total_site->fetch())
			{
				$nombre_vue_total_restant = $info_nombre_restant['SUM(nombre_vue)'] ; 
				$nombre_interesse_total_restant = $info_nombre_restant['SUM(nombre_interesse)'] ;
			} 
			
			// On initie tout à zero si c'est vide ! 
			if(empty($nombre_vue_totale_resto))
			{
				$nombre_vue_totale_resto = 0 ; 
			}
			if(empty($nombre_interesse_totale_resto))
			{
				$nombre_interesse_totale_resto = 0 ; 
			}
			// Si il y à que ces propres plats, beug donc on initie à zero
			if(empty($nombre_vue_total_restant))
			{
				$nombre_vue_total_restant = 0 ; 
			}
			// Idem si c'est vide au moins mettre 0
			if(empty($nombre_interesse_total_restant))
			{
				$nombre_interesse_total_restant = 0 ; 
			}
			?>
			<div style="text-align:center; width:100% ; overflow:hidden">
				<p class="titre"> Statistiques plats</p>
				
				<p class="texte_site">Pour augmenter votre part de visiteurs et clients potentiels, il vous suffit d'ajouter plus de plat. </p>
				
				<div style="display:inline-block; margin:auto; width:350px" id="chart_div_utilisation_appli"></div>
				<div style="display:inline-block; margin:auto; width:350px" id="chart_div_utilisation_client_potentiel"></div>
				
				<p class="texte_site">
					Répartition des visites sur vos plats : <br />
					<span class="texte_site_noir" style="font-size:12px" >Très intéressant pour voir les plats qui fonctionnent le mieux grâce au taux de convertion des visiteurs en clients potentiels</span>
				</p>
				
				<div style="width:100% ; overflow:auto">
					<table style="width:500px ;margin:auto ">
						<thead> 
							<tr>
								<th>Nom plat</th>
								<th>Nombre de vue</th>
								<th>Client potentiels</th>
								<th>Taux de conversion</th>
							</tr>
						</thead> 
						<tbody>
							<?php
							// On initialise les variable
							$nombre_vue_total = 0 ; 
							$nombre_interesse_total = 0 ;
							
							// On récupère les données des plats 
							$requete_info_plat = $bdd->prepare('SELECT nomplat, nombre_vue, nombre_interesse , idimage, etat, date_ FROM plat WHERE login = :login ORDER BY nombre_vue DESC') ;
							$requete_info_plat->execute(array(':login' => $_SESSION['login'])) ;
							
							while($info_info_plat = $requete_info_plat->fetch())
							{
								$info_info_plat = protection_array_faille_xss($info_info_plat) ;
								?>
								<tr>
									<td><?php echo $info_info_plat['nomplat'] ; ?></td>
									<td><?php echo $info_info_plat['nombre_vue'] ; ?></td>
									<td><?php echo $info_info_plat['nombre_interesse'] ; ?></td>
									<td><?php if($info_info_plat['nombre_interesse'] <= 0 || $info_info_plat['nombre_vue'] <= 0){echo '0' ; } else{echo number_format($info_info_plat['nombre_interesse'] / $info_info_plat['nombre_vue'] , 2) * 100 ;} ?> %</td>
								</tr>
								<?php
								
									$nombre_vue_total = $nombre_vue_total + $info_info_plat['nombre_vue']; 
									$nombre_interesse_total = $nombre_interesse_total + $info_info_plat['nombre_interesse'] ; 
							} 
							?>
						</tbody>
						<tfoot>
							<tr>
								<th>TOTAL</th>
								<th><?php echo $nombre_vue_total; ?></th>
								<th><?php echo $nombre_interesse_total; ?></th>
								<th><?php if($nombre_vue_total <= 0 || $nombre_interesse_total <= 0){echo '0' ; } else{echo number_format($nombre_interesse_total / $nombre_vue_total , 2) * 100 ;} ?> %</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div><br />
			
		<?php include('../include/footer.php'); ?>
		
		<!--Load the AJAX API-->
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script type="text/javascript">
	
	// On créer le tableau de données 
	tableau_donnee_chart = {
		nombre_vue_totale_site : <?php echo $nombre_vue_total_restant ; ?> ,
		nombre_vue_totale_resto : <?php echo $nombre_vue_totale_resto ; ?> ,
		nombre_interesse_totale_resto : <?php echo $nombre_interesse_totale_resto ; ?>, 
		nombre_interesse_total_restant : <?php echo $nombre_interesse_total_restant ; ?>
	};  

      // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});

		if(typeof(addEventListener) != 'undefined')
		{
			window.addEventListener("load", loadChartsGoogle , false) ;
		}
		else
		{
			loadEventIEcompatible('loadChartsGoogle') ; 
		}
		
		function loadChartsGoogle()
		{
			// On fait patienter car c'est assez long quand meme
			document.getElementsByTagName('body')[0].style.cursor = 'wait' ; 
			
			// Premier chart
			var tableauValeurChart1 = new Array (tableau_donnee_chart['nombre_vue_totale_resto'] , tableau_donnee_chart['nombre_vue_totale_site']),
			tableauTextChart1 = new Array ('Sur votre resto' , 'Sur les autres restos');
			
			// Second chart
			var tableauValeurChart2 = new Array  (tableau_donnee_chart['nombre_interesse_totale_resto'] , tableau_donnee_chart['nombre_interesse_total_restant']) , 
			tableauTextChart2 = new Array ('Sur votre resto' , 'Sur les autres restos') ;
			
			
			// On déssine les charts un par un 
			drawChart(document.getElementById('chart_div_utilisation_appli') , 'Nombre de fois q\'un plat a été visionné : ' , tableauValeurChart1 , tableauTextChart1) ;
			drawChart(document.getElementById('chart_div_utilisation_client_potentiel') , 'Clients potentiels qui ont consulté les informations restos' , tableauValeurChart2, tableauTextChart2) ;
			
			// Au bout de 1 secondes on sait que tout à chargé donc c'est bon
			setTimeout( function(){document.getElementsByTagName('body')[0].style.cursor = 'default' ; } , 1000) ; 
		}
      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart(elementToChart, titreChart, tableauValeurChart , tableauTextChart) {
	
        // Create the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Topping');
        data.addColumn('number', 'Slices');

		var longueur_tableau = tableauValeurChart.length ; 
		
		// boucle for pour parcourir les valeurs du tableau
		for(i=0 ; i < longueur_tableau ; i++)
		{
			data.addRows([
				[tableauTextChart[i] , tableauValeurChart[i]]
			]);
		}

        // Set chart options
        var options = {'title': titreChart,
                       'width':350,
                       'height':280 , 
					  colors : ['#db302d', '#96302F','#6F1C1B','#333']};

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(elementToChart);
        chart.draw(data, options);
      }
    </script>
	<script async type="text/javascript" src="../javascript/compte_resto.<?php echo versionning($fichier = 'javascript/compte_resto.js'); ?>.js"></script>
	<script async type="text/javascript" src="../javascript/general.<?php echo versionning($fichier = 'javascript/general.js'); ?>.js"></script>
  </body>
</html>