<?php
###############################################################################
# LEGAL NOTICE                                                                # 
###############################################################################
# Copyright (C) 2008/2010  Florent Denis                                      #
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

namespace pok\Controleur;

use \systems\cfg\general;

abstract class Page
{
  public static $mere = array();
  
  public static $actuelle = array();
  
  // -------------------------------------
  // String :
  //    pour les permalinks
  public static function url( $page = '', $article = '', $plus = '' )
  {
    // utiliser un fichier externe ? genre un yml ?
    // configuration de base :
    $rendu = 'index.php?page=' . $page;
    if( !empty($article) ) $rendu .= '&amp;article=' . $article;
    
    if( !empty($plus) )
    {
      // gestion des requetes
      if( is_array($plus) )
        $rendu .= '&amp;' . http_build_query( $plus, '', '&amp;' );
      else
        $rendu .= $plus;
    }
    
    return $rendu;
  }
  
  // -------------------------------------
  // Void :
  //   Simple redirection
  public static function redirect( $url = './', array $query = array() )
  {
    // élément pré-défini
    if( $url[0] === '@' )
    {
      switch($url)
      {
        // reviens à la page précédente
        case '@revenir':
          header('location: ' . self::buildUrlQuery( $_SERVER['HTTP_REFERER'], $query ));
        break;
        // reviens à la page simplement
        case '@top':
          if( isset(self::$actuelle['arborescence']) )
            header('location: ' . self::getAdresse() . htmlspecialchars_decode(self::url( self::$actuelle['arborescence'], '', $query )) );
          else
            throw new \pok\Exception('Donn&eacute;es pok\Controleur\Page::$actuelle incorrecte, v&eacute;rifi&eacute; que pok\Main soit appel&eacute; !');
        break;
        // par default sur l'index.php, à éviter en cas d'URL rewriting, attention quand on dev :p
        default:
          header('location: ' . self::buildUrlQuery( self::getAdresse() . 'index.php', $query ));
        break;
      }
    }
    else
    {
      // on decode l'url car elle peut contenir des caractères encoder comme "&amp;"
      // pour les urls valides on doit utiliser "&amp;" mais pour faire une redirection on doit utiliser "&" à la place !
      header('location: ' . self::getAdresse() . htmlspecialchars_decode(self::buildUrlQuery( $url, $query )) );
    }
    exit;
  }
  
  // -------------------------------------
  // String :
  //    pour les permalinks
  public static function buildUrlQuery( $url, array $query, $separateur = '&' )
  {
    if( $query > array() )
    {
      // ajoutes les requetes avec un "&amp;" ou un "?"
      if( parse_url( $url, PHP_URL_QUERY ) != '' )
        $url .= $separateur;
      else
        $url .= '?';
      
      $url .= http_build_query( $query, '', $separateur );
    }
    
    return $url;
  }
  
  // -------------------------------------
  // String :
  //    pour les permalinks
  public static function getAdresse()
  {
    return 'http://' . general\DOMAINE . general\PATH;
  }
  
  // -------------------------------------
  // Array :
  //   Retour une liste de page
  public static function details( $page, $nb_page, $nb_affiche = 4 )
  {
    // initialise
    $list_page = array();
    $nb_page = max( $nb_page, 1 ); // minimum une page
    // pour savoir si on a besoin de mettre les '...'
    if( $nb_page > ( $nb_affiche * 2 + 1 ) ) {
      // dans ce cas il faut afficher les premières pages et les dernières
      // on commence par les premières
      for( $i = 1; $i <= $nb_affiche; $i++ )
        $list_page[] = $i;
      // on oublie pas
      $list_page[] = '...';
      // on termine par les dernières
      for( $i = ( $nb_page - $nb_affiche ); $i < $nb_page; $i++ )
        $list_page[] = $i;
    }
    else {
      // sinon on fait une simple boucle qui affiche toutes les pages
      for( $i = 1; $i <= $nb_page; $i++ )
        $list_page[] = $i;
    }
    return $list_page;
  }
}
