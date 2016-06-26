<?php
require_once 'banniere.php';

$idequip = null;
if (isset ($_GET["idequip"]))
		$idequip = $_GET["idequip"];


afficheEquipeJoueur($login, $idequip);

function afficheEquipeJoueur($login, $idequip) {
	require_once 'common.php';
	$link = get_mysql_link();
	
	if ($idequip!=null){

		$req = "select d.idequipe, d.nomequipe, c.points from descr_equipe d, classement_equipe c where c.idequipe=d.idequipe and c.idequipe='".$idequip."' ";
		$res = executerRequete($req);
		$eq = mysql_fetch_array($res, MYSQL_NUM);
		echo "<br> <h3>Composition de l'&eacute;quipe ".$eq[1]."</h3>";
		afficheEquipe($eq,$login,$link);
	}
	echo "<br> <h3>Composition de l'ensemble des &eacute;quipes</h3>";
	
	$query = "select d.idequipe, d.nomequipe,c.points from descr_equipe d, classement_equipe c where c.idequipe=d.idequipe order by c.points DESC";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query, $link);
	
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		afficheEquipe($row,$login, $link);

	}	
}
function afficheEquipe ($row, $login, $link){
		echo "<table class=\"tableau\">";
		echo "<tr class=\"beige\">";
		echo "<td width=\"60%\">".$row[1]."</td><td width=\"40%\" colspan=2>Moyenne&nbsp;".$row[2]."</td></tr>";
		echo "<tr class=\"beige\"><td>Joueurs</td><td>Place</td><td>Points</td></tr>";
		// nico 08/06
		$query2 = "select j.login, j.prenom, j.nom,c.points,c.place from joueur_equipe je, joueurs j, classement c where idequipe='".$row[0]."' and j.login = je.login and c.login = j.login order by c.points DESC";
		$result2 = mysql_query($query2, $link);
		if (!$result2)
		my_error($link, $query2);
		$i=0;
		while ($row2 = mysql_fetch_array($result2, MYSQL_NUM)) {
			echo "<tr onclick=\"window.location.href='accueil.php?visualiser=" . $row2[0] . "'\"";
			if (($i % 2)==0){
				echo " class=\"vertclairLien\" ";
			}
			else {
				echo " class=\"vertfonceLien\" ";
			}
			if ($row2[0] == $login) {
				echo "style='color:blue' ";	
			} 
			echo " >";
			echo "<td>" .$row2[1]  ." ".$row2[2]."</td>";
			echo "<td>" . $row2[4] ."</td>";
			echo "<td>" . $row2[3] ."</td>";
			
			echo "</tr>";
			$i++;
		}
		echo "</table>";
		echo "<br>";
}


require_once 'footer.php';
?>