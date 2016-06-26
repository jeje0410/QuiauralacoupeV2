<?php


// Configuration de la connexion à la base de donnes
require_once 'config.php';

if (get_magic_quotes_gpc()) {
	global $is_magic_quotes_unfck_yet_done;

	function unfck($v) {
		return is_array($v) ? array_map('unfck', $v) : stripslashes($v);
	}

	if (!$is_magic_quotes_unfck_yet_done) {
		foreach (array (
				'POST',
				'GET',
				'REQUEST',
				'COOKIE'
			) as $gpc)
			$GLOBALS["_$gpc"] = array_map('unfck', $GLOBALS["_$gpc"]);
		$is_magic_quotes_unfck_yet_done = true;
	}
}

/*
 * Display a page with an error message and stop processing. Nothing must
 * have been echoed until now.
 * This error message is for the user, not the admin.
 * To print an error message for the admin, use trigger_error().
 */
function on_error($msg) {
	require_once ('header.php');
?>
<table border="1"><tr><td class="errorTable">Erreur</td></tr>
<tr><td>
<?php echo $msg; ?>
</td></tr>
</table><br />
<?php


	require_once ('footer.php');
	exit (1);
}

/*
 * Return a required GET argument or display an error.
 */
function get_arg($argname) {
	$arg = null;
	if (isset ($_GET[$argname]))
		$arg = $_GET[$argname];
	if (is_null($arg))
		on_error("Argument '$argname' manquant");
	return (string) $arg;
}

/*
 * Return a required POST argument or display an error.
 */
function get_post_arg($argname) {
	$arg = null;
	if (isset ($_POST[$argname]))
		$arg = $_POST[$argname];
	if (is_null($arg))
		on_error("Argument de formulaire '$argname' manquant");
	return (string) $arg;
}

/*
 * Display an error message to the user.
 */
function internal_error() {
	on_error("Erreur interne a  l'application");
}

/*
 * Errors in database access.
 */
function my_error($link, $req) {
	$err = mysql_error($link);
	if (!empty ($err)) {
		trigger_error("Database error ($req) : $err", E_USER_WARNING);
	}
	internal_error();
}

/*
 * Connect to the database according to configuration in config.php.
 */
function get_mysql_link() {
	global $mysql_db;
	global $mysql_login;
	global $mysql_password;
	global $mysql_server;

	//$link = @mysql_connect($mysql_server, $mysql_login, $mysql_password);
	$link = mysql_connect('localhost','root','');
	if (!$link)
		internal_error();
	if (!mysql_select_db($mysql_db, $link))
		my_error($link, "use " . $mysql_db);
	if (!mysql_query("set names utf8", $link))
		my_error($link, "set names utf8");
	$query = "set autocommit=0";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	return $link;
}

/*
 * Return the session ID to append to an URL without parameter.
 */
function sid_0() {
	if (SID == "")
		return "";
	else
		return "?" . SID;
}

/*
 * Return the session ID to append to an URL with parameters.
 */
function sid_n() {
	if (SID == "")
		return "";
	else
		return "&" . SID;
}

function is_admin($login) {
	$link = get_mysql_link();
	$query = "select groupe from joueur_groupe where login='" . $login . "'";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);

	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {

		if ($row[0] == "admin")
			return true;

		else
			return false;
	}
}

/*
 * Affiche Tableau Score des matchs déja joués
*/

function afficheTableauScoreJoue($query) {
	$link = get_mysql_link();

	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);

	echo "<table  class=\"tableau\">";
	echo "<tr class=\"beige\">";
	
	echo "<td> Match </td>";
	echo "<td> R&eacute;sultat </td>";
	echo "<td> Date du match </td>";
	echo "<td> Paris </td>";
	echo "<td> Joker Jou&eacute;</td>";
	echo "<td> Points R&eacute;sultat </td>";
	echo "<td> Points Cote </td>";
	echo "<td> Points buteurs </td>";
	echo "<td> Total Point </td>";
	echo "</tr>";
	$j = 0;
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		echo "<tr class=\"beige\"><td colspan=\"9\"></td></tr>";
		echo "<tr onclick=\"javascript:ouvrirResultatMatch('" . $row[0] . "');\"";
		if ($j % 2 == 0) {
			echo " class=\"vertclairLien\">";
		} else {
			echo " class=\"vertfonceLien\">";
		}
		$j++;
		echo "<td>" . $row[1] . "</td>";
		echo "<td>" . $row[2] . "-" . $row[3] . "</td>";
		echo "<td>" . $row[4] . "</td>";
		echo "<td>" . $row[5] . "-" . $row[6] . "</td>";
		echo "<td>" . $row[7] . "</td>";
		echo "<td>" . $row[8] . "</td>";
		echo "<td>" . $row[9] . "</td>";
		echo "<td>" . $row[10] . "</td>";
		echo "<td>" . $row[11] . "</td>";
		echo "</tr>";
	}
	echo "</table>";
}

/*
 * Affiche Tableau Score des matchs à parier 
*/

function afficheTableauScoreAJoue($query, $idmatch) {
	$link = get_mysql_link();
	$tendance=calculCoteMatch();
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);

	echo "<table class=\"tableau\">";
	echo "<tr class=\"beige\">";
	echo "<td> Match </td>";
	echo "<td> Date du match </td>";
	echo "<td> Paris </td>";
	echo "<td> Joker </td>";
	echo "<td> &nbsp; </td>";
	echo "<td> Cote </td>";
	echo "</tr>";
	$j = 0;
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		echo "<tr class=\"beige\"><td colspan=\"6\"></td></tr>";
		if ($j % 2 == 0) {
			echo "<tr class=\"vertclairparis\">";
		} else {
			echo "<tr class=\"vertfonceparis\">";
		}
		$j++;
		echo "<td>" . $row[1] . "</td>";
		echo "<td>" . $row[4] . "</td>";

		if ($idmatch == $row[0]) {
			echo "<form action=\"savepari.php\" method=\"post\">";
			echo "<input type=\"hidden\" name=\"idmatch\" value=\"" . $row[0] . "\">";
			echo "<td><select name=\"score1\">";
			for ($i = 0; $i < 10; $i++) {
				if ($i == $row[5]) {
					echo "<option value=\"" . $i . "\" selected>" . $i . "</option>";
				} else {
					echo "<option value=\"" . $i . "\">" . $i . "</option>";
				}
			}
			echo "</select>-";
			echo "<select name=\"score2\">";
			for ($i = 0; $i < 10; $i++) {
				if ($i == $row[6]) {
					echo "<option value=\"" . $i . "\" selected>" . $i . "</option>";
				} else {
					echo "<option value=\"" . $i . "\">" . $i . "</option>";

				}
			}
			echo "</select></td>";
			echo "<input type=\"hidden\" name=\"joker\" value=\"0\">";
			//echo "<td><select name=\"joker\">";
			//echo "<option value=\"0\" selected>Non</option>";
			//echo "<option value=\"1\">Oui</option></td>";
			echo "<td>&nbsp;</td>";
			echo "<td><input type=\"submit\" value=\"Valider\" class=\"bouton\"/></td>";
			echo "<td><table class=\"tendance\"><tr><td colspan=\"3\">".$tendance[$row[0]][4]." pari(s)</td></tr><tr><td>1</td><td>N</td><td>2</td></tr><tr><td>".$tendance[$row[0]][1]."</td><td>".$tendance[$row[0]][2]."</td><td>".$tendance[$row[0]][3]."</td></tr></table></td>";
			echo "</tr>";
			echo "</form>";
		} else {
			// Nico 5/05 
			if($row[7]==1) {$joker="X";}
			else {$joker="&nbsp;";}
			echo "<form action=\"paris.php?idmatch=" . $row[0] . "\" method=\"post\">";
			echo "<td>" . $row[5] . "&nbsp;-&nbsp;" . $row[6] . "</td>";
			echo "<td>" . $joker . "</td>";
			echo "<td><input type=\"submit\" value=\"Modifier\" class=\"bouton\"></td>";
			echo "<td><table class=\"tendance\"><tr><td colspan=\"3\">".$tendance[$row[0]][4]." pari(s)</td></tr><tr><td>1</td><td>N</td><td>2</td></tr><tr><td>".$tendance[$row[0]][1]."</td><td>".$tendance[$row[0]][2]."</td><td>".$tendance[$row[0]][3]."</td></tr></table></td>";			
			echo "</tr>";
			echo "</form>";
		}
	}
	echo "</table>";
}
/*
 * Affiche Classement Solo
*/

