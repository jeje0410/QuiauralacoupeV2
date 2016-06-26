<?php
$query="SELECT idMatch, lib_match, score1, score2, dateM FROM `match` m order by idMatch asc";
$query2="SELECT max(idMatch) FROM `match`";
afficheStatsEuro2008($query,$login,$query2);

//affiche Euro2008 - Récapitulatif
function afficheStatsEuro2008($query, $login, $query2) {
	$link = get_mysql_link();
	
	calculeStatsEuro2008($link);
	
	//récupération du nb de match
	$result = mysql_query($query2, $link);
	if (!$result) my_error($link, $query2);
	$row = mysql_fetch_array($result, MYSQL_NUM);
	$nbMatch = $row[0]+1;
	
	//récupération des matchs
	$result = mysql_query($query, $link);
	if (!$result) my_error($link, $query);

	//groupe A : 1     2    3   4    6    5
	//groupe B : 7    8    9  10   11   12
	//groupe C : 13  14  15  16   18   17
	//groupe D : 19 20  21  22  24  23

	//récupération des scores
	for ($b=1;$b<$nbMatch;$b++) {
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			if ($b==5 or $b==17 or $b==23) {
				$b = $b+1;
				$match[$b] = $row[1];$score[$b] = $row[2] . "-" . $row[3];
				$b = $b-1;
			} else if ($b==6 or $b==18 or $b==24) {
				$b = $b-1;
				$match[$b] = $row[1];$score[$b] = $row[2] . "-" . $row[3];
				$b = $b+1;
			} else {
				$match[$b] = $row[1];$score[$b] = $row[2] . "-" . $row[3];
			}
			$b++;
		}
	}
	
	//initialisation des scores pour match non joué
	for($a=1;$a<$nbMatch;$a++){
		if ($score[$a] == null) $score[$a] = " - ";
	}

	//titre de la page
	echo "<table align='center' width=20% cellpadding=0 cellspacing=0><tr><td style='font-weight:bold;color:#FFFF99;'>Coupe du monde 2010 - R&eacute;sultats<td></tr></table>";
	echo "<br/>";
	
	//grande table qui contient les tableaux de groupe
	echo "<table width='80%'>";
	
	//compteur pour les dates
	$k = 0;
	
	//for les 4 groupes
	for($l=0;$l<24;$l=$l+6) {
		
		//initialisation nom groupe
		if($l==0) $groupe = "A";if($l==6) $groupe = "B";if($l==12) $groupe = "C";if($l==18) $groupe = "D";
		
		echo "<tr><td>";
		echo "<table align='center' cellspacing=0 cellpadding=0 bordercolor=white>";
		echo "<tr style='background-color:black;font-weight:bold;color:#FFFF99;'><td colspan=3> Groupe " .$groupe. "</td></tr>";
	    echo "<tr class=\"beige\"><td>Date</td><td width=300> Match </td><td width=30>Score</td></tr>";

		//compteurs pour les matchs
		$j = 1 + $l; //1,7,13,19
		
		//pour tous les matchs du groupe
		while ($j <= (6+$l)) {
			if ($j-$l==1) {
				echo "<tr class=\"vertclair\"><td align=right>";
				echo $j+6+$k;
				echo " juin 18h00</td>";
			}
			if ($j-$l==2 or $j-$l==6) echo "<tr class=\"vertclair\"><td align=right>20h45</td>";
			if ($j-$l==3) {
				echo "<tr class=\"vertfonce\"><td align=right>";
				echo $j+8+$k;
				echo " juin 18h00</td>";
			}
			if ($j-$l==4) echo "<tr class=\"vertfonce\"><td align=right>20h45</td>";
			if ($j-$l==5) {
				echo "<tr class=\"vertclair\"><td align=right>";
				echo $j+10+$k;
				echo " juin 20h45</td>";
			}
			echo "<td><b>" . $match[$j] . "</b></td><td>" . $score[$j] . "</td>";
			echo "</tr>"; 
			$j++;
		}//fin pour tous les matchs
		
		echo "</table>";
		echo "</td>";
		
		//début colonne classement
		echo"<td>";
		echo"<table width=80% cellspacing=0>";
		echo "<tr style='background-color:black;font-weight:bold;color:#FFFF99;'><td colspan=6> Groupe " .$groupe. "</td></tr>";
		echo"<tr class='beige'>";
			echo"<td>Clt</td>";
			echo"<td>Equipe</td>";
			echo"<td>Pts</td>";
			echo"<td>Bp</td>";
			echo"<td>Bc</td>";
			echo"<td>Diff</td>";
		echo"<tr>";
		
		if ($groupe=='A') {
			$query = "SELECT s.equipe, s.points, s.bp, s.bc, s.diff FROM `stats_euro` s where s.idEquipe < 5 order by s.points desc, s.diff desc, s.bp desc, s.bc asc";
		}
		
		if ($groupe=='B') {
			$query = "SELECT s.equipe, s.points, s.bp, s.bc, s.diff FROM `stats_euro` s where s.idEquipe > 4 and s.idEquipe < 9 order by s.points desc, s.diff desc, s.bp desc, s.bc asc";
		}
		
		if ($groupe=='C') {
			$query = "SELECT s.equipe, s.points, s.bp, s.bc, s.diff FROM `stats_euro` s where s.idEquipe > 8 and s.idEquipe < 13 order by s.points desc, s.diff desc, s.bp desc, s.bc asc";
		}
		
		if ($groupe=="D") {
			$query = "SELECT s.equipe, s.points, s.bp, s.bc, s.diff FROM `stats_euro` s where s.idEquipe > 12 order by s.points desc, s.diff desc, s.bp desc, s.bc asc";
		}
		
		$result = mysql_query($query, $link);
		if (!$result) my_error($link, $query);
		$i=0;
		while($row = mysql_fetch_array($result, MYSQL_NUM)) {
			$i++;
			if ($i<3) {
				echo"<tr class='vertclair'>";
			} else {
				echo"<tr class='vertfonce'>";
			}
				echo"<td>" .$i. "</td>";
				echo"<td>" .$row[0]. "</td>";
				echo"<td>" .$row[1]. "</td>";
				echo"<td>" .$row[2]. "</td>";
				echo"<td>" .$row[3]. "</td>";
				echo"<td>" .$row[4]. "</td>";
			echo"<tr>";
		}
		echo"</table>";
		
		echo "</td></tr>";
		//fin colonne classement

		echo "<tr><td><br/></td></tr>";
		$k = $k - 5;
	}//fin for les 4 groupes

	echo "</table>";//fin grande table
}

