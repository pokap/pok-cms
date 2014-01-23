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

use pok\Apps\Outils\Session;

abstract class Enligne
{
  // Int :
  //   Temps d'affichage des membres en ligne avant déconnexion automatique
  public static $actualise = 300;
  
  // Int :
  //   DEFINITION POUR LE TRI
  const MEMBRE = 0;
  const VISITEUR = 1;
  
  // -------------------------------------
  // Void :
  //   Met à jour le fichier temporaire de ça représentation de connexion sur le site.
  public static function revise()
  {
    if( !Session::connecter() )
      touch( ADRESSE_ENLIGNE . '/' . ip2long($_SERVER['REMOTE_ADDR']) );
    // Sinon le visiteur est connecté
    else
      touch( ADRESSE_ENLIGNE . '/m' . $_SESSION['id'] );
  }
  
  // -------------------------------------
  // Array :
  //   on renvoie un tableau 'membre' et 'visiteur' qui contient les ID membres et les IP visiteurs
  public static function traiter()
  {
    // initialise
    $resultat = array( array(), array() );
    // Timestamp qu'il sera dans 5 minutes
    $timestamp = $_SERVER['REQUEST_TIME'] - self::$actualise;
    // On modifie la date de dernière modification du fichier
    self::revise();
    // fouille le dossier
    if( $dh = opendir(ADRESSE_ENLIGNE) )
    {
      while( ( $file = readdir($dh) ) !== false )
      {
        // on récupère tous les fichiers
        if( is_file(ADRESSE_ENLIGNE . '/' . $file) )
        {
          if( filemtime(ADRESSE_ENLIGNE . '/' . $file) <= $timestamp ) unlink(ADRESSE_ENLIGNE . '/' . $file);
          // on trie les visiteurs et utilisateur connecté
          else
          {
            if( ctype_digit($file) )
              $resultat[Enligne::VISITEUR][] = long2ip($file);
            else
              $resultat[Enligne::MEMBRE][] = intval( substr( $file, 1 ) );
          }
        }
      }
      closedir($dh); // ne pas oublier de fermer
    }
    return $resultat;
  }
}
?>