function afficheClassementSolo($query, $login) {
	$link = get_mysql_link();

	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	echo "<table align=\"center\"></table>";
	echo "<br>";
	echo "<table class=\"tableau\">";
	echo "<tr class=\"vertfonce\"><td align=\"left\" colspan=12><a href=\"record.php\">les records</a></td></tr>";
	echo "<tr class=\"beige\">";
	echo "<td>Place</td>";
	echo "<td width=\"40%\">Nom</td>";
	echo "<td>Points</td>";
	echo "<td>Points R&eacute;sultat</td>";
	echo "<td>Points Cote</td>";
	echo "<td>Points Buteurs</td>";
	echo "<td>Points Favoris</td>";
	echo "<td>Joker(s) jou&eacute;(s)</td>";
	echo "<td>% Sens Correct</td>";
	echo "<td>% Score exact</td>";
	echo "<td>Progression &darr; = &uarr;</td>";
	echo "<td>Nombre match</td>";

	echo "</tr>";
     // nico 08/06
    $i = 0;
	$j = 1;
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		//echo "<tr class=\"beige\"><td colspan=\"11\"></td></tr>";
		echo "<tr onclick=\"window.location.href='accueil.php?visualiser=" . $row[1] . "'\"";
		//la ligne du joueur connecté en bleue
		if ($row[1] == $login) {
			echo " style='color:blue'";
		}
		//la ligne de Monsieur Defaut en rouge
		if ($row[1] == 'MonsieurDefaut') {
			echo " style='color:red'";
		}
		if ($i<5) { 
			if (($i % 2)==0){
				echo " class=\"teteclassementf\">";
			} else {
				echo " class=\"teteclassementc\">";
			}
		} else {
			if (($i % 2)==0){
				echo " class=\"vertclairLien\">";
			} else {
				echo " class=\"vertfonceLien\">";
			}
		}
	// fin nico 08/06
		echo "<td>" . $j . "</td>";
		$j++;
		if ($row[1] == $login) {
			echo "<td><b>" . $row[2] . " " . /*substr($row[3],0,2)*/$row[3] . "</b></td>";
		} else {
			//echo "<td><a href=\"accueil.php?visualiser=" . $row[1] . "\">" . $row[2] . " " . /*substr($row[3],0,2)*/$row[3] . "</a></td>";
			echo "<td>" . $row[2] . " " . /*substr($row[3],0,2)*/$row[3] . "</td>";
		}
		echo "<td>" . $row[4] . "</td>";
		echo "<td>" . $row[5] . "</td>";
		echo "<td>" . $row[6] . "</td>";
		echo "<td>" . $row[7] . "</td>";
		echo "<td>" . $row[12] . "</td>";
		echo "<td>" . $row[13] . "</td>";
		if ($row[11] == 0) {
			echo "<td>" . $row[8] . "</td>";
			echo "<td>" . $row[9] . "</td>";
		} else {
			echo "<td>" . round(100 * $row[8] / $row[11]) . "</td>";
			echo "<td>" . round(100 * $row[9] / $row[11]) . "</td>";
		}
		echo "<td>" . $row[10] . "</td>";
		echo "<td>" . $row[11] . "</td>";

		echo "</tr>"; 
		// nico 08/06
		$i++;
	}
	echo "</table>";
}

/* Remplit la table match du resultat */
function remplitMatch($idmatch, $score1, $score2) {
	if ($score1 > $score2) {
		$victoire = "1";
	}
	elseif ($score1 == $score2) {
		$victoire = "N";
	} else {
		$victoire = "2";
	}
	$ecart = intval($score1)-intval($score2);
	$query = "update `match` set score1='" . $score1 . "', score2='" . $score2 . "', resultat='" . $victoire . "', ecart='" .$ecart. "' where idmatch='" . $idmatch . "'";
	$link = get_mysql_link();
	// Debut Nico 29/05
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	// fin nico 29/05	
	// Debut Nico 18/05/2006
	$query = "select dateM from `match` where idmatch='" . $idmatch . "'";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$date = $row[0];
	}
	// Fin Nico
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
}

/* Calcul la cagnotte */
function calculCagnotte($idMatch) {
	require_once 'config.php';
	global $pointcagnotte;
	global $pointcagnotte8;
	global $pointcagnotte4;
	global $pointcagnotte2;
	global $pointcagnotte1;
	$link = get_mysql_link();
	$query = "SELECT score1,score2 FROM `match` where idmatch='" . $idMatch . "'";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$score1 = intval($row[0]);
		$score2 = intval($row[1]);
	}
	$cote = renvoiLaCote($idMatch);
	if ($score1 == $score2) {
		return $cote[1];
	} 
	if ($score1 > $score2) {
		return $cote[0];
	} 
	if ($score1 < $score2) {
		return $cote[2];
	}
}

/* Remplit les points */
function remplitPoint($idmatch, $cagnotte,$ecart) {
	$link = get_mysql_link();
	$query = "select type from `match` where idmatch='" . $idmatch . "'";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$type = $row[0];
	}	
	global $pointsensgroupe;
	global $pointecart;
	global $pointexactgroupe;
	global $pointsens8;
	global $pointecart8;
	global $pointexact8;
	global $pointsens4;
	global $pointecart4;
	global $pointexact4;
	global $pointsens2;
	global $pointecart2;
	global $pointexact2;
	global $pointsens1;
	global $pointecart1;
	global $pointexact1;

	switch ($type) {
		//Match de poule
		case 0 :
			$nbpointsens = $pointsensgroupe;
			$nbpointecart = $pointecart;
			$nbpointexact = $pointexactgroupe;
			break;
		//8éme
		case 1 :
			$nbpointsens = $pointsens8;
			$nbpointecart = $pointecart8;
			$nbpointexact = $pointexact8;
			break;
		//quart
		case 2 :
			$nbpointsens = $pointsens4;
			$nbpointecart = $pointecart4;
			$nbpointexact = $pointexact4;
			break;
		//demi
		case 3 :
			$nbpointsens = $pointsens2;
			$nbpointecart = $pointecart2;
			$nbpointexact = $pointexact2;
			break;
		//Finale
		case 4 :
			$nbpointsens = $pointsens1;
			$nbpointecart = $pointecart1;
			$nbpointexact = $pointexact1;
			break;
	default :
			break;
	}
	
	$query = "update pari set points_match ='" . $nbpointsens . "' where idmatch ='" . $idmatch . "' and sens='1' and exact='0' and joker='0'";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	$query = "update pari set points_match ='" . ($nbpointsens*2) . "' where idmatch ='" . $idmatch . "' and sens='1' and exact='0' and joker='1'";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	$query = "update pari set points_match ='" . $nbpointecart . "' where idmatch ='" . $idmatch . "' and sens='1' and ecart='".$ecart."' and joker='0'";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	$query = "update pari set points_match ='" . ($nbpointecart*2) . "' where idmatch ='" . $idmatch . "' and sens='1' and ecart='".$ecart."' and joker='1'";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	$query = "update pari set points_match ='" . $nbpointexact . "' where idmatch ='" . $idmatch . "' and sens='1' and exact='1' and joker='0'";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	$query = "update pari set points_match ='" . ($nbpointexact*2) . "' where idmatch ='" . $idmatch . "' and sens='1' and exact='1' and joker='1'";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	$query = "update pari set points_match ='0' where idmatch ='" . $idmatch . "' and sens='0' and exact='0'";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	$query = "update pari set points_cagnotte ='" . $cagnotte . "' where idmatch ='" . $idmatch . "' and sens='1'";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	$query = "update pari set points_cagnotte ='" . ($cagnotte*2) . "' where idmatch ='" . $idmatch . "' and sens='1' and joker='1'";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	$query = "update pari set points_cagnotte ='0' where idmatch ='" . $idmatch . "' and sens='0'";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	/* a optimiser : remplit les points total*/
	$query = "select login, points_cagnotte, points_match from `pari` where idmatch ='" . $idmatch . "'";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$pointtot = $row[1] + $row[2];
		$query = "update pari set points_total ='" . $pointtot . "' where idmatch ='" . $idmatch . "' and login ='" . $row[0] . "'";
		$result2 = mysql_query($query, $link);
		if (!$result2)
			my_error($link, $query);
	}

}
/* Remplit le classement*/
function remplitClassement($idmatch) {
	$nbMatch = nbmatchjoue();
	$link = get_mysql_link();
	$query = "SELECT login FROM `pari` p WHERE p.idmatch='" . $idmatch . "'";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {	
		$points = "select sum(points_total), sum(points_match), sum(points_cagnotte), count(*), sum(sens), sum(exact) from `pari` p where p.login='".$row[0]."'";
		$resultpoint = mysql_query($points, $link);
		if (!$resultpoint)
			my_error($link, $points);
		while ($row2 = mysql_fetch_array($resultpoint, MYSQL_NUM)) {
			$newpoint = $row2[0];
			$newpointmatch = $row2[1];
			$newpointcagnotte = $row2[2];
			$newnbmatch = $row2[3];
			$newsens = $row2[4];
			$newexact = $row2[5];
		}
		$update = "update classement c set points='" . $newpoint . "', points_matchs='" . $newpointmatch . "', points_cagnotte='" . $newpointcagnotte . "', exact='" . $newexact . "', sens='" . $newsens . "', nb_match='". $nbMatch . "' WHERE c.login='" . $row[0] . "'";
		$updateclassement = mysql_query($update, $link);
		if (!$updateclassement)
			my_error($link, $update);
	}
}

