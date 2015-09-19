// Verification du login
function verif_login_formulaire()
{
	var xhr = creation_xhr() ; 
	
	var login = document.getElementById('loginformulaire').value; 
		
	xhr.open ('POST', 'ajax/compte_resto/veriflogin.php');
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send('loginformulaire=' + login ) ; 
			 
	xhr.onreadystatechange = function()
	{
		if (xhr.readyState == 4 && xhr.status == 200)
		{
			var texte = xhr.responseText;  
			document.getElementById('oklogin').innerHTML = texte; 
		}
	}
}
function envoi_formulaire_compte(modif_formulaire)
{
	// Lancement du chargement
	chargement_bloc_action() ; 
	
	// On met le loading du compte
	document.getElementById('loading_creation_compte').style.display = 'inline' ;
	
	// Convertir une adresse en code lattitude,longitude on récupère l'adresse du resto écrite
	geocoder = new google.maps.Geocoder();
	
	// Récupération de l'adresse
	var adressresto = document.getElementById('adressresto').value,
	ville = document.getElementById('ville').value,
	mdp = document.getElementById('mdpformulaire').value, 
	verif_mdp = document.getElementById('verif_mdpformulaire').value,
	nomresto = document.getElementById('nomresto_inscription').value, 
	type_resto = document.getElementById('type_resto').value,
	telephone = document.getElementById('telephone').value,
	site_internet = document.getElementById('site_internet').value,
	mail = document.getElementById('mail_formulaire').value ; 
	
	if(document.getElementById('token_modif_compte'))
	{
		var token = document.getElementById('token_modif_compte').value ; 
	}
	
	// Si on est sur le compte de modification
	if(typeof(modif_formulaire) != 'undefined')
	{}
	else
	{
		// On bloque le formulaire d'ajout de compte pour éviter d'en envoyer plein ! 
		var formulaire_enregistrement = document.getElementById('formulaire_enregistrement') ; 
		
		formulaire_enregistrement.onsubmit = function()
		{
			return false;
		}
		
		var login = document.getElementById('loginformulaire').value;
	}
	
	// On récupère l'adresse
	var adresse_text = adressresto + ' ' +  ville ;
	
	// Si le text est remplie on le géencode
	if(adresse_text != '')
	{
		geocoder.geocode({ 'address': adresse_text }, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) 
			{
				coordonnees_latitude = results[0].geometry.location.lat() ;
				coordonnees_longitude = results[0].geometry.location.lng()  ;
				
				var adresse_postal = results[0].formatted_address; 
				
					// Si les coordonnées existe bien 
				if(typeof(coordonnees_latitude) != 'undefined' && typeof(coordonnees_longitude) != 'undefined')
				{}
				else
				{
					// Si ca fonctionne pas on met a un endroit du globe supérieur a ce qui existe pour le resortir en dernier
					coordonnees_latitude = 'erreur' ;
					coordonnees_longitude = 'erreur' ;
				}
				
				var xhr = creation_xhr(); 
				
				xhr.open ('POST', '/ajax/compte_resto/creacompte.php');
				xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				
				// Si c'est la modification
				if(typeof(modif_formulaire) != 'undefined')
				{
					xhr.send('type_formulaire=modif&mdpformulaire=' + mdp + '&nomresto=' + nomresto + '&adressresto=' + adressresto + '&ville=' + ville + '&mail=' + mail +  '&type_resto=' + type_resto + '&verif_mdp=' + verif_mdp + '&telephone=' + telephone + '&site_internet=' + site_internet + '&adresse_postal=' + adresse_postal + '&coordonnees_latitude=' + coordonnees_latitude + '&coordonnees_longitude=' + coordonnees_longitude + '&token=' + token ) ;
				}
				// Sinon
				else
				{
					xhr.send('loginformulaire=' + login + '&mdpformulaire=' + mdp + '&nomresto=' + nomresto + '&adressresto=' + adressresto + '&ville=' + ville + '&mail=' + mail + '&type_resto=' + type_resto + '&verif_mdp=' + verif_mdp + '&telephone=' + telephone + '&site_internet=' + site_internet  + '&adresse_postal=' + adresse_postal + '&coordonnees_latitude=' + coordonnees_latitude + '&coordonnees_longitude=' + coordonnees_longitude ) ;
				}

				xhr.onreadystatechange = function() 
				{
					if (xhr.readyState == 4 && xhr.status == 200)
					{
						// Le chargement est terminé 
						chargement_bloc_action() ; 
						
						var tableau_json = JSON.parse(xhr.responseText);

						if(tableau_json['erreur'] == 0)
						{	
							
							// Si ce n'est pas la modification
							if(typeof(modif_formulaire) == 'undefined')
							{
								// Ouverture des informations complémentaires
								etape_info_complementaire_resto() ;
								
								// On met un token dans token_compte_juste_cree pour pouvoir le récupérer et modifier les attribus du compte
								document.getElementById('token_compte_juste_cree').value = tableau_json['token_compte_juste_cree'] ; 
								
								// On rentre dans le formulaire de connection les identifiants pour pas avoir à les remettre 
								document.getElementById('login').value = login ; 
								document.getElementById('mdp').value = mdp ;
								
								ouverture_alert(alert_basic = '<span>Votre compte resto a bien été créé. Vous pouvez à présent remplir les données complémentaires.</span><br />') ;
							}
							else
							{
								ouverture_alert(alert_basic = '<span>Modification de vos paramètres bien effectuée.</span><br />') ;
								
								setTimeout(function(){window.location.reload();} , 1000) ; 
							}
						}
						else
						{
							// On ouvre notre alert des erreurs de formulaire
							ouverture_alert(retour_erreur_formulaire = tableau_json['erreur']) ; 
						}
						
						// si ça existe on ne sait jamais avec tout les includes
						if(document.getElementById('loading_creation_compte'))
						{
							// On enleve le chargement
							document.getElementById('loading_creation_compte').style.display = 'none' ;
						}
						
						// si ça existe on ne sait jamais avec tout les includes
						if(formulaire_enregistrement)
						{
							// On remet l'évènement onsubmit sur le button du compte 
							formulaire_enregistrement.onsubmit = function()
							{
								envoi_formulaire_compte(); 
								return false;
							}
						}
					}	
				}
			}
			else
			{
				ouverture_alert(retour_erreur_formulaire = '<span class="erreur"> L\'adresse resto semble éronnée, vérifiez que votre adresse soit une adresse valide :<br />- adresse resto à vérifier<br />- Ville resto à vérifier<br /><br /> Si le problème persiste, contactez-nous.</span><br />') ; 
			}
		});
	}
}
function etape_info_complementaire_resto()
{	
	display_bloc_upload('facade_resto') ; 
	
	$('#formulaire_enregistrement').fadeOut(300, function()
	{
		$('#bloc_ajout_facade_complementaire').fadeIn(300) ; 
	});
}
function infos_complementaire_plus_tard(statut)
{
	// Si le status est suivant, on passe juste au suivant 
	if(statut == 'suivant')
	{
		voir_cache_facade_resto() ;
	}
	else if(statut == 'suivant_contact_pro')
	{
		voir_cache_contact_pro() ; 
	}
	else if(statut == 'termine')
	{
		cache_bloc_coulissant ('#enregistrement') ; 
		ouverture_alert(alert_basic = 'Félicitations, votre compte resto est opérationnel ! Vous pourrez ajouter ou modifier vos informations complémentaires dans les paramètres de votre compte.');
	}
}
function connection_compte()
{
	chargement_bloc_action() ; 
	
	var xhr = creation_xhr();
	
	var login = document.getElementById('login').value; 
	var mdp = document.getElementById('mdp').value; 

	xhr.open ('POST', 'ajax/compte_resto/connectionvalide.php');
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send('login=' + login + '&mdp=' + mdp) ; 

	xhr.onreadystatechange = function()
	{
		if (xhr.readyState == 4 && xhr.status == 200) 
		{
			chargement_bloc_action() ; 
			
			if(xhr.responseText == '' )
			{
				window.location.href="/compte-resto/ajout-de-plat.html"; 
			}
			else 
			{
				ouverture_alert(alert_basic = xhr.responseText );
			}
		}
	}
};
function voir_bloc_modif_resto(bloc_a_ouvrir)
{
	if(bloc_a_ouvrir == 'bloc_ajout_facade_complementaire')
	{
		display_bloc_upload('facade_resto') ;
	}
	
	// On masque tout les bloques
	$('.form_modif_compte_resto').css('display' , 'none') ; 
	// On fait apparaitre le bloc désiré
	document.getElementById(bloc_a_ouvrir).style.display = 'block' ; 
	
	// On fait apparaitre le bloc en question
	voir_bloc_coulissant('#bloc_modif_compte_resto') ;
}
function envoyer_mdp_oublier()
{
	chargement_bloc_action() ; 
	
	var xhr = creation_xhr();

	var envoi_mail = document.getElementById('oubli_adresse_mail_resto').value; 
	
	xhr.open ('POST', 'ajax/compte_resto/envoi_mdp_compte_resto.php');
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send('envoimail=' + envoi_mail ) ; 

	xhr.onreadystatechange = function()
	{
		if (xhr.readyState == 4 && xhr.status == 200) 
		{
			chargement_bloc_action() ; 
			
			var response_text = xhr.responseText ; 
			// Si tout c'est bien passé on affiche un alert et on ferme la fenetre
			if(response_text == '')
			{
				ouverture_alert(alert_basic = '<span>Un mail vient de vous être envoyé à l\'adresse indiquée.</span><br />') ;
				cache_bloc_coulissant ('#bloc_mdp_compte_resto' ) ; 			
			}
			else
			{
				// On ouvre notre alert des erreurs de formulaire
				ouverture_alert(retour_erreur_formulaire = response_text) ; 
			}
			
		}
	}
}

