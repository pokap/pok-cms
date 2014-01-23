<?php

use admin\templates\Pages;

include( __DIR__ . '/article.php' );

?>
<div class="select">
  <p>
    <a class="bouton" href="./newsletter.php">Newsletter</a>
    <a class="bouton" href="article.php?new&amp;page=<?php echo $preselected_dossier;?>&amp;cat=<?php echo $preselected_cat;?>&amp;ref=<?php echo $reference;?>">Rédiger un nouvelle article ici</a>
  </p>
</div>
<div class="select">
  <div class="select_page">Page : <?php Pages::get_list_page( $url_page, 'numpagearticle', 'article', $url_page );?></div>
<?php

    if( $reference > 0 )
      echo '<a class="bouton" href="article.php?page=',$preselected_dossier,'&amp;cat=',$preselected_cat,'"><strong>&laquo; Revenir</strong></a> |';

?>
    <form method="get" action="">
      <label for="page">Page :</label>
      <select name="page" id="page" tabindex="1">
        <?php Pages::getListOptionId( $select_arbo, $preselected_dossier ); ?>
      </select>
      <label for="cat">Catégorie :</label>
      <select name="cat" id="cat" tabindex="1">
        <option value="">Toutes les catégories</option>
        <?php Pages::getListOptionCat( $select_cat, $preselected_cat ); ?>
      </select>
      <input type="submit" value="Filtrer" />
    </form>
    <?php 
    ?>
  </div>
<table class="dossier">
  <tr class="partie">
    <th colspan="4" class="option"><-></th>
    <th width="1%">#ID</th>
    <th width="1%">*</th>
    <th width="56%">Post</th>
    <th width="10%">Auteur</th>
    <th width="15%">Categorie</th>
    <th width="15%">Date</th>
  </tr>
<?php

$i = 0;
foreach( $articles AS $article )
{
  $id_post_view = $article['count'] > 0 ? $article['article_id'] : $reference;
  $color = ( $i % 2 != 0 ) ? ' class="pair"' : '';

?>
  <tr <?php echo $color;?>>
    <td><a href="article.php?modif&amp;article=<?php echo $article['article_id'];?>"><img src="images/b_edit.png" alt="Modifier" title="Modifier le post" /></a></td>
    <td><a href="#" onClick="verif('utils/supprimer_article.php?article=<?php echo $article['article_id'];?>&amp;jeton=<?php echo $_SESSION['jeton'];?>');"><img src="images/b_drop.png" alt="Supprimer" title="Supprimer le post" /></a></td>
    <td><a href="../index.php?d=<?php echo $article['page_id'];?>&amp;article=<?php echo $id_post_view;?>"><img src="images/b_select.png" alt="Voir" title="Voir la page" /></a></td>
    <td><a href="./form.php?article=<?php echo $article['article_id'];?>"><img src="images/formulaire.png" alt="Formulaire" title="Gérer le formulaire" /></a></td>
    <td><?php echo $article['article_id'];?></td>
    <td align="center"><a href="article.php?niveau=<?php echo $article['article_niveau'];?>&amp;page=<?php echo $preselected_dossier;?>&amp;cat=<?php echo $preselected_cat;?>&amp;ref=<?php echo $article['article_id'];?>"><?php echo $article['count'];?></a></td>
    <td style="font-weight:bold;"><?php echo $article['article_titre'];?></td>
    <td>
<?php if( $article['article_auteur'] > 0 ) { ?>
        <a href="./membre.php?modif&m=<?php echo $article['article_auteur'];?>"><?php echo $article['pseudo_auteur'];?></a>
<?php } else { ?>
        n/a
<?php } ?>
    </td>

<?php     if( !empty( $article['cat_nom'] ) ) { ?>
    <td><?php echo $article['cat_nom'];?></td>
<?php     } else { ?>
    <td>Aucune catégorie</td>
<?php     } ?>

    <td align="center"><?php echo $article['article_date_reviser'];?></td>
  </tr>

<?php
    
    $i++;
  }
  
?>
</table>
<?php
  
if( isset($_GET['niveau']) && $_GET['niveau'] == 1 )
{
  
?>
<script type="text/javascript">
$(document).ready(function(){
  $("#toggle_deplacement").click(function(){
    $(".deplacement_switch").toggle();
    $(".deplacement_icone").toggle();
    $("#deplacement_id").val("");
  });
});
</script>
<div style="margin-top: 10px;">
<fieldset>
  <legend>Options</legend>
<form method="post" action="utils/deplacer_article.php?p=<?php echo $reference;?>&amp;jeton=<?php echo $_SESSION['jeton'];?>">
  <img src="images/deplace.png" alt="Déplace" title="Déplace la page" /> Déplacer ce sujet vers la page
  <span class="deplacement_switch">: <select name="deplacement" id="deplacement" tabindex="1">
<?php

    foreach( $select_arbo AS $id => $arbo )
      if( $id != $id_dossier )
        echo '<option value="',$id,'">',$arbo,'</option>';

?>
  </select>
  </span>
  <span class="deplacement_switch" style="display: none;">ID : <input name="deplacement_id" id="deplacement_id" tabindex="2" /></span>
  <a href="#" class="toggle" id="toggle_deplacement"><span title="Manuel" class="deplacement_icone">M</span><span title="Assisté" class="deplacement_icone" style="display: none;">A</span></a>
  <input type="submit" value="Déplacer le sujet parent" />
</form>
</fieldset>
</div>
<?php

}
