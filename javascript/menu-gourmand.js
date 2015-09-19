// Objet permettant la navigation via HTML HISTORY - si il existe déjà c'est qu'il à déjà été défini, exemple dans le menu-gourmand pour une image unique
if(typeof(state_idimage) != 'undefined')
{}
else
{
	state_idimage = [];
}

taille_avis_voir_plus = 150 ;
nombre_plat_vue = 0 ;

// Pour les applications style retour navigateur savoir que c'est le menu-gourmant
page_actuelle_site = 'menu-gourmand' ;

// Fonction de la creation du Xhr 

function voir_tout_plats(id_plat_actuelle)
{
	var xhr = creation_xhr();
	
	xhr.open ('POST', 'ajax/menu_gourmand/voir_tout_plats.php');
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send('id_plat_actuelle=' + id_plat_actuelle);
	
	//Récupération du rapport de l'envoi
	xhr.onreadystatechange = function() 
	{
		//Requête envoyé ( == 4 ) et tout à bien été reçu ( == 200 ) 
		if (xhr.readyState == 4 && xhr.status == 200)
		{
			document.getElementById('ensemble_plat_resto').innerHTML = xhr.responseText ;
		}
		
	}
}
function voir_photo_entier()
{ 
	// Si le header n'est déjà plus la, on le remet 
	if(document.getElementsByTagName('header')[0].style.display == 'none')
	{
		tout_voir();
	}
	else
	{
		cacher_tout_entier();
	}
}
function ameliorer_lisibilite_menu_gourmand() 
{
	// Si c'est le tuto il faut pas que ca gene
	if(document.getElementById('tuto_premiere_visite') && document.getElementById('tuto_premiere_visite').style.display == 'none')
	{
		if(document.getElementById('bloc_info_plat').style.display == 'none')
		{
			// Si bloc_info_coulissant n'est pas ouvert alors on fait apparaitre le bloc_info_plat
			if(document.getElementById('bloc_info_coulissant').style.display == 'none')
			{
				$('#bloc_info_plat').fadeIn(); 
				$('#button_donnemoifaim').fadeIn(); 
			}
		}
		else
		{
			$('#bloc_info_plat').fadeOut(); 
			$('#button_donnemoifaim').fadeOut(); 
		}
	}
}
function tout_voir()
{ 	
	faire_apparaitre_header();
	
	$('#bloc_recherche_appronfondie').animate({bottom:'50'} , 500);
	
	$('#footer_menu_gourmand').animate({bottom:'0'} , 300);  
	$('#button_fleche_bas').animate({bottom:'0'} , 300);

}
// La on enleve tout 
function cacher_tout_entier()
{
	// On cache tout les autres bloc qui sont apparu
	faire_disparaitre_bloc_apparait() ; 
	
	$('#button_fleche_bas').animate({bottom:'-100px'} , 300); 
	
	$('#bloc_recherche_appronfondie').animate({bottom:'-400px'} , 500);
	
	$('#footer_menu_gourmand').animate({bottom:'-50px'} , 300); 

	faire_disparaitre_header() ; 

}

function faire_apparaitre_header()
{
	document.getElementsByTagName('header')[0].style.display = 'block' ;
	$('header').animate({top:'0'} , 300, function(){
		// On ne fait apparaitre le bandeau de réduction que si il est visible de base
		if(typeof(bandeau_reduction_visible) != 'undefined' && bandeau_reduction_visible == 1)
		{
			$('#bandeau_reduction').fadeIn(300) ;
		}
		// remettre la barre de chargement en bas
		document.getElementById('bloc_chargement_action').style.top = '50px' ;  
	}) ; 
	$('#logo').animate({top:'20px'} , 300) ;
}

function faire_disparaitre_header()
{
	// Si le menu est ouvert on le ferme 
	if(document.getElementsByTagName('header')[0].style.overflow == 'visible')
	{
		apparaitre_menu_responsive() ; 
	}
	
	// Si la réduction est apparante on l'enleve
	if(document.getElementById('bandeau_reduction').style.display != 'none')
	{
		$('#bandeau_reduction').fadeOut(300) ;
		bandeau_reduction_visible = 1 ; 
	}
	else
	{
		bandeau_reduction_visible = 0 ;
	}
	$('header').animate({top:'-50px'} , 300, function()
	{
		document.getElementsByTagName('header')[0].style.display = 'none' ;
		// Mettre la barre de chargement en haut
		document.getElementById('bloc_chargement_action').style.top = '0' ;   
	}
	);
	$('#logo').animate({top:'-80px'} , 400) ;
}

// ouverture des avis
function voir_avis()
{
	chargement_bloc_action() ;
	
	var xhr = creation_xhr();
	
	xhr.open ('GET', 'ajax/menu_gourmand/voir_avis.php');
	xhr.send(null);
	
	//Récupération du rapport de l'envoi
	xhr.onreadystatechange = function() 
	{
		//Requête envoyé ( == 4 ) et tout à bien été reçu ( == 200 ) 
		if (xhr.readyState == 4 && xhr.status == 200)
		{
			chargement_bloc_action() ;
			
			//récupération des commentaires
			var reponse_texte = xhr.responseText; 
		 
			// Si c'est parce que on est pas connecté 
			if(reponse_texte == 'non_connecte')
			{
				// On fait apparaitre le header 
				faire_apparaitre_header() ; 
				
				voir_bloc_coulissant('#compte_visiteur') ;
				
				// calback comme ca une fois connecté on arrive directement sur les avis
				formulaire_compte_visiteur_connexion.onsubmit = function()
				{
					connection_compte_visiteur(voir_avis);
					return false ;
				} ; 
			}
			else
			{
				if(document.getElementById('ensemble_avis').innerHTML = reponse_texte)
				{
					// On fait apparaitre le header 
					faire_apparaitre_header() ;
					
					voir_bloc_coulissant('#bloc_avis') ;
					
					// On récupère la variable avis_utilisateur_deja_poste qui se trouve en ajax a voir_avis
					avis_utilisateur_deja_poste = document.getElementById('avis_utilisateur_deja_poste').value ;
					// si avis_utilisateur_deja_poste = 0 alors l'utilisateur n'à pas encore posté de message 
					if(avis_utilisateur_deja_poste == 0)
					{
						var nombre_etoile = 0 ;
						
						document.getElementById('button_ouverture_form_avis').innerHTML = 'Donnez votre avis' ; 
						document.getElementById('bloc_etoile_modif_ajout').innerHTML = creation_etoile_avis(0 , 'ajout-modif') ; 
						document.getElementById('input_nombre_etoile_avis').value = 0 ;
						
						// avis_utilisateur_deja_poste contient l'id de l'avis donc on peut récupérer facilement le contenu dans le bloc directement
						document.getElementById('form_avis_utilisateur').value = '' ; 
					}
					// Sinon il à bien mis un message, alors on change ce qu'il faut changer
					else
					{
						var nombre_etoile = document.getElementById('input_note_avis_ajax').value ;
						
						document.getElementById('button_ouverture_form_avis').innerHTML = 'Modifier votre avis' ; 
						document.getElementById('bloc_etoile_modif_ajout').innerHTML = creation_etoile_avis(nombre_etoile , 'ajout-modif') ; 
						document.getElementById('input_nombre_etoile_avis').value = nombre_etoile ;
						
						// avis_utilisateur_deja_poste contient l'id de l'avis donc on peut récupérer facilement le contenu dans le bloc directement
						document.getElementById('form_avis_utilisateur').value = document.getElementById('contenu_avis' + avis_utilisateur_deja_poste).innerHTML ;
					}
					
					// fonction qui permet de mettre les afficher_plus
					display_afficher_plus_avis() ; 
				}
			}
		}
		
	}
		
}
function display_afficher_plus_avis(id_avis_display)
{
	// Pour le cas ou on le fait pour tout les éléments , donc quand ca apparait et également au resize
	
	// Si id_avis_display existe alors on le prend directement, on fait en sorte que le tableau ne soit que d'1
	if(typeof(id_avis_display) != 'undefined')
	{	
		var taille_tableau = 1 ;
	}
	else
	{
		// On récupère le contenu des blocs
		var contenu_avis_utilisateur = document.getElementsByClassName('contenu_avis_utilisateur') ; 
		
		// On calcule le nombre d'élement
		var taille_tableau = contenu_avis_utilisateur.length ;
	}
	
	// On récupère la hauteur des blocs pour comparer
	var taille_bloc_avis_utilisateur = taille_avis_voir_plus ;
	
	// Execution de la fonction qui va permettre le redimenssionnement le display ou non des bouton voir plus ect..
	display_afficher_plus_avis_recurence(0) ;
	
	function display_afficher_plus_avis_recurence(i)
	{		
		if(i <= taille_tableau - 1)
		{
			if(typeof(id_avis_display) == 'undefined')
			{
				var contenu_avis_utilisateur = document.getElementsByClassName('contenu_avis_utilisateur')[i] ; 
			}
			else
			{
				var contenu_avis_utilisateur = document.getElementById('contenu_bloc_avis' + id_avis_display) ;
			}
			
			var taille_contenu_bloc = contenu_avis_utilisateur.offsetHeight || contenu_avis_utilisateur.style.pixelHeight ; 
			// On compare le contenu à son contenant , si le contenu est plus grand alors on va mettre le lire la suite tout simplement
			
			// L'id est contenu a la fin de lid du contenu
			var id_groupe = contenu_avis_utilisateur.id ; 
			id_groupe =  id_groupe.replace('contenu_bloc_avis' , '') ;
			
			if(taille_contenu_bloc > taille_bloc_avis_utilisateur)
			{
				// Si il a déjà été créer pas besoin de le rajouter on s'assure juste qu'il soit en display block et on lui attribu le onclick approrié à sa nouvelle taille
				var afficher_plus_avis = document.getElementById('afficher_plus_avis' + id_groupe) ;
				
				if(afficher_plus_avis)
				{
					afficher_plus_avis.style.display = 'block' ;
					
					// On met la nouvelle hauteur au bloc seulement si il est ouvert
					if(afficher_plus_avis.innerHTML == 'Refermer')
					{
						document.getElementById('bloc_avis' + id_groupe).style.height =  taille_contenu_bloc + 50 + 'px';
						
						// Nouveau onclick car la taille à surement changé 
						afficher_plus_avis.onclick = function() {
							afficher_plus_bloc('bloc_avis' + id_groupe , taille_contenu_bloc + 50, taille_bloc_avis_utilisateur) ;
						}
					}
					else
					{
						// Si c'est ouvert on lui met un évènement au clique normal d'ouverture
						afficher_plus_avis.onclick = function() {
							afficher_plus_bloc('bloc_avis' + id_groupe , taille_bloc_avis_utilisateur , taille_contenu_bloc + 50 ) ;
						}
					}
				}
				else
				{
					// On créer à la fin un petit lire la suite
					var afficher_plus = document.createElement('span') ; 
					afficher_plus.className = 'afficher_plus' ; 
					afficher_plus.innerHTML = 'Voir +' ; 
					afficher_plus.id = 'afficher_plus_avis' + id_groupe ; 
					
					// On récupère l'id du groupe stocké dans l'attribu rel du contenu_avis
					var id_bloc_a_modif = 'bloc_avis' + id_groupe ;
					
					// Attribution des attribut object a récupérer
					afficher_plus.nouvelle_taille = taille_contenu_bloc + 50 ; 
					
					afficher_plus.onclick = function() {
						afficher_plus_bloc(id_bloc_a_modif , taille_avis_voir_plus, this.nouvelle_taille) ;
					} ; 
					
					// On met la barre à la fin
					contenu_avis_utilisateur.appendChild(afficher_plus) ;
				}
			}
			else
			{
				// Si c'est au resize on met un display none si jaamais il est apparant
				if(document.getElementById('afficher_plus_avis' + id_groupe))
				{
					document.getElementById('afficher_plus_avis' + id_groupe).style.display = 'none' ;
				}
			}
			
			// Tant qu'il reste des objects à parcourir on les parcour
			display_afficher_plus_avis_recurence(i + 1) ; 
		}
	}
}
function afficher_plus_bloc(id_bloc, taille_actuel, nouvelle_taille, event)
{
	// Récupération de l'évènement pour savoir qui la appelé
	var event = event || window.event ;
	var target = event.target || event.srcElement;
	
	// On augmente le bloc à la taille désirer
	$('#' + id_bloc).animate({height :  nouvelle_taille + 'px'} , 300) ;
	
	// On met la nouvelle fonction au afficher plus pour qu'il se referme
	target.onclick = function(){
		afficher_plus_bloc(id_bloc , nouvelle_taille, taille_actuel) ;
	}
	
	// Changement de l'écriture
	if(target.innerHTML == 'Refermer')
	{
		target.innerHTML = 'Voir +' ; 
	}
	else
	{
		target.innerHTML = 'Refermer' ; 
	}
}

