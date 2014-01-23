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

namespace pok\Apps;

use pok\Apps\Models\Base\Requete\PDOFournie;

class PageModif extends Models\PageModif
{
  // -------------------------------------
  // Int :
  //   Ajouter informations d'une page
  public function ajouter()
  {
    // connexion SQL
    PDOFournie::autoConnexion();
    $nouveau = parent::ajouter();
    // déconnexion SQL
    PDOFournie::deconnexionAt(0);
    
    // développelent des pages auto
    if( $nouveau > 0 )
    {
      Ressource::$auto = true;
      Ressource::developper( $this->arborescence[0], $nouveau );
    }
    
    return $nouveau;
  }
  
  // -------------------------------------
  // Void :
  //   Mais à jour les informations d'une page
  public function modifier()
  {
    // connexion SQL
    PDOFournie::autoConnexion();
    parent::modifier();
    // déconnexion SQL
    PDOFournie::deconnexionAt(0);
  }
  
  // -------------------------------------
  // Void :
  //   supprime une page
  public function supprimer()
  {
    // connexion SQL
    PDOFournie::autoConnexion();
    parent::supprimer();
    // déconnexion SQL
    PDOFournie::deconnexionAt(0);
  }
}