<?php
// en-tête
include(__DIR__.'/header.php');

// si l'installation à réussi
if( $installer )
{
?>

<p style="color:green">L'installation à fonctionner !</p>
<?php
  if( $code_config != '' ) {
?>
<p>Vous devez copier le code suivant dans un config.php dans le répertoire "systems/cfg" :</p>
<blockquote style="border: 1px solid #999; background: #CCC; padding: 10px;">
<?php
    echo $code_config;
?>
</blockquote>
<?php
  }
?>
<p><strong>Mettez ces répertoires en CHMOD 777 : "systems/" &amp; "web/fichiers".</strong></p>
<p>Vous pouvez supprimer le répertoire "install/" pour plus de sécurité.</p>

<?php
// si l'installation à échoué
}
else
{
?>

<p style="color:red">L'installation à échouer !</p>
<p>Vous voulez rééssayer l'installation, veillez réactualiser la page pour retancer le script d'installation.</p>
<p>Si cela ne fonctionne toujours pas, vous pouvez relancer l'installation depuis le début : <a href="install/deletesession.php"><strong>Réinitialiser les informations d'installation.</strong></a></p>

<?php
}

// pied de page
include(__DIR__.'/footer.php');
?>