function renitialisation_mdp_compte_resto()
{
	chargement_bloc_action() ; 
	
	// Initialisation des variables 
	var nouveau_mdp_compte_resto = document.getElementById('nouveau_mdp_compte_resto').value,  
	lien_unique_renitialisation = document.getElementById('lien_unique_renitialisation').value ; 
	
	var xhr = creation_xhr(); 
	
	xhr.open('POST', 'ajax/compte_resto/renitialiser_mdp_compte_resto.php');
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send('nouveau_mdp_compte_resto=' + nouveau_mdp_compte_resto + '&lien_unique_renitialisation=' + lien_unique_renitialisation);
	
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && xhr.status == 200)
		{
			chargement_bloc_action() ; 
			
			var reponse_texte = xhr.responseText ; 
			
			if(reponse_texte == '')
			{
				ouverture_alert(alert_basic = 'Votre mot de passe a bien été réinitialisé, vous pouvez à présent vous connecter');
				cache_bloc_coulissant ('#bloc_renitialisation_mdp_compte_resto' ) ; 
				
			}
			else
			{
				ouverture_alert(alert_basic = reponse_texte) ; 
			}
		}
	}
}

// Pouvoir le réutiliser dans l'iframe // ajout de bloc
function ajout_bloc_image(idimage, nom_image, methode)
{
	if(methode == 'ajout-de-plat')
	{
		var texte_input = '* Nom du plat :' ,
		placeholderText = 'Choisissez le nom du plat' ;
	}
	else if(methode == 'carte_resto')
	{
		var texte_input = '* Nom de la carte :' ,
		placeholderText = 'Choisissez le nom de la carte' ;
	}
	
	var div = document.createElement('div') ; 
	div.className = 'bloc_gestion_image_upload' ;
	div.id = 'bloc_gestion_image_upload' + idimage + '' + methode ;
	div.innerHTML = '<span id="supp_image' + idimage + '' + methode +'" class="delete_image titre_site" onclick="supprimer_image_upload('+ idimage +' , \'' + methode + '\');"> X </span><p class="preview_image texte_site" id="image_image' + idimage + '' + methode +'"><img onclick="apercu_image(\'../temporaire/' + nom_image + '.jpg\') ; " class="apercu_image_miniature" src="../temporaire/miniature/'+ nom_image +'.jpg" /></p><br /><div><span class="texte_site">'+ texte_input +'</span></br /><input id="input_nom_image'+ idimage + '' + methode +'" type="text" class="input reset_input" name="nom_image' + idimage + '' + methode +'" placeholder="'+ placeholderText +'" required/></div>' ;
	
	// On ajoute dans le formulaire juste avant le button suivant processus 
	var formulaire_image_nom =  document.getElementById('formulaire' + methode); 
	
	formulaire_image_nom.insertBefore(div, document.getElementById('bloc_button_suivant_processus' + methode)) ;
	
	// petite miniature en dessous de récap que l'on ne va mettre que si c'est pour l'ajout de plat
	if(methode == 'ajout-de-plat')
	{
		var recap_image = document.getElementById('recap_image' + methode) ,
		image_apercu = document.createElement('li') ; 
		image_apercu.id = 'recap_image_plat' + idimage ; 
		image_apercu.innerHTML = '<p class="preview_image_miniature texte_site"><img onclick="apercu_image(\'../temporaire/' + nom_image + '.jpg\') ; " class="apercu_image_miniature" src="../temporaire/miniature/'+ nom_image +'.jpg" /></p>' ;
		
		recap_image.appendChild(image_apercu) ;
		
		document.getElementById('info_ajout_plat').style.display='none' ;
	}
	
	// Si on est déjà dans ajout de plat pas de probleme,sinon on fait apparaitre l'ajout de plat avant de le lancer
	if(methode == 'ajout-de-plat' && document.getElementById('bloc_ajout_de_plat').style.display == 'none')
	{
		retour_etape_ajout_plat(methode , function()
		{
			scroll_to_image(idimage , methode) ; 
		}) ;
	}
	else
	{
		scroll_to_image(idimage, methode) ; 
	}
	
	if(document.getElementById('bloc_button_suivant_processus' + methode).style.display == 'none')
	{
		$("#bloc_button_suivant_processus" + methode).fadeIn(200) ;
	}
}
function scroll_to_image(idimage, methode)
{
	// On temporise pour que la fonction ne soit pas envoyé tout de suite
	setTimeout(function(){$('body').scrollTo('#bloc_gestion_image_upload' + idimage + '' + methode, 1000, {queue:true});} , 100) ; 
}
function upload_image(id_image_max , methode, envoi_multiple) 
{
	var affichage_pourcentage = document.getElementById('progresse_upload' + methode) ; 
	
	var fileInput = document.getElementById('fichier_upload' + methode),
	progress = document.getElementById('progress' + methode);
	
	erreur_upload = '' ; 
	
	//On compte le nombre de fichier dans l'input file que l'on va parcourir un par un
	var taille_tableau = fileInput.files.length;
	
	// Si la taille du tableau est différent de 0 on envoi le début de l'upload et l'upload
	if(taille_tableau != 0)
	{
		debut_upload(methode);
		
		multi_upload(0) ;
	}
	
	// On utilise une fonction récursive pour simuler un calback et non une boucle qui va tout lancer en même temps ! 
	function multi_upload(i)
	{
		// C'est le nombre d'image qu'il faut récupérer car le premier est 0 donc  il y aura 0 images + id 0 donc 0 perfect, si déjà up il va savoir que y à une images donc 1 + premier iténérance 0 donc 1 et ainsi de suite ce qui est parfait ! 
		var idimage = id_image_max + i ;
		
		// Permet de voir le téléchargement en cours
		document.getElementById('telechargement_en_cour' + methode).innerHTML = i + 1 + '/' + taille_tableau ;
		 
		xhr = creation_xhr() ; 

		xhr.open('POST', '/ajax/compte_resto/ajout_image.php');
		// Récupération de l'état du téléchargement
		xhr.upload.onprogress = function(e){
			progress.value = e.loaded;
			progress.max = e.total;
			
			// On donne le pourcentage d'upload
			affichage_pourcentage.value = parseInt((e.loaded / e.total) * 100);
		};
		
		// Création d'un object formData qui peux contenir des fichiers
		var form = new FormData();
		
		//Injection par la method append du fichier x // pour ajouter une variable => form.append('nom_variable', valeur);	
		form.append('monfichier', fileInput.files[i]);
		
		// Si c'est une modification de plat on aimerais bien pouvoir récupérer l'input du plat et le mettre en form 
		if(methode == 'modif-de-plat')
		{
			var idimagemodif = document.getElementById('input_plat_a_modif').value ;
			form.append('idimagemodif',idimagemodif);
		}
		
		// Si c'est une modification de facade resto
		if(methode == 'facade_resto')
		{
			form.append('facade_resto' , 1);
		}
		
		// Si l'envoi est multiple on le précise pour gérer en ajax
		if(envoi_multiple == 'multiple')
		{
			form.append('envoi_multiple', methode);
		}
		
		// On envoi le tout
		xhr.send(form);
		
		// Récupération du resultat si c'est le dernier fichier
		xhr.onreadystatechange = function() 
		{
			if (xhr.readyState == 4 && xhr.status == 200) 
			{
				//Récupérer l'état les réponses ( préférer a des session PHP car + rapide et utile en cas d'annulation pour voir les fichiers envoyé )
				
				progress.value = 0;
				
				var tableau_json = JSON.parse(xhr.responseText) ; 
				
				// Si il n'y a pas d'erreur
				if(tableau_json['erreur'] == 0 && envoi_multiple != '')
				{
					ajout_bloc_image(idimage , tableau_json['nom_image'] , methode) ;
				}
				else
				{
					var numero_image = i + 1 ; 
					
					// Si c'est un upload unique on met un message d'erreur perso
					if(envoi_multiple == '')
					{
						erreur_upload = tableau_json['erreur'] ; 
					}
					else
					{
						erreur_upload = erreur_upload + '<span>Erreur Image numéro ' + numero_image + ' : <br />' + tableau_json['erreur'] + '<br /><br />'  ; 
					}
				}
				
				//Si c'est le dernier fichier
				if(i == taille_tableau - 1 || envoi_multiple == '')
				{
					fin_upload(methode);
					
					// Si ce n'est pas un upload multiple
					if(envoi_multiple == '')
					{
						if(methode == 'modif-de-plat')
						{
							AppendImageModif(tableau_json['nom_image']) ; 
						}
						else if(methode == 'facade_resto')
						{
							// Création d'une fonction pour pouvoir le mettre également dans le iframe ie8
							AppendFacadeRestoBloc(tableau_json['nom_image']) ; 
						}
					}
					else
					{
						// Permet de faire comprendre que y a des images en plus pour un prochain upload , +1 pour chaque tour 
						document.getElementById('fichier_upload' + methode).onchange = function(){upload_image(idimage + 1, methode , 'multiple')};
					}
					
					// On affiche les erreurs à la fin
					if(erreur_upload != '')
					{
						ouverture_alert(alert_basic = erreur_upload); 
					}
				}
				// sinon on rappel la fonction en ajoutant 1 pour passer à l'élement d'après
				else
				{
					multi_upload(i + 1);
				}
			}
		};
	};
}
function debut_upload(methode)
{
	document.getElementById('annuler_upload' + methode).style.display='inline' ;

	$('#picto-upload' + methode).fadeOut(300 , function() 
		{
			$('#bloc_pourcentage_fichier' + methode).fadeIn(300) ; 
		}
	);
}
function fin_upload(methode)
{
	$('#bloc_pourcentage_fichier' + methode).fadeOut(300 , 
		function() 
		{
			$('#picto-upload' + methode).fadeIn(300) ; 
			document.getElementById('progresse_upload' + methode).value = 0 ; 
		}
	);
	
	document.getElementById('progress' + methode).value= '0' ;
}
function annuler_upload(methode)
{
	xhr.abort();
	fin_upload(methode) ;
}
//Supprimer image au click
function supprimer_image_upload(idimage , methode)
{
	// Si il existe une réponse de la part de la fonction
	if(typeof(reponse_fonction) != 'undefined' && reponse_fonction != '')
	{
		//Si c'est ok tant mieux
		if(reponse_fonction == 'ok')
		{
			chargement_bloc_action() ; 
			
			var xhr = creation_xhr() ; 
				
			xhr.open('GET' , '../ajax/compte_resto/supp_image_upload.php?idimage=' + idimage + '&methode=' + methode) ;
			xhr.send() ;
			xhr.onreadystatechange = function() 
			{
				if (xhr.readyState == 4 && xhr.status == 200)
				{
					chargement_bloc_action() ; 

					// Suppression du noeuf du bloc de l'image
					$( "#bloc_gestion_image_upload" + idimage + '' + methode ).fadeOut(200 , function(){
						var supp_noeu = document.getElementById('bloc_gestion_image_upload' + idimage + '' + methode)
						document.getElementById('formulaire' + methode ).removeChild(supp_noeu) ;
					});
					
					// Si la méthode est l'ajout de plat
					if(methode == 'ajout-de-plat')
					{
						// Suppression du noeuf du petit icone de recap des plats
						$( "#recap_image_plat" + idimage ).fadeOut(200 , function(){
							var supp_recap_image = document.getElementById('recap_image_plat' + idimage);
							document.getElementById('recap_image' + methode).removeChild(supp_recap_image) ;
						});
					}
				} 
			}
		}
		//on vide la variable réponse
		reponse_fonction = ''; 
	}
	else
	{
		// La fonction à rappeler après le confirme
		fonction_a_appeler = function()
		{
			supprimer_image_upload(idimage , methode) ; 
		};
		 
		if(methode == 'ajout-de-plat')
		{
			ouverture_alert(confirm_action = 'Voulez-vous vraiment supprimer ce plat de votre liste d\'ajout de plat ? ' );
		}
		else if(methode == 'carte_resto')
		{
			ouverture_alert(confirm_action = 'Voulez-vous vraiment retirere cette carte resto de vos cartes ? ' );
		}
	}
}
function envoyer_nom_image_upload(id_image_max , methode)
{
	var url_a_envoyer = '' ;
	
	// On reprend les valeurs des champs input pour les envoyés + 1 pour envoyer une premiere fois 
	for(i = 0 ;  i <= id_image_max ; i++)
	{
		if(document.getElementById('input_nom_image' + i + '' + methode))
		{
			// Si c'est le premier on met pas de &
			if(i == 0)
			{
				url_a_envoyer = 'nom_image_upload' + i + '' + methode + '='+ document.getElementById('input_nom_image' + i + '' + methode).value ; 
			}
			else
			{
				url_a_envoyer = url_a_envoyer + '&nom_image_upload' + i + '' + methode + '=' + document.getElementById('input_nom_image' + i + '' + methode).value ; 
			}
		}
	}
	
	//On envoi un ajax pour sauvegarder le nom des plats 
	var xhr = creation_xhr() ; 
	
	// Récupération en xhr du nombre d'image = plus rapide et plus securise
	xhr.open('POST' , '../ajax/compte_resto/sauvegarde_nom_image.php') ;
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send(url_a_envoyer + '&methode=' + methode) ;
}
function terminer_envoi_upload_multiple_image(methode) 
{
	// On load la barre de chargement 
	chargement_bloc_action() ; 

	var xhr = creation_xhr() ; 
	
	// Récupération en xhr du nombre d'image = plus rapide et plus securise
	xhr.open('GET' , '../ajax/compte_resto/recuperation_nombre_image.php?methode=' + methode) ;
	xhr.send() ;
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && xhr.status == 200)
		{
			var tableau_json = JSON.parse(xhr.responseText)
			nombre_image = tableau_json['nombre_image']  ;
			id_image_max = tableau_json['id_image_max']; 

			if(methode == 'ajout-de-plat')
			{
				// Remplissage du nombre d'image
				$('.nombre_plat_span').html('( x'+ nombre_image +' )') ; 
				
				// Si le nombre d'image est supérieur à 1 on enleve l'offre d'essais
				if(nombre_image == 1)
				{
					document.getElementById('bloc_offre_essai').style.display = 'block' ;  
				}
				else
				{
					document.getElementById('bloc_offre_essai').style.display = 'none' ;
				}
				
				// direct pour qu'il calcule déjà le prix
				montant_totale = calcule_total() ;
				
				// Si il y à bien des images à up
				if(typeof(montant_totale) != 'undefined' && montant_totale != 0)
				{
					//Calcule de l'id de la commande
					calcule_id_commande(nombre_image) ; 
					
					// On cache le bloc d'ajout de plat et on fait apparaitre celui des abonnements
					cache_bloc_coulissant('#bloc_ajout_de_plat' , function()
					{
						faire_apparaitre_choix_abonnement(methode) ;
					});
				}
				else
				{
					// Sinon on ne fait rien et on le dit
					ouverture_alert(alert_basic = 'Vous n\'avez aucune image à ajouter !');
				}
			}
				// On envoi en session le nom des images
				envoyer_nom_image_upload(id_image_max , methode) ; 
				
				// On arrete le chargement
				chargement_bloc_action() ; 
		}
	}
	
}
function faire_apparaitre_choix_abonnement(methode)
{
	$('body').scrollTo('#titre_choix_abonnement', 1000, {queue:true});
	document.getElementById('bloc_choix_abonnement').style.left = '0px' ;
	// Si on est sur une résolution en dessous de 600 px on fait disparaitre le bloc
	if($('#bloc_upload' + methode).css('position') == 'static' )
	{
		document.getElementById('bloc_upload' + methode).style.display = 'none' ;  
	}
	
	voir_bloc_coulissant('#bloc_choix_abonnement' , function()
	{
		$('#bloc_recap_image' + methode).fadeIn(300);
	});
	
	// Un point dans l'history pour le montrer
	if(history.pushState)
	{
		history.pushState(null, null);	
	}
}
function retour_etape_ajout_plat(methode, calback)
{
	$('#bloc_recap_image' + methode).fadeOut(300) ;
	
	cache_bloc_coulissant('#bloc_choix_abonnement' , function()
	{
		if(typeof(calback) != 'undefined' && calback != '')
		{
			faire_apparaitre_ajout_plat(calback);
		}
		else
		{
			faire_apparaitre_ajout_plat();
		}
	} , 'right');
	
	// Si l'upload n'est pas visible on l'appel 
	display_bloc_upload(methode) ; 
}
function faire_apparaitre_ajout_plat(calback)
{
	document.getElementById('bloc_ajout_de_plat').style.left = '0px' ;
	
	if(typeof(calback) != 'undefined' && calback != '')
	{
		voir_bloc_coulissant('#bloc_ajout_de_plat' , function()
		{
			calback.call();
		});
	}
	else
	{
		voir_bloc_coulissant('#bloc_ajout_de_plat' , function()
		{
			$('body').scrollTo('#titre_ajout_plat', 1000, {queue:true});
		});
	}
}
function apparaitre_disparaitre_bloc_offre_sans_abonnement()
{
	if(document.getElementById('bloc_offre_sans_abonnement').style.display == 'none')
	{
		$('#bloc_offre_sans_abonnement').fadeIn() ;
	}
	else
	{
		$('#bloc_offre_sans_abonnement').fadeOut() ; 
	}
}
function calcule_total()
{
	var prix_total = 0 ; 
	var prix_unitaire = 0 ;
	var nom_object_vente = '';
	
	// On est obligé de faire comme ça pour les offre compplémentaire
	function check_formule(id_formule)
	{
		// Voir la formule choisis
		var statut_check = document.getElementById('offre_' + id_formule).checked ; 
		
		// Si la formule de base est bien checké
		if(statut_check == true)
		{
			prix_total = prix_total + prix_formule[id_formule] * nombre_image ; 
			prix_unitaire = prix_formule[id_formule] ; 
			
			if(id_formule == 'abonnement')
			{
				if(nombre_image > 1)
				{
					var variable_s = 's' ;
				}
				else
				{
					var variable_s = '' ; 
				}
				
				 nom_object_vente = 'Abonnement mensuel DMF ' + nombre_image + ' plat' + variable_s  ;
			}
			else
			{
				nom_object_vente = 'Image en ligne ' + temps_abonnement_formule[id_formule] + ' mois' ;
			}
			
			document.getElementById('stocker_choix_abonnement').value = id_formule;
			
			document.getElementById('champ_prix_abonnement_mois').value =  (prix_formule[id_formule] * nombre_image) ;
			
			// Si l'id check est l'id de l'abonnement 
			if(id_formule == 'abonnement')
			{
				// On va d'un abonnement ou d'un paiement direct Si c'est un abonnement => _xclick-subscriptions si c'est un achat direct _xclick
				document.getElementById('champ_type_offre_abonnement').value = '_xclick-subscriptions' ;
				
				// On display le chaque mois si ce n'est pas fait 
				$('.chaque_mois_complement_abonnement').css('display' , 'inline') ;
				$('.prix_resume_abonnement_plat_unitaire').html(prix_total) ;
				
				// C'est un abonnement donc pas question de mettre le godMod
				document.getElementById('bloc_godMod_paiement').style.display = 'none' ; 
			}
			else
			{
				// Sinon ce sont les anciens
				document.getElementById('champ_type_offre_abonnement').value = '_xclick' ;
				
				// On enleve le chaque mois si ce n'est pas fait 
				$('.chaque_mois_complement_abonnement').css('display' , 'none') ;
				
				// On peut montrer le godMod puisque on veut prendre un abonnement que l'on paie directement
				document.getElementById('bloc_godMod_paiement').style.display = 'block' ; 
			}
		}
		else
		{
			// Si c'était la formule de l'abonnement alors on commence avec le premier abonnement
			if(id_formule == 'abonnement')
			{
				check_formule(1) ; 
			}
			else
			{
				check_formule(id_formule + 1) ; 
			}
		}
	}
	// Ici ça va etre la formule de base donc l'abonnement
	check_formule('abonnement') ;
	
	// Voir si l'option facebook est coché
	if(document.getElementById('offre_facebook').checked == true)
	{
		prix_total = prix_total + prix_formule['facebook'] *  nombre_image;
		prix_unitaire = prix_unitaire + prix_formule['facebook']; 
		nom_object_vente = nom_object_vente + ' + offre facebook' ; 
	}
	if(document.getElementById('offre_news').checked == true)
	{
		prix_total = prix_total + prix_formule['news'] *  nombre_image;
		prix_unitaire = prix_unitaire + prix_formule['news']; 
		nom_object_vente = nom_object_vente + ' + news DMF' ; 
	}
	
	// Arrondi du prix total et prix unitaire
	prix_total = Math.round(prix_total*100)/100;
	prix_unitaire = Math.round(prix_unitaire*100)/100;
	
	document.getElementById('total_formule').innerHTML = prix_total ;
	document.getElementById('prix_resume_abonnement_plat').innerHTML = prix_total ; 
	document.getElementById('champ_paypal_quantite').value = nombre_image ; 
	document.getElementById('champ_prix_remplissage').value =  prix_unitaire; 
	document.getElementById('champ_nom_remplissage').value =  nom_object_vente;
	document.getElementById('champ_premier_mois_paiement').value = prix_total ;
	
	return prix_total ; 
}
function calcule_id_commande(nombre_image)
{
	var date_actuelle = new Date(); 
	date_actuelle = date_actuelle.getTime()
	var id_commande = login_compte_resto + '-' + nombre_image + '-' +  date_actuelle ;
	document.getElementById('champ_custum_remplissage').value = id_commande; 
}
function envoi_formulaire_paypal(godModRestoAjoutPlat)
{
	chargement_bloc_action() ; 
	
	// on met le curser pour attendre
	document.getElementsByTagName('body')[0].style.cursor = 'wait' ;
	
	if(typeof(formulaire_deja_envoye) == 'undefined')
	{
		var xhr = creation_xhr() ; 
		
		var abonnement = document.getElementById('stocker_choix_abonnement').value;
		var prix_total = document.getElementById('total_formule').innerHTML ;
		var id_commande = document.getElementById('champ_custum_remplissage').value ;
		var mail_qui_recoit_facture = document.getElementById('mail_qui_recoit_facture').value ; 
		
		var options = '';
		
		// Voir si l'option facebook est coché que l'on met a la fin pour éviter a avoir à l'enlever
		if(document.getElementById('offre_facebook').checked == true )
		{
			options = options + 'facebook' ; 
		}
		if(document.getElementById('offre_reduction').checked == true)
		{
			// Si reduction_associe est selectionne alors on ne créer pas une nouvelle réduction
			var reduction_associe = document.getElementById('reduction_associe');		
			
			// Si une réduction est bien faites 
			if(reduction_associe.value != 0)
			{
				var id_reduction = reduction_associe.value ; 
				
				// Ajout de la réduction dans les options
				if(options == '')
				{
					options = 'reduction|==|' + id_reduction ;
				}
				else
				{	
					options = options + '|-<>-|reduction|==|' + id_reduction ;
				}
			}
		}
		if(document.getElementById('offre_news').checked == true)
		{			
			// Ajout de la news dans les options
			if(options == '')
			{
				options = 'news' ;
			}
			else
			{	
				options = options + '|-<>-|news' ;
			}
		}

		xhr.open('POST', '../ajax/commandes/ajout_commande.php');
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.send('abonnement=' + abonnement + '&options=' + options +  '&prix_total=' + prix_total + '&id_commande=' + id_commande + '&mail_qui_recoit_facture=' + mail_qui_recoit_facture);
	 
		xhr.onreadystatechange = function() 
		{
			if (xhr.readyState == 4 && xhr.status == 200)
			{

				if(document.getElementById('loading_envoi'))
				{
					document.getElementById('loading_envoi').style.display = 'inline' ;
				}
				
				// Si on est en mode Dieu alors la variable godMod dois exister, il faut également que l'offre ne soit pas un abonnement
				if(typeof(godModRestoAjoutPlat) != 'undefined' && abonnement != 'abonnement')
				{
				
					var token = document.getElementById('token_ajout_de_plat').value ; 
					
					// On fais un xhr en direction direct du fichier ou sont ajoutés les plats 
					xhr.open('POST', 'paiement/trait_paiement.php');
					xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
					xhr.send('token=' + token + '&id_commande=' + id_commande);
				 
					xhr.onreadystatechange = function() 
					{
						if (xhr.readyState == 4 && xhr.status == 200)
						{
							// On redirige direct vers le compte du mec 
							window.location.href = 'paiement/finalisation.html' ;
						}
					}
					
				}
				else
				{
					document.formulaire_paiement_paypal.submit();
					
					chargement_bloc_action() ; 
				}
			}
		}
		formulaire_deja_envoye = 1 ;	
	}
}

