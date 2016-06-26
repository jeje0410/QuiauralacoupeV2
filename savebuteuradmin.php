<?php
function mettreAJourStatsButeur($idButeur, $idMatch) {
	require_once ("common.php");

	executerRequete("insert into `buteur_match` (idmatch,idbuteur) values (" . $idMatch . "," . $idButeur . ")");

}

function mettreAJourPointsUtilisateurs($idButeur, $idMatch) {
	require_once ("common.php");

	//DECLARATIONS
	global $pointbuteurgroupe;
	global $pointbuteur8;
	global $pointbuteur4;
	global $pointbuteur2;
	global $pointbuteur1;
	$pointButeur = 0;

	//Récupération du type de match
	$typeMatch = executerRequeteResultatUnique("select type from `match` where idmatch='" . $idMatch . "'");
	switch ($typeMatch) {
		case 0 :
			$pointButeur = $pointbuteurgroupe;
			break;
		case 1 :
			$pointButeur = $pointbuteur8;
			break;
		case 2 :
			$pointButeur = $pointbuteur4;
			break;
		case 3 :
			$pointButeur = $pointbuteur2;
			break;
		case 4 :
			$pointButeur = $pointbuteur1;
			break;

		default :
			break;
	}	
	$pointButeur = recupCoteButeur($idButeur,$pointButeur);
	//Update de la table pari pour tous les utilisateurs ayant sélectionné ce buteur
	executerRequete("update `pari` set points_buteur = points_buteur + " . $pointButeur . ", points_total = points_total + " . $pointButeur . " where idmatch = " . $idMatch . " and login in (select login from choisi_buteur where idbuteur = " . $idButeur . ")");
}

// Debut Nico 06/06
function mettreAJourClassement(){
	require_once ("common.php");
	$result = executerRequete("select login from joueurs");
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		executerRequete("update `classement` set points_buteurs = (select sum(points_buteur) from `pari` where login = '".$row[0]."' and points_buteur is not null) where login='".$row[0]."'");		
		executerRequete("update `classement` set points = (select sum(points_total) from `pari` where login = '".$row[0]."' and points_total is not null) where login='".$row[0]."'");		
	}
	$result2 = executerRequete("select login, place from `classement` order by points DESC , sens DESC , exact DESC , points_matchs DESC");
	$i = 1;
	while ($row2 = mysql_fetch_array($result2, MYSQL_NUM)) {
		$progression = $row2[1] - $i;
		if ($progression > 0) {
			$progression = "+" . $progression;
		}
		elseif ($progression == 0) {
			$progression = "=";
		}
		$result3 = executerRequete("update `classement` set place='".$i."', progression='".$progression."' where login='".$row2[0]."'");
		if (!$result3)
			my_error($link, $query3);
		$i++;
	}
}
// Fin Nico 06/06



require_once 'common.php';

//DECLARATIONS
$nbButeurs = get_post_arg('nbButeurs');
$idMatch = get_post_arg('idMatch');
//rem - deb - 17.04.08
$filtre = get_post_arg('filtre');
//rem - fin - 17.04.08
	
//Pour chaque buteur, les stats de celui-ci seront mises à jour et les points des utilisateurs
//ayant voté pour lui actualisés
//Avant de mettre à jour les buteurs, on s'assure qu'il n'y a pas déjà ce match de saisi en supprimant les précédents enregistrements
executerRequete("delete from `buteur_match` where idmatch = " . $idMatch);

//Pour gérer les erreurs de saisie, on supprime avant toutes les entrées pour le match en question
executerRequete("update `pari` set points_buteur = 0 where idmatch = " . $idMatch);

//rem - deb - 09.05.08
if ($nbButeurs == 0) {
	mettreAJourStatsButeur(0, $idMatch);
} else {
	for ($i = 0; $i < $nbButeurs; $i++) {
		$idButeur = get_post_arg('buteur' . $i);
		mettreAJourStatsButeur($idButeur, $idMatch);
		mettreAJourPointsUtilisateurs($idButeur, $idMatch);
	}
}
//rem - fin - 09.05.08

// Debut Nico 08/06
mettreAJourClassement();
remplitClassementEquipe();
// Fin Nico 08/06

echo "<script>\n";
//on rafraîchit la page appelante
//rem - deb - 17.04.08
echo "window.opener.location.href=\"administration.php?filtre=" . $filtre . "\"\n";
//rem - fin - 17.04.08
//on ferme la popup
echo "window.close()\n";
echo "</script>\n";
//FIN MODIFICATION LE 16 mai 2006 PAR OCLA
?>