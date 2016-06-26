<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<HTML xmlns="http://www.w3.org/1999/xhtml">

<head>
<link title="coupedumonde" type="text/css" rel="stylesheet" href="css/coupedumonde.css"/>

<title>R&eacute;sultats</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

</head>
<body>
<?php
require_once 'common.php';
if (isset($_GET['idMatch'])) {
	$idmatch = $_GET['idMatch'];
}
$query="SELECT m.idMatch, m.lib_match, m.score1, m.score2, b.nom, b.prenom, e.nom FROM `match` m
LEFT OUTER JOIN `buteur_match` bm ON  m.idMatch = bm.idMatch
LEFT OUTER JOIN `buteur` b ON bm.idbuteur = b.idbuteur 
LEFT OUTER JOIN `equipe` e ON b.idEquipe = e.idEquipe WHERE m.idMatch='".$idmatch."'";
afficheResultat($query);
?>
</body>
</HTML>
