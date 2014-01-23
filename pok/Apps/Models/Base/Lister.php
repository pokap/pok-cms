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

namespace pok\Apps\Models\Base;

use \systems\cfg\config;

/*
  \pok\Liste est la classe de base pour toutes les Listes.
*/

abstract class Lister extends Requete
{
  // Boolean : 
  //   Si l'ajout des champs est automatique
  private $autoChamps = true;
  
  // String :
  //   Utiliser pour distinguer les requêtes dans un url
  protected $alias;
  
  // -------------------------------------
  // Void :
  //   Initialise la class
  public function __construct( array $list_arg = array(array()) )
  {
    if( $list_arg > array() )
    {
      $dernier_key = count($list_arg) - 1;
      // on utilise ces arguments pour générer la requète SQL
      foreach( $list_arg AS $num => $arg )
      {
        $this->clause( (array) $arg );
        // ne pas oublier de prendre en compte les ajouts
        // sauf pour le dernier
        if( $num != $dernier_key )
          $this->addOr();
      }
    }
  }
  
  // -------------------------------------
  // String :
  //   @string $alias : valeur de l'alias à mettre
  //   Enregistre l'alias
  public function setAutoChamps( $autoChamps ) {
    $this->autoChamps = (boolean) $autoChamps;
  }
  
  // -------------------------------------
  // String :
  //   @string $alias : valeur de l'alias à mettre
  //   Enregistre l'alias
  public function setAlias( $alias ) {
    $this->alias = (string) $alias;
  }
  
  // -------------------------------------
  // Boolean :
  //   Renvoie la valeur de l'alias
  public function getAutoChamps() {
    return( $this->autoChamps );
  }
  
  // -------------------------------------
  // String :
  //   Renvoie la valeur de l'alias
  public function getAlias() {
    return( $this->alias );
  }
  
  // -------------------------------------
  // Void :
  //   Pour limiter les donnees
  public function limit( $mini, $maxi = 0 )
  {
    $mini = intval($mini);
    $maxi = intval($maxi);
    // l'alias permet de modifier les valeurs de "limit" avec un $_GET
    // s'il n'est pas défini, on considère que les valeurs de "limit" ne change pas
    if( $this->alias == '' ) 
    {
      if( $maxi > 0 )
        $this->setLimit( $mini.','.$maxi );
      else
        $this->setLimit( $mini );
    }
    else
    {
      $page = array_key_exists( $this->alias, $_GET )? max( (int) $_GET[$this->alias], 1 ) : 1;
      
      if( $maxi > 0 )
        $this->setLimit( ( $page * $mini + ($page - 1) * ($maxi - $mini) ).', '.$maxi );
      else
        $this->setLimit( ( ($page - 1) * $mini ).', '.$mini );
    }
  }
  
  // -------------------------------------
  // Void :
  //   Permet d'obtenir des requêtes rapide et précis
  public static function __callStatic( $name, $arguments )
  {
    if( empty($arguments) )
    {
      if( $name == 'fetchAll' )
      {
        $cl = new static();
        if( !empty($arguments[1]) )
          return $cl->$arguments[1]();
        else
          return $cl->publier();
      }
    }
    else
    {
      if( strpos( $name, 'fetchBy' ) === 0 )
      {
        if( !empty($arguments[1]) )
          return self::publierByClass( 'fetchBy', $name, $arguments[0], '', $arguments[1] );
        else
          return self::publierByClass( 'fetchBy', $name, $arguments[0] );
      }
      elseif( strpos( $name, 'getBy' ) === 0 )
      {
        if( !empty($arguments[1]) )
          $result = self::publierByClass( 'getBy', $name, $arguments[0], '', $arguments[1] );
        else
          $result = self::publierByClass( 'getBy', $name, $arguments[0] );
        
        return( isset($result[0])? $result[0] : array() );
      }
    }
    throw new \pok\Exception('La méthode '.$name.' n\'existe pas.');
  }
  
  // -------------------------------------
  // Void :
  //   Permet d'obtenir des requêtes rapide et précis
  protected static function publierByClass( $method, $name, $argument, $class = '', $publier = 'publier' )
  {
    $name = substr( $name, strlen($method), strlen($name) );
    $name = \pok\Texte::lc_($name);
    
    if( is_array($argument) )
    {
      $clause = array();
      foreach( $argument AS $arg )
        $clause[] = array( '=', $arg );
    }
    else
    {
      $clause = array( '=', $argument );
    }
    
    if( $class == '' )
      $cl = new static(array(array( static::TABLE.'.'.$name => $clause )));
    else
      $cl = new $class(array(array( $class::TABLE.'.'.$name => $clause )));
    
    return $cl->$publier();
  }
  
  // -------------------------------------
  // String :
  //   @string $classnom : Nom de la classe
  //   On n'exécute pas de requete, on ajoute juste les champs
  protected function publier( $classnom )
  {
    $classnom_modif = $classnom . 'Modif';
    // Champs de la table
    if( $this->autoChamps )
      foreach( $classnom_modif::getChamps() AS $champ )
        $this->addDonnee( $classnom::TABLE.'.'.$champ );
    // ne pas oubliez l'alias du FROM :
    $this->addAlias( Requete::JOINS, $classnom::TABLE );
    // fabrique la requete
    $this->setRequete('SELECT ' . $this->getDonnees() . "\n" . 'FROM ' . config\PREFIX . $classnom::TABLE . ' AS ' . $classnom::TABLE . ' ' . $this->getJoins() . $this->getWheres() . $this->getGroups() . $this->getOrders() . $this->getLimit());
    return $this->getRequete();
  }
  
  // -------------------------------------
  // Int :
  //   Nombre d'information
  protected function count( $table )
  {
    $this->setRequete('SELECT COUNT(*) FROM ' . config\PREFIX . $table . ' AS ' . $table . ' ' . $this->getJoins() . $this->getWheres());
    return $this->getRequete();
  }
}