/* Retourne le nombre de match joué */
function nbmatchjoue() {
	$link = get_mysql_link();

	$query = "SELECT count(idMatch) FROM `match` c WHERE resultat is not null";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$nb = $row[0];
	}
	return $nb;
}

/* Retourne le nombre de points d'un joueur dans le pari de match idmatch */
function nbpointparijoueur($idmatch, $login) {
	
$link = get_mysql_link();
	$query = "SELECT points FROM `pari` p WHERE p.idmatch='" . $idmatch . "' and p.login='" . $login . "'";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$nb = $row[0];
	}
	return $nb;
}

/* Retourne le nombre de points actuel d'un joueur */
function nbpointactueljoueur($login) {
	$link = get_mysql_link();
	$query = "SELECT points FROM `classement` c WHERE c.login='" . $login . "'";
	$result = mysql_query($query, $link);
	//DEBUT MODIFICATION LE 20 mai 2006 PAR OCLA
	$nb = 0;
	//FIN MODIFICATION LE 20 mai 2006 PAR OCLA
	if (!$result)
		my_error($link, $query);
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$nb = $row[0];
	}
	return $nb;
}
/* Retourne le nombre de points matchs actuel d'un joueur */
function nbpointmatchactueljoueur($login) {
	$link = get_mysql_link();
	$query = "SELECT points_matchs FROM `classement` c WHERE c.login='" . $login . "'";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	//DEBUT MODIFICATION LE 20 mai 2006 PAR OCLA
	$nb = 0;
	//FIN MODIFICATION LE 20 mai 2006 PAR OCLA
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$nb = $row[0];
	}
	return $nb;
}
/* Retourne le nombre de points cagnotte actuel d'un joueur */
function nbpointcagnotteactueljoueur($login) {
	$link = get_mysql_link();
	$query = "SELECT points_cagnotte FROM `classement` c WHERE c.login='" . $login . "'";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	//DEBUT MODIFICATION LE 20 mai 2006 PAR OCLA
	$nb = 0;
	//FIN MODIFICATION LE 20 mai 2006 PAR OCLA
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$nb = $row[0];
	}
	return $nb;
}
/* Retourne le nombre de bon sens d'un joueur */
function nbsensactueljoueur($login) {
	$link = get_mysql_link();
	$query = "SELECT sens FROM `classement` c WHERE c.login='" . $login . "'";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	//DEBUT MODIFICATION LE 20 mai 2006 PAR OCLA
	$nb = 0;
	//FIN MODIFICATION LE 20 mai 2006 PAR OCLA
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$nb = $row[0];
	}
	return $nb;
}

/* Retourne le nombre de bon score exact  d'un joueur */
function nbexactactueljoueur($login) {
	$link = get_mysql_link();
	$query = "SELECT exact FROM `classement` c WHERE c.login='" . $login . "'";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	//DEBUT MODIFICATION LE 20 mai 2006 PAR OCLA
	$nb = 0;
	//FIN MODIFICATION LE 20 mai 2006 PAR OCLA
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$nb = $row[0];
	}
	return $nb;
}

/* Retourne le nombre de bon score exact  d'un joueur */
function nbmatch($login) {
	$link = get_mysql_link();
	$query = "SELECT nb_match FROM `classement` c WHERE c.login='" . $login . "'";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	//DEBUT MODIFICATION LE 20 mai 2006 PAR OCLA
	$nb = 0;
	//FIN MODIFICATION LE 20 mai 2006 PAR OCLA
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$nb = $row[0];
	}
	return $nb;
}

/* Remplit les paris */
function remplitPari($idmatch, $score1, $score2) {
	$link = get_mysql_link();
	if ($score1 > $score2) {
		$query = "update `pari` set sens='1' where idmatch='" . $idmatch . "' and score1>score2";
		$result = mysql_query($query, $link);
		if (!$result)
			my_error($link, $query);
		$query = "update `pari` set sens='0' where idmatch='" . $idmatch . "' and score1<=score2";
		$result = mysql_query($query, $link);
		if (!$result)
			my_error($link, $query);
	}
	elseif ($score1 == $score2) {
		$query = "update `pari` set sens='1' where idmatch='" . $idmatch . "' and score1=score2";
		$result = mysql_query($query, $link);
		if (!$result)
			my_error($link, $query);
		$query = "update `pari` set sens='0' where idmatch='" . $idmatch . "' and score1!=score2";
		$result = mysql_query($query, $link);
		if (!$result)
			my_error($link, $query);
	} else {
		$query = "update `pari` set sens='1' where idmatch='" . $idmatch . "' and score1<score2";
		$result = mysql_query($query, $link);
		if (!$result)
			my_error($link, $query);
		$query = "update `pari` set sens='0' where idmatch='" . $idmatch . "' and score1>=score2";
		$result = mysql_query($query, $link);
		if (!$result)
			my_error($link, $query);
	}

	$query = "update `pari` set exact='1' where idmatch='" . $idmatch . "' and score1='" . $score1 . "' and score2='" . $score2 . "'";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	$query = "update `pari` set exact='0' where idmatch='" . $idmatch . "' and (score1!='" . $score1 . "' or score2!='" . $score2 . "')";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
}

function verif_pari($login, $idmatch) {
	$query = "select count(*) from pari where idmatch='" . $idmatch . "' and login='" . $login . "'";
	$link = get_mysql_link();
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	$row = mysql_fetch_array($result, MYSQL_NUM);
	if ($row[0] == 0)
		return true;
	else
		return false;
}
function aDejaParie($login) {
	$link = get_mysql_link();
	$query = "SELECT count(*) FROM `prono_classement_final` p WHERE p.login='" . $login . "'";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$nb = $row[0];
	}
	if ($nb > 1) {
		on_error("Il y a plus d'un pronostic pour " . $login);
	}
	return ($nb == 1);
}

// Debut modification le 13/05 JWAL
function afficheResultat($query) {
	$link = get_mysql_link();

	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);

	$row = mysql_fetch_array($result, MYSQL_NUM);

	echo "<h3>" . $row[1] . "<br>";
	echo $row[2] . " - " . $row[3] . "</h3>";

	echo "<table class=\"tableau\">";
	echo "<tr class=\"beige\">";
	echo "<td> Buteurs </td>";
	echo "</tr>";

	echo "<tr class=\"vertfonce\">";
	echo "<td>" . $row[5] . " " . $row[4] . " (" . $row[6] . ") " . "</td>";
	echo "</tr>";

	$j = 0;

	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		if ($j % 2 == 0) {
			echo "<tr class=\"vertclair\">";
		} else {
			echo "<tr class=\"vertfonce\">";
		}
		$j++;
		echo "<td>" . $row[5] . " " . $row[4] . " (" . $row[6] . ") "  . "</td>";
		echo "</tr>";
	}
	echo "</table>";
}
// Fin modification le 13/05 JWAL

//DEBUT MODIFICATION LE 10 mai 2006 PAR OCLA

function selectButeurs($idbuteur, $cpt) {
	$link = get_mysql_link();
	// nico 25/05
	//$query = "SELECT idButeur, prenom, nom FROM `buteur` order by prenom";
	$query = "SELECT b.idButeur, b.prenom, b.nom, e.nom FROM `buteur` b LEFT JOIN `equipe` e ON b.idEquipe = e.idEquipe order by e.nom";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	echo "<select name=\"buteur" . $cpt . "\">";

	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		if ($row[0] == $idbuteur) {
			echo "<option value=" . $row[0] . " selected > " . $row[3] . " - "  . $row[1] . " " . $row[2] . "</option>";
		} else {
			echo "<option value=" . $row[0] . " > " . $row[3] . " - " . $row[1] . " " . $row[2] . "</option>";
		}
	}
	echo "</select>";
}

