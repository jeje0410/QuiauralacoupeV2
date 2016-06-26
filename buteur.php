<?php


//DEBUT MODIFICATION LE 13 mai 2006 PAR OCLA
function coteButeur($idButeur, $tour) {
	require_once ("config.php");
	global $pointbuteurgroupe;
	global $pointbuteur4;
	global $pointbuteur2;
	global $pointbuteur1;
	if ($tour == 0) {$pointbuteur = $pointbuteurgroupe;}
	if ($tour == 4) {$pointbuteur = $pointbuteur4;}
	if ($tour == 2) {$pointbuteur = $pointbuteur2;}
	if ($tour == 1) {$pointbuteur = $pointbuteur1;}
	
	
	return recupCoteButeur($idButeur,$pointbuteur);
}

function peutEncoreParieButeur($login) {
	require_once ("common.php");
	return getParametre("canSaisieButeur") == "1";
}

function determinerNbVotant() {
	require_once ("config.php");
	global $nbbuteurs;

	//Récupération du nombre de personnes ayant voté pour calculer les pourcentages
	$nbVotes = executerRequeteResultatUnique("SELECT count(*) FROM `choisi_buteur`");
	return $nbVotes / $nbbuteurs;
}

function isPourUtilisateur($idbuteur, $login) {
	$buteurs = executerRequete("select idbuteur from `choisi_buteur` where login = '" . $login . "'");

	while ($row = mysql_fetch_array($buteurs, MYSQL_NUM)) {
		if ($idbuteur == $row[0])
			return true;
	}

	return false;
}

function afficheTableauRecapButeurs($login, $buteurs) {
	require_once ("common.php");

	//DECLARATIONS

	echo "<table class=\"tableau\" width=\"90%\">";
	echo "<tr class=\"beige\">";
	echo "<td rowspan=2> Buteurs </td>\n";
	echo "<td rowspan=2> Nb. Buts marqu&eacute;s </td> \n";
	echo "<td colspan=4> Cote </td>\n";
	echo "</tr>";

	echo "<tr class=\"beige\">";
	echo "<td width=40>Poule</td>\n";
	echo "<td width=40>8&egrave;me et Quart</td>\n";
	echo "<td width=40>Demi</td>\n";
	echo "<td width=40>Finale</td>\n";
	echo "</tr>";

	//Parcours sur les buteurs
	$i = 0;
	while ($row = mysql_fetch_array($buteurs, MYSQL_NUM)) {
		//Pour chaque buteur, on va récupérer sa cote et son nombre de buts
		
		$idButeur = $row[0];
		$nomButeur = $row[1];
		$prenomButeur = $row[2];
		$equipeButeur = $row[3];
		$isPourUtilisateur = isPourUtilisateur($idButeur, $login);
		$coteButeur = coteButeur($idButeur, 0);
		$nbButsButeur = nbButsButeur($idButeur);
		$coteButeur4 = coteButeur($idButeur, 4);
		$coteButeur2 = coteButeur($idButeur, 2);
		$coteButeur1 = coteButeur($idButeur, 1);
	
		//Affichage du tableau
		echo "<tr class=\"beige\"><td colspan=\"6\"></td></tr>";
		//Une couleur différente est utilisée pour les buteurs choisis par l'utilisateur
			if ($i % 2 == 0) {
				echo "<tr onclick=\"window.location.href='detailbuteur.php?idbute=" . $idButeur . "'\" class=\"vertclairLien\"";
			} else {
				echo "<tr onclick=\"window.location.href='detailbuteur.php?idbute=" . $idButeur . "'\" class=\"vertfonceLien\"";
			}
			if ($isPourUtilisateur){
				echo " style='color:blue'>";
			} else {
				echo ">";
			}
		$i++;
		echo "<td>\n";
		echo $prenomButeur . " " . $nomButeur . " (" . $equipeButeur . ") " . "";
		echo "</td>\n";
		echo "<td>" . $nbButsButeur . "</td>\n";
		echo "<td width=40>" . $coteButeur . "</td>\n";
		echo "<td width=40>" . $coteButeur4 . "</td>\n";
		echo "<td width=40>" . $coteButeur2 . "</td>\n";
		echo "<td width=40>" . $coteButeur1 . "</td>\n";
		echo "</tr>";
	}

	echo "</table>\n";
}

