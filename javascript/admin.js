function valider_tache(id_unique, id_tache, type, statut)
{
	var token = document.getElementById('id_token_admin').value ; 
	
	var xhr = creation_xhr(); 
	
	xhr.open('GET' , '../ajax/admin/valider_tache.php?token=' + token + '&id_unique=' + id_unique + '&id_tache=' + id_tache + '&type=' + type + '&statut=' + statut) ;
	xhr.send() ; 
	
	xhr.onreadystatechange = function()
	{
		if (xhr.readyState == 4 && xhr.status == 200) 
		{	
			var response = xhr.responseText ; 
			
			if(response == '')
			{
				$('#bloc_tache' + id_tache).fadeOut() ; 
			}
			else
			{
				ouverture_alert(alert_basique = response);
			}
		}
	}
}

function showMoreDetailClient(idClient)
{
	if(document.getElementById('fiche_client_' + idClient).style.display == 'none')
	{
		$('#fiche_client_' + idClient).fadeIn(300) ;
		
		// Si on fait apparaitre on met un history dessus 
		history.pushState(null, null);
	}
	else
	{
		$('#fiche_client_' + idClient).fadeOut(300) ;
	}
}

function changerAbonnementPlat(id_plat , valueAbonnement)
{
	var token = document.getElementById('id_token_admin').value ; 
	
	var xhr = creation_xhr(); 
	
	xhr.open('GET' , '../ajax/admin/modifier_abonnement_plat.php?token=' + token + '&id_plat=' + id_plat + '&abo=' + valueAbonnement ) ;
	xhr.send() ; 
	
	xhr.onreadystatechange = function()
	{
		if (xhr.readyState == 4 && xhr.status == 200) 
		{	
			var response = xhr.responseText ;
			
			if(response == '')
			{
				var validation_changement_abonnement = document.getElementById('ok_changement_abonnement' + id_plat) ; 
				
				$(validation_changement_abonnement).fadeIn(200 , function()
				{
					// On fait disparraitre le display
					setTimeout(function(){$(validation_changement_abonnement).fadeOut(200) ; } , 1000);
				}) ;  
			}
			else
			{
				ouverture_alert(alert_basique = response);
			}
		}
	}	
	
}

// connexion admin sur les restos utilisateur sans posséder leurs mot de passe
function ConnexionRestoDieu(idResto)
{
	// Ajax pour allez créer les sessions utilisateur que l'on va utiliser
	var token = document.getElementById('id_token_admin').value ; 
	
	var xhr = creation_xhr(); 
	
	xhr.open('POST' , '../ajax/admin/connexion_resto_dieu.php') ;
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send('token=' + token + '&id_resto=' + idResto) ; 
	
	xhr.onreadystatechange = function()
	{
		if (xhr.readyState == 4 && xhr.status == 200) 
		{
			var response = xhr.responseText ; 
			
			if(response == '')
			{
				// On va directement sur le compte connexion car normalement y à pas de problème vu que toutes les sessions sont créés en ajax
				window.location.href = '../connection-compte.html' ;
			}
			else
			{
				ouverture_alert(alert_basique = response);
			}
		}
	}
}

function modifier_commercial_associe_commande(id_commande , id_tache , type)
{
	chargement_bloc_action() ; 
	
	var commercial_commande_choix = document.getElementById('select_commercial_commande_choix' + id_tache).value ;
	
	// Ajax pour allez créer les sessions utilisateur que l'on va utiliser
	var token = document.getElementById('id_token_admin').value ; 
	
	var xhr = creation_xhr(); 
	
	xhr.open('POST' , '../ajax/admin/attribution_commercial_commande.php') ;
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send('token=' + token + '&commercial_commande_choix=' + commercial_commande_choix + '&id_commande=' + id_commande) ;
	
	xhr.onreadystatechange = function()
	{
		if (xhr.readyState == 4 && xhr.status == 200) 
		{
			chargement_bloc_action() ; 
			
			if(xhr.responseText == '')
			{
				// On va mettre un ok chargement à coté pour dire que c'est op
				$('#ok_attribution_commercial_commande' + id_tache).fadeIn(300 , function()
				{
					// On l'enleve au bout de 1 seconde
					setTimeout(function()
					{
						$('#ok_attribution_commercial_commande' + id_tache).fadeOut(300) ;
					}, 1000) ;
				}) ; 
			}
			else
			{
				ouverture_alert(alert_basique = xhr.responseText);
			}
		}
	}
}