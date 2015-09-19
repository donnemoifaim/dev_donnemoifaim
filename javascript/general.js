if(typeof(addEventListener) != 'undefined')
{
	window.addEventListener("load", chargement_page_load, false) ;
}
else
{
	loadEventIEcompatible('chargement_page_load') ; 
}

// Compatbile IE8 load
function loadEventIEcompatible(fonctionExecute)
{
	if(typeof(argument_passe) == 'undefined')
	{
		var argument_passe = '' ; 
	}
	
	// Un sorte d'appel en ajax pour savoir quand s'est chargé
	document.onreadystatechange = function()
	{
		if(document.readyState == 'loaded' || document.readyState == 'complete')
		{
			// On check la compatibility ajout-image
			fonctionExecute.call() ; 
			document.onreadystatechange = null;
			
		}
	}
}

function faire_disparaitre_bloc_et_apparaitre_autre_simple(elementAfaireDisparaitre , elementAfaireApparaitre, calback)
{
	$(elementAfaireDisparaitre).fadeOut(300, function()
	{
		$(elementAfaireApparaitre).fadeIn(300 , function()
		{
			// Si le calback existe bien 
			if(typeof(calback) != 'undefined')
			{
				calback.call() ;
			}
		}) ;
	}) ;
}

function connaitre_mobile_systeme()
{
	var userAgent = navigator.userAgent || navigator.vendor || window.opera;

	if(userAgent.match( /iPad/i ) || userAgent.match( /iPhone/i ) || userAgent.match( /iPod/i ) )
	{
		systeme_mobile = 'os' ; 
		
		// Si c'est iphone on change l'icone de partage pour que ce soit celui associé à iphone
		document.getElementById('picto_partager_plat').src = 'imgs/picto-partage-iphone.png' ; 
	}
	else if( userAgent.match( /Android/i ) )
	{
		systeme_mobile = 'android' ;  
		
		// Si le mec à déjà une session qu'il à déjà vu alors + besoin qu'il revoit le android est sur app
		if(typeof(annonce_app_deja_vu) == 'undefined')
		{
			// Faire apparaitre seulement si on est sur android 
			if(typeof(systeme_mobile) != 'undefined' && systeme_mobile == 'android')
			{
				var time_versionning = new Date().getTime() ;
				
				// On fait apparaitre la bannière de l'application qui est mobile-friendly, pas de plein écran pour google
				faire_apparaitre_banniere_application(systeme_mobile) ; 
			}
		}
	}
	else if(userAgent.match( /windows phone/i ) )
	{
		systeme_mobile = 'windowsPhone' ;
	}
	
	
	// On enleve les élément target qui font beugger l'application et qui sont inutile pour mobile
	if(typeof(systeme_mobile) != 'undefined' )
	{
		$('a[target="_blank"]').removeAttr('target');
	}
}

function faire_apparaitre_banniere_application(application)
{
	// On va créer un hammer.js pour faire en sorte que au swipe ça enleve le block
	var hammerTutoSlice = new Hammer(document.getElementById('banniere_application_' + application));
					
	hammerTutoSlice.get('swipe').set({ enable: true });

	hammerTutoSlice.on('swipe', function(ev) {
		faire_apparaitre_banniere_application(application) ;
	});
	
	// On fait apparaitre normalement
	if(document.getElementById('banniere_application_' + application).style.display == 'none')
	{
		voir_bloc_coulissant('#banniere_application_' + application , function(){}, 'bottom') ; 
	}
	else
	{
		cache_bloc_coulissant('#banniere_application_' + application , function(){}, 'bottom') ;
	}
}

function loadScrollTo()
{
	// A METTRE AU DEBUT POUR EVITER DE BLOQUER la page principale
		/**
		 * Copyright (c) 2007-2014 Ariel Flesler - aflesler<a>gmail<d>com | http://flesler.blogspot.com
		 * Licensed under MIT
		 * @author Ariel Flesler
		 * @version 1.4.11
		 */
		 //Permet le scrollto
		;(function(a){if(typeof define==='function'&&define.amd){define(['jquery'],a)}else{a(jQuery)}}(function($){var j=$.scrollTo=function(a,b,c){return $(window).scrollTo(a,b,c)};j.defaults={axis:'xy',duration:parseFloat($.fn.jquery)>=1.3?0:1,limit:true};j.window=function(a){return $(window)._scrollable()};$.fn._scrollable=function(){return this.map(function(){var a=this,isWin=!a.nodeName||$.inArray(a.nodeName.toLowerCase(),['iframe','#document','html','body'])!=-1;if(!isWin)return a;var b=(a.contentWindow||a).document||a.ownerDocument||a;return/webkit/i.test(navigator.userAgent)||b.compatMode=='BackCompat'?b.body:b.documentElement})};$.fn.scrollTo=function(f,g,h){if(typeof g=='object'){h=g;g=0}if(typeof h=='function')h={onAfter:h};if(f=='max')f=9e9;h=$.extend({},j.defaults,h);g=g||h.duration;h.queue=h.queue&&h.axis.length>1;if(h.queue)g/=2;h.offset=both(h.offset);h.over=both(h.over);return this._scrollable().each(function(){if(f==null)return;var d=this,$elem=$(d),targ=f,toff,attr={},win=$elem.is('html,body');switch(typeof targ){case'number':case'string':if(/^([+-]=?)?\d+(\.\d+)?(px|%)?$/.test(targ)){targ=both(targ);break}targ=$(targ,this);if(!targ.length)return;case'object':if(targ.is||targ.style)toff=(targ=$(targ)).offset()}var e=$.isFunction(h.offset)&&h.offset(d,targ)||h.offset;$.each(h.axis.split(''),function(i,a){var b=a=='x'?'Left':'Top',pos=b.toLowerCase(),key='scroll'+b,old=d[key],max=j.max(d,a);if(toff){attr[key]=toff[pos]+(win?0:old-$elem.offset()[pos]);if(h.margin){attr[key]-=parseInt(targ.css('margin'+b))||0;attr[key]-=parseInt(targ.css('border'+b+'Width'))||0}attr[key]+=e[pos]||0;if(h.over[pos])attr[key]+=targ[a=='x'?'width':'height']()*h.over[pos]}else{var c=targ[pos];attr[key]=c.slice&&c.slice(-1)=='%'?parseFloat(c)/100*max:c}if(h.limit&&/^\d+$/.test(attr[key]))attr[key]=attr[key]<=0?0:Math.min(attr[key],max);if(!i&&h.queue){if(old!=attr[key])animate(h.onAfterFirst);delete attr[key]}});animate(h.onAfter);function animate(a){$elem.animate(attr,g,h.easing,a&&function(){a.call(this,targ,h)})}}).end()};j.max=function(a,b){var c=b=='x'?'Width':'Height',scroll='scroll'+c;if(!$(a).is('html,body'))return a[scroll]-$(a)[c.toLowerCase()]();var d='client'+c,html=a.ownerDocument.documentElement,body=a.ownerDocument.body;return Math.max(html[scroll],body[scroll])-Math.min(html[d],body[d])};function both(a){return $.isFunction(a)||typeof a=='object'?a:{top:a,left:a}};return j}));
}

// Permet de charger le scroll to après jquery 
loadScriptAfterJquery('loadScrollTo') ; 

// Permet de charger asyncrone de google map
function loadScriptGoogleMap(calback) 
{
	lancement_geocalisation = '&signed_in=true&callback=' + calback ;
	
	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp' + lancement_geocalisation;

	document.body.appendChild(script);
}
function ok_calback_google_map()
{
	// Permet de faire fonctionner le calback
}
function chargement_page_load()
{
	// En cas de resize en general voici ce qui ce passe ( notaement pour le header qui est dans toute les pages )
	// Calcule de la hauteur du header
	$(window).resize(function()
	{
		// Si le tutoriel existe
		if(document.getElementById('tuto_premiere_visite'))
		{
			document.getElementById('tuto_premiere_visite').style.height = document.getElementsByTagName('body')[0].offsetHeight - 50 + 'px' || document.getElementsByTagName('body')[0].style.pixelHeight - 50 + 'px' ; 
		}
		// Si le bloc des avis est ouvert alors on enleve les voir plus si le bloc est assez grand
		if(document.getElementById('bloc_avis'))
		{
			if(document.getElementById('bloc_avis').style.display != 'none') 
			{
				if(typeof(display_afficher_plus_avis) != 'undefined')
				{	
					// On balance la fonction qui permet de créer des afficher_plus pour les avis
					display_afficher_plus_avis() ;
				}
			}
		}
		
		// Si on est dans l'ajout de plat
		
		if(document.getElementById('bloc_uploadajout-de-plat'))
		{
			var largueur_fenetre = window.innerWidth || document.body.clientWidth; 
			// Connaitre la largueur d'écran
			
			// Si la largueur est supérieur à 610 on met margin-left à 0 sinon inverse
			if(largueur_fenetre > 610)
			{
				$('#menu_compte_resto').animate({marginLeft : '150px'}) ;
			}
			else
			{
				$('#menu_compte_resto').animate({marginLeft : '0px'}) ;
			}
		}
		// Rendre les blocs apparait responsive en fonction de la hauteur du header 50px
		bloc_apparait_responsive() ;
		
		var header = document.getElementsByTagName('header')[0];
		
		// Si le contenu header existe bien
		if(typeof(contenu_header) != 'undefined')
		{}
		else
		{
			contenu_header = document.getElementById('bloc_contenu_header') ; 
		}
		if(header.offsetHeight)
		{
			hauteur_header = header.offsetHeight ;
		}
		else if(contenu_header.style.pixelHeight)
		{
			hauteur_header = header.style.pixelHeight;
		}
		else
		{
			hauteur_header = 50 ; 
		}
		

		// Resize du responsive du header si le header est ouvert ( donc supérieur à 50 ! )
		if(hauteur_header > 50)
		{
			contenu_header = document.getElementById('bloc_contenu_header') ; 
				
			if(contenu_header.offsetHeight)
			{
				hauteur_contenu_header = contenu_header.offsetHeight ;
			}
			else if(contenu_header.style.pixelHeight)
			{
				hauteur_contenu_header = contenu_header.obj.style.pixelHeight;
			}
			
			$('header').animate({ height:hauteur_contenu_header + 10  }, 300, function(){
				// Si la hauteur du header devient 50 alors il repasse en hidden
				if(hauteur_contenu_header <= 50)
				{
					header.style.overflow = 'hidden' ;
					$('#logo').fadeIn(200); ;
				}
			});
		}
		
		// Placement du bloc de notification seulement si la fonction existe
		if(typeof(placement_notification_compte_visiteur) != 'undefined')
		{
			placement_notification_compte_visiteur() ; 
		}
	}); 
	
	connaitre_mobile_systeme() ; 
}

