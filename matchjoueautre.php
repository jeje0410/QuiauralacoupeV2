

<?php
$joueur = $_GET['visualiser'];
$nom = recupNomPrenom($joueur);
echo "<h3>Les paris de ".$nom."</h3>";
echo "<h3> Ses favoris</h3>";
$query="SELECT equipe1, equipe2, equipe3 FROM `prono_classement_final` p WHERE p.login='" . $joueur . "'";
afficheFavoris2($query);
echo "<h3> Ses buteurs</h3>";
$query="select b.nom, b.prenom, e.nom, b.idbuteur from `choisi_buteur` c join `buteur` b on c.idbuteur = b.idbuteur join `equipe` e on b.idEquipe = e.idEquipe where c.login='".$joueur."' group by b.idbuteur order by b.nom";
afficheButeur2($query);
echo "<h3> Ses points</h3>";
$query="SELECT m.idMatch, lib_match, m.score1, m.score2, dateM, p.score1, p.score2, joker, points_match, points_cagnotte, points_buteur, points_total FROM `match` m, `pari` p where m.idMatch = p.idMatch and dateM < sysdate() and p.login='".$joueur."' order by dateM desc";
afficheTableauScoreJoue($query);

?>

