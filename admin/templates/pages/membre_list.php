<?php

use admin\templates\Pages;
use pok\Apps\Membre;
use pok\Apps\Outils\Base\Fichier;

include( __DIR__ . '/membre.php' );

?>
<div class="select"><div class="select_page">Page : <?php Pages::get_list_page( $nb_membres, 'page', 'membre' );?></div>&nbsp;</div>
<table class="dossier">
  <tr class="partie">
    <th colspan="3" class="option"><-></th>
    <th style="width:9%;"><a href="membre.php?trie=<?php echo $trie[$_GET['trie']];?>&amp;champ=id&amp;page=<?php echo $page;?>">#ID <?php echo $img_trie['id'];?></a></th>
    <th style="width:20%;"><a href="membre.php?trie=<?php echo $trie[$_GET['trie']];?>&amp;champ=pseudo&amp;page=<?php echo $page;?>">Pseudo <?php echo $img_trie['pseudo'];?></a></th>
    <th style="width:30%;"><a href="membre.php?trie=<?php echo $trie[$_GET['trie']];?>&amp;champ=email&amp;page=<?php echo $page;?>">E-mail <?php echo $img_trie['email'];?></a></th>
    <th style="width:20%;"><a href="membre.php?trie=<?php echo $trie[$_GET['trie']];?>&amp;champ=inscrit&amp;page=<?php echo $page;?>">Inscrit le : <?php echo $img_trie['inscrit'];?></a></th>
    <th style="width:10%;"><a href="membre.php?trie=<?php echo $trie[$_GET['trie']];?>&amp;champ=statut&amp;page=<?php echo $page;?>">Statut <?php echo $img_trie['statut'];?></a></th>
    <th style="width:10%;"><a href="membre.php?trie=<?php echo $trie[$_GET['trie']];?>&amp;champ=valide&amp;page=<?php echo $page;?>">Valide <?php echo $img_trie['valide'];?></a></th>
  </tr>
<?php

    $i = 0;
    $valide = array('non','oui');
    
    foreach( $list_membres AS $membre )
    {
      if( $membre['statut'] == Membre::BANNIE || !$membre['valide'] )
        $color = ' style="background: #333; color: #BBB;"';
      elseif( $i % 2 != 0 )
        $color = ' class="pair"';
      else
        $color = NULL;
      
      echo '<tr',$color,">\n",
        '<td><a href="membre.php?modif&amp;m=',$membre['membre_id'],'"><img src="images/b_edit.png" alt="Modifier" title="Modifier le membre" /></a></td>',
        '<td><a href="membre.php?newsletter&amp;m=',$membre['membre_id'],'"><img src="images/newsletter.png" alt="[N]" title="Gestion newsletter" /></a></td>',
        '<td><a href="#" onClick="verif(\'utils/supprimer_membre.php?m=',$membre['membre_id'],'&amp;jeton=',$_SESSION['jeton'],'\');"><img src="images/b_drop.png" alt="Supprimer" title="Supprimer le membre" /></a></td>',
        '<td>',$membre['membre_id'],"</td>\n",
        '<td>',$membre['membre_pseudo'],"</td>\n",
        '<td>',$membre['membre_email'],"</td>\n",
        '<td>',$membre['membre_inscrit'],"</td>\n",
        '<td>',$tab_mode[$membre['statut']],"</td>\n",
        '<td align="center">',$valide[$membre['valide']],"</td>\n",
      "</tr>\n";
      $i++;
    }

?>
</table>
<div style="margin-top: 10px;">
<fieldset>
  <legend>Nouveau membre</legend>
  <form class="form_admin_modif_create" method="post" action="utils/create_membre.php?jeton=<?php echo $_SESSION['jeton'];?>">
<fieldset>
  <legend>Données membre</legend>
    <label for="pseudo">Pseudo * :</label>
    <input type="text" name="pseudo" id="pseudo" value="<?php echo $info_membre['pseudo'];?>" tabindex="1" /><br />
    <label for="mdp">Mot de passe * :</label>
    <input type="text" name="mdp" id="mdp" value="<?php echo $info_membre['mdp'];?>" tabindex="2" /><br />
    <label for="email">Email * :</label>
    <input type="text" name="email" id="email" value="<?php echo $info_membre['email'];?>" tabindex="3" /><br />
    <label for="statut">Statut * :</label>
    <select name="statut" id="statut" tabindex="4">
<?php

    foreach( $tab_mode AS $enum => $statut ) {
      if( $enum == Membre::MEMBRE )
        echo '<option value="',$enum,'" selected="selected">',$statut,'</option>';
      else
        echo '<option value="',$enum,'">',$statut,'</option>';
    }

?>
    </select><br />
  </fieldset>
  <fieldset>
  <legend>Données Dossier du membre</legend>
    <label for="cat_id">Catégorie * :</label>
    <select name="cat_id" id="cat_id" tabindex="1">
<?php

    foreach( $cat_list AS $cat ) {
      if( $cat['cat_id'] == $info_membre['cat_id'] )
        echo '<option value="' , $cat['cat_id'] , '" selected="selected">' , $cat['cat_nom'] , '</option>';
      else
        echo '<option value="' , $cat['cat_id'] , '">' , $cat['cat_nom'] , '</option>';
    }
 
?>
    </select><br />
    <label for="ordre">Ordre * :</label>
    <input type="text" name="ordre" id="ordre" value="<?php echo $info_membre['ordre'];?>" tabindex="3" /><br />
    <label for="template">Template :</label>
    <select name="template" id="template" tabindex="4">
<?php

  // on charge la liste des templates
  $liste_templates = Fichier::lister( ADRESSE_TEMPLATES, Fichier::DIR, array('.svn') );
  foreach( $liste_templates AS $template )
  {
    if( $template == $info_membre['template'] )
      echo '<option value="' , $template , '" selected="selected">' , $template , '</option>';
    elseif( $template[0] != '_' )
      echo '<option value="' , $template , '">' , $template , '</option>';
  }

?>
  </select>
  </fieldset>
  <p style="margin-left: 30px;"><input type="submit" value="Créer le nouveau membre" /></p>
  </form>
</fieldset>
</div>
