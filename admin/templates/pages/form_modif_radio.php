<?php

include( __DIR__ . '/form.php' );

?>
<script type="text/javascript">
var numRadio = <?php echo $nb_choix;?>;
$(document).ready(function() {
  $('#ajout_choix_radio').click( function () {
    $('#choix_radio').append('<input type="text" name="choix['+numRadio+'][alias]" /> => <input type="text" name="choix['+numRadio+'][texte]" />, <input type="radio" name="value" value="'+numRadio+'" /><br />');
    numRadio++;
  });
});
</script>
<h2 style="margin-left: 30px;">Radio</h2>
<form class="form_admin_modif_create" method="post" action="./utils/create_form.php?modif=<?php echo $id;?>&amp;radio&amp;article=<?php echo $_GET['article'];?>&amp;jeton=<?php echo $_SESSION['jeton'];?>">
  <fieldset>
    <legend>D&eacute;finition</legend>
    <label for="radio_label">Label : </label><input type="text" id="radio_label" name="radio_label" value="<?php echo $label;?>" /><br />
    <label for="radio_desc">Description : </label><input type="text" id="radio_desc" name="radio_desc" value="<?php echo $texte;?>" />
  </fieldset>
  <fieldset>
    <legend>Options</legend>
    <a id="ajout_choix_radio" href="#">&raquo; Ajoutez des champs</a> : Alias => Texte, S&eacute;lectionner par d&eacute;faut.
    <div id="choix_radio">
<?php
        $i = 0;
        foreach( $choix AS $alias => $choix ) {
          echo '<input type="text" name="choix[',$i,'][alias]" value="',$alias,'" /> => <input type="text" name="choix[',$i,'][texte]" value="',$choix,'" />, <input type="radio" name="value" value="',$i,'"';
          if( $alias == $value )
            echo 'checked="checked" /><br />';
          else
           echo ' /><br />';
          $i++;
        }
?>
    </div>
  </fieldset>
  <p style="margin-left: 30px;"><input type="submit" value="Cr&eacute;er le champ radio" /></p>
</form>
