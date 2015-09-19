// Inititation de l'id_bloc_page 1 qui correspond au premier bloc doit TOUJOURS rester en variable global
id_bloc_page = 1 ; 

if(typeof(addEventListener) != 'undefined')
{
	// Une fois que tout est chargé
	window.addEventListener('load' , ActionAlancerChargement , false); 
}
else
{
	window.attachEvent('load' , ActionAlancerChargement , false);
}
function ActionAlancerChargement()
{
	// mettre la fenetre tout en haut pour éviter que le rafraichissement ne perturbe le script
	$('body').scrollTo(0, 1000, {queue:true});
}

// Fonction de scroll
function scroll_page(direction)
{

	// On met a zero puisqu'on est au debut de la page
	if(id_bloc_page == 1)
	{
		var calcul_hauteur = 0 ; 
	}
	else if(id_bloc_page == 5)
	{
		// quand on veut allez en bas alors que on est deja a la fin c'est pas possible ! 
		if(direction == 'en_bas')
		{}
		else
		{
			var laisser_passer = 1 ;
		}	
	}
	// On cherche la position à atteindre en fonction du bloc ( la taille total du bloc height + padding + true= les margins ) + 50 pour la taille du header -1 pour le bloc_page_1
	else
	{
		var calcul_hauteur = document.getElementById('bloc_page_' + id_bloc_page).offsetTop  ;  
	}
	// On compare : si la position du scroll est atteinte on continu sinon on ne fait rien dutout || ON laisse passer si il y a une différence de max 2 qui peut être noté quand on zoom la fenetre genre différence de 0.1545154
	if($(window).scrollTop() == calcul_hauteur || laisser_passer == 1 || calcul_hauteur - $(window).scrollTop() <= 2 && calcul_hauteur - $(window).scrollTop() >= 0)
	{
		// Si la direction est en bas ( fleche du bas , scroll en bas )
		if(direction == 'en_bas')
		{
			// On passe a l'id suivant ( on ne met pas avant car cela permet de tester sans changer l'id si c'est faux)
			id_bloc_page++ ;
			
			// En cas de scroll alors que le dernier élément à été atteint on a pas besoin de scroll !
			if(id_bloc_page > 5)
			{
				var stop_fonction = 0 ;
				// Permet d'éviter les beugs
				id_bloc_page = 5 ;
			}
			
			cible_scroll = '#bloc_page_' + id_bloc_page ;
		}
		// SI a l'inverse on scroll ou tappe flèche en haut
		else if(direction == 'en_haut')
		{
			// On passe a l'id dans dessous (idem que pour direction en bas)
			id_bloc_page-- ;
			//Idem
			if(id_bloc_page == 1)
			{
				// On revient au tout debut
				cible_scroll = 0 ; 
			}
			// Si c'est plus petit que un c'est que la personne est au debut ca ne sert a rien de continuer ! 
			else if(id_bloc_page < 1 )
			{
				var stop_fonction = 1 ;
				// Permet d'éviter les beugs
				id_bloc_page = 1 ;
			}
			else
			{
				var stop_fonction = 0 ;
				cible_scroll = '#bloc_page_' + id_bloc_page ; 
			}
			if(laisser_passer == 1)
			{
				id_bloc_page = 4 ;
				cible_scroll = '#bloc_page_4' ; 
			}
		}
		
		// On execute pas la fonction dans le cas si on est deja au debut ou a la fin
		if(stop_fonction == 1)
		{}
		else
		{
			ScrollPageCible(cible_scroll) ;
			
			// Couleurs des puces
			$('.puce_menu').css('background-color' , "white") ;
			$('.puce_menu').css('color' , "#333") ;
	
			document.getElementById('puce_' + id_bloc_page).style.backgroundColor = '#db302d' ; 
			document.getElementById('puce_' + id_bloc_page).style.color = 'white' ; 
		}

	}
}
// Fonction de traitement de l'évènement keydown
function traitement(evenement)
{			
	//on teste si le code correspond au code de la flêche du bas
	if(evenement.which == 40)
	{
		scroll_page(direction = 'en_bas');
	}
	// On test si le code correspond a la flêche du haut
	if(evenement.which == 38)
	{
		scroll_page(direction = 'en_haut');
	}
}

//fonction clique sur les boutons
function button_scroll_page(cible_scroll)
{
	$('.puce_menu').css('background-color' , "white") ;
	$('.puce_menu').css('color' , "#333") ;
	
	document.getElementById('puce_' + cible_scroll).style.backgroundColor = '#db302d' ; 
	document.getElementById('puce_' + cible_scroll).style.color = 'white' ; 
	
	// permet de mettre l'id en ordre pour savoir sur quelle bloque on se trouve 
	id_bloc_page = cible_scroll ;
	
	//La cible du scroll doit être 0 pour revenir tout en haut pour le cas du premier bloc page 1
	if(cible_scroll == 1)
	{
		cible_scroll = 0 ; 
	}
	
	// On fait scroller la page
	ScrollPageCible('#bloc_page_' + cible_scroll) ;
}

function ScrollPageCible(cible)
{
	// Execution du scroll to en fonction de l'id pour allez au bloc voulue. 1 argument : l'élément, 2 eme : temps d'excution , 3 eme : tableau d'option, pour un callback utiliser onAfter ex : {queue:true , onAfter:function(){mafonction}}
	$('body').scrollTo(cible, 2000, {queue:true , onAfter:function(){
		// On fait disparaitre tout les liens
		$('.donnemoifaim_button_index').css('display' , 'none') ;
		// On fait apparaitre le lien de chaque bloc
		$('#lien_fonctionnalite_index_' + id_bloc_page).fadeIn() ; 
	}});
}

// Lorsque l'on appuie sur une touche // Mis a la fin pour éviter que le loadScript soit par encore chargé
function ActionUserUI()
{
	// Permet de mettre l'action au touche 
	$(document).keydown(traitement);
	
	// Enlever la possibilité d'appuyer sur le button de la molette souris
	$(document).mousedown(function(event){
		if(event.which == 2)
		{
			return false;
		}
	}) ; 
	
	// Permet d'attribuer l'évènement à la roulette de la souris compatible tout navigateur
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
	
	function RouletteEventAction(event) 
	{
		var event = window.event || event ; 
		// On met le -event.detail pour mozilla car il est inversé de sens par rapport au autre
		var deplacement_roulette = event.wheelDelta || -event.detail;
		
		if (deplacement_roulette <= 0)
		{
			scroll_page('en_bas');
		}
		else
		{
			scroll_page('en_haut');
		}
	}
	
	var hammerTutoSlice = new Hammer(document.getElementById('main'));
					
	hammerTutoSlice.get('swipe').set({ direction: Hammer.DIRECTION_VERTICAL });

	hammerTutoSlice.on('swipe', function(ev) {
		
		// Lorsque c'est vers le haut
		if(ev.direction == 16)
		{
			scroll_page('en_haut');
		}
		// Lorsque c'est vers le bas
		else if(ev.direction == 8)
		{
			scroll_page(direction = 'en_bas');
		}
	});

}

// Permet de charger le scroll to après jquery 
loadScriptAfterJquery('ActionUserUI') ; 