//fonction pour rentrer les buteurs d'un match (ne propose que les buteurs des équipes du match)
function selectButeursMatch($idbuteur, $cpt, $libMatch) {
	$link = get_mysql_link();
	$query = "SELECT b.idButeur, b.prenom, b.nom, e.nom FROM `buteur` b LEFT OUTER JOIN `equipe` e on b.idEquipe = e.idEquipe WHERE b.idEquipe = 0 OR instr(\"" .$libMatch. "\", e.nom) != 0 ORDER BY e.nom";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	echo "<select name=\"buteur" . $cpt . "\">";

	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		if ($row[0] == $idbuteur) {
			echo "<option value=" . $row[0] . " selected > " . $row[3] . " - "  . $row[1] . " " . $row[2] . "</option>";
		} else {
			echo "<option value=" . $row[0] . " > " . $row[3] . " - " . $row[1] . " " . $row[2] . "</option>";
		}
	}
	echo "</select>";
}

/** 
 * Fonction permettant d'exécuter directement une requête, traitant les erreurs et retournant les
 * résultats sous la forme d'un tableau ordonné par numéro de ligne
*/
function executerRequete($query) {
	$link = get_mysql_link();
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	return $result;
}

/** 
 * Fonction qui permet de récupérer directement le résultat d'une requête si celui-ci est unique
 */
function executerRequeteResultatUnique($query) {
	$result = executerRequete($query);
	$row = mysql_fetch_array($result, MYSQL_NUM);
	return $row[0];
}

/** 
 * Fonction permettant de récupérer la valeur d'un paramètre qui est dans la table 'PARAMETRES'
 */
function getParametre($param) {
	$result = executerRequete("select valeur from `parametres` where param = '" . $param . "'");
	$row = mysql_fetch_array($result, MYSQL_NUM);
	return $row[0];
}
//FIN MODIFICATION LE 10 mai 2006 PAR OCLA

//DEBUT MODIFICATION LE 16 mai 2006 PAR OCLA
function nbButeursDejaParies($login) {
	$result = executerRequete("SELECT count(*) FROM `choisi_buteur` c WHERE c.login='" . $login . "'");

	$row = mysql_fetch_array($result, MYSQL_NUM);
	$nbButeursParies = $row[0];
	return $nbButeursParies;
}

function aDejaParieButeur($login) {
	global $nbbuteurs;

	$nbButeursParies = nbButeursDejaParies($login);
	if ($nbButeursParies > $nbbuteurs) {
		on_error("Il y a plus de" . $nbbuteurs . " pronostics pour " . $login);
	}
	return ($nbButeursParies == $nbbuteurs);
}

function nbButsButeur($idButeur) {
	return executerRequeteResultatUnique("select count(*) from `buteur_match` where idbuteur = " . $idButeur);
}

function recupCoteButeur($idButeur,$pointButeur){
	//Récupération du nombre de personnes ayant voté pour ce buteur
	$nbVotes = executerRequeteResultatUnique("SELECT count(*) FROM `choisi_buteur` where idButeur = " . $idButeur);
	$nbVoteTotal = intval(executerRequeteResultatUnique("SELECT count(*) FROM `choisi_buteur`"));
	//La cote correspond au nombre de point de la cagnotte divisé par le nobmre de joueurs ayant joué ce buteur
	if ($nbVotes == 0) {
		//Personne n'a voté pour ce joueur
		return $pointButeur = round($nbVoteTotal*$pointButeur);
	} else {
		return $pointButeur = round((1/($nbVotes/$nbVoteTotal))*$pointButeur);
	}
}

//FIN MODIFICATION LE 16 mai 2006 PAR OCLA

// Debut modification le 13/06 JWAL
function afficheButeur2($query) {
	$link = get_mysql_link();

	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	echo "<table class=\"tableau\">";
	echo "<tr class=\"beige\">";
	echo "<td> Buteurs </td>";
	echo "</tr>";
	$j = 0;	
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		if ($j % 2 == 0) {
			echo "<tr  onclick=\"window.location.href='detailbuteur.php?idbute=" . $row[3] . "'\" class=\"vertclairLien\">";
		} else {
			echo "<tr  onclick=\"window.location.href='detailbuteur.php?idbute=" . $row[3] . "'\" class=\"vertfonceLien\">";
		}
		$j++;
		echo "<td>" . $row[1] . " " . $row[0] . " (" . $row[2] . ") " . "</td>";
		echo "</tr>";
	}
	echo "</table>";
}

// Debut modification le 03/07 JWAL
function afficheFavoris2($query) {
	$link = get_mysql_link();

	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	echo "<table class=\"tableau\">";
	echo "<tr class=\"beige\">";
	echo "<td colspan=\"2\"> Favoris </td>";
	echo "</tr>";
	$j = 0;	
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		echo "<tr onclick=\"window.location.href='detailfavoris.php?idequi=" . $row[0] . "'\" class=\"vertclairLien\">";
		echo "<td>1er</td>";
		echo "<td>" . $row[0] . "</td>";
		echo "</tr>";
		echo "<tr onclick=\"window.location.href='detailfavoris.php?idequi=" . $row[1] . "'\" class=\"vertfonceLien\">";
		echo "<td>2eme</td>";
		echo "<td>" . $row[1] . "</td>";
		echo "</tr>";		
		echo "<tr class=\"vertclair\">";
		echo "<td>3eme</td>";
		echo "<td>" . $row[2] . "</td>";
		echo "</tr>"; //décommenter ici pour la gestion du 3ème
	}
	echo "</table>";
}

//affichage pour la MainPage
function afficheFavoris3($query) {
	$link = get_mysql_link();

	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	echo "<table class=\"tabmainpage\" height=\"60%\">";
	$j = 0;	
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$j++;
		echo "<tr onclick=\"window.location.href='detailfavoris.php?idequi=" . $row[0] . "'\" class=\"vertclairLien\">";
		echo "<td>1er</td>";
		echo "<td>" . $row[0] . "</td>";
		echo "</tr>";
		echo "<tr onclick=\"window.location.href='detailfavoris.php?idequi=" . $row[1] . "'\" class=\"vertfonceLien\">";
		echo "<td>2eme</td>";
		echo "<td>" . $row[1] . "</td>";
		echo "</tr>";		
		/*echo "<tr class=\"vertclair\">";
		echo "<td>3eme</td>";
		echo "<td>" . $row[2] . "</td>";
		echo "</tr>";*/ //décommenter ici pour la gestion du 3ème
	}
	if($j==0) {echo "<tr class=\"choixvide\"><td>Il faut choisir des favoris</td></tr>";}
	echo "</table><table><tr class=\"celClassementLib\"><td width=\"85%\">Favoris</td><td><a href=\"favoris.php\" class=\"bouton\">";
	if($j>0) {echo "Modifier";}
	else {echo "Choisir";}
	echo"</a></td></tr>";
	echo "</table>";
}

function afficheButeur3($query) {
	$link = get_mysql_link();
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	echo "<table class=\"tabmainpage\" height=\"60%\">";
	$j = 0;	
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		if ($j % 2 == 0) {
			echo "<tr  onclick=\"window.location.href='detailbuteur.php?idbute=" . $row[3] . "'\"  class=\"vertclairLien\">";
		} else {
			echo "<tr  onclick=\"window.location.href='detailbuteur.php?idbute=" . $row[3] . "'\" class=\"vertfonceLien\">";
		}
		$j++;
		echo "<td>" . $row[1] . " " . $row[0] . " (" . $row[2] . ") " . "</td>";
		echo "</tr>";
	}
	if($j==0) {echo "<tr class=\"choixvide\"><td>Il faut choisir des buteurs</td></tr>";}
	echo "</table><table><tr class=\"celClassementLib\"><td width=\"85%\">Buteurs</td><td><a href=\"buteur.php\" class=\"bouton\">";
	if($j>0) {echo "Modifier";}
	else {echo "Choisir";}
	echo"</a></td></tr>";
	echo "</table>";
}

// Deb nico 25/05
/* Remplit le classementequipe*/
function remplitClassementEquipe() {
	$link = get_mysql_link();
	$query = "select idequipe, nbjoueur from descr_equipe";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		// debut nico 11/06
		$sumjoueur = executerRequeteResultatUnique("SELECT sum( points ) FROM `classement` WHERE login IN (SELECT login FROM joueur_equipe WHERE idequipe ='".$row[0]."')");
		$pointequipe = round ($sumjoueur/$row[1],2);
		$query2 = "update classement_equipe set points = ".$pointequipe." where idequipe='".$row[0]."'";		
		// fin nico 11/06
		$result2 = mysql_query($query2, $link);
		if (!$result2)
			my_error($link, $query);
	}
	/* Remplit les places */
	$query = "select idequipe, place from `classement_equipe` order by points desc";
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
		$query = "update `classement_equipe` set place='".$i."', progression='".$progression."' where idequipe='".$row[0]."'";
		$result2 = mysql_query($query, $link);
		if (!$result2)
			my_error($link, $query);
		$i++;
	}
}

