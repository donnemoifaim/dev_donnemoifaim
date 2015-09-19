<?php

session_start();

include ('../../include/configcache.php') ;
include ('../../include/fonctions.php') ;

$id_resto = $_SESSION['id_resto_actuel'] ; 

// Récupération de la note totale de ce restaurant
$note_avis_resto = recup_note_resto($id_resto, 'affichage') ; 

$note_finale = $note_avis_resto['note_finale'] ;
$nombre_avis_vote = $note_avis_resto['nombre_vote'] ;

$affichage_etoile_avis = affichage_etoile_avis($note_finale, 'affichage') ; 

$tableau_json = array('affichage_etoile_avis' => $affichage_etoile_avis , 'note_finale' => $note_finale,  'nombre_avis_vote' => $nombre_avis_vote ) ; 

echo json_encode($tableau_json); 

?>