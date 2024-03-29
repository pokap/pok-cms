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

class DroitModif extends Models\DroitModif
{
  // -------------------------------------
  // Void :
  //   Ajouter informations de droits
  public function ajouter()
  {
    // connexion SQL
    PDOFournie::autoConnexion();
    parent::ajouter();
    // d�connexion SQL
    PDOFournie::deconnexionAt(0);
  }
  
  // -------------------------------------
  // Void :
  //   Mais � jour les informations de droits
  public function modifier()
  {
    // connexion SQL
    PDOFournie::autoConnexion();
    parent::modifier();
    // d�connexion SQL
    PDOFournie::deconnexionAt(0);
  }
  
  // -------------------------------------
  // Void :
  //   supprime un droit
  public function supprimer()
  {
    // connexion SQL
    PDOFournie::autoConnexion();
    parent::supprimer();
    // d�connexion SQL
    PDOFournie::deconnexionAt(0);
  }
}