function cacher_bloc_avis_visiteur()
{
	cache_bloc_coulissant('#bloc_avis');
	faire_disparaitre_header() ; 
}

function ouvrir_bloc_form_avis()
{
	if(document.getElementById('bloc_form_avis_utilisateur').style.display == 'none')
	{
		$('#bloc_ensemble_avis').fadeOut(300 , function() 
		{
			$('#bloc_form_avis_utilisateur').fadeIn(300) ;
		}) ;
	}
	else
	{
		faire_apparaitre_bloc_avis();
	}
}

function faire_apparaitre_bloc_avis(calback_fonction)
{
	$('#bloc_form_avis_utilisateur').fadeOut(300 , function() 
	{
		$('#bloc_ensemble_avis').fadeIn(300 , calback_fonction) ;
	}) ;
}
function etoile_avis_remplissage(id_note)
{
	// On remplis les étoiles en fonction sur quel étoile on est
	for(i=1 ; i < 6; i++)
	{
		if(i <= id_note)
		{
			document.getElementById('etoile_avis' + i).src = 'imgs/picto-etoile-pleine.png' ; 
		}
		else
		{
			document.getElementById('etoile_avis' + i).src = 'imgs/picto-etoile-vide.png' ;
		}
	}
	// On met l'input étoile au nombre de l'id de la note
	document.getElementById('input_nombre_etoile_avis').value = id_note; 
}
function submit_avis()
{
	chargement_bloc_action() ;
	
	var xhr = creation_xhr();
	
	var avis_utilisateur = document.getElementById('form_avis_utilisateur').value ; 
	
	var input_nombre_etoile_avis = document.getElementById('input_nombre_etoile_avis').value; 
	
	var token_visiteur = document.getElementById('token_visiteur').value ; 
	
	// Si input_nombre_etoile_avis on plus grand que zero on envoit
	if(input_nombre_etoile_avis > 0)
	{
		xhr.open ('POST', 'ajax/menu_gourmand/ajout_avis.php');
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.send('avis_utilisateur='+ avis_utilisateur + '&note_avis=' + input_nombre_etoile_avis + '&token_visiteur=' + token_visiteur);
		
		xhr.onreadystatechange = function() 
		{
			if (xhr.readyState == 4 && xhr.status == 200)
			{
				chargement_bloc_action() ;
				
				var tableau_json = JSON.parse(xhr.responseText); 
				
				if(tableau_json['erreur'] == 0)
				{
					var id_ajout_avis = tableau_json['id_avis'] ,
					contenu_vis_ajout = tableau_json['contenu_avis'] ;
					
					var type_appel_submit = tableau_json['type'] ; 
					
					// Si c'est un ajout d'avis
					if(type_appel_submit == 'ajout_avis' )
					{
						ajout_avis_final(id_ajout_avis , contenu_vis_ajout) ; 
					}
					else if(type_appel_submit == 'modif_avis')
					{
						// Modification
						document.getElementById('button_ouverture_form_avis').innerHTML = 'Modifier votre avis' ;
						
						document.getElementById('contenu_avis' + id_ajout_avis).innerHTML = tableau_json['contenu_avis'] ; 
						
						// Création des variable étoile 
						var affichage_etoile_avis = creation_etoile_avis(input_nombre_etoile_avis); 
						
						// ajout de la variable des etoiles dans le bloc 
						document.getElementById('bloc_etoile_avis' + id_ajout_avis).innerHTML = affichage_etoile_avis ; 
						
						ouverture_alert(alert_basic = '<span>Votre avis a bien été modifié.</span>') ;
						
						// Recalcule les donnes du resto comme l'ajout ou la modfication d'une note
						recalcule_donnes_avis_resto() ; 
					}
					// Que ce soit une modif ou un ajout on retourne en arriere dans tout les cas , pour éviter les probleme on s'assure que les avis_utilisateur on la hauteur de base
					var taille_bloc_avis_utilisateur = document.getElementsByClassName('avis_utilisateur')[0].offsetHeight || document.getElementsByClassName('avis_utilisateur')[0].style.pixelHeight ;
					
					faire_apparaitre_bloc_avis(function(){display_afficher_plus_avis(id_ajout_avis)}) ;
					
				}
				else
				{
					ouverture_alert(retour_erreur_formulaire = tableau_json['erreur']) ;	
				}
			}			
		}
	}
	else
	{
		ouverture_alert(retour_erreur_formulaire = '<span>La note du plat est manquante</span>') ;	
	}
}
function ajout_avis_final(id_avis , contenu_avis)
{
	// On récupère le nombre d'étoile 
	var input_nombre_etoile_avis = document.getElementById('input_nombre_etoile_avis').value;
	// Si le bloc aucun plat est la alors qu'il n'a rien a faire on le supprime
	if(document.getElementById('bloc_aucun_avis'))
	{
		var bloc_aucun_avis = document.getElementById('bloc_aucun_avis') ; 
		
		if(bloc_aucun_avis.style.display != 'none')
		{
			bloc_aucun_avis.style.display = 'none' ; 
		}
	}
	var avis_utilisateur = contenu_avis ; 
	
	var ensemble_avis = document.getElementById('ensemble_avis');
	
	// Donnons un nouvelle id au bloc
	var id_avis_bloc = id_avis;
	// Création d'un enfant, on va pas s'amuser à tout réécrire
	var paragraphe_avis = document.createElement('p') ; 
	
	var date_actuelle = new Date();
	
	var date_complete = date_actuelle.getDate()+"-"+(date_actuelle.getMonth()+ 1)+"-"+date_actuelle.getFullYear() ;
	
	// Création des variable étoile 
	var affichage_etoile_avis = creation_etoile_avis(input_nombre_etoile_avis); 
	
	paragraphe_avis.id =  'bloc_avis' + id_avis_bloc ; 
	paragraphe_avis.innerHTML = document.getElementById('login_visiteur_avis').value + '<br /><span id="bloc_etoile_avis'+ id_avis_bloc +'">' + affichage_etoile_avis + '</span><span class="date_avis">' + date_complete + '</span><br /><span class="contenu_avis_utilisateur"  id="contenu_bloc_avis' + id_avis_bloc +  '">" ' + avis_utilisateur + ' "<br /><br /><span id="bloc_supprimer_avis" class="texte_site"><img onclick="supp_avis(' + id_avis_bloc + ');" style="cursor:pointer" src="../imgs/picto-supp.png" alt="picto supprimer"><br />Supprimer</span><br />' ;
	
	paragraphe_avis.className = 'avis_utilisateur texte_site_noir' ;
	
	// On met le nouveau avis créer dans le bloc des avis en premier
	ensemble_avis.insertBefore(paragraphe_avis , ensemble_avis.firstChild) ; 
	
	// Buttton pour allez au formulaire
	document.getElementById('button_ouverture_form_avis').innerHTML = 'Modifier votre avis' ;

	// On fait apparaitre la suppression si elle était invisible
	var supp_avis_button = document.getElementById('bloc_supprimer_avis'); 
	supp_avis_button.style.display = 'inline' ; 
	
	// Quand on clique sur le bouton supprimé c'est le nouvelle id qui doit etre supprimé
	document.getElementById('id_avis_a_supp').value = id_avis_bloc;
	
	ouverture_alert(alert_basic = '<span>Votre avis a bien été posté.</span>') ;
	
	// Recalcule des données du resto comme le nombre de votant ou la moyenne du plat
	recalcule_donnes_avis_resto() ; 
}
function creation_etoile_avis(nombre_etoile, type) 
{
	if(typeof(type) != 'undefined' && type == 'ajout-modif')
	{
		var etoile_cliquable = 1 ; 
	}
	
	// Initialisation de la variable qui va contenir toute les etoiles 
	var affichage_etoile_avis = '' ; 
	// Création de la boucle des étoile
	for(i =1 ; i < 6; i++)
	{
			// Cela veut dire que le nombre d'étoile est décimale et donc que ce doit etre pour la moyenne du plats
		if(i > nombre_etoile && i - nombre_etoile <= 0.8)
		{
			var etoile_decimale = '<img src="imgs/picto-etoile-presque-vide.png" alt="etoile presque vide" class="etoile_avis" />' ;
			
			if(i - nombre_etoile <= 0.5)
			{
				etoile_decimale = '<img src="imgs/picto-etoile-moitie-pleine.png" alt="etoile moitie pleine" class="etoile_avis" />' ;
				
				if(i - nombre_etoile <= 0.2)
				{
					etoile_decimale = '<img src="imgs/picto-etoile-presque-pleine.png" alt="etoile presque pleine" class="etoile_avis" />' ;
				}
			}
			
			// à la fin on met la variable etoile decimale dans l'affichage
			affichage_etoile_avis = affichage_etoile_avis + etoile_decimale; 
		}
		else
		{
			if(i <= nombre_etoile)
			{
				if(typeof(etoile_cliquable) != 'undefined')
				{
					affichage_etoile_avis = affichage_etoile_avis + '<img id="etoile_avis'+ i +'" src="imgs/picto-etoile-pleine.png" onclick="etoile_avis_remplissage(' + i +');" alt="etoile vide" class="etoile_avis etoile_cliquable" />' ; 
				}
				else
				{
					affichage_etoile_avis = affichage_etoile_avis + '<img src="imgs/picto-etoile-pleine.png" alt="etoile vide" class="etoile_avis" />' ; 
				}
			}
			else
			{
				if(typeof(etoile_cliquable) != 'undefined')
				{
					affichage_etoile_avis = affichage_etoile_avis + '<img id="etoile_avis'+ i +'" src="imgs/picto-etoile-vide.png" onclick="etoile_avis_remplissage(' + i + ');" alt="etoile vide" class="etoile_avis etoile_cliquable" />' ;
				}
				else
				{
					affichage_etoile_avis = affichage_etoile_avis + '<img src="imgs/picto-etoile-vide.png" alt="etoile vide" class="etoile_avis" />' ; 
				}
			}
		}
	}
	
	return affichage_etoile_avis ; 
}
function supp_avis()
{
	if(typeof(reponse_fonction) != 'undefined' && reponse_fonction != '')
	{
		if(reponse_fonction == 'ok')
		{
			chargement_bloc_action() ;
			
			var xhr = creation_xhr();
			
			var id_supp = document.getElementById('id_avis_a_supp').value ; 
			
			var token_visiteur = document.getElementById('token_visiteur').value ; 
			
			xhr.open ('GET', 'ajax/menu_gourmand/supp_avis.php?id_supp=' + id_supp + '&token_visiteur=' + token_visiteur);
			xhr.send(null);
			
			//Récupération du rapport de l'envoi
			xhr.onreadystatechange = function() 
			{
				//Requête envoyé ( == 4 ) et tout à bien été reçu ( == 200 ) 
				if (xhr.readyState == 4 && xhr.status == 200)
				{
					chargement_bloc_action() ;
					
					var reponse_text = xhr.responseText ; 
					
					if(reponse_text == '')
					{
						var ensemble_avis = document.getElementById('ensemble_avis'); 
						
						// Suppression de l'avis de l'utilisateur puis on revient aux avis globaux
						ensemble_avis.removeChild(document.getElementById('bloc_avis' + id_supp)); 
						
						faire_apparaitre_bloc_avis(); 
						
						// On change le button pour qu'on dise de donner son avis
						document.getElementById('button_ouverture_form_avis').innerHTML = 'Donnez votre avis !' ;
						
						// Supprimer le contenu de l'ancien avis
						document.getElementById('form_avis_utilisateur').value = '' ; 
						
						// Idem les etoiles 
						document.getElementById('bloc_etoile_modif_ajout').innerHTML = creation_etoile_avis(0 , 'ajout-modif') ; 
						
						// Si il n'y a plus d'avis alors on affiche le message comme quoi il n'y en a pas.
						if(ensemble_avis.children.length == 0 )
						{
							ensemble_avis.innerHTML = '<p id="bloc_aucun_avis" style="font-size:18px; text-align:center" class="texte_site">Auncun avis n\'a été ajouté pour ce plat, vous avez déjà gouté ce plat ? Que diriez-vous de rédiger votre avis à ce sujet ?<br><img src="imgs/picto-non-plat.png" alt="aucun avis utilisateur"></p>' ; 
						}
						// On recalcule la moyenne des images ducoup
						recalcule_donnes_avis_resto();
					}
					else
					{
						ouverture_alert(alert_basic = reponse_text) ;	
					}
				}
			}
		}
		else
		{
			// On vide la variable pour pouvoir la réutiliser
			reponse_fonction = '' ; 
		}
	}
	else
	{
		// Récupération du nom de la fonction a rappeler pour savoir la réponse
		fonction_a_appeler = supp_avis;
		ouverture_alert(confirm_action = 'Voulez-vous vraiment supprimer votre avis ?') ;
	}
		
}
function recalcule_donnes_avis_resto()
{
	// Récupération en ajax des données 
	
	var xhr = creation_xhr();
			
	var id_supp = document.getElementById('id_avis_a_supp').value ; 
	
	xhr.open ('GET', 'ajax/menu_gourmand/recalcule_donnes_avis.php');
	xhr.send(null);
	
	//Récupération du rapport de l'envoi
	xhr.onreadystatechange = function() 
	{
		//Requête envoyé ( == 4 ) et tout à bien été reçu ( == 200 ) 
		if (xhr.readyState == 4 && xhr.status == 200)
		{
			var tableau_json = JSON.parse(xhr.responseText);
			
			// On modifie les données relative au  resto puisqu'elles on casi forcement changé ( si l'utilisateur à ajouté , supprimé ou modifié la note )
			document.getElementById('nombre_vote_avis_resto').innerHTML = tableau_json['nombre_avis_vote'] ;
			document.getElementById('bloc_note_resto').innerHTML = tableau_json['affichage_etoile_avis'] ; 
			document.getElementById('note_texte_global_resto').innerHTML = tableau_json['note_finale']; 
			
			// Si le nombre de vote est de zéro on enleve la note et le nombre de note
			if(tableau_json['nombre_avis_vote'] == 0)
			{
				document.getElementById('bloc_englobans_number_vote').style.display = 'none' ;
			}
			// A l'inverse on s'asure que ce soit bien visible
			else
			{
				document.getElementById('bloc_englobans_number_vote').style.display = 'inline' ;
			}
			if(tableau_json['note_finale'] == 0)
			{
				document.getElementById('bloc_englobans_rating_texte').style.display = 'none' ; 
			}
			else
			{
				document.getElementById('bloc_englobans_rating_texte').style.display = 'inline' ; 
			}
		}
	}
}