//Fonction qui renvoi la cote d'un match
function renvoiLaCote($idMatch) {
	$resultat = array();
	$link = get_mysql_link();	
		$query2 = "select count(idmatch) from pari where score1 = score2 and idmatch ='".$idMatch."'";
		$query3 = "select count(idmatch) from pari where score1 > score2 and idmatch ='".$idMatch."'";
		$query4 = "select count(idmatch) from pari where score1 < score2 and idmatch ='".$idMatch."'";
		//Nb de 1
		$result2 = mysql_query($query3, $link);
		if (!$result2)
			my_error($link, $query3);
		while($row = mysql_fetch_array($result2, MYSQL_NUM)){
			$nb1 = intval($row[0]);
		}
		//Nb de N
		$result2 = mysql_query($query2, $link);
		if (!$result2)
			my_error($link, $query2);
		while($row = mysql_fetch_array($result2, MYSQL_NUM)){
			$nbN = intval($row[0]);
		}
		//Nb de 2
		$result2 = mysql_query($query4, $link);
		if (!$result2)
			my_error($link, $query4);
		while($row = mysql_fetch_array($result2, MYSQL_NUM)){
			$nb2 = intval($row[0]);
		}
		//NB total
		$nbTotal = $nb1+$nbN+$nb2;
		//calcul de la cote 1
		if ($nb1!=0) $resultat[0]=round(1/($nb1/$nbTotal));
		else $resultat[0] = $nbTotal;
		//calcul de la cote N
		if ($nbN!=0) $resultat[1]=round(1/($nbN/$nbTotal));
		else $resultat[1] = $nbTotal;
		//calcul de la cote 2
		if ($nb2!=0) $resultat[2]=round(1/($nb2/$nbTotal));
		else $resultat[2] = $nbTotal;
		//mysql_query("insert into debug values('".$nbTotal." ".$resultat[0]." ".$resultat[1]." ".$resultat[2]."')", $link);
		return $resultat;
}


function calculCoteMatch() {
	global $pointcagnotte;
	global $pointcagnotte8;
	global $pointcagnotte4;
	global $pointcagnotte2;
	global $pointcagnotte1;
	$link = get_mysql_link();
	$resultat = array(array());
	$query = "SELECT m.idMatch,count(p.idMatch) FROM `match` m left outer join  `pari` p on m.idMatch = p.idMatch  where m.resultat is null group by m.idMatch ORDER BY idMatch";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$j=$row[0];
		$resultat[$j][0] = $j;
		$resultat[$j][4] = $row[1];
		$cote = renvoiLaCote($j);
		$minCote=$cote[0];
		if ($minCote>$cote[1]) $minCote=$cote[1];
		if ($minCote>$cote[2]) $minCote=$cote[2];
		//Calcul pour les 1
		if($minCote==$cote[0]) $resultat[$j][1] = "<FONT color=\"blue\"><B>".$cote[0]."</B></font>";
		else $resultat[$j][1] = $cote[0];
		//Calcul pour les N
		if($minCote==$cote[1]) $resultat[$j][2] = "<FONT color=\"blue\"><B>".$cote[1]."</B></font>";
		else $resultat[$j][2] = $cote[1];
		//Calcul pour les 2
		if($minCote==$cote[2]) $resultat[$j][3] = "<FONT color=\"blue\"><B>".$cote[2]."</B></font>";
		else $resultat[$j][3] = $cote[2];
	}
	return $resultat;
}

// Fin nico 25/05
// Debut Nico 29/05
function inscritJoueur() {
	echo "<form action=\"inscrit.php\" method=\"post\">";
	echo "<table class=\"tableau\">";
	echo "<tr class=\"beige\"> <td> Login : </td>";
    echo "<td width=\"30%\"><input type=\"text\" tabindex=\"1\" name=\"id\"/>";
	echo "</td></tr>";
	echo "<tr class=\"beige\"> <td> Pr&eacute;nom : </td>";
    echo "<td width=\"30%\"><input type=\"text\" tabindex=\"1\" name=\"prenom\"/>";
	echo "</td></tr>";
	echo "<tr class=\"beige\"> <td> Nom : </td>";
    echo "<td width=\"30%\"><input type=\"text\" tabindex=\"1\" name=\"nom\"/>";
	echo "</td></tr>";
	echo "<tr class=\"beige\"> <td> Mot de passe : </td>";
    echo "<td width=\"30%\"><input type=\"password\" tabindex=\"1\" name=\"pass\"/>";
	echo "</td></tr>";
	echo "<tr class=\"beige\"> <td> &nbsp; </td>";
	echo "<td><input type=\"submit\" value=\"Inscrire\"/>";
	echo "</table>";
	echo "</form>";
}

function ecrireMatch() {

	echo "<form name=\"formu\" action=\"ecrireMatch.php\" method=\"post\">";
	echo "<table class=\"tableau\">";
	
	//deb - rem - 22/06/2008
	/*
	echo "<tr class=\"beige\"> <td> Match : </td>";
	echo "<td width=\"30%\"><input type=\"text\" tabindex=\"1\" name=\"match\"/>";
	echo "</td></tr>";
	*/

	//récupération des équipes
	$link = get_mysql_link();
	$query = "SELECT nom FROM `equipe` order by nom";
	echo "<tr class=\"beige\"><td width=\"65%\">Match :</td><td>";
	for ($cpt=1; $cpt<3; $cpt++) {
		$result = mysql_query($query, $link);
			echo "<select name=\"equipe" . $cpt . "\">";
				echo "<option value=" . "" . " > " . " " . "</option>";
				while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
					echo "<option value=\"" . $row[0] . "\" > " . $row[0] . "</option>";	
				}
			echo "</select>";
		if ($cpt == 1) echo " - ";
	}
	echo "</td></tr>";
	//fin - rem - 22/06/2008

	echo "<tr class=\"beige\"> <td> Type : </td>";
    echo "<td width=\"30%\"><select name=\"type\"><option value=\"0\" selected>Match de poule</option><option value=\"1\">Huitieme</option><option value=\"2\" selected>Quart</option><option value=\"3\" selected>Demie</option><option value=\"4\" selected>Finale</option></select>";

	echo "</td></tr>";
	echo "<tr class=\"beige\"> <td> Date et Heure : </td>";
    echo "<td width=\"30%\"><select name=\"annee\"><option value=\"2016\" selected>2016</option></select><select name=\"mois\"><option value=\"1\">1</option><option value=\"2\">2</option><option value=\"3\">3</option><option value=\"4\">4</option><option value=\"5\">5</option><option value=\"6\" selected>6</option><option value=\"7\">7</option><option value=\"8\">8</option><option value=\"9\">9</option><option value=\"10\">10</option><option value=\"11\">11</option><option value=\"12\">12</option></select><select name=\"jours\"><option value=\"1\">1</option><option value=\"2\">2</option><option value=\"3\">3</option><option value=\"4\">4</option><option value=\"5\">5</option><option value=\"6\">6</option><option value=\"7\">7</option><option value=\"8\">8</option><option value=\"9\">9</option><option value=\"10\">10</option><option value=\"11\">11</option><option value=\"12\">12</option><option value=\"13\">13</option><option value=\"14\">14</option><option value=\"15\" selected>15</option><option value=\"16\">16</option><option value=\"17\">17</option><option value=\"18\">18</option><option value=\"19\">19</option><option value=\"20\">20</option><option value=\"21\">21</option><option value=\"22\">22</option><option value=\"23\">23</option><option value=\"24\">24</option><option value=\"25\">25</option><option value=\"26\">26</option><option value=\"27\">27</option><option value=\"28\">28</option><option value=\"29\">29</option><option value=\"30\">30</option><option value=\"31\">31</option></select><input type=\"text\" name=\"heure\" value=\"20:00:00\"/>";
	echo "</td></tr>";
	echo "<tr class=\"beige\"> <td> &nbsp; </td>";
	echo "<td><input type=\"submit\" value=\"Valider\"/>";
	echo "</table>";
	echo "</form>";
}
// fin nico 29/05

