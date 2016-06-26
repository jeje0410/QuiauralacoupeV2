<?php
require_once 'banniere.php';
echo "<br> <h3>Records</h3>";
afficheEquipeJoueur($login);

function afficheEquipeJoueur($login) {
	require_once 'common.php';
	$link = get_mysql_link();
	$query = "SELECT j.Nom, j.Prenom, max( p.points_total ) AS maxi,j.login FROM pari p, `joueurs` j WHERE j.login = p.login GROUP BY j.Login having maxi>=15 ORDER BY maxi DESC";
	$result = mysql_query($query, $link);
	echo "<table class=\"tableau\">";
	echo "<tr class=\"beige\"><td colspan=3>Gain maximum sur un match</td></tr>";
	echo "<tr class=\"vertclair\"><td width=\"33%\">Points</td><td width=\"33%\">Joueurs</td><td width=\"34%\">Match</td></tr>";
	$i=0;
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$queryMax = "SELECT m.lib_match FROM `match` m, `pari` p WHERE p.idmatch = m.idmatch AND p.points_total = ".$row[2]." AND p.login = '".$row[3]."'";
		$resultMax = mysql_query($queryMax, $link);
		while ($rowMax = mysql_fetch_array($resultMax, MYSQL_NUM)) {
			$lib = $rowMax[0];
		}
		if($i%2==0) {
			echo "<tr class=\"vertfonce\"><td width=\"33%\">".$row[2]."</td><td width=\"33%\">".$row[1]." ".$row[0]."</td><td width=\"34%\">".$lib."</td></tr>";
		} else { 
			echo "<tr class=\"vertclair\"><td width=\"33%\">".$row[2]."</td><td width=\"33%\">".$row[1]." ".$row[0]."</td><td width=\"34%\">".$lib."</td></tr>";
		}
		$i++;
	}
	echo "</table>";
	echo "<br>";	
	//Bloque pour gérer les meilleurs et les moins bon au sens du match
	$query = "select j.Nom, j.Prenom from classement c, `joueurs` j where j.login = c.login and c.sens = (select max(sens) from classement)";
	$queryListeMin = "select j.Nom, j.Prenom from classement c, `joueurs` j where j.login = c.login and c.sens = (select min(sens) from classement)";
	$querymax = "select max(sens) from classement";
	$querymin = "select min(sens) from classement";
	$result = mysql_query($querymax, $link);
	if (!$result)
		my_error($link, $query);
	$row = mysql_fetch_array($result, MYSQL_NUM);
	$max = 	$row[0];
	$result = mysql_query($querymin, $link);
	if (!$result)
		my_error($link, $query);
	$row = mysql_fetch_array($result, MYSQL_NUM);
	$min = 	$row[0];
	$result = mysql_query($query, $link);
	echo "<table class=\"tableau\">";
	echo "<tr class=\"beige\"><td colspan=2>Record pour le sens du match</td></tr>";
	echo "<tr class=\"vertclair\"><td width=\"33%\">Nb</td><td width=\"33%\">Joueurs</td></tr>";
	echo "<tr class=\"vertfonce\"><td colspan=3>Les +</td></tr>";
	$i=0;
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		if($i%2==0) {
		echo "<tr class=\"vertclair\"><td width=\"33%\">".$max."</td><td width=\"33%\">".$row[1]." ".$row[0]."</td></tr>";
		} else { echo "<tr class=\"vertfonce\"><td width=\"33%\">".$max."</td><td width=\"33%\">".$row[1]." ".$row[0]."</td></tr>";}
		$i++;
	}
	echo "<tr class=\"vertfonce\"><td colspan=3>Les -</td></tr>";
	$i=0;
	$result = mysql_query($queryListeMin, $link);
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		if($i%2==0) {
		echo "<tr class=\"vertclair\"><td width=\"33%\">".$min."</td><td width=\"33%\">".$row[1]." ".$row[0]."</td></tr>";
		} else { echo "<tr class=\"vertfonce\"><td width=\"33%\">".$min."</td><td width=\"33%\">".$row[1]." ".$row[0]."</td></tr>";}
		$i++;
	}
	echo "</table>";
	echo "<br>";	
	//Bloque pour gérer les meilleurs et les moins bon au résulat exact du match
	$query = "select j.Nom, j.Prenom from classement c, `joueurs` j where j.login = c.login and c.exact = (select max(exact) from classement)";
	$queryListeMin = "select j.Nom, j.Prenom from classement c, `joueurs` j where j.login = c.login and c.exact = (select min(exact) from classement)";
	$querymax = "select max(exact) from classement";
	$querymin = "select min(exact) from classement";
	$result = mysql_query($querymax, $link);
	if (!$result)
		my_error($link, $query);
	$row = mysql_fetch_array($result, MYSQL_NUM);
	$max = 	$row[0];
	$result = mysql_query($querymin, $link);
	if (!$result)
		my_error($link, $query);
	$row = mysql_fetch_array($result, MYSQL_NUM);
	$min = 	$row[0];
	$result = mysql_query($query, $link);
	echo "<table class=\"tableau\">";
	echo "<tr class=\"beige\"><td colspan=2>Record pour le nombre de r&eacute;sultat exact</td></tr>";
	echo "<tr class=\"vertclair\"><td width=\"33%\">Nb</td><td width=\"33%\">Joueurs</td></tr>";
	echo "<tr class=\"vertfonce\"><td colspan=3>Les +</td></tr>";
	$i=0;
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		if($i%2==0) {
		echo "<tr class=\"vertclair\"><td width=\"33%\">".$max."</td><td width=\"33%\">".$row[1]." ".$row[0]."</td></tr>";
		} else { echo "<tr class=\"vertfonce\"><td width=\"33%\">".$max."</td><td width=\"33%\">".$row[1]." ".$row[0]."</td></tr>";}
		$i++;
	}
	echo "<tr class=\"vertfonce\"><td colspan=3>Les -</td></tr>";
	$i=0;
	$result = mysql_query($queryListeMin, $link);
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		if($i%2==0) {
		echo "<tr class=\"vertclair\"><td width=\"33%\">".$min."</td><td width=\"33%\">".$row[1]." ".$row[0]."</td></tr>";
		} else { echo "<tr class=\"vertfonce\"><td width=\"33%\">".$min."</td><td width=\"33%\">".$row[1]." ".$row[0]."</td></tr>";}
		$i++;
	}
	echo "</table>";
	echo "<br>";
	
	//Bloque pour gérer les meilleurs  en point buteur
	$query = "SELECT j.Nom, j.Prenom, sum( p.points_buteur ) AS maxi,j.login FROM pari p, `joueurs` j WHERE j.login = p.login GROUP BY j.Login having maxi>=5 ORDER BY maxi DESC";
	$result = mysql_query($query, $link);
	echo "<table class=\"tableau\">";
	echo "<tr class=\"beige\"><td colspan=2>Record pour le nombre de points buteurs</td></tr>";
	echo "<tr class=\"vertfonce\"><td width=\"33%\">Points</td><td width=\"33%\">Joueurs</td></tr>";
	$i=0;
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		if($i%2==0) {
		echo "<tr class=\"vertclair\"><td width=\"33%\">".$row[2]."</td><td width=\"33%\">".$row[1]." ".$row[0]."</td></tr>";
		} else { echo "<tr class=\"vertfonce\"><td width=\"33%\">".$row[2]."</td><td width=\"33%\">".$row[1]." ".$row[0]."</td></tr>";}
		$i++;
	}
	echo "</table>";
	echo "<br>";
}
require_once 'footer.php';
?>