function javascriptButeursDoublons($nbButeurs) {
	echo "<script>\n";
	echo "function verifDoublonsButeurs(){\n";
	//On boucle sur tous les champs de buteur pour s'assurer que 2 ne sont pas identiques
	echo "if(\n";
	for ($i = 1; $i < $nbButeurs +1; $i++) {
		for ($j = $nbButeurs; $j > $i; $j--) {
			if ($i == $j) {
				continue;
			}
			//Sauf pour la première boucle
			if ($j != $nbButeurs || $i != 1) {
				echo " || \n";
			}
			echo "document.forms[0].buteur" . $i . ".value == document.forms[0].buteur" . $j . ".value";
		}
	}
	echo "){\n";
	echo "alert('Veuillez saisir des buteurs differents');\n";
	echo "return false;";
	echo "} else {\n";
	//Si tout va bien, on submite la forme
	echo "return true;\n";
	echo "}\n";
	echo "}\n";
	echo "</script>\n";

}

function afficheButeur($login, $update) {
	require_once "common.php";


	// DECLARATIONS
	global $nbbuteurs;
	$idbuteur = null;
	$nom = null;
	$prenom = null;

	//On regarde si l'utilisateur a déjà voté pour au moins un buteur
	$nbButeursParies = nbButeursDejaParies($login);
	$peutEncoreParier = peutEncoreParieButeur($login);
	//Si l'utilisateur peut parier, il peut choisir son buteur

	if ($peutEncoreParier) {
		echo "<h3>Les Buteurs de " . $_SESSION['prenom'] . " ".$_SESSION['nom']. "</h3>";
		if ($nbButeursParies == 0 && $update != "write") {
			//L'utilisateur n'a pas encore parié , on lui affiche un bouton pour le faire
			//tableau des statistiques pour les votes

			echo "<table>";
				echo "<form action=\"savebuteur.php\" method=\"post\">";
					echo "<tr><td>";
						echo "<input type=\"hidden\" name=\"update\" value=\"write\">";
						echo "<input type=\"submit\" value=\"Choisir vos buteurs\" class=\"bouton\"/>";
					echo "</td></tr>";
				echo "</form>";
			echo "</table>";
		} else {
			//On lui affiche la liste des buteurs pour lesquels il a parié et un bouton "modifier"
			if ($update == "read") {
				//Récupération des buteurs
				$result = executerRequete("SELECT c.idbuteur, b.nom, b.prenom, e.nom FROM `choisi_buteur` c, `buteur` b, `equipe` e WHERE c.login='" . $login . "' and c.idbuteur=b.idbuteur and e.idEquipe = b.idEquipe");
				echo "<table class=\"tableau\" width=\"80%\">";
				echo "<tr class=\"beige\">";
				echo "<td rowspan=2>Mes buteurs</td>\n";
				echo "<td colspan=4>Cote</td>\n";
				echo "</tr>\n";
				echo "<tr class=\"beige\">";
				echo "<td width=40>Poule</td>\n";
				echo "<td width=40>Quart</td>\n";
				echo "<td width=40>Demi</td>\n";
				echo "<td width=40>Finale</td>\n";
				echo "</tr>\n";
				
				$i = 1;
				while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
					$idbuteur = $row[0];
					$nom = $row[1];
					$prenom = $row[2];
					$equipe = $row[3];
					if (($i % 2) == 1) {
						echo "<tr class=\"vertclair\" width=\"50%\">";
					} else {
						echo "<tr class=\"vertfonce\" width=\"50%\">";
					}
					echo "<td>" . $prenom . " " . $nom . " (" . $equipe . ") " . "</td><td>" . coteButeur($idbuteur, 0) . "</td><td>" . coteButeur($idbuteur, 4) . "</td><td>" . coteButeur($idbuteur, 2) . "</td><td>" . coteButeur($idbuteur, 1);
					echo "</td></tr>\n";
					$i++;
				}
				// fin nico 25/05

				echo "<form action=\"savebuteur.php\" method=\"post\">";
					echo "<tr><td align=left>";
						echo "<input type=\"hidden\" name=\"update\" value=\"write\">";
						echo "<input type=\"submit\" value=\"Modifier\" class=\"bouton\"/>";
					echo "</td></tr>";
				echo "</form>";
				echo "</table>";
			} else {

				//Génération de la méthode javascript permettant de contrôler les doublons
				
				javascriptButeursDoublons($nbbuteurs);
				//L'utilisateur a cliqué sur Modifier, on lui affiche des combos-box
				echo "<table>";
				echo "<form action=\"savebuteur.php\" method=\"post\" onsubmit=\"return verifDoublonsButeurs()\">";
				echo "<input type=\"hidden\" name=\"update\" value=\"read\">";
				// deb nico 25/05
				echo "<table class=\"tableau\">";
				//Récupération des buteurs
				$result = executerRequete("SELECT c.idbuteur, nom, prenom FROM `choisi_buteur` c, `buteur` b WHERE c.login='" . $login . "' and c.idbuteur=b.idbuteur");

				for ($i = 1; $i < $nbbuteurs +1; $i++) {
					$row = mysql_fetch_array($result, MYSQL_NUM);
					if (($i % 2) == 1) {
						echo "<tr class=\"vertclair\" width=\"50%\">";
					} else {
						echo "<tr class=\"vertfonce\" width=\"50%\">";
					}
					$idbuteur = $row[0];
					echo "<td width=\"10%\"> Buteur" . $i . "</td> <td>";
					// fin nico 25/05
					selectButeurs($idbuteur, $i);

				}
				echo "<tr><td>";
				echo " <input type =\"submit\" value=\"Enregistrer\" class=\"bouton\"/>";
				echo "</td></tr>";
				echo "</form>";
				echo "</table>";
			}
		}
	}

	//Les utilisateurs ne peuvent plus parier, ils ne voient qu'un tableau avec le récapitulatif des cotes et des buts marqués
	//Affiche les buteurs sélectionnés
	$nbVotant = determinerNbVotant();
	echo "<h3>Les buteurs s&eacute;lectionn&eacute;s (" . $nbVotant . " votants)</h3>";
	
	$buteurs = executerRequete("select b.idbuteur, b.nom, b.prenom, e.nom from `choisi_buteur` c join `buteur` b on c.idbuteur = b.idbuteur join `equipe` e on b.idEquipe = e.idEquipe group by b.idbuteur order by b.nom");
	afficheTableauRecapButeurs($login, $buteurs);
	echo "<br/><br/>";
	
	//Affiche les buteurs non sélectionnés
	if (!$peutEncoreParier) {
		echo "<h3>Les buteurs non s&eacute;lectionn&eacute;s</h3>";
		$buteurs = executerRequete("select b.idbuteur, b.nom, b.prenom, e.nom from `buteur` b join `equipe` e on b.idEquipe = e.idEquipe where b.idButeur not in (select distinct c.idButeur from `choisi_buteur` c) group by b.idbuteur order by b.nom");
		afficheTableauRecapButeurs($login, $buteurs);
	}
	//FIN MODIFICATION LE 16 mai 2006 PAR OCLA
}

require_once 'banniere.php';

if (!isset ($update)) {
	$update = "read";
}
afficheButeur($login, $update);

	echo "<h3> Les buteurs par parieurs </h3>";

affichejoueursparbuteur(null);

require_once 'footer.php';
?>