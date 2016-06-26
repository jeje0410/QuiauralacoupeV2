<h3> Liste des matchs &agrave compl&eacute;ter </h3>
<?php
function afficheTableauScoreAJoueAdmin($query, $idmatch, $filtre) {
	require_once 'common.php';
	$link = get_mysql_link();

	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);

	//rem - deb - 17.04.08
	echo "<form action=\"administration.php\" method=\"post\">";
	echo "<table align=\"center\">";
	echo "<tr>";
	echo "<td><select name='filtre'>";
	if ($filtre == 0) {
		echo "<option value=\"" . 0 . "\" selected>" . "Matchs en cours" . "</option>";
		echo "<option value=\"" . 1 . "\">" . "Voir tous les matchs" . "</option>";
	} else {
		echo "<option value=\"" . 0 . "\">" . "Matchs en cours" . "</option>";
		echo "<option value=\"" . 1 . "\" selected >" . "Voir tous les matchs" . "</option>";
	}
	echo "</select></td>";
	echo "<td><input type=\"submit\" value=\"Filtrer\"></td>";
	echo "</tr>";
	echo "</table>";
	echo "<br/>";
	echo "</form>";
	//rem - fin - 17.04.08
	
	echo "<table class=\"tableau\">";
	echo "<tr class=\"beige\">";
	echo "<td> Match </td>";
	echo "<td> Date du match </td>";
	echo "<td> Score </td>";
	echo "<td> &nbsp; </td>";
	echo "</tr>";
	$j = 0;
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		if ($j % 2 == 0) {
			echo "<tr class=\"vertclair\">";
		} else {
			echo "<tr class=\"vertfonce\">";
		}
		$j++;
		echo "<td>" . $row[1] . "</td>";
		echo "<td>" . $row[4] . "</td>";

		echo "<form action=\"maj.php\" method=\"post\">";
		echo "<input type=\"hidden\" name=\"idmatch\" value=\"" . $row[0] . "\">";
		//rem - deb - 17.04.08
		echo "<input type=\"hidden\" name=\"filtre\" value=\"" . $filtre . "\">";
		//rem - fin - 17.04.08
		echo "<td><select name=\"score1a\">";
		for ($i = 0; $i < 8; $i++) {
			if ($i == $row[2]) {
				echo "<option value=\"" . $i . "\" selected>" . $i . "</option>";
			} else {
				echo "<option value=\"" . $i . "\">" . $i . "</option>";
			}
		}
		echo "</select>-";
		echo "<select name=\"score2a\">";
		for ($i = 0; $i < 8; $i++) {
			if ($i == $row[3]) {
				echo "<option value=\"" . $i . "\" selected>" . $i . "</option>";
			} else {
				echo "<option value=\"" . $i . "\">" . $i . "</option>";
			}
		}
		echo "</select></td>";
		//on regarde si l'on a appuyé sur "enregistrer" pour le match de cette ligne
		if (isset ($idmatch) && $idmatch == $row[0]) {
			//On affiche le lien vers la page d'enregistrement des buteurs
			echo "<td><input type=\"button\" onclick=\"window.open('renseignerbuteur.php?idMatch=" . $row[0] . "&libMatch=" .str_replace("'","\'",$row[1]) . "&score1=" . $row[2] . "&score2=" . $row[3] . "&filtre=" . $filtre . "','buteurs','location=no,width=600,height=400,resizable=no,status=no');\" value=\"Buteurs\"</td>";
		} else {
			echo "<td><input type=\"submit\" value=\"Enregistrer\"</td>";
		}
		echo "</tr>";
		echo "</form>";
	}
	echo "</table>";
}

//rem - deb - 17.04.08
//rem - deb - 17.04.08
	//ajout d'un filtre sur les matchs pour alléger l'affichage
	$filtre = 0;
	if (isset ($_GET['filtre'])) {
		$filtre = $_GET['filtre'];
	}
	if (isset ($_POST['filtre'])) {
		$filtre = $_POST['filtre'];
	}
	
//test sur la valeur du filtre (0: on affiche les matchs en cours, 1: on affiche tous les matchs)
if ($filtre == 0) {
	$query = "SELECT distinct m.idMatch, lib_match, m.score1, m.score2, dateM, p.score1, p.score2, joker, points_total FROM `match` m left outer join `pari` p on p.idMatch=m.idMatch and m.resultat is null and p.login='" . $login . "' where m.idMatch not in (select distinct bm.idmatch from buteur_match bm) order by m.dateM asc";
} else {
	$query = "SELECT m.idMatch, lib_match, m.score1, m.score2, dateM, p.score1, p.score2, joker, points_total FROM `match` m left outer join `pari` p on p.idMatch=m.idMatch and m.resultat is null and p.login='" . $login . "' order by dateM asc";
}
//rem - fin - 17.04.08

if (!isset ($idmatch)) {
	$idmatch = 0;
}
afficheTableauScoreAJoueAdmin($query, $idmatch, $filtre);
?>