function ouvrir_bloc_modif(idimagemodif)
{
	var idimage = document.getElementById('input_hidden_idimage' + idimagemodif).value ;
	
	document.getElementById('image_plat_nouvelle').innerHTML = '<img src="../imgs/picto-new.png" alt="picto new" />' ; 
	
	var timestamp_actuel = new Date().getTime() ; 
	
	document.getElementById('image_plat_ancienne').innerHTML = '<img onclick="apercu_image(\'../plats/' + idimage + '.' + timestamp_actuel + '.jpg\') ; " class="apercu_image_miniature" src="../plats/miniature/' + idimage + '.' + timestamp_actuel + '.jpg"  />' ; 
	
	document.getElementById('input_plat_a_modif').value= idimagemodif ;
	document.getElementById('input_idimage_a_modif').value = idimage ;
	
	voir_bloc_coulissant('#modifimageformulaire' , function()
	{
		var type_upload = 'modif-de-plat' ;
		display_bloc_upload(type_upload) ; 
	}) ; 
}
function AppendFacadeRestoBloc(nom_image)
{
	document.getElementById('bloc_image_facade_resto').innerHTML = '<img onclick="apercu_image(\'../temporaire/' + nom_image + '.jpg\') ; " class="apercu_image_miniature" src="../temporaire/miniature/' + nom_image + '.jpg"  />' ; 
}
function AppendImageModif(nom_image)
{
	document.getElementById('image_plat_nouvelle').innerHTML = '<img onclick="apercu_image(\'../temporaire/' + nom_image + '.jpg\') ; " class="apercu_image_miniature" src="../temporaire/miniature/' + nom_image + '.jpg"  />' ;

	// On fait apparaitre l'input pour envoyer l'image, puis on scroll jusqu'à cet input
	$('#div_valide_modification').fadeIn(200 , function()
	{
		$('#modifimageformulaire').scrollTo('#div_valide_modification', 1000, {queue:true});
	}) ; 
}

