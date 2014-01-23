<?php

include( __DIR__ . '/form.php' );

?>
<div class="stats">
<?php

if( $nombre_de_reponse > 0 )
{
  foreach( $result AS $info )
  {
    echo '<p><strong>',$info['texte'],'</strong></p>';
    if( is_array($info['valeur']) )
    {
      echo '<ul>';
      foreach( $info['valeur'] AS $value )
      {
        $nb = str_pad( round( $value['valeur'] / $nombre_de_reponse * 100 ), 2, '0', STR_PAD_LEFT );
        echo '<li><div style="width:200px;" class="pourcent"><div class="remplissage" style="width:',$nb,'%;">&nbsp;</div></div>',$nb,'% : ',$value['texte'],'</li>';
      }
      echo '</ul>';
    }
    else
    {
      $nb = str_pad( round( $info['valeur'] / $nombre_de_reponse * 100 ), 2, '0', STR_PAD_LEFT );
      echo '<ul><li><div style="width:200px;" class="pourcent"><div class="remplissage" style="width:',$nb,'%;">&nbsp;</div></div>',$nb,'%</li></ul>';
    }
  }
}

?>
<p>Sur <?php echo $nombre_de_reponse;?> réponse(s).</p></div>