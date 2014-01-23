<?php

if( isset($_GET['deplaceok']) )
  echo '<div class="valide">Le déplacement à fonctionné correctement !</div>';
elseif( isset($_GET['e_deplace']) )
  echo '<div class="erreur">Le déplacement à échoué !</div>';

if( isset($_GET['erreur']) )
{
  switch($_GET['erreur'])
  {
    case 'edit':
      echo '<div class="erreur">L\'édition n\'a pas fonctionné.</div>';
    break;
    case 'souscreer':
      echo '<div class="erreur">La création du sous-article n\'a pas fonctionné.</div>';
    break;
    case 'creer':
      echo '<div class="erreur">La création de l\'article n\'a pas fonctionné.</div>';
    break;
    default:
      echo '<div class="erreur">Une erreur est survenu.</div>';
    break;
  }
}