//rem - deb - 09.05.08
function ecrirePodium() {
	
	$link = get_mysql_link();
	$query = "SELECT * FROM `classement_final`";
	$result = mysql_query($query, $link);
	$row2 = mysql_fetch_array($result, MYSQL_NUM);

	$query = "SELECT idEquipe, nom FROM `equipe` order by nom";
	
	echo "<form action=\"ecrirePodium.php\" method=\"post\">";
	echo "<table class=\"tableau\">";
	echo "<tr class=\"beige\"> <td width=\"45%\"> Rang </td>";
	echo "<td width=\"45%\">Equipe</td><td>Modifier</td></tr>";

	for ($cpt=1; $cpt<4; $cpt++) { //<4 pour faire apparaître le 3ème
	$result = mysql_query($query, $link);
		if (!$result) my_error($link, $query);
		switch($cpt) {
			case 1 : echo "<tr class=\"vertclair\"><td width=\"45%\">Vainqueur";break;
			case 2 : echo "<tr class=\"vertfonce\"><td width=\"45%\">Finaliste";break;
			case 3 : echo "<tr class=\"vertclair\"><td width=\"45%\">Troisi&egrave;me";
		}
		echo "</td><td width=\"45%\">" . $row2[$cpt-1] . "</td><td>";
		echo "<select name=\"equipe" . $cpt . "\">";
		echo "<option value=" . "" . " > " . " " . "</option>";
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			echo "<option value=" . $row[1] . " > " . $row[1] . "</option>";	
		}
		echo "</select>";
		echo "</td></tr>";
	}
	echo "<tr class=\"vertfonce\"><td></td><td></td>";
	echo "<td width=\"10%\"><input type=\"submit\" value=\"Valider\"/></td></tr>";
	echo "</table>";
	echo "</form>";
}

/* Affiche reste à faire */
function afficheResteAfaire($query1, $query2) {
	$link = get_mysql_link();

	$result1 = mysql_query($query1, $link);
	if (!$result1) my_error($link, $query1);

	$result2 = mysql_query($query2, $link);
	if (!$result2) my_error($link, $query2);
	
	$row1 = mysql_fetch_array($result1, MYSQL_NUM);
	$row2 = mysql_fetch_array($result2, MYSQL_NUM);
	
	$nbButeursChoisis = $row1[0];
	$nbFavorisChoisis = $row2[0];

	if ($nbButeursChoisis < 2 || $nbFavorisChoisis < 1) {
		echo "<br/>";
		echo "<table  class=\"tableau\"'>";	
				
		echo "<tr class=\"beige\" style='background-color:red;'><td style='color:white;'>Attention !</td></tr>";
		
		if ($nbButeursChoisis < 2) {
			echo "<tr class=\"beige\"><td style='color:red;' align=center>";
			echo "Vous n'avez pas choisi vos deux buteurs !";
			echo "</td></tr>";
		}
		
		if ($nbFavorisChoisis < 1) {
			echo "<tr class=\"beige\"><td style='color:red;' align=center>";
			echo "Vous n'avez pas choisi vos deux favoris pour la finale !";
			echo "</td></tr>";
		}
		
		echo "</table>";
		echo "<br/>";
	}
}
//rem - fin - 09.05.08


function recupNomPrenom($login) {
	$link = get_mysql_link();
	$result = executerRequete("select Nom,Prenom from joueurs where login ='".$login."'");
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$NomPrenom = $row[1]." ".$row[0];
	}
	return $NomPrenom;
}

// tir au but et insertion dans la table coupe_rencontre pour affichage
//renvoi le vainqueur
function tiraubut($joueur1,$joueur2,$tour)
{
	$link = get_mysql_link();
	$scoreJ1=0;
	$scoreJ2=0;
	for($i=0;$i<5;$i++){
		$scoreJ1=$scoreJ1+rand(0,1);
		$scoreJ2=$scoreJ2+rand(0,1);	
	}
	while($scoreJ1==$scoreJ2){
		$scoreJ1=$scoreJ1+rand(0,1);
		$scoreJ2=$scoreJ2+rand(0,1);
	}
	//j'update le résultat
	$query = "update coupe_rencontre set tiraubutJ1 = '".$scoreJ1."', tiraubutJ2 = '".$scoreJ2."' where login1 = '".$joueur1."' and login2 = '".$joueur2."' and tour = '".$tour."'";
		echo "req ".$query;
		$result = mysql_query($query, $link);	
		if (!$result)
			my_error($link, $query);
	if($scoreJ1>$scoreJ2){	
		$vainqueur = $joueur1;
	}
	else {
		$vainqueur = $joueur2;
	}
	return $vainqueur;
}

function afficheCreerCoupe(){
	$link = get_mysql_link();
	global $nbMatchTourCoupe;
	$nbtour=0;
	
	//test pour la création de la coupe
	$query= "SELECT valeur from parametres where param = 'tourCoupeCourant'";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query1);
	while ($row = mysql_fetch_array($result, MYSQL_NUM)){
			$nbtour=$row[0];
	}
	if ($nbtour==0){
		echo "<form action=\"creerCoupe.php\" method=\"post\">";
		echo "<input type=\"submit\" value=\"Cr&eacute;er Coupe\"/>";
		echo "</form>";
	}
	// si pas de créaton je suis en cours je regarde si je dois afficher le bouton pour passer au tour d'apres
	else {
		$query1= "SELECT resultat FROM `match` m, coupe_tour_match ctm, parametres p,coupe_tour ct WHERE ctm.id_match=m.idMatch and ctm.id_tour=ct.type_tour and ct.num_tour=p.valeur and p.param='tourCoupeCourant'";
		$result1 = mysql_query($query1, $link);
		if (!$result1)
			my_error($link, $query1);
		$nbmatch=0;
		while ($row1 = mysql_fetch_array($result1, MYSQL_NUM)){
			if(!is_null($row1[0]))
				$nbmatch=$nbmatch+1;
		}
		if ($nbmatch==$nbMatchTourCoupe){
			echo "<form action=\"passerTour.php\" method=\"post\">";
			echo "<input type=\"submit\" value=\"Passer tour\"/>";
			echo "</form>";
		} else {
			$matchajouer=$nbMatchTourCoupe-$nbmatch;
			echo "Il reste ".$matchajouer." match(s) avant de passer au prochain tour";
		}
	}
	
}

function creerCoupe() {
	require_once 'config.php';
	global $nbMatchTourCoupe;
	
	//Récupération du nombre de partcicipants
	$query1="SELECT count(*) from classement";

	$link = get_mysql_link();
	$result = mysql_query($query1, $link);
	if (!$result)
		my_error($link, $query1);
	$participants=0;
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$participants=$row[0];
	}
	
	//Initialisation des libellés des tours
	$tableau[0][0]=2;
	$tableau[0][1]="Finale";	
	$tableau[1][0]=4;
	$tableau[1][1]="Demi Finale";	
	$tableau[2][0]=8;
	$tableau[2][1]="Quart de Finale";	
	$tableau[3][0]=16;
	$tableau[3][1]="8&egrave;me de Finale";	
	$tableau[4][0]=32;
	$tableau[4][1]="16&egrave;me de Finale";	
	$tableau[5][0]=64;
	$tableau[5][1]="32&egrave;me de Finale";	
	
	//Déterminisation du nombre de tours nécessaires sans le tour préliminaire
	$min = $participants-$tableau[0][0];
	$tour = $tableau[0];//tableau du premier tour sans le tour préliminaire
	$indicetour=0;//indice du premier tour sans le tour préliminaire
	for($i=1;$i < 6;$i++){ 
		$s = $participants-$tableau[$i][0];
		if ($s>=0 && $s < $min){
			$min = $s;
			$tour = $tableau[$i];
			$indicetour = $i;
		}
	}

	//récupération des matchs du prochain tour
	$a=0;
	$query3="SELECT idMatch FROM `match` where resultat is null order by dateM asc";
	$result = mysql_query($query3, $link);
	if (!$result)
		my_error($link, $query3);
	while ($a<$nbMatchTourCoupe&&$ma = mysql_fetch_array($result, MYSQL_NUM)){
		$matchtour[$a]=$ma[0];
		$a=$a+1;
	}
	
	$numtour=1;
	//$min est le différence entre le nombre de participants et le nomre de joueur qu'il faut pour le prochain tour
	// Test si besoin d'un tour préléminaire avec $min
	if ($min==0){// pas besoin de tour préléminaire
		$participants1tour=$participants;
		$nomtour=$tour;
		for ($b=0;$b<$nbMatchTourCoupe;$b++){
			$query = "insert into coupe_tour_match(id_tour,id_match) values (".$nomtour.",".$matchtour[$b].")";
			$result = mysql_query($query, $link);	
			if (!$result)
				my_error($link, $query);		
		}
	} else {//besoin d'un tour préléminaire
		$participants1tour = 2*$participants-2*$tour[0];//nb joueurs pour le 1 tour
		$nomtour=1000;
		$query = "insert into coupe_tour(type_tour,nom_tour,num_tour) values (".$nomtour.",'Tour pr&eacute;liminaire',".$numtour.")";
		$result = mysql_query($query, $link);	
		if (!$result)
			my_error($link, $query);
		$numtour=$numtour+1;
		for ($b=0;$b<$nbMatchTourCoupe;$b++){
			$query = "insert into coupe_tour_match(id_tour,id_match) values (".$nomtour.",".$matchtour[$b].")";
			$result = mysql_query($query, $link);	
			if (!$result)
				my_error($link, $query);		
		}
	}
	for ($i=$indicetour;$i>=0;$i--){
		$query = "insert into coupe_tour(type_tour,nom_tour,num_tour) values (".$tableau[$i][0].",'".$tableau[$i][1]."',".$numtour.")";
		$result = mysql_query($query, $link);	
		if (!$result)
			my_error($link, $query);
		$numtour=$numtour+1;
	}
	
	// choisis les partcipants 1er tour
	$query2 = "SELECT c.login, Prenom, Nom FROM `classement` c, joueurs j WHERE j.login = c.login ORDER BY c.points ASC, c.sens ASC, c.exact ASC, c.points_matchs ASC";
	$result = mysql_query($query2, $link);
	if (!$result)
		my_error($link, $query2);

	$j=0;
	$m=0;
	//contrcutions des deux listes de concurrents participants 1 premier tour et exempt
	while ($row = mysql_fetch_array($result, MYSQL_NUM)){
		if ($j < $participants1tour){
			$concurents[$j]=$row;//variable pour concurennts tour préliminaire
		} else {
				$concurents2[$m]=$row;//variable pour concurennts exempt
				$m=$m+1;
		}
		$j=$j+1;
	}
	$j=0;
	$tirageAdversaire=tirage_sort($participants1tour/2,$participants1tour);
	for($i=0;$i < sizeof($concurents)/2;$i++){ 
		$match[$i][0] = $concurents[$i];
		$match[$i][1] = $concurents[$tirageAdversaire[$i]];
		$query = "insert into coupe_rencontre(login1,login2,tour) values ('".$match[$i][0][0]."','".$match[$i][1][0]."',".$nomtour.")";
		$result = mysql_query($query, $link);	
		if (!$result)
			my_error($link, $query);	
	}
	for ($m=0;$m < sizeof($concurents2);$m++){ 
		$query = "insert into coupe_rencontre(login1,login2,tour) values ('".$concurents2[$m][0]."','null',".$nomtour.")";
		$result = mysql_query($query, $link);	
		if (!$result)
			my_error($link, $query);	
	}
	
	//Update le premier numero de tour dans Paramètre
	$query = "update parametres set valeur ='1' where param ='tourCoupeCourant' ";
	$result = mysql_query($query, $link);	
	if (!$result)
		my_error($link, $query);
}

