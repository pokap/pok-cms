<?php
if( !empty($erreur) ) {
  echo $erreur;
}

// Les messages d'erreurs
if( isset($_GET['erreur']) )
{
  switch($_GET['erreur'])
  {
    case 'EXIST': echo '<div class="erreur">Le dossier du membre existe déjà !</div>'; break;
    case 'MANQUE': echo '<div class="erreur">Il manque des informations au dossier du membre !</div>'; break;
    case 'ERROR': echo '<div class="erreur">Une erreur sur la création du dossier du membre !</div>'; break;
    default : echo '<div class="erreur">Vous avez oubliez des champs, sinon le pseudo ou l\'e-mail existe déjà !</div>'; break;
  }
}
elseif( isset($_GET['erreurdelete']) )
  echo '<div class="erreur">Une erreur est survenu dans la suppression du membre !</div>';
elseif( isset($_GET['modifok']) )
  echo '<div class="valide">Modification effectué !</div>';
?>