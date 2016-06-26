<h3> Classement individuel</h3>
<?php
$query="SELECT place, c.login, j.prenom, j.nom, c.points, c.points_matchs, c.points_cagnotte, c.points_buteurs, c.sens, c.exact, c.progression, c.nb_match, c.Points_classement_final, sum( p.joker ) FROM `classement` c, joueurs j, pari p WHERE j.login = c.login AND j.login = p.login AND p.sens IS NOT NULL GROUP BY c.login ORDER BY c.points DESC, c.sens DESC, c.exact DESC, c.points_matchs DESC";
afficheClassementSolo($query,$login);
?>