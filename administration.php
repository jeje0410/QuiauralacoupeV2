<?php
require_once 'banniere.php';
if (is_admin($login)){
	require_once 'matchjoueadmin.php';
// Nico 30/05
	//rem - deb - 09.05.08
	echo "<h3> Classement final </h2>";
	ecrirePodium();
	//rem - fin - 09.05.08
	echo "<h2> Inscription d'un joueur </h2>";
	require_once 'common.php';
	inscritJoueur();
	echo "<h2> Ajouter un match </h2>";
	ecrireMatch();
	echo "<h2> Suivi de la coupe </h2>";
	afficheCreerCoupe();
}
require_once 'footer.php';

// Nico 30/05
?>