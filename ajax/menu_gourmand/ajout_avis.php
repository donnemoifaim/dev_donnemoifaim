<?php

session_start();

//connection à la bdd
include('../../include/configcache.php');
include('../../include/fonctions.php');

if(!empty($_SESSION['login_visiteur']))
{
	if(!empty($_SESSION['token_visiteur']) && !empty($_POST['token_visiteur']) && $_POST['token_visiteur'] == $_SESSION['token_visiteur'])
	{
		if(!empty($_POST['note_avis']))
		{
			if(!empty($_POST['avis_utilisateur']))
			{
				// Permet de supprimer les espaces blanc en debut et fin de chaine // Rtrim supprime les caractère blanc de fin de chaine
				$avis_utilisateur = rtrim(trim($_POST['avis_utilisateur'])) ;
				// Si l'utilisateur à déjà voté et rédigé un avis
				$requete_avis_utilisateur = $bdd->prepare('SELECT id FROM avis_utilisateur WHERE login = :login && id_resto = :id_resto');
				$requete_avis_utilisateur->execute(array(':login' => $_SESSION['login_visiteur'] ,  ':id_resto' => $_SESSION['id_resto_actuel']));
				
				// Si l'utilisaeur à déjà poster c'est donc une modification
				if($donnees_avis_utilisateur = $requete_avis_utilisateur->fetch())
				{
					$requete_modif_avis = $bdd->prepare('UPDATE avis_utilisateur SET contenu_avis = :contenu_avis, date_ajout = :date_ajout, note =:note WHERE login = :login_visiteur && id_resto = :id_resto');
					$requete_modif_avis->execute(array(':login_visiteur' => $_SESSION['login_visiteur'], ':contenu_avis' => $avis_utilisateur , ':id_resto' => $_SESSION['id_resto_actuel'] , ':note' => $_POST['note_avis'] ,  ':date_ajout' => time()));
					
					$requete_modif_avis->closeCursor(); // Termine le traitement de la requête 
					
					$id_avis = $donnees_avis_utilisateur['id'] ; 
					
					$contenu_avis = $avis_utilisateur ; 
					
					$type = 'modif_avis' ;
				}
				else
				{
					$requete_insert_avis = $bdd->prepare('INSERT INTO avis_utilisateur(contenu_avis, login,note, id_resto, date_ajout) VALUES( :contenu_avis, :login_visiteur,:note, :id_resto, :date_ajout)');
					$requete_insert_avis->execute(array(':login_visiteur' => $_SESSION['login_visiteur'], ':contenu_avis' => $avis_utilisateur , ':note' => $_POST['note_avis'] , ':id_resto' => $_SESSION['id_resto_actuel'] , ':date_ajout' => time()));
					
					$requete_insert_avis->closeCursor(); // Termine le traitement de la requête
					
					$id_avis = $bdd->lastInsertId(); 
					
					$contenu_avis = $avis_utilisateur ; 
					
					$type = 'ajout_avis' ;
					
					// Ajout de point compte visiteur
					if(!empty($_SESSION['login_visiteur']))
					{
						include ('../../include/points-dmf.php');
						ajouter_point_utilisateur($point_dmf_visiteur['redaction_avis']) ; 
					}
				}
			}
			else
			{
				$erreur = '<span>Champ avis manquant</span><br />' ; 
			}
		}
		else
		{
			$erreur = '<span>Note avis manquant</span><br />' ; 
		}
	}
	else
	{
		$erreur = '<span class="erreur" >'.$erreur_faille_csrf.'</span><br />' ;
	}
}
if(!isset($erreur))
{
	$erreur = 0 ; 
}

echo json_encode(array('id_avis' => $id_avis , 'erreur' => $erreur, 'contenu_avis' => $contenu_avis, 'type' => $type )) ; 

