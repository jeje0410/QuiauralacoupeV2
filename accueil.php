<?php
require_once 'banniere.php';

//rem - deb - 12.05.08
require_once 'resteAfaire.php';
//rem - fin - 12.05.08

if (!isset($_GET['visualiser'])) {
	require_once 'matchjoue.php';
}
else {
	require_once 'matchjoueautre.php';
}

require_once 'footer.php';
?>