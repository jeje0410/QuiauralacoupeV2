<?php
require_once 'common.php';
require_once 'session.php';

$update = get_post_arg('update');
if ($update=="read") {
	$equipe1 = get_post_arg('equipe1');
	$equipe2 = get_post_arg('equipe2');
	//$equipe3 = get_post_arg('equipe3')/*null*/;//décommenter ici pour la gestion du 3ème
	if (!aDejaParie ($login)){
		$query = "insert into  `prono_classement_final` (login,equipe1,equipe2,equipe3) values (\"".$login."\",\"".$equipe1."\",\"".$equipe2."\",\"".$equipe3."\")";
	} else {
		$query = "update `prono_classement_final` set equipe1=\"".$equipe1."\", equipe2=\"".$equipe2."\", equipe3=\"".$equipe3."\" where login='".$login."'";
	}
	$link = get_mysql_link();
	$result = mysql_query($query, $link);
	if (!$result) my_error($link, $query);
}
require_once "favoris.php";

?>