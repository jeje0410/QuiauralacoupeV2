<?php
function selectEquipes($nom, $equipe) {
	require_once 'common.php';
	$link = get_mysql_link();
	// nico 31/05
	$query = "SELECT nom FROM `equipe` order by nom";
	$result = mysql_query($query, $link);
	if (!$result)
		my_error($link, $query);
	echo "<select name=\"" . $nom . "\">";

	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		if ($row[0] == $equipe) {
			echo "<option value=\"" . $row[0] . "\" selected > " . $row[0] . "</option>";
		} else {
			echo "<option value=\"" . $row[0] . "\" > " . $row[0] . "</option>";
		}
	}
	echo "</select>";
}
//DEBUT MODIFICATION LE 10 mai 2006 PAR OCLA

function determinerNbVotant() {
	//Récupération du nombre de personnes ayant voté pour calculer les pourcentages
	$result = executerRequete("SELECT count(*) FROM `prono_classement_final`");
	$ligne = mysql_fetch_array($result, MYSQL_NUM);
	$nbVotes = $ligne[0];

	return $nbVotes;
}

function isPourUtilisateur($nomEquipe, $login, $place) {
	if ($place==1) {$Equipe = executerRequete("select equipe1 from `prono_classement_final` where login = '" . $login . "'");}
	if ($place==2) {$Equipe = executerRequete("select equipe2 from `prono_classement_final` where login = '" . $login . "'");}
	if ($place==3) {$Equipe = executerRequete("select equipe3 from `prono_classement_final` where login = '" . $login . "'");}
	
	while ($row = mysql_fetch_array($Equipe, MYSQL_NUM)) {
		if ($nomEquipe == $row[0]) return true;
	}
	return false;
}

function trStatistiquesChoixVainqueur($nbVotant, $login) {
	require_once 'common.php';

	//Récupération du nombre de personne ayant voté par équipe pour le vainqueur
	$result = executerRequete("SELECT equipe1, count(equipe1) as nb FROM `prono_classement_final` GROUP BY equipe1 order by nb desc");
	
	//Boucle sur les 3 premières équipes sélectionnées
	if ($nbVotant > 0) {
		$i = 0;
		echo "<table width=\"100%\">\n";
		while (($ligne = mysql_fetch_array($result, MYSQL_NUM))) {
		
		$nomEquipe = $ligne[0];
		$countEquipe = $ligne[1];
		
		$isPourUtilisateur = isPourUtilisateur($nomEquipe, $login, 1);
		//Une couleur différente est utilisée pour les équipes choisis par l'utilisateur
			if (peutEncoreParier()){
				if ($i % 2 == 0)
						echo "<tr width=\"100%\" class=vertclair ";
				else
						echo "<tr width=\"100%\" class=vertfonce ";
				
			}	else {
					if ($i % 2 == 0)
						echo "<tr width=\"100%\" onclick=\"window.location.href='detailfavoris.php?idequi=" . $nomEquipe . "'\" class=vertclairLien ";
					else
						echo "<tr width=\"100%\" onclick=\"window.location.href='detailfavoris.php?idequi=" . $nomEquipe . "'\" class=vertfonceLien ";
				}
			if ($isPourUtilisateur)	{
				echo " style='color:blue' >\n";
			}
			else {
				echo " >\n";
			}
			echo "<td width=\"80%\">\n";
			echo $nomEquipe . "\n";
			echo "</td>\n";
			echo "<td>\n";
			echo ceil($countEquipe / $nbVotant * 100) . "%\n";
			echo "</td>\n";
			echo "</tr>";
			echo "<tr class=\"beige\"><td colspan=\"2\"></td></tr>";
			$i++;
		}
		echo "</table>\n";
	}
}

