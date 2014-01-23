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

use pok\Apps\Models\Base\Requete\PDOFournie,
    \systems\cfg\config;

class FormulaireQuestionModif extends Base\Modifier
{
  // Int :
  //   Identidiant de la question du formulaire
  protected $fq_id = array(null);
  //   Identifiant de l'article qui contient le formulaire
  protected $article_id = array(null);
  // String :
  //   Type de question
  protected $fq_inputype = array(null);
  // Int :
  //   Ordre d'affichage des questions
  protected $fq_ordre = array(null);
  // String :
  //   <label> de la question
  protected $fq_label = array(null); 
  //   Texte descriptif de la question
  protected $fq_texte = array(null);
  //   Option de la question
  protected $fq_option = array(null);
  
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
    if( is_array($this->fq_option[0]) ) {
      $this->fq_option[0] = serialize($this->fq_option[0]);
    }
    PDOFournie::$INSTANCE->exec( parent::ajouter( FormulaireQuestion::TABLE, self::getChamps() ) );
    // récupère le nouvelle ID
    return (int) PDOFournie::$INSTANCE->lastInsertId();
  }
  
  // -------------------------------------
  // Bool :
  //   Met à jour un droit
  public function modifier()
  {
    // serialize automatique les options
    if( !empty($this->fq_option[0]) && is_array($this->fq_option[0]) ) {
      $this->fq_option[0] = serialize($this->fq_option[0]);
    }
    $this->baseClause();
    PDOFournie::$INSTANCE->exec( parent::modifier( FormulaireQuestion::TABLE, self::getChamps() ) );
  }

  // -------------------------------------
  // Bool :
  //   Enregistre le nouveau champ
  public static function changeOrdre( $id, $autre_id, $up_or_down )
  {
    // initialise
    $modif = $up_or_down ? '+ 1' : '- 1';
    // sécurise
    $id = intval($id);
    $autre_id = intval($autre_id);
    // on change
    PDOFournie::$INSTANCE->exec('UPDATE ' . config\PREFIX . FormulaireQuestion::TABLE . ' AS f1, ' . config\PREFIX . FormulaireQuestion::TABLE . ' AS f2 SET f1.fq_ordre = f2.fq_ordre WHERE f2.fq_id = '.$autre_id.' AND f1.fq_id = '.$id);
    PDOFournie::$INSTANCE->exec('UPDATE ' . config\PREFIX . FormulaireQuestion::TABLE . ' SET fq_ordre = fq_ordre '.$modif.' WHERE fq_id = '.$autre_id);
  }
  
  // -------------------------------------
  // Bool :
  //   Supprime un droit
  public function supprimer()
  {
    // serialize automatique les options
    if( is_array($this->fq_option[0]) ) {
      $this->fq_option[0] = serialize($this->fq_option[0]);
    }
    $this->baseClause();
    PDOFournie::$INSTANCE->exec( parent::supprimer( FormulaireQuestion::TABLE, self::getChamps() ) );
  }
  
  // -------------------------------------
  // Void :
  //   Vérifie la présence d'une clause, utile pour la modification ou la suppression
  public function baseClause()
  {
    if( $this->getWheres() == '' && $this->fq_id[0] !== null )
      $this->addWhere(self::simpleCritereClause( 'fq_id', array_merge( array('='), $this->fq_id ) ));
  }
  
  // -------------------------------------
  // Array :
  //   Représentation
  public function __toString()
  {
    return $this->fq_label[0];
  }
}
