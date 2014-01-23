<?php

include( __DIR__ . '/form.php' );

?>
<ul>
<?php

foreach( $infos_reponse AS $rep )
{

?>
  <li>
    <a href="#" onclick="sur('./utils/supprimer_form.php?article=<?php echo $_GET['article'];?>&amp;rep=<?php echo $rep['fr_id'];?>')"><img title="Supprimer" alt="Supprimer" src="images/b_drop.png" /></a>
    <a href="./form.php?article=<?php echo $_GET['article'];?>&amp;viewreponse=<?php echo $rep['fr_id'];?>">Réponse à <?php echo $rep['fr_date'];?> de <?php echo $rep['membre_pseudo'];?></a>
  </li>
<?php
}

?>
</ul>