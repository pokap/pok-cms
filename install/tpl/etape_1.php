<?php
// en-tête
include(__DIR__.'/header.php');
?>

<p>Vous allez indiquer les info pour la connexion au serveur mysql.</p>
<form method="post" action="">

<?php
  if( !empty($extension_non_installer) ) echo '<p style="color:red;">Ces extensions : '.implode(', ', $extension_non_installer).' doivent êtres activée.</p>';
  else echo '<p style="color:green;">Extensions nécessaire activée.</p>';
?>

  <fieldset>
    <legend>Information serveur</legend>

<?php
  if( $erreurs == 101 ) echo '<p style="color:red;">Connexion à MySql impossible !</p>';
?>

    <p><label for="server">Adresse serveur :</label> <input type="text" name="server" id="server" value="<?php echo $server;?>" /></p>
    <p><label for="user">Utilisateur :</label> <input type="text" name="user" id="user" value="<?php echo $user;?>" /></p>
    <p><label for="password">Mot de passe :</label> <input type="text" name="password" id="password" value="<?php echo $password;?>" /></p>

<?php
  if( $erreurs == 102 ) echo '<p style="color:red;">Connexion à la base de donnée impossible !</p>';
?>

    <p><label for="bdd">Base de donnée :</label> <input type="text" name="bdd" id="bdd" value="<?php echo $bdd;?>" /></p>
  </fieldset>
  <fieldset>
    <legend>Information CMS</legend>
    <p><label for="prefix">Prefix des tables :</label> <input type="text" name="prefix" id="prefix" value="<?php echo $prefix;?>" /></p>
  </fieldset>
  <p align="center"><input type="submit" name="etape1" value="Tester et passer (peut-être) à l'étape 2" /></p>
</form>

<?php
// pied de page
include(__DIR__.'/footer.php');
?>