function partageFacebookUrl(url)
{
	FB.ui(
	{
		method: 'share',
		href: url
	},
	  // callback
	  function(response) {
		if (response && !response.error_code) {
			
			chargement_bloc_action() ;
			
			var xhr = creation_xhr();
		
			xhr.open ('GET', 'ajax/menu_gourmand/share_facebook_calback.php');
			xhr.send();
			
			//Récupération du rapport de l'envoi
			xhr.onreadystatechange = function() 
			{
				//Requête envoyé ( == 4 ) et tout à bien été reçu ( == 200 ) 
				if (xhr.readyState == 4 && xhr.status == 200)
				{
					chargement_bloc_action() ;
					
					// Si la personne n'est pas connecté on ne lui attribus pas de point
					if(xhr.responseText == 'non_connecte')
					{}
					// Si la personne à déjà aimé on va pas à nouveau pouvoir le faire
					else if(xhr.responseText == 'deja_partage')
					{}
					else
					{
						ajoutPointsCompteVisiteur(xhr.responseText , '<span>Merci d\'avoir partagé ce plat ! ' + xhr.responseText + ' points DMF ont été ajoutés à votre compte.</span>'); 
					}
				}
			}
		} else {
		  // On ne fait rien puisque la personne à annulé 
		}
	  }
	);
}

function ajoutPointsCompteVisiteur(nombre_point , texte_afficher)
{
	ouverture_alert(alert_basic = texte_afficher) ;
					
	// On ajoute les points directement sur le compte
	var nombre_point_actuel = parseFloat(document.getElementById('nombre_points_dmf').innerHTML) ;

	document.getElementById('nombre_points_dmf').innerHTML = nombre_point_actuel + parseFloat(nombre_point) ; 	
}

// Charger un script asyncronous
function loadScript(src, callback)
{
  var s,
	  r,
	  t;
  r = false;
  s = document.createElement('script');
  s.type = 'text/javascript';
  s.src = src;
  s.onload = s.onreadystatechange = function() {
	//console.log( this.readyState ); //uncomment this line to see which ready states are called.
	if ( !r && (!this.readyState || this.readyState == 'complete') )
	{
	  r = true;
	  callback();
	}
  };
  t = document.getElementsByTagName('script')[0];
  t.parentNode.insertBefore(s, t);
}

// variable global
fonction_a_appeler= '' ;

function creation_xhr()
{
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
	
	return xhr ;
}
function faire_disparaitre_bloc_apparait()
{
	document.getElementById('bloc_full_click').style.display= 'none' ; 
	
	// Si on est sur le menu gourmant on s'assure que les blocs qui doivent etre la sont bien la 
	if(typeof(ameliorer_lisibilite_menu_gourmand) != 'undefined')
	{
		if(document.getElementById('bloc_info_plat'))
		{
			if(document.getElementById('bloc_info_plat').style.display == 'none')
			{
				// Si uniquement les blocs ne sont pas visible on balance la fonction
				ameliorer_lisibilite_menu_gourmand() ; 
			}
		}
	}
}

// Effet de coulissement des blocs que l'on peut appeler grace à la function
function voir_bloc_coulissant(id_bloc , calback, direction)
{
	// Si le header est visible on le rend invisible 
	if(document.getElementsByTagName('header')[0].style.overflow == 'visible')
	{
		apparaitre_menu_responsive() ; 
	}
	
	// On enleve le # ou le . pour les classes et les ID 
	var id_bloc_formate = id_bloc.substring(1 , id_bloc.length)  ; 
	
	// Si le bloque est déjà ouvert on ne fait rien 
	if(document.getElementById(id_bloc_formate).style.display != 'none' )
	{}
	else
	{
		var classElement = document.getElementById(id_bloc_formate).className ; 
		// Si on veut voir un bloc avec la classe bloc_apparait, il faut enlever les autre
		if(classElement.match(/bloc_apparait/))
		{
			// On met en display none tout les bloc
			$('.bloc_apparait').css('display' , 'none') ; 
			// Remet pour l'animation
			$('.bloc_apparait').css('left' , '-100%' );
		}
		
		// Quand on clique sur le coté le bloc disparait
		document.getElementById('bloc_full_click').style.display= 'block' ; 
		
		// On fait apparaitre ce bloc
		document.getElementById(id_bloc_formate).style.display='block' ;
		document.getElementById(id_bloc_formate).style.opacity = 0 ; 
		
		if(typeof(direction) != 'undefined')
		{
			if(direction == 'left')
			{
				var direction = {left :'0' , opacity : '1'}  ;
			}
			else if(direction == 'right')
			{
				var direction = {right :'0' , opacity : '1'}  ;  
			}
			else if(direction == 'top')
			{
				var direction = {top :'0' , opacity : '1'}  ;  
			}
			else if(direction == 'bottom')
			{
				var direction = {bottom :'0' , opacity : '1'}  ;  
			}
		}
		else
		{
			var direction = {left :'0' , opacity : '1'}  ;  
		}
		
		// Et hop coulissement
		$(id_bloc).animate(direction , 500, function()
		{
			// Si le calback est spécifié on le lance
			if(typeof(calback) != 'undefined' && calback != '')
			{
				calback.call() ; 
			}
		});
		
		// Si on est bien dans le menu-gourmand.php
		if(typeof(ameliorer_lisibilite_menu_gourmand) != 'undefined')
		{
			ameliorer_lisibilite_menu_gourmand() ; 
		}
			
		// Création d'un html history pour pouvoir fermer en faisant précédent
		if(history.pushState)
		{	
			history.pushState(null, null);
		}
	}
}
function cache_bloc_coulissant(id_bloc, calback, direction)
{
	// Si l'id du bloc est le bloc de connection de plus de plat est ouvert on doit enlever le header
	if(document.getElementById('bloc_info_coulissant'))
	{
		if(document.getElementById('bloc_info_coulissant').style.display != 'none')
		{
			faire_disparaitre_header() ; 
		}
	}

	if(typeof(direction) != 'undefined')
	{
		if(direction == 'left')
		{
			var direction = {left :'-100%' , opacity : '0'}  ;
		}
		else if(direction == 'right')
		{
			var direction = {left :'100%' , opacity : '0'}  ;  
		}
		else if(direction == 'top')
		{
			var direction = {top :'-100%' , opacity : '0'}  ;  
		}
		else if(direction == 'bottom')
		{
			var direction = {bottom :'-100%' , opacity : '0'}  ;  
		}
	}
	else
	{
		var direction = {left :'-100%' , opacity : '0'}  ;  
	}
	
	// On enleve le # ou le . pour les classes et les ID 
	var id_bloc_formate = id_bloc.substring(1 , id_bloc.length)  ; 
	
	// Si le bloque est déjà fermé pas la peine de le fermer
	if(document.getElementById(id_bloc_formate).style.display == 'none')
	{}
	else
	{
		$(id_bloc).animate(direction, 500 , function() {
			document.getElementById(id_bloc_formate).style.display='none' ;
			// Si il y a un calback on l'appel
			if(typeof(calback) != 'undefined')
			{
				calback.call();
			}			
			// Pour virer le bloc ou on peut appuyer dessus
			faire_disparaitre_bloc_apparait() ; 
		});
	}
	
	// Si on est bien dans le menu-gourmand.php
	if(typeof(ameliorer_lisibilite_menu_gourmand) != 'undefined')
	{
		ameliorer_lisibilite_menu_gourmand() ; 
	}
}
// Si on clique sur le body
if(typeof(addEventListener) != 'undefined')
{
	document.addEventListener("click",action_clique_window,false);
}
else
{
	document.attachEvent("onclick",action_clique_window);
}

