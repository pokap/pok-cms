<?php

// est-que ça serai pas plus intéressant de partir sur un architecture sur serveur pour l'arborescence ?
// et pour trier les données de chaque page

include( __DIR__ . '/membre.php' );

?>
<h2>Newsletter de <?php echo $membre ?></h2>
<fieldset>
  <legend>Options</legend>
  <a href="membre.php?modif&m=<?php echo $membre['membre_id'] ?>"><img src="images/back.png" alt="[M]" /> Gestion du membre</a>
  <br /><a href="page.php?page=<?php echo $membre['membre_arborescence'] ?>"><img src="images/repertoire.jpg" alt="page :" /> Voir la page du membre</a>
</fieldset>
<fieldset>
  <legend>Liste des newsletters abonner</legend>
  <form method="post" action="utils/add_membre_newsletter.php?membre=<?php echo $membre['membre_id'] ?>&amp;jeton=<?php echo $_SESSION['jeton'] ?>">
    <label for="newsletter">Newsletter :</label>
    <select id="newsletter" name="newsletter">
<?php

    foreach( $newsletter AS $nl )
    {
    	echo '<option value="',$nl['newsletter_id'],'">',$nl,'</option>';
    }

?>
    </select>
    <input type="submit" value="Ajouter la newsletter"/>
  </form>
  <table class="dossier">
    <tr class="partie">
      <th class="option"><-></th>
      <th>Newsletter</th>
      <th>Page ID#</th>
      <th>Page</th>
      <th>Page arborescence</th>
    </tr>
<?php

    $i = 0;
    
    foreach( $membre_newsletters AS $mnl )
    {
      $color = ( $i % 2 != 0 )? ' class="pair"' : '';
      
      echo '<tr',$color,">\n",
        '<td align="center"><a href="#" onClick="verif(\'utils/delete_membre_newsletter.php?m=',$mnl['membre_id'],'&amp;n=',$mnl['newsletter_id'],'&amp;jeton=',$_SESSION['jeton'],'\');"><img src="images/b_drop.png" alt="Supprimer" title="Supprimer le membre" /></a></td>',
        '<td>',$mnl['newsletter_titre'],"</td>\n",
        '<td>',$mnl['page_id'],"</td>\n",
        '<td>',$mnl['page_nom'],"</td>\n",
        '<td>/',$mnl['arborescence'],"</td>\n",
      "</tr>\n";
      $i++;
    }

?>
  </table>
</fieldset>