// géolocalisation lancé au calback de l'initation de google map dans googlemap.php dans application
function geocalisation_user()
{
	// Try HTML5 geolocation
	if(navigator.geolocation) { 
		
		// Permet de récupérer tout le temps la position
		navigator.geolocation.watchPosition(function(position) 
		{
			position_user = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
			
			position_user_latitude = position.coords.latitude ; 
			position_user_longitude = position.coords.longitude ; 
			
	
		});
		
		// On géolocalise une première fois normalement et on calback sur chargementimage avec les infos nécessaires
		navigator.geolocation.getCurrentPosition(function(position) 
		{
			position_user = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
			
			position_user_latitude = position.coords.latitude ; 
			position_user_longitude = position.coords.longitude ; 
			
			// Appel de l'image dès que c'est chargé
			if(type == "geocaliser")
			{
				// Si c'est égale à zéro c'est que l'on ne veut pas de chargement dès le début -- utile pour concerver la session de recherche geocalise tout en allant chercher un plat dans l'url
				if(typeof(non_appel_chargementimage) != 'undefined' && non_appel_chargementimage == 1)
				{}
				else
				{
					// On met l'option pour revenir au debut comme ca si on change on revient facilement
					chargementimage('revenir_au_debut_geolocalisation') ; 
				}
			}
			
			// Dit que la géolocalisation à bien été activé
			geocalisation_active = 1 ; 
			
			// Si le picto en attente était visible
			if(document.getElementById('picto_en_attente_geo').style.display == 'inline')
			{
				$('#picto_cible2').fadeIn(200);
				document.getElementById('picto_en_attente_geo').style.display = 'none' ;
			}
			
		}, function(error) {
		
			// Si le type de recherche désiré est géolocalisé
			if(error.PERMISSION_DENIED)
			{
				ouverture_alert(alert_basic = '<span> La géolocalisation a été refusée : vous ne pourrez pas voir les plats les plus proches. Pour la réactiver allez dans les paramètre de localisation. </span><br />') ;
			}
			else if(error.POSITION_UNAVAILABLE)
			{
				ouverture_alert(alert_basic = '<span> Impossible de récupérer la position de la géolocalisation. Rechargez la page en cas de problème. </span><br />') ;
			}
			else
			{
				ouverture_alert(alert_basic = '<span> Erreur géolocalisation, veuillez recharger la page. </span><br />') ;
			}

			bloque_button_geocalisation() ; 
			
		});
	} else {
		// Le navigateur ne supporte pas la géolocalisation ou bien refue du navigateur
		if(typeof(handleNoGeolocation) != 'undefined')
		{
			handleNoGeolocation(false);
		}
		
		bloque_button_geocalisation(); 
	}
	
}

function message_premiere_connexion()
{
	ouverture_alert(alert_basic = '<span>Bienvenue sur notre application ! N\'oubliez pas : la géolocalisation doit etre activé pour trouver les plats les plus proches de votre position.</span>') ; 
}

function bloque_button_geocalisation()
{
	// On désactive les autres picto cible par précautions
	document.getElementById('picto_cible').style.display = 'none' ;
	document.getElementById('picto_cible2').style.display = 'none' ;
	document.getElementById('picto_en_attente_geo').style.display = 'none' ;
	
	// On active le picto pour montrer que c'est pas possible
	$('#picto_cible_hs').fadeIn(200) ;
	
	geocalisation_bloque = 1 ; 
	
	connaitre_type_recherche(type = 'aleatoire' , 1) ;
}
function google_map()
{
	// On récupère l'adresse du plat
	adresse_text = document.getElementById('adressresto').textContent || document.getElementById('adressresto').innerText ;
	
	// Si on est sur mobile pas besoin de géocodé l'adresse on ouvre directement sur l'application avec l'adresse en dur permet que ce soit plus rapide à charger
	if(typeof(systeme_mobile) != 'undefined')
	{
		ouvrir_google_map() ; 
	}
	else
	{
		// Permet de remettre le header
		faire_apparaitre_header();
		
		coordonnees_restaurant = new google.maps.LatLng(coordonnees_latitude,coordonnees_longitude) ;
		
		ouvrir_google_map() ;
	}
}
function ouvrir_google_map()
{
	// Si c'est un mobile 
	if(typeof(systeme_mobile) != 'undefined')
	{
		if(systeme_mobile == 'android' )
		{
			window.location.href = 'http://maps.google.com/?q=' + adresse_text ;
		}
		else if(systeme_mobile == 'os')
		{
			window.location.href = 'http://maps.google.com/?q=' + adresse_text ;
		}
		else
		{
			window.location.href = 'http://maps.google.com/?q=' + adresse_text ;
		}
	}
	else
	{
		if(typeof(google_map_ok) != 'undefined' &&  google_map_ok == document.getElementById('div_id_plat_invisible').innerHTML)
		{
			// Si map a déjà été chargé on l'ouvre juste
		}
		else
		{
			// on le créé le nouveau google map
			var directionsService = new google.maps.DirectionsService();
			var map;
			var directionsDisplay = new google.maps.DirectionsRenderer();
			
			  var mapOptions = {
				zoom: 17,
				center: coordonnees_restaurant
			  };
			  map = new google.maps.Map(document.getElementById('google_map_plat'),mapOptions);
			  
			// Si l'utilisateur à refuser la géolocalisation ou bien que le navigateur est trop vieux
			if(typeof(position_user) == 'undefined' )
			{
				 var marker = new google.maps.Marker({
					  position: coordonnees_restaurant,
					  map: map,
				  });
			}
			else
			{
				// Inialiser la direction  
				 directionsDisplay.setMap(map);
					  
				// Calcule un iténéraire entre les deux points
				var request = {
				  origin: position_user,
				  destination: coordonnees_restaurant,
				  travelMode: google.maps.TravelMode.WALKING
				};
				directionsService.route(request, function(response, status) {
				if (status == google.maps.DirectionsStatus.OK) {
				  directionsDisplay.setDirections(response);
				}
				});
			}
			
			// Dès que l'on à déjà chargé maps, on lui attribut l'id du plat
			google_map_ok = document.getElementById('div_id_plat_invisible').innerHTML ;
		}
		
		// On ouvre le bloc google map
		voir_bloc_coulissant(id_bloc = '#bloc_google_map_plat') ;
		
		// On refresh la map si le plat change et que l'objet map existe déjà
		google.maps.event.trigger(map, 'resize');
	}
}

function disparition_google_map()
{
	// Déjà on sait que l'on doit cacher google map 
	cache_bloc_coulissant(id_bloc = '#bloc_google_map_plat'); 
	
	// Ensuite la question est de savoir si on doit faire disparaitre le header ou pas
	// La condition c'est si est visible 
	if(document.getElementById('bloc_info_coulissant').style.display != 'none')
	{
		faire_disparaitre_header();
	}
	
	// Sinon la personne n'est pas passé par le bloc coulissant
}

