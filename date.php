<?php
function date1SupDate2SQL($date1, $dt) {
	$array1 = getdate($date1);
	$annee1 = $array1["year"];
	$mois1 = $array1["mon"];
	$jour1 = $array1["mday"];
	$heure1 = $array1["hours"];
	$minutes1 = $array1["minutes"];
	$annee2=strval(substr($dt,0,4)); 
	$mois2=strval(substr($dt,5,2)); 
	$jour2=strval(substr($dt,8,2)); 
	$heure2=strval(substr($dt,11,2)); 
	$minutes2=strval(substr($dt,14,2)); 
	
	if ($annee1>$annee2) {
		return true;
	}
	elseif ($annee1<$annee2) {
		return false;
	}
	else {
		if ($mois1>$mois2) { 
			return true;
		 }
		if ($mois1<$mois2) { 
			return false;
		 }
		else {
			if ($jour1>$jour2){
				return true;
			}
			if ($jour1<$jour2){
				return false;
			}
			else {
				if ($heure1>$heure2){
					return true;
				}
				if ($heure1<$heure2){
					return false;
				}
				else {
					if ($minutes1>$minutes2) {
						return true;
					}
					if ($minutes1<$minutes2) {
						return false;
					}
					else {
						return false;
					}
				}
			}
		}
	}
}

function todaySupDate($date){
	$today = time();
	return (date1SupDate2SQL($today, $date));
}

?>