<?php

include( __DIR__ . '/form.php' );

?>
<script type="text/javascript">
var numCheckbox = 1;
var numRadio = 1;
var numSelect = 1;
$(document).ready(function() {
	$('#select_normal').click( function () {
    $('#normal').css("display","block");
    $('#checkbox').css("display","none");
    $('#radio').css("display","none");
    $('#select').css("display","none");
    $('#textarea').css("display","none");
  });
	$('#select_checkbox').click( function () {
    $('#normal').css("display","none");
    $('#checkbox').css("display","block");
    $('#radio').css("display","none");
    $('#select').css("display","none");
    $('#textarea').css("display","none");
  });
	$('#select_radio').click( function () {
    $('#normal').css("display","none");
    $('#checkbox').css("display","none");
    $('#radio').css("display","block");
    $('#select').css("display","none");
    $('#textarea').css("display","none");
  });
	$('#select_select').click( function () {
    $('#normal').css("display","none");
    $('#checkbox').css("display","none");
    $('#radio').css("display","none");
    $('#select').css("display","block");
    $('#textarea').css("display","none");
  });
	$('#select_textarea').click( function () {
    $('#normal').css("display","none");
    $('#checkbox').css("display","none");
    $('#radio').css("display","none");
    $('#select').css("display","none");
    $('#textarea').css("display","block");
  });
  
  $('#ajout_choix_checkbox').click( function () {
    $('#choix_checkbox').append('<input type="text" name="choix['+numCheckbox+'][alias]" /> => <input type="text" name="choix['+numCheckbox+'][texte]" />, <input type="radio" name="choix['+numCheckbox+'][value]" value="1" /> Oui <input type="radio" name="choix['+numCheckbox+'][value]" value="0" checked="checked" /> Non<br />');
    numCheckbox++;
  });

  $('#ajout_choix_radio').click( function () {
    $('#choix_radio').append('<input type="text" name="choix['+numRadio+'][alias]" /> => <input type="text" name="choix['+numRadio+'][texte]" />, <input type="radio" name="value" value="'+numRadio+'" /><br />');
    numRadio++;
  });

  $('#ajout_choix_select').click( function () {
    $('#choix_select').append('<input type="text" name="choix['+numSelect+'][alias]" /> => <input type="text" name="choix['+numSelect+'][texte]" />, <input type="radio" name="value" value="'+numSelect+'" /><br />');
    numSelect++;
  });
});
</script>
<div class="select">
  <p><a id="select_normal" href="#">Normal</a> | <a id="select_checkbox" href="#">Checkbox</a> | <a id="select_radio" href="#">Radio</a> | <a id="select_select" href="#">Select</a> | <a id="select_textarea" href="#">Textarea</a></p>
</div>

<!--  NORMAL  -->
<div id="normal">
  <h2 style="margin-left: 30px;">Normal</h2>
  <form class="form_admin_modif_create" method="post" action="./utils/create_form.php?normal&amp;article=<?php echo $_GET['article'];?>&amp;jeton=<?php echo $_SESSION['jeton'];?>">
  <fieldset>
    <legend>Définition</legend>
    <label for="normal_label">Label : </label><input type="text" id="normal_label" name="normal_label" /><br />
    <label for="normal_desc">Description : </label><input type="text" id="normal_desc" name="normal_desc" />
  </fieldset>
  <fieldset>
    <legend>Options</legend>
    <label for="normal_strmini">Caractère minimum : </label><input type="text" id="normal_strmini" name="normal_strmini" value="1" /><br />
    <label for="normal_strmax">Caractère maximum : </label><input type="text" id="normal_strmax" name="normal_strmax" value="25" /><br />
    <label for="normal_value">Valeur : </label><input type="text" id="normal_value" name="normal_value" /><br />
    <label for="normal_type">Type : </label>
    <select id="normal_type" name="normal_type">
      <option value="texte">Texte</option>
      <option value="chiffre">Chiffre</option>
      <option value="email">E-mail</option>
      <option value="phone">Téléphone</option>
    </select>
  </fieldset>
  <p style="margin-left: 30px;"><input type="submit" value="Créer le champ normal" /></p>
  </form>
