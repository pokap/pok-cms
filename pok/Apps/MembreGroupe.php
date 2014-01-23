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

class MembreGroupe extends Models\MembreGroupe
{
  // -------------------------------------
  // Array :
  //   La requete sql
  public function publier()
  {
    // initialise
    $donnees = array();
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
  // Array :
  //   La requete sql
  public function publierGroupe()
  {
    // initialise
    $donnees = array();
    // groupe toussa =)
    $this->addDonnee(Groupe::TABLE.'.couleur','couleur');
    $this->addJoin( config\PREFIX.Groupe::TABLE, Groupe::TABLE, Groupe::TABLE.'.groupe_id = '.self::TABLE.'.groupe_id');
    // la page du groupe
    $this->addDonnee(Page::TABLE.'.page_id','page_id');
    $this->addDonnee(Page::TABLE.'.page_ordre','page_ordre');
    $this->addDonnee(Page::TABLE.'.page_nom','page_nom');
    $this->addDonnee(Page::TABLE.'.page_description','page_description');
    $this->addDonnee(Page::TABLE.'.template','template');
    $this->addDonnee(Page::TABLE.'.arborescence','arborescence');
    $this->addJoin( config\PREFIX.Page::TABLE, Page::TABLE, Page::TABLE.'.page_id = '.Groupe::TABLE.'.page_id');
    // la categorie de la page
    $this->addDonnee(Categorie::TABLE.'.cat_id','cat_id');
    $this->addDonnee(Categorie::TABLE.'.cat_nom','cat_nom');
    $this->addJoin( config\PREFIX.CatRelation::TABLE, CatRelation::TABLE, CatRelation::TABLE.'.relation_id = '.Page::TABLE.'.page_id AND terms = "page"');
    $this->addJoin( config\PREFIX.Categorie::TABLE, categorie::TABLE, categorie::TABLE.'.cat_id = '.CatRelation::TABLE.'.cat_id');
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
  // Array :
  //   La requete sql
  public function publierMembre()
  {
    // initialise
    $donnees = array();
    // groupe toussa =)
    $this->addDonnee(Groupe::TABLE.'.couleur','couleur');
    $this->addJoin( config\PREFIX.Groupe::TABLE, Groupe::TABLE, Groupe::TABLE.'.groupe_id = '.self::TABLE.'.groupe_id');
    // la categorie de la page
    $this->addDonnee(Membre::TABLE.'.membre_id ','membre_id ');
    $this->addDonnee(Membre::TABLE.'.membre_pseudo','membre_pseudo');
    $this->addDonnee(Membre::TABLE.'.membre_email','membre_email');
    $this->addDonnee(Membre::TABLE.'.membre_inscrit','membre_inscrit');
    $this->addDonnee(Membre::TABLE.'.membre_visite','membre_visite');
    $this->addDonnee(Membre::TABLE.'.statut','statut');
    $this->addDonnee(Membre::TABLE.'.valide','valide');
    $this->addJoin( config\PREFIX.Membre::TABLE, Membre::TABLE, Membre::TABLE.'.membre_id = '.MembreGroupe::TABLE.'.membre_id');
    // la page du groupe
    $this->addDonnee(Page::TABLE.'.page_id','page_id');
    $this->addDonnee(Page::TABLE.'.page_ordre','page_ordre');
    $this->addDonnee(Page::TABLE.'.page_nom','page_nom');
    $this->addDonnee(Page::TABLE.'.page_description','page_description');
    $this->addDonnee(Page::TABLE.'.template','template');
    $this->addDonnee(Page::TABLE.'.arborescence','arborescence');
    $this->addJoin( config\PREFIX.Page::TABLE, Page::TABLE, Page::TABLE.'.page_id = '.Membre::TABLE.'.page_id');
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
  //   Nombre d'article
  public function count()
  {
    // connexion SQL
    Models\Base\Requete\PDOFournie::autoConnexion();
    // récupère les informations de la base de donnée
    $num = parent::count();
    // déconnexion SQL
    Models\Base\Requete\PDOFournie::deconnexionAt(0);
    return $num;
  }
}
