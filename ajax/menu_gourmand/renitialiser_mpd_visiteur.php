<?php
include ('../../include/configcache.php') ;
include ('../../include/fonctions.php') ;

if(!empty($_POST['lien_unique_renitialisation']))
{
	// Si le mot de passe existe bien
	if(!empty($_POST['nouveau_mdp_visiteur']))
	{
		if(strlen($_POST['nouveau_mdp_visiteur']) > 5)
		{
			$nouveau_mdp = securise_mdp($_POST['nouveau_mdp_visiteur']) ;
			
			// Cherchons le compte qui à appelé ce lien unique et changons la variable mdp par la nouvelle
			$requete_user = $bdd->prepare('UPDATE utilisateur SET ancien_mdp = mdp, mdp = :mdp_nouveau WHERE ancien_mdp = :lien_unique') ; 
			$requete_user->execute(array(':lien_unique' => $_POST['lien_unique_renitialisation'] , ':mdp_nouveau' => $nouveau_mdp)) ;
			
			// On test que ca est bien fonctionné
			if($requete_user)
			{}
			else
			{
				echo '<span>Lien de réinitialisation de mot de passe érroné. Si le problème persiste, contactez-nous.</span><br />' ; 
			}
		}
		else
		{
			echo '<span>Mot de passe trop court</span><br />' ; 
		}
	}
	else
	{
		echo '<span>Nouveau mot de passe manquant</span><br />' ; 
	}
}
else
{
	echo '<span>Lien unique de réinitialisation manquant, veuillez nous contacter en cas de problème.</span><br />' ; 
}