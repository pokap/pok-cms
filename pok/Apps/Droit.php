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

class Droit extends Models\Droit
{
  // -------------------------------------
  // Array :
  //   Renvoie la requete SQL qui selectionne la table, pour tous les droits d'un membre
  public function publierPourMembre()
  {
    // initialise
    $donnees = array();
    // on doit être connecté
    if( Outils\Session::connecter() )
    {
      $this->calculDonnees();
      $this->addJoin( config\PREFIX.MembreGroupe::TABLE, MembreGroupe::TABLE, MembreGroupe::TABLE.'.groupe_id = '.self::TABLE.'.groupe_id' );
      $this->addWhere( MembreGroupe::TABLE.'.membre_id = '.$_SESSION['id'] );
      $this->addGroup( self::TABLE.'.cat_id' );
      
      // connexion SQL
      PDOFournie::autoConnexion();
      // récupère les informations de la base de donnée
      $requete = parent::publier();
      $requete->execute();
      // déconnexion SQL
      PDOFournie::deconnexionAt(0);
      
      while( $enr = $requete->fetch(\PDO::FETCH_ASSOC) ) {
        $donnees[] = new DroitModif($enr);
      }
    }
    return $donnees;
  }
  
  public function publierCategorie()
  {
    // initialise
    $rules = array();
    //$this->calculDonnees();
    $this->addGroup('cat_id');
    // connexion SQL
    PDOFournie::autoConnexion();
    // récupère les informations de la base de donnée
    $requete = parent::publier();
    $requete->execute();
    // déconnexion SQL
    PDOFournie::deconnexionAt(0);
    
    while( $enr = $requete->fetch(\PDO::FETCH_ASSOC) ) {
      $rules[] = new DroitModif($enr);
    }
    return $rules;
  }
  
  // -------------------------------------
  // Array :
  //   Renvoie la requete SQL qui selectionne la table, pour tous les droits d'un visiteur
  public function publierPourVisiteur()
  {
    // initialise
    $donnees = array();
    // on doit être connecté
    if( !Outils\Session::connecter() )
    {
      $this->addWhere( self::TABLE.'.groupe_id = 1' );
      $this->calculDonnees();
      // connexion SQL
      PDOFournie::autoConnexion();
      // récupère les informations de la base de donnée
      $requete = parent::publier();
      $requete->execute();
      // déconnexion SQL
      PDOFournie::deconnexionAt(0);
      
      while( $enr = $requete->fetch(\PDO::FETCH_ASSOC) ) {
        $donnees[] = new DroitModif($enr);
      }
    }
    return $donnees;
  }
  
  // -------------------------------------
  // Array :
  //   Somme de tout les droits par page du membre
  public function publierPourMembreParPage( $id_page )
  {
    // lie la table cat_relation pour charger la catégorie par rapport à la page
    $this->addJoin( config\PREFIX.CatRelation::TABLE, CatRelation::TABLE, CatRelation::TABLE.'.cat_id = '.self::TABLE.'.cat_id AND terms = "page"' );
    $this->addWhere( CatRelation::TABLE.'.relation_id = '.intval($id_page) );
    $this->setLimit(1);
    $donnees = $this->publierPourMembre();
    // renvoie la premier ligne
    if( $donnees > array() )
      return $donnees[0];
    else
      return array();
  }
  
  // -------------------------------------
  // Array :
  //   Somme de tout les droits par page du visiteur
  public function publierPourVisiteurParPage( $id_page )
  {
    // lie la table cat_relation pour charger la catégorie par rapport à la page
    $this->addJoin( config\PREFIX.CatRelation::TABLE, CatRelation::TABLE, CatRelation::TABLE.'.cat_id = '.self::TABLE.'.cat_id AND terms = "page"' );
    $this->addWhere( CatRelation::TABLE.'.relation_id = '.intval($id_page) );
    $this->limit(1);
    $donnees = $this->publierPourVisiteur();
    // renvoie la premier ligne
    if( $donnees > array() )
      return $donnees[0];
    else
      return array();
  }
  
  // -------------------------------------
  // Void :
  //   Calcul la somme des champs de droit
  private function calculDonnees()
  {
    // ré-écrit les données
    $this->addDonnee(self::TABLE.'.cat_id');
    $this->addDonnee(self::TABLE.'.groupe_id');
    // les droits
    foreach( array('vlp','euna','raa','blr','etla','stla','ssa','mda') AS $champ ) {
      $this->addDonnee( 'SUM('.self::TABLE.'.'.$champ.') AS '.$champ );
    }
    $this->setAutoChamps(false);
  }
}
?>
