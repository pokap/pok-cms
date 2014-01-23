<?php

use pok\Apps\Page;
use pok\Apps\Outils\Base\Fichier;

include( __DIR__ . '/page.php' );

?>
<script type="text/javascript">
  $(document).ready( function () {
    $(".voir").click( function () {
      $(this).parent().parent().parent().next(".cache").slideToggle("slow");
    });
    $(".checkbox_id").click( function () {
      if( $(this).attr('checked') )
        $(this).parent().parent().addClass('checked');
      else
        $(this).parent().parent().removeClass('checked');
    });
  });
</script>
<?php

		if( !empty($rep_arbo_absolu) )
    {
      // initalise
      $dossier_tab = array();
      foreach( $rep_arbo_absolu AS $dossier_donnees )
      {
        $dossier_tab[$dossier_donnees['cat_nom']][] = $dossier_donnees;
      }
      
			foreach( $dossier_tab AS $categorie_name => $categorie ) 
			{
				if( !empty( $categorie_name ) ) {
					$th_titre = '<th colspan="11"><button class="voir">Voir/Cacher</button>Categorie : '.$categorie_name.'</th>';
          $class_plus = '';
        }
				else {
					$th_titre = '<th colspan="11">Aucune Catégorie</th>';
          $class_plus = ' error';
        }
        
?>
<table class="dossier<?php echo $class_plus;?>">
	<tr class="titre">
    <?php echo $th_titre;?>
	</tr>
  <tbody class="cache">
	<tr class="partie">
		<th colspan="5" class="option"><-></th>
		<th style="width:10%;">#ID</th>
		<th style="width:10%;">Ordre</th>
		<th style="width:44%;">Nom</th>
		<th style="width:20%;">Template</th>
		<th style="width:5%;">Lu/non-lu</th>
	</tr>

<?php
				$comm_nb = 0;
				foreach( $categorie AS $dossiers ) 
				{
					$color = ( $comm_nb % 2 != 0 ) ? ' class="pair"': NULL;
?>
					<tr <?php echo $color;?>>
						<td><a href="page.php?page=<?php echo $dossiers['arborescence'];?>"><img src="images/b_browse.png" alt="Voir" title="Entrez dans le dossier" /></a></td>
						<td><a href="page.php?modifier&amp;page=<?php echo $dossiers['arborescence'];?>&amp;return=<?php echo $dossier_arbo;?>"><img src="images/b_edit.png" alt="Modifier" title="Modifier le dossier" /></a></td>
						<td><a href="#" onClick="verif('utils/supprimer_page.php?courant=<?php echo $dossiers['page_id'];?>&amp;jeton=<?php echo $_SESSION['jeton'];?>');"><img src="images/b_drop.png" alt="Supprimer" title="Supprimer le dossier" /></a></td>
						<td><a href="./page.php?page=<?php echo $dossiers['arborescence'];?>&amp;ressources&amp;tpl=<?php echo $dossiers['template'];?>"><img src="images/b_cfg.png" alt="Cfg" title="Met à jour avec les ressources" /></a></td>
						<td><a href="../index.php?page=<?php echo $dossiers['arborescence'];?>"><img src="images/b_select.png" alt="Voir" title="Voir le dossier" /></a></td>
						<td><?php echo $dossiers['page_id'];?></td>
						<td><?php echo $dossiers['page_ordre'];?></td>
						<td>
              <img src="images/repertoire.jpg" alt="page :" /> 
<?php
            // s'il n'y a pas de description, on l'indique !
            if( empty($dossiers['description']) )
              $dossiers['description'] = 'Aucune description.';
?>
              <strong class="info"><a href="page.php?page=<?php echo $dossiers['arborescence'];?>"><?php echo $dossiers['page_nom'];?></a><span><strong>Description :</strong><br /><?php echo $dossiers['page_description'];?></span></strong>
            </td>
						<td>
<?php
            // Affiche l'info bulle pour indiquer si le template existe
            if( file_exists( ADRESSE_TEMPLATES . '/' . $dossiers['template'] ) )
              echo '<a href="template.php?view=',$dossiers['template'],'">',$dossiers['template'],'</a>';
            else
              echo '<strong class="infoerreur">',$dossiers['template'],'<span class="all"><span class="text">Ce template n\'existe pas.</span><span class="fleche">&nbsp;</span></span></strong>';
?>
            </td>
            <td align="center"><a title="Système lu/non-lu automatique" href="utils/create_page.php?page=<?php echo $dossier_arbo;?>&amp;courant=<?php echo $dossiers['page_id'];?>&amp;nonlu=<?php echo $dossiers['nonlu'];?>&amp;jeton=<?php echo $_SESSION['jeton'];?>"><img src="images/<?php
            if( $dossiers['nonlu'] )
              echo 'head_on.png" alt="désactiver le système lu/non-lu automatique" />';
            else
              echo 'head_off.png" alt="activer le système lu/non-lu automatique" />';
?></a>
              </td>
            </tr>
<?php
					$comm_nb++;
				}
				
				echo '</tbody></table>';
			}
		}
		else {
			echo '<p>Aucune page.</p>';
		}

?>
<script type="text/javascript">
$(document).ready(function(){
  $("#toggle_deplacement").click(function(){
    $(".deplacement_switch").toggle();
    $(".deplacement_icone").toggle();
    $("#deplacement_id").val("");
  });
});
</script>
<div style="margin-top: 10px;">
<?php

    if( $myRepertoire['page_id'] > 0 )
    {

?>
<form method="post" action="utils/deplacer_page.php?page=<?php echo $dossier_arbo;?>&amp;courant=<?php echo $myRepertoire['page_id'];?>&amp;jeton=<?php echo $_SESSION['jeton'];?>">
<fieldset>
  <legend>Options</legend>
  <img src="images/articles.png" alt="Article" title="Voir les articles" /> <a href="article.php?page=<?php echo $myRepertoire['page_id'];?>&amp;cat=">Voir les articles de la page courant.</a><br />
  <img src="images/b_edit.png" alt="Modifier" title="Modifier le page" /> <a href="page.php?modifier&amp;page=<?php echo $dossier_arbo;?>&amp;return=<?php echo $dossier_arbo;?>">Modifier la page courante.</a><br />
<?php

      if( $myRepertoire['page_id'] > 3 )
      {
        $select_arbo = Page::publierListeFilter($dossier_arbo);

?>
	<img src="images/deplace.png" alt="Déplace" title="Déplace le dossier" /> Déplacer la page courant vers
	<span class="deplacement_switch">: <select name="deplacement" id="deplacement" tabindex="1">
<?php

        foreach( $select_arbo AS $id => $nom ) {
          echo '<option value="',$id,'">',$nom,'</option>';
        }

?>
	</select>
  </span>
  <span class="deplacement_switch" style="display: none;">
    l'ID : <input name="deplacement_id" id="deplacement_id" tabindex="2" />
  </span>
  <a href="#" class="toggle" id="toggle_deplacement"><span title="Manuel" class="deplacement_icone">M</span><span title="Assisté" class="deplacement_icone" style="display: none;">A</span></a>
	<input type="submit" value="Déplacer" />
<?php

      }

?>
</fieldset>
</form>
<?php

    }

?>
<fieldset>
  <legend>Nouvelle Page</legend>
  <form class="form_admin_modif_create" method="post" action="utils/create_page.php?page=<?php echo $dossier_arbo;?>&amp;courant=<?php echo $myRepertoire['page_id'];?>&amp;jeton=<?php echo $_SESSION['jeton'];?>">
    <label for="cat_id">Catégorie * :</label>
    <select name="cat_id" id="cat_id" tabindex="1">
<?php

		foreach( $cat_list AS $cat ) {
			if( $cat['cat_id'] == $info_dossier['cat_id'] )
				echo '<option value="',$cat['cat_id'],'" selected="selected">',$cat['cat_nom'],'</option>';
			else
				echo '<option value="',$cat['cat_id'],'">',$cat['cat_nom'],'</option>';
		}

?>
    </select><br />
    <label for="page_nom">Page * :</label>
    <input type="text" name="page_nom" id="page_nom" tabindex="2" value="<?php echo $info_dossier['page_nom'];?>" /><br />
    <label for="page_description">Description * :</label>
    <input type="text" name="page_description" id="page_description" tabindex="4" value="<?php echo $info_dossier['page_description'];?>" /><br />
    <label for="page_ordre">Ordre * :</label>
    <input type="text" name="page_ordre" id="page_ordre" value="<?php echo $info_dossier['page_ordre'];?>" tabindex="5" /><br />
    <label for="template">Template :</label>
    <select name="template" id="template" tabindex="6">
<?php
  
  // on charge la liste des templates
  $liste_templates = Fichier::lister( ADRESSE_TEMPLATES, Fichier::DIR, array('.svn') );
  foreach( $liste_templates AS $template )
  {
    if( $template == $info_dossier['template'] || $template == $myRepertoire['template'] )
      echo '<option value="' , $template , '" selected="selected">' , $template , '</option>';
    elseif( $template[0] != '_' )
      echo '<option value="' , $template , '">' , $template , '</option>';
  }
  
?>
    </select><br />
    <label for="cache">Système Lu/Non-lu :</label>
    <input type="radio" name="nonlu" id="lu" value="1" tabindex="9" <?php echo $info_dossier['nonlu'] == 1 ? 'checked="checked"' :'';?> /> Oui
    <input type="radio" name="nonlu" id="nonlu" value="0" tabindex="10" <?php echo $info_dossier['nonlu'] == 0 ? 'checked="checked"' :'';?> /> Non
	<p style="margin-left: 30px;"><input type="submit" value="Créer la page ici" /></p>
</form>
</fieldset>
</div>