function connaitre_type_recherche(type , appel_chargement_image)
{
	//aleatoire // geocaliser// image_unique
	type_recherche = type ; 
	
	if(typeof(appel_chargement_image) != 'undefined' )
	{
		if(appel_chargement_image == 1)
		{
			// Permet d'appeler chargementimage en cas de besoin
			chargementimage() ;
		}
		else
		{
			// Variable global pour faire comprendre à toute action que l'on ne veut pas charger  d'autre image (utile pour image-unique)
			non_appel_chargementimage = 1 ; 
		}
	}
	
	if(type == 'aleatoire')
	{	
		// On rend apparant le picto_aleatoire rouge
		document.getElementById('picto_aleatoire').style.display = 'none' ;
		document.getElementById('picto_aleatoire2').style.display = 'inline' ;
		
		// Si la géolocalisation est bloque la fonction approprié aura déjà fait le nécessaire
		if(typeof(geocalisation_bloque) != 'undefined')
		{}
		else
		{
			// On enleve le picto cible rouge on met lautre
			document.getElementById('picto_en_attente_geo').style.display = 'none' ;
			document.getElementById('picto_cible2').style.display = 'none' ;
			document.getElementById('picto_cible').style.display = 'inline' ;
		}
	}
	else if(type == "geocaliser")
	{
		// Si une recherche est en cours on l'enleve et on remet normal
		if(document.getElementById('recherche_en_cours').style.display != 'none' || document.getElementById('button_menu_responsive').style.display != 'none')
		{
			supp_recherche() ; 
		}
		if(typeof(geocalisation_bloque) != 'undefined')
		{
			//Si la géolocalisation est bloqué
		}
		else
		{
			// On rend apparant le picto_aleatoire rouge dans tout les cas
			document.getElementById('picto_aleatoire2').style.display = 'none' ;
			document.getElementById('picto_aleatoire').style.display = 'inline' ;
			
			// Si la géolocalisation n'est pas encore active on active le picto en attente
			if(typeof(geocalisation_active) != 'undefined')
			{
			
				// On enleve le picto cible rouge on met lautre
				document.getElementById('picto_cible').style.display = 'none' ;
				document.getElementById('picto_cible2').style.display = 'inline' ;
			}
			else
			{
				document.getElementById('picto_cible').style.display = 'none' ;
				document.getElementById('picto_cible2').style.display = 'none' ;
				// Met le picto attente pour faire comprendre que l'on attend quelque chose
				document.getElementById('picto_en_attente_geo').style.display = 'inline' ;
				
				// Faire tourner le picto en attente pour bien montrer que on attend un chargement
				rotate_element(document.getElementById('picto_en_attente_geo') , 20 , 5) ; 
			}
		}
	}
	else if(type == 'image_unique')
	{
		// Si il y à des différences à faire quand on charge une image unique on peut les faire ici
	}
}
function chargementimage(option) 
{
	faire_apparaitre_publicite() ; 
	
	// Si le bloque d'image complémantaire est ouvert on le ferme (utile pour "tout les plats")
	if(document.getElementById('bloc_info_coulissant').style.display != 'none' )
	{
		info_image_en_plus();
	}
	// Si le bloque de j'aime est ouvert on le ferme
	if(document.getElementById('bloc_mes_jaimes').style.display != 'none')
	{
		cache_bloc_coulissant('#bloc_mes_jaimes') ; 
	}
	
	// Si le bloc responsive est ouvert on le ferme pour éviter de géner la vue
	if(document.getElementsByTagName('header')[0].style.overflow != 'hidden')
	{
		apparaitre_menu_responsive() ;
	}
	
	// on regarde si une recherche est en cours
	var recherche_value = document.getElementById('recherche').value ;
	document.getElementById('autre_action').style.display = 'none'; 
	
	// Bloque loading sur l'image que si il n'est pas visible
	chargement_bloc_action() ;

	var xhr = creation_xhr();
	
	//Si idimage_url existe, qui correspond à la précense d'un idimage dans l'url pour trouver une image pour html history
	if (typeof(nom_image_history) != "undefined" && nom_image_history != 0)
	{
		xhr.open('GET', 'ajax/menu_gourmand/menu_gourmand.php?idimage=' + nom_image_history);
		xhr.send(null);
	}
	//Sinon envoi basique avec check si il y a pas parametre de recherche
	else
	{
		//Si l'input de recherche n'est pas spécifié
		if(recherche_value == '')
		{
			var recherche_en_cour_value = document.getElementById('recherche_en_cours').innerHTML ;
			//si le bloc de recherche en cour n'est pas spécifié
			
			if(type_recherche == 'aleatoire')
			{
				if(recherche_en_cour_value == '')
				{
					//Recherche aléatoire sans rien
					xhr.open('GET', 'ajax/menu_gourmand/menu_gourmand.php');
					xhr.send(null);
				}
				else
				{
					xhr.open('GET', 'ajax/menu_gourmand/menu_gourmand.php?recherche=' + recherche_en_cour_value);
					xhr.send(null);
				}
			}
			else if(type_recherche == 'geocaliser')
			{
				if(typeof(position_user) != 'undefined')
				{
					// Ici on regarde si l'option de revenir au début de la géolisation est active ou pas 
					if(typeof(option) != 'undefined' && option == 'revenir_au_debut_geolocalisation')
					{
						xhr.open('GET', 'ajax/menu_gourmand/menu_gourmand.php?position_user_latitude=' + position_user_latitude + '&position_user_longitude=' + position_user_longitude + '&revenir_au_debut_geolocalisation=1');
					}
					else
					{
						xhr.open('GET', 'ajax/menu_gourmand/menu_gourmand.php?position_user_latitude=' + position_user_latitude + '&position_user_longitude=' + position_user_longitude);
					}
					xhr.send(null);
				}
			}
		}
		else
		{
			xhr.open('GET', 'ajax/menu_gourmand/menu_gourmand.php?recherche=' + recherche_value);
			xhr.send(null);
			
			$('.recherche_valide').text(recherche_value) ;
			
			$( ".recherche_ville" ).fadeOut(200 , function() {
				
				$( ".recherche_valide" ).fadeIn(200);
				
				$( ".submit_recherche" ).fadeOut(200 , function(){
					$( "#supp_recherche_valide" ).fadeIn(200);
				});
			});
			
		}
	}
	xhr.onreadystatechange = function() 
	{		
		//Si tout la méthode est terminé ( = 4 ) et que tout c'est bien passé ( xhr.status == 200 )
		if (xhr.readyState == 4 && xhr.status == 200)
		{
			chargement_bloc_action() ; 
			
			function chargementImageComplete()
			{
				// ON dit que la premiere image a été chargé pour éviter que la geocalisation ne recharge l'image au changement d'onglet
				chargement_image_premiere = 1 ;

				id_plat_actuelle = tableau_json['image_plat'] ; 
				
				// On s'occupe d'allez les cherchers les attribus du resto
				
				if (tableau_json['attribus_resto'] != '')
				{
					var xhr = creation_xhr();
					
					xhr.open('GET', 'compte-resto/inc/attribus_resto_affichage.php?id_resto=' + tableau_json['id_resto']);
					xhr.send(null);
					
					xhr.onreadystatechange = function() 
					{
						//Si tout la méthode est terminé ( = 4 ) et que tout c'est bien passé ( xhr.status == 200 )
						if (xhr.readyState == 4 && xhr.status == 200)
						{
							document.getElementById('bloc_attribus_resto_ajax_modif').innerHTML = xhr.responseText; 
						}
						
					};
				}
				else
				{
					document.getElementById('bloc_attribus_resto_ajax_modif').innerHTML = '' ; 
				}
				
				var facette_resto = document.getElementById('bloc_image_fiche_resto') ; 
				// Modification de l'image de la facade resto
				facette_resto.style.background = 'url(\'' + tableau_json['image_facade'] + '\') no-repeat center' ; 
				// Le mettre en cover également pour qu'il prenne toute l'espace disponible
				facette_resto.style.backgroundSize = 'cover'; 
				// Attribution d'un onclick qui va permettre de voir en grand
				facette_resto.onclick = function()
				{
					
					apercu_image(tableau_json['image_facade']) ; 
				}
				
				// Tout à terminé de chargé on appel la fonction qui permet de montrer que l'action est terminé
				chargement_bloc_action() ; 
				
				// on rappel la fonction pour remettre le slice sur l'image
				sliceImagePlat() ;  
			}
			
			tableau_json = JSON.parse(xhr.responseText);
			
			// Si le plat est inexistant 
			if(tableau_json['idimage'] == 'admin/image-non-ville')
			{
				ouverture_alert(alert_basic = '<span> Aucun plat n\'a été trouvé dans la ville de votre recherche. Chaque jour, nous démarchons de nouveaux restaurateurs, cette ville viendra bientôt, c\'est promis ! </span><br />') ;
				
				// On le temporise histoire que l'effet d'avant est eux le temps de se faire
				setTimeout(function(){supp_recherche();} , 300) ; 				
			}
			else
			{
				//on va créer l'image à partir de l'url pour savoir précisement quand elle est terminé
				var image_plat = new Image();

				// Partie application si c'est sur mobile on met image de qualité mobile
				if(typeof(systeme_mobile) != 'undefined')
				{
					image_plat.src = 'plats/mobiles/' + tableau_json['idimage'] + '.' + tableau_json['versionning'] + '.jpg';
				}
				else
				{
					image_plat.src = 'plats/' + tableau_json['idimage'] + '.' + tableau_json['versionning'] + '.jpg';
				}
				image_plat.alt = tableau_json['nomplat'] ;
				image_plat.oncontextmenu= function(){protection_image();} ;
				image_plat.onclick= function(){voir_photo_entier(); } ; 
				image_plat.id = 'image_plat' ; 
				
				if(typeof(addEventListener) != 'undefined')
				{
					image_plat.addEventListener('load', chargementImageComplete, false);
				}
				else
				{
					// Tant pis on charge en meme temps ce sera ralentis sur les vieux navigateurs
					chargementImageComplete() ; 
				}
				
				var bloc_image = document.getElementById('bloc_image_plat') ;
				var image_actuelle = document.getElementById('image_plat');
				
				// On remplace l'image actuel par celle-ci
				bloc_image.replaceChild(image_plat, image_actuelle) ; 
				
				// Contenir dans une div invisible l'id du plat en cour 
				document.getElementById('div_id_plat_invisible').innerHTML = tableau_json['idimage'] ; 
				
				document.getElementById('nomplat').innerHTML = tableau_json['nomplat']; 
				
				document.getElementById('nomresto').innerHTML = tableau_json['nomresto'] ; 
					 
				// Pour pouvoir formater l'avis
				document.getElementById('nom_resto_info_complementaire').innerHTML = tableau_json['nomresto'] ;
					
				document.getElementById('adressresto').innerHTML = tableau_json['adressresto']; 

				document.getElementById('adressresto_info_complementaire').innerHTML = tableau_json['adressresto'] ; 		
					
				document.getElementById('url_select').value = tableau_json['url_image'];
				
				//HTML HISTOY pour changer l'url 
				if(history.pushState)
				{	
					if(typeof(nom_image_history) != 'undefined' && nom_image_history != 0 )
					{
						nom_image_history = 0 ; 
					}
					else
					{
						if(tableau_json['history_api'] != 0)
						{
							if(typeof(arret_state_idimage) != 'undefined' && arret_state_idimage == 1)
							{
								// Ici on ne veut pas mettre dans le tableau la nouvelle id, par exemple pour le cas d'un précédent image ou il ne faut pas réécrire l'image sous peine de créer une boucle
								arret_state_idimage = 0 ; 
							}
							else
							{
								state_idimage[state_idimage.length] = tableau_json['image_plat'] ;
							}
						}
					}
					
					if(tableau_json['history_api'] != 0)
					{	
						history.pushState(state_idimage, null, tableau_json['idimage'] + '.html');
						
						document.title = tableau_json['nomplat'] + ' - ' + tableau_json['nomresto'] ; 
					} 
					else
					{
						document.title = 'recherche non valide' ;
					}
				}
				
				// Permet de passer le resto, si on à déjà vu 5 de ses plats, permet d'éviter que certain bourrine de plat
				if(tableau_json['nombre_resto_vu_suite'] >= 4)
				{
					$('#bloc_passer_resto').fadeIn() ;
				}
				else
				{
					$('#bloc_passer_resto').fadeOut() ; 
				}
				
				document.getElementById('nombre_jaime_plat').innerHTML = tableau_json['nombre_jaime']; 
				
				//Voir si le plat à déjà été aimé ou pas
				if(tableau_json['deja_jaime_vote'] == 1)
				{
					$('.picto_jaime').css('display' , 'none') ; 
					$('.picto_jaime_deja_vote').css('display' , 'inline-block' ) ; 
					
				}
				else
				{
					$('.picto_jaime_deja_vote').css('display' , 'none') ; 
					$('.picto_jaime').css('display' , 'inline-block' );
				}
				
				coordonnees_latitude = tableau_json['coordonnees_latitude'] ;
				coordonnees_longitude = tableau_json['coordonnees_longitude'] ;
				
				// Chargement de la note avis resto
				document.getElementById('bloc_note_resto').innerHTML = creation_etoile_avis(tableau_json['note_avis_resto']) ;
				
				if(['note_avis_resto'] != 0)
				{
					var note_texte_global_resto = document.getElementById('note_texte_global_resto'),
					nombre_vote_avis_resto = document.getElementById('nombre_vote_avis_resto'),
					denomination_votant_avis_resto = document.getElementById('denomination_votant_avis_resto');
					
					note_texte_global_resto.style.display = 'inline' ; 
					nombre_vote_avis_resto.style.display = 'inline' ; 
					denomination_votant_avis_resto.style.display = 'inline' ;
					document.getElementById('bloc_englobans_rating_texte').style.display = 'inline' ;
					document.getElementById('bloc_englobans_number_vote').style.display = 'inline' ; 
					
					note_texte_global_resto.innerHTML = tableau_json['note_avis_resto'] ;  
					
					// Calcule savoir si on met un s ou pas à votant
					if(tableau_json['nombre_vote_avis_resto'] > 1)
					{
						denomination_votant_avis_resto.innerHTML = 'Votants' ; 
					}
					else
					{
						denomination_votant_avis_resto.innerHTML = 'Votant' ;
						
					}
					nombre_vote_avis_resto.innerHTML = tableau_json['nombre_vote_avis_resto'];
					// Remplissage de la micro_data count
					document.getElementById('nombre_vote_avis_resto_count').innerHTML = tableau_json['nombre_vote_avis_resto'];
				}
				else
				{
					note_texte_global_resto.innerHTML = '' ; 
					nombre_vote_avis_resto.innerHTML = '' ;
					
					note_texte_global_resto.style.display = 'none' ; 
					nombre_vote_avis_resto.style.display = 'none' ;
					
					document.getElementById('bloc_englobans_rating_texte').style.display = 'none' ; 
					document.getElementById('bloc_englobans_number_vote').style.display = 'none' ; 
				}
				
				// Chargement des liens de partage des réseaux sociaux
				
				// FACEBOOK
				document.getElementById('lien_partage_facebook').href = 'https://www.facebook.com/dialog/feed?app_id=' + meta_id_app_facebook + '&link=http://' +  tableau_json['url_image'] + '&redirect_uri=http://' + tableau_json['url_image']; 
				
				// TWITTER 
				document.getElementById('lien_partage_twitter').href = 'http://twitter.com/share?text=' + tableau_json['nomplat'] + ' - ' + tableau_json['nomresto'] + '&url=http://' + tableau_json['url_image'] + '&hashtags=donnemoifaim';
				
				//GOOGLE PLUS
				document.getElementById('lien_partage_google_plus').href = 'https://plus.google.com/share?url=http://' +  tableau_json['url_image']; 
				
				// On met la réduction si besoin
				if(typeof(tableau_json['reduction_libelle']) != 'undefined' && typeof(tableau_json['reduction_id']) != 'undefined')
				{					
					if(tableau_json['reduction_libelle'] != '' && tableau_json['reduction_id'] != '')
					{
						document.getElementById('labelle_reduction').innerHTML = tableau_json['reduction_libelle'] ; 
						document.getElementById('input_id_reduction').value = tableau_json['reduction_id'] ; 
						
						// On dit que la réduction doit etre visible dans une variable global 
						bandeau_reduction_visible = 1 ; 
						
						// On met que si le header est visible
						if(document.getElementsByTagName('header')[0].style.display != 'none')
						{
							$('#bandeau_reduction').fadeIn(300) ;
						}
					}
					else
					{
						// On enleve la réduction si il y en avait une 
						document.getElementById('bandeau_reduction').style.display = 'none' ;
						
						// On dit bien que la réduction ne doit pas etre visible 
						bandeau_reduction_visible = 0 ;
					}
				}
				else
				{
					// On enleve la réduction si il y en avait une 
					document.getElementById('bandeau_reduction').style.display = 'none' ;
					
					// On dit bien que la réduction ne doit pas etre visible 
					bandeau_reduction_visible = 0 ; 
				}
				
				// API roulette ZOOM image //
				document.getElementById('image_plat').style.transform = 'scale(1)' ; 
			}
		}
	}
}
function supp_recherche() 
{
	// Reset de l'input de recherche et effet de slide
	document.getElementById('recherche').value = '' ;
	
	$('.recherche_valide').text('') ;
	
	$( ".recherche_ville" ).val('') ; 
	
	$( ".recherche_valide" ).fadeOut(200, function() {
		
		$( ".recherche_ville" ).fadeIn(200) ;
		
		 $( "#supp_recherche_valide" ).fadeOut(200, function(){
			$( ".submit_recherche" ).fadeIn(200) ;
		});
	});
	
	// On envoi le chargement de l'image
	chargementimage('revenir_au_debut_geolocalisation') ;
}

