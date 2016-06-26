<?php

require_once('common.php');

session_start();

global $login;

if (isset($_SESSION['login'])) {
	$login = $_SESSION['login'];
	return;
}

function check_auth($login, $password) {
	$link = get_mysql_link();
	$query = "select Password from joueurs where login = '"
		. mysql_real_escape_string($login)
		. "'";
	$result = mysql_query($query, $link);
	if (!$result) my_error($link, $query);
	$row = mysql_fetch_array($result, MYSQL_NUM);
	if ($row) {
		if (strcmp($password, $row[0]) == 0) return true;
	}
	// suppr nico 25/05
	return false;
}

//recuperation du nom et prénom et mise en session
function check_nom_prenom($login) {
$link = get_mysql_link();

$query = "select j.Prenom, j.Nom from joueurs j where j.login='" . $login . "'";

$result = mysql_query($query, $link);
if (!$result)
	my_error($link, $query);
$row = mysql_fetch_array($result, MYSQL_NUM);
$_SESSION['prenom'] = $row[0];
$_SESSION['nom'] = $row[1];
}

$auth_error = false;
if (isset($_POST['authlogin'])) {
	$login = $_POST['authlogin'];
	if (isset($_POST['authpassword']))
		$password = $_POST['authpassword'];
	else
		$password = "";
	if (check_auth($login, $password)) {
		$_SESSION['login'] = $login;
		return;
	} else {
		$auth_error = true;
	}
}

unset($_POST['authpassword']);
unset($_POST['authlogin']);

$connect_uri = "mainPage.php";
$connect_post = array();
foreach($_POST as $p => $v) {
	$connect_post[$p] = $v;
}

$_SESSION = array();

if (isset($_COOKIE[session_name()])) {
	setcookie(session_name(), '', time()-42000, '/');
}

session_destroy();

require('connect.php');

exit(0);

?>
