<?php
require_once 'common.php';
require_once 'session.php';

$prenom = get_post_arg('prenom');
$nom = get_post_arg('nom');
$id = get_post_arg('id');
$pass = get_post_arg('pass');

$query1 = "INSERT INTO `joueurs` (`Login`, `Password`, `Nom`, `Prenom`) VALUES ('".$id."', '".$pass."', '".$nom."', '".$prenom."')";
executerRequete($query1);
$query2 = "INSERT INTO `classement` (`login`, `points`, `points_matchs`, `points_buteurs`, `points_cagnotte`, `nb_match`, `sens`, `exact`, `place`, `progression`) VALUES ('".$id."', 0, 0, 0, 0, 0, 0, 0, 1, '=')";
executerRequete($query2);

header('location: administration.php' );

exit;

?>