function connection_compte_visiteur(calback_connexion)
{
	chargement_bloc_action() ;
	
	var xhr = creation_xhr();
	
	var login = document.getElementById('login_compte_visiteur').value,
	mdp = document.getElementById('mdp_compte_visiteur').value;

	xhr.open('POST', 'ajax/menu_gourmand/connection_compte.php');
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send('login=' + login + '&mdp=' + mdp);

	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && xhr.status == 200)
		{
			chargement_bloc_action() ;
			
			var tableau_json = JSON.parse(xhr.responseText); 
			
			if(tableau_json['erreur'] == 0)
			{
				if(typeof(calback_connexion) != 'undefined')
				{
					// On va créer un calback juste après la connexion
					action_tout_juste_connecte(tableau_json , login, function()
					{
						calback_connexion.call() ; 
					}) ; 
				}
				else
				{
					// Sinon par défaut on dit bon retour sur donnemoifaim
					ouverture_alert(alert_basic = '<span>Hello ' + tableau_json['login'] + ', bon retour sur donnemoifaim.</span><br />') ;	
					
					action_tout_juste_connecte(tableau_json , login, function()
					{
						affichage_compte_visiteur() ; 
					}) ;
				}
			}
			else
			{
				ouverture_alert(alert_basic = tableau_json['erreur']) ;
			}
		}
	}
}

function action_tout_juste_connecte(tableau_json , login, calback)
{
	if(typeof(login) != 'undefined')
	{}
	else
	{
		var login = tableau_json['login'] ; 
	}
	
	document.getElementById('login_visiteur_recuperer').value = login ;
	// Remplacement du nom visiteur par celui-ci
	document.getElementById('nom_proprietaire_compte_visiteur').innerHTML = login ; 	
				
	// Modification du bouton j'aime si jamais on se connecte
	if(tableau_json['deja_vote'] == 1)
	{
		// On enleve l'image cliquable et on en met une non clicable
		$('.picto_jaime').css('display' , 'none') ;
		$('.picto_jaime_deja_vote').fadeIn() ;
	}
	 
	// Variable global avec un token
	document.getElementById('token_visiteur').value = tableau_json['token_visiteur']; 
	
	// On recompte les notifications pour savoir combien il en reste vraiment à voir
	recompter_notification() ; 
		
	// On indique ce qu'il faut faire dans le calback 
	if(typeof(calback) != 'undefined')
	{
		cache_bloc_coulissant('#compte_visiteur' , calback) ;
	}
	else
	{
		cache_bloc_coulissant('#compte_visiteur') ; 
	}
}

function deconnexion_compte_visiteur()
{
	chargement_bloc_action() ;
	
	var xhr = creation_xhr();

	xhr.open('GET', 'ajax/menu_gourmand/deconnexion_compte_visiteur.php');
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send();

	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && xhr.status == 200)
		{	
			chargement_bloc_action() ;
			
			ouverture_alert(alert_basic = '<span>Vous êtes maintenant bien déconnecté.</span><br />') ;	
			cache_bloc_coulissant('#compte_visiteur_connecte_menu');
			
			document.getElementById('login_visiteur_recuperer').value = '' ; 
			
			// On enleve l'image cliquable et on en met une non clicable
			$('.picto_jaime').fadeIn() ;
			$('.picto_jaime_deja_vote').css('display' , 'none') ;
			
			// Remplacement du nom du compte contre visiteur
			document.getElementById('nom_proprietaire_compte_visiteur').innerHTML = 'Visiteur' ; 
			
			// Variable global avec un token
			document.getElementById('token_visiteur').value = ''; 
		}
	}
}

// FACEBOOK API CONNECT

// FACEBOOK API CONNECT

// This is called with the results from from FB.getLoginStatus().
function statusChangeCallback(response , type) {
// The response object is returned with a status field that lets the
// app know the current login status of the person.
// Full docs on the response object can be found in the documentation
// for FB.getLoginStatus().
if (response.status === 'connected') {
	connection_facebook_ajax(type) ; 
} else if (response.status === 'not_authorized') {
} else {
}
}

// This function is called when someone finishes with the Login
// Button.  See the onlogin handler attached to it in the sample
// code below.
function checkLoginState(type) {
FB.getLoginStatus(function(response) {
statusChangeCallback(response, type);
});
}


window.fbAsyncInit = function() {
FB.init({
appId      : '1462284764045188',
cookie     : true,  // enable cookies to allow the server to access 
			// the session
xfbml      : true,  // parse social plugins on this page
version    : 'v2.1' // use version 2.1
});

	FB.getLoginStatus(function(response) {
	statusChangeCallback(response);
	});
}; 

function connection_facebook_ajax(type) 
{
	chargement_bloc_action() ; 
	
	FB.api('/me', function(response) {
	// On envoi en ajax une petite connection normal
		if(window.XMLHttpRequest)
		{
			var xhr = new XMLHttpRequest();
		}
		// pour internet explorer
		else if(window.ActiveXObject) 
		{
			var xhr = new ActiveXObject("Microsoft.XMLHTTP");  
		}

		else 
		{ // XMLHttpRequest non supporté par le navigateur  
			alert("Votre navigateur ne supporte pas nos applications, veuillez le mettre à jour");  
			return;  
		}
		
		xhr.open('GET', '/applications/facebook/connect_facebook_app.php?type=' + type);
		xhr.send(null);
	
		xhr.onreadystatechange = function() 
		{
			if (xhr.readyState == 4 && xhr.status == 200)
			{
				chargement_bloc_action() ;
				
				// Si on a quelque chose à afficher on l'affiche
				var tableau_json = JSON.parse(xhr.responseText) ;
				
				// Si le visiteur est déjà connecté c'est ok on ne fait rien de plus! 
				if(tableau_json['deja_connecte'] == 1)
				{}
				else
				{
					// Si c'est une inscrption, on fait en sorte qu'il ouvre le code promo à ajouter
					if(tableau_json['inscription'] == 1)
					{
						// On fait comme si on se connecte
						action_tout_juste_connecte(tableau_json , tableau_json['login'], function()
						{
							// La fonction de validation ou de quitter fera apparaitre le compte
							$('#bloc_ajout_promo').fadeIn() ;
						}) ; 
					}
					else
					{
						// Sinon on ouvre tout normalement
						action_tout_juste_connecte(tableau_json , tableau_json['login'], function()
						{
							affichage_compte_visiteur() ; 
						}) ; 
					}
						
					if(tableau_json['texte_a_afficher'] != '')
					{
						ouverture_alert(alert_basic = tableau_json['texte_a_afficher']);
					}
					else
					{
						ouverture_alert(alert_basic = '<span>Hello ' + tableau_json['login'] + ', bon retour sur donnemoifaim.</span><br />') ;
					}
				}
			}
		}
	});
}

// twttr TWITTER
if (!window.twttr) {
window.twttr = (function (d, s, id) {
var t, js, fjs = d.getElementsByTagName(s)[0];
if (d.getElementById(id)) return; js = d.createElement(s); js.id = id;
js.src = "//platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs);
return window.twttr || (t = { e: [], ready: function (f) { t.e.push(f) } });
} (document, "script", "twitter-wjs"));
}

function retour_connection_compte()
{
	document.getElementById('bloc_creation_compte_visiteur').style.display = 'none' ;
	document.getElementById('bloc_mdp_compte_visiteur').style.display = 'none' ;
	document.getElementById('bloc_renitialisation_mdp').style.display = 'none' ;
	
	$( "#bloc_connection_compte_visiteur" ).fadeIn(200);
}
function affichage_creation_compte() 
{
	document.getElementById('bloc_connection_compte_visiteur').style.display='none' ;
	document.getElementById('bloc_mdp_compte_visiteur').style.display = 'none' ;
	$( "#bloc_creation_compte_visiteur" ).fadeIn(200);
}

