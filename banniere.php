<?php
require_once 'common.php';
require_once 'session.php';
require_once 'header.php';
check_nom_prenom($login);
$prenom = $_SESSION['prenom'];
$nom = $_SESSION['nom'];
?>

<div id="container">
	<div id="bandeauhaut">
		<table>
			<tr>
				<td width="35%"><a href="mainPage.php"><img src="images/logo.jpg" style="width=90px; height:90px;"/></a></td>
				<td width="25%"><a href="mainPage.php"><img src="images/Logo_Comp.jpg" style="width=90px; height:90px;" /></a></td>
				<td width="8%"><a href="mainPage.php"><img src="images/logo.jpg" style="width=90px; height:90px;"/></a></td>
				<td width="17%"><a href="regledujeu.php"><H5>R&egrave;glement</H5></a></td>
				<td width="15%"><h4>Bienvenue <?php echo $prenom." ".$nom; ?> (<span style="font-style: italic;"><?php echo $login; ?></span>)<a href="disconnect.php">D&eacute;connexion</a></h4></td>
			</tr>
			<tr>
				<td></td>
				
			</tr>
		</table>
	</div>				
	<div id="pageinterne">
	
    


