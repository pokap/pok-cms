<?php

use pok\Texte;
use pok\Apps\Outils\Base\Fichier;

?>
<h2>Liste des templates</h2>
  <div class="select">
    <p>
      <b><a href="./general.php" class="bouton">&laquo; Revenir à la configuration</a></b>
    </p>
  </div>
<ul id="liste_template">
<?php

foreach( $liste_templates AS $template )
{
  if( file_exists( '../images/' . $template . '/screenshot.png' ) )
    $images = '<a href="../images/' . $template . '/screenshot.png" title="capture d\'écran" rel="lightbox"><img src="../images/' . $template . '/screenshot.png" width="100"  alt="screenshot" /></a>';
  else
    $images = '<img src="images/unscreenshot.png" alt="screenshot" />';
  
  // description du template
  $text = file_exists( ADRESSE_TEMPLATES . '/' . $template . '/Information/lisez-moi.txt' ) ? Texte::extrait( Fichier::renvoie( ADRESSE_TEMPLATES . '/' . $template . '/Information/lisez-moi.txt' ), 200 ) : '';

?>
  <li id="<?php echo $template;?>"><h3><a href="template.php?view=<?php echo $template;?>"><?php echo $template;?></a></h3><?php echo $text;?></li>
<?php

}

?>
</ul>