function creation_compte()
{
	chargement_bloc_action() ;
	
	var xhr = creation_xhr();
	
	var login = document.getElementById('form_login_compte_visiteur').value,
	mdp = document.getElementById('form_mdp_compte_visiteur').value,
	mdp_check = document.getElementById('form_mdp_verif_compte_visiteur').value,
	ville = document.getElementById('form_ville_compte_visiteur').value,
	email = document.getElementById('form_mail_compte_visiteur').value,
	capatch = document.getElementById('form_capatch_compte_visiteur').value, 
	code_promo = document.getElementById('form_code_promo_compte_visiteur').value ;
	
	if(document.getElementById('label_newsletter_visiteur').checked)
	{
		var newsletter = 1 ; 
	}
	else
	{
		var newsletter = 0 ; 
	}
	
	xhr.open('POST', 'ajax/menu_gourmand/creer_compte_visiteur.php');
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send('login=' + login + '&mdp=' + mdp + '&mdp_check=' + mdp_check + '&ville=' + ville +  '&email=' + email  + '&capatch=' + capatch + '&code_promo=' + code_promo + '&newsletter=' + newsletter);

	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && xhr.status == 200)
		{	
			chargement_bloc_action() ;
			
			var reponse_text = JSON.parse(xhr.responseText) ; 
			if(reponse_text['erreur'] == '')
			{
				ouverture_alert(alert_basic = 'Félicitations ' + reponse_text['login'] + ', votre compte a bien été créé ! ');
				action_tout_juste_connecte(reponse_text , reponse_text['login'] , function()
				{
					affichage_compte_visiteur() ; 
				}) ; 
			}
			else
			{
				// On ouvre le bloc full screen avec un contenu de gestion des erreurs
				ouverture_alert(retour_erreur_formulaire = reponse_text['erreur']); 
			}
		}
	}
}

function verif_pseudo_utilisateur()
{
	var xhr = creation_xhr();
	
	var login = document.getElementById('form_login_compte_visiteur').value ; 

	xhr.open ('POST', 'ajax/menu_gourmand/verif_pseudo_visiteur.php');
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send('login=' + login ) ; 

	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && xhr.status == 200) 
		{
			var texte = xhr.responseText.split("||");  
			document.getElementById('verif_login_utilisateur').innerHTML = texte; 
		}
	}
}

function cache_compte_visiteur()
{	
	cache_bloc_coulissant('#compte_visiteur') ;
	document.getElementById('bloc_creation_compte_visiteur').style.display='none'; 
	document.getElementById('bloc_connection_compte_visiteur').style.display='block'; 
}
	
function affichage_mdp_oublier() 
{ 
	document.getElementById('bloc_connection_compte_visiteur').style.display='none' ;
	$( "#bloc_mdp_compte_visiteur" ).fadeIn(200);
}
function envoyer_mdp_oublier()
{
	var xhr = creation_xhr();

	envoi_mail = document.getElementById('oubli_adresse_mail_visiteur').value; 
	
	xhr.open ('POST', 'ajax/menu_gourmand/envoi_mdp_visiteur.php');
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send('envoimail=' + envoi_mail ) ; 

	xhr.onreadystatechange = function()
	{
		if (xhr.readyState == 4 && xhr.status == 200) 
		{
			var response_text = xhr.responseText ; 
			// Si tout c'est bien passé on affiche un alert et on ferme la fenetre
			if(response_text == '')
			{
				ouverture_alert(alert_basic = '<span>Un mail vient de vous être envoyé.</span><br />') ;
				retour_mdp_oublie() ; 			
			}
			else
			{
				// On ouvre notre alert des erreurs de formulaire
				ouverture_alert(retour_erreur_formulaire = response_text) ; 
			}
			
		}
	}
}
function retour_mdp_oublie()
{
	document.getElementById('bloc_mdp_compte_visiteur').style.display = 'none' ; 	
	$( "#bloc_connection_compte_visiteur" ).fadeIn(200);
}

function info_image_en_plus()
{
	if(document.getElementById('bloc_info_coulissant').style.display == 'none' )
	{
		document.getElementById('bloc_info_coulissant').style.display  = 'block' ;
			
		$('#button_fleche_bas').animate({bottom:'-100px'} , 300, function()
		{
			$('#bloc_info_coulissant').animate({bottom:"0"}, 500) ;
			//show du fond rouge
			$('#fond_info_complementaire').fadeIn(200) ;
			document.getElementById('button_fleche_bas').style.display = 'none' ;
			
			if(typeof(id_plat_actuelle) != 'undefined')
			{
				voir_tout_plats(id_plat_actuelle) ;
			}
			
			// On créer un petit xhr qui va juste rajouter 1 dans la base de donnée pour les gens interessé !
			var xhr = creation_xhr();
			
			xhr.open ('GET', 'ajax/menu_gourmand/statistique_interesse.php');
			xhr.send() ; 
		}); 
		
		// Ca fait moche que ce soit coupé quand on regarde les infos complémentaire
		ameliorer_lisibilite_menu_gourmand() ; 

		voir_photo_entier() ; 
		
	}
	else
	{
		document.getElementById('button_fleche_bas').style.display = 'block' ; 
		$('#bloc_info_coulissant').animate({bottom:"-100%"}, 500 , function()
		{
			document.getElementById('bloc_info_coulissant').style.display  = 'none' ; 
			$('#button_fleche_bas').animate({bottom:"0"},300) ;
			voir_photo_entier() ;
			
			// Ca fait moche que ce soit coupé quand on regarde les infos complémentaire
			ameliorer_lisibilite_menu_gourmand() ; 
		}); 
		$('#fond_info_complementaire').fadeOut(200);
	}
	
	if(history.pushState)
	{
		history.pushState(null, null);
	}
}

function jaime_plat()
{
	var xhr = creation_xhr();

	var token_visiteur = document.getElementById('token_visiteur').value ; 
	
	xhr.open('GET', 'ajax/menu_gourmand/jaime_traitement.php?token_visiteur=' + token_visiteur);
	xhr.send(null);

	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && xhr.status == 200)
		{
			var reponse_ajax = xhr.responseText; 
			
			if(reponse_ajax == 'non_connecte')
			{
				voir_bloc_coulissant('#compte_visiteur') ;
				
				formulaire_compte_visiteur_connexion.onsubmit = function()
				{
					connection_compte_visiteur(jaime_plat);
					return false ; 
				} ; 
			}
			// si on ajoute met j'aime pour la premiere fois
			else if (reponse_ajax == 'ok_vote')
			{ 
				document.getElementById('nombre_jaime_plat').innerHTML = parseInt(document.getElementById('nombre_jaime_plat').innerHTML) + 1 ; 
				
				// On enleve l'image cliquable et on en met une non clicable
				$('.picto_jaime').css('display' , 'none') ;
				$('.picto_jaime_deja_vote').fadeIn(200) ;

				// Si on aime un plat alors que le bloc des j'aimes est ouvert on le recharge
				if(document.getElementById('bloc_mes_jaimes').style.display != 'none')
				{
					affichage_mes_jaimes() ;
				}
				
			}
			else
			{
				ouverture_alert(alert_basic = reponse_ajax) ; 
			}
		}
	}
}

function affichage_compte_visiteur()
{
	chargement_bloc_action() ;
	
	var xhr = creation_xhr(); 
	
	xhr.open('GET', 'ajax/menu_gourmand/affichage_compte_visiteur.php');
	xhr.send(null);
	
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && xhr.status == 200)
		{
			chargement_bloc_action() ;
			
			if(xhr.responseText == 'non_connecte')
			{
				voir_bloc_coulissant('#compte_visiteur') ;
			}
			else
			{
				document.getElementById('bloc_actu_site').innerHTML =  xhr.responseText;
					
				// Rajouter le contenu à allez chercher en ajax
				voir_bloc_coulissant('#compte_visiteur_connecte_menu') ;
				
				$('.bloc_apparait_menu_visiteur').fadeOut(300 , function()
				{
					var temps_abonnement_compte_visiteur = document.getElementById('temps_abonnement_compte_visiteur_ajax').value ; 
					// On va donc récupérer les secondes du temps d'abonnement
					if(temps_abonnement_compte_visiteur)
					{
						if(temps_abonnement_compte_visiteur > 0)
						{
							document.getElementById('bandeau_compte_visiteur_statut_off').style.display = 'none' ; 
							
							// Si il est abonné on va également s'interessé a combien de jours il lui reste le time divisé par le nombre de seconde dans une journée
							var jour_abonnement_compte_visiteur = parseInt(temps_abonnement_compte_visiteur / 86400) ; 
							
							if(jour_abonnement_compte_visiteur > 1)
							{
								var mettre_un_s = 's' ;
							}
							else
							{
								jour_abonnement_compte_visiteur = 1 ;
								var mettre_un_s = 's' ; 
							}
							
							document.getElementById('temps_abonnement_compte_visiteur_restant').innerHTML = jour_abonnement_compte_visiteur + ' jour' + mettre_un_s; 
							
							if(document.getElementById('bandeau_compte_visiteur_statut_on').style.display == 'none')
							{
								refermer_bandeau_promo('bandeau_compte_visiteur_statut_on') ;
							}
						}
						else
						{
							document.getElementById('bandeau_compte_visiteur_statut_on').style.display = 'none' ; 
							
							if(document.getElementById('bandeau_compte_visiteur_statut_off').style.display == 'none')
							{
								refermer_bandeau_promo('bandeau_compte_visiteur_statut_off') ;
							}
						}
					}
				
					$('#bloc_actu_site').fadeIn(300) ;
					
					// On enleve la bulle de notification
					$('#bulle_notification_compte_visiteur').hide() ; 
					$('#notification_compte_visiteur').hide() ; 
				}) ; 
			}
		}
	}
}

function affichage_events()
{
	chargement_bloc_action() ;
	
	var xhr = creation_xhr(); 
	
	xhr.open('GET', 'ajax/menu_gourmand/affichage_event_dmf.php');
	xhr.send(null);
	
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && xhr.status == 200)
		{ 
			chargement_bloc_action() ;
			
			if(xhr.responseText == 'non_connecte')
			{
				voir_bloc_coulissant('#compte_visiteur') ;
			}
			else
			{
				document.getElementById('bloc_event_dmf').innerHTML =  xhr.responseText;
					
				// Rajouter le contenu à allez chercher en ajax
				voir_bloc_coulissant('#compte_visiteur_connecte_menu') ;
				
				$('.bloc_apparait_menu_visiteur').fadeOut(300 , function()
				{
					$('#bloc_event_dmf').fadeIn(300) ;
				}) ;
			}
		}
	}
}

function affichage_mes_jaimes()
{
	chargement_bloc_action() ;
	
	var xhr = creation_xhr(); 
	
	xhr.open('GET', 'ajax/menu_gourmand/affichage_mes_jaimes.php');
	xhr.send(null);
	
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && xhr.status == 200)
		{
			chargement_bloc_action() ;
			
			if(xhr.responseText == 'non_connecte')
			{
				voir_bloc_coulissant('#compte_visiteur') ;
			}
			else
			{
				document.getElementById('bloc_mes_jaimes').innerHTML = xhr.responseText ;
				
				$('.bloc_apparait_menu_visiteur').fadeOut(300 , function()
				{
					$('#bloc_mes_jaimes').fadeIn(300) ;
				}) ; 
			}
		}
	}
}

function affichage_mes_points()
{
	chargement_bloc_action() ;
	
	var xhr = creation_xhr(); 
	
	xhr.open('GET', 'ajax/menu_gourmand/affichage_mes_points.php');
	xhr.send(null);
	
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && xhr.status == 200)
		{
			chargement_bloc_action() ;
			
			if(xhr.responseText == 'non_connecte')
			{
				voir_bloc_coulissant('#compte_visiteur') ;
			}
			else
			{
				document.getElementById('bloc_mes_points').innerHTML = xhr.responseText ;
				
				// On reload les buttons de twitter
				twttr.widgets.load() ;
				
				// Idem pour facebook like
				FB.XFBML.parse(); 
				
				$('.bloc_apparait_menu_visiteur').fadeOut(300 , function()
				{
					$('#bloc_mes_points').fadeIn(300) ;
				}) ;
			}
		}
	}
}