function puissance($x,$y)
 { 
  $resultat=1;
  for ($i=0;$i<$y;$i++)
   $resultat *= $x;
  return $resultat;
 }
 
 
 
 function recuperationPointCoupe($tour,$joueur)
 { 
	$link = get_mysql_link();
	$query = "SELECT sum( p.points_total ) FROM coupe_tour_match ctm, pari p WHERE ctm.id_tour =".$tour." AND p.idmatch = ctm.id_match AND p.login = '".$joueur."'";
	$result = executerRequete($query);
	$pointsJ1=0;
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$points=$row[0];
	}
	if ($points == null)
		$points = 0;
	return $points;
 }
 
//tirage au sort des tours de coupe suivant un inertvalle
//renvoi un tableau avec la liste des numeros tirés
function tirage_sort($min, $max){
  $nbtour=0;
  $verif = array();//tableau des nombres déja tirés
  $retour = array();//tableau retour
  for($i=0;$i<$max-$min;$i++){
	$tirage[$i]=$min+$i;//construction du tableau avec les numeros a tirer
	$verif[$i]=0;
	$nbtour=$nbtour+1;
  }
  for ($j=0; $j<$nbtour; $j++){
		$aleas = array_rand($tirage);
		if ($verif[$aleas]==1) {
		$j--;                   
		} else {
		$verif[$aleas]=1;
		$retour[$j] = $tirage[$aleas];
		}
	}
return $retour;
}

function affichePosClassementSolo($joueur){
	$link = get_mysql_link();
	$query = "SELECT place,progression FROM classement WHERE login = '".$joueur."'";
	$result = executerRequete($query);
	$points=0;
	$progression="=";
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$points=$row[0];
		$progression=$row[1];
	}
	echo "<table width=100% height=100%>";
	if($points!=0){
		echo "<tr class=\"celClassement\"><td>";
		echo $points;
		if($points == 1) {echo "<sup>er</sup>";} else {echo "<sup>&egrave;me</sup>";}
		} else echo "---";
		echo " (".$progression.")</td></tr>";
		echo "<tr class=\"celClassementLib\"><td>Classement Individuel</td></tr></table>";
	
}

function affichePosClassementEquipe($joueur){
	$link = get_mysql_link();
	$query = "SELECT clas.place,clas.progression FROM classement_equipe clas,joueur_equipe j WHERE j.idequipe = clas.idequipe and j.login = '".$joueur."'";
	$result = executerRequete($query);
	$points=0;
	$progression="=";
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$points=$row[0];
		$progression=$row[1];
	}
	
	echo "<table width=100% height=100% >";
	if($points!=0){
		echo "<tr class=\"celClassement\"><td>";
		echo $points;
		if($points == 1) {echo "<sup>er</sup>";} else {echo "<sup>&egrave;me</sup>";}
		} else echo "---";
		echo " (".$progression.")</td></tr>";
	echo "<tr class=\"celClassementLib\"><td>Classement Equipe</td></tr></table>";
}

function afficheClassementResume($login){
	$link = get_mysql_link();
	$query="SELECT place, c.login, j.prenom, j.nom, c.points, c.progression, c.nb_match FROM `classement` c, joueurs j, pari p WHERE j.login = c.login AND j.login = p.login AND p.sens IS NOT NULL GROUP BY c.login ORDER BY c.points DESC, c.sens DESC, c.exact DESC, c.points_matchs DESC";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	echo "<table class=\"classementlight\">";
	echo "<tr>";
	echo "<td>Place</td>";
	echo "<td width=\"40%\">Nom</td>";
	echo "<td>Points</td>";
	echo "<td>Prog &darr; = &uarr;</td>";
	echo "<td>Nb match</td>";
	echo "</tr>";
    $i = 1;
	$nbjoueur = mysql_num_rows($result);
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		if ($i<=3) {
			echo "<tr onclick=\"window.location.href='accueil.php?visualiser=" . $row[1] . "'\"";
			//la ligne du joueur connecté en bleue
			if ($row[1] == $login) {
				echo " style='color:blue'";
			}
			if (($i % 2)==0){
				echo " class=\"teteclassementf\">";
			} else {
				echo " class=\"teteclassementc\">";
			}
			echo "<td>" . $i . "</td>";
			echo "<td>" . $row[2] . " " . /*substr($row[3],0,2)*/$row[3] . "</td>";
			echo "<td>" . $row[4] . "</td>";
			echo "<td>" . $row[5] . "</td>";
			echo "<td>" . $row[6] . "</td>";
			echo "</tr>";
		}
		if ($row[1] == $login && $i>3) {
				echo "<tr onclick=\"window.location.href='accueil.php?visualiser=" . $row[1] . "'\"";
				echo " style='color:blue'";
				if (($i % 2)==0){
					echo " class=\"teteclassementf\">";
					} else {
					echo " class=\"teteclassementc\">";
					}
				echo "<td>" . $i . "</td>";
				echo "<td>" . $row[2] . " " . /*substr($row[3],0,2)*/$row[3] . "</td>";
				echo "<td>" . $row[4] . "</td>";
				echo "<td>" . $row[5] . "</td>";
				echo "<td>" . $row[6] . "</td>";	
		}
		if ($i>$nbjoueur-3 && $row[1]!= $login) {
			echo "<tr onclick=\"window.location.href='accueil.php?visualiser=" . $row[1] . "'\"";
			//la ligne du joueur connecté en bleue
			if ($row[1] == $login) {
				echo " style='color:blue'";
				$flagjoueur=true;
			}
			if (($i % 2)==0){
				echo " class=\"teteclassementf\">";
			} else {
				echo " class=\"teteclassementc\">";
			}
			echo "<td>" . $i . "</td>";
			echo "<td>" . $row[2] . " " . /*substr($row[3],0,2)*/$row[3] . "</td>";
			echo "<td>" . $row[4] . "</td>";
			echo "<td>" . $row[5] . "</td>";
			echo "<td>" . $row[6] . "</td>";
			echo "</tr>";
		}
		$i++;
	}
	echo "</table>";
}

