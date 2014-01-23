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

class CatRelationModif extends Base\Modifier
{
  // Int :
  //   Identifiant de la page ou de l'article
  protected $relation_id = array(null);
  //   Identifiant de la cat�gorie
  protected $cat_id = array(null);
  // String :
  //   Type de relation : page, article
  protected $terms = array(null);
  
  // -------------------------------------
  // Array :
  //   Renvoie la les attributs de la classe sauf donnees, soit la liste des champs de la table
  public static function getChamps() {
    return self::getChampsBy( get_class_vars(__CLASS__) );
  }
  
  // -------------------------------------
  // Int :
  //   Cr�er une relation de cat�gorie page/article
  public function ajouter()
  {
    Base\Requete\PDOFournie::$INSTANCE->exec( parent::ajouter( CatRelation::TABLE, self::getChamps() ) );
    // r�cup�re le nouvelle ID
    return (int) Base\Requete\PDOFournie::$INSTANCE->lastInsertId();
  }
  
  // -------------------------------------
  // Bool :
  //   Met � jour une relation de cat�gorie page/article
  public function modifier()
  {
    $this->baseClause();
    Base\Requete\PDOFournie::$INSTANCE->exec( parent::modifier( CatRelation::TABLE, self::getChamps() ) );
  }
  
  // -------------------------------------
  // Bool :
  //   Supprime une relation de cat�gorie page/article
  public function supprimer()
  {
    $this->baseClause();
    Base\Requete\PDOFournie::$INSTANCE->exec( parent::supprimer( CatRelation::TABLE, self::getChamps() ) );
  }
  
  // -------------------------------------
  // Void :
  //   V�rifie la pr�sence d'une clause, utile pour la modification ou la suppression
  public function baseClause()
  {
    if( $this->getWheres() == '' && $this->cat_id[0] !== null )
      $this->addWhere(self::simpleCritereClause( 'cat_id', array_merge( array('='), $this->cat_id ) ));
  }
}