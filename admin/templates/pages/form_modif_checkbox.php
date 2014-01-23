<?php

include( __DIR__ . '/form.php' );

?>
<script type="text/javascript">
var numCheckbox = <?php echo $nb_choix;?>;
$(document).ready(function() {
  $('#ajout_choix_checkbox').click( function () {
    $('#choix_checkbox').append('<input type="text" name="choix['+numCheckbox+'][alias]" /> => <input type="text" name="choix['+numCheckbox+'][texte]" />, <input type="radio" name="choix['+numCheckbox+'][value]" value="1" /> Oui <input type="radio" name="choix['+numCheckbox+'][value]" value="0" checked="checked" /> Non<br />');
    numCheckbox++;
  });
});
</script>
<h2 style="margin-left: 30px;">Checkbox</h2>
<form class="form_admin_modif_create" method="post" action="./utils/create_form.php?modif=<?php echo $id;?>&amp;checkbox&amp;article=<?php echo $_GET['article'];?>&amp;jeton=<?php echo $_SESSION['jeton'];?>">
  <fieldset>
    <legend>D&eacute;finition</legend>
    <label for="checkbox_label">Label : </label><input type="text" id="checkbox_label" name="checkbox_label" value="<?php echo $label;?>" /><br />
    <label for="checkbox_desc">Description : </label><input type="text" id="checkbox_desc" name="checkbox_desc" value="<?php echo $texte;?>" />
  </fieldset>
  <fieldset>
    <legend>Options</legend>
    <label for="checkbox_selmini">S&eacute;lection minimum : </label><input type="text" id="checkbox_selmini" name="checkbox_selmini" value="<?php echo $nbchoix_mini;?>" /><br />
    <label for="checkbox_selmax">S&eacute;lection maximum : </label><input type="text" id="checkbox_selmax" name="checkbox_selmax" value="<?php echo $nbchoix_max;?>" /><br />
    <a id="ajout_choix_checkbox" href="#">&raquo; Ajoutez des champs</a> : Alias => Texte, S&eacute;lectionner par d&eacute;faut.
    <div id="choix_checkbox">
<?php
        $i = 0;
        foreach( $choix AS $alias => $choix ) {
          echo '<input type="text" name="choix[',$i,'][alias]" value="',$alias,'" /> => <input type="text" name="choix[',$i,'][texte]" value="',$choix,'" />, ';
          if( in_array( $alias, $value ) )
          {
            echo '<input type="radio" name="choix[',$i,'][value]" value="1" checked="checked" /> Oui <input type="radio" name="choix[',$i,'][value]" value="0" /> Non<br />';
          }
          else
          {
            echo '<input type="radio" name="choix[',$i,'][value]" value="1" /> Oui <input type="radio" name="choix[',$i,'][value]" value="0" checked="checked" /> Non<br />';
          }
          $i++;
        }
?>
    </div>
  </fieldset>
  <p style="margin-left: 30px;"><input type="submit" value="Cr&eacute;er le champ checkbox" /></p>
</form>
