<?php

require_once "common.php";

$idmatch = get_post_arg('idmatch');
$score1 = get_post_arg('score1a');
$score2 = get_post_arg('score2a');

/* remplit la table match */
remplitMatch($idmatch, $score1, $score2);

/* calcul la cagnotte sans prendre en compte les paris de Monsieur défaut*/
$cagnotte = calculCagnotte ($idmatch);

// Nico 18/05
/* Calcul le score de monsieur defaut et remplit les paris vide*/
remplitDefautAndThenPariVide($idmatch, "MonsieurDefaut");

/* remplit la table pari */
remplitPari($idmatch, $score1, $score2);

/* remplit les points */
$ecart = intval($score1)-intval($score2);
remplitPoint($idmatch, $cagnotte,$ecart);

/* remplit le classement */
remplitClassement($idmatch);

/* remplitClassementEquipe*/
//remplitClassementEquipe();

// Nico 18/05 
require_once "administration.php";

function remplitDefautAndThenPariVide($idmatch, $login) {
	$score1 = rand(0,4);
	if ($score1>2) {
		$score1 = rand(0,4);
	}
	$score2 = rand(0,4);
	if ($score2>2) {
		$score2 = rand(0,4);
	}
	$ecart = intval($score1)-intval($score2);
	$result = executerRequete("SELECT `login` FROM `joueurs` WHERE `login`  NOT in(SELECT `login` FROM pari WHERE idmatch = '".$idmatch."')");
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$joueur = $row[0];
		if (verif_pari($joueur,$idmatch)==true){
			$query = "insert into pari (login,idmatch,score1,score2,joker,ecart) values ('".$joueur."','".$idmatch."','".$score1."','".$score2."','0','".$ecart."')";
			$result2 = 	executerRequete($query);
		} 
	}

}

// Fin Nico 18/05
?>