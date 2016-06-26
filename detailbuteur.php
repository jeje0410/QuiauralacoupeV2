<?php
require_once 'banniere.php';

echo "<h3> Liste des joueurs ayant choisis le buteur </h3>";
$idbute = null;
if (isset ($_GET["idbute"]))
		$idbute = $_GET["idbute"];

affichejoueursparbuteur( $idbute);

require_once 'footer.php';

?>