function trStatistiquesChoixFinaliste($nbVotant, $login) {
	require_once 'common.php';

	//Récupération du nombre de personnes ayant voté par équipe pour le finaliste
	$result = executerRequete("SELECT equipe2, count(equipe2) as nb FROM `prono_classement_final` GROUP BY equipe2 order by nb desc");
	//Boucle sur les 3 premières équipes sélectionnées
	if ($nbVotant > 0) {
		$i = 0;
		echo "<table width=\"100%\">\n";
		while (($ligne = mysql_fetch_array($result, MYSQL_NUM))) {
			$nomEquipe = $ligne[0];
			$countEquipe = $ligne[1];
		//echo $nomEquipe.$countEquipe;
			$isPourUtilisateur = isPourUtilisateur($nomEquipe, $login, 2);
			//Une couleur différente est utilisée pour les équipes choisis par l'utilisateur
			if (peutEncoreParier()){
				if ($i % 2 == 0)
						echo "<tr width=\"100%\" class=vertclair ";
				else
						echo "<tr width=\"100%\" class=vertfonce ";
				
			}	else {
					if ($i % 2 == 0)
						echo "<tr width=\"100%\" onclick=\"window.location.href='detailfavoris.php?idequi=" . $nomEquipe . "'\" class=vertclairLien ";
					else
						echo "<tr width=\"100%\" onclick=\"window.location.href='detailfavoris.php?idequi=" . $nomEquipe . "'\" class=vertfonceLien ";
				}
			if ($isPourUtilisateur)	{
				echo " style='color:blue' >\n";
			}
			else {
				echo " >\n";
			}
				echo "<td width=\"80%\">\n";
				echo $nomEquipe . "\n";
				echo "</td>\n";
				echo "<td>\n";
				echo ceil($countEquipe / $nbVotant * 100) . "%\n";
				echo "</td>\n";
				echo "</tr>";
				echo "<tr class=\"beige\"><td colspan=\"2\"></td></tr>";
				$i++;
		}
		echo "</table>\n";
	}
}

function trStatistiquesChoixTroisieme($nbVotant, $login) {
	require_once 'common.php';

	//Récupération du nombre de personnes ayant voté par équipe pour le 3ème
	$result = executerRequete("SELECT equipe3, count(equipe3) as nb FROM `prono_classement_final` GROUP BY equipe3 order by nb desc");
	//Boucle sur les 3 premières équipes sélectionnées
	if ($nbVotant > 0) {
		$i = 0;
		echo "<table width=\"100%\">\n";
		while (($ligne = mysql_fetch_array($result, MYSQL_NUM)) && $i < 8) {
			$nomEquipe = $ligne[0];
			$countEquipe = $ligne[1];
			$isPourUtilisateur = isPourUtilisateur($nomEquipe, $login, 3);
			//Une couleur différente est utilisée pour les équipes choisis par l'utilisateur			echo "salut".$peutEncoreParier();
			if (peutEncoreParier()){
				if ($i % 2 == 0)
						echo "<tr width=\"100%\" class=vertclair ";
				else
						echo "<tr width=\"100%\" class=vertfonce ";
				
			}	else {
					if ($i % 2 == 0)
						echo "<tr width=\"100%\" onclick=\"window.location.href='detailfavoris.php?idequi=" . $nomequipe . "'\" class=vertclairLien ";
					else
						echo "<tr width=\"100%\" onclick=\"window.location.href='detailfavoris.php?idequi=" . $nomequipe . "'\" class=vertfonceLien ";
				}
			if ($isPourUtilisateur)	{
				echo " style='color:blue' >\n";
			}
			else {
				echo " >\n";
			}
			echo "<td width=\"80%\">\n";
				echo $nomEquipe . "\n";
			echo "</td>\n";
			echo "<td>\n";
				echo ceil($countEquipe / $nbVotant * 100) . "%\n";
			echo "</td>\n";
			echo "</tr>";
			echo "<tr class=\"beige\"><td colspan=\"2\"></td></tr>";
			$i++;
		}
		echo "</table>\n";
	}

}
//FIN MODIFICATION LE 10 mai 2006 PAR OCLA

function peutEncoreParier() {
	require_once ("common.php");

	return getParametre("canSaisieFavoris") == "1";
}