function sup_jaime_plat(idimage, choix)
{
	// On sauvegarde l'idimage à sup pour le récupérer plus tard si l'idimage existe
	if(typeof(idimage) != 'undefined')
	{
		idimage_jaime_sup = idimage ;
	}
	
	if(typeof(reponse_fonction) != 'undefined' && reponse_fonction != '')
	{
		if(reponse_fonction == 'ok')
		{
			chargement_bloc_action() ;
			
			var xhr = creation_xhr(); 
			
			var token_visiteur = document.getElementById('token_visiteur').value ; 
			
			xhr.open('POST', 'ajax/menu_gourmand/supp_jaime_plat.php');
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhr.send('idimage=' +  idimage_jaime_sup + '&token_visiteur=' + token_visiteur);

			xhr.onreadystatechange = function() 
			{
				if (xhr.readyState == 4 && xhr.status == 200)
				{
					chargement_bloc_action() ;
					
					$( "#jaime_plat" + idimage_jaime_sup ).fadeOut(200); 
					
					// Si l'image que l'on supprime est l'image en cours alors on restaure le bouton
					if(idimage_jaime_sup == id_plat_actuelle)
					{
						document.getElementById('nombre_jaime_plat').innerHTML = parseInt(document.getElementById('nombre_jaime_plat').innerHTML) - 1 ; 
						
						// On enleve l'image cliquable et on en met une non clicable
						$('.picto_jaime').fadeIn() ;
						$('.picto_jaime_deja_vote').css('display' , 'none') ;
					}
				}
			}
		}
		else
		{
			// On vide la variable pour pouvoir la réutiliser
			reponse_fonction = '' ; 
		}
	}
	else
	{
		// Récupération du nom de la fonction a rappeler pour savoir la réponse
		fonction_a_appeler = sup_jaime_plat; 
		ouverture_alert(confirm_action = 'Voulez-vous vraiment supprimer ce plat de la liste de vos plats que vous aimez ?') ;
	}
	
	// On vide la reponse fonction pour que le callback soit rappeler
	reponse_fonction = '' ; 
}
function renitialisation_mdp_visiteur()
{
	chargement_bloc_action() ;
	
	// Initialisation des variables 
	var nouveau_mdp_visiteur = document.getElementById('nouveau_mdp_visiteur').value,  
	lien_unique_renitialisation = document.getElementById('lien_unique_renitialisation').value ; 
	
	var xhr = creation_xhr(); 
	
	xhr.open('POST', 'ajax/menu_gourmand/renitialiser_mpd_visiteur.php');
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send('nouveau_mdp_visiteur=' + nouveau_mdp_visiteur + '&lien_unique_renitialisation=' + lien_unique_renitialisation);
	
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && xhr.status == 200)
		{
			chargement_bloc_action() ;
			
			var reponse_texte = xhr.responseText ; 
			
			if(reponse_texte == '')
			{
				ouverture_alert(alert_basic = 'Votre mot de passe a bien été réinitialisé, vous pouvez à présent vous connecter');
				
				retour_connection_compte(); 
			}
			else
			{
				ouverture_alert(alert_basic = reponse_texte) ; 
			}
		}
	}
}
// Permet de proteger au clic droit sur les images les images
function protection_image()
{
	// Si on à pas déjà montré l'image
	if(typeof(proctection_image_deja_check) == 'undefined')
	{
		ouverture_alert(alert_basic = '<span>Les images sont protégées par des droits d\'auteur :<br /><br />- Pour un usage personnel ou professionnel lié au web merci de faire une référence à DonneMoiFaim sur votre site internet (exemple dans la page partenaire) : <br /> <input onclick="this.select();"class="input" style="width:100%; min-width:200px; max-width:500px" value=\'&lt;a  href="' + nom_domaine_site + '"&gt;' + nom_domaine_site + '&lt;/a&gt; \'/><br /><br />- Tout usage commercial des images (revente, etc..) est strictement interdit. </span>', taille_box = '300px' );
		
		proctection_image_deja_check = 1;
	}
}
function ouvrir_bloc_reduction()
{
	chargement_bloc_action() ;
	
	// Vérification de connection
	var xhr = creation_xhr(); 
	
	xhr.open('GET', 'ajax/menu_gourmand/verif_connexion_compte.php');
	xhr.send();
	
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && xhr.status == 200)
		{
			chargement_bloc_action() ;
			
			var tableau_json = JSON.parse(xhr.responseText) ; 
			
			if(tableau_json['connection'] == 'connecte')
			{
				if(tableau_json['access_reduction'] == 'ok')
				{
					// Si la personne n'est pas abonné on va ouvrir le mini block pour demandé ce que l'utilisateur veut
					 voir_bloc_coulissant('#bloc_reduction_plat') ;
				}
				else if(tableau_json['access_reduction'] == 'non')
				{
					// Si il est connecté et qu'il est abonné c'est bon on coulisse le bloc 
					faire_disparaitre_bloc_non_abo_reduction_choix() ; 
				}
			}
			else
			{
				voir_bloc_coulissant('#compte_visiteur') ;
			}
		}
	}
}

function faire_disparaitre_bloc_non_abo_reduction_choix()
{
	if(document.getElementById('bloc_non_abo_reduction_choix').style.display == 'none')
	{
		$('#bloc_non_abo_reduction_choix').fadeIn() ;
	}
	else
	{
		$('#bloc_non_abo_reduction_choix').fadeOut() ;
	}
}

function load_pub_reduction()
{
	// On ouvre le bloc qui dit que c'est entrain de charger
	$('#chargement_pub_reduction_en_cour').fadeIn() ; 
	
	// Chargement du script de teads
	(function (d) {
			var js, s = d.getElementsByTagName('script')[0];
			js = d.createElement('script');
			js.async = false;
			js.src = 'https://as.ebz.io/api/choixPubJS.htm?pid=1140305&screenLayer=1&mode=NONE&home=https://donnemoifaim.fr/'
			s.parentNode.insertBefore(js, s);
			
			// Si il existe bien un les ecouteurs load
			if(typeof(addEventListener) != 'undefined')
			{
				// Dès que c'est chargé on envoi une fonction
				js.addEventListener("load", action_apres_avoir_regarder_pub_reduction , false) ;
			}
			else
			{
				// Tant pis on lance normal si la personne à un ancien navigateur
				action_apres_avoir_regarder_pub_reduction() ; 
			}
	})(window.document);
}

function action_apres_avoir_regarder_pub_reduction()
{
	// Après au moins 10 secondes on ouvre le tout
	setTimeout(function()
	{
		// On peut fermer le chargement c'est bon
		$('#chargement_pub_reduction_en_cour').fadeOut() ;
		
		// Ouverture de l'iframe pour crééer la session
		document.getElementById('iframe_pub_reduction').src = 'applications/iframe_pub_reduction.php' ; 
		document.getElementById('iframe_pub_reduction').style.display = 'block' ; 
		
		// On fait aussi apparaitre après 10 secondes le liens qui servira à accéder à la réducton
		document.getElementById('bloc_acces_reduction_gratuit').style.display = 'block' ; 
	} , 10000) ;
}

function fermer_message_access_reduction()
{
	$('#bloc_acces_reduction_gratuit').fadeOut();
	
	document.getElementById('iframe_pub_reduction').style.display = 'none' ; 
}

function ouverture_abonnement_compte_visiteur_premium(fonction_retour_button)
{
	if(typeof(fonction_retour_button) != 'undefined')
	{
		// On met le onclick de la fonction normale avec a fermeture avec le callback
		document.getElementById('retour_abonnement_visiteur').onclick = function()
		{
			cache_bloc_coulissant('#bloc_abonnement_visiteur' , fonction_retour_button) ;
		}
	}
	else
	{
		// On refaire tout simplement le onclick en mode normal
		document.getElementById('retour_abonnement_visiteur').onclick = function(){
			cache_bloc_coulissant('#bloc_abonnement_visiteur');
		}
	}
	
	// Si il est pas abonné on lui propose de le faire
	voir_bloc_coulissant('#bloc_abonnement_visiteur') ;
}
function apparaitre_bon_reduction()
{
	var id_reduction = document.getElementById('input_id_reduction').value ; 
	
	var xhr = creation_xhr(); 
	
	xhr.open('POST', 'ajax/menu_gourmand/generateur_bon_reduction.php');
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send('id_reduction=' + id_reduction);
	
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && xhr.status == 200)
		{
			var reponse_texte = xhr.responseText ;
			
			ouverture_alert(alert_basic = reponse_texte , taille_box = '100%') ;
			
			var url_image = document.getElementById('image_plat').scr ;
			
			// Si c'est sur mobile on enleve l'impression
			if(typeof(systeme_mobile) != 'undefined')
			{
				document.getElementById('impression_bon_commande').style.display = 'none' ;  
			}
		}
	}
}

function calcule_total_premium(id_offre)
{
	// Récupération du tarrif associé 
	document.getElementById('champ_prix_remplissage').value = document.getElementById('offre_premium' + id_offre).value ; 
	
	// champ_nom_remplissage pour le nom de ce que va payer la personne
	if(id_offre == 1)
	{
		var champ_nom_remplissage = 'Offre découverte compte visiteur premium' ;
	}
	else if(id_offre == 2)
	{
		var champ_nom_remplissage = 'Offre campeur compte visiteur premium' ;
	}
	else if(id_offre == 3)
	{
		var champ_nom_remplissage = 'Offre sumo compte visiteur premium' ;
	}
	
	document.getElementById('champ_nom_remplissage').value = champ_nom_remplissage ; 
	
	// Récupération du login visiteur
	document.getElementById('champ_custum_remplissage').value = document.getElementById('login_visiteur_recuperer').value + '-||-' + id_offre ; 
	
	// On scroll jusqu'au paypal par exemple pour les mobiles 
	scroll_to('#bloc_abonnement_visiteur', '#bloc_input_submit_form_visiteur_premium') ; 
}
// On créer un tableau des blocs que l'on veut mettre dans le tuto
bloc_etape_tuto = ['bloc_partage_plat_categorie' , 'bloc_jaime_plat_categorie' , 'bloc_geo_aleatoire' , 'button_fleche_bas' , '' , '' ] ;
texte_etape_tuto = ['Partagez de délicieux plats où vous voulez, quand vous voulez ! ' , ' Aimez ce plat pour le retrouver facilement et soutenir son créateur.' , 'Recherchez de la manière qui vous convient le mieux : géolocalisé ou aléatoire' , 'Retrouvez toutes les informations liées aux créateurs du plat : adresse, avis, etc...' , 'Cliquez sur le plat pour pouvoir le voir en entier, vous pouvez le zoomer également ! ' , 'Merci et Bonne navigation ! L\'équipe DonneMoiFaim.' ]

function display_bloc_demande_tuto()
{
	var bloc_demande_tuto_nouveau = document.getElementById('bloc_demande_tuto_nouveau') ; 
	
	if(bloc_demande_tuto_nouveau.style.display == 'none')
	{
		bloc_demande_tuto_nouveau.style.display = 'block' ;
		
		// On anime pour le faire apparaitre
		$('#bloc_demande_tuto_nouveau').animate({top : '-10px'} , 600) ; 
	}
	else
	{
		// On anime pour le faire disparaitre
		$('#bloc_demande_tuto_nouveau').animate({top : '-200px'} , 600) ; 
	}
}

