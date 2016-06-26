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
				<td width="17%"><a href="regledujeu.php"><H4>R&egrave;glement</H4></a>
				<?php
				//Affichage du lien vers la page d'administration uniquement pour les administrateurs
				if (is_admin($login)) {
				echo "<a href=\"administration.php\">Administration</a>";
				}
				?>
				</td>
				<td width="15%"><h4>Bienvenue <?php echo $prenom." ".$nom; ?> (<span style="font-style: italic;"><?php echo $login; ?></span>)<a href="disconnect.php">D&eacute;connexion</a></h4></td>
			</tr>
			<tr>
				<td></td>
				
			</tr>
		</table>
	</div>
	<div id="mainPage">
	<div id="forum">
		<?php afficheForummainpage()?>
	</div>
	<div id="boutonforum"><?php echo "<a href=\"forum.php\" class=\"bouton\">Message</a>"; ?></div>
	<div id="information">
		<div id="classements">
			<div id="classementsolo"><?php affichePosClassementSolo($login)?></div>
			<div id="classementequipe"><?php affichePosClassementEquipe($login)?></div>
		</div>
		<div id="classementdetail"><?php afficheClassementResume($login);?><table><tr><td width="85%">&nbsp;</td><td><a href="classement.php" class="bouton">Classement</a></td></tr></table></div>
	</div>
		<div id="coupe"><?php afficheEtatCoupe($login);?><table><tr  class="celClassementLib"><td width="85%">La Coupe</td><td><a href="coupe.php" class="bouton">Coupe</a></td></tr></table></div>	
	<div id="choix">
		<div id="paris"><?php afficheProchainParis($login);?></div>
		<div id="choixequipe"><?php $query="SELECT equipe1, equipe2, equipe3 FROM `prono_classement_final` p WHERE p.login='" . $login . "'";
									afficheFavoris3($query); ?></div>
		<div id="choixbuteur"><?php $query="select b.nom, b.prenom, e.nom, b.idbuteur from `choisi_buteur` c join `buteur` b on c.idbuteur = b.idbuteur join `equipe` e on b.idEquipe = e.idEquipe where c.login='".$login."' group by b.idbuteur order by b.nom";
									afficheButeur3($query); ?></div>
	</div>
	</div>
</div>
</body>
</html>


