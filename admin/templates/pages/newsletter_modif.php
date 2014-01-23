<?php

use systems\cfg\general;

?>
  <div class="select">
    <p><a class="bouton" href="./article.php">&laquo; Revenir aux articles</a> <a class="bouton" href="./newsletter.php">&laquo; Revenir aux newsletters</a></p>
  </div>
<?php

  if( !general\NEWSLETTER )
    echo '<div class="avertissement">La newsletter n\'est pas activ&eacute;, si vous d&eacute;sirez l\'activez, allez dans la configuration g&eacute;n&eacute;ral.</div>';

?>
	<h2>Modifie la newsletter "<?php echo $newsletter['newsletter_titre'] ?>"</h2>
  <fieldset>
    <legend>Modifier</legend>
    <form method="post" action="./utils/newsletter.php?n=<?php echo $newsletter['newsletter_id'] ?>&amp;jeton=<?php echo $_SESSION['jeton'];?>">
      <label for="page">Page de r&eacute;f&eacute;rence :</label>
      <select name="page" id="page" tabindex="1">
<?php

  foreach( $select_arbo AS $id => $arbo )
  {
    if( $id == $newsletter['page_id'] )
      echo '<option value="',$id,'" selected="selected">',$arbo,'</option>';
    else
      echo '<option value="',$id,'">',$arbo,'</option>';
  }
?>
      </select><br />
      <label for="autopost">Envoyez une mail &agrave; chaque nouveau premier article ?</label>
      <input type="checkbox" id="autopost" name="autopost" <?php if( $newsletter['newsletter_auto'] ) echo 'checked="checked"' ?> /><br />
      <label for="titre">Titre :</label>
      <input type="text" id="titre" name="titre" value="<?php echo $newsletter['newsletter_titre'] ?>" />
      <p style="margin-left: 20px;"><input type="submit" value="Modifier" /></p>
    </form>
  </fieldset>
