<?php
$query1="SELECT count(*) FROM `choisi_buteur` cb where cb.login = '".$login."'";
$query2="SELECT count(*) FROM `prono_classement_final` pcf where pcf.login = '".$login."'";
afficheResteAfaire($query1, $query2);
?>

