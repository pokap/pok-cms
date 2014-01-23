<?php

if( isset($_GET['save']) )
{
  if( $resultat !== false )
    echo '<div class="valide">La restauration à bien fonctionnée.</div>';
  else
    echo '<div class="erreur">Le fichier de restauration est corrompu.</div>';
}

?>
<div class="select"><p><b><a class="bouton" href="./general.php">&laquo; Revenir au panneau de configuration</a></b></p></div>
  <h2>Liste des sauvegardes</h2>
  <p>Cliquez sur une sauvegarde pour la restaurer.</p>
  <ul>
<?php

foreach( $liste_saves AS $saves )
{

?>
    <li><a href="#" onclick="sur(\'restauration.php?save=<?php echo $saves;?>&amp;token=<?php echo $_SESSION['token'];?>\')">Sauvegarde du <?php echo date( "d/m Y à H\hi\ms\s" , basename( $saves, '.txt' ));?></a></li>
<?php

}

?>
  </ul>