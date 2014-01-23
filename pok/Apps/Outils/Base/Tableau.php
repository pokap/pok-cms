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

namespace pok\Apps\Outils\Base;

abstract class Tableau
{
  // -------------------------------------
  // Mixed :
  //   comme array_search, il recherche une clé par rapport à la clé de la deuxième dimention du tableau
  public static function keySearch( $seach, $donnees )
  {
    foreach( $donnees AS $cle => $valeur )
      while( $ss = array_keys($valeur) )
        if( $seach == $ss )
          return $cle;

    return false;
  }

  // ----
  // Supprimer la clé d'un tableau
  public static function deleteByValue( $value, $array )
  {
    // clé de la valeur associer
    $key = array_search( $value, $array );
    unset( $array[$key] );
  }
}
?>