function modification_plat()
{
	chargement_bloc_action() ; 
	
	xhr = creation_xhr() ; 
	
	var idimagemodif = document.getElementById('input_plat_a_modif').value,
	idimage = document.getElementById('input_idimage_a_modif').value;
	
	// Si on appel la page c'est que l'on valide le changement donc c'est ok
	xhr.open('POST', '/ajax/compte_resto/modif_plat.php');
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send('idimagemodif=' + idimagemodif + '&idimage=' + idimage ); 
	
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && xhr.status == 200) 
		{	
			chargement_bloc_action() ; 
			
			var tableau_json_modif = JSON.parse(xhr.responseText) ; 
			
			// Si il n'y a aucune erreur on affiche simplement un message comme quoi ca a fonctionne
			if(tableau_json_modif['erreur'] == 0)
			{	
				ouverture_alert(alert_basic = 'Votre image a bien été modifiée');
				
				cache_bloc_coulissant ('#modifimageformulaire' , function()
				{
					// L'input de validation on le remasque
					document.getElementById('div_valide_modification').style.display = 'none' ;
				}) ; 
				
				var timestamp_actuelle = new Date().getTime();
				
				document.getElementById('image' + idimagemodif).style.backgroundImage = "url('/plats/miniature/"+ idimage + "." + timestamp_actuelle + ".jpg')" ;
			}
			else
			{
				ouverture_alert(alert_basic = tableau_json_modif['erreur']); 
			}
		}
	};
}
function modif_image_facade_resto(type)
{
	chargement_bloc_action() ; 
	
	xhr = creation_xhr() ; 
	
	// Si on appel la page c'est que l'on valide le changement donc c'est ok
	xhr.open('POST', '/ajax/compte_resto/modif_image_facade_resto.php');
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send(); 
	
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && xhr.status == 200) 
		{
			chargement_bloc_action() ; 
			
			var tableau_json_modif = JSON.parse(xhr.responseText) ; 
			
			// Si il n'y a aucune erreur on affiche simplement un message comme quoi ca a fonctionne
			if(tableau_json_modif['erreur'] == 0)
			{	
				// Passage au suivant
				if(typeof(type) != 'undefined' && type == 'compte')
				{
					cache_bloc_coulissant('#bloc_modif_compte_resto') ; 
					
					var timestamp_actuelle = new Date().getTime() ; 
					
					var idfacade = tableau_json_modif['nom_facade_resto'] ; 
					
					var chemin_facade = "image-resto/" + idfacade +  "." + timestamp_actuelle + ".jpg" ; 
					// On modifie la photo de la facade
					document.getElementById('facade_resto_visielle').style.backgroundImage = "url('" + chemin_facade + "')" ;
					
					// On modifie également sont onclick qui est l'ancien pour voir la photo en plein écran
					facade_resto_visielle.onclick = function()
					{
						apercu_image(chemin_facade) ; 
					};
				}
				else
				{
					voir_cache_facade_resto() ;
				}				
			}
			else
			{
				ouverture_alert(alert_basic = tableau_json_modif['erreur']); 
			}
		}
	};
}

