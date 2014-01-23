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

class FichierModif extends Base\Modifier
{
  // Int :
  //   Identidiant du fichier
  protected $fichier_id = array(null);
  // String :
  //   Nom du fichier
  protected $fichier_nom = array(null);
  // Int :
  //   Poids du fichier
  protected $poids = array(null);
  // String :
  //   Description du fichier
  protected $fichier_description = array(null);
  //   Extension du fichier
  protected $extension = array(null); 
  // Int :
  //   Nombre de t�l�chargement
  protected $telecharger = array(null);
  
  // -------------------------------------
  // Array :
  //   Renvoie la les attributs de la classe sauf donnees, soit la liste des champs de la table
  public static function getChamps() {
    return self::getChampsBy( get_class_vars(__CLASS__) );
  }
  
  // -------------------------------------
  // Int :
  //   Cr�er un droit
  public function ajouter()
  {
    Base\Requete\PDOFournie::$INSTANCE->exec( parent::ajouter( Fichier::TABLE, self::getChamps() ) );
    // r�cup�re le nouvelle ID
    return (int) Base\Requete\PDOFournie::$INSTANCE->lastInsertId();
  }
  
  // -------------------------------------
  // Bool :
  //   Met � jour un droit
  public function modifier()
  {
    $this->baseClause();
    Base\Requete\PDOFournie::$INSTANCE->exec( parent::modifier( Fichier::TABLE, self::getChamps() ) );
  }
  
  // -------------------------------------
  // Bool :
  //   Supprime un droit
  public function supprimer()
  {
    $this->baseClause();
    Base\Requete\PDOFournie::$INSTANCE->exec( parent::supprimer( Fichier::TABLE, self::getChamps() ) );
  }
  
  // -------------------------------------
  // Void :
  //   V�rifie la pr�sence d'une clause, utile pour la modification ou la suppression
  public function baseClause()
  {
    if( $this->getWheres() == '' && $this->fichier_id[0] !== null )
      $this->addWhere(self::simpleCritereClause( 'fichier_id', array_merge( array('='), $this->fichier_id ) ));
  }
  
  // -------------------------------------
  // Array :
  //   Repr�sentation
  public function __toString()
  {
    return $this->fichier_nom[0];
  }
}
