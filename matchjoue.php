<h3>Mes points</h3>

<?php
$query="SELECT m.idMatch, lib_match, m.score1, m.score2, dateM, p.score1, p.score2, joker, points_match, points_cagnotte, points_buteur, points_total FROM `match` m, `pari` p where m.idMatch = p.idMatch and m.resultat is not null and p.login='".$login."' order by dateM desc";
afficheTableauScoreJoue($query);
?>

