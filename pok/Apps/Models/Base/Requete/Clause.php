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

namespace pok\Apps\Models\Base\Requete;

/*
  Permet de construire des clauses
*/

abstract class Clause
{
  // Array :
  //   Contient tout les donnes ajouters
  private $r_all_wheres = array();
  //   Contient la liste des clauses
  private $r_wheres = array();
  //   Contient la liste des clauses au total
  private $r_wheres_or = array();

  // String :
  //   Dernière requète
  private $_requete = '';

  protected function setRequete( $value ) {
    $this->_requete = (string) $value;
  }
  public function getRequete() {
    return $this->_requete;
  }
  
  // -------------------------------------
  // Requete :
  //   @string $data : les informations à ajouter
  //   Ajoute des informations à la liste des clauses
  public function addWhere( $data )
  {
    $this->r_wheres[] = (string) $data;
    return $this;
  }
  // -------------------------------------
  // Void :
  //   Ajoute des clauses
  public function addOr()
  {
    if( $this->r_wheres > array() )
      $this->r_wheres_or[] = '( '.implode(' ) AND ( ', $this->r_wheres).' )';
    // réinitialise
    $this->r_wheres = array();
  }
  // -------------------------------------
  // Void :
  //   Réinitialise la liste des clauses
  public function clearWheres() {
    $this->r_wheres = $this->r_wheres_or = array();
  }
  
  // -------------------------------------
  // String :
  //   Renvoie l'équivalent sql des clauses
  protected function getWheres()
  {
    $this->addOr();
    if( $this->r_wheres_or > array() )
      return "\n".'WHERE ( '.implode(' ) OR ( ', $this->r_wheres_or).' )';
    else
      return '';
  }
  
  // -------------------------------------
  // String :
  //   Renvoie un clause classique du type : "mon_champ = 1"
  public static function critereClause( $champ, $condition, $valeur = null, $encode = \PDO::PARAM_STR )
  {
    // connexion SQL
    if( !($connexion_deja_existante = PDOFournie::issetConnexion(0)) )
      PDOFournie::autoConnexion();
    
    // initialise
    $clause = $champ . ' ' . $condition;
    // s'il y a une valeur
    if( $valeur !== null ) {
      $clause .= ' '.( ($encode === PDOFournie::NOT_QUOTE)? $valeur : PDOFournie::$INSTANCE->quote( $valeur, $encode ) );
    }
    // si la connexion vient d'être créer on la supprime
    if( !$connexion_deja_existante )
      PDOFournie::deconnexionAt(0);
    
    return $clause;
  }
  
  // -------------------------------------
  // String :
  //   Représente "critereClause" en plus simple
  public static function simpleCritereClause( $nom, array $donnees )
  {
    if( empty($donnees) ) throw new \pok\Exception('Erreur de données');
    
    return self::critereClause( $nom, $donnees[0], (array_key_exists( 1, $donnees )? $donnees[1] : null), (array_key_exists( 2, $donnees )? $donnees[2] : \PDO::PARAM_STR) );
  }
  
  // -------------------------------------
  // Void :
  //   Gère les cas d'argument dans le WHERE de la requete
  protected function clause( array $arg )
  {
    // piste toutes les clauses
    // une clause doit contenir au moins 2 éléments : le nom du champ & une condition
    // ensuite on peut ajouter la valeur à chercher, et un type d'encodage PDO
    foreach( $arg AS $champ => $data )
    {
      // si c'est un tableau, cela veut dire qu'on utilise plusieurs clause pour le même champ
      // on effectue la fonction d'ajoute de l'argument
      if( is_array($data[0]) )
      {
        // on est obligé de repasser une boucle
        $where = array();
        foreach( $data AS $value ) {
          $where[] = self::simpleCritereClause( $champ, $value );
        }
        // ajoute les autres clauses possible
        $this->addWhere(implode(' OR ', $where));
      }
      else
      {
        // un ajout classique
        $this->addWhere(self::simpleCritereClause( $champ, $data ));
      }
    }
  }
}