function premiere_visite()
{
	// On fait disparaitre la demande de tuto 
	display_bloc_demande_tuto() ; 
	
	// On rend non visible les éléments du site
	var nombre_bloc = bloc_etape_tuto.length ; 
	
	for(i=0 ; i < nombre_bloc; i++)
	{
		// Si il n'est pas vide
		if(bloc_etape_tuto[i] != '')
		{
			document.getElementById(bloc_etape_tuto[i]).style.display = 'none' ;
			document.getElementById(bloc_etape_tuto[i]).style.zoom = '300%' ;
		}
	}
	
	document.getElementById('bloc_info_plat').style.display = 'none' ; 
	document.getElementById('button_donnemoifaim').style.display = 'none'; 
	
	
	var hauteur_bloc_tuto = document.body.clientHeight - 50 ; 
	// On ouvre le bloc du tuto
	document.getElementById('tuto_premiere_visite').style.display = 'inline-block' ; 
	$('#tuto_premiere_visite').fadeIn(500 , function()
	{
		suivant_etape_tuto(0); 
		
		$('#picto_suivant_tuto').fadeIn(300) ;
		$('#bloc_button_tuto').fadeIn(300);
	});
}
function suivant_etape_tuto(etape)
{
	
	// Si ce n'est pas vide (certain ne nécessite que du texte)
	if(bloc_etape_tuto[etape] != '')
	{
		$('#' + bloc_etape_tuto[etape]).fadeIn(500 , function()
		{
			$('#' + bloc_etape_tuto[etape]).animate({zoom : '100%' } , 300) ; 
		}) ;
		ecrire_texte_tuto(etape) ; 
	}
	else
	{
		ecrire_texte_tuto(etape) ; 
	}

	// Si c'est le dernier alors on ferme tout a la fin
	if(etape == bloc_etape_tuto.length - 1)
	{
		document.getElementById('bloc_button_tuto').style.display = 'none' ;
		document.getElementById('picto_suivant_tuto').style.display = 'none' ; 
		
		// On va en faire un rond 
		document.getElementById('tuto_premiere_visite').style.borderRadius = '100px' ;  
		$('#tuto_premiere_visite').animate({height : '200px' , width: '200px' } , 3000 , function()
		{			
			document.getElementById('texte_tuto_premiere_visite').innerHTML = '' ;
			document.getElementById('icone_dmf_tuto').style.display = 'inline' ;
			
			// Si jamais pour x raison ça beug, on le supprime quand on clique dessus
			document.getElementById('tuto_premiere_visite').onclick = function()
			{
				this.style.display = 'none' ;
			}; 
			
			setTimeout(function()
			{
			// Animation pour le cacher hors de l'écran
			$('#tuto_premiere_visite').fadeOut();
			
			// On remet ce qu'on avait enlever pour voir mieux
			$('#bloc_info_plat').fadeIn(300) ;
			$('#button_donnemoifaim').fadeIn(300) ;
			}, 1000);
		}) ;
	}
	else
	{
	
		// On temporise avec un setTimeout pour éviter qu'on clique trop de fois sur le slide
		setTimeout(function()
		{
			// Permet de mettre au button suivant du tuto l'étape à faire ensuite
			picto_suivant_tuto.onclick = function()
			{
				suivant_etape_tuto(etape + 1);
			}
		
			// Variable global car hammer n'arrive pas a recupérer les variable normal
			position_tuto_animation = etape ; 
			
			// évènement pour le tutoriel slice sur le coté pour faire passer à l'étape suivante 
			var hammerTutoSlice = new Hammer(document.getElementById('tuto_premiere_visite'));
					
			hammerTutoSlice.get('swipe').set({ enable: true });
			
			hammerTutoSlice.on('swipe', function(ev) {
				suivant_etape_tuto(position_tuto_animation + 1);
			});
		} , 300) ; 
	}
}

function ecrire_texte_tuto(etape)
{
	var texte_tuto_premiere_visite = document.getElementById('texte_tuto_premiere_visite') ; 
	
	// On enleve le texte
	$(texte_tuto_premiere_visite).fadeOut(300 , function()
	{
		texte_tuto_premiere_visite.innerHTML = texte_etape_tuto[etape] ;
		// On le remet
		$(texte_tuto_premiere_visite).fadeIn(300) ; 
	}) ; 
	
	document.getElementById('button_tuto' + etape).style.backgroundColor = '#bc210f' ; 
}

// API roulette ZOOM image //
// Permet d'attribuer l'évènement à la roulette de la souris compatible tout navigateur
if(typeof(window.addEventListener) != 'undefined')
{
	window.addEventListener('load' , function()
	{
		if(typeof(addEventListener) != 'undefined')
		{
			// IE9, Chrome, Safari, Opera
			document.addEventListener("mousewheel", RouletteEventAction, false);
			// Firefox
			document.addEventListener("DOMMouseScroll", RouletteEventAction, false);
		}
		else
		{
			document.attachEvent("onmousewheel", RouletteEventAction);
		}
		
		var hauteurDocumentMoinsHeaderFooter = document.getElementById('wrap').offsetHeight - 100 || document.getElementById('wrap').style.pixelHeight - 100 ;
		
		// Definition de la hauteur de bloc_image_plat pour servir de conteneur 
		document.getElementById('bloc_image_plat').style.height = hauteurDocumentMoinsHeaderFooter  + 'px' ;
		
		// Si on touche l'élément conteneur
		var hammertime = new Hammer(document.getElementById('bloc_image_plat'));
		
		hammertime.get('pinch').set({ enable: true });
		hammertime.get('rotate').set({ enable: true });
		
		hammertime.on('pinch rotate', function(ev) {
			// Si le scale est en dessous de 1 c'est que c'est une réduction 
			if(ev.scale <= 1)
			{
				var direction = -1 ;
			}
			else
			{
				var direction = 1 ;
			}
			ApiZoomImage(direction , 'tactile') ;
		});
		
	}, false) ; 
}

function RouletteEventAction(event) 
{
	// Si on est sur l'élément
	if ($('#bloc_image_plat:hover').length != 0)
	{
		var event = window.event || event ; 
		// On met le -event.detail pour mozilla car il est inversé de sens par rapport au autre
		var deplacement_roulette = event.wheelDelta || -event.detail;
		
		ApiZoomImage(deplacement_roulette , 'roulette') ; 
	}
}
function ApiZoomImage(direction , type)
{
	var imagePlat = document.getElementById('image_plat'); 
	
	var valueTransform = imagePlat.style.transform ;
		
	valueTransform = valueTransform.replace('scale(' , '') ; 
	valueTransform = valueTransform.replace(')' , '') ;
	
	if (direction <= 0)
	{
		// Si la valeur du scale est déjà égale ou inférieur à 0.4 on arrete le rétrécissement
		if(valueTransform <= 0.5)
		{}
		else
		{
			if(type == 'roulette')
			{
				var nouvelleValeur = parseFloat(valueTransform) - 0.1 ;
			}
			else if(type == 'tactile')
			{
				var nouvelleValeur = parseFloat(valueTransform) - 0.005 ;
			}
		}
	}
	else
	{
		// Si la valeur du scale est déjà égale ou supérieur à 3 on arrete le rétrécissement
		if(valueTransform >= 2)
		{}
		else
		{
			if(type == 'roulette')
			{
				var nouvelleValeur = parseFloat(valueTransform) + 0.1 ;
			}
			else if(type == 'tactile')
			{
				var nouvelleValeur = parseFloat(valueTransform) + 0.005 ;
			}
		}
	}
	
	document.getElementById('image_plat').style.transform = 'scale(' + nouvelleValeur +  ')' ;
}

function sliceImagePlat()
{
	// pouvoir passer du doigt les plats en slice
	var hammerTutoSlice = new Hammer(document.getElementById('image_plat'));
					
	hammerTutoSlice.get('swipe').set({ enable: true });

	hammerTutoSlice.on('swipe', function(ev) {
		if(ev.direction == 2)
		{
			chargementimage() ;
		}
		else if(ev.direction == 4)
		{
			// On revient en arrière si le slide est précédent 
			history.back() ; 
		}
	});
}

// on appel l'image une première fois pour le slice
if(typeof(addEventListener) != 'undefined')
{
	window.addEventListener("load", sliceImagePlat, false) ;
}
else
{
	loadEventIEcompatible('sliceImagePlat') ; 
}

function passer_resto()
{
	var xhr = creation_xhr(); 
	
	xhr.open('GET', 'ajax/menu_gourmand/passer_resto.php');
	xhr.send();
	
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && xhr.status == 200)
		{
			// Si on à bien changé les sessions pour le plus afficher de plat du resto on balance le chargementimage
			chargementimage() ;
			
			// on cache le passer ce resto
			$('#bloc_passer_resto').fadeOut() ; 
		}
	}
}

function faire_apparaitre_publicite()
{
	nombre_plat_vue++ ;  
	
	// Si ça fait 15 plats de vue
	if(nombre_plat_vue) 
	{
		// FUTURE POUR METTRE LA PUB
		/*
		var xhr = creation_xhr(); 
		
		xhr.open('GET', 'ajax/menu_gourmand/recuperation_publicite_apparante.php');
		xhr.send();
		
		xhr.onreadystatechange = function() 
		{
			if (xhr.readyState == 4 && xhr.status == 200)
			{
				var responseText = xhr.responseText ; 
				
				if(responseText != 'no-pub')
				{
					document.getElementById('bloc_publicite_apparante').innerHTML = responseText;
					
					// on fait apparaitre la publicité en question
					voir_bloc_coulissant('#bloc_publicite_apparante') ;
				}
			}
		}
		*/
		
		// On remet le nombre de plat vue à 0
		nombre_plat_vue = 0 ; 
	}
}

function recompter_notification()
{
	var xhr = creation_xhr(); 
		
	xhr.open('GET', 'ajax/menu_gourmand/changement_notifications.php');
	xhr.send();
	
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && xhr.status == 200)
		{
			if(xhr.responseText == 0)
			{
				$('#bulle_notification_compte_visiteur').fadeOut() ; 
			}
			else
			{
				document.getElementById('bulle_notification_compte_visiteur').innerHTML = xhr.responseText ;
			}
		}
	}
}

function placement_notification_compte_visiteur()
{
	var notification_compte_visiteur = document.getElementById('notification_compte_visiteur') ; 
	// Mettre a la meme position que le texte + un peu + quand meme 
	notification_compte_visiteur.style.left = document.getElementById('nom_proprietaire_compte_visiteur').offsetLeft + 'px' ; 
}

function notification_compte_visiteur(nombre_notification)
{
	placement_notification_compte_visiteur() ; 
	
	if(nombre_notification > 1)
	{
		texte_a_ecrire = 'Nouvelles notifications' ; 
	}
	else
	{
		texte_a_ecrire = 'Nouvelle notification' ;
	}
	
	// On regarde ce qu'il faut écrire dans l'espace prévue à cette effet
	document.getElementById('texte_a_ecrire_bulle_notification').innerHTML = texte_a_ecrire; 
	
	// On fait apparaitre le bloc de notification 
	$('#notification_compte_visiteur').fadeIn() ; 
}

function ajout_code_promo_visiteur(code_promo)
{ 
	// Si ce n'est pas égale à 6 on ne fait rien 
	if(code_promo.length != 6)
	{}
	else
	{
		chargement_bloc_action() ; 
		
		var xhr = creation_xhr(); 
		
		xhr.open('GET', 'ajax/menu_gourmand/attribution_code_promo.php?code_promo=' + code_promo);
		xhr.send();
		
		xhr.onreadystatechange = function() 
		{
			if (xhr.readyState == 4 && xhr.status == 200)
			{
				chargement_bloc_action() ; 
				
				if(xhr.responseText == 'valide')
				{
					ouverture_alert(alert_basic = 'Votre code est valide. Vous avez bien reçu les avantages décrient dans la promotion.') ;
					
					// On ferme le bloc puis on ouvre le code 
					$('#bloc_ajout_promo').fadeOut(300 , function()
					{
						// On ouvre le compte visiteur
						affichage_compte_visiteur();
					}) ;
				}
				else
				{
					// On ne l'ouvre que si il est fermé
					if(document.getElementById('bloc_full_screen').style.display == 'none')
					{
						ouverture_alert(alert_basic = xhr.responseText) ;
					}
					
					// On éfface le contenu pour pas que ce soit trop sensible
					document.getElementById('champ_input_ajout_code_promo').value = '' ; 
				}
			}
		}
	}
}