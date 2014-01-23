<?php

use admin\templates\Pages;

include( __DIR__ . '/article.php' );

?>
<div class="select"><p><a class="bouton" href="article.php?ref=<?php echo $reference;?>&amp;dossier=<?php echo $preselected_dossier;?>&amp;cat=<?php echo $preselected_cat;?>">&laquo; Revenir</a></p></div>
<script type="text/javascript">
  $(document).ready(function() {
    var etat_toggle_menu_select = false;
    $("#toggle_menu_select").click(function()
    {
      if( !etat_toggle_menu_select )
      {
        $("#menu_select_afficher").css({ 'display' : 'none' });
        $("#menu_select").css({ 'width' : '10px' });
        $(this).html('<b>&laquo;</b>');
        $(".contenu").css({ 'margin-right' : '80px' });
        etat_toggle_menu_select = true;
      }
      else
      {
        $("#menu_select_afficher").css({ 'display' : 'block' });
        $("#menu_select").css({ 'width' : '280px' });
        $(this).html('<b>&raquo;</b>');
        $(".contenu").css({ 'margin-right' : '360px' });
        etat_toggle_menu_select = false;
      }
    });
  });
</script>
  <form method="post" action="utils/article.php?d=<?php echo $preselected_dossier;?>&amp;r=<?php echo $reference;?>&amp;jeton=<?php echo $_SESSION['jeton'];?>">
    <div id="menu_select" class="border_radius_6">
      <p id="toggle_menu_select" align="right"><b>&raquo;</b></p>
      <div id="menu_select_afficher">
      <p><label for="cat">Catégorie :</label>
        <select name="cat" id="cat" >
          <option value="0">Aucune catégorie</option>
          <?php Pages::getListOptionCat( $select_cat, $donnees_article['cat_id'] ); ?>
        </select>
      </p>
<?php
      if( $reference == 0 )
      {
        echo '<p>Tags :<br />';
        
        foreach( $select_tag AS $tagis )
        {
          echo in_array( $tagis['cat_id'], $tag ) ? '<input type="checkbox" checked="checked" name="tag[]" id="tag_'.$tagis['cat_id'].'" value="'.$tagis['cat_id'].'" />' : '<input type="checkbox" name="tag[]" id="tag_'.$tagis['cat_id'].'" value="'.$tagis['cat_id'].'" />';
          // label
          echo '<label for="tag_',$tagis['cat_id'],'">',$tagis['cat_nom'],'</label><br />'."\n";
        }

        echo '</p>';
      }
?>
      <p><label for="niveau_comments">Nombre de niveau de sous-commentaire possible : <small><q>0 = aucun commentaire</q></small> :</label>
      <input type="text" style="width:20px;" name="niveau_comments" id="niveau_comments" value="<?php echo $donnees_article['niveau_comments'];?>" /></p>
      <p><label for="brouillon">Brouillon : </label><input type="checkbox" name="brouillon" <?php if($donnees_article['brouillon']) echo 'checked="checked"';?> id="brouillon" /></p>
        <input type="radio" name="encode" value="html" id="encode_html" /><label for="encode_html">Html <small>Aucun encodage de caractère</small></label><br />
        <input type="radio" name="encode" value="text" checked="checked" id="encode_text" /><label for="encode_text">Texte <small>Encodage html &amp; template</small></label><br />
      <p align="right"><input type="submit" value="Enregistrer l'article" /></p>
      </div>
    </div>
    <hr style="margin-top:20px;border:none;" />
    <div class="label"><label for="titre">Titre :</label></div>
    <div class="contenu"><input type="text" name="titre" id="titre" value="<?php echo $donnees_article['article_titre'];?>" /></div>
    <div class="label"><label for="date">Date d'apparition :</label></div>
    <div class="contenu"><input type="text" name="date" id="date" value="<?php echo $donnees_article['article_date_creer'];?>" /></div>
    <div class="label"><label for="date_max">Date de fin :<br /><small>saisez vide si aucune</small></label></div>
    <div class="contenu"><input type="text" name="date_max" value="<?php echo $donnees_article['article_date_max'];?>" id="date_max" /></div>
    <hr style="margin-top:40px;border:none;" />
    <div class="label"><label for="chapo">Chapô :</label></div>
    <div class="contenu"><textarea id="chapo" style="height:200px" name="chapo"><?php echo $donnees_article['article_chapo'];?></textarea></div>
    <div class="label"><label for="chapo">Article :</label></div>
    <div class="contenu"><textarea id="text" style="height:300px" name="text"><?php echo $donnees_article['article_texte'];?></textarea></div>
  </form>