function calculeStatsEuro2008($link) {

	//vide la table stats_euro
	$query_vide = "DELETE FROM `stats_euro`";
	$result = mysql_query($query_vide, $link);
	if (!$result) my_error($link, $query_vide);

	//récupération de l'équipe
	for ($idEquipe=1;$idEquipe<=16;$idEquipe++) {
		$query_equipe = "SELECT nom FROM `equipe` e where e.idequipe = " .$idEquipe;
		$result = mysql_query($query_equipe, $link);
		if (!$result) my_error($link, $query_equipe);
		$row = mysql_fetch_array($result, MYSQL_NUM);
		$equipe = trim($row[0]);

		//nb points
		$query_pts = "SELECT 3*count(*) FROM `match` m where (m.lib_match like '".$equipe." %' and m.score1 > m.score2) or (m.lib_match like '% ".$equipe."' and m.score2 > m.score1)";
		$result = mysql_query($query_pts, $link);
		if (!$result) my_error($link, $query_pts);
		$row = mysql_fetch_array($result, MYSQL_NUM);
		$points = $row[0];
		$query_pts = "SELECT count(*) FROM `match` m where m.lib_match like '%" .$equipe. "%' and m.score1 = m.score2 and m.score1 is not null";
		$result = mysql_query($query_pts, $link);
		if (!$result) my_error($link, $query_pts);
		$row = mysql_fetch_array($result, MYSQL_NUM);
		$points = $points + $row[0];
		//Bp
		$query_bp = "SELECT sum(m.score1) FROM `match` m where m.lib_match like '" .$equipe. "%'";
		$result = mysql_query($query_bp, $link);
		if (!$result) my_error($link, $query_bp);
		$row = mysql_fetch_array($result, MYSQL_NUM);
		$bp = $row[0];
		$query_bp = "SELECT sum(m.score2) FROM `match` m where m.lib_match like '% " .$equipe. "'";
		$result = mysql_query($query_bp, $link);
		if (!$result) my_error($link, $query_bp);
		$row = mysql_fetch_array($result, MYSQL_NUM);
		$bp = $bp + $row[0];
		//Bc
		$query_bc = "SELECT sum(m.score2) FROM `match` m where m.lib_match like '" .$equipe. "%'";
		$result = mysql_query($query_bc, $link);
		if (!$result) my_error($link, $query_bc);
		$row = mysql_fetch_array($result, MYSQL_NUM);
		$bc = $row[0];
		$query_bc = "SELECT sum(m.score1) FROM `match` m where m.lib_match like '% " .$equipe. "'";
		$result = mysql_query($query_bc, $link);
		if (!$result) my_error($link, $query_bc);
		$row = mysql_fetch_array($result, MYSQL_NUM);
		$bc = $bc + $row[0];
		//Diff
		$diff = $bp-$bc;
		
		$query_majClt = "INSERT INTO `stats_euro` ( `idEquipe` , `equipe` , `points` , `bp` , `bc` , `diff` ) VALUES (" .$idEquipe. ", '" .$equipe. "', " .$points. ", " .$bp. ", " .$bc. ", " .$diff. ")";
		$result = mysql_query($query_majClt, $link);
		if (!$result) my_error($link, $query_majClt);
	}
}

?>