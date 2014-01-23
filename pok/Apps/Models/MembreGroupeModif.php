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

use pok\Apps\Models\Base\Requete\PDOFournie,
    systems\cfg\config;

class MembreGroupeModif extends Base\Modifier
{
  // Int :
  //   Identidiant du membre
	protected $membre_id = array(null);
  //   Identidiant du groupe
  protected $groupe_id = array(null);
  // Boolean :
  //   Si c'est le groupe principal du membre
	protected $principal = array(null);
  
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
    PDOFournie::$INSTANCE->exec( parent::ajouter( MembreGroupe::TABLE, self::getChamps() ) );
    // récupère le nouvelle ID
    return (int) PDOFournie::$INSTANCE->lastInsertId();
  }
  
  // -------------------------------------
  // Bool :
  //   Met à jour une catégorie
  public function modifier()
  {
    $this->baseClause();
    PDOFournie::$INSTANCE->exec( parent::modifier( MembreGroupe::TABLE, self::getChamps() ) );
  }
  
  // -------------------------------------
  // Bool :
  //   Supprime une catégorie
  public function supprimer()
  {
    $this->baseClause();
    PDOFournie::$INSTANCE->exec( parent::supprimer( MembreGroupe::TABLE, self::getChamps() ) );
  }
  
  public function supprimerMembre()
  {
    // le groupe_id est obligatoire
    if( empty($this->membre_id[0]) || empty($this->groupe_id[0]) ) return false;
    // sécurité
    $this->membre_id[0] = intval($this->membre_id[0]);
    $this->groupe_id[0] = intval($this->groupe_id[0]);
    
    if( $this->groupe_id[0] != 2 )
    {
      // si on supprime le groupe principal du membre, on le met principal dans le groupe Utilisateur
      $groupe_head = (boolean) PDOFournie::$INSTANCE->query('SELECT principal FROM ' . config\PREFIX . MembreGroupe::TABLE . ' WHERE membre_id = ' . $this->membre_id[0] . ' AND groupe_id = ' . $this->groupe_id[0] . ' LIMIT 1')->fetchColumn();
      
      // supprime la relation
      PDOFournie::$INSTANCE->query('DELETE FROM ' . config\PREFIX . MembreGroupe::TABLE . ' WHERE membre_id = ' . $this->membre_id[0] . ' AND groupe_id = ' . $this->groupe_id[0]);
      
      if( $groupe_head ) {
        PDOFournie::$INSTANCE->query('UPDATE ' . config\PREFIX . MembreGroupe::TABLE . ' SET principal = "1" WHERE membre_id = '.$this->membre_id[0].' AND groupe_id = 2');
      }
    }
  }
  
  public function supprimerGroupe()
  {
    // le groupe_id est obligatoire
    if( empty($this->groupe_id[0]) ) return false;
    // sécurité
    $this->groupe_id[0] = intval($this->groupe_id[0]);
    
    if( $this->groupe_id[0] > 2 )
    {
      // si on supprime le groupe principal du membre, on le met principal dans le groupe Utilisateur
      $membres = PDOFournie::$INSTANCE->query('SELECT membre_id FROM ' . config\PREFIX . MembreGroupe::TABLE . ' WHERE groupe_id = ' . $this->groupe_id[0] . ' AND principal = "1"');
      
      foreach( $membres AS $membre )
      {
        $mg = new MembreGroupeModif();
        $mg->addWhere('membre_id = '.$membre['membre_id'].' AND groupe_id = 2');
        $mg['principal'] = '1';
        $mg->modifier();
      }
      // supprime la relation
      PDOFournie::$INSTANCE->query('DELETE FROM ' . config\PREFIX . MembreGroupe::TABLE . ' WHERE groupe_id = ' . $this->groupe_id[0]);
    }
  }
  
  // -------------------------------------
  // Void :
  //   Vérifie la présence d'une clause, utile pour la modification ou la suppression
  public function baseClause()
  {
    if( $this->getWheres() == '' && $this->membre_id[0] !== null )
      $this->addWhere(self::simpleCritereClause( 'membre_id', array_merge( array('='), $this->membre_id ) ));
  }
}
