<?php
require_once 'common.php';
//$nomChampionnat = get_post_arg('nomChampionnat');
$nomChampionnat = $_GET['nomChampionnat'];

ExecuteFinChampionnat($nomChampionnat);


function Palmares($loginVainqueurD1,$login2emeD1,$login3emeD1,$loginVainqueurD2,$nomChampionnat){
	//Incremente le nombre de championnat du vainqueur et sock le dernier vainqueur
	$queryInsetChampion = "update `palmares` set champion = champion +1 where login = '".$loginVainqueurD1."'";
	$result = executerRequete($queryInsetChampion);
	$queryInsetChampionD2 = "update `palmares` set nbreVainqueurD2 = nbreVainqueurD2 +1 where login = '".$loginVainqueurD2."'";
	$result = executerRequete($queryInsetChampionD2);
	$dernierChampion = "insert into `derniervainqueur` set LoginVainqueurD1 = '".$loginVainqueurD1."' ,deuxieme = '".$login2emeD1. "' ,troisieme = '".$login3emeD1. "',LoginVainqueurD2 = '".$loginVainqueurD2."',NomChampionnat = '".$nomChampionnat."'";
	$result = executerRequete($dernierChampion);
}

function changeDivision($loginVainqueurD2,$login2emeD2,$login3emeD2,$loginavanavandernierD1,$loginavandernierD1,$logindernierD1){
	$queryUpdateMonte = "update classement set division = 1 where login ='".$loginVainqueurD2."'";
	$result = executerRequete($queryUpdateMonte);
	$queryUpdateMonte = "update classement set division = 1 where login ='".$login2emeD2."'";
	$result = executerRequete($queryUpdateMonte);
	$queryUpdateMonte = "update classement set division = 1 where login ='".$login3emeD2."'";
	$result = executerRequete($queryUpdateMonte);
	$queryUpdatedescend = "update classement set division = 2 where login ='".$loginavanavandernierD1."'";
	$result = executerRequete($queryUpdatedescend);
	$queryUpdatedescend = "update classement set division = 2 where login ='".$loginavandernierD1."'";
	$result = executerRequete($queryUpdatedescend);
	$queryUpdatedescend = "update classement set division = 2 where login ='".$logindernierD1."'";
	$result = executerRequete($queryUpdatedescend);

}

function remiseZero(){
	$remiseazero = "update classement set points=0,points_matchs=0,points_buteurs=0,points_cagnotte=0,nb_match=0,sens=0,exact=0,place=0,progression='='";
	$result = executerRequete($remiseazero);
	$remiseazero = "delete from `pari` where points_match is not null";
	$result = executerRequete($remiseazero);
	$remiseazero = "delete from `match` where score1 is not null";
	$result = executerRequete($remiseazero);
}


function ExecuteFinChampionnat($nomChampionnat){
	$query1="SELECT c.login,j.Nom,j.Prenom FROM `classement` c,`joueurs` j WHERE c.login = j.login AND c.division = 1 GROUP BY c.login ORDER BY c.points DESC, c.sens DESC, c.exact DESC, c.points_matchs DESC";
	$query2="SELECT c.login,j.Nom,j.Prenom FROM `classement` c,`joueurs` j WHERE c.login = j.login AND c.division = 2 GROUP BY c.login ORDER BY c.points DESC, c.sens DESC, c.exact DESC, c.points_matchs DESC";
	$result = executerRequete($query1);
	$link = get_mysql_link();
	$logindernierD1="";
	$loginavandernierD1="";
	$logindernierD2="";
	$i=0;
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		//stock le vainqueur
		if($i==0){
			$loginVainqueurD1 = $row[0];
		}
		//stock le deuxieme
		if($i==1){
			$login2emeD1 = $row[0];
		}
		//stock le troisième
		if($i==2){
			$login3emeD1 = $row[0];
		}
		$loginavanavandernierD1 = $loginavandernierD1;
		$loginavandernierD1 = $logindernierD1;
		$logindernierD1 = $row[0];
		$i++;
	}
	$result = executerRequete($query2);
	$link = get_mysql_link();
	$i=0;
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		//stock le vainqueur
		if($i==0){
			$loginVainqueurD2 = $row[0];
		}
		//stock le deuxieme
		if($i==1){
			$login2emeD2 = $row[0];
		}
		//stock le troisième
		if($i==2){
			$login3emeD2 = $row[0];
		}
		$i++;
	}
	Palmares($loginVainqueurD1,$login2emeD1,$login3emeD1,$loginVainqueurD2,$nomChampionnat);
	changeDivision($loginVainqueurD2,$login2emeD2,$login3emeD2,$loginavanavandernierD1,$loginavandernierD1,$logindernierD1);
	remiseZero();
}
header('location: administration.php' );
exit;

?>
