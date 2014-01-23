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

abstract class Modifier extends Requete\Clause implements \ArrayAccess
{
  // Array :
  //   Autres donn�es
  private $donnees = array();
  
  abstract public static function getChamps();
  
  private $replace_mode = false;
  
  // -------------------------------------
  // Void :
  //   @array $valeurs : Les valeurs � assigner
  //   Constructeur de la classe qui assigne les donn�es sp�cifi�es en param�tre aux attributs correspondants
  public function __construct( array $valeurs = array() ) {
    $this->hydrate( $valeurs );
  }
  
  // -------------------------------------
  // Array :
  //   @array $attributs : liste des attributs de la classe
  //   Renvoie la liste des cl�s de la liste  tableau sauf "donnees"
  protected static function getChampsBy( array $attributs )
  {
    unset($attributs['donnees']); // sauf donnees
    return array_keys($attributs);
  }
  
  // -------------------------------------
  // Void :
  //   @array $donnees : Les donn�es � assigner
  //   M�thode assignant les valeurs sp�cifi�es aux attributs correspondant
  protected function hydrate( array $donnees )
  {
    foreach( $donnees as $attribut => $valeur )
    {
      $this->offsetSet( $attribut, $valeur );
    }
  }
  
  // -------------------------------------
  // Void :
  //   @bool $valeur : si on utilise le mode REPLACE au lieu de INSERT
  public function setReplaceMode( $valeur ) {
    $this->replace_mode = (boolean) $valeur;
  }
  
  // -------------------------------------
  // Void :
  //   Alias PDO::lastInsertId()
  public function getLastId() {
    Requete\PDOFournie::$INSTANCE->lastInsertId();
  }
  
  // -------------------------------------
  // String :
  //   @string  $table   : Nom de la Table
  //  [@boolean $replace : Si on choisie le mode REPLACE
  //   M�thode qui retourne le sql ajoutant les donn�es de la classe dans la table
  protected function ajouter( $table, $champs )
  {
    // initialise
    $attributs = array();
    // r�cup�re les champs
    foreach( $champs AS $champ ) {
      $attributs[] = self::getAttributQuote( $this->$champ );
    }
    // renvoie le r�sultat sous forme SQL
    if( !$this->replace_mode )
      $this->setRequete('INSERT INTO ' . config\PREFIX . $table . ' VALUES( ' . implode( ', ', $attributs ) . ' )');
    else
      $this->setRequete('REPLACE INTO ' . config\PREFIX . $table . ' VALUES( ' . implode( ', ', $attributs ) . ' )');
    
    return $this->getRequete();
  }
  
  // -------------------------------------
  // String :
  //   @string $table    : Nom de la Table
  //  [@array  $attribut : Clause de modification]
  //   M�thode qui retourne le sql modifiant les donn�es de la classe dans la table
  protected function modifier( $table, $champs )
  {
    // initialise
    $modifier = array();
    // r�cup�re les champs � modifier
    foreach( $champs AS $champ )
    {
      $valeur = &$this->$champ;
      if( $valeur[0] !== null )
        $modifier[] = $champ . ' = ' . self::getAttributQuote( $this->$champ );
    }
    $this->setRequete('UPDATE ' . config\PREFIX . $table . ' SET ' . implode( ', ', $modifier ) . $this->getWheres());
    return $this->getRequete();
  }
  
  // -------------------------------------
  // String :
  //   @string $table    : Nom de la Table
  //  [@array  $attribut : Clause de suppression]
  //   M�thode qui retourne le sql pour supprimer des lignes dans la table
  protected function supprimer( $table, $champs )
  {
    // r�cup�re les champs � modifier
    foreach( $champs AS $champ )
    {
      $valeur = &$this->$champ;
      if( $valeur[0] !== null )
        $this->addWhere(self::simpleCritereClause( $champ, array_merge( array('='), $this->$champ ) ));
    }
    $this->setRequete('DELETE ' . $table . ' FROM ' . config\PREFIX . $table . ' AS ' . $table . $this->getWheres());
    return $this->getRequete();
  }
  
  // -------------------------------------
  // Boolean :
  //   @string $attribut : Le nom de l'attribut
  //   M�thode v�rifiant que l'attribut sp�cifi� en param�tre existe
  public function offsetExists( $attribut )
  {
    return( isset($this->$attribut) || array_key_exists( $attribut, $this->donnees ) );
  }
  
  // -------------------------------------
  // Mixed :
  //   @string $attribut : Le nom de l'attribut
  //   M�thode renvoyant l'attribut sp�cifi� en param�tre
  public function offsetGet( $attribut )
  {
    if( isset($this->$attribut) ) {
      // on est obliger de r�cup�r� le contenu dans une autre variable sinon on r�cup�re le permier caract�re de "$attribut"
      $valeur = &$this->$attribut;
      
      if( array_key_exists( 0, $valeur ) )
        return $valeur[0];
      else
        throw new \pok\Exception('Erreur offset <strong>CLASS::$'.$attribut.'</strong>');
    }
    elseif( array_key_exists( $attribut, $this->donnees ) ) {
      return $this->donnees[$attribut][0];
    }
    else {
      throw new \pok\Exception('Vous n\'avez pas acc�s � l\'attribut <strong>CLASS::$'.$attribut.'</strong>');
    }
  }
  
  // -------------------------------------
  // Void :
  //   @string $attribut : Le nom de l'attribut
  //   @mixed $valeur : La valeur � assigner
  //   M�thode permettant d'assigner une valeur � un attribut
  public function offsetSet( $attribut, $valeur )
  {
    // test l'attibut appartient � la classe
    // et on modifie la variable en cons�quance
    if( isset($this->$attribut) )
      $this->$attribut = is_array($valeur)? $valeur : array( $valeur, null );
    else
      $this->donnees[$attribut] = is_array($valeur)? $valeur : array( $valeur, null );
  }
  
  // -------------------------------------
  // Bool :
  //   @string $attribut : Le nom de l'attribut
  //   M�thode permettant de supprimer un attribut
  public function offsetUnset( $attribut )
  {
    if( array_key_exists( $attribut, $this->donnees ) )
      unset( $this->donnees[$attribut] );
    elseif( isset($this->$attribut) )
      $this->$attribut = array(null);
    else
      return false;
    
    return true;
  }
  
  protected static function getAttributQuote( array $attribut )
  {
    // si on a pr�cis� le type d'encodage
    if( array_key_exists( 1, $attribut ) )
    {
      if( $attribut[1] == Requete\PDOFournie::NOT_QUOTE )
        return $attribut[0];
      else
        return Requete\PDOFournie::$INSTANCE->quote( $attribut[0], $attribut[1] );
    }
    else {
      return Requete\PDOFournie::$INSTANCE->quote( $attribut[0] );
    }
  }
}
?>
