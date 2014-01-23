<?php

use admin\templates\Pages;
use pok\Apps\Outils\Base\Fichier;

if( isset($_GET['creerok']) )
  echo '<div class="valide">Création du groupe !</div>';
elseif( isset($_GET['e_creer']) )
  echo '<div class="erreur">Erreur de création du groupe !</div>';

?>
<div class="select"><div class="select_page">Page : <?php Pages::get_list_page( $nb_groupe, 'page', 'groupe' );?></div>&nbsp;</div>
<table class="dossier">
  <tr class="partie">
    <th colspan="3" class="option"><-></th>
    <th>#ID</th>
    <th>Nom</th>
  </tr>
<?php

  foreach( $list_groupe AS $num => $group ) 
  {
    $color = ( $num % 2 != 0 )? ' class="pair"': NULL;
    echo '<tr',$color,">\n",
      '<td><a href="groupe.php?modif&amp;g=',$group['groupe_id'],'"><img src="images/b_edit.png" alt="Modifier" title="Modifier le groupe"/></a></td>',
      '<td><a href="#" onClick="verif(\'utils/supprimer_groupe.php?g=',$group['groupe_id'],'&amp;d=',$group['page_id'],'&amp;jeton=',$_SESSION['jeton'],'\');"><img src="images/b_drop.png" alt="Supprimer" title="Supprimer le groupe"/></a></td>',
      '<td><a href="groupe.php?droit&amp;g=',$group['groupe_id'],'&amp;name=',$group['page_nom'],'"><img src="images/b_props.png" alt="Droits" title="Modifier les droits"/></a></td>',
      '<td>',$group['groupe_id'],"</td>\n",
      '<td><span style="color:#',$group['couleur'],';">',$group['page_nom'],"</span></td>\n",
    "</tr>\n";
  }

?>
</table>
<div style="margin-top: 10px;">
<fieldset>
  <legend>Nouveau Groupe</legend>
  <form class="form_admin_modif_create" method="post" action="utils/create_groupe.php?jeton=<?php echo $_SESSION['jeton'];?>">
  <fieldset>
  <legend>Données Groupe</legend>
  <p>
    <label for="nom">Nom * :</label>
    <input type="text" name="nom" id="nom" value="<?php echo $info_groupe['nom'];?>" tabindex="1"/><br/>
    <label for="couleur">Couleur * : #</label>
    <input type="text" name="couleur" id="couleur" value="<?php echo $info_groupe['couleur'];?>" tabindex="2"/><br/>
  </p>
  </fieldset>
  <fieldset>
  <legend>Données Dossier du groupe</legend>
  <p>
    <label for="cat_id">Catégorie * :</label>
    <select name="cat_id" id="cat_id" tabindex="1">
<?php
  
  foreach( $cat_list AS $cat ) {
    if( $cat['cat_id'] == $info_groupe['cat_id'] )
      echo '<option value="',$cat['cat_id'],'" selected="selected">',$cat['cat_nom'],'</option>';
    else
      echo '<option value="',$cat['cat_id'],'">',$cat['cat_nom'],'</option>';
  }
  
?>
    </select><br/>
    <label for="ordre">Ordre * :</label>
    <input type="text" name="ordre" id="ordre" value="<?php echo $info_groupe['ordre'];?>" tabindex="3"/><br/>
    <label for="template">Template :</label>
    <select name="template" id="template" tabindex="4">
<?php

  // on charge la liste des templates
  $liste_templates = Fichier::lister( ADRESSE_TEMPLATES, Fichier::DIR, array('.svn') );
  foreach( $liste_templates AS $template )
  {
    if( $template == $info_groupe['template'] )
      echo '<option value="' , $template , '" selected="selected">' , $template , '</option>';
    elseif( $template[0] != '_' )
      echo '<option value="' , $template , '">' , $template , '</option>';
  }
  
?>
    </select>
  </p>
  </fieldset>
  <p style="margin-left: 30px;"><input type="submit" value="Créer le nouveau groupe"/></p>
  </form>
</fieldset>
</div>