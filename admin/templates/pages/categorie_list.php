<h2>Liste des catégories</h2>

<table class="dossier">
  <tr class="partie">
    <th colspan="2" class="option"><-></th>
    <th style="width:9%;"><a href="categorie.php?trie=<?php echo $trie[$_GET['trie']];?>&champ=cat_id"># ID <?php echo $img_trie['cat_id'];?></a></th>
    <th style="width:70%;"><a href="categorie.php?trie=<?php echo $trie[$_GET['trie']];?>&champ=cat_nom">Nom <?php echo $img_trie['cat_nom'];?></a></th>
    <th style="width:20%;"><a href="categorie.php?trie=<?php echo $trie[$_GET['trie']];?>&champ=taxon">Pour <?php echo $img_trie['taxon'];?></a></th>
  </tr>
<?php

  $i = 0;
  foreach( $cat_list AS $cat ) 
  {
    $color = ( $i % 2 != 0 ) ? ' class="pair"': NULL;
    echo '<tr',$color,">\n",
      '<td><a href="categorie.php?modifier&amp;c=',$cat['cat_id'],'&amp;nom=',$cat['cat_nom'],'&amp;taxon=',$cat['taxon'],'"><img src="images/b_edit.png" alt="Modifier" title="Modifier la catégorie" /></a></td>',
      '<td><a href="#" onClick="verif(\'utils/supprimer_cat.php?c=',$cat['cat_id'],'&amp;jeton=',$_SESSION['jeton'],'\');"><img src="images/b_drop.png" alt="Supprimer" title="Supprimer la catégorie" /></a></td>',
      '<td>',$cat['cat_id'],"</td>\n",
      '<td>',$cat['cat_nom'],"</td>\n",
      '<td>',$cat['taxon'],"</td>\n",
    "</tr>\n";
    $i++;
  }
  
?>
</table>
<div style="margin-top: 10px;">
<fieldset>
  <legend>Nouvelle Catégorie</legend>
  <form method="post" action="utils/create_cat.php?jeton=<?php echo $_SESSION['jeton'];?>">
    <label for="nom">Nom * :</label>
    <input type="text" name="nom" id="nom" tabindex="2" />
    <label for="taxon">Pour * :</label>
    <select name="taxon" id="taxon" tabindex="1">
<?php

  foreach( $taxon_array AS $taxon ) {
    echo '<option value="',$taxon,'">',$taxon,'</option>';
  }
  
?>
    </select>
    <input type="submit" value="Créer la catégorie" />
  </form>
</fieldset>
</div>