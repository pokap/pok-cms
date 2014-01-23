<?php

include( __DIR__ . '/form.php' );

use pok\Apps\Formulaire;

if( !empty($formulaire) )
{
  // initialise
  $nb_form = count($formulaire) - 1;
  for( $i = 0; $i <= $nb_form; $i++ )
  {
  
?>
<fieldset style="margin: 12px 50px;">
  <legend>
    <a href="./form.php?article=<?php echo $_GET['article'];?>&amp;modif=<?php echo $formulaire[$i]['fq_id'];?>"><img title="Modifier" alt="Modifier" src="images/b_edit.png" /></a>
    <a href="#" onclick="sur('./utils/supprimer_form.php?article=<?php echo $_GET['article'];?>&amp;delete=<?php echo $formulaire[$i]['fq_id'];?>&amp;jeton=<?php echo $_SESSION['jeton'];?>')"><img title="Supprimer" alt="Supprimer" src="images/b_drop.png" /></a>
    <?php
    
    if( $i > 0 )
    {
    
    ?>
    <a href="./utils/modif_form_order.php?article=<?php echo $_GET['article'];?>&amp;id=<?php echo $formulaire[$i]['fq_id'];?>&amp;up=<?php echo $formulaire[$i - 1]['fq_id'];?>&amp;jeton=<?php echo $_SESSION['jeton'];?>"><img title="Remonter" alt="monter" src="images/up.png" /></a>
    <?php
    
    }
    if( $i < $nb_form )
    {
    
    ?>
    <a href="./utils/modif_form_order.php?article=<?php echo $_GET['article'];?>&amp;id=<?php echo $formulaire[$i]['fq_id'];?>&amp;down=<?php echo $formulaire[$i + 1]['fq_id'];?>&amp;jeton=<?php echo $_SESSION['jeton'];?>"><img title="Redescendre" alt="descendre" src="images/down.png" /></a>
    <?php
    
    }
    
    ?>
  </legend>
  <dl style="margin:0;">
    <dt><strong><?php echo $formulaire[$i]['fq_inputype'];?> :</strong></dt>
    <dd style="margin: 12px 20px;"><?php echo Formulaire::getHtmlResult(array($formulaire[$i]));?></dd>
  </dl>
</fieldset>
<?php
    
  }
}
else
{

?>
<fieldset style="margin: 12px 50px;">
  <legend> <img title="Modifier" alt="Modifier" src="images/b_unedit.png"/> <img title="Supprimer" alt="Supprimer" src="images/b_undrop.png"/> </legend>
  <dl style="margin:0;">
    <dt><strong>Info :</strong></dt>
    <dd style="margin:12px 20px;">Il n'y a pas de formulaire.</dd>
  </dl>
</fieldset>
<?php

}
