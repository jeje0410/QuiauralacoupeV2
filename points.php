<?php
require_once 'banniere.php';

if (!isset($_GET['visualiser'])) {
	require_once 'matchjoue.php';
}
else {
	require_once 'matchjoueautre.php';
}
require_once 'footer.php';
?>