function voir_cache_facade_resto()
{
	if(document.getElementById('bloc_ajout_facade_complementaire').style.display != 'none')
	{
		$('#bloc_ajout_facade_complementaire').fadeOut(300, function()
			{
				$('#bloc_ajout_attribus_complementaire').fadeIn(300) ; 
			}
		) ;
	}
	else
	{
		display_bloc_upload('facade_resto') ;
		
		$('#bloc_ajout_attribus_complementaire').fadeOut(300, function()
			{
				$('#bloc_ajout_facade_complementaire').fadeIn(300) ; 
			}
		) ;
	}
	
	// Dans tout les cas on revient en haut pour + de lisibilité 
	$('#enregistrement').scrollTo(0, 1000, {queue:true});
}

function voir_cache_contact_pro()
{
	if(document.getElementById('bloc_ajout_contact_pro').style.display != 'none')
	{
		$('#bloc_ajout_contact_pro').fadeOut(300, function()
			{
				$('#bloc_ajout_attribus_complementaire').fadeIn(300) ; 
			}
		) ;
	}
	else
	{
		// Il y à de forte chance à ce que l'on soit tout en bas ducoup on remonte si possible en haut 
		$('#bloc_ajout_attribus_complementaire').fadeOut(300, function()
			{
				$('#bloc_ajout_contact_pro').fadeIn(300 , function()
				{
					$('#enregistrement').scrollTo('#titre_ajout_contact_pro', 200, {queue:true});
				}) ; 
			}
		) ;
	}
}

function reset_formulaire_ajout_contact_pro(fonction_a_appeler) 
{
	// Reset des données formulaire direct
	document.getElementById('formulaire_ajout_contact_pro').reset() ;
	
	// Permet de modifier les class et de les remettre à off
	$('.bulle_jour_semaine_on').removeClass("bulle_jour_semaine_on").addClass("bulle_jour_semaine_off") ; 
	
	// On s'asurre que les buttons soient lus visible
	$('.select_disponibilite_semaine').css('display' , 'none') ;  
	
	if(typeof(fonction_a_appeler) != 'undefined' && fonction_a_appeler != '')
	{
		fonction_a_appeler.call() ; 
	}	
}

function remplissage_formulaire_modif_contact_pro(id, civ, nom, prenom, poste, email , tel, disponibilite)
{
	// On doit reset le formulaire pour s'assurer que rien n'à été mis avant 
	reset_formulaire_ajout_contact_pro(function()
	{
		modif_disponibilite_contact_pro_affichage(disponibilite) ; 
	}) ; 
	
	//Tout d'abord on inscrit lid dans un input pour savoir lequel modifier 
	document.getElementById('input_id_modif_contact_pro').value = id ; 
	document.getElementById('civ_contact_pro').value = civ ; 
	document.getElementById('nom_contact_pro').value = nom ; 
	document.getElementById('prenom_contact_pro').value = prenom ; 
	document.getElementById('post_contact_pro').value = poste ; 
	document.getElementById('email_contact_pro').value = email ; 
	document.getElementById('tel_contact_pro').value = tel ;
	
}

function modif_disponibilite_contact_pro_affichage(disponibilite)
{ 
	// Les disponibilites
	var tableau_jour_dispo = disponibilite.split('--') ;
	var nombre_jour_dispo = tableau_jour_dispo.length ; 
	
	for(i=0; i < nombre_jour_dispo ; i++)
	{
		var jour_dispo_horraire = tableau_jour_dispo[i].split('-||-') ;
		
		// Pour activer la select
		select_jour_semaine(jour_dispo_horraire[0], 'disponibilite_pro' , 1) ; 
		
		// On met le select à la valeur indiqué 
		document.getElementById('select' + jour_dispo_horraire[0] + 'disponibilite_pro').value = jour_dispo_horraire[1] ; 
	}
}

function affichage_formulaire_ajout_contact_pro_creation_compte()
{
	// On rénitialise le formulaire avec la fonction pour voir le formulaire
	reset_formulaire_ajout_contact_pro() ; 
	
	$('#formulaire_ajout_contact_pro').fadeIn() ;
	
	// On scroll en sa direction 
	$('#enregistrement').scrollTo('#formulaire_ajout_contact_pro', 1000, {queue:true});

}

function ajout_formulaire_ajout_contact_pro() 
{
	// Création du formulaire afin de l'ajouter dans div
	
	// On compte le nombre d'enfant de conteneur_formulaire_ajout_contact_pro pour savoir combien de div il y à déjà
	var conteneur_formulaire_ajout_contact_pro = document.getElementById('conteneur_formulaire_ajout_contact_pro') ;

	var  nombre_enfant_conteneur = conteneur_formulaire_ajout_contact_pro.childNodes ; 
	
	var formulaire_clone = document.getElementById('formulaire_ajout_contact_pro_' + nombre_enfant_conteneur).cloneNode(true) ;
	
	conteneur_formulaire_ajout_contact_pro.appendChild(formulaire_clone) ; 
}

