<?php
// en-tête
include(__DIR__.'/header.php');
?>

<p>Indiquez vos identifiants principals pour votre compte admin.</p>
<form method="post" action="">
  <fieldset>
    <legend>Information admin</legend>

<?php 
if( $erreurs == 201 ) echo '<p style="color:red;">Vous avez oubliez de remplir tous les champs.</p>';
?>

    <p><label for="pseudo">Pseudo : </label><input type="text" name="pseudo" id="pseudo" value="<?php echo $pseudo ?>" /></p>
    <p><label for="mdp">Mot de passe : </label><input type="text" name="mdp" id="mdp" value="<?php echo $mdp ?>" /></p>
  </fieldset>
  <fieldset>
    <legend>Configuration g&eacute;n&eacute;ral</legend>
    <p>Configurer votre systèmes pour les redirections et simplifier l'exécution d'url dans les templates.<br />
    L'adresse complete ( Adresse serveur + Compl&eacute;mentaire ) doit correspondre a l'url du votre site (sans le protocole, exemple : "www.monsite.com/pok/").</p>
    <p><label for="server_name">Adresse serveur : </label><input type="text" name="server_name" id="server_name" value="<?php echo $server_name ?>" /></p>
    <p><label for="server_path">Compl&eacute;mentaire : </label><input type="text" name="server_path" id="server_path" value="<?php echo $server_path ?>" /></p>
  </fieldset>
  <fieldset>
    <legend>SALT script password</legend>
    <p>Le SALT permet d'améliorer la sécurité de scriptage de mot de passe.</p>
    <p><label for="prefix_salt">Prefix SALT : </label><input type="text" name="prefix_salt" id="prefix_salt" value="<?php echo $prefix_salt ?>" /></p>
    <p><label for="suffix_salt">Suffix SALT : </label><input type="text" name="suffix_salt" id="suffix_salt" value="<?php echo $suffix_salt ?>" /></p>
    <p>CRYPT, Si l'argument n'est pas fourni, le comportement est défini par l'implémentation de l'algorithme et peut provoquer des résultats inattendus. <a href="http://fr2.php.net/manual/fr/function.crypt.php">function:crypt()</a></a></p>
    <p><label for="crypt">CRYPT : </label><input type="text" name="crypt" id="crypt" value="<?php echo $crypt;?>" /></p>
  </fieldset>
  <p align="center"><input type="submit" name="etape2" value="Passez à la dernière étape" /></p>
</form>
<?php

// pied de page
include(__DIR__.'/footer.php');
