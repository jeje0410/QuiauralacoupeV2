<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<HTML xmlns="http://www.w3.org/1999/xhtml">

<head>
<link title="coupedumonde" type="text/css" rel="stylesheet" href="css/coupedumonde.css"/>

<title>Saisie des buteurs du match</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

</head>
<body>
<?php
require_once 'common.php';

$idMatch = $_GET['idMatch'];
$libMatch = $_GET['libMatch'];
$score1 = $_GET['score1'];
$score2 = $_GET['score2'];
//rem - deb - 17.04.08
$filtre = $_GET['score2'];
//rem - fin - 17.04.08

echo "<table width=\"100%\">\n";
echo "<tr>\n";
echo "<td align=\"center\">\n";
echo "<form name=\"buteurs\" action=\"savebuteuradmin.php\" method=\"post\"> \n";
$scoretotal = $score1 + $score2;
echo "<input type=\"hidden\" name=\"nbButeurs\" value=\"" . $scoretotal . "\">\n";
echo "<input type=\"hidden\" name=\"idMatch\" value=\"" . $idMatch . "\">\n";
//rem - deb - 17.04.08
echo "<input type=\"hidden\" name=\"filtre\" value=\"" . $filtre . "\">";
//rem - fin - 17.04.08
echo $libMatch;
echo "</td>\n";
echo "</tr>";
echo "</table>\n";
echo "<br><br>\n";
echo "<table width=\"90%\">\n";
echo "<tr>\n";
echo "<td width=\"50%\" valign=\"top\">\n";
echo "<table width=\"100%\">\n";
$i = 0;
while ($i < $score1) {
	echo "<tr>\n";
	echo "<td>\n";
	selectButeursMatch(0, $i, $libMatch);
	$i++;
	echo "</td>\n";
	echo "</tr>";
}
echo "</table>\n";
echo "</td>\n";
echo "<td width=\"50%\" valign=\"top\">\n";
echo "<table width=\"100%\">\n";
while ($i < $score1 + $score2) {
	echo "<tr>\n";
	echo "<td>\n";
	selectButeursMatch(0, $i, $libMatch);
	$i++;
	echo "</td>\n";
	echo "</tr>";
}
echo "</table>\n";
echo "</td>\n";
echo "</tr>";
echo "</table>\n";
echo "<br><br><br>\n";
echo "<table width=\"100%\">\n";
echo "<tr>\n";
echo "<td align=\"center\">\n";
echo "<input type=\"submit\" value=\"Enregistrer\"";
echo "</td>\n";
echo "</tr>";
echo "</table>\n";
echo "</form>\n";
?>
</body>
</HTML>
