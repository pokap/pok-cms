<?php

use systems\cfg\general;
use systems\cfg\newsletter;

?>
  <div class="select">
    <p><a class="bouton" href="./article.php">&laquo; Revenir aux articles</a></p>
  </div>
<?php

  // les messages d'erreur :
  if( isset( $_GET['mailok'] ) )
    echo '<div class="valide">Le mail &agrave; bien &eacute;t&eacute; envoyez.</div>';
  elseif( isset( $_GET['mailerreur'] ) )
    echo '<div class="erreur">Le mail n\'a pas pu &ecirc;tre envoyez !</div>';

  if( !general\NEWSLETTER )
    echo '<div class="avertissement">La newsletter n\'est pas activ&eacute;, si vous d&eacute;sirez l\'activez, allez dans la configuration g&eacute;n&eacute;ral.</div>';

?>
  <div style="margin-top: 10px;">
    <fieldset>
      <legend>Options</legend>
      <form method="post" action="./utils/newsletter_cfg.php?jeton=<?php echo $_SESSION['jeton'];?>">
        <label for="objetmail">Sujet du mail :</label>
        <input type="text" id="objetmail" name="objetmail" value="<?php echo newsletter\OBJET_MAIL;?>" />
        <input type="submit" value="Enregistrer la configuration !" />
      </form>
    </fieldset>
    <fieldset>
      <legend>Ajouter</legend>
      <form method="post" action="./utils/newsletter.php?jeton=<?php echo $_SESSION['jeton'];?>">
        <label for="page">Page de r&eacute;f&eacute;rence :</label>
        <select name="page" id="page" tabindex="1">
<?php

  foreach( $select_arbo AS $id => $arbo )
  {
    echo '<option value="',$id,'">',$arbo,'</option>';
  }
?>
        </select><br />
        <label for="autopost">Envoyez une mail &agrave; chaque nouveau premier article ?</label>
        <input type="checkbox" id="autopost" name="autopost" /><br />
        <label for="titre">Titre :</label>
        <input type="text" id="titre" name="titre" />
        <p style="margin-left: 20px;"><input type="submit" value="Ajouter" /></p>
      </form>
    </fieldset>
    <table class="dossier">
      <tr class="partie">
        <th colspan="3" class="option"><-></th>
        <th>Arborescence</th>
        <th>Page</th>
        <th>Newsletter titre</th>
        <th>Automatique</th>
      </tr>
<?php

    $auto = array('manuel','automatique');
    $i = 0;
    
    foreach( $newsletters AS $mnl )
    {
      $color = ( $i % 2 != 0 )? ' class="pair"' : '';
			$page = pok\Apps\Page::getByPageId($mnl['page_id']);
      
      echo '<tr',$color,">\n",
        '<td align="center"><a href="newsletter.php?n=',$mnl['newsletter_id'],'"><img src="images/b_edit.png" alt="Supprimer" title="Modifier la newsletter" /></a></td>',
        '<td align="center"><a href="#" onClick="verif(\'utils/delete_newsletter.php?n=',$mnl['newsletter_id'],'&amp;jeton=',$_SESSION['jeton'],'\');"><img src="images/b_drop.png" alt="Supprimer" title="Supprimer la newsletter" /></a></td>',
        '<td align="center"><a href="#" onClick="verif(\'utils/newsletter_envoyer.php?n=',$mnl['newsletter_id'],'&amp;jeton=',$_SESSION['jeton'],'\');"><img src="images/newsletter.png" alt="[>]" title="Envoyez la newsletter" /></a></td>',
        '<td>/',$page['arborescence'],"</td>\n",
        '<td>',$page['page_nom'],"</td>\n",
        '<td>',$mnl['newsletter_titre'],"</td>\n",
        '<td align="center">',$auto[$mnl['newsletter_auto']],'</td>',
      "</tr>\n";
      $i++;
    }

?>
    </table>
  </div>
