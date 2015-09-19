<?php //inclusion du fichier config et de fonction pour l'ensemble des pages si il n'ont pas été déjà inclus comme dans le menu-gourmand

if(!isset($config_deja_include))
{
	include('configcache.php'); 
}
if(!isset($fonction_deja_include))
{
	include('fonctions.php') ; 
}

// On regarde si le client est un mobile ou pas
$mobile_device = check_appareil_mobile();

// Parfois on peut avoir besoin de le définir nous meme exemple de l'url rewritting avec des slash 
if(!isset($niveau_arborescence))
{
	// Initiation de la fonction qui permet de calculer l'arboresence
	$niveau_arborescence = niveau_site() ; 
}

// On dit qu'on à déjà vu l'annonce de l'app pour éviter qu'elle réapparaisse si on l'a déjà vu une fois, ou bien qu'on est déjà sur l'appli ! 
if(!empty($_SESSION['annonce_app_deja_vu']) || $_SERVER['HTTP_X_REQUESTED_WITH'] == "fr.donnemoifaim.donnemoifaim")
{?>
	<script>annonce_app_deja_vu = 1 ; </script>
<?php
}
else
{
	$_SESSION['annonce_app_deja_vu'] = 1 ; 
}
?>
<title><?php echo $meta_titre ; ?></title>
<meta charset="utf-8" />
<meta name="description" content="<?php echo $meta_description; ?>"/>
<meta name="keywords" content="<?php echo $meta_keywords; ?>" />
<!-- meta google verification https -->
<meta name="google-site-verification" content="ACyn_SsTPcmQTNFI_tdJv2cMUa7_C796_gRCeCuU5Us" />
<!-- meta bing -->
<meta name="msvalidate.01" content="BB9E5BD4FCF80FF803BE868E454C35E3" />
<!-- meta wot -->
<meta name="wot-verification" content="0fe4bb6dba168437e3df"/>

<script>
	// Création de la variable javascript pour récupérer l'adresse du site
	nom_domaine_site = '<?php echo $protocole_site.''.$_SERVER['HTTP_HOST'] ; ?>' ;
	
	// Fonction lorsque javascript est chargé, important de mettre ici pour le charger en premier
	function loadScriptAfterJquery(fonction , arguments)
	{
		// Si il n'y a pas d'argument ont met arguments a vide
		if(typeof(arguments) == '')
		{
			var arguments = '' ; 
		}
		// Tant que jquery n'est pas load 
		if(typeof(jQuery) != 'undefined')
		{
			window[fonction](arguments) ;		
		}
		else
		{
			// On relance avec un petit 10 milliseconde entre chaque pour pas faire trop travailler le processeur
			setTimeout(function(){loadScriptAfterJquery(fonction , arguments) ;} , 10) ;		
		}
	}
</script>

<?php
// Si c'est le nom de domaine de test on empeche l'indexisation
if($_SERVER['HTTP_HOST'] == 'dev.donnemoifaim.fr')
{?>
	<meta name="robots" content="noindex"/><?php
}
else
{?>
	<meta name="robots" content="<?php echo $meta_robots; ?>"/><?php
}

// Page actuelle du site 
$page_actuelle = page_actuelle() ;

echo '<script>page_actuelle_site = \''.$page_actuelle.'\' ; </script>'; 
 
if(!empty($info_referencement_page))
{ 
	$meta_image =  $protocole_site.''.$_SERVER['HTTP_HOST'].'/plats/'.$info_referencement_page['idimage'].'.jpg' ;
	$meta_type_image = 'image/jpeg' ;
	$taille_image_plat = getimagesize($protocole_site.''.$_SERVER['HTTP_HOST'].'/plats/'.$info_referencement_page['idimage'].'.jpg') ; 
	$meta_width_image = $taille_image_plat[0] ;
	$meta_height_image = $taille_image_plat[1] ;
	$meta_url = $protocole_site.''.$_SERVER['HTTP_HOST'].'/'.$info_referencement_page['idimage'].'.html' ;
} 
else 
{
	$meta_image = $protocole_site.''.$_SERVER['HTTP_HOST'].'/imgs/logo-dmf.png' ; 
	$meta_type_image = 'image/png' ;
	$meta_width_image = '' ;
	$meta_width_image = 518 ;
	$meta_height_image = 519 ;
	$meta_url = $protocole_site.''.$_SERVER['HTTP_HOST'].'/'.$_SERVER['REQUEST_URI'] ;
} 

$meta_id_app_facebook = "1462284764045188" ; 
?>

<script>
	meta_id_app_facebook = "<?php echo $meta_id_app_facebook ; ?>" ; 
</script>

<!-- facebook meta -->
<meta property="og:image" content="<?php echo $meta_image ?>" />
<meta property="og:image:type" content="<?php echo $meta_type_image ; ?>">
<meta property="og:image:width" content="<?php echo $meta_width_image ;?>">
<meta property="og:image:height" content="<?php echo $meta_height_image ; ?>">
<meta property="og:title" content="<?php echo $meta_titre ; ?>" />
<meta property="og:url" content="<?php echo $meta_url ; ?>" />
<meta property="og:description" content="<?php echo $meta_description ; ?>" />
<meta property="og:type" content="website" />
<meta property="fb:app_id" content="<?php echo $meta_id_app_facebook ; ?>" />
<meta property="og:site_name" content="DonneMoiFaim" />

<!-- twitter meta -->
<meta name="twitter:card" content="summary" />
<meta name="twitter:url" content="<?php echo $meta_url ; ?>" />
<meta name="twitter:title" content="<?php echo $meta_titre ; ?>" />
<meta name="twitter:description" content="<?php echo $meta_description ; ?>" />
<meta name="twitter:image" content="<?php echo $meta_image ?>" />
<meta name="twitter:site" content="@donnemoifaim" />

<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

<link href="/style/style.<?php echo versionning($fichier = 'style/style.css'); ?>.css" rel="stylesheet" title="Style"  />

<!-- spécification du style pour l'inpression, on le met ici pour éviter d'avoir une dexieme feuille de style avec rien dedans -->
<style media="print">
	#bloc_impression_bon_reduction{display:block;}
	#button_donnemoifaim, #bandeau_reduction, #bloc_montrer_bon_reduction, #bloc_image_plat, #button_screen_ok, .bloc_apparait{display:none}
	#contenu_bloc_full_screen{width:100%; background-color:white}
	#span_reduction_valable_chez , #titre_bon_reduction{color:#333}
	#image_plat_miniature_bon_reduction{display:block}
</style>

<link rel="icon" href="/imgs/favicon.<?php echo versionning($fichier = 'imgs/favicon.png'); ?>.png" />
<!--[if lte IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<!--[if lte IE 8]>
	<style>
		.preview_liste_plats_compte , .input , .input_submit{border:2px solid #888}
	</style>
<![endif]-->

<div class="banniere_application" id="banniere_application_android" style="display:none; position:fixed ; bottom:-100%">
	<span onclick="faire_apparaitre_banniere_application('android') ; " class="petite_croix_fermer_bloc">x</span>
	<br />
	<div class="div_annonce_application">
		<p class="titre" style="font-size:16px; color:white"> DonneMoiFaim est aussi disponible sur android !</p>
	</div>
	<div class="div_annonce_application">
		<a href="https://play.google.com/store/apps/details?id=fr.donnemoifaim.donnemoifaim">
			<img src="/imgs/android-chef.<?php echo versionning('imgs/android-chef.png') ; ?>.png" alt="application DMF android" />
		</a>
	</div>
</div>