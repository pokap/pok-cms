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

namespace pok\Apps\Models;

class ArticlePriveModif extends Base\Modifier
{
  // Int :
  //   Identidiant de l'article
  protected $prive_article_id = array(null);
  //   Identidiant du membre
	protected $prive_membre_id = array(null);
  
  // -------------------------------------
  // Array :
  //   Renvoie la les attributs de la classe sauf donnees, soit la liste des champs de la table
  public static function getChamps() {
    return self::getChampsBy( get_class_vars(__CLASS__) );
  }
  
  // -------------------------------------
  // Int :
  //   Créer une catégorie
  public function ajouter()
  {
    // ajoute une ligne
    Base\Requete\PDOFournie::$INSTANCE->exec( parent::ajouter( ArticlePrive::TABLE, self::getChamps() ) );
    // récupère le nouvelle ID
    return (int) Base\Requete\PDOFournie::$INSTANCE->lastInsertId();
  }
  
  // -------------------------------------
  // Bool :
  //   Met à jour une catégorie
  public function modifier()
  {
    $this->baseClause();
    Base\Requete\PDOFournie::$INSTANCE->exec( parent::modifier( ArticlePrive::TABLE, self::getChamps() ) );
  }
  
  // -------------------------------------
  // Bool :
  //   Supprime une catégorie
  public function supprimer()
  {
    $this->baseClause();
    Base\Requete\PDOFournie::$INSTANCE->exec( parent::supprimer( ArticlePrive::TABLE, self::getChamps() ) );
  }
  
  // -------------------------------------
  // Void :
  //   Vérifie la présence d'une clause, utile pour la modification ou la suppression
  public function baseClause()
  {
    if( $this->getWheres() == '' && $this->prive_article_id[0] !== null )
      $this->addWhere(self::simpleCritereClause( 'prive_article_id', array_merge( array('='), $this->prive_article_id ) ));
  }
}
