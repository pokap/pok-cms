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

class FormulaireReponseModif extends Base\Modifier
{
  // Int :
  //   Identidiant de la question du formulaire
  protected $fr_id = array(null);
  //   Identifiant de l'article qui contient le formulaire
  protected $article_id = array(null);
  //   Identifiant du membre
  protected $membre_id = array(null);
  // String :
  //   La réponse
  protected $fr_value = array(null);
  //   Date de réponse
  protected $fr_date = array(null);
  
  // -------------------------------------
  // Array :
  //   Renvoie la les attributs de la classe sauf donnees, soit la liste des champs de la table
  public static function getChamps() {
    return self::getChampsBy( get_class_vars(__CLASS__) );
  }
  
  // -------------------------------------
  // Int :
  //   Créer un droit
  public function ajouter()
  {
    // serialize automatique les options
    if( is_array($this->fr_value[0]) ) {
      $this->fr_value[0] = serialize($this->fr_value[0]);
    }
    Base\Requete\PDOFournie::$INSTANCE->exec( parent::ajouter( FormulaireReponse::TABLE, self::getChamps() ) );
    // récupère le nouvelle ID
    return (int) Base\Requete\PDOFournie::$INSTANCE->lastInsertId();
  }
  
  // -------------------------------------
  // Bool :
  //   Met à jour un droit
  public function modifier()
  {
    // serialize automatique les options
    if( is_array($this->fr_value[0]) ) {
      $this->fr_value[0] = serialize($this->fr_value[0]);
    }
    $this->baseClause();
    Base\Requete\PDOFournie::$INSTANCE->exec( parent::modifier( FormulaireReponse::TABLE, self::getChamps() ) );
  }
  
  // -------------------------------------
  // Bool :
  //   Supprime un droit
  public function supprimer()
  {
    // serialize automatique les options
    if( is_array($this->fr_value[0]) ) {
      $this->fr_value[0] = serialize($this->fr_value[0]);
    }
    $this->baseClause();
    Base\Requete\PDOFournie::$INSTANCE->exec( parent::supprimer( FormulaireReponse::TABLE, self::getChamps() ) );
  }
  
  // -------------------------------------
  // Void :
  //   Vérifie la présence d'une clause, utile pour la modification ou la suppression
  public function baseClause()
  {
    if( $this->getWheres() == '' && $this->fr_id[0] !== null )
      $this->addWhere(self::simpleCritereClause( 'fr_id', array_merge( array('='), $this->fr_id ) ));
  }
}
