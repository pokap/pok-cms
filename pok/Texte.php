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

namespace pok;

abstract class Texte
{
  // -------------------------------------
  // String :
  //   Renforme une chaine du type "ArticleId" en "article_id"
  static public function lc_( $name )
  {
    $name = lcfirst($name);
    $len  = strlen($name);
    
    for( $i = 1; $i < $len; ++$i )
    {
      $ord = ord($name[$i]);
      if( $ord >= 65 && $ord <= 90 ) {
        $name = str_replace( $name[$i], '_'.lcfirst($name[$i]), $name );
      }
    }
    return $name;
  }
  
  // -------------------------------------
  // String :
  //   Sort un extrait d'un texte
  static public function extrait( $string, $start = 20, $sep = ' [...]')
  {
    $extrait = substr( $string, 0, $start );
    $extrait = substr( $string, 0, strrpos( $extrait, ' ' ) ) . $sep;
    return $extrait;
  }
  
  // -------------------------------------
  // String :
  //   Enleve les <br />
  static public function br2nl( $string ) {
    return str_replace( array('<br />','<br>'), '', $string );
  }

  // -------------------------------------
  // String :
  //   Générateur de chaine de caractère aléatoire
  static public function generateur( $nchar = 8, $lettre_en_plus = '' )
  {
    $lettres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'.$lettre_en_plus;
    $number = $lettre_en_plus != '' ? 61 + strlen($lettre_en_plus) : 61;
    $lettres_generer = '';

    for( $i = 0; $i < $nchar; $i++ )
      $lettres_generer .= $lettres[mt_rand( 0, $number )];

    return $lettres_generer;
  }
  
  // -------------------------------------
  // String :
  //   Renvoie l'équivalent d'un texte pour un slug (url), merci symfony
  static public function slugify($text)
  {
    // replace non letter or digits by -
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
    // trim
    $text = trim($text, '-');
    // transliterate
    if( function_exists('iconv') )
      $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    
    // lowercase
    $text = strtolower($text);
    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    
    if( empty($text) )
      return 'n-a';
    
    return $text;
  }

  // -------------------------------------
  // String :
  //   supprime les caractères spéciaux
  static public function slcs( $str )
  {
    $str = strtr($str, 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝ@àáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaaceeeeiiiioooooouuuuyy');
    $str = preg_replace('`[^a-zA-Z0-9._-]`', '-', $str);
    return preg_replace('`(-){2,}`', '-', $str);
  }
}
