<?php

session_start();

include ('../../include/configcache.php') ;
include ('../../include/fonctions.php') ;

$nombre_notification = compter_nombre_notification_compte_visiteur(); 

echo $nombre_notification ; 