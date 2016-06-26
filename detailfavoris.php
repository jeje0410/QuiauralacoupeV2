<?php
require_once 'banniere.php';

echo "<h3> Liste des joueurs ayant choisis l'&eacutequipe dans les 2 finalistes </h3>";
$idequi = null;
if (isset ($_GET["idequi"]))
		$idequi = $_GET["idequi"];

affichejoueursparequipe( $idequi);

require_once 'footer.php';

?>