function action_clique_window()
{
	// Si un picto title est ouvert on le ferme
	$('.title_picto').css('display', 'none') ; 
}
// SDK facebook
(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v2.4&appId=1462284764045188";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

//Lorsque la touche précédente du navigateur est appuyé
if(typeof(addEventListener) != 'undefined')
{
	window.addEventListener('popstate', function(event){RetourNavigateur(event)}, false) ;
}
else
{
	window.attachEvent('popstate', function(event){RetourNavigateur(event)}) ;
}

function RetourNavigateur(event)
{
	// Si c'est la partie admin et que c'est les clients 
	var ficheClientAdmin = document.getElementsByClassName('fiche_resto') ; 
	
	if(typeof(ficheClientAdmin[0]) != 'undefined')
	{
		var nombreFicheClient = ficheClientAdmin.length ; 
		// On créer une boucle pour savoir lequel fermer
		for(i=0; i < nombreFicheClient ; i++)
		{
			// On check si c'est ouvert ou fermé 
			if(ficheClientAdmin[i].style.display != 'none')
			{
				// On le referme
				$('#' + ficheClientAdmin[i].id).fadeOut() ;
				
				// On annule la boucle pour arreter
				break; 	
			}
		}
	}
	// Si on est sur l'ajout ou la modif des avis
	if(document.getElementById('bloc_form_avis_utilisateur'))
	{
		if(document.getElementById('bloc_form_avis_utilisateur').style.display != 'none')
		{
			faire_apparaitre_bloc_avis(); 
			var fermeture_application = 1 ;
		}
	}
	// Permet de fermer les bloc alert personnalisé si ils sont ouvert
	if(document.getElementById('bloc_full_screen').style.display != 'none' )
	{
		fermeture_bloc_full_screen(); 
	}
	if(document.getElementById('bloc_full_screen').style.display != 'none' )
	{
		confirm_action(choix = 'non') ; 
		var fermeture_application = 1 ; 
	}
	if(typeof(fermeture_application) == 'undefined')
	{	
		var bloc_apparait = document.getElementsByClassName('bloc_apparait') ;
		
		// Tant que la boucle est correct et que l'élément bloc_apparait[i] existe
		for(var i= 0;  typeof(bloc_apparait[i]) != 'undefined'; i++)
		{
			// Si l'élément est visible il faut le fermer 
			if(bloc_apparait[i].style.display != 'none')
			{
				// On execute la fonction de fermeture des blocs avec l'id
				cache_bloc_coulissant('#' + bloc_apparait[i].id ) ;
				var fermeture_application = 1 ; 
			}
		}
		// On vérifie que le bloque existe bien
		if(document.getElementById('bloc_info_coulissant') && typeof(fermeture_application) == 'undefined')
		// Si le bloque des infos est ouvert on le ferme juste
		if(document.getElementById('bloc_info_coulissant').style.display != 'none' )
		{
			info_image_en_plus();
			var fermeture_application = 1 ; 
		}
	}
	// Si le bloc d'abonnement est ouvert on va avant et que aucune autre fermeture n'à été faites avant
	if(document.getElementById('bloc_choix_abonnement'))
	{
		if(typeof(fermeture_application) == 'undefined')
		{
			if(document.getElementById('bloc_choix_abonnement').style.display != 'none')
			{
				retour_etape_ajout_plat() ; 
				var fermeture_application = 1 ;
			}
		}
	}
	
	// Si la fermeture application n'existe et qu'on est bien dans menu-gourmant pas alors on fait un retour via les images
	if(typeof(fermeture_application) == 'undefined' && typeof(page_actuelle_site) !='undefined' && page_actuelle_site == 'menu-gourmand' )
	{
		if (event.state)
		{
			if(typeof(event.state) !='undefined' && typeof(state_idimage[state_idimage.length - 2]) != 'undefined' && state_idimage.length > 1)
			{ 
				nom_image_history = state_idimage[state_idimage.length - 2] ; 
				
				state_idimage.splice(-1 ,1) ; 
				
				// On précise que l'on ne veut pas que le tableau state_idimage soit a nouveau réécrit avec un variable global on met à 1 pour que ça fonctionne
				arret_state_idimage = 1 ; 
				
				// On reprend le meme type de recherche que précédent de toute manière on a spécifié nom_image_history dont il sera intercepté
				connaitre_type_recherche(type = type_recherche , appel_chargement_image = 1) ;
			}
			else
			{}
		}
	}
}

// Header menu responsive
function apparaitre_menu_responsive()
{
	var header = document.getElementsByTagName('header')[0];
	
	var timestamp = new Date().getTime() ; 
	
	if(header.style.overflow == 'hidden')
	{
		// Pour éviter de casser le design avec un logo trop grand en hauteur
		$('#logo').fadeOut(200);
		
		document.getElementById('button_menu_responsive').src = '/imgs/button_menu_responsive_close.' + timestamp + '.png' ;  
		
		header.style.overflow = 'visible' ; 
		
		contenu_header = document.getElementById('bloc_contenu_header') ; 
		
		if(contenu_header.offsetHeight)
		{
			hauteur_contenu_header = contenu_header.offsetHeight ;
		}
		else if(contenu_header.style.pixelHeight)
		{
			hauteur_contenu_header = contenu_header.style.pixelHeight;
		}
		
		$('header').animate({ height:hauteur_contenu_header}, 300);
	}
	else
	{
		$('header').animate({ height:'50' }, 300 , function() {$('#logo').fadeIn(200) ; header.style.overflow = 'hidden' ; document.getElementById('button_menu_responsive').src = '/imgs/button_menu_responsive.' + timestamp + '.png' ;   } );
	} 
}

function ouverture_alert(choix)
{
	// Si le bloque n'est pas ouvert on l'ouvre
	if(document.getElementById('bloc_full_screen').style.display == 'none')
	{
		reponse_fonction = '' ;
		
		// On rend plus grand la box on fonction du contenu 
		if(typeof(taille_box) != 'undefined' && taille_box != '' )
		{
			document.getElementById('contenu_bloc_full_screen').style.height = taille_box ;
			
			taille_box = '' ; 
		}
		else
		{
			// Sinon on remet la box dans l'etat d'origine
			document.getElementById('contenu_bloc_full_screen').style.height = '200px' ;
		}
		// On veut un affichage des erreurs d'un formulaire
		if(typeof(retour_erreur_formulaire) != 'undefined' && retour_erreur_formulaire != '')
		{
			document.getElementById('contenu_bloc_full_screen').innerHTML = '<span style="font-weight:bold">Erreur formulaire : </span><br /><br />' + retour_erreur_formulaire + '<br /><button onclick="fermeture_bloc_full_screen();" class="reset_button">Corriger</button>' ;
			
			retour_erreur_formulaire = '' ; 
		}
		if(typeof(alert_basic) != 'undefined' && alert_basic != '' )
		{
			document.getElementById('contenu_bloc_full_screen').innerHTML = '<p>' + alert_basic + '</p><br /><button id="button_screen_ok" onclick="fermeture_bloc_full_screen();" class="reset_button">Ok</button>' ;
			
			alert_basic = '' ; 
		}
		// On veut simuler un confirm en javascript
		if(typeof(confirm_action) != 'undefined' && confirm_action != '' )
		{
			document.getElementById('contenu_bloc_full_screen').innerHTML = '<p>' + confirm_action + '</p><br /><button onclick="ouverture_alert(choix = \'ok\');" class="reset_button">Oui</button> <button onclick="ouverture_alert(choix = \'non\');" class="reset_button">Annuler</button>' ; 
			
			confirm_action = '' ; 
		}
		
		// On fait apparaitre le bloc full screen pour demander si l'utilisateur valide son choix
		$('#bloc_full_screen').fadeIn(300) ;
	}
	else
	{
		// Si l'utilisateur a fait un choix
		if(typeof(choix) != 'undefined' && choix != '')
		{
			// On enleve le bloc full screen qui ne sert plus a rien puisqu'un choix à été fait
			fermeture_bloc_full_screen();
			
			reponse_fonction = choix; 
			
			// Si il y a des arguments à passer 
			if(typeof(fonction_a_appeler) != 'undefined' && fonction_a_appeler != '')
			{
				fonction_a_appeler.call() ;
			}
			
			choix = '' ; 
		}
	}
	if(history.pushState)
	{
		history.pushState(null, null);
	}
}

function fermeture_bloc_full_screen()
{
	$('#bloc_full_screen').fadeOut(300) ;
}

function verif_champ(id_champ, taille_condition) 
{
	// Si on veut verifier le mdp
	if(taille_condition == 'verif_mdp')
	{ 
		if(typeof(verif_mdp_comparaison) != 'undefined' )
		{
			if(document.getElementById(id_champ).value == document.getElementById(verif_mdp_comparaison).value)
			{
				document.getElementById('ok_' + id_champ).innerHTML = "<img src='/imgs/okchargement.png' alt='champ valide' width='20px' /> " ; 
			}
			else
			{
				document.getElementById('ok_' + id_champ).innerHTML = "<img src='/imgs/nonchargement.png' alt='champ valide' width='20px' /> " ; 
			}
		}
	}
	else
	{
		// Si on veut verifier les mails
		if(taille_condition == 'mail')
		{
			var reg = new RegExp('^[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*@[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*[\.]{1}[a-z]{2,6}$', 'i') ;
			if(reg.test(document.getElementById(id_champ).value))
			{
				document.getElementById('ok_' + id_champ).innerHTML = "<img src='/imgs/okchargement.png' alt='champ valide' width='20px' /> " ; 
			}
			else
			{
				document.getElementById('ok_' + id_champ).innerHTML = "<img src='/imgs/nonchargement.png' alt='champ valide' width='20px' /> " ; 
			}
		}
		else
		{
			//Si on veut verifier la taille
			if(document.getElementById(id_champ).value.length >= taille_condition )
			{
				document.getElementById('ok_' + id_champ).innerHTML = "<img src='/imgs/okchargement.png' alt='champ valide' width='20px' /> " ; 
			}
			else
			{
				document.getElementById('ok_' + id_champ).innerHTML = "<img src='/imgs/nonchargement.png' alt='champ valide' width='20px' /> " ; 
			}
		}
	}
}
// Permet de rendre les blocs de class .bloc_apparait responsvive en fonction de la hauteur du header qui est de 50px
function bloc_apparait_responsive()
{
	// On cherche la hauteur de la fenêtre pour que l'image soit redimensionner en fonction des bords
	var hauteur_body = document.body.clientHeight ; 
	
	// Bloc application resize
	$('.bloc_apparait').css('height' , hauteur_body - 50 + 'px') ; 
}

function footer_apparaitre()
{
	id_bloc_page = 1 ;
	
	voir_bloc_coulissant('#autre_action'); 
	
	document.getElementById('menufooter').style.display ='inline' ;
	contenufooter.style.display = 'none' ;
}
function retour_menu()
{
	$( "#menufooter" ).fadeIn(200);
	contenufooter.style.display = 'none';
}

function supprimer_footer()
{
	document.getElementById('contact_footer').style.display = 'none' ;
	document.getElementById('block_conditions_generales').style.display = 'none' ; 
	document.getElementById('block_partenaire').style.display = 'none' ;
	document.getElementById('bloc_remerciement_footer').style.display = 'none' ;

	$( "#autre_action" ).animate({ left:'-50%' }, 200 , function() {document.getElementById('autre_action').style.display='none' ;});
}


function contact_footer()
{
	largueur_footer = document.getElementById('autre_action').offsetWidth ;
	if(largueur_footer < 1000)
	{
		difference_largueur = 1000 - largueur_footer;
		difference_largueur = difference_largueur / 10 ; 
		document.getElementById('contenu_mail').rows = document.getElementById('contenu_mail').cols = 100 - difference_largueur ; 
		document.getElementById('contenu_mail').rows = document.getElementById('contenu_mail').rows = '8' ; 
	}
	
	menufooter.style.display = 'none'; 
	contenufooter.style.display = 'block' ;
	
	$( "#contact_footer" ).fadeIn(200);
	
	document.getElementById('block_conditions_generales').style.display = 'none' ; 
	document.getElementById('block_partenaire').style.display = 'none' ;
	document.getElementById('bloc_remerciement_footer').style.display = 'none' ;
}

function partenaire_footer()
{
	contenufooter.style.display = 'block' ;
	menufooter.style.display = 'none'; 
 
	document.getElementById('contact_footer').style.display = 'none' ;
	document.getElementById('block_conditions_generales').style.display = 'none' ;
	document.getElementById('bloc_remerciement_footer').style.display = 'none' ;
	$( "#block_partenaire" ).fadeIn(200);;
}

function conditions_generales_footer()
{
	contenufooter.style.display = 'block' ;
	menufooter.style.display = 'none'; 
	document.getElementById('contact_footer').style.display = 'none' ;
	$( "#block_conditions_generales" ).fadeIn(200) ;
	document.getElementById('block_partenaire').style.display = 'none' ;
	document.getElementById('bloc_remerciement_footer').style.display = 'none' ;
}

function remerciement_footer()
{
	contenufooter.style.display = 'block' ;
	menufooter.style.display = 'none'; 
	$( "#bloc_remerciement_footer" ).fadeIn(200) ;
	
	document.getElementById('block_conditions_generales').style.display = 'none' ;
	document.getElementById('contact_footer').style.display = 'none' ;
	document.getElementById('block_partenaire').style.display = 'none' ;
}

function envoi_mail() 
{
	chargement_bloc_action() ; 
	
	var xhr = creation_xhr();
	
	 xhr.open('POST', '/ajax/general/contact_traitement.php');
	 xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	 
	 var prenom_nom_contact_footer = document.getElementById('prenom_nom_contact_footer').value , 
	 mailcontact = document.getElementById('mailcontact').value , 
	 naturemail = document.getElementById('naturemail').value ,
	 choixcontact = document.getElementById('choixcontact').value ,
	 contenu_mail = document.getElementById('contenu_mail').value ;
	 
	 xhr.send('prenom_nom=' + prenom_nom_contact_footer + '&mailcontact=' + mailcontact + '&naturemail=' + naturemail + '&choixcontact=' + choixcontact + '&contenu_mail=' + contenu_mail)
		 
	xhr.onreadystatechange = function()
	{
		if (xhr.readyState == 4 && xhr.status == 200) 
		{
			chargement_bloc_action() ; 
			
			var response_text = xhr.responseText ; 
			
			if(response_text == '') 
			{
				ouverture_alert(alert_basic = '<span>Votre mail à bien été envoyé ! Vous recevrez une réponse dans les prochaines 48h.</span><br />') ;
				$( "#autre_action" ).fadeOut(200);
				
				// Si le mail à bien été envoyé on peut reset le formulaire
				document.getElementById('formulaire_contact_envoi_mail_footer').reset() ; 
			}
			else
			{
				ouverture_alert(alert_basic = response_text) ;
			}
		}
	}
} ; 

function bonmail(mail_check)
{
	var reg = new RegExp('^[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*@[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*[\.]{1}[a-z]{2,6}$', 'i');

	if(reg.test(mail_check))
	{
		document.getElementById('email_check').innerHTML='<img src=\'/imgs/okchargement.png\' width=\'20px\' />';
	}
	else
	{
		document.getElementById('email_check').innerHTML='<img src=\'/imgs/nonchargement.png\' width=\'20px\' />';
	}
}
// Permet d'avoir un apercu d'une image en full screen
function apercu_image(chemin)
{
	ouverture_alert(alert_basic = '<p class="apercu_full_screen"><img style="width:100%" src="'+ chemin + '" alt=""/></p>' , taille_box = '100%' );
}
function faire_apparaitre_title_picto(id_picto)
{
	// Attendre que le window le ferme de lui meme
	setTimeout( function()
	{
		var id_title = document.getElementById('title_' + id_picto);
		
		// Si l'id existe bien
		if(id_title)
		{
			if(id_title.style.display == 'none')
			{
				$(id_title).fadeIn(100) ; 
			}
			else
			{
				$(id_title).fadeOut(100) ; 
			}
		}
	}, 200);
}
function scroll_to(bloc_scroll, cible_scroll)
{
	$(bloc_scroll).scrollTo(cible_scroll, 2000, {queue:true});
}

function chargement_bloc_action()
{
	load_chargement = document.getElementById('bloc_chargement_action') ;
	
	if(load_chargement.style.display == 'none')
	{
		load_chargement.style.display = 'inline-block' ; 
		// On agrandit progressivement sa taille jusqu'à un certain point ou ca bloque
		$(load_chargement).queue(function(){
			$(this).animate({'width':'100%'}, 3000)
			.animate({'width':'100%'}, 500 , function(){
					// On arrete la fonction pour éviter que le 95% se relance
					$(this).stop(true , false) ;
					// La fonction stop peut mettre un peu de temps avant de se lancer vaut mieux temporiser
					setTimeout(function()
					{
						load_chargement.style.display = 'none' ; 
						load_chargement.style.width = '0' ;
					}, 500) ; 
				}
			) ;
		});
		
		$(load_chargement).dequeue();
	}
	// Sinon c'est une fermeture
	else
	{
		// On appel directement le 100% dans le queue 
		$(load_chargement).dequeue() ; 
	}
}

// Pour éviter que les versions anciennes des navigateurs ne prenne en compte la géocalisation n'est pas pris en charge par ie8 et + récent donc c'est parfait
if(typeof(navigator.geolocation) != 'undefined')
{
// Gestion des éléments tactiles 
/*! Hammer.JS - v2.0.4 - 2014-09-28
 * http://hammerjs.github.io/
 *
 * Copyright (c) 2014 Jorik Tangelder;
 * Licensed under the MIT license */
!function(a,b,c,d){"use strict";function e(a,b,c){return setTimeout(k(a,c),b)}function f(a,b,c){return Array.isArray(a)?(g(a,c[b],c),!0):!1}function g(a,b,c){var e;if(a)if(a.forEach)a.forEach(b,c);else if(a.length!==d)for(e=0;e<a.length;)b.call(c,a[e],e,a),e++;else for(e in a)a.hasOwnProperty(e)&&b.call(c,a[e],e,a)}function h(a,b,c){for(var e=Object.keys(b),f=0;f<e.length;)(!c||c&&a[e[f]]===d)&&(a[e[f]]=b[e[f]]),f++;return a}function i(a,b){return h(a,b,!0)}function j(a,b,c){var d,e=b.prototype;d=a.prototype=Object.create(e),d.constructor=a,d._super=e,c&&h(d,c)}function k(a,b){return function(){return a.apply(b,arguments)}}function l(a,b){return typeof a==kb?a.apply(b?b[0]||d:d,b):a}function m(a,b){return a===d?b:a}function n(a,b,c){g(r(b),function(b){a.addEventListener(b,c,!1)})}function o(a,b,c){g(r(b),function(b){a.removeEventListener(b,c,!1)})}function p(a,b){for(;a;){if(a==b)return!0;a=a.parentNode}return!1}function q(a,b){return a.indexOf(b)>-1}function r(a){return a.trim().split(/\s+/g)}function s(a,b,c){if(a.indexOf&&!c)return a.indexOf(b);for(var d=0;d<a.length;){if(c&&a[d][c]==b||!c&&a[d]===b)return d;d++}return-1}function t(a){return Array.prototype.slice.call(a,0)}function u(a,b,c){for(var d=[],e=[],f=0;f<a.length;){var g=b?a[f][b]:a[f];s(e,g)<0&&d.push(a[f]),e[f]=g,f++}return c&&(d=b?d.sort(function(a,c){return a[b]>c[b]}):d.sort()),d}function v(a,b){for(var c,e,f=b[0].toUpperCase()+b.slice(1),g=0;g<ib.length;){if(c=ib[g],e=c?c+f:b,e in a)return e;g++}return d}function w(){return ob++}function x(a){var b=a.ownerDocument;return b.defaultView||b.parentWindow}function y(a,b){var c=this;this.manager=a,this.callback=b,this.element=a.element,this.target=a.options.inputTarget,this.domHandler=function(b){l(a.options.enable,[a])&&c.handler(b)},this.init()}function z(a){var b,c=a.options.inputClass;return new(b=c?c:rb?N:sb?Q:qb?S:M)(a,A)}function A(a,b,c){var d=c.pointers.length,e=c.changedPointers.length,f=b&yb&&d-e===0,g=b&(Ab|Bb)&&d-e===0;c.isFirst=!!f,c.isFinal=!!g,f&&(a.session={}),c.eventType=b,B(a,c),a.emit("hammer.input",c),a.recognize(c),a.session.prevInput=c}function B(a,b){var c=a.session,d=b.pointers,e=d.length;c.firstInput||(c.firstInput=E(b)),e>1&&!c.firstMultiple?c.firstMultiple=E(b):1===e&&(c.firstMultiple=!1);var f=c.firstInput,g=c.firstMultiple,h=g?g.center:f.center,i=b.center=F(d);b.timeStamp=nb(),b.deltaTime=b.timeStamp-f.timeStamp,b.angle=J(h,i),b.distance=I(h,i),C(c,b),b.offsetDirection=H(b.deltaX,b.deltaY),b.scale=g?L(g.pointers,d):1,b.rotation=g?K(g.pointers,d):0,D(c,b);var j=a.element;p(b.srcEvent.target,j)&&(j=b.srcEvent.target),b.target=j}function C(a,b){var c=b.center,d=a.offsetDelta||{},e=a.prevDelta||{},f=a.prevInput||{};(b.eventType===yb||f.eventType===Ab)&&(e=a.prevDelta={x:f.deltaX||0,y:f.deltaY||0},d=a.offsetDelta={x:c.x,y:c.y}),b.deltaX=e.x+(c.x-d.x),b.deltaY=e.y+(c.y-d.y)}function D(a,b){var c,e,f,g,h=a.lastInterval||b,i=b.timeStamp-h.timeStamp;if(b.eventType!=Bb&&(i>xb||h.velocity===d)){var j=h.deltaX-b.deltaX,k=h.deltaY-b.deltaY,l=G(i,j,k);e=l.x,f=l.y,c=mb(l.x)>mb(l.y)?l.x:l.y,g=H(j,k),a.lastInterval=b}else c=h.velocity,e=h.velocityX,f=h.velocityY,g=h.direction;b.velocity=c,b.velocityX=e,b.velocityY=f,b.direction=g}function E(a){for(var b=[],c=0;c<a.pointers.length;)b[c]={clientX:lb(a.pointers[c].clientX),clientY:lb(a.pointers[c].clientY)},c++;return{timeStamp:nb(),pointers:b,center:F(b),deltaX:a.deltaX,deltaY:a.deltaY}}function F(a){var b=a.length;if(1===b)return{x:lb(a[0].clientX),y:lb(a[0].clientY)};for(var c=0,d=0,e=0;b>e;)c+=a[e].clientX,d+=a[e].clientY,e++;return{x:lb(c/b),y:lb(d/b)}}function G(a,b,c){return{x:b/a||0,y:c/a||0}}function H(a,b){return a===b?Cb:mb(a)>=mb(b)?a>0?Db:Eb:b>0?Fb:Gb}function I(a,b,c){c||(c=Kb);var d=b[c[0]]-a[c[0]],e=b[c[1]]-a[c[1]];return Math.sqrt(d*d+e*e)}function J(a,b,c){c||(c=Kb);var d=b[c[0]]-a[c[0]],e=b[c[1]]-a[c[1]];return 180*Math.atan2(e,d)/Math.PI}function K(a,b){return J(b[1],b[0],Lb)-J(a[1],a[0],Lb)}function L(a,b){return I(b[0],b[1],Lb)/I(a[0],a[1],Lb)}function M(){this.evEl=Nb,this.evWin=Ob,this.allow=!0,this.pressed=!1,y.apply(this,arguments)}function N(){this.evEl=Rb,this.evWin=Sb,y.apply(this,arguments),this.store=this.manager.session.pointerEvents=[]}function O(){this.evTarget=Ub,this.evWin=Vb,this.started=!1,y.apply(this,arguments)}function P(a,b){var c=t(a.touches),d=t(a.changedTouches);return b&(Ab|Bb)&&(c=u(c.concat(d),"identifier",!0)),[c,d]}function Q(){this.evTarget=Xb,this.targetIds={},y.apply(this,arguments)}function R(a,b){var c=t(a.touches),d=this.targetIds;if(b&(yb|zb)&&1===c.length)return d[c[0].identifier]=!0,[c,c];var e,f,g=t(a.changedTouches),h=[],i=this.target;if(f=c.filter(function(a){return p(a.target,i)}),b===yb)for(e=0;e<f.length;)d[f[e].identifier]=!0,e++;for(e=0;e<g.length;)d[g[e].identifier]&&h.push(g[e]),b&(Ab|Bb)&&delete d[g[e].identifier],e++;return h.length?[u(f.concat(h),"identifier",!0),h]:void 0}function S(){y.apply(this,arguments);var a=k(this.handler,this);this.touch=new Q(this.manager,a),this.mouse=new M(this.manager,a)}function T(a,b){this.manager=a,this.set(b)}function U(a){if(q(a,bc))return bc;var b=q(a,cc),c=q(a,dc);return b&&c?cc+" "+dc:b||c?b?cc:dc:q(a,ac)?ac:_b}function V(a){this.id=w(),this.manager=null,this.options=i(a||{},this.defaults),this.options.enable=m(this.options.enable,!0),this.state=ec,this.simultaneous={},this.requireFail=[]}function W(a){return a&jc?"cancel":a&hc?"end":a&gc?"move":a&fc?"start":""}function X(a){return a==Gb?"down":a==Fb?"up":a==Db?"left":a==Eb?"right":""}function Y(a,b){var c=b.manager;return c?c.get(a):a}function Z(){V.apply(this,arguments)}function $(){Z.apply(this,arguments),this.pX=null,this.pY=null}function _(){Z.apply(this,arguments)}function ab(){V.apply(this,arguments),this._timer=null,this._input=null}function bb(){Z.apply(this,arguments)}function cb(){Z.apply(this,arguments)}function db(){V.apply(this,arguments),this.pTime=!1,this.pCenter=!1,this._timer=null,this._input=null,this.count=0}function eb(a,b){return b=b||{},b.recognizers=m(b.recognizers,eb.defaults.preset),new fb(a,b)}function fb(a,b){b=b||{},this.options=i(b,eb.defaults),this.options.inputTarget=this.options.inputTarget||a,this.handlers={},this.session={},this.recognizers=[],this.element=a,this.input=z(this),this.touchAction=new T(this,this.options.touchAction),gb(this,!0),g(b.recognizers,function(a){var b=this.add(new a[0](a[1]));a[2]&&b.recognizeWith(a[2]),a[3]&&b.requireFailure(a[3])},this)}function gb(a,b){var c=a.element;g(a.options.cssProps,function(a,d){c.style[v(c.style,d)]=b?a:""})}function hb(a,c){var d=b.createEvent("Event");d.initEvent(a,!0,!0),d.gesture=c,c.target.dispatchEvent(d)}var ib=["","webkit","moz","MS","ms","o"],jb=b.createElement("div"),kb="function",lb=Math.round,mb=Math.abs,nb=Date.now,ob=1,pb=/mobile|tablet|ip(ad|hone|od)|android/i,qb="ontouchstart"in a,rb=v(a,"PointerEvent")!==d,sb=qb&&pb.test(navigator.userAgent),tb="touch",ub="pen",vb="mouse",wb="kinect",xb=25,yb=1,zb=2,Ab=4,Bb=8,Cb=1,Db=2,Eb=4,Fb=8,Gb=16,Hb=Db|Eb,Ib=Fb|Gb,Jb=Hb|Ib,Kb=["x","y"],Lb=["clientX","clientY"];y.prototype={handler:function(){},init:function(){this.evEl&&n(this.element,this.evEl,this.domHandler),this.evTarget&&n(this.target,this.evTarget,this.domHandler),this.evWin&&n(x(this.element),this.evWin,this.domHandler)},destroy:function(){this.evEl&&o(this.element,this.evEl,this.domHandler),this.evTarget&&o(this.target,this.evTarget,this.domHandler),this.evWin&&o(x(this.element),this.evWin,this.domHandler)}};var Mb={mousedown:yb,mousemove:zb,mouseup:Ab},Nb="mousedown",Ob="mousemove mouseup";j(M,y,{handler:function(a){var b=Mb[a.type];b&yb&&0===a.button&&(this.pressed=!0),b&zb&&1!==a.which&&(b=Ab),this.pressed&&this.allow&&(b&Ab&&(this.pressed=!1),this.callback(this.manager,b,{pointers:[a],changedPointers:[a],pointerType:vb,srcEvent:a}))}});var Pb={pointerdown:yb,pointermove:zb,pointerup:Ab,pointercancel:Bb,pointerout:Bb},Qb={2:tb,3:ub,4:vb,5:wb},Rb="pointerdown",Sb="pointermove pointerup pointercancel";a.MSPointerEvent&&(Rb="MSPointerDown",Sb="MSPointerMove MSPointerUp MSPointerCancel"),j(N,y,{handler:function(a){var b=this.store,c=!1,d=a.type.toLowerCase().replace("ms",""),e=Pb[d],f=Qb[a.pointerType]||a.pointerType,g=f==tb,h=s(b,a.pointerId,"pointerId");e&yb&&(0===a.button||g)?0>h&&(b.push(a),h=b.length-1):e&(Ab|Bb)&&(c=!0),0>h||(b[h]=a,this.callback(this.manager,e,{pointers:b,changedPointers:[a],pointerType:f,srcEvent:a}),c&&b.splice(h,1))}});var Tb={touchstart:yb,touchmove:zb,touchend:Ab,touchcancel:Bb},Ub="touchstart",Vb="touchstart touchmove touchend touchcancel";j(O,y,{handler:function(a){var b=Tb[a.type];if(b===yb&&(this.started=!0),this.started){var c=P.call(this,a,b);b&(Ab|Bb)&&c[0].length-c[1].length===0&&(this.started=!1),this.callback(this.manager,b,{pointers:c[0],changedPointers:c[1],pointerType:tb,srcEvent:a})}}});var Wb={touchstart:yb,touchmove:zb,touchend:Ab,touchcancel:Bb},Xb="touchstart touchmove touchend touchcancel";j(Q,y,{handler:function(a){var b=Wb[a.type],c=R.call(this,a,b);c&&this.callback(this.manager,b,{pointers:c[0],changedPointers:c[1],pointerType:tb,srcEvent:a})}}),j(S,y,{handler:function(a,b,c){var d=c.pointerType==tb,e=c.pointerType==vb;if(d)this.mouse.allow=!1;else if(e&&!this.mouse.allow)return;b&(Ab|Bb)&&(this.mouse.allow=!0),this.callback(a,b,c)},destroy:function(){this.touch.destroy(),this.mouse.destroy()}});var Yb=v(jb.style,"touchAction"),Zb=Yb!==d,$b="compute",_b="auto",ac="manipulation",bc="none",cc="pan-x",dc="pan-y";T.prototype={set:function(a){a==$b&&(a=this.compute()),Zb&&(this.manager.element.style[Yb]=a),this.actions=a.toLowerCase().trim()},update:function(){this.set(this.manager.options.touchAction)},compute:function(){var a=[];return g(this.manager.recognizers,function(b){l(b.options.enable,[b])&&(a=a.concat(b.getTouchAction()))}),U(a.join(" "))},preventDefaults:function(a){if(!Zb){var b=a.srcEvent,c=a.offsetDirection;if(this.manager.session.prevented)return void b.preventDefault();var d=this.actions,e=q(d,bc),f=q(d,dc),g=q(d,cc);return e||f&&c&Hb||g&&c&Ib?this.preventSrc(b):void 0}},preventSrc:function(a){this.manager.session.prevented=!0,a.preventDefault()}};var ec=1,fc=2,gc=4,hc=8,ic=hc,jc=16,kc=32;V.prototype={defaults:{},set:function(a){return h(this.options,a),this.manager&&this.manager.touchAction.update(),this},recognizeWith:function(a){if(f(a,"recognizeWith",this))return this;var b=this.simultaneous;return a=Y(a,this),b[a.id]||(b[a.id]=a,a.recognizeWith(this)),this},dropRecognizeWith:function(a){return f(a,"dropRecognizeWith",this)?this:(a=Y(a,this),delete this.simultaneous[a.id],this)},requireFailure:function(a){if(f(a,"requireFailure",this))return this;var b=this.requireFail;return a=Y(a,this),-1===s(b,a)&&(b.push(a),a.requireFailure(this)),this},dropRequireFailure:function(a){if(f(a,"dropRequireFailure",this))return this;a=Y(a,this);var b=s(this.requireFail,a);return b>-1&&this.requireFail.splice(b,1),this},hasRequireFailures:function(){return this.requireFail.length>0},canRecognizeWith:function(a){return!!this.simultaneous[a.id]},emit:function(a){function b(b){c.manager.emit(c.options.event+(b?W(d):""),a)}var c=this,d=this.state;hc>d&&b(!0),b(),d>=hc&&b(!0)},tryEmit:function(a){return this.canEmit()?this.emit(a):void(this.state=kc)},canEmit:function(){for(var a=0;a<this.requireFail.length;){if(!(this.requireFail[a].state&(kc|ec)))return!1;a++}return!0},recognize:function(a){var b=h({},a);return l(this.options.enable,[this,b])?(this.state&(ic|jc|kc)&&(this.state=ec),this.state=this.process(b),void(this.state&(fc|gc|hc|jc)&&this.tryEmit(b))):(this.reset(),void(this.state=kc))},process:function(){},getTouchAction:function(){},reset:function(){}},j(Z,V,{defaults:{pointers:1},attrTest:function(a){var b=this.options.pointers;return 0===b||a.pointers.length===b},process:function(a){var b=this.state,c=a.eventType,d=b&(fc|gc),e=this.attrTest(a);return d&&(c&Bb||!e)?b|jc:d||e?c&Ab?b|hc:b&fc?b|gc:fc:kc}}),j($,Z,{defaults:{event:"pan",threshold:10,pointers:1,direction:Jb},getTouchAction:function(){var a=this.options.direction,b=[];return a&Hb&&b.push(dc),a&Ib&&b.push(cc),b},directionTest:function(a){var b=this.options,c=!0,d=a.distance,e=a.direction,f=a.deltaX,g=a.deltaY;return e&b.direction||(b.direction&Hb?(e=0===f?Cb:0>f?Db:Eb,c=f!=this.pX,d=Math.abs(a.deltaX)):(e=0===g?Cb:0>g?Fb:Gb,c=g!=this.pY,d=Math.abs(a.deltaY))),a.direction=e,c&&d>b.threshold&&e&b.direction},attrTest:function(a){return Z.prototype.attrTest.call(this,a)&&(this.state&fc||!(this.state&fc)&&this.directionTest(a))},emit:function(a){this.pX=a.deltaX,this.pY=a.deltaY;var b=X(a.direction);b&&this.manager.emit(this.options.event+b,a),this._super.emit.call(this,a)}}),j(_,Z,{defaults:{event:"pinch",threshold:0,pointers:2},getTouchAction:function(){return[bc]},attrTest:function(a){return this._super.attrTest.call(this,a)&&(Math.abs(a.scale-1)>this.options.threshold||this.state&fc)},emit:function(a){if(this._super.emit.call(this,a),1!==a.scale){var b=a.scale<1?"in":"out";this.manager.emit(this.options.event+b,a)}}}),j(ab,V,{defaults:{event:"press",pointers:1,time:500,threshold:5},getTouchAction:function(){return[_b]},process:function(a){var b=this.options,c=a.pointers.length===b.pointers,d=a.distance<b.threshold,f=a.deltaTime>b.time;if(this._input=a,!d||!c||a.eventType&(Ab|Bb)&&!f)this.reset();else if(a.eventType&yb)this.reset(),this._timer=e(function(){this.state=ic,this.tryEmit()},b.time,this);else if(a.eventType&Ab)return ic;return kc},reset:function(){clearTimeout(this._timer)},emit:function(a){this.state===ic&&(a&&a.eventType&Ab?this.manager.emit(this.options.event+"up",a):(this._input.timeStamp=nb(),this.manager.emit(this.options.event,this._input)))}}),j(bb,Z,{defaults:{event:"rotate",threshold:0,pointers:2},getTouchAction:function(){return[bc]},attrTest:function(a){return this._super.attrTest.call(this,a)&&(Math.abs(a.rotation)>this.options.threshold||this.state&fc)}}),j(cb,Z,{defaults:{event:"swipe",threshold:10,velocity:.65,direction:Hb|Ib,pointers:1},getTouchAction:function(){return $.prototype.getTouchAction.call(this)},attrTest:function(a){var b,c=this.options.direction;return c&(Hb|Ib)?b=a.velocity:c&Hb?b=a.velocityX:c&Ib&&(b=a.velocityY),this._super.attrTest.call(this,a)&&c&a.direction&&a.distance>this.options.threshold&&mb(b)>this.options.velocity&&a.eventType&Ab},emit:function(a){var b=X(a.direction);b&&this.manager.emit(this.options.event+b,a),this.manager.emit(this.options.event,a)}}),j(db,V,{defaults:{event:"tap",pointers:1,taps:1,interval:300,time:250,threshold:2,posThreshold:10},getTouchAction:function(){return[ac]},process:function(a){var b=this.options,c=a.pointers.length===b.pointers,d=a.distance<b.threshold,f=a.deltaTime<b.time;if(this.reset(),a.eventType&yb&&0===this.count)return this.failTimeout();if(d&&f&&c){if(a.eventType!=Ab)return this.failTimeout();var g=this.pTime?a.timeStamp-this.pTime<b.interval:!0,h=!this.pCenter||I(this.pCenter,a.center)<b.posThreshold;this.pTime=a.timeStamp,this.pCenter=a.center,h&&g?this.count+=1:this.count=1,this._input=a;var i=this.count%b.taps;if(0===i)return this.hasRequireFailures()?(this._timer=e(function(){this.state=ic,this.tryEmit()},b.interval,this),fc):ic}return kc},failTimeout:function(){return this._timer=e(function(){this.state=kc},this.options.interval,this),kc},reset:function(){clearTimeout(this._timer)},emit:function(){this.state==ic&&(this._input.tapCount=this.count,this.manager.emit(this.options.event,this._input))}}),eb.VERSION="2.0.4",eb.defaults={domEvents:!1,touchAction:$b,enable:!0,inputTarget:null,inputClass:null,preset:[[bb,{enable:!1}],[_,{enable:!1},["rotate"]],[cb,{direction:Hb}],[$,{direction:Hb},["swipe"]],[db],[db,{event:"doubletap",taps:2},["tap"]],[ab]],cssProps:{userSelect:"none",touchSelect:"none",touchCallout:"none",contentZooming:"none",userDrag:"none",tapHighlightColor:"rgba(0,0,0,0)"}};var lc=1,mc=2;fb.prototype={set:function(a){return h(this.options,a),a.touchAction&&this.touchAction.update(),a.inputTarget&&(this.input.destroy(),this.input.target=a.inputTarget,this.input.init()),this},stop:function(a){this.session.stopped=a?mc:lc},recognize:function(a){var b=this.session;if(!b.stopped){this.touchAction.preventDefaults(a);var c,d=this.recognizers,e=b.curRecognizer;(!e||e&&e.state&ic)&&(e=b.curRecognizer=null);for(var f=0;f<d.length;)c=d[f],b.stopped===mc||e&&c!=e&&!c.canRecognizeWith(e)?c.reset():c.recognize(a),!e&&c.state&(fc|gc|hc)&&(e=b.curRecognizer=c),f++}},get:function(a){if(a instanceof V)return a;for(var b=this.recognizers,c=0;c<b.length;c++)if(b[c].options.event==a)return b[c];return null},add:function(a){if(f(a,"add",this))return this;var b=this.get(a.options.event);return b&&this.remove(b),this.recognizers.push(a),a.manager=this,this.touchAction.update(),a},remove:function(a){if(f(a,"remove",this))return this;var b=this.recognizers;return a=this.get(a),b.splice(s(b,a),1),this.touchAction.update(),this},on:function(a,b){var c=this.handlers;return g(r(a),function(a){c[a]=c[a]||[],c[a].push(b)}),this},off:function(a,b){var c=this.handlers;return g(r(a),function(a){b?c[a].splice(s(c[a],b),1):delete c[a]}),this},emit:function(a,b){this.options.domEvents&&hb(a,b);var c=this.handlers[a]&&this.handlers[a].slice();if(c&&c.length){b.type=a,b.preventDefault=function(){b.srcEvent.preventDefault()};for(var d=0;d<c.length;)c[d](b),d++}},destroy:function(){this.element&&gb(this,!1),this.handlers={},this.session={},this.input.destroy(),this.element=null}},h(eb,{INPUT_START:yb,INPUT_MOVE:zb,INPUT_END:Ab,INPUT_CANCEL:Bb,STATE_POSSIBLE:ec,STATE_BEGAN:fc,STATE_CHANGED:gc,STATE_ENDED:hc,STATE_RECOGNIZED:ic,STATE_CANCELLED:jc,STATE_FAILED:kc,DIRECTION_NONE:Cb,DIRECTION_LEFT:Db,DIRECTION_RIGHT:Eb,DIRECTION_UP:Fb,DIRECTION_DOWN:Gb,DIRECTION_HORIZONTAL:Hb,DIRECTION_VERTICAL:Ib,DIRECTION_ALL:Jb,Manager:fb,Input:y,TouchAction:T,TouchInput:Q,MouseInput:M,PointerEventInput:N,TouchMouseInput:S,SingleTouchInput:O,Recognizer:V,AttrRecognizer:Z,Tap:db,Pan:$,Swipe:cb,Pinch:_,Rotate:bb,Press:ab,on:n,off:o,each:g,merge:i,extend:h,inherit:j,bindFn:k,prefixed:v}),typeof define==kb&&define.amd?define(function(){return eb}):"undefined"!=typeof module&&module.exports?module.exports=eb:a[c]=eb}(window,document,"Hammer");
}

function rotate_element(element_a_tourner, latence, vitesse) 
{
	// Si l'élément est visible on le fait tourner 
	function rotate_element_boucle(orientation_actuel)
	{
		// On arrete la boucle dès que le picto n'est + visible
		if(element_a_tourner.style.display != 'none')
		{
			element_a_tourner.style.transform = 'rotate(' + orientation_actuel + 'deg)' ; 
			
			var orientation_actuel = orientation_actuel + vitesse ; 
			
			// On rappel la fonction toute les 20ms seconde
			setTimeout(function()
			{
				rotate_element_boucle(orientation_actuel)
			} , latence) ;
		}
	}
	
	rotate_element_boucle(0) ; 
}

function trim (myString)
{
	return myString.replace(/^\s+/g,'').replace(/\s+$/g,'')
} 

// Sélection des horraires
function select_jour_semaine(jour_de_la_semaine , libelle_id , select_option)
{
	// On remplace les espaces 
	var jour_de_la_semaine = trim(jour_de_la_semaine) ; 
	
	var id_element_jour = jour_de_la_semaine + '' + libelle_id ; 
	var id_element_select = 'select' + jour_de_la_semaine + '' + libelle_id ; 
	
	if(document.getElementById(id_element_jour).className == 'bulle_jour_semaine_off')
	{
		document.getElementById(id_element_jour).className = 'bulle_jour_semaine_on' ; 
		if(typeof(select_option) != 'undefined' && select_option == 1)
		{
			$('#' + id_element_select).fadeIn() ; 
		}
	}
	else
	{
		document.getElementById(id_element_jour).className = 'bulle_jour_semaine_off' ; 
		
		if(typeof(select_option) != 'undefined' && select_option == 1)
		{
			$('#' + id_element_select).fadeOut() ; 
		}
	}
}

// admin + compte_resto

function affichage_contenu_contact_pro(id_contact_pro)
{
	if(document.getElementById('bloc_infos_complementaire_contact_pro' + id_contact_pro).style.display == 'none')
	{
		$('#bloc_infos_complementaire_contact_pro' + id_contact_pro).fadeIn() ; 
		$('#bloc_actions_contact_pro' + id_contact_pro).fadeIn() ; 
	}
	else
	{
		$('#bloc_infos_complementaire_contact_pro' + id_contact_pro).fadeOut() ; 
		$('#bloc_actions_contact_pro' + id_contact_pro).fadeOut() ; 
	}
}

function refermer_bandeau_promo(id_bloc_promo)
{
	if(document.getElementById(id_bloc_promo).style.display != 'none')
	{
		// Si le bloc n'est pas sur le coté mais en responsive
		if(document.getElementById(id_bloc_promo).style.position == 'static')
		{
			$('#' + id_bloc_promo).fadeOut() ; 
		}
		else
		{
			$('#' + id_bloc_promo).animate({top : '0px' , left : '-350px'} , 400, function()
			{
				document.getElementById(id_bloc_promo).style.display = 'none' ; 
			}) ;
		}
	}
	else
	{
		// Si le bloc n'est pas sur le coté mais en responsive
		if(document.getElementById(id_bloc_promo).style.position == 'static')
		{
			$('#' + id_bloc_promo).fadeIn() ; 
		}
		else
		{
			document.getElementById(id_bloc_promo).style.display = 'block' ; 
			$('#' + id_bloc_promo).animate({top : '24px' , left : '-87px'} , 400) ;
		}
	}
}

function display_formulaire_recherche_approfondie(etape , valeur)
{
	if(etape == 'recherche_par')
	{
		// On s'assure que tout les autres groupe d'input sont fermé 
		$('.fieldset_input_display').fadeOut(300 , function()
		{
			// On reset le formulaire si jamais il ne l'est pas 
			document.getElementById('recherche_approfondie_annuaire').reset() ;
		
			// On remet la valeur car le reset à tout enlever
			document.getElementById('select_recherche_par').value = valeur ;
		
			if(valeur == 'tous_les_resto')
			{
				faire_apparaitre_ville_choix_annuaire() ; 
			}
			else if(valeur == 'resto_type')
			{
				$('#fieldset_type_resto_annuaire').fadeIn() ;
			}
			else if(valeur == 'resto_plusieurs_type')
			{
				$('#fieldset_tout_type_resto_annuaire').fadeIn() ;
			}
			else if(valeur == 'nom_resto')
			{
				$('#fieldset_nom_resto_annuaire').fadeIn() ;
			}		
		}) ; 
	}
}

function faire_apparaitre_ville_choix_annuaire(select_appel)
{
	//On ne demande pas la ville pour le resto unique en ligne sinon ce serait trop compliqé : allez chercher en ajax les restaurants disponible
	if(typeof(select_appel) != 'undefined')
	{
		// Si on ne doit pas afficher le tri par réduciton
		if(select_appel == 'nom_resto')
		{
			document.getElementById('bloc_conteneur_option_reduction').style.display = 'none' ;
			document.getElementById('bloc_conteneur_option_livraison').style.display = 'none' ;
		}
		else
		{
			document.getElementById('bloc_conteneur_option_reduction').style.display = 'block' ;
			document.getElementById('bloc_conteneur_option_livraison').style.display = 'block' ;
		}
	}
	else
	{
		// On affiche les options normal 
		document.getElementById('bloc_conteneur_option_reduction').style.display = 'block' ;
		document.getElementById('bloc_conteneur_option_livraison').style.display = 'block' ;
	}
	
	if(document.getElementById('fieldset_ville_annuaire').style.display == 'none')
	{
		$('#fieldset_ville_annuaire').fadeIn() ;
	}
}

function faire_apparaitre_option_complementaire()
{
	$('#fieldset_option_complementaire_annuaire').fadeIn();
}

function rechercher_appronfondie_annuaire(type)
{
	// On commande l'action
	chargement_bloc_action() ; 
	
	// On fait une recherche donc on remet le titre de l'annuaire si ce n'est pas le bon
	document.getElementById('titre_annuaire_dynamique').innerHTML = 'Annuaire des restos en ligne' ;  
	
	var recherche_par = document.getElementById('select_recherche_par').value ,
	ville = document.getElementById('select_recherche_ville').value ,
	resto_type = document.getElementById('select_type_resto').value ,
	resto_plusieur_type = '' , 
	nom_resto = document.getElementById('select_nom_resto').value ,
	voir_reduction = '' ,
	voir_attribus = '' ;

	if(recherche_par == 'resto_plusieurs_type')
	{
		if(document.getElementsByClassName)
		{
			var checkbox_type_resto_multiple = document.getElementsByClassName('checkbox_type_resto_multiple') ; 
		}
		else
		{
			var checkbox_type_resto_multiple = document.querySelector('.checkbox_type_resto_multiple')
		}
		
		// On va compter le nombre de checkbox en tout
		var nombre_checkbox = checkbox_type_resto_multiple.length ;
		
		// On va faire une boucle
		for(i=0 ; i < nombre_checkbox ; i++)
		{
			// Si le checkbox est checke on l'ajoute dans la liste des type resto
			if(checkbox_type_resto_multiple[i].checked)
			{
				if(resto_plusieur_type == '')
				{
					resto_plusieur_type = ' type_resto = "' + checkbox_type_resto_multiple[i].name + '"' ; 
				}
				else
				{
					resto_plusieur_type = resto_plusieur_type + ' OR type_resto = "' + checkbox_type_resto_multiple[i].name + '"'  ;
				}
			}
		}
	}
	
	if(document.getElementById('checkbox_voir_que_reduction').checked && document.getElementById('bloc_conteneur_option_reduction').style.display != 'none')
	{
		voir_reduction = 1 ; 
	}
	if(document.getElementById('checkbox_voir_que_livraison').checked && document.getElementById('bloc_conteneur_option_livraison').style.display != 'none')
	{
		voir_attribus = 'livraison' ;
	}
	
	//On va faire la recherche appronfondie et on va transmettre tout ce qu'il faut transmettre par ajax
	var xhr = creation_xhr();
		
	xhr.open ('POST', '/ajax/annuaire/recherche_approfondie.php');
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send('recherche_par=' + recherche_par + '&ville=' + ville + '&resto_type=' + resto_type + '&resto_plusieur_type=' + resto_plusieur_type + '&nom_resto=' + nom_resto + '&voir_reduction=' + voir_reduction + '&voir_attribus=' + voir_attribus);
	
	//Récupération du rapport de l'envoi
	xhr.onreadystatechange = function() 
	{
		//Requête envoyé ( == 4 ) et tout à bien été reçu ( == 200 ) 
		if (xhr.readyState == 4 && xhr.status == 200)
		{
			if(xhr.responseText != 'erreur')
			{
				// La présence d'un résultat
				if(xhr.responseText != '')
				{
					document.getElementById('resultat_recherche_annuaire').innerHTML = xhr.responseText ;
					
					document.getElementById('nombre_resultat_annnuaire').innerHTML = document.getElementById('input_nombre_resultat_annuaire').value ;
					
					$('body').scrollTo('#nombre_resultat_annnuaire', 2000, {queue:true});
					
					// On termine l'action
					chargement_bloc_action() ;
				}
				else
				{
					ouverture_alert(alert_basic = 'Oups : 0 restaurants trouvés pour les critères de votre recherche. DMF remplit au mieux tous les jours sa base de donnée pour vous satisfaire !') ;
				}
			}
			else
			{
				ouverture_alert(retour_erreur_formulaire = xhr.responseText) ;
			}
		}
	}
}

// Permet d'avoir les propriétés css global avec currentStyle
function getvalueCSS( element, styleProp )
{ 
	if (element.currentStyle )
	{ 
		var result = element.currentStyle[styleProp]; 
	} 
	else if(window.getComputedStyle){ 
		var result = document.defaultView.getComputedStyle(element,null)[styleProp]; 
	} 
	return result; 
} 

function display_bloc_upload(methode , calback)
{
	// On va checker la comptabilité 
	checkCompatibilityFormData(methode) ; 
	
	var id_bloc_uplaod = document.getElementById('bloc_upload' + methode) ;
	
	if(id_bloc_uplaod.style.display == 'none')
	{
		// On fait apparaitre
		id_bloc_uplaod.style.display = 'block' ; 
		
		// On va voir si c'est sur mobile ou pas
		var position = getvalueCSS(id_bloc_uplaod , 'position') ; 
		
		// Si c'est différent de static c'est que c'est pas sur mobile
		if(position != 'static')
		{
			// On le fait glisser vers le bas avec une opacity de base
			$(id_bloc_uplaod).animate({top : '50px' , opacity : '0.3'} , 500 , function()
			{
				// On fait apparaitre tout totalement
				$(id_bloc_uplaod).animate({opacity : '1'} , 300, function()
				{
					if(typeof(calback) != 'undefined')
					{
						calback.call() ; 
					}
				})  ;
			}) ;
		}
		else
		{
			$(id_bloc_uplaod).animate({opacity : '1'}, function()
			{
				if(typeof(calback) != 'undefined')
				{
					calback.call() ; 
				}
			}); 
		}
	}
}

// Si le type de l'object FormData n'est pas définie (normalement function) alors il n'existe pas !
function checkCompatibilityFormData(methode)
{
	if(typeof(FormData) == 'undefined')
	{
		document.getElementById('navigateur_recent' + methode).style.display='none' ;
		// On charge le téléchargement en iframe = pas de barre de chargement + sintillement dut au rafraichissement !
		document.getElementById('navigateur_ancien' + methode).innerHTML = '<iframe src="/ajax/compte_resto/iframe_upload.php?methode=' + methode + '" style="height:150px; width:300px; border:0px" frameborder="0" scrolling="no" border="0" ></iframe>' ; 
	}
	// Si FormData existe parfait, on peut mettre notre site en place
}