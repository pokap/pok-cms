<fieldset>
  <legend>Nouvelle Catégorie</legend>
  <form method="post" action="utils/create_cat.php?modif&amp;c=<?php echo $_GET['c'];?>&amp;jeton=<?php echo $_SESSION['jeton'];?>">
  <p>
    <label for="nom">Nom * :</label>
    <input type="text" name="nom" id="nom" value="<?php echo $_GET['nom'];?>" tabindex="1" />
    <label for="taxon">Pour * :</label>
    <select name="taxon" id="taxon" tabindex="1">

<?php
    foreach( $taxon_list AS $taxon ) {
      if( $taxon == $_GET['taxon'] )
        echo '<option value="',$taxon,'" selected="selected">',$taxon,'</option>';
      else
        echo '<option value="',$taxon,'">',$taxon,'</option>';
    }
?>

    </select>
    <input type="submit" value="Modifier" />
  </p>
  </form>
</fieldset>