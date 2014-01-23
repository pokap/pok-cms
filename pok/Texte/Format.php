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

namespace pok\Texte;

abstract class Format
{
  // -------------------------------------
  // Renvoie un booléen selon si la donnée entrée correspond au format
  // Format du téléphone : Française, USA ou vert
  public static function telephone( $phone )
  {
    $purger = str_replace( array('-','.',' '), '', $phone );
    // il doit contenir 10 chiffres exactement
    if( ctype_digit( $purger ) && strlen( $purger ) == 10 )
      return true;
    else
      return false;
  }
}