<?php
header("Content-Type: text/html; charset=UTF-8");

if (!isset ($title))
	$title = 'France 2016';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<link title="coupedumonde" type="text/css" rel="stylesheet" href="css/coupedumonde.css"/>

<SCRIPT language="Javascript">

function ouvrirResultatMatch(idMatch){
	url= "resultatMatch.php?idMatch="+idMatch;
	window.open(url,'Resultat',"location=no,width=400,height=300,resizable=no,status=no");
}

    if (document.images)
    {
      preload_image_object = new Image();
      // set image url
      image_url = new Array();
      image_url[0] = "images/fond-ecran.jpg";

       var i = 0;
       for(i=0; i<1; i++) 
         preload_image_object.src = image_url[i];
    }    
  
</SCRIPT>
<title><?php echo $title; ?></title>
</head>
<body>
