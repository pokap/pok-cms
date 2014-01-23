<?php
/* -------------------------------------
  Si l'utilisateur n'est  pas connecté, on affiche le formulaire de connexion.
    > voir la documentation sur la création d'un formulaire de connexion
*/
if( !pok\Apps\Outils\Session::connecter() )
{
?>
<h3>Connexion : </h3>
<form method="post" action="connexion.php?page=<?php echo pok\Controleur\Page::$actuelle['arborescence']; if( $article_slug != null ) echo '&amp;article=',$article_slug;?>">
  <input type="text" name="login" id="login" />
  <input type="password" name="password" id="password" />
  <input type="submit" value="Se connecter" />
</form>
<?php
}
else
{
/* -------------------------------------
  Sinon on affiche des informations de l'utilisateur
*/
?>
<h3>Bonjour <?php echo $_SESSION['pseudo'];?> : </h3>
<ul>
  <li><a href="deconnexion.php?d=<?php echo pok\Controleur\Page::$actuelle['arborescence']; if( $article_slug != null ) echo '&amp;article=',$article_slug;?>">Vous déconnectez</a></li>
  <li><a href="<?php echo pok\Controleur\Page::url('membres/'.pok\Texte::slcs($_SESSION['pseudo']));?>">Votre profil</a></li>
</ul>
<?php
}
?>