<?php
require_once 'banniere.php';
echo "<br><h3>Palmares</h3>";
affichePalmares();

function affichePalmares(){
	require_once 'common.php';
	$query = "select j.Nom, j.Prenom, p.champion,p.nbreVainqueurD2 ,p.vainqueurCoupe,p.nbrefoisD1,p.nbrefoisD2 from palmares p, `joueurs` j where j.login = p.login order by p.champion DESC, p.nbreVainqueurD2 DESC";
	$link = get_mysql_link();
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	echo "<table class=\"tableau\">";
	echo "<tr class=\"beige\"><td colspan=3>Le nombre de victoire</td></tr>";
	echo "<tr class=\"vertclair\"><td width=\"33%\">Nom</td><td width=\"33%\"><img src=\"images/tropLigue1.jpg\"></td><td width=\"33%\"><img src=\"images/tropLigue2.jpg\"></td></tr>";
	$i=0;
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		if($row[2]!=0 or $row[3]!=0){
		if($i%2==1) {
		echo "<tr class=\"vertclair\"><td width=\"33%\">".$row[0]." ".$row[1]. "</td><td width=\"33%\">".$row[2]."</td><td width=\"33%\">".$row[3]."</td></tr>";
		} else { echo "<tr class=\"vertfonce\"><td width=\"33%\">".$row[0]." ".$row[1]. "</td><td width=\"33%\">".$row[2]."</td><td width=\"33%\">".$row[3]."</td></tr>";}
		$i++;}
		
	}
	echo "</table>";
	echo "<br>";
	$query = "select d.LoginVainqueurD1,d.deuxieme,d.troisieme,d.LoginVainqueurD2,d.LoginVainqueurCoupe,d.idChampionnat,d.NomChampionnat from derniervainqueur d order by d.idChampionnat DESC";
	$link = get_mysql_link();
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	echo "<table class=\"tableau\">";
	echo "<tr class=\"beige\"><td colspan=3>Les Derniers Vainqueurs</td></tr>";
	echo "<tr class=\"vertclair\"><td width=\"33%\">Nom du championnat</td><td width=\"33%\"><img src=\"images/tropLigue1.jpg\"></td><td width=\"33%\"><img src=\"images/tropLigue2.jpg\"></td></tr>";
	$i=0;
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$NomPrenomD1 = recupNomPrenom($row[0]);
		$NomPrenom2D1 = recupNomPrenom($row[1]);
		$NomPrenom3D1 = recupNomPrenom($row[2]);
		$NomPrenomD2 = recupNomPrenom($row[3]);
		if($i%2==1) {
		echo "<tr class=\"vertclair\"><td width=\"33%\">".$row[6]."</td><td align=\"left\" width=\"33%\">
			<table>
				<tr>
					<td width=\"50%\" align=\"right\">
						1.
					<td/>
					<td align=\"left\">
						".$NomPrenomD1."
					</td>
				</tr>
				<tr>
					<td width=\"50%\" align=\"right\">
						2.
					<td/>
					<td align=\"left\">
						".$NomPrenom2D1."
					</td>
				</tr>
				<tr>
					<td width=\"50%\" align=\"right\">
						3.
					<td/>
					<td align=\"left\">
						".$NomPrenom3D1."
					</td>
				</tr>
			</table>		
</td><td width=\"33%\">".$NomPrenomD2."</td></tr>";
		} else { echo "<tr class=\"vertfonce\"><td width=\"33%\">".$row[6]."</td>
		<td width=\"33%\">
			<table>
				<tr>
					<td width=\"50%\" align=\"right\">
						1.
					<td/>
					<td align=\"left\">
						".$NomPrenomD1."
					</td>
				</tr>
				<tr>
					<td width=\"50%\" align=\"right\">
						2.
					<td/>
					<td align=\"left\">
						".$NomPrenom2D1."
					</td>
				</tr>
				<tr>
					<td width=\"50%\" align=\"right\">
						3.
					<td/>
					<td align=\"left\">
						".$NomPrenom3D1."
					</td>
				</tr>
			</table>
		</td>
		<td width=\"33%\">".$NomPrenomD2."</td></tr>";}
		$i++;
	}
	echo "</table>";
	echo "<br>";
}

require_once 'footer.php';
?>