<?php
###############################################################################
# LEGAL NOTICE                                                                #
###############################################################################
# Copyright (C) 2008/2009  Florent Denis                                      #
# http://www.florentdenis.net                                                 #
#                                                                             #
# This program is free software: you can redistribute it and/or modify        #
# it under the terms of the GNU General Public License as published by        #
# the Free Software Foundation, either version 3 of the License, or           #
# (at your option) any later version.                                         #
#                                                                             #
# This program is distributed in the hope that it will be useful,             #
# but WITHOUT ANY WARRANTY; without even the implied warranty of              #
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               #
# GNU General Public License for more details.                                #
#                                                                             #
# You should have received a copy of the GNU General Public License           #
# along with this program.  If not, see <http://www.gnu.org/licenses/>        #
###############################################################################

namespace templates\pok_accueil\Controleur\fonctions;

// Alias namespace
use \pok\Controleur\Page;
use \pok\Apps\Outils;

// -------------------------------------
// String :
// 
//   Affiche un fil d'ariane
function affiche_fil_ariane( array $arborescence )
{
  foreach( $arborescence AS $page )
  {
    echo ' &raquo; <a href="',Page::url($page['arborescence']),'">',$page['page_nom'],'</a>';
  }
  // affiche la sous-page
  if( Page::$mere['page_id'] != Page::$actuelle['page_id'] )
  {
    echo ' &raquo; <a href="',Page::url(Page::$actuelle['arborescence']),'">',Page::$actuelle['page_nom'],'</a>';
  }
}

// -------------------------------------
// String :
//   Gère la pagination
function pagination( $numpage, $nb_topics, $article = '' )
{
  $pagination = '';
  
  foreach( Page::details( $numpage, $nb_topics ) AS $num )
  {
    if( $num != '...' )
      $pagination .= '<a href="' . Page::url( Page::$actuelle['arborescence'], $article, '&numpage='.$num ) . '">' . $num . '</a> ';
    else
      $pagination .= $num . ' ';
  }
  
  return $pagination;
}

// -------------------------------------
// String :
//   Affiche la date
function date_forme( $strtime )
{
  // jour en français
  $jours = array('Lun','Mar','Mer','Jeu','Ven','Sam','Dim');
  // mois
  $mois = array('Jan','Fév','Mars','Avr','Mai','Juin','Juil','Août','Sep','Oct','Nov','Dec');
  // timestamp
  $time = strtotime($strtime);
  // la date final
  return $jours[date('N', $time) - 1] . '. ' . date('d', $time) . ' ' . $mois[date('n', $time) - 1] . '. ' . date('Y \&\a\g\r\a\v\e\; H:i:s', $time);
}

// -------------------------------------
// String :
//   Renvoie le nom de l'image à utiliser pour l'état de lecture d'un article
function article_vu( $posts_id, $dernier_posts_id, $poster )
{
  // Si le membre est connecté
  if( Outils\Session::connecter() )
  {
    // s'il n'a pas posté
    if( $poster == 0 )
    {
      // s'il n'y a pas de nouveau message
      if( $posts_id >= $dernier_posts_id )
        return 'contenu_lu';
      // s'il y a un nouveau message
      else
        return 'contenu_vu_non_lu';
    }
    // s'il a posté
    else
    {
      // s'il n'y a pas de nouveau message
      if( $posts_id >= $dernier_posts_id )
        return 'contenu_poster_lu';
      // s'il y a un nouveau message
      else
        return 'contenu_poster_non_lu';
    }
  }
  // s'il n'est pas connecté
  else
    return 'contenu_lu';
}
