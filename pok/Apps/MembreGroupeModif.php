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

namespace pok\Apps;

use pok\Apps\Models\Base\Requete\PDOFournie,
    systems\cfg\config;

class MembreGroupeModif extends Models\MembreGroupeModif
{
  // -------------------------------------
  // Void :
  //   Ajouter informations à la table
  public function ajouter()
  {
    // connexion SQL
    PDOFournie::autoConnexion();
    $id = parent::ajouter();
    // déconnexion SQL
    PDOFournie::deconnexionAt(0);
    
    return $id;
  }
  
  // -------------------------------------
  // Void :
  //   Mais à jour les informations à la table
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
  //   Mais à jour les informations à la table
  public function supprimer()
  {
    // connexion SQL
    PDOFournie::autoConnexion();
    parent::supprimer();
    // déconnexion SQL
    PDOFournie::deconnexionAt(0);
  }
  
  // -------------------------------------
  // Void :
  //   Mais à jour les informations à la table
  public function supprimerMembre()
  {
    // connexion SQL
    PDOFournie::autoConnexion();
    parent::supprimerMembre();
    // déconnexion SQL
    PDOFournie::deconnexionAt(0);
  }
  
  // -------------------------------------
  // Void :
  //   Mais à jour les informations à la table
  public function supprimerGroupe()
  {
    // connexion SQL
    PDOFournie::autoConnexion();
    parent::supprimerGroupe();
    // déconnexion SQL
    PDOFournie::deconnexionAt(0);
  }
}
?>