<?php
if(!empty($_POST['methode']))
{
	$methode = $_POST['methode'] ; 
	// Si il y a le post c'est que ca a été envoyé
	if($_POST['methode'] == 'ajout-de-plat')
	{
		$labelle_ajout = 'Ajouter mes images';
		$envoi_multiple = 1;
	}
	else if($_POST['methode'] == 'facade_resto' )
	{
		$labelle_ajout = 'Votre façade resto';
	}
	else if($_POST['methode'] == 'modif-de-plat')
	{
		$labelle_ajout = 'Nouvelle Image';
	}
	
	include('ajout_image.php') ;
}

if(!empty($_GET['methode']))
{
	$methode = $_GET['methode'] ;
	
	if($_GET['methode'] == 'ajout-de-plat')
	{
		$labelle_ajout = 'Ajouter mes images'; 
		$envoi_multiple = 1; 
	}
	else if($_GET['methode'] == 'facade_resto')
	{
		$labelle_ajout = 'Votre façade resto';
	}
	else if($_GET['methode'] == 'modif-de-plat')
	{
		$labelle_ajout = 'Nouvelle Image';
	}
}?>

<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<meta name="ROBOTS" content="NOINDEX,NOFOLLOW" />
	<meta http-equiv="ROBOTS" content="NOINDEX,NOFOLLOW" />

	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
		<!--[if lt IE 8]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body>
	<br />
	<form style="display:inline-block; width:250px; margin:0 ; position:relative;"  id="formulaire<?php echo $methode; ?>" action="iframe_upload.php" method="POST" enctype="multipart/form-data" >
		<input id="fichier_upload<?php echo $methode ; ?>" name="monfichier" class="input_file_original" style="z-index:20;position:absolute; height:50px;width:100%; max-width:180px;opacity:0; filter:alpha(opacity=0);" type="file" />
		<input type="submit" style="box-shadow:1px 2px 5px #333;cursor:pointer;background-color:#db302d; color:white;z-index:19;position:relative;height: 50px;width:100%; max-width:180px; border-radius: 3px; font-weight:bold; font-size:14px; border:none" value="<?php echo $labelle_ajout; ?>" onclick="return false;" /><br /><br />
		<input type="hidden" name="iframe_upload" value="1" />
		<?php 
		if($_GET['methode'] == 'facade_resto')
		{?>
			<input type="hidden" name="facade_resto" value="1" />
		<?php
		}
		if($_GET['methode'] == 'modif-de-plat')
		{?>
			<input type="hidden" name="idimagemodif" value="1" />
		<?php
		}
		if(!empty($envoi_multiple))
		{?>
			<input type="hidden" name="envoi_multiple" value="<?php echo $methode; ?>" />
		<?php
		}?>
		<input type="hidden" name="methode" value="<?php if(!empty($_GET['methode'])){echo $_GET['methode'] ; }else if(!empty($_POST['methode'])){echo $_POST['methode'] ; } ?>" />
		
		<img style="display:none; width:25px" id="chargement<?php echo $methode; ?>" src="../../imgs/chargement.gif" alt="chargement" />
		<img style="display:none; width:25px" id="retour_chargement<?php echo $methode; ?>" src="../../imgs/nonchargement.png" alt="chargement" /><br />
		<?php 
		
		if(!empty($erreur)) 
		{
		?>
			<span id="envoi_ok<?php echo $methode; ?>" style="color:red; opacity:1" >Format du fichier invalide</span>
			<script>
				document.getElementById('retour_chargement<?php echo $methode; ?>').style.display='inline'; 
				document.getElementById('fichier_upload<?php echo $methode; ?>').onclick = function () {document.getElementById('retour_chargement<?php echo $methode; ?>').style.display='none' ; document.getElementById('fichier_non_autorise<?php echo $methode; ?>').style.display='none' ;}
			</script>
		<?php
		}
		else if(!empty($envoi_ok))
		{?> 
			<span id="envoi_ok<?php echo $methode; ?>" style="color:green; opacity:1" >Votre fichier à bien été envoyé !</span>
			<script>
				document.getElementById('retour_chargement<?php echo $methode; ?>').style.display='inline'; 
				document.getElementById('retour_chargement<?php echo $methode; ?>').src='../../imgs/okchargement.png' ;
				document.getElementById('fichier_upload<?php echo $methode; ?>').onclick = function () {document.getElementById('retour_chargement<?php echo $methode; ?>').style.display='none' ;}
				<?php
				if($_POST['methode'] == 'ajout-de-plat')
				{?>
					parent.ajout_bloc_image(<?php echo $_SESSION['id_image_max'.$methode] ; ?> , '<?php echo $name_final; ?>' , '<?php echo $methode ; ?>') ;
				<?php
				}
				elseif($_POST['methode'] == 'facade_resto')
				{?>
					parent.AppendFacadeRestoBloc('<?php echo $name_final; ?>') ; 
				<?php
				}
				elseif($_POST['methode'] == 'modif-de-plat')
				{?>
					parent.AppendImageModif('<?php echo $name_final; ?>') ;
				<?php
				}?>
			</script> 
		<?php
		}
		if(!empty($_FILES['monfichier']))
		{?>
		<script>
			function disparition()
			{
				var envoi_ok = document.getElementById('envoi_ok<?php echo $methode; ?>') ;
				var retour_chargement = document.getElementById('retour_chargement<?php echo $methode; ?>') ;
			
				envoi_ok.style.opacity = envoi_ok.style.opacity - 0.1 ;
				retour_chargement.style.opacity =  envoi_ok.style.opacity - 0.1 ;

				if(envoi_ok.style.opacity != 0.10000000000000014)
				{
					setTimeout (disparition , 50) ; 				
				}
				else
				{
					envoi_ok.style.display="none" ;
					retour_chargement.style.display="none" ;
				}
			}
			setTimeout(disparition, 1000);
		</script>
		<?php
		}
		?> 
		
	</form>
	
	<script>
		var	formulaire = document.getElementById('formulaire<?php echo $methode; ?>'),
		fichier_upload = document.getElementById('fichier_upload<?php echo $methode; ?>');  
		fichier_upload.onchange = function(){formulaire.submit(); document.getElementById('chargement<?php echo $methode; ?>').style.display='inline';} ; 
	</script> 
</body> 
</html>