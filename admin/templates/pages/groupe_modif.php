<?php

use admin\templates\Pages;
use pok\Apps\Membre;

?>
<script type="text/javascript">
// Lorsque la totalité de la page est chargée
$(document).ready(function() {
  $.ajax({ // Requete ajax
    type: "POST", // envoie en POST
    url: "_autocompletion_groupe.php", // url cible du script PHP
    async: true, // mode asynchrone
    data: "", // données envoyées
    
    success: function(xml){ // Lorsque le PHP à renvoyé une réponse
      var elementsArray = new Array();

      $(xml).find('element').each(function(){ // pour chaque "element"
        elementsArray.push($(this).text()); // ajout dans le tableau
      });

      $("#membre_search").autocomplete(elementsArray); // activation de l'autocompletion
    }
  });
});
</script>

<h2>Groupe "<?php echo $groupe[0]['page_nom'];?>"</h2>

<div class="select"><div class="select_page">Page : <?php Pages::get_list_page( $nb_membre, 'page', 'groupe', 'modif&amp;g='.$_GET['g'].'&amp;' );?></div>&nbsp;</div>

<fieldset>
  <legend>Membres du Groupe</legend>
  <form method="post" action="utils/add_membre_groupe.php?g=<?php echo $_GET['g'];?>&amp;jeton=<?php echo $_SESSION['jeton'];?>">
    <label for="membre_search">Pseudo du membre :</label>
    <input type="text" name="membre" id="membre_search" tabindex="5"/>
    <input type="submit" value="Ajouter un membre"/>
  </form>
  <table class="dossier">
  <tr class="partie">
    <th colspan="2" class="option"><-></th>
    <th>#ID</th>
    <th>Pseudo</th>
    <th>Statut</th>
    <th style="width:1%;">Principal</th>
  </tr>
 
<?php
    // initialise, permet de changer de couleur entre chaque ligne
    $i = 0;
    foreach( $list_membres AS $membre ) 
    {
      if( $membre['statut'] == Membre::BANNIE )
        $color = ' style="background:#000;color:#fff;"';
      elseif( $i % 2 != 0 )
        $color = ' class="pair"';
      else
        $color = NULL;
      
      echo '<tr',$color,">\n",
        '<td><a href="membre.php?modif&amp;m=',$membre['membre_id'],'&amp;d=',$membre['page_id'],'"><img src="images/b_edit.png" alt="Modifier" title="Modifier le Membre"/></a></td>',
        '<td><a href="#" onClick="verif(\'utils/supprimer_membre_groupe.php?g=',$_GET['g'],'&amp;m=',$membre['membre_id'],'&amp;jeton=',$_SESSION['jeton'],'\');"><img src="images/b_drop.png" alt="Kick" title="Enlever le membre du groupe"/></a></td>',
        '<td>',$membre['membre_id'],"</td>\n",
        '<td><a href="membre.php?modif&m=',$membre['membre_id'],'">',$membre['membre_pseudo'],"</a></td>\n",
        '<td>',$tab_mode[$membre['statut']],'</td>'."\n".'<td align="center"><a href="utils/create_groupe.php?groupe=',$_GET['g'],'&amp;head=',$membre['membre_id'],'&amp;jeton=',$_SESSION['jeton'],'"><img src="images/';
      // si le tableau 'groupe' du membre est vide, cela veux dire que c'est son groupe principal
      if( $membre['principal'] )
        echo 'head_on';
      else
        echo 'head_off';
      
      echo '.png" alt="modfier le head" /></a>'."</td>\n</tr>\n";
      $i++;
    }
    echo '</table>';

?>
</fieldset>
<fieldset>
  <legend>Options</legend>
  <a href="page.php?page=<?php echo $groupe[0]['arborescence'] ?>"><img src="images/repertoire.jpg" alt="page :" /> Voir la page du groupe</a>
</fieldset>
<fieldset>
  <legend>Modifier Groupe</legend>
  <form class="form_admin_modif_create" method="post" action="utils/create_groupe.php?modif&amp;groupe=<?php echo $_GET['g'];?>&amp;page=<?php echo $groupe[0]['page_id'];?>&amp;jeton=<?php echo $_SESSION['jeton'];?>">
  <fieldset>
  <legend>Donnée Groupe</legend>
    <label for="nom">Nom * :</label>
    <input type="text" name="nom" id="nom" value="<?php echo $groupe[0]['page_nom'];?>" tabindex="1"/><br/>
    <label for="couleur">Couleur * : #</label>
    <input type="text" name="couleur" id="couleur" value="<?php echo $groupe[0]['couleur'];?>" tabindex="2"/><br/>
  </fieldset>
  <fieldset>
  <legend>Donnée Page du groupe</legend>
    <label for="cat_id">Catégorie * :</label>
    <select name="cat_id" id="cat_id" tabindex="3">
<?php

  foreach( $cat_list AS $cat ) {
    if( $groupe[0]['cat_id'] == $cat['cat_id'] )
      echo '<option value="',$cat['cat_id'],'" selected="selected">',$cat['cat_nom'],'</option>';
    else
      echo '<option value="',$cat['cat_id'],'">',$cat['cat_nom'],'</option>';
  }

?>
    </select><br/>
    <label for="ordre">Ordre * :</label>
    <input type="text" name="ordre" id="ordre" value="<?php echo $groupe[0]['page_ordre'];?>" tabindex="4"/><br/>
    <label for="template">Template :</label>
    <input type="text" name="template" id="template" value="<?php echo $groupe[0]['template'];?>" tabindex="5"/>
  </fieldset>
  <p style="margin-left: 30px;"><input type="submit" value="Modifier le groupe"/></p>
  </form>
</fieldset>