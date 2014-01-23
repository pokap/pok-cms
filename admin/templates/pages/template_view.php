<?php

use pok\Texte;
use pok\Apps\Page;
use pok\Apps\Outils\Base\Fichier;

?>
<h2>Liste des templates</h2>
  <div class="select">
    <p>
      <b><a href="./general.php" class="bouton">&laquo; Revenir à la configuration</a></b> | <a href="./template.php#<?php echo $_GET['view'];?>" class="bouton">&laquo; Liste des templates</a></p>
  </div>
  <div style="float:right;margin:10px;padding:10px;background:#CCC;">
  <script type="text/javascript">
  $(document).ready(function(){
    $("#toggle_deplacement").click(function(){
      $(".deplacement_switch").toggle();
      $(".deplacement_icone").toggle();
      $("#deplacement_id").val("");
    });
  });
  </script>
  <form method="get" action="./templatetest.php">
    <input type="hidden" name="view" value="<?php echo $_GET['view'];?>" />
    <span class="deplacement_switch">
      <select name="deplacement" id="deplacement" tabindex="1">
<?php

    foreach( Page::publierListeFilter('') AS $id => $nom ) {
      echo '<option value="',$id,'">',$nom,'</option>';
    }
    
?>
      </select>
    </span>
    <span class="deplacement_switch" style="display: none;">
      l'ID : <input name="deplacement_id" id="deplacement_id" tabindex="2" />
    </span>
    <a href="#" class="toggle" id="toggle_deplacement"><span title="Manuel" class="deplacement_icone">M</span><span title="Assisté" class="deplacement_icone" style="display: none;">A</span></a>
    <button>Tester le template sur le dossier</button>
  </form>
  </div>
  <h3><?php echo $_GET['view'];?></h3>
<?php

  if( file_exists( ADRESSE_TEMPLATES . '/' . $_GET['view'] . '/infos.ini' ) )
  {
    $infos = parse_ini_file( ADRESSE_TEMPLATES . '/' . $_GET['view'] . '/infos.ini' );

?>
  <h4>Informations</h4>
  <p>
    <blockquote>
      <b>Nom :</b> <?php echo $infos['nom'];?><br />
      <b>Tags :</b> <?php echo $infos['tags'];?><br />
      <b>Version :</b> <?php echo $infos['version'];?><br />
      <b>Date :</b> <?php echo $infos['date'];?><br />
      <b>Description :</b> <?php echo $infos['description'];?>
    </blockquote>
  </p>
  <p>
    <blockquote>
      <b>Auteur :</b> <?php echo $infos['auteur'];?><br />
      <b>E-Mail :</b> <?php echo $infos['email'];?><br />
      <b>Url :</b> <a href="<?php echo $infos['URL'];?>" target="_blank"><?php echo $infos['URL'];?></a>
    </blockquote>
  </p>
<?php

  }

?>
  <h4>Lisez-moi</h4>
  <p><?php echo file_exists( ADRESSE_TEMPLATES . '/' . $_GET['view'] . '/Information/lisez-moi.txt' ) ? file_get_contents( ADRESSE_TEMPLATES . '/' . $_GET['view'] . '/Information/lisez-moi.txt' ) : 'Aucun fichier "lisez-moi".';?></p>
  <h4>Liste des fichiers</h4>
  <ul>
<?php

    // osef du svn
    $liste_fichiers = Fichier::lister( ADRESSE_TEMPLATES . '/' . $_GET['view'] . '/', Fichier::BOTH, array('.svn') );
    sort($liste_fichiers);
    foreach( $liste_fichiers AS $file ) {
      echo '<li>' , $file , '</li>';
    }

?>
  </ul>