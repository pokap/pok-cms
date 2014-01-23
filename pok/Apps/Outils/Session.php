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

namespace pok\Apps\Outils;

abstract class Session
{
  // -------------------------------------
  // Bool :
  //   Renvoie TRUE si le visiteur courant est connecté
  public static function connecter()
  {
    return( !empty($_SESSION['id']) && $_SESSION['id'] > 0 );
  }
  
  // -------------------------------------
  // Bool :
  //   Grâce à ce code, chaque session a un propriétaire unique, définit par son IP et son user-agent,
  //   ce qui rend impossible les attaquer par fixation de session, par faille XSS, ou tout autre vol de session.
  //   Renvoie TRUE si le visiteur est en règle
  public static function fixeConnexion()
  {
    if( self::connecter() )
    {
      $identifiant = md5( $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] );
      if( !isset($_SESSION['identifiant']) )
        $_SESSION['identifiant'] = $identifiant;
      elseif( $_SESSION['identifiant'] != $identifiant )
        return false;
    }
    return true;
  }
  
  // -------------------------------------
  // Void :
  //   Creer un jeton contre les failles de type CSRF
  public static function creerJeton()
  {
    if( !isset($_SESSION['jeton']) )
    {
      $_SESSION['jeton'] = md5( uniqid( mt_rand(), true ) );
      $_SESSION['jeton_temps'] = $_SERVER['REQUEST_TIME'];
    }
  }
  
  // -------------------------------------
  // Bool :
  //   @int $temps_limite : Temps d'expiration du jeton
  //   Test l'existence et la validité d'un jeton
  public static function verifieJeton( $temps_limite = 240 )
  {
    return( isset($_GET['jeton'], $_SESSION['jeton'], $_SESSION['jeton_temps']) && $_GET['jeton'] == $_SESSION['jeton'] && ( ($_SERVER['REQUEST_TIME'] - $_SESSION['jeton_temps']) <= $temps_limite || $temps_limite <= 0 ) );
  }
}
 ?> 