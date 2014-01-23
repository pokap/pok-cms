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

class Groupe extends Models\Groupe
{
  // -------------------------------------
  // Array :
  //   La requete sql
  public function publier()
  {
    // initialise
    $donnees = array();
    // la page du groupe
    $this->addDonnee(Page::TABLE.'.page_id','page_id');
    $this->addDonnee(Page::TABLE.'.page_ordre','page_ordre');
    $this->addDonnee(Page::TABLE.'.page_nom','page_nom');
    $this->addDonnee(Page::TABLE.'.page_description','page_description');
    $this->addDonnee(Page::TABLE.'.template','template');
    $this->addDonnee(Page::TABLE.'.arborescence','arborescence');
    $this->addJoin( config\PREFIX.Page::TABLE, Page::TABLE, Page::TABLE.'.page_id = '.Groupe::TABLE.'.page_id');
    $this->addGroup(Groupe::TABLE.'.page_id');
    // la categorie de la page
    $this->addDonnee(Categorie::TABLE.'.cat_id','cat_id');
    $this->addDonnee(Categorie::TABLE.'.cat_nom','cat_nom');
    $this->addJoin( config\PREFIX.CatRelation::TABLE, CatRelation::TABLE, CatRelation::TABLE.'.relation_id = '.Page::TABLE.'.page_id AND terms = "page"');
    $this->addJoin( config\PREFIX.Categorie::TABLE, Categorie::TABLE, Categorie::TABLE.'.cat_id = '.CatRelation::TABLE.'.cat_id');
    // relation membre-groupe
    $this->addJoin( config\PREFIX.MembreGroupe::TABLE, MembreGroupe::TABLE, MembreGroupe::TABLE.'.groupe_id = '.Groupe::TABLE.'.groupe_id');
    // connexion SQL
    PDOFournie::autoConnexion();
    // récupère les informations de la base de donnée
    $requete = parent::publier();
    $requete->execute();
    // déconnexion SQL
    PDOFournie::deconnexionAt(0);
    
    while( $enr = $requete->fetch(\PDO::FETCH_ASSOC) ) {
      $donnees[] = new GroupeModif($enr);
    }
    return $donnees;
  }
  
  // -------------------------------------
  // Int :
  //   Nombre de groupe
  public function count()
  {
    // connexion SQL
    PDOFournie::autoConnexion();
    // récupère les informations de la base de donnée
    $num = parent::count();
    // déconnexion SQL
    PDOFournie::deconnexionAt(0);
    return $num;
  }
}
  
