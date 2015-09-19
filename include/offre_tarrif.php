<?php

// INITIALISATION des differents prix des offres --PLAT--
$offre_formule = array(
'0' => 0,
'1' => 10,
'2' => 50,
'3' => 65,
'abonnement' => 5,
'facebook' => 1 ,
'news' => 2,
'reduction' => 0
) ;

$courte_description_options = array(
	'facebook' => 'Promotion de l\'image mise en ligne sur Facebook' ,
	'news' => 'Promotion de l\'image mise en ligne sur DMF',
	'reduction' => 'Mise en place d\'une réduction utilisateur'
);

// INITIALISATION des differents prix des offres visiteurs
$offre_formule_visiteur = array(
'0' => 0,
'1' => 3.49,
'2' => 14.49,
'3' => 20.95, 
) ; 

// INITIALISATION des different temps d'abonnement général
$temps_abonnement_formule = array(
'1' => 1,
'2' => 6,
'3' => 12
);

//taille du tableau abonnement
$nombre_abonnement_formule = count($temps_abonnement_formule) ; 