function afficheProchainParis($login){
	$query="SELECT m.idMatch, lib_match, m.score1, m.score2, dateM, p.score1, p.score2, joker FROM `match` m left outer join  `pari` p on m.idMatch = p.idMatch and p.login='".$login."' where m.resultat is null ORDER BY dateM ASC";
	$link = get_mysql_link();
	$tendance=calculCoteMatch();
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);

	echo "<table class=\"parislight\">";
	echo "<tr class=\"beige\">";
	echo "<td> Match </td>";
	echo "<td> Date du match </td>";
	echo "<td> Paris </td>";
	echo "<td> Joker </td>";
	echo "<td> Cote </td>";
	echo "</tr>";
	$j = 0;
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		if($j<4) {
			echo "<tr class=\"beige\"><td colspan=\"5\"></td></tr>";
			if ($j % 2 == 0) {
				echo "<tr class=\"vertclair\">";
			} else {
				echo "<tr class=\"vertfonce\">";
			}
			if($row[7]==1) {$joker="X";}
			else {$joker="&nbsp;";}
			echo "<td>" . $row[1] . "</td>";
			echo "<td>" . $row[4] . "</td>";
			echo "<td>" . $row[5] . "&nbsp;-&nbsp;" . $row[6] . "</td>";
			echo "<td>" . $joker . "</td>";
			echo "<td><table class=\"tendancemainpage\"><tr><td>".$tendance[$row[0]][1]."</td><td>".$tendance[$row[0]][2]."</td><td>".$tendance[$row[0]][3]."</td></tr></table>";
			echo "</tr>";
			$j++;
			}
	}
	echo "</table><table><tr class=\"celClassementLib\"><td width=\"90%\">Prochains paris</td><td><a href=\"paris.php\" class=\"bouton\">Parier</a></td></tr></table>";
}

function afficheEtatCoupe($login){
$link = get_mysql_link();
	$query ="SELECT login1,login2,nom_tour FROM `coupe_rencontre`,coupe_tour WHERE tour=type_tour and tour=(select type_tour from coupe_tour,parametres where num_tour = valeur and param ='tourCoupeCourant')";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	echo "<table align=\"center\" height=\"80%\">";
	$j = 0;
	$flagelimine=true;
	echo "<tr>";
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$j++;
		if (($login == $row[0] or $login == $row[1]) and ($row[1] != "null") ) {
			echo "<td>Qualifi&eacute; en ". $row[2] . "</td>";
			$flagelimine=false;
		}
		
		if ($login == $row [0] and  $row[1] == "null") {
			echo "<td>Exempt</td>";
			$flagelimine=false;
		}
		
	}
	if ($flagelimine && $j>0) {
		echo "<td>Elimin&eacute;</td>";
	}
	if ($j == 0) {
		echo "<td>Tirage au sort prochainement</td>";
	}
	echo "</tr>";
	echo "</tr>";
	echo "</table>";
}

function afficheForummainpage(){
	$query = "SELECT f.login,nom,prenom,message,heure FROM forum f,joueurs j WHERE j.login = f.login order by heure desc";
	$result = executerRequete($query);
	echo "<MARQUEE SCROLLAMOUNT=\"4\">";
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		echo " ######----> ".$row[2]." ".$row[1]." : ";
		echo " ".$row[3];
	}
echo "</MARQUEE>";
}


function affichejoueursparbuteur( $idbute){
	require_once ("common.php");

				if ($idbute == null){
						$req1 = executerRequete("SELECT b.prenom, b.nom, b.idbuteur FROM `buteur` b");
				} else {
						$req1 = executerRequete("SELECT b.prenom, b.nom, b.idbuteur FROM `buteur` b WHERE b.idbuteur = '".$idbute."'");
				}
				while ($rowreq1 = mysql_fetch_array($req1, MYSQL_NUM)) {
					$nombuteur = $rowreq1[0]." ".$rowreq1[1];
					$idb = $rowreq1[2];
					$result = executerRequete("SELECT j.Prenom, j.Nom, c.place, j.login FROM joueurs j, choisi_buteur cb, classement c WHERE j.login=cb.login and j.login=c.login AND cb.idbuteur='" . $idb . "' order by c.place");
					$nbb = nbButsButeur($idb);
					echo "<table class=\"tableau\" width=80>";
					echo "<tr class=\"beige\">";
					echo "<td width=\"70%\">".$nombuteur."</td>\n";
					echo "<td>".$nbb." but(s) </td></tr>\n";
					echo "<tr class=\"beige\"><td> Joueurs </td><td> Place </td></tr>";
					
					$i = 1;
					while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
						if (($i % 2) == 1) {
							echo "<tr onclick=\"window.location.href='accueil.php?visualiser=" . $row[3] . "'\" class=\"vertclairLien\" width=\"50%\">";
						} else {
							echo "<tr onclick=\"window.location.href='accueil.php?visualiser=" . $row[3] . "'\" class=\"vertfonceLien\" width=\"50%\">";
						}
						echo "<td width=\"70%\">" . $row[0]. " ". $row[1]. " </td><td>". $row[2] . "</td>";
						echo "</tr>\n";
					$i++;
					}
				}
				// fin nico 25/05
			echo "</table>";
	}

function affichejoueursparequipe( $idequipe){
	require_once ("common.php");

				if ($idequipe == null){
						$req1 = executerRequete("SELECT p.equipe1 FROM prono_classement_final p group by p.equipe1");
						$req2 = executerRequete("SELECT p.equipe2 FROM prono_classement_final p group by p.equipe2");
						
				} else {
						$req1 = executerRequete("SELECT p.equipe1 FROM prono_classement_final p WHERE p.equipe1 ='".$idequipe."' group by p.equipe1");
						$req2 = executerRequete("SELECT p.equipe2 FROM prono_classement_final p WHERE p.equipe2 ='".$idequipe."' group by p.equipe2");
				}
				$i = 1;
				while ($rowreq1 = mysql_fetch_array($req1, MYSQL_NUM)) {
					$nomequipe = $rowreq1[0];
					
					echo "<table class=\"tableau\">";
					echo "<tr class=\"beige\">";
					echo "<td width=\"70%\">".$nomequipe."</td>\n";
					echo "<td> Vainqueur </td></tr>\n";
					echo "<tr class=\"beige\"><td> Joueurs </td><td> Place </td></tr>";
					
					$req3 = executerRequete("SELECT j.Prenom, j.Nom, j.login, c.place, p.equipe1 FROM prono_classement_final p, classement c, joueurs j WHERE c.login=p.login and c.login=j.Login and p.equipe1 ='".$nomequipe."' order by c.place");
				
					$i = 1;
					while ($rowreq3 = mysql_fetch_array($req3, MYSQL_NUM)) {
					
						if (($i % 2) == 1) {
							echo "<tr onclick=\"window.location.href='accueil.php?visualiser=" . $rowreq3[2] . "'\" class=\"vertclairLien\" width=\"50%\">";
						} else {
							echo "<tr onclick=\"window.location.href='accueil.php?visualiser=" . $rowreq3[2] . "'\" class=\"vertfonceLien\" width=\"50%\">";
						}
						echo "<td width=\"70%\">" . $rowreq3[0]. " ". $rowreq3[1]. " </td><td>". $rowreq3[3]. "</td>";
						echo "</tr>\n";
					$i++;
					}
				}
				while ($rowreq2 = mysql_fetch_array($req2, MYSQL_NUM)) {
					$nomequipe = $rowreq2[0];
					
					echo "<table class=\"tableau\">";
					echo "<tr class=\"beige\">";
					echo "<td width=\"70%\">".$nomequipe."</td>\n";
					echo "<td > Finaliste </td></tr>\n";
					echo "<tr class=\"beige\"><td> Joueurs </td><td> Place </td></tr>";
					
					$req4 = executerRequete("SELECT j.Prenom, j.Nom, j.login, c.place, p.equipe2 FROM prono_classement_final p, classement c, joueurs j WHERE c.login=p.login and c.login=j.Login and p.equipe2 ='".$nomequipe."' order by c.place");
				
					$i = 1;
					while ($rowreq4 = mysql_fetch_array($req4, MYSQL_NUM)) {
					
						if (($i % 2) == 1) {
							echo "<tr onclick=\"window.location.href='accueil.php?visualiser=" . $rowreq4[2] . "'\" class=\"vertclairLien\" width=\"50%\">";
						} else {
							echo "<tr onclick=\"window.location.href='accueil.php?visualiser=" . $rowreq4[2] . "'\" class=\"vertfonceLien\" width=\"50%\">";
						}
						echo "<td width=\"70%\">" . $rowreq4[0]. " ". $rowreq4[1]. " </td><td>". $rowreq4[3]. "</td>";
						echo "</tr>\n";
					$i++;
					}

				}
				
				// fin nico 25/05
			echo "</table>";
	}


?>