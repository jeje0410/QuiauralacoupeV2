<?php
require_once ('common.php');

require_once ('header.php');

if (!isset ($connect_uri)) {
	$connect_uri = "mainPage.php";
}

if (!isset ($connect_post)) {
	$connect_post = array ();
}
?>

<div id="container">
	<div id="bandeauhaut">
		<table>
			<tr>
				<td width="40%"><a href="accueil.php"><img src="images/logo.jpg" alt="logo" style="width=90px; height:90px;"/></a></td>
				<td width="45%"><a href="accueil.php"><img src="images/Logo_Comp.jpg" alt="logoComp" style="width=90px; height:90px;" /></a></td>
				<td width="15%"><a href="accueil.php"><img src="images/logo.jpg" alt="logo" style="width=90px; height:90px;"/></a></td>
			</tr>
		</table>
	</div>
	<div id="pageinterne">
<form action="<?php echo $connect_uri; ?>" method="post">
<?php

foreach ($connect_post as $p => $v) {
	echo "<input type=\"hidden\" name=\"$p\" value=\"$v\" />\n";
}
?>
	<div id="identification">
		<table cellpadding="0" cellspacing="0" style="text-align:center">
<?php
// debut nico 25/05
if (isset($auth_error) && $auth_error){
	echo "<tr class=\"erreur\"><td colspan=3>Erreur dans l'authentification</td></tr>"; 	
// fin nico 25/05
}
?>
			<tr class="beige">
				<td width="40%" align="left">Identifiant : </td>
				<td width="30%" align="left"><input type="text" tabindex="1" size="20" maxlength="20" name="authlogin"<?php if (isset($login)) echo " value=\"$login\""; ?> /></td>
 <?php

//DEBUT MODIFICATION LE 13 mai 2006 PAR OCLA

if (!isset ($login)) {
	echo "<script language=\"javascript\">\n";
	echo "document.forms[0].authlogin.focus();\n";
	echo "</script>\n";
}
//FIN MODIFICATION LE 13 mai 2006 PAR OCLA
?>
				<td width="30%" rowspan="2"><input type="submit" value="Connexion" class="bouton"/></td>
			</tr>
			<tr class="beige">
				<td width="40%" align="left">Mot de passe : </td>
				<td width="30%" align="left"><input type="password" name="authpassword" size="20" maxlength="20" tabindex="2"/></td>
			</tr>
			<tr class="beige">
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3" align="left"><a href="mailto:quiauralacoupe@free.fr">Inscription</a></td>
			</tr>
		</table>
	</div>
</form>
<br>
<?php
require_once 'reglement.php';
?>
</div>
</body>
</html>
