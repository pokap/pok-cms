<h2>Les utilisateurs du groupe "<?php echo $_GET['name'];?>" on-t-il droits à :</h2>
<fieldset>
  <legend>Modification</legend>
  <form method="post" action="utils/create_droit.php?g=<?php echo $_GET['g'];?>&amp;name=<?php echo $_GET['name'];?>&amp;jeton=<?php echo $_SESSION['jeton'];?>">
  <table class="dossier">
    <tr class="partie">
      <th>#ID</th>
      <th>Nom</th>
      <th>Pour</th>
      <th>Voir les pages</th>
      <th>Ecrire un nouvel article</th>
      <th>Repondre aux articles</th>
      <th>Bloquer les reponses</th>
      <th>Editer tous les articles</th>
      <th>Supprimer tous les articles</th>
      <th>Supprimer son article</th>
      <th>Modifier la duree d'un article</th>
    </tr>
<?php
    foreach( $cat_list AS $num => $cat ) 
    {
      $color = ( $num % 2 != 0 )? ' class="pair"': '';
      echo '<tr' , $color , ">\n" ,
        '<td>' , $cat['cat_id'] , "</td>\n" ,
        '<td>' , $cat['cat_nom'] , "</td>\n" ,
        '<td>' , $cat['taxon'] , "</td>\n";
      
      for( $s = 0; $s < 8; $s++ )
      {
        if( array_key_exists( $cat['cat_id'], $all_rules ) && $all_rules[$cat['cat_id']][$tab_droits[$s]] > 0 )
        {
          echo '<td align="center" style="background:#AFA;">
            <select name="' , $cat['cat_id'] , '[' , $tab_droits[$s] , ']" tabindex="' , ( $s + 1 ) , '">
            <option value="0">Non</option>
            <option value="1" selected="selected">Oui</option>
            </select>
          </td>';
        }
        else
        {
          echo '<td align="center" style="background:#FAA;">
            <select name="' , $cat['cat_id'] , '[' , $tab_droits[$s] , ']" tabindex="' , ( $s + 1 ) , '">
            <option value="0" selected="selected">Non</option>
            <option value="1">Oui</option>
            </select>
          </td>';
        }
      }
      echo "</tr>\n";
    }
    
?>
  </table>
  <hr style="margin-top: 20px; border-left: none; border-right: none;" />
  <p style="margin-left: 30px;"><input type="submit" value="Mettre à jour le groupe"/></p>
</fieldset>
</form>
