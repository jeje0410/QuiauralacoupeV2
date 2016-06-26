<?php
require_once 'common.php';
require_once 'session.php';
require_once 'date.php';
$idmatch = get_post_arg('idmatch');
$score1 = get_post_arg('score1');
$score2 = get_post_arg('score2');
$joker = get_post_arg('joker');

/* Nico 16/05 Tester la validité du pari */
// Nico 31/05
$erreur = is_valid_pari($login,$idmatch,$joker);
if ($erreur=="ok") {
	$ecart=intval($score1)-intval($score2);
	if (verif_pari($login,$idmatch)==true){
		$query = "insert into pari (login,idmatch,score1,score2,joker,ecart) values ('".$login."','".$idmatch."','".$score1."','".$score2."','".$joker."','".$ecart."')";
	} else {
		$query = "update pari set score1='".$score1."', score2='".$score2."', joker='".$joker."',ecart='".$ecart."' where idmatch='".$idmatch."' and login='".$login."'";
	}
	$link = get_mysql_link();
	$result = mysql_query($query, $link);
	if (!$result) my_error($link, $query);	
	//  Nico 5/05 
	require_once "paris.php";
} 
else {
	require_once 'banniere.php';

	echo "<H2>".$erreur."</H2>";
	require_once 'matchajoue.php';

	require_once 'footer.php';
}


// Nico 31/05
function is_valid_pari($login,$idmatch, $joker){
	$res1 = verif_date($idmatch); 
	if ($res1) {
		return "Vous ne pouvez plus parier sur ce match, bien essay&eacute;...";
	}
	// Nico 31/05
	$res2 = verif_joker($login,$joker);
	if (!$res2){
		return "Vous ne pouvez utiliser que 3 jokers";
	}	
	return "ok";
}
function verif_date($idmatch){
	$query = "select dateM from `match` where idmatch='".$idmatch."'";
	$result = executerRequete($query);
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$date = $row[0];
	}
	return (todaySupDate($date));
}

function verif_joker($login,$joker) {
	require_once 'common.php';
	$query = "select count(*) from pari where login='".$login."' and joker='1'";
	$result = executerRequete($query);
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$nb = $row[0];
	}
	// Debut Nico 31/05
	if (($nb>=3)&&($joker==1)) {
		return false;
	}
	// fin Nico 31/05
	else {
		return true;
	}
}
?>