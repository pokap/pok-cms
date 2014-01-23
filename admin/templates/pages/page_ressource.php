<?php

include( __DIR__ . '/page.php' );

?>
<h3>Liste des ressources disponibles pour cette page :</h3>
<blockquote>
  <div class="avertissement">Attention, cela va cr&eacute;er des pages et des articles automatiquements, prenez soins de bien conna&icirc;tre votre syst&egrave;me avant d'effectuer l'op&eacute;ration.</div>
  <p>S&eacute;lectionnez les ressouces que vous voulez utilisez de chaque template.</p>
  <form method="post" action="./utils/create_page.php?ressources=<?php echo $_GET['page'];?>&amp;jeton=<?php echo $_SESSION['jeton'];?>">
  <hr />
<?php
  
  $name = str_replace( '/', '.', pok\Apps\Page::strSupDernierePage($myRepertoire['arborescence']) );
  // cherche s'il existe une ressource spécifique au dossier où est instancier le template
  if( file_exists( ADRESSE_TEMPLATES . '/' . $_GET['tpl'] . '/ressources/.global.xml' ) ) {
    echo '<input type="checkbox" name="sources[n]" value="',$_GET['tpl'],'"> ',$_GET['tpl'];
  }
  // liste des ressources qui existe
  $all_file = glob( ADRESSE_TEMPLATES . '/*/ressources/' . $name . '.xml' );
  foreach( $all_file AS $file )
  {
    $plo = basename(dirname(dirname($file)));
    
    echo '<input type="checkbox" id="ressources_',$myRepertoire['page_id'],'" name="sources[',$myRepertoire['page_id'],']" value="',$plo,'"> <label for="ressources_',$myRepertoire['page_id'],'">',$plo,'</label><br />';
  }
  
?>
  <hr />
  <p align="center"><input type="submit" value="Utilisez les ressources s&eacute;lectionn&eacute;es" /></p>
  </form>
</blockquote>
