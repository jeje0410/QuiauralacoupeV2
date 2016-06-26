<?php

function javascriptControleMessage() {
	echo "\n<script>\n";
	echo "function verifMessage(){\n";
	echo "var letterNumber = /^[0-9a-zA-Z\s*\?;,éèê'à!:.ç]+$/;\n";
	echo "if(document.forms[0].message.value.match(letterNumber))\n";   
	echo "{\n";
    echo "return true;\n";
	echo "}\n";
	echo "else\n";
	echo "{\n";
	echo "alert('Veuillez saisir un message valide');\n";
	echo "return false;\n";
	echo "}\n";
	echo "}\n";
	echo "</script>\n";

}

function afficheForum($update){
	require_once ("common.php");
	$query = "SELECT f.login,nom,prenom,message,heure FROM forum f,joueurs j WHERE j.login = f.login order by heure desc";
	$result = executerRequete($query);
	echo "<table width=100% height=90% class=\"tableauForum\">";
	echo "<tr><td width=\"15%\">Nom</td><td width=\"70%\">Message</td><td width=\"15%\">Heure</td></tr>";
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		echo "<tr class=\"celluletableauforum\"><td>".$row[1]." ".$row[2]."</td>";
		echo "<td>".$row[3]."</td>";
		echo "<td>".$row[4]."</td></tr>";
	}
	echo "</table>";
	if ($update == "read") {
				echo "<form action=\"savemessage.php\" method=\"post\">";
						echo "<input type=\"hidden\" name=\"update\" value=\"write\">";
						echo "<input type=\"submit\" value=\"Ajouter un message\" class=\"bouton\"/>";
				echo "</form>";
			} else {
				//Génération de la méthode javascript permettant de contrôler le message
				javascriptControleMessage();
				//L'utilisateur a cliqué sur Modifier, on lui affiche le message
				echo "<form action=\"savemessage.php\" method=\"post\" onsubmit=\"return verifMessage()\">";
				echo "<input type=\"hidden\" name=\"update\" value=\"read\"/>";
				echo "<textarea name=\"message\" cols=\"50\" rows=\"4\" maxlength=\"200\"> </textarea>";
				echo " <input type =\"submit\" value=\"Enregistrer\" class=\"bouton\"/>";
				echo "</form>";
				}		
}


require_once 'banniere.php';
if (!isset ($update)) {
	$update = "read";
}
afficheForum($update);

require_once 'footer.php';
?>