function afficheTableauRecapEquipes($login) {
	require_once ("common.php");

	//DECLARATIONS

	//REQUETES
	$equipes = executerRequete("select b.idbuteur, nom, prenom, login, count(b.idbuteur) from `choisi_buteur` c join `buteur` b on c.idbuteur = b.idbuteur group by b.idbuteur");

	echo "<h3>Les équipes s&eacute;lectionn&eacute;s ttttttttttttttt</h3>";

	echo "<table  class=\"tableau\" width=\"90%\">";
	echo "<tr class=\"beige\">";
	echo "<td> Equipe </td>\n";
	echo "<td> Cote </td>\n";
	echo "</tr>";

	//Parcours sur les équipes
	$i = 0;
	while ($row = mysql_fetch_array($equipes, MYSQL_NUM)) {
		//Pour chaque équipe, on va récupérer sa côte
		$idEquipe = $row[0];

		$isPourUtilisateur = ($row[1] == $login);
		$coteEquipe = coteEquipe($idEquipe);

		//Affichage du tableau
		echo "<tr class=\"beige\"><td colspan=\"2\"></td></tr>";
		//Une couleur différente est utilisée pour les équipes choisies par l'utilisateur
		if ($isPourUtilisateur) {
			echo "<tr class=\"orange\">";
		} else
			if ($i % 2 == 0) {
				echo "<tr class=\"vertclair\">";
			} else {
				echo "<tr class=\"vertfonce\">";
			}
		$i++;
		echo "<td>\n";
		echo $idEquipe;
		echo "</td>\n";
		echo "<td>" . $coteEquipe . "</td>\n";
		echo "</tr>";
	}

	echo "</table>\n";
}

