<?php
require_once 'common.php';
require_once 'session.php';

//DECLARATIONS
$update = get_post_arg('update');
if ($update == "read") {

		$message = get_post_arg('message');
		$message = str_replace("'", "''", $message);
		executerRequete("insert into `forum` (login,message) values ('" . $login . "','" . $message . "')");
	}

require_once 'forum.php';
?>