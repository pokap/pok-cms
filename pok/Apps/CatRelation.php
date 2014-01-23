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

use \systems\cfg\config;
use pok\Apps\Models\Base\Requete\PDOFournie;

class CatRelation extends Models\CatRelation
{
  // -------------------------------------
  // Array :
  //   La requete sql
  public function publier()
  {
    // categorie
    $this->addDonnee(Categorie::TABLE.'.cat_id');
    $this->addDonnee(Categorie::TABLE.'.cat_nom');
    $this->addDonnee(Categorie::TABLE.'.taxon');
    $this->addJoin( config\PREFIX.Categorie::TABLE, Categorie::TABLE, self::TABLE.'.cat_id = '.Categorie::TABLE.'.cat_id');
    
    $donnees = array();
    // connexion SQL
    PDOFournie::autoConnexion();
    // récupère les informations de la base de donnée
    $requete = parent::publier();
    $requete->execute();
    // déconnexion SQL
    PDOFournie::deconnexionAt(0);
    
    while( $enr = $requete->fetch(\PDO::FETCH_ASSOC) ) {
      $donnees[] = new CatRelationModif($enr);
    }
    return $donnees;
  }
}
?>
