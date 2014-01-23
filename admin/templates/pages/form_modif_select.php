<?php

include( __DIR__ . '/form.php' );

?>
<script type="text/javascript">
var numSelect = <?php echo $nb_choix;?>;
$(document).ready(function() {
  $('#ajout_choix_select').click( function () {
    $('#choix_select').append('<input type="text" name="choix['+numSelect+'][alias]" /> => <input type="text" name="choix['+numSelect+'][texte]" />, <input type="radio" name="value" value="'+numSelect+'" /><br />');
    numSelect++;
  });
});
</script>
<h2 style="margin-left: 30px;">Select</h2>
<form class="form_admin_modif_create" method="post" action="./utils/create_form.php?modif=<?php echo $id;?>&amp;select&amp;article=<?php echo $_GET['article'];?>&amp;jeton=<?php echo $_SESSION['jeton'];?>">
  <fieldset>
    <legend>D&eacute;finition</legend>
    <label for="select_label">Label : </label><input type="text" id="select_label" name="select_label" value="<?php echo $label;?>" /><br />
    <label for="select_desc">Description : </label><input type="text" id="select_desc" name="select_desc" value="<?php echo $texte;?>" />
  </fieldset>
  <fieldset>
    <legend>Options</legend>
    <a id="ajout_choix_select" href="#">&raquo; Ajoutez des champs</a> : Alias => Texte, S&eacute;lectionner par d&eacute;faut.
    <div id="choix_select">
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
  <p style="margin-left: 30px;"><input type="submit" value="Cr&eacute;er le champ select" /></p>
</form>
