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

class CatRelation extends Base\Lister
{
  // String :
  //   NOM DE LA TABLE
  const TABLE = 'cat_relation';
  
  // -------------------------------------
  // PDOStatement :
  //   Renvoie la requete SQL qui selectionne la table
  public function publier() {
    return Base\Requete\PDOFournie::$INSTANCE->prepare( parent::publier( __CLASS__ ) );
  }
  
  // -------------------------------------
  // Int :
  //   Nombre de relation catégorie
  public function count() {
    return (int) Base\Requete\PDOFournie::$INSTANCE->query( parent::count( self::TABLE ) )->fetchColumn();
  }
}
?>