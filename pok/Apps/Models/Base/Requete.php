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

namespace pok\Apps\Models\Base;

/*
  Permet de construire ça requete
*/

abstract class Requete extends Requete\Clause
{
  // Array :
  //   Contient la liste des données
  private $r_donnees = array();
  //   Contient la liste des jointures
  private $r_joins = array();
  //   Contient la liste des groupes
  private $r_groups = array();
  //   Contient la liste des ordres
  private $r_orders = array();
  //   Contient la liste des ordres
  private $r_alias = array( Requete::DONNEES => array(), Requete::JOINS => array() );
  //   Contient la limite
  private $r_limit = '';
  
  // Int :
  //   DEFINITION POUR LES TABLEAUX
  const DONNEES = 0;
  const JOINS = 1;
  
  // -------------------------------------
  // Requete/Boolean :
  //   @string $data  : les informations à ajouter
  //  [@string $alias : l'alias de l'information]
  //   Ajoute des informations à la liste des données, renvoie FALSE en cas d'echec
  public function addDonnee( $data, $alias = null )
  {
    if( $alias === null || $alias && $this->addAlias( Requete::DONNEES, $alias ) )
    {
      $this->r_donnees[] = $data.( ($alias)? ' AS '.$alias : '' );
      return $this;
    }
    else return false;
  }
  // -------------------------------------
  // Requetee/Boolean :
  //   @string $data  : les informations à ajouter
  //   @string $alias : l'alias de l'information
  //   @string $on    : l'alias de l'information
  //   Ajoute des informations à la liste des jointures, renvoie FALSE en cas d'echec
  public function addJoin( $data, $alias, $on = null )
  {
    if( $this->addAlias( Requete::JOINS, $alias ) )
    {
      $this->r_joins[$alias] = $data.' '.$alias.( ($on)? ' ON '.$on : '' );
      return $this;
    }
    else return false;
  }
  // -------------------------------------
  // Requete :
  //   @string $data : les informations à ajouter
  //   Ajoute des informations à la liste des groupes
  public function addGroup( $data )
  {
    $this->r_groups[] = (string) $data;
    return $this;
  }
  // -------------------------------------
  // Requete :
  //   @string $data : les informations à ajouter
  //   Ajoute des informations à la liste des ordres
  public function addOrder( $data )
  {
    $this->r_orders[] = (string) $data;
    return $this;
  }
  // -------------------------------------
  // Void :
  //   @string $data : les informations à ajouter
  //   Ajoute la limite
  public function setLimit( $data ) {
    $this->r_limit = (string) $data;
    return $this;
  }
  
  // -------------------------------------
  // Bool :
  //   @int    $type  : type d'alias
  //   @string $alias : alias à ajouter
  //   Ajoute un alias, renvoie TRUE si réussi
  protected function addAlias( $type, $alias )
  {
    if( !in_array( $alias, $this->r_alias[$type] ) && ( $type == Requete::DONNEES || $type == Requete::JOINS ) )
    {
      $this->r_alias[$type][] = (string) $alias;
      return true;
    }
    else return false;
  }
  
  // -------------------------------------
  // Void :
  //   Réinitialise la liste des données
  public function clearDonnees() {
    $this->r_donnees = array();
  }
  // -------------------------------------
  // Void :
  //   Réinitialise la liste des jointures
  public function clearJoins() {
    $this->r_joins = array();
  }
  // -------------------------------------
  // Void :
  //   Réinitialise la liste des groupes
  public function clearGroups() {
    $this->r_groups = array();
  }
  // -------------------------------------
  // Void :
  //   Réinitialise la liste des ordres
  public function clearOrders(){
    $this->r_orders = array();
  }
  
  // -------------------------------------
  // String :
  //   Renvoie l'équivalent sql des données
  protected function getDonnees()
  {
    return implode(', ', $this->r_donnees);
  }
  // -------------------------------------
  // String :
  //   Renvoie l'équivalent sql des jointures
  protected function getJoins()
  {
    if( $this->r_joins > array() )
      return "\n".'LEFT JOIN '.implode("\n".'LEFT JOIN ', $this->r_joins);
    else
      return '';
  }
  // -------------------------------------
  // String :
  //   Renvoie l'équivalent sql des groupes
  protected function getGroups()
  {
    if( $this->r_groups > array() )
      return ' GROUP BY '.implode(', ', $this->r_groups);
    else
      return '';
  }
  // -------------------------------------
  // String :
  //   Renvoie l'équivalent sql de la limite
  protected function getLimit()
  {
    if( $this->r_limit != '' )
      return ' LIMIT '.$this->r_limit;
    else
      return '';
  }
  // -------------------------------------
  // String :
  //   Renvoie l'équivalent sql des ordres
  protected function getOrders()
  {
    if( $this->r_orders > array() )
      return ' ORDER BY '.implode(', ', $this->r_orders);
    else
      return '';
  }
}
?>