</div>
<!--  CHECKBOX  -->
<div id="checkbox" style="display:none;">
  <h2 style="margin-left: 30px;">Checkbox</h2>
  <form class="form_admin_modif_create" method="post" action="./utils/create_form.php?checkbox&amp;article=<?php echo $_GET['article'];?>&amp;jeton=<?php echo $_SESSION['jeton'];?>">
  <fieldset>
    <legend>Définition</legend>
    <label for="checkbox_label">Label : </label><input type="text" id="checkbox_label" name="checkbox_label" /><br />
    <label for="checkbox_desc">Description : </label><input type="text" id="checkbox_desc" name="checkbox_desc" />
  </fieldset>
  <fieldset>
    <legend>Options</legend>
    <label for="checkbox_selmini">Sélection minimum : </label><input type="text" id="checkbox_selmini" name="checkbox_selmini"  /><br />
    <label for="checkbox_selmax">Sélection maximum : </label><input type="text" id="checkbox_selmax" name="checkbox_selmax" /><br />
    <a id="ajout_choix_checkbox" href="#">&raquo; Ajoutez des champs</a> : Alias => Texte, Sélectionner par défaut.
    <div id="choix_checkbox">
    <input type="text" name="choix[0][alias]" /> => <input type="text" name="choix[0][texte]" />, <input type="radio" name="choix[0][value]" value="1" /> Oui <input type="radio" name="choix[0][value]" value="0" checked="checked" /> Non<br />
    </div>
  </fieldset>
  <p style="margin-left: 30px;"><input type="submit" value="Créer le champ checkbox" /></p>
  </form>
</div>
<!--  RADIO  -->
<div id="radio" style="display:none;">
  <h2 style="margin-left: 30px;">Radio</h2>
  <form class="form_admin_modif_create" method="post" action="./utils/create_form.php?radio&amp;article=<?php echo $_GET['article'];?>&amp;jeton=<?php echo $_SESSION['jeton'];?>">
  <fieldset>
    <legend>Définition</legend>
    <label for="radio_label">Label : </label><input type="text" id="radio_label" name="radio_label" /><br />
    <label for="radio_desc">Description : </label><input type="text" id="radio_desc" name="radio_desc" />
  </fieldset>
  <fieldset>
    <legend>Options</legend>
    <a id="ajout_choix_radio" href="#">&raquo; Ajoutez des champs</a> : Alias => Texte, Sélectionner par défaut.
    <div id="choix_radio">
    <input type="text" name="choix[0][alias]" /> => <input type="text" name="choix[0][texte]" />, <input type="radio" name="value" value="0" checked="checked" /><br />
    </div>
  </fieldset>
  <p style="margin-left: 30px;"><input type="submit" value="Créer le champ radio" /></p>
  </form>
</div>
<!--  SELECT  -->
<div id="select" style="display:none;">
  <h2 style="margin-left: 30px;">Select</h2>
  <form class="form_admin_modif_create" method="post" action="./utils/create_form.php?select&amp;article=<?php echo $_GET['article'];?>&amp;jeton=<?php echo $_SESSION['jeton'];?>">
  <fieldset>
    <legend>Définition</legend>
    <label for="select_label">Label : </label><input type="text" id="select_label" name="select_label" /><br />
    <label for="select_desc">Description : </label><input type="text" id="select_desc" name="select_desc" />
  </fieldset>
  <fieldset>
    <legend>Options</legend>
    <a id="ajout_choix_select" href="#">&raquo; Ajoutez des champs</a> : Alias => Texte, Sélectionner par défaut.
    <div id="choix_select">
    <input type="text" name="choix[0][alias]" /> => <input type="text" name="choix[0][texte]" />, <input type="radio" name="value" value="0" checked="checked" /><br />
    </div>
  </fieldset>
  <p style="margin-left: 30px;"><input type="submit" value="Créer le champ select" /></p>
  </form>
</div>
<!--  TEXTAREA  -->
<div id="textarea" style="display:none;">
  <h2 style="margin-left: 30px;">Textarea</h2>
  <form class="form_admin_modif_create" method="post" action="./utils/create_form.php?textarea&amp;article=<?php echo $_GET['article'];?>&amp;jeton=<?php echo $_SESSION['jeton'];?>">
  <fieldset>
    <legend>Définition</legend>
    <label for="textarea_label">Label : </label><input type="text" id="textarea_label" name="textarea_label" /><br />
    <label for="textarea_desc">Description : </label><input type="text" id="textarea_desc" name="textarea_desc" />
  </fieldset>
  <fieldset>
    <legend>Options</legend>
    <label for="textarea_strmini">Caractère minimum : </label><input type="text" id="textarea_strmini" name="textarea_strmini" value="1" /><br />
    <label for="textarea_strmax">Caractère maximum : </label><input type="text" id="textarea_strmax" name="textarea_strmax" value="255" /><br />
    <label for="textarea_value">Valeur : </label><input type="text" id="textarea_value" name="textarea_value" />
  </fieldset>
  <p style="margin-left: 30px;"><input type="submit" value="Créer le champ textarea" /></p>
  </form>
</div>