<h3>Mes paris</h3>
<?php
$query="SELECT m.idMatch, lib_match, m.score1, m.score2, dateM, p.score1, p.score2, joker, points_total FROM `match` m left outer join  `pari` p on m.idMatch = p.idMatch and p.login='".$login."' where m.resultat is null ORDER BY dateM ASC";
$idmatch = null;
if (isset($_GET['idmatch'])) {
	$idmatch = $_GET['idmatch'];
}
afficheTableauScoreAJoue($query, $idmatch);

?>