function afficheFavoris($login, $update) {
	require_once 'common.php';
	$link = get_mysql_link();
	$equipe1 = null;
	$equipe2 = null;
	$equipe3 = null;

	if (aDejaParie($login)) {
		$query = "SELECT equipe1, equipe2, equipe3 FROM `prono_classement_final` p WHERE p.login='" . $login . "'";
		$result = mysql_query($query, $link);
		if (!$result)
			my_error($link, $query);
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			$equipe1 = $row[0];
			$equipe2 = $row[1];
			$equipe3 = $row[2];
		}
	}
	//DEBUT MODIFICATION LE 27 mai 2006 PAR OCLA
	//Si les paris sont finis, on n'affiche que le récapitulatif des équipes choisies
	if (peutEncoreParier()) {
		if ($update == "write") {
			echo "<script>\n";
			//DEBUT MODIFICATION LE 10 mai 2006 PAR OCLA

			echo ("function verifierEquipes() {\n");
			echo "selectEquipe1=document.forms[0].equipe1\n";
			echo "selectEquipe2=document.forms[0].equipe2\n";
			//echo "selectEquipe3=document.forms[0].equipe3\n"; //décommenter ici pour la gestion du 3ème
			
			echo "if (selectEquipe1.options[selectEquipe1.selectedIndex].value == selectEquipe2.options[selectEquipe2.selectedIndex].value){\n";
			
			/*echo "if (selectEquipe1.options[selectEquipe1.selectedIndex].value == selectEquipe2.options[selectEquipe2.selectedIndex].value || selectEquipe2.options[selectEquipe2.selectedIndex].value == selectEquipe3.options[selectEquipe3.selectedIndex].value || selectEquipe1.options[selectEquipe1.selectedIndex].value == selectEquipe3.options[selectEquipe3.selectedIndex].value){\n";*/ //inverser la mise en commentaire des if pour la gestion du 3ème
			
			//L'utlisateur a saisi 2 fois la même équipe
			echo "alert('Veuillez renseigner des equipes differentes');\n";
			echo "return false;";
			echo "}\n";
			echo "else {\n";
			//On enregistre la modification
			echo "return true;\n";
			echo "}\n";
			echo "}\n";
			echo "</script>\n";
			echo "<table class=\"tableau\">";
			echo "<form name=\"favoris\" action=\"savefavoris.php\" method=\"post\" onsubmit=\"return verifierEquipes()\" >";
			echo "<input type=\"hidden\" name=\"update\" value=\"read\">";
			echo "<tr class=\"vertclair\" width=\"50%\"><td> Vainqueur </td> <td>";
			selectEquipes("equipe1", $equipe1);
			echo "</td></tr>";
			echo "<tr class=\"vertfonce\" width=\"50%\"><td> Finaliste </td> <td>";
			selectEquipes("equipe2", $equipe2);
			echo "</td></tr>";
			
			/*echo "<tr class=\"vertclair\" width=\"50%\"><td> Troisi&egrave;me </td> <td>"; //décommenter ici pour la gestion du 3ème
			selectEquipes("equipe3", $equipe3); //décommenter ici pour la gestion du 3ème
			echo "</td></tr>";*/ //décommenter ici pour la gestion du 3ème
			
			echo "<tr><td><input type=\"submit\" value=\"Enregistrer\" class=\"bouton\"/>"; 
			echo "</form>";
			echo "</table>";
			//FIN MODIFICATION LE 10 mai 2006 PAR OCLA
		} else
			if ($update == "read") {
				echo "<table class=\"tableau\">";
				echo "<form action=\"savefavoris.php\" method=\"post\">";
				echo "<tr class=\"beige\"><td colspan=\"2\">Choisir mes Favoris </td></tr>";
				echo "<input type=\"hidden\" name=\"update\" value=\"write\">";
				echo "<tr><td><input type=\"submit\" value=\"Modifier\" class=\"bouton\"/>";
				echo "</td></tr>";
				echo "</form>";
				echo "</table>";
				echo "<br/>";
			}
	}

	//Si l'utilisateur peut parier, il peut choisir ses favoris
	if (peutEncoreParier()) {
		echo "<table class=\"tableau\">";
		echo "<tr class=\"beige\"><td colspan=\"2\"> Favoris pour " . $_SESSION['prenom'] . " ".$_SESSION['nom'] . " </td></tr>";
		echo "<tr width=\"100%\" class=\"vertclair\"><td> Vainqueur </td> <td>" . $equipe1 . "</td></tr>";
		echo "<tr width=\"100%\" class=\"vertfonce\"><td> Finaliste </td> <td>" . $equipe2 . "</td></tr>";
		//echo "<tr width=\"100%\" class=\"vertclair\"><td> Troisi&egrave;me </td> <td>" . $equipe3 . "</td></tr>"; //décommenter ici pour la gestion du 3ème
		echo "</table>";
		echo "<br/>";
		echo "<br/>";
	}	
	
	//DEBUT MODIFICATION LE 10 mai 2006 PAR OCLA
	//tableau des statistiques pour les votes
	$nbVotant = determinerNbVotant();
	echo "<h3>Les &eacute;quipes s&eacute;lectionn&eacute;es (" . $nbVotant . " votants)</h3>";
	echo "<table class=\"tableau\">";
	echo "<tr class=\"beige\">";
	echo "<td>Vainqueur</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>";
	trStatistiquesChoixVainqueur($nbVotant, $login);
	echo "</td>";
	echo "</tr>\n";
	echo "<tr class=\"beige\">";
	echo "<td>Finaliste</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>";
	trStatistiquesChoixFinaliste($nbVotant, $login);
	echo "</td>";
	echo "</tr>\n";
	
	/*echo "<tr class=\"beige\">"; //décommenter ici pour la gestion du 3ème
	echo "<td>Troisi&egrave;me</td>\n"; //décommenter ici pour la gestion du 3ème
	echo "</tr>\n"; //décommenter ici pour la gestion du 3ème
	echo "<tr>\n"; //décommenter ici pour la gestion du 3ème
	echo "<td>"; //décommenter ici pour la gestion du 3ème
	trStatistiquesChoixTroisieme($nbVotant, $login); //décommenter ici pour la gestion du 3ème
	echo "</td>"; //décommenter ici pour la gestion du 3ème
	echo "</tr>\n";*/	//décommenter ici pour la gestion du 3ème
	
	echo "</table>\n";
	//FIN MODIFICATION LE 10 mai 2006 PAR OCLA

	//FIN MODIFICATION LE 27 mai 2006 PAR OCLA

}

require_once 'banniere.php';

if (!isset ($update)) {
	$update = "read";
}
afficheFavoris($login, $update);

echo "<h3> Liste des favoris par joueurs</h3>";

affichejoueursparequipe(null);

require_once 'footer.php';
?>