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

class NewsletterModif extends Base\Modifier
{
  // Int :
  //   Identidiant
  protected $newsletter_id = array(null);
  //   Identifiant de la page
  protected $page_id = array(null);
  // Boolean :
  //   Si elle est envoyer dès qu'il y a un nouvel article
  protected $newsletter_auto = array(null);
  // String :
  //   Titre
  protected $newsletter_titre = array(null);
  
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
    Base\Requete\PDOFournie::$INSTANCE->exec( parent::ajouter( Newsletter::TABLE, self::getChamps() ) );
    // récupère le nouvelle ID
    return (int) Base\Requete\PDOFournie::$INSTANCE->lastInsertId();
  }
  
  // -------------------------------------
  // Bool :
  //   Met à jour un droit
  public function modifier()
  {
    $this->baseClause();
    Base\Requete\PDOFournie::$INSTANCE->exec( parent::modifier( Newsletter::TABLE, self::getChamps() ) );
  }
  
  // -------------------------------------
  // Bool :
  //   Supprime un droit
  public function supprimer()
  {
    $this->baseClause();
    Base\Requete\PDOFournie::$INSTANCE->exec( parent::supprimer( Newsletter::TABLE, self::getChamps() ) );
  }
  
  // -------------------------------------
  // Void :
  //   Vérifie la présence d'une clause, utile pour la modification ou la suppression
  public function baseClause()
  {
    if( $this->getWheres() == '' && $this->newsletter_id[0] !== null )
      $this->addWhere(self::simpleCritereClause( 'newsletter_id', array_merge( array('='), $this->newsletter_id ) ));
  }
  
  // -------------------------------------
  // String :
  //   Représentation
  public function __toString()
  {
    return $this->newsletter_titre[0];
  }
}
