<?php
require_once 'common.php';
require_once 'session.php';

//date
$annee = get_post_arg('annee');
$mois = get_post_arg('mois');
$jours = get_post_arg('jours');
$heure = get_post_arg('heure');
$date = $annee."-".$mois."-".$jours." ".$heure;

//match
//$match = get_post_arg('match');
$equipe1 = get_post_arg('equipe1');
$equipe2 = get_post_arg('equipe2');
$match = $equipe1 ." - ". $equipe2;

//tour
$type = get_post_arg('type');



$query1 = "INSERT INTO `match` (`idMatch`, `lib_match`, `dateM`, `type`) VALUES ('auto_increment', '".$match."', '".$date."', '".$type."')";
executerRequete($query1);
header('location: administration.php' );
exit;

?>