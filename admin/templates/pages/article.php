<?php

if( isset($_GET['deplaceok']) )
  echo '<div class="valide">Le d�placement � fonctionn� correctement !</div>';
elseif( isset($_GET['e_deplace']) )
  echo '<div class="erreur">Le d�placement � �chou� !</div>';

if( isset($_GET['erreur']) )
{
  switch($_GET['erreur'])
  {
    case 'edit':
      echo '<div class="erreur">L\'�dition n\'a pas fonctionn�.</div>';
    break;
    case 'souscreer':
      echo '<div class="erreur">La cr�ation du sous-article n\'a pas fonctionn�.</div>';
    break;
    case 'creer':
      echo '<div class="erreur">La cr�ation de l\'article n\'a pas fonctionn�.</div>';
    break;
    default:
      echo '<div class="erreur">Une erreur est survenu.</div>';
    break;
  }
}

