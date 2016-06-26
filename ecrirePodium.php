<?php
//rem - deb - 09.05.08
require_once "common.php";

$equipe1 = get_post_arg('equipe1');
$equipe2 = get_post_arg('equipe2');
$equipe3 = 'n/a';
//$equipe3 = get_post_arg('equipe3');

/* remplit la table classement_final */
remplitClassementFinal($equipe1, $equipe2, $equipe3);

/* maj du classement */
remplitClassementAvecPodium();

require_once "administration.php";

function remplitClassementFinal($equipe1, $equipe2, $equipe3) {
	
	require_once 'common.php';
	
	if (verif_classement_final()){
		$query = "insert into classement_final (equipe1, equipe2, equipe3) values ('" . $equipe1 . "', '" . $equipe2 . "', '" . $equipe3 . "')";
	} else {
		$query = "update classement_final set equipe1='" . $equipe1 . "', equipe2='" . $equipe2 . "', equipe3='" . $equipe3 . "'";
	}
	$link = get_mysql_link();
	$result = mysql_query($query, $link);
	if (!$result) my_error($link, $query);
	
}


function verif_classement_final() {
	$query = "select count(*) from classement_final where equipe1 is not null";
	$link = get_mysql_link();
	$result = mysql_query($query, $link);
	if (!$result) my_error($link, $query);
	$row = mysql_fetch_array($result, MYSQL_NUM);
	if ($row[0] == 0)
		return true;
	else
		return false;
}


/* Remplit le classement avec les points favoris */
function remplitClassementAvecPodium() {

	global $pointVainqueurExact;
	global $pointFinalisteExact;
	global $pointTroisiemeExact;
	global $pointTrioInexact;
	
	$link = get_mysql_link();
	
	$queryC = "SELECT * FROM `classement_final` c";
	$result = mysql_query($queryC, $link);
	if (!$result) my_error($link, $queryC);

	$row = mysql_fetch_array($result, MYSQL_NUM);
	$vainqueur = $row[0];
	$finaliste = $row[1];
	$troisieme = $row[2];

	$queryP = "SELECT * FROM `prono_classement_final` p";
	$result = mysql_query($queryP, $link);
	if (!$result) my_error($link, $queryP);
	
	while ($row2 = mysql_fetch_array($result, MYSQL_NUM)) {
		$login = $row2[0];
		$vainqueurFavori = $row2[1];
		$finalisteFavori = $row2[2];
		$troisiemeFavori = $row2[3];

		//calcul des points
		$points = 0;
		if ($vainqueur==$vainqueurFavori) $points = $points + $pointVainqueurExact;
		if ($finaliste==$finalisteFavori) $points = $points + $pointFinalisteExact;
		if ($troisieme==$troisiemeFavori) $points = $points + $pointTroisiemeExact;
		if ($vainqueur==$finalisteFavori OR $vainqueur==$troisiemeFavori) $points = $points + $pointTrioInexact;
		if ($finaliste==$vainqueurFavori OR $finaliste==$troisiemeFavori) $points = $points + $pointTrioInexact;
		if ($troisieme==$vainqueurFavori OR $troisieme==$finalisteFavori) $points = $points + $pointTrioInexact;

		//maj classement
		$update = "update classement c set points=points-Points_classement_final WHERE c.login='" . $login . "'";
		$updateclassement = mysql_query($update, $link);
		$update = "update classement c set points=points+" . $points . ", Points_classement_final='" . $points . "' WHERE c.login='" . $login . "'";
		$updateclassement = mysql_query($update, $link);
		if (!$updateclassement) my_error($link, $update);
	}
	$query = "select login, place from `classement` order by points DESC , sens DESC , exact DESC , points_matchs DESC";
	$result = mysql_query($query, $link);	
	if (!$result)
		my_error($link, $query);
	$i = 1;
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$progression = $row[1] - $i;
		if ($progression > 0) {
			$progression = "+" . $progression;
		}
		elseif ($progression == 0) {
			$progression = "=";
		}
		$query = "update `classement` set place='".$i."', progression='".$progression."' where login='".$row[0]."'";
		$result2 = mysql_query($query, $link);
		if (!$result2)
			my_error($link, $query);
		$i++;
	}
	remplitClassementEquipe();
}

//rem - fin - 09.05.08
?>