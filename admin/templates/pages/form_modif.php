<?php

include( __DIR__ . '/form.php' );

?>
<h2 style="margin-left: 30px;">Normal</h2>
<form class="form_admin_modif_create" method="post" action="./utils/create_form.php?modif=<?php echo $id;?>&amp;normal&amp;article=<?php echo $_GET['article'];?>&amp;jeton=<?php echo $_SESSION['jeton'];?>">
  <fieldset>
    <legend>D&eacute;finition</legend>
    <label for="normal_label">Label : </label><input type="text" id="normal_label" name="normal_label" value="<?php echo $label;?>" /><br />
    <label for="normal_desc">Description : </label><input type="text" id="normal_desc" name="normal_desc" value="<?php echo $texte;?>" />
  </fieldset>
  <fieldset>
    <legend>Options</legend>
    <label for="normal_strmini">Caract&egrave;re minimum : </label><input type="text" id="normal_strmini" name="normal_strmini" value="<?php echo $str_mini;?>" /><br />
    <label for="normal_strmax">Caract&egrave;re maximum : </label><input type="text" id="normal_strmax" name="normal_strmax" value="<?php echo $str_max;?>" /><br />
    <label for="normal_value">Valeur : </label><input type="text" id="normal_value" name="normal_value" value="<?php echo $value;?>" /><br />
    <label for="normal_type">Type : </label>
    <select id="normal_type" name="normal_type">
<?php
    foreach( $default_type AS $alias => $text )
    {
      if( $alias == $type )
        echo '<option value="',$alias,'" selected="selected">',$text,'</option>';
      else
        echo '<option value="',$alias,'">',$text,'</option>';
    }
?>
    </select>
  </fieldset>
  <p style="margin-left: 30px;"><input type="submit" value="Cr&eacute;er le champ normal" /></p>
</form>
