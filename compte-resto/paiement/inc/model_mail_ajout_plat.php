<?php
$heure = date("H");

//on cherche a savoir si c'est le soir ou la journée
if($heure > 19)
{
	$moment_journee = 'Bonsoir';
	$aurevoir = 'soirée';
}
else
{
	$moment_journee = 'Bonjour';
	$aurevoir = 'journée';
}

$option_text = '' ; 
$option_html = '' ;

if($abonnement == 'abonnement')
{
	$message_duree = '' ; 
}
else
{
	$message_duree = 'pour une durée de '.$temps_abonnement_formule[$abonnement].' mois' ;
}

// verification si l'option facebook à été ajouté
if(!empty($option_facebook_active)) 
{
	$option_text .= '- Offre Facebook, un mail vous sera envoyé lors de la création du statut sur Facebook. Cela peut prendre 48h au maximum.
	
	' ;
	$option_html .= '<br /> - Offre Facebook, un mail vous sera envoyé lors de la création du statut sur facebook. Cela peut prendre 48h au maximum.<br />' ; 
}

$message_txt = $moment_journee.' '.$login.',
Les plats suivants ont bien été ajoutés à votre compte '.$message_duree.'  : 


'.$ensemble_nom_plat_text.'


Vos options supplémentaires :

'.$option_text.'

Vous pouvez dès à présent voir, modifier, avoir accès aux statistiques et au temps d\'abonnement restant de l\'ensemble de ces images sur votre compte resto. 

Etant disponibles 24h/24, n\'hésitez pas à nous contacter en cas de besoin.

Nous vous remercions encore pour votre confiance et vous souhaitons une agréable '.$aurevoir.'.

L\'équipe DonneMoiFaim.' ; 

$message_html = '
<html>
	<head>
	<style>
		strong{color:#db302d}
		p{color:#333}
	</style>
	</head>
	<body>
		<p>
			'.$moment_journee.' '.$login.',<br /><br />
			Les plats suivants ont bien été ajoutés à votre compte '.$message_duree.' :<br /><br />
			'.$ensemble_nom_plat_html.'<br />
			
			Vos options supplémentaires :<br />
			
			'.$option_html.'<br />
			
			Vous pouvez dès à présent voir, modifier, avoir accès aux statistiques et au temps d\'abonnement restant de l\'ensemble de ces images sur votre compte resto. <br /><br />
			
			Etant disponibles 24h/24, n\'hésitez pas à nous contacter en cas de besoin.<br /><br />
			
			Nous vous remercions encore pour votre confiance et vous souhaitons une agréable '.$aurevoir.'.<br /><br />
			
			<strong>L\'équipe de DonneMoiFaim.</strong>
			<a href="'.$protocole_site.''.$_SERVER['HTTP_HOST'].'"><img src="'.$protocole_site.''.$_SERVER['HTTP_HOST'].'/imgs/logo-donnemoifaim.png" alt="logo donnemoifaim" /></a>
		</p>
	</body>
</html>';