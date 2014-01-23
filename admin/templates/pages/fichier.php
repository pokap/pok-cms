<?php

use pok\Apps\Outils\Base\Fichier;
use admin\templates\Pages;

?>
<div class="select">
  <p align="right">Page : <?php Pages::get_list_page( $nb_fichier, 'page', 'fichier' );?></p>
</div>
<table class="dossier">
  <tr class="partie">
    <th colspan="2" class="option"><-></th>
    <th width="10%"># ID</th>
    <th width="20%">Titre</th>
    <th width="10%">Taille</th>
    <th>Description</th>
    <th width="10%">Extension</th>
    <th width="1%">Téléchargement</th>
  </tr>
<?php

if( !empty($mes_fichiers) )
{
  foreach( $mes_fichiers AS $num => $files ) 
  {
    $color = ( $num % 2 != 0 ) ? ' class="pair"' : '';
    echo '<tr',$color,">\n",
      '<td><a href="#"><img src="images/b_edit.png" alt="Modifier" title="Modifier le fichier" /></a></td>',
      '<td><a href="#" onClick="verif(\'./fichier.php?suppr&amp;f=',$files['fichier_id'],'&amp;ext=',$files['extension'],'&amp;jeton=',$_SESSION['jeton'],'\');"><img src="images/b_drop.png" alt="Supprimer" title="Supprimer le fichier" /></a></td>',
      '<td>',$files['fichier_id'],"</td>\n",
      '<td>',$files['fichier_nom'],"</td>\n",
      '<td>',Fichier::octetSize( $files['poids'] ),"</td>\n",
      '<td>',$files['fichier_description'],"</td>\n",
      '<td>',$files['extension'],"</td>\n",
      '<td>',$files['telecharger'],"</td>\n",
    "</tr>\n";
  }
}
  
?>
</table>
<div style="margin-top: 20px;">
  <form method="post" action="fichier.php?jeton=<?php echo $_SESSION['jeton'] ?>" enctype="multipart/form-data">
    <fieldset style="float: left; width: 47%; height: 100px;">
      <legend>Le fichier</legend>
      <label for="titre">Titre du fichier :</label><br />
      <input type="text" style="width: 50%;" name="titre" value="Titre du fichier" id="titre" /><br />
      <label for="fichier">Fichier :</label><br />
      <input type="file" style="width: 50%;" name="fichier" id="fichier" />
    </fieldset>
    <fieldset style="width: 47%; height: 100px;">
      <legend>Description du fichier</legend>
      <label for="description">Description de votre fichier (max 255 caractères):</label><br />
      <textarea name="description" style="width:100%;" id="description"></textarea>
    </fieldset>
    <p style="margin-left: 30px; clean: both;"><input type="submit" name="submit" value="Enregistrer &amp; Téléverser le nouveau fichier" /></p>
  </form>
</div>