function ajouter_contact_pro(action)
{
	chargement_bloc_action() ; 
	
	// On bloque l'action du formulaire d'ajout pour éviter qu'une personne en ajoute plein en meme temps
	document.getElementById('formulaire_ajout_contact_pro').onsubmit = '' ; 
	
	// Si c'est une modification que le champ adéquat est rempli avec l'id du contact à modifier
	var id_contact_pro_modif = document.getElementById('input_id_modif_contact_pro') ; 
	
	// Si on est bien dans le compte resto connecté
	if(id_contact_pro_modif)
	{
		// Si c'est bien une modif
		if(id_contact_pro_modif.value != '')
		{
			var id_modif = id_contact_pro_modif.value ; 
			
			// On change l'action par modif 
			action = 'modif_contact' ; 
		}
		else
		{
			var id_modif = '' ; 
		}
	}
	else
	{
		id_modif = '' ; 
	}
	
	xhr = creation_xhr() ;
	
	var civ = document.getElementById('civ_contact_pro').value , 
	nom = document.getElementById('nom_contact_pro').value ,
	prenom = document.getElementById('prenom_contact_pro').value ,
	email = document.getElementById('email_contact_pro').value ,
	tel = document.getElementById('tel_contact_pro').value , 
	poste = document.getElementById('post_contact_pro').value ; 
	
	if(document.getElementById('token_modif_compte'))
	{
		var token = document.getElementById('token_modif_compte').value ; 
	}
	else
	{
		var token = document.getElementById('token_compte_juste_cree').value ; 
	}
	
	var chaine_jour_semaine = '' ; 
	
	// Trouver les jours et leur dispo tout ceux qui sont sur on bulle_jour_semaine_on
	
	// Si ClassName est supporté 
	if(document.getElementsByClassName)
	{
		var jour_semaine_on = document.getElementsByClassName('bulle_jour_semaine_on') ; 
	}
	else
	{
		// Sinon ie supporte le querySelectorAll
		var jour_semaine_on = document.querySelectorAll('.bulle_jour_semaine_on') ;
	}
	
	// Nombre de jour active 
	var nombre_jour_semaine = jour_semaine_on.length ; 
	
	// On va boucler pour récupérer les valeurs
	for(i=0 ; i < nombre_jour_semaine ; i++)
	{
		// On supprime les espaces en trop 
		var jour_semaine_texte = trim(jour_semaine_on[i].innerHTML) ;
			
		// Si le select est bien là alors on l'ajoute également
		if(document.getElementById('select' + jour_semaine_on[i].id).style.display != 'none')
		{
			var valeur_select = document.getElementById('select' + jour_semaine_on[i].id).value ; 
			
			var a_ajouter_chaine = jour_semaine_texte + '-||-' + valeur_select  ;
		}
		else
		{
			var a_ajouter_chaine = jour_semaine_texte ;
		}
		
		// Si c'est la première occurrence 
		if(chaine_jour_semaine == '')
		{
			chaine_jour_semaine = a_ajouter_chaine ;
		}
		// Si c'est la dernière occurence
		else
		{
			chaine_jour_semaine = chaine_jour_semaine + '--' + a_ajouter_chaine ;
		}
	}
	
	xhr = creation_xhr() ;
	
	xhr.open('POST', '/ajax/compte_resto/ajout_modif_contact_pro.php');
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	
	xhr.send('jour_semaine=' + chaine_jour_semaine + '&civ=' + civ + '&nom=' + nom + '&prenom=' + prenom + '&poste=' + poste + '&email=' + email + '&tel=' + tel + '&action=' + action + '&id_modif=' + id_modif + '&token=' + token); 
	
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && xhr.status == 200) 
		{
			chargement_bloc_action() ; 
			
			var tableau_json = JSON.parse(xhr.responseText) ; 
			
			// On remet le bouton en submit
			document.getElementById('formulaire_ajout_contact_pro').onsubmit = function()
			{
				ajouter_contact_pro('ajout_contact') ; return false ;
			}; 
			
			// Si aucun erreur alors on créer le nouvelle utilisateur
			if(tableau_json['erreur'] == 0)
			{
				if(action == 'modif_contact')
				{
					ouverture_alert(alert_basic = 'Ce contact a bien été modifié ! Veuillez patienter le rechargement des données.');
					
					setTimeout(function()
					{
						window.location.reload() ; 
					}, 1000) ; 
				}
				else
				{
					reset_formulaire_ajout_contact_pro() ;
					
					// Si on est pas dans le compte
					if(!document.getElementById('bloc_global_donnees_resto'))
					{
						$('#formulaire_ajout_contact_pro').fadeOut(300, function()
						{
							// On lance la fonction qui permet de créer un nouvelle utilisateur
							creation_ok_nouveau_contact_pro(civ,nom, prenom) ;
						}) ; 
					}
					else
					{
						// C'est qu'on est sur le compte donc on met un message et on reload
						ouverture_alert(alert_basic = 'Votre contact a bien été ajouté ! Veuillez patienter le rechargement des données.');
					
						setTimeout(function()
						{
							window.location.reload() ; 
						}, 1000) ; 
					}
				}
			}
			else
			{
				ouverture_alert(alert_basic = tableau_json['erreur']);
			}
		}
	} ;
}

function supprimer_contact_pro(id_contact)
{
	if(typeof(id_contact) != 'undefined')
	{
		id_a_supp_contact_pro = id_contact ;
	}
	
	if(typeof(reponse_fonction) != 'undefined' && reponse_fonction != '')
	{
		if(reponse_fonction == 'ok')
		{
			if(document.getElementById('token_modif_compte'))
			{
				var token = document.getElementById('token_modif_compte').value ; 
			}
			else
			{
				var token = document.getElementById('token_compte_juste_cree').value ; 
			}
			
			chargement_bloc_action() ; 
			
			xhr = creation_xhr() ;
			
			xhr.open('POST', '/ajax/compte_resto/supprimer_contact_pro.php');
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			
			xhr.send('id_contact=' + id_a_supp_contact_pro + '&token=' + token); 
			
			xhr.onreadystatechange = function() 
			{
				if (xhr.readyState == 4 && xhr.status == 200) 
				{
					chargement_bloc_action() ; 
					
					var response_texte = xhr.responseText ; 
					
					// Si aucun erreur alors enleve
					if(response_texte == '')
					{
						$('#bloc_contact_pro' + id_a_supp_contact_pro).fadeOut() ; 
					}
					else
					{
						ouverture_alert(alert_basic = response_texte);
					}
				}
			} ;
			
			reponse_fonction = '' ; 
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
		fonction_a_appeler = supprimer_contact_pro;
		ouverture_alert(confirm_action = 'Voulez-vous vraiment supprimer ce contact pro ?') ;
	}
}

function creation_ok_nouveau_contact_pro(civ, nom, prenom)
{	
	if(civ == 1)
	{
		civ = 'monsieur' ; 
	}
	else
	{
		civ = 'madame' ; 
	}
	
	var contact_pro = document.createElement('div') ;
		
	contact_pro.className = 'bloc_affichage_contact_pro' ;
	contact_pro.innerHTML = '<p class="texte_site">' + nom + ' ' + prenom + '</p><img src="imgs/picto-' + civ + '.png" alt="picto '+ civ + '" /><br /><p class="texte_site_noir" style="background-color:white; padding:5px ; border-radius:8px ; ">Vous pourrez gérer ce contact dans les paramètres de votre compte.</p>' ; 
	
	document.getElementById('bloc_tout_les_contact_pro').appendChild(contact_pro) ;
}

function modif_attribut_resto(type)
{
	chargement_bloc_action() ; 
	
	xhr = creation_xhr() ;

	var chaine_xhr = '' ; 
	
	// Si ClassName est supporté 
	if(document.getElementsByClassName)
	{
		var input_checkbox_attribus = document.getElementsByClassName('input_checkbox_attribus') ;
	}
	else
	{
		// Sinon ie supporte le querySelectorAll
		var input_checkbox_attribus = document.querySelectorAll('.input_checkbox_attribus') ;
	}

	// Nombre d'élément input_checkbox_attribus
	var nombre_input_checkbox_attribus = input_checkbox_attribus.length; 
	
	for(i=0 ; i < nombre_input_checkbox_attribus; i++)
	{
		// Récupération voir si l'élément est checké si il l'est on récupère son name en lui attribuant 1
		if(input_checkbox_attribus[i].checked)
		{
			// Si l'attribut name est le prix
			if(input_checkbox_attribus[i].name == 'prix')
			{
				var value_attribus = input_checkbox_attribus[i].value ; 
			}
			else
			{
				var value_attribus = 1 ;					
			}
			
			if(chaine_xhr == '')
			{
				// Si c'est la première occurence dans la chaine on ne met pas de tiret
				chaine_xhr = chaine_xhr + input_checkbox_attribus[i].name + '=' + value_attribus ; 
			}
			else
			{
				chaine_xhr = chaine_xhr + '--' + input_checkbox_attribus[i].name + '=' + value_attribus ; 
			}
		}
		else 
		{
			// Si c'est le prix on ne le rajoute pas
			if(input_checkbox_attribus[i].name == 'prix')
			{}
			else
			{
				if(chaine_xhr == '')
				{
					// Si c'est la première occurence dans la chaine on ne met pas de tiret
					chaine_xhr = chaine_xhr + input_checkbox_attribus[i].name + '=0' ; 
				}
				else
				{
					chaine_xhr = chaine_xhr + '--' + input_checkbox_attribus[i].name + '=0' ; 
				}
			}
		}
	}
	
	if(document.getElementById('token_modif_compte'))
	{
		var token = document.getElementById('token_modif_compte').value ; 
	}
	else
	{
		var token = document.getElementById('token_compte_juste_cree').value ; 
	}
	
	xhr.open('POST', '/ajax/compte_resto/modif_attribus_resto.php');
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	
	// Si ce n'est pas une modification
	if(typeof(type) != 'undefined' && type != 'modif_compte')
	{
		xhr.send('valeur_attribus=' + chaine_xhr + '&token=' + token); 
	}
	else
	{
		xhr.send('valeur_attribus=' + chaine_xhr + '&modif=1&token=' + token);
	}
	
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && xhr.status == 200) 
		{
			chargement_bloc_action() ; 
			
			var tableau_json_modif = JSON.parse(xhr.responseText) ; 
			
			// Si il n'y a aucune erreur on affiche simplement un message comme quoi ca a fonctionne
			if(tableau_json_modif['erreur'] == 0)
			{	
				// On remballe le tout en précisant que l'on pourra les modifiers dans le compte normal
				if(typeof(type) != 'undefined' && type != 'modif_compte')
				{
					infos_complementaire_plus_tard('suivant_contact_pro');  
				}
				else
				{
					setTimeout(function() {window.location.reload() ; } , 500) ; 
					ouverture_alert(alert_basic = 'Vos informations complémentaires ont bien été modifiées.');
				}
			}
			else
			{
				ouverture_alert(alert_basic = tableau_json_modif['erreur']); 
			}
		}
	};
}

