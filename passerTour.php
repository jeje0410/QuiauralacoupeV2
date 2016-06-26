<?php
require_once 'common.php';
require_once 'session.php';
require_once 'config.php';
$link = get_mysql_link();
$tour = null;
$newTour=null;
global $nbMatchTourCoupe;

//récupération du prochain type tour a partir du tour courant
$query="select ct.type_tour from coupe_tour ct,parametres p where ct.num_tour=p.valeur+1 and p.param = 'tourCoupeCourant'";
$result = mysql_query($query, $link);
if (!$result)
	my_error($link, $query);
$newTour=null;
while ($row = mysql_fetch_array($result, MYSQL_NUM)){
		$newTour=$row[0];
}

//récupépration du type de tour courant a partir du tour courant
$query="select ct.type_tour from coupe_tour ct,parametres p where ct.num_tour=p.valeur and p.param = 'tourCoupeCourant'";
$result = mysql_query($query, $link);
if (!$result)
	my_error($link, $query);
while ($row = mysql_fetch_array($result, MYSQL_NUM)){
		$tour=$row[0];
}

//insertion des matchs pour le prochain tour de coupe
$a=0;
$query3="SELECT idMatch FROM `match` where resultat is null order by dateM asc";
$result = mysql_query($query3, $link);
if (!$result)
	my_error($link, $query3);
while ($a<$nbMatchTourCoupe&&$ma = mysql_fetch_array($result, MYSQL_NUM)){
	$matchtour[$a]=$ma[0];
	$a=$a+1;
}
for ($b=0;$b<$nbMatchTourCoupe;$b++){
	$query = "insert into coupe_tour_match(id_tour,id_match) values (".$newTour.",".$matchtour[$b].")";
	$result = mysql_query($query, $link);	
	if (!$result)
		my_error($link, $query);		
}
//Fin insertion des matchs

//récupération des logins du tour courant
$query1="SELECT j1.login, j2.login from  joueurs j1, joueurs j2 RIGHT OUTER JOIN coupe_rencontre c ON j2.login = c.login2 where c.login1=j1.login and c.tour=".$tour;
$link = get_mysql_link();
$result1 = mysql_query($query1, $link);
	if (!$result1)
		my_error($link, $query1);
	$j=0;
	
	//Parcours des Joueurs pour calculer le nombre de point
	while ($row1 = mysql_fetch_array($result1, MYSQL_NUM)) {
		//Joueur 1
		$pointsJ1=recuperationPointCoupe($tour,$row1[0]);
		//Joueur 2
		$pointsJ2=recuperationPointCoupe($tour,$row1[1]);
		if ($row1[1]==null){
			$pointsJ2=-1;
		}
		if ($pointsJ1>$pointsJ2){
			$concurents[$j]=$row1[0];
		} if ($pointsJ1<$pointsJ2){
			$concurents[$j]=$row1[1];		
		}
		if ($pointsJ1==$pointsJ2){
			$concurents[$j]=tiraubut($row1[0],$row1[1],$tour);
		}
		$j=$j+1;
	}
	$nbparticipants=$j;
	$tirageAdversaire=tirage_sort($nbparticipants/2,$nbparticipants);
	for ($n=0;$n<$nbparticipants/2;$n++){
		$match[$n][0] = $concurents[$n];
		$match[$n][1] = $concurents[$tirageAdversaire[$n]];
		$query = "insert into coupe_rencontre(login1,login2,tour) values ('".$match[$n][0]."','".$match[$n][1]."',".$newTour.")";
		$result = mysql_query($query, $link);	
		if (!$result)
			my_error($link, $query);	
	}
		

	//Update le numero de tour
	$query = "update parametres set valeur =valeur+1 where param ='tourCoupeCourant'";
		$result = mysql_query($query, $link);	
		if (!$result)
			my_error($link, $query);
	

exit;

?>