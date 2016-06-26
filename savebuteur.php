<?php
require_once 'common.php';
require_once 'session.php';
//DEBUT MODIFICATION LE 16 mai 2006 PAR OCLA

//DECLARATIONS
global $nbbuteurs;
$update = get_post_arg('update');
if ($update == "read") {

	if (nbButeursDejaParies($login) > 0) {
		//Si l'utilisateur a déjà parié, on commence par supprimer ses précédents choix avant de les réinsérer
		executerRequete("delete from `choisi_buteur` where login='" . $login . "'");
	}
	//On boucle pour tous les buteurs renseignés par l'utilisateur
	for ($i = 1; $i < $nbbuteurs +1; $i++) {

		$idbuteur = get_post_arg('buteur' . $i);

		executerRequete("insert into `choisi_buteur` (login,idbuteur) values ('" . $login . "','" . $idbuteur . "')");
	}
}

require_once 'buteur.php';
//FIN MODIFICATION LE 16 mai 2006 PAR OCLA
?>