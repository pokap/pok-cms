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

use pok\Apps\Models\Base\Requete\PDOFournie,
    systems\cfg\config;

class Membre extends Base\Lister
{
  // String :
  //   DEFINITION DU STATUT D'UN MEMBRE
  const MEMBRE = 'M';
  const ADMIN  = 'A';
  const BANNIE = 'B';
  
  // String :
  //   NOM DE LA TABLE
  const TABLE = 'membre';
  
  // -------------------------------------
  // PDOStatement :
  //   Renvoie la requete SQL qui selectionne la table
  public function publier() {
    return PDOFournie::$INSTANCE->prepare(parent::publier(__CLASS__));
  }
  
  // -------------------------------------
  // Int :
  //   Nombre de membre
  public function count() {
    return (int) PDOFournie::$INSTANCE->query(parent::count( self::TABLE))->fetchColumn();
  }
  
  // -------------------------------------
  // String :
  //   Scriptage pour mot de passe
  public static function scriptmdp( $mdp )
  {
    return substr( self::script( $mdp, config\PREFIX_SALT, config\SUFFIX_SALT, config\CRYPT ), 0, 42 );
  }
  
  // -------------------------------------
  // String :
  //   Scriptage pour mot de passe
  public static function script( $mdp, $prefix_salt, $suffix_salt, $crypt )
  {
    return crypt( $prefix_salt . $mdp . $suffix_salt, $crypt );
  }
}
