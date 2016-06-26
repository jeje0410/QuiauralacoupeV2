<h3> Classement par &eacute;quipes</h3>
<?php
$query1="SELECT place, nomequipe, points, progression, nbjoueur, c.idequipe FROM `classement_equipe` c, `descr_equipe` d where c.idequipe=d.idequipe order by place asc";
$query2="SELECT nomequipe FROM `joueur_equipe` j, `descr_equipe` d where j.idequipe=d.idequipe and j.login='".$login."'";

afficheClassementEquipe($query1,$query2);


function afficheClassementEquipe($query, $query2) {
	require_once 'common.php';
	$link = get_mysql_link();

	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);

	$result2 = mysql_query($query2, $link);
	if (!$result2)
		my_error($link, $query);
	while ($row = mysql_fetch_array($result2, MYSQL_NUM)) {
		$nomequipelogin = $row[0];
	}

	echo "<table class=\"tableau\">";
	echo "<tr class=\"beige\">";
	echo "<td> Place </td>";
	echo "<td> Nom </td>";
	echo "<td> Points en moyenne</td>";
	echo "<td> Progression</td>";
	echo "</tr>";
	// nico 08/2006
	$i = 1;
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {

		
		// nico 08/2006
		if ($i <2) { 
			echo "<tr  onclick=\"window.location.href='compoequipe.php?idequip=".$row[5]."'\" class=\"teteclassementf\" ";
		} else {
		if ($i % 2 == 1){
			echo "<tr onclick=\"window.location.href='compoequipe.php?idequip=".$row[5]."'\" class=\"vertclairLien\" ";
		}
		else {
			echo "<tr onclick=\"window.location.href='compoequipe.php?idequip=".$row[5]."'\" class=\"vertfonceLien\" ";
		}
		}
		if ($row[1]==$nomequipelogin) {
			echo "style='color:blue' ";
		}
		echo " >";
		echo "<td>" . $row[0] . "</td>";
		echo "<td>" . $row[1] . "</td>";
		echo "<td>" . $row[2] . "</td>";
		echo "<td>" . $row[3] . "</td>";
		echo "</tr>";

		$i++;
	}
	echo "</table>";
}
?>