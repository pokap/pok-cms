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

namespace
{
  ###############################################################################
  #   PERMET D'ADAPTER LE SYSTEME SELON LA VERSION DE PHP                       #
  ###############################################################################
  // si on est pas sur une version au-dessus de php 5.3.0
  if( !defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50300 )
  {
    die('<h1>Erreur</h1><p>Votre version <b>PHP '.phpversion().'</b> n\'est pas suffisante, vous devez disposez de <a href="http://fr2.php.net/downloads.php">PHP 5.3.0</a> minimum</p>');
  }
  // à la version php 6 get_magic_quotes_gpc() n'existe plus
  if( PHP_VERSION_ID < 60000 )
  {
    // enleve les backslach dans les $_POST
    if( get_magic_quotes_gpc() )
    {
      $stripslashes_deep = function( $value ) {
        $value = is_array($value) ? array_map($stripslashes_deep, $value) : stripslashes($value);
        return $value;
      };
      $_POST = array_map($stripslashes_deep, $_POST);
    }
  }
}
?>