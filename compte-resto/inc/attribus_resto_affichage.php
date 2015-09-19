<?php
// Si l'attribus resto n'existe pas c'est que c'est surement un post en ajax
if(!isset($attribus_resto) && !empty($_GET['id_resto']))
{
	//connection à la bdd
	include ('../../include/configcache.php') ;
	include ('../../include/fonctions.php');
	
	$niveau_arborescence = '../../' ; 

	// On doit récupérer les données ici pour pas quelle soit visible par l'utilisateur // Sécurité 
	$id_resto = $_GET['id_resto'] ;
	
	if(is_numeric($id_resto))
	{
		$requete_attribus_resto = $bdd->prepare('SELECT telephone, mail_crypte,mail, site_internet, attribus FROM client WHERE id = :id') ;
		$requete_attribus_resto->execute(array(':id' => $id_resto)) ; 
		
		if($donnees_attrius_resto = $requete_attribus_resto ->fetch())
		{
			$attribus_resto = $donnees_attrius_resto ['attribus'] ;
			$telephone = $donnees_attrius_resto ['telephone'] ;
			$mail_crypte =  $donnees_attrius_resto['mail_crypte'] ; 
			$mail =  $donnees_attrius_resto['mail'] ; 
			$site_internet = $donnees_attrius_resto['site_internet'];	
		}
	}
	
}
else
{
	// Si ce n'est pas de l'ajax
	
	// Si c'est du php du menu-gourmant
	if(!empty($info_referencement_page))
	{
		$telephone = $info_referencement_page['telephone'] ;
		$mail = $info_referencement_page['mail'] ;
		$mail_crypte =  $info_referencement_page['mail_crypte'] ; 
		$site_internet = $info_referencement_page['site_internet']; 
	}
	else
	{
		$telephone = $_SESSION['telephone'] ;
		$mail =  $_SESSION['mail'] ;
		$mail_crypte =  $_SESSION['mail_crypte'] ; 
		$site_internet = $_SESSION['site_internet'];
	}
}
	// On check si l'appareil est un mobile pour mettre bien le mail 
	$mobile_device = check_appareil_mobile() ;
	
	$tableau_attribus =  affichage_attribut_resto($attribus_resto);

	// On affiche d'abord le prix moyen
	if($tableau_attribus['attribus_prix'] != 0)
	{
		if($tableau_attribus['attribus_prix'] == 1)
		{
			$prix_moyen = 'Moins de 10€'; 
		}
		else if($tableau_attribus['attribus_prix'] == 2)
		{
			$prix_moyen = 'Entre 10€ et 25€'; 
		}
		else if($tableau_attribus['attribus_prix'] == 3)
		{
			$prix_moyen = 'Plus de 25€'; 
		}
	?>
		<div  onclick="faire_apparaitre_title_picto('picto_attribus_prix<?php echo $tableau_attribus['attribus_prix']; ?>')" style="display:inline-block; cursor:pointer" title="<?php echo $prix_moyen; ?>">
			<p class="texte_site">Prix moyen : </p>
			<img src="/imgs/picto-prix.<?php echo versionning('imgs/picto-prix.png') ; ?>.png" alt="picto prix" />
			<?php
			if($tableau_attribus['attribus_prix'] > 1)
			{?>
				<img src="/imgs/picto-prix.<?php echo versionning('imgs/picto-prix.png') ; ?>.png" alt="picto prix" />
			<?php
			}
			else
			{?>
				<img src="/imgs/picto-non-prix.<?php echo versionning('imgs/picto-non-prix.png') ; ?>.png" alt="picto non prix" />
			<?php
			}
			if($tableau_attribus['attribus_prix'] > 2)
			{?>
				<img src="/imgs/picto-prix.<?php echo versionning('imgs/picto-prix.png') ; ?>.png" alt="picto prix" />
			<?php
			}
			else
			{?>
				<img src="/imgs/picto-non-prix.<?php echo versionning('imgs/picto-non-prix.png') ; ?>.png" alt="picto non prix" />
			<?php
			}
			?>
			<span id="title_picto_attribus_prix<?php echo $tableau_attribus['attribus_prix']; ?>"  class="title_picto" style="display:none"><?php echo $prix_moyen; ?></span>
		</div>
		<?php
	}
	
	// Affichage des attribus
	echo '<p class="texte_site">Caractéristiques resto :</p>' ; 
	echo $tableau_attribus['affichage_attribus'] ;

	// Si on peut contacter le resto
	if($tableau_attribus['attribus_numero'] || $tableau_attribus['attribus_contact'] == 1)
	{
		echo '<p class="texte_site">Contactez-les : </p>' ; 
		if($tableau_attribus['attribus_numero'] == 1)
		{
			?>
				<a href="tel:<?php echo $telephone; ?>">
					<img style="cursor:pointer" src="/imgs/picto-phone.<?php echo versionning('imgs/picto-phone.png') ; ?>.png" alt="picto phone" /><br />
				</a>
			<?php
			// On fait apparaitre le numéro de tel si il existe bien entendu 
			if(!empty($telephone))
			{
				echo '<span class="texte_site_noir">'.$telephone.'</span><br />' ; 
			}
		}
		if($tableau_attribus['attribus_contact'] == 1)
		{
			echo '<br />' ; 
			
			// On protège l'url que sur ordi sur mobile on met le mailto normal
			if($mobile_device != 1)
			{?>
				<a onclick="window.open(this.href, '' , '') ; return false;" href="contact_securise/contact.php?mail=<?php echo $mail_crypte; ?>">
					<img style="cursor:pointer" src="/imgs/picto-contact.<?php echo versionning('imgs/picto-contact.png') ; ?>.png" alt="picto contact" /><br />
				</a>
			<?php
			}
			else
			{?>
				<a href="mailto:<?php echo $mail; ?>">
					<img style="cursor:pointer" src="/imgs/picto-contact.<?php echo versionning('imgs/picto-contact.png') ; ?>.png" alt="picto contact" /><br />
				</a>
			<?php
			}
			echo '<span class="texte_site_noir">Envoyer un mail</span><br />' ;
		}
		if($tableau_attribus['attribus_site_internet'] == 1)
		{
			echo '<br />' ; 
			?>
				<a href="<?php echo $site_internet ; ?>" <?php if($mobile_device != 1){ echo 'target="_blank"' ; }?>>
					<img src="/imgs/picto-site-internet.<?php echo versionning('imgs/picto-site-internet.png') ; ?>.png" alt="picto site internet" /><br />
				</a>
			<?php
		}
	}