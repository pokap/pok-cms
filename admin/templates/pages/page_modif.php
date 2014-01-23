<?php

use pok\Apps\Page;
use pok\Apps\Outils\Base\Fichier;

include( __DIR__ . '/page.php' );

?>
<h2>Modifier <?php echo $infos['page_nom'];?></h2>

<fieldset>
  <legend>Modifier Page</legend>
  <form class="form_admin_modif_create" method="post" action="utils/create_page.php?page=<?php echo $_GET['return'];?>&amp;courant=<?php echo $infos['page_id'];?>&amp;modif&amp;jeton=<?php echo $_SESSION['jeton'];?>">
  <p>
    <label for="cat_id">Catégorie * :</label>
    <select name="cat_id" id="cat_id" tabindex="1">
<?php

		foreach( $cat_list AS $cat ) {
			if( $cat['cat_id'] == $infos['cat_id'] )
				echo '<option value="',$cat['cat_id'],'" selected="selected">',$cat['cat_nom'],'</option>';
			else
				echo '<option value="',$cat['cat_id'],'">',$cat['cat_nom'],'</option>';
		}

?>
	</select><br />
	<label for="page_nom">Page * :</label>
	<input type="text" name="page_nom" id="page_nom" value="<?php echo $infos['page_nom'];?>" tabindex="2" /><br />
  <label for="resume">Résumer :</label>
  <input type="text" name="resume" id="resume" value="<?php echo end( Page::explode($infos['arborescence']) );?>" tabindex="3" /><br />
	<label for="page_description">Description * :</label>
	<input type="text" name="page_description" id="page_description" value="<?php echo $infos['page_description'];?>" tabindex="4" /><br />
	<label for="page_ordre">Ordre * :</label>
	<input type="text" name="page_ordre" id="page_ordre" value="<?php echo $infos['page_ordre'];?>" tabindex="5" /><br />
  <label for="template">Template :</label>
  <select name="template" id="template" tabindex="6">
<?php
  // on charge la liste des templates
  $liste_templates = Fichier::lister( ADRESSE_TEMPLATES, Fichier::DIR, array('.svn') );
  foreach( $liste_templates AS $template )
  {
    if( $template == $infos['template'] )
      echo '<option value="' , $template , '" selected="selected">' , $template , '</option>';
    elseif( $template[0] != '_' )
      echo '<option value="' , $template , '">' , $template , '</option>';
  }
?>
  </select><br />
  <label for="nonlu">Système Lu / Non-lu :</label>
  <input type="radio" name="nonlu" id="lu" value="1" tabindex="9" <?php echo $infos['nonlu'] == 1 ? 'checked="checked"' :'';?> /> Oui
  <input type="radio" name="nonlu" id="nonlu" value="0" tabindex="10" <?php echo $infos['nonlu'] == 0 ? 'checked="checked"' :'';?> /> Non
  <p style="margin-left: 30px;"><input type="submit" value="Modifier" /></p>
  </p>
</form>
</fieldset>