function modification_nom_plat(idimagemodif, idimage)
{
	if(document.getElementById('bloc_modif_nom' + idimagemodif).style.display != 'none')
	{
		chargement_bloc_action() ; 
		
		xhr = creation_xhr() ; 
		
		var nouveau_nom_plat = document.getElementById('input_modif_nom' + idimagemodif).value ,
		token = document.getElementById('token_modif_plat').value ; 
		
		// Si on appel la page c'est que l'on valide le changement donc c'est ok
		xhr.open('POST', '/ajax/compte_resto/modifier_nom_plat.php');
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.send('idimagemodif=' + idimagemodif + '&nomplat=' + nouveau_nom_plat + '&idimage=' + idimage + '&token=' + token); 
		
		xhr.onreadystatechange = function() 
		{
			if (xhr.readyState == 4 && xhr.status == 200) 
			{
				chargement_bloc_action() ; 
				
				tableau_json = JSON.parse(xhr.responseText);
				
				if(tableau_json['erreur'] == 0)
				{
					// Si il n'y a aucune erreur on affiche simplement un message comme quoi ca a fonctionne
					$('#bloc_modif_nom' + idimagemodif).fadeOut(200) ; 
					
					// Modification de tout ce qui est nécéssaire au visuel ou pour remodifier proprement
					modification_nom_plat_reussis(idimagemodif , tableau_json['idimage_final']);
					
					$('#nomplat' + idimagemodif).fadeIn(300) ;
				}
				else
				{
					ouverture_alert(alert_basic = tableau_reduction['erreur']) ;
				}
			}
		};
	}
	else
	{
		$('#nomplat' + idimagemodif).fadeOut(200 , function(){
			$('#bloc_modif_nom' + idimagemodif).fadeIn(200) ; 
		}) ; 
	}
}
function modification_nom_plat_reussis(idimagemodif, idimage_final)
{
	document.getElementById('nomplat' + idimagemodif).innerHTML = document.getElementById('input_modif_nom' + idimagemodif).value ;
	document.getElementById('image' + idimagemodif).href = '../' + idimage_final + '.html' ;

	// Changement de la fonctionde modif pour changer le nouvelle idimage
	document.getElementById('bloc_modif_nom' + idimagemodif).onsubmit = function(){
		modification_nom_plat(idimagemodif , '' + idimage_final + ''  ); 
		return false;
	}
	// Récupération du nouvelle idimage
	document.getElementById('input_hidden_idimage' + idimagemodif).value = tableau_json['idimage_final'];
}
function affichage_champ_reduction()
{
	// Si une réduction est déjà initié on ferme le champ
	if(document.getElementById('bloc_input_reduction').style.display != 'none')
	{
		$('#bloc_input_reduction').fadeOut(300) ; 
	}
	else
	{
		$('#bloc_input_reduction').fadeIn(300) ; 
	}
}
function faire_apparaitre_ajout_reduction()
{
	if(document.getElementById('formulaire_ajout_reduction').style.display == 'none')
	{
		voir_bloc_coulissant('#formulaire_ajout_reduction' , function()
		{
			document.getElementById('bloc_ajouter_une_reduction').innerHTML = 'Annuler l\'ajout' ;
		}) ; 
	}
	else
	{
		cache_bloc_coulissant('#formulaire_ajout_reduction' , function()
		{
			document.getElementById('bloc_ajouter_une_reduction').innerHTML = 'Ajouter une réduction' ;
		}) ; 
	}
}
function ajouter_reduction_compte(type) 
{
	chargement_bloc_action() ; 
	
	xhr = creation_xhr() ; 
	
	if(typeof(type) != 'undefined')
	{
		if(type == 'pendant-commande')
		{
			var intitule_reduction = document.getElementById('champ_input_reduction').value, 
			token = document.getElementById('token_ajout_de_plat').value ; 
		}
	}
	else
	{
		var intitule_reduction = document.getElementById('intitule_reduction').value, 
		token = document.getElementById('token_modif_plat').value ; 
	}
	
	// Si on appel la page c'est que l'on valide le changement donc c'est ok
	xhr.open('POST', '/ajax/compte_resto/ajout_modif_reduction.php');
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send('intitule_reduction=' + intitule_reduction + '&token=' + token); 
	
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && xhr.status == 200) 
		{
			chargement_bloc_action() ; 
			
			var tableau_reduction = JSON.parse(xhr.responseText) ; 
			
			// Récupération de l'id_reduction en JSON
			var id_reduction = tableau_reduction['id_reduction'] ; 
			
			// Si la réponse est positive et qu'un id à bien été passé 
			if(tableau_reduction['erreur'] == 0)
			{
				if(typeof(type) != 'undefined')
				{
					if(type == 'pendant-commande')
					{
						// Ajout de l'option
						var option_reduction_ajout = document.createElement("option");
						option_reduction_ajout.text = intitule_reduction;
						option_reduction_ajout.value = id_reduction;
						option_reduction_ajout.selected = true ;
						option_reduction_ajout.className = 'option_reduction_associe' ;  
						
						var select_cible = document.getElementById('reduction_associe');
						
						select_cible.add(option_reduction_ajout);
						
						
						// A la fin on remet en mode avec l'option apparente
						switch_associe_nouvelle_reduction() ; 
						
						// On simule un changement de plat pour faire apparaitre le petit button valide
						changement_reduction_plat() ; 
					}
				}
				else
				{
					// Si il n'y a aucune erreur on affiche simplement un message comme quoi ca a fonctionne
					var div = document.createElement('div') ; 
					
					div.className = 'bloc_reduction_global' ;
					div.id = 'bloc_reduction'+ id_reduction ;
					div.innerHTML = '<div class="bloc_reduction"><p class="texte_bloc_reduction"><textarea onchange="modification_reduction(' + id_reduction + ');" rows="7" cols="20" id="textarea_texte_bloc_reduction' + id_reduction + '" style="color:white" class="textarea_non_resize reset_input" type="text">' + intitule_reduction + '</textarea><input type="hidden" id="recup_value_reduction' + id_reduction + '" /></p><p style="display:inline-block"><img id="crayon_reduction' + id_reduction + '" onclick="focus_textarea_reduction(' + id_reduction + ');" style="cursor:pointer" src="../imgs/picto-crayon.png" alt="picto crayon" /><br /><br /><img id="supp_reduction' + id_reduction + '" onclick="supp_reduction(' + id_reduction + ');" style="cursor:pointer" src="../imgs/picto-supp.png" alt="picto supprimer" /></p></div>' ; 
					
					var ensemble_reduction = document.getElementById('ensemble_reduction') ;
					
					// On remet dans son état initial le formulaire
					faire_apparaitre_ajout_reduction() ; 
					
					// Si il n 'y avais aucune réduction
					if(document.getElementById('bloc_aucune_reduction'))
					{
						$('#bloc_aucune_reduction').fadeOut(200 , function(){
							ensemble_reduction.appendChild(div) ;
						});
					}
					else
					{
						// On met la div créer réduction dans l'ensemble des réductions
						ensemble_reduction.appendChild(div) ; 	
					}
					
					// On scroll jusqu'à la dite réduction
					$('#bloc_gestion_reduction').scrollTo('#' + div.id , 1000 , {queue:true}) ; 
					
					// Création de l'option a ajouter au select
					
					// On rajoute aux option le noeu pour pouvoir etre selectionné
					var select_tableau_reduction = document.getElementsByName('reduction_associe') ; 
					
					// On compte le nombre d'entrée dans le tableau
					var taille_tableau = select_tableau_reduction.length ; 
					// On boucle pour les prendres 1 par 1
					for(i=0 ; i < taille_tableau ; i++)
					{
						// On récupère l'id du select pour pouvoir le mettre directement dedans ( semble pas fonctionner sinon à voir.. ) 
						var id_select_reduction = select_tableau_reduction[i].id ; 
						
						var select_cible = document.getElementById(id_select_reduction);
						
						var option_reduction_ajout = document.createElement("option");
						option_reduction_ajout.text = intitule_reduction;
						option_reduction_ajout.value = id_reduction;
						
						// Ajout de l'option
						select_cible.add(option_reduction_ajout);
					}
				}
			}
			else
			{
				ouverture_alert(alert_basic = tableau_reduction['erreur']) ;
			}
		}
	};
}

function focus_textarea_reduction(id_reduction)
{
	var textarea_reduction = document.getElementById('textarea_texte_bloc_reduction' + id_reduction) ; 
	var crayon_reduction = document.getElementById('crayon_reduction' + id_reduction) ; 
	var supp_reduction = document.getElementById('supp_reduction' + id_reduction) ; 

	// Si le contenu du textarea est en italic
	if(textarea_reduction.style.fontStyle == 'italic')
	{
		// Permet de faire perdre le focus et activer le onchange du textarea
		$(textarea_reduction).prop('readonly', true);
		textarea_reduction.blur() ; 
		textarea_reduction.style.fontStyle = 'normal' ;
		crayon_reduction.src= "../imgs/picto-crayon.png" ;
		crayon_reduction.onclick = function(){
			focus_textarea_reduction(id_reduction);
		} ; 
		
		// On remet le bouton supp
		supp_reduction.style.display = 'inline' ;  
	}
	else
	{
		$(textarea_reduction).prop('readonly', false);
		textarea_reduction.select() ;
		textarea_reduction.style.fontStyle = 'italic' ;
		crayon_reduction.src="../imgs/picto-crayon-fermer.png" ;
		crayon_reduction.onclick = function(){
			focus_textarea_reduction(id_reduction);
			modification_reduction(id_reduction);
		} ;
		
		// On enleve le bouton supp pour pas qu'il y est de probleme
		supp_reduction.style.display = 'none' ;  
		
		// Récupération du contenu ancien de l'input pour pouvoir le restaurer en cas d'erreur
		document.getElementById('recup_value_reduction' + id_reduction).value = textarea_reduction.value ; 
	}
}
function modification_reduction(id_reduction)
{
	chargement_bloc_action() ; 
	
	xhr = creation_xhr() ; 
	
	var  intitule_reduction = document.getElementById('textarea_texte_bloc_reduction' + id_reduction).value, 
	token = document.getElementById('token_modif_plat').value ; 
	
	// Si on appel la page c'est que l'on valide le changement donc c'est ok
	xhr.open('POST', '/ajax/compte_resto/ajout_modif_reduction.php');
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send('intitule_reduction=' + intitule_reduction + '&id_modif_reduction=' + id_reduction + '&token=' + token); 
	
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4 && xhr.status == 200) 
		{
			chargement_bloc_action() ; 
			
			var tableau_reduction = JSON.parse(xhr.responseText) ; 
			// Si la réponse est positive et qu'un id à bien été passé 
			if(tableau_reduction['erreur'] == 0)
			{
				// Si tout c'est bien passé on modifie tout les éléments <option> qui ont en value la réduction
				var option_select = document.getElementsByClassName('option_reduction_associe') ; 
				
				// On compte le nombre d'entrée dans le tableau
				var taille_tableau = option_select.length ; 
				// On boucle pour les prendres 1 par 1
				for(i=0 ; i < taille_tableau ; i++)
				{
					// Si c'est le meme id alors on modifie la valeur innerHTML
					if(option_select[i].value == id_reduction)
					{
						option_select[i].innerHTML =  intitule_reduction; 
					}
				}
			}
			else
			{
				ouverture_alert(alert_basic = tableau_reduction['erreur']) ; 
				// Restauration de la valeur de base vue qu'il y a une erreur
				document.getElementById('textarea_texte_bloc_reduction' + id_reduction).value = document.getElementById('recup_value_reduction' + id_reduction).value ; 
			}
		}
	};
}
function supp_reduction(id_reduction)
{
	xhr = creation_xhr() ; 
	
	if(typeof(choix) != 'undefined' && choix  != '')
	{
		if(choix  == 'ok')
		{
			var token = document.getElementById('token_modif_plat').value ; 
			
			chargement_bloc_action() ; 
			
			// Si on appel la page c'est que l'on valide le changement donc c'est ok
			xhr.open('POST', '/ajax/compte_resto/supprimer_reduction.php');
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhr.send('id_supp_reduction=' + id_reduction + '&token=' + token ); 
			
			xhr.onreadystatechange = function() 
			{
				if (xhr.readyState == 4 && xhr.status == 200) 
				{
					chargement_bloc_action() ; 
					
					$('#bloc_reduction' + id_reduction).fadeOut(200) ; 
					
					// On supprime les noeux des options
					var option_select = document.getElementsByClassName('option_reduction_associe') ; 
					
					// On compte le nombre d'entrée dans le tableau
					var taille_tableau = option_select.length ; 
					// On boucle pour les prendres 1 par 1
					for(i=0 ; i < taille_tableau ; i++)
					{
						// Si c'est le meme id alors on supprime le noeu
						if(option_select[i])
						{
							if(option_select[i].value == id_reduction)
							{
								option_select[i].parentNode.removeChild(option_select[i]) ; 
							}
						}
					}
				}
			};
		}
		
		choix = '' ; 
	}
	else
	{
		fonction_a_appeler = supp_reduction ; 
		argument_fonction = id_reduction ; 
	
		ouverture_alert(confirm_action = 'Voulez-vous vraiment supprimer cette réduction de votre compte ainsi que de tous les plats où elle est associée ?' ) 
	}
}
function associer_reduction_plat(id_plat)
{
	// Récupération de la valeur du select 
	var id_reduction = document.getElementById('select_reduction' + id_plat).value,
	token = document.getElementById('token_modif_plat').value ; 
	
	chargement_bloc_action() ; 
	
	xhr = creation_xhr() ; 
	
	// Si on appel la page c'est que l'on valide le changement donc c'est ok
	xhr.open('POST', '/ajax/compte_resto/associer_reduction_plat.php');
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send('id_plat=' + id_plat + '&id_reduction=' + id_reduction + '&token=' + token ); 
	
	xhr.onreadystatechange = function() 
	{
		chargement_bloc_action() ; 

		if(xhr.readyState == 4 && xhr.status == 200) 
		{
			var texte = xhr.responseText ;
			
			if(texte != '')
			{
				ouverture_alert(alert_basic = texte) ;
			}
			else
			{
				changement_reduction_plat(id_plat) ;
			}
		}
	};
}
function changement_reduction_plat(id_plat)
{
	// Si c'est une modification d'un plat déjà existant
	if(typeof(id_plat) != 'undefined')
	{
		var ok_changement_reduction = document.getElementById('ok_changement_reduction' + id_plat) ;
	}
	// Si c'est à l'ajout de plat
	else
	{
		var ok_changement_reduction = document.getElementById('ok_changement_reduction') ;
	}

	// Faire apparaitre le picto de validation
	$(ok_changement_reduction).fadeIn() ; 
	// Le faire disparaitre au bout d'un certain temps
	setTimeout(function(){$(ok_changement_reduction).fadeOut() ; }, 1000) ; 
}
function switch_associe_nouvelle_reduction()
{
	if(document.getElementById('bloc_champ_input_reduction').style.display == 'none')
	{
		$('#bloc_reduction_associe').fadeOut(300 ,function()
		{
			$('#bloc_champ_input_reduction').fadeIn(300);
		}) ; 
	}
	else
	{
		$('#bloc_champ_input_reduction').fadeOut(300 ,function()
		{
			$('#bloc_reduction_associe').fadeIn(300);
		}) ; 
	}
}
function faire_apparaitre_offre_special(id_offre)
{
	if(document.getElementById('offre_special_' + id_offre).style.display == 'none')
	{
		$('#clique_voir_offre_special').fadeOut(200 , function(){
			$('#offre_special_' + id_offre).fadeIn(200) ;
		}) ;
	}
	else
	{
		$('#offre_special_' + id_offre).fadeOut(200 , function(){
			$('#clique_voir_offre_special').fadeIn(200) ; 
		}) ; 
	}
}
function remettreEnLignePlat(idimage , nomplat)
{
	// On lance le chargement
	chargement_bloc_action() ;
	
	xhr = creation_xhr() ; 
	
	// Si on appel la page c'est que l'on valide le changement donc c'est ok
	xhr.open('POST', '/ajax/compte_resto/ajout_image.php');
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	
	// C'est forcement de type ajout-de-plat
	xhr.send('idimage=' + idimage + '&nomplat=' + nomplat + '&envoi_multiple=ajout-de-plat&remettre_en_ligne=1'); 
	
	xhr.onreadystatechange = function() 
	{
		if(xhr.readyState == 4 && xhr.status == 200)
		{
			// On referme le chargement
			chargement_bloc_action() ;
			
			var tableau_json = JSON.parse(xhr.responseText) ;
			
			if(tableau_json['erreur'] == 0)
			{
				ouverture_alert(alert_basic = '<span>Ce plat a bien été ajouté à la catégorie ajouter vos plats, accéder à cette page afin de remettre votre plat en ligne rapidement.</span><br />') ;
			}
			else
			{
				ouverture_alert(alert_basic = tableau_json['erreur']) ;
			}
		}
	};
}

function annuler_abonnement_plat(id_abonnement)
{
	xhr = creation_xhr() ; 
	
	if(typeof(choix) != 'undefined' && choix  != '')
	{
		if(choix  == 'ok')
		{			
			var token = document.getElementById('token_supp_abonnement').value ; 
					
			// Si on appel la page c'est que l'on valide le changement donc c'est ok
			xhr.open('POST', '/ajax/commandes/annuler_abonnement.php');
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhr.send('id_abonnement=' + argument_fonction + '&token=' + token ); 
			
			xhr.onreadystatechange = function() 
			{
				if (xhr.readyState == 4 && xhr.status == 200) 
				{
					if(xhr.responseText == '' )
					{
						chargement_bloc_action() ; 
						
						setTimeout(function()
						{
							chargement_bloc_action() ; 
							
							ouverture_alert(alert_basic = 'Vous allez recevoir un email dès que notre équipe technique aura suspendu l\'abonnement dans les prochaines 24h.' ) ;
							
							var statut_facture_id_commande = document.getElementById('statut_facture' + argument_fonction) ; 
							
							
							statut_facture_id_commande.style.color = 'orange' ; 
							statut_facture_id_commande.innerHTML = '- annulation en cours' ; 
							
							// On enleve le bloc annuler commande
							document.getElementById('bloc_button_annuler_commande' + argument_fonction).style.display = 'none' ; 
							
						} , 1000) ;
					}
					else
					{
						chargement_bloc_action() ; 
						
						// On temporise car on peut pas en mettre 2 d'affiler
						setTimeout(function()
						{
							ouverture_alert(alert_basic = xhr.responseText) ;
						} , 1000) ; 
					}
				}
			};
		}
		
		choix = '' ; 
	}
	else
	{
		fonction_a_appeler = annuler_abonnement_plat ; 
		argument_fonction = id_abonnement ; 
	
		ouverture_alert(confirm_action = 'Voulez-vous vraiment annuler cet abonnement ? Tous les plats associés à cette commande seront alors mis hors ligne. Vous pouvez nous contacter si vous rencontrez un problème.' ) ; 
	}
}