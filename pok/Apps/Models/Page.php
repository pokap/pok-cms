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

namespace pok\Apps\Models;

class Page extends Base\Lister
{
  // String :
  //   NOM DE LA TABLE
  const TABLE = 'page';
  
  // -------------------------------------
  // PDOStatement :
  //   Renvoie la requete SQL qui selectionne la table
  public function publier() {
    return Base\Requete\PDOFournie::$INSTANCE->prepare( parent::publier( __CLASS__ ) );
  }
  
  // -------------------------------------
  // Int :
  //   Nombre de page
  public function count() {
    return (int) Base\Requete\PDOFournie::$INSTANCE->query( parent::count( self::TABLE ) )->fetchColumn();
  }
  
  // -------------------------------------
  // String :
  //   @string $arbo : arborescence sous forme 1/2/3 
  //   Supprime la derniere page de l'arborescence
  public static function strSupDernierePage( $arbo )
  {
    // on supprime la derniere page
    if( Page::issetPlus2Page($arbo) )
      $arbo = dirname($arbo);
    
    return $arbo;
  }
  
  // -------------------------------------
  // Bool :
  //   @string $arbo : arborescence sous forme 1/2/3 
  //   S'il y a plus d'une page dans l'arbo
  public static function issetPlus2Page( $arbo ) {
    return (boolean) strpos( $arbo, '/' );
  }
  
  // -------------------------------------
  // Bool :
  //   @string $arbo : arborescence sous forme 1/2/3 
  //   Vérifie que l'arborescence est en une
  public static function format( $arbo ) {
    return preg_match('`^[a-zA-Z0-9._-]+(/[a-zA-Z0-9._-]+)*$`', $arbo) || $arbo == '';
  }
  
  // -------------------------------------
  // Array :
  //   @string $str_arbo : arborescence sous forme 1/2/3 
  //  [@int    $limit    : correspond au paramètre "limit" de la fonction "explode"]
  //   Transforme la chaîne qui contient l'arborescence de dossier de la bdd en tableau
  public static function explode( $str_arbo, $limit = null )
  {
    if( $limit !== null )
      return \explode( '/', $str_arbo, $limit );
    else
      return \explode( '/', $str_arbo );
  }
  
  // -------------------------------------
  // String :
  //   @array $array_arbo : arborescence sous forme array( 1, 2, 3 ) 
  //   Inverse de pok\Dossier\explode()
  public static function implode( array $array_arbo ) {
    return \implode( $array_arbo, '/' );
  }
}
?>