<?php
// en-t�te
include(__DIR__.'/header.php');

// si l'installation � r�ussi
if( $installer )
{
?>

<p style="color:green">L'installation � fonctionner !</p>
<?php
  if( $code_config != '' ) {
?>
<p>Vous devez copier le code suivant dans un config.php dans le r�pertoire "systems/cfg" :</p>
<blockquote style="border: 1px solid #999; background: #CCC; padding: 10px;">
<?php
    echo $code_config;
?>
</blockquote>
<?php
  }
?>
<p><strong>Mettez ces r�pertoires en CHMOD 777 : "systems/" &amp; "web/fichiers".</strong></p>
<p>Vous pouvez supprimer le r�pertoire "install/" pour plus de s�curit�.</p>

<?php
// si l'installation � �chou�
}
else
{
?>

<p style="color:red">L'installation � �chouer !</p>
<p>Vous voulez r��ssayer l'installation, veillez r�actualiser la page pour retancer le script d'installation.</p>
<p>Si cela ne fonctionne toujours pas, vous pouvez relancer l'installation depuis le d�but : <a href="install/deletesession.php"><strong>R�initialiser les informations d'installation.</strong></a></p>

<?php
}

// pied de page
include(__DIR__.'/footer.php');
?>
