<?php

// est-que ça serai pas plus intéressant de partir sur un architecture sur serveur pour l'arborescence ?
// et pour trier les données de chaque page

include( __DIR__ . '/membre.php' );

use pok\Apps\Outils\Base\Fichier;

?>
<div class="select">
  <div id="Nav">Groupe(s) de l'utilisateur :
  <div>
    <span class="head"><?php echo $membre[0]['groupe_nom'];?></span>
    <ul class="Menu">
      <li><a href="groupe.php?modif&amp;g=<?php echo $membre[0]['groupe_id'];?>">Modifier le groupe</a></li>
      <li><a href="groupe.php?droit&amp;g=<?php echo $membre[0]['groupe_id'];?>&amp;name=<?php echo $membre[0]['groupe_nom'];?>">Modifier les droits</a></li>
      <li><a href="dossier.php?d=<?php echo $membre[0]['groupe_page_id'];?>">Voir le dossier</a></li>
    </ul>
  </div>
<?php

foreach( $groupes AS $groupe )
{
  echo '<div>, ' , $groupe['page_nom'] , '<ul class="Menu">
    <li><a href="groupe.php?modif&amp;g=',$groupe['groupe_id'],'">Modifier le groupe</a></li>
    <li><a href="groupe.php?droit&amp;g=',$groupe['groupe_id'],'&amp;name=',$groupe['page_nom'],'">Modifier les droits</a></li>
    <li><a href="dossier.php?d=',$groupe['page_id'],'">Voir le dossier</a></li>
  </ul></div>';
}

?>
  </div>
</div>

<fieldset>
  <legend>Modification du membre</legend>
  <form class="form_admin_modif_create" method="post" action="utils/create_membre.php?m=<?php echo $_GET['m'];?>&amp;jeton=<?php echo $_SESSION['jeton'];?>">
<fieldset>
  <legend>Données membre</legend>
    <label for="pseudo">Pseudo * :</label>
    <input type="text" name="pseudo" id="pseudo" value="<?php echo $membre[0]['membre_pseudo'];?>" tabindex="1" /><br />
    <label for="mdp">Mot de passe :</label>
    <input type="text" name="mdp" id="mdp" tabindex="2" /><br />
    <label for="email">Email * :</label>
    <input type="text" name="email" id="email" value="<?php echo $membre[0]['membre_email'];?>" tabindex="3" /><br />
    <label for="inscrit">Inscrit * :</label>
    <input type="text" name="inscrit" id="inscrit" value="<?php echo $membre[0]['membre_inscrit'];?>" tabindex="4" /><br />
    <label for="statut">Statut * :</label>
    <select name="statut" id="statut" tabindex="4">
<?php

    foreach( $tab_mode AS $indic => $mode ) {
      if( $indic == $membre[0]['statut'] )
        echo '<option value="',$indic,'" selected="selected">',$mode,'</option>';
      else
        echo '<option value="',$indic,'">',$mode,'</option>';
    }

?>
    </select><br /><label> Valide :</label>
    <?php if( $membre[0]['valide'] ) { ?>
      <input type="radio" name="valide" id="valide" value="1" tabindex="5" checked="checked" /> Oui
      <input type="radio" name="valide" id="nonvalide" value="0" tabindex="5" /> Non
    <?php } else { ?>
      <input type="radio" name="valide" id="valide" value="1" tabindex="5" /> Oui
      <input type="radio" name="valide" id="nonvalide" value="0" tabindex="5" checked="checked" /> Non
    <?php } ?>
  </fieldset>
  <fieldset>
  <legend>Données de la page du membre</legend>
    <label for="cat_id">Catégorie * :</label>
    <select name="cat_id" id="cat_id" tabindex="1">
      <option value="">--</option>
<?php

    foreach( $cat_list AS $cat ) {
      echo '<option value="',$cat['cat_id'],'">',$cat['cat_nom'],'</option>';
    }

?>
    </select><br />
    <label for="ordre">Ordre * :</label>
    <input type="text" name="ordre" id="ordre" value="<?php echo $membre[0]['membre_page_ordre'];?>" tabindex="3" /><br />
    <label for="template">Template :</label>
    <select name="template" id="template" tabindex="4">
<?php
  
  // on charge la liste des templates
  $liste_templates = Fichier::lister( ADRESSE_TEMPLATES, Fichier::DIR, array('.svn') );
  foreach( $liste_templates AS $template )
  {
    if( $template == $membre[0]['membre_page_template'] )
      echo '<option value="' , $template , '" selected="selected">' , $template , '</option>';
    elseif( $template[0] != '_' )
      echo '<option value="' , $template , '">' , $template , '</option>';
  }
  
?>
    </select><br />
  </fieldset>
  <p style="margin-left: 30px;"><input type="submit" value="Mettre à jour le membre" /></p>
  </form>
</fieldset>
<fieldset>
  <legend>Options</legend>
  <a href="page.php?page=<?php echo $membre[0]['membre_arborescence'] ?>"><img src="images/repertoire.jpg" alt="page :" /> Voir la page du membre</a>
</fieldset>
<fieldset>
  <legend>Prendre l'identité</legend>
  <p>Vous pouvez prendre l'identité du membre par exemple pour vérifier ses droits.<br />Cette manipulation modifie simplement votre ID sur cette connexion, elle ne prend pas le pseudo, le mode, ...<br />Pour reprendre votre Identité, revenez sur votre fiche pour effectuer la même manipulation, ou bien faite une Déconnexion/Reconnexion.</p>
  <p style="margin-left"><b><a href="membre.php?modif&amp;m=<?php echo $membre[0]['membre_id'];?>&amp;prisedidentite"><img src="images/oeil.png" alt="<" /> Prendre l'identité du membre</a></b></p>
  <p>Faite attention à vos manipulation une fois la prise d'identité.</p>
</fieldset>