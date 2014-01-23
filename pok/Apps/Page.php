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

namespace pok\Apps;

use pok\Apps\Models\Base\Requete\PDOFournie;
use \systems\cfg\config;

class Page extends Models\Page
{
  // -------------------------------------
  // Array :
  //   La requete sql
  public function publier()
  {
    // intialise
    $donnees = array();
    // à completer
    $this->addDonnee(Categorie::TABLE.'.cat_id');
    $this->addDonnee(Categorie::TABLE.'.cat_nom');
    $this->addJoin(config\PREFIX.CatRelation::TABLE, CatRelation::TABLE, Page::TABLE.'.page_id = '.CatRelation::TABLE.'.relation_id');
    $this->addJoin(config\PREFIX.Categorie::TABLE, Categorie::TABLE, CatRelation::TABLE.'.cat_id = '.Categorie::TABLE.'.cat_id');
    $this->addWhere(CatRelation::TABLE.'.terms = "page"');
    $this->addOrder(Page::TABLE.'.page_ordre');
    $this->addOrder(Page::TABLE.'.arborescence');
    // connexion SQL
    PDOFournie::autoConnexion();
    // récupère les informations de la base de donnée
    $requete = parent::publier();
    $requete->execute();
    // déconnexion SQL
    PDOFournie::deconnexionAt(0);
    
    while( $enr = $requete->fetch(\PDO::FETCH_ASSOC) ) {
      $donnees[] = new PageModif($enr);
    }
    return $donnees;
  }
  
  // -------------------------------------
  // Array :
  //   @string $reference : arborescence de référence sous forme 1/2/3 
  //   renvoie un tableau qui contient l'arborescence général des dossiers à partir d'une reference
  //   Attention : les dossiers Membres et Groupes sont automatiquement ignoré
  public static function publierListeFilter( $reference )
  {
    // initialise
    $arbo = array();
    $tabul = '';
    // connexion SQL
    PDOFournie::autoConnexion();
    // sécurité
    $reference = substr( PDOFournie::$INSTANCE->quote($reference), 1, -1 );
    if( $reference != '' ) $reference = '"' . $reference . '",';
    // récupère les informations de la base de donnée
    $requete = PDOFournie::$INSTANCE->prepare('SELECT page_id, page_nom, arborescence
    FROM '.config\PREFIX.'page
    WHERE arborescence NOT REGEXP CONCAT( "^(", CONCAT_WS("|", '.$reference.' (SELECT arborescence FROM '.config\PREFIX.'page WHERE page_id = 2), (SELECT arborescence FROM '.config\PREFIX.'page WHERE page_id = 3) ), ")(/[a-zA-Z0-9._-]+)*$")
    OR page_id = 2 OR page_id = 3
    ORDER BY arborescence');
    $requete->execute();
    // déconnexion SQL
    PDOFournie::deconnexionAt(0);
    
    while( $enr = $requete->fetch(\PDO::FETCH_ASSOC) )
    {
      // réinitialise
      $tabul = '';
      // calcule la profondeur du dossier
      for( $i = 0, $nb = count( self::explode($enr['arborescence']) ); $i < $nb; ++$i )
        $tabul .= '- ';
      
      $arbo[$enr['page_id']] = $tabul.$enr['page_nom'];
    }
    return $arbo;
  }
  
  // -------------------------------------
  // Array :
  //   @string $arborescence : arborescence à traiter (sous le format : 1/2/3)
  //   Renvoie la liste des sur-arborescences d'une arborescences
  public static function getFilAriane( $arborescence )
  {
    // initialise
    $clause = array( $arborescence );
    $strtarbopos = strrpos( $arborescence, '/' );
    // récupère chaque sous-parent-arbo
    while( $strtarbopos !== false )
    {
      $strtarbopos = strrpos( $arborescence, '/' );
      // récupère l'arborescence de la page parent
      $arborescence = substr( $arborescence, 0, $strtarbopos );
      $clause[] = $arborescence;
    }
    // s'il y a un seul dossier sans dernier / on ajoute la premier page
    if( $strtarbopos === false && $arborescence !== '' )
      $clause[] = '';
    
    return $clause;
  }
  
  // -------------------------------------
  // Array :
  //   @array $arborescence : arborescence à traiter (sous le format : array( '1/2/3', '1/2' ))
  //   Renvoie la clause pour une arborescence en reference
  public static function getReferenceClause( array $arborescence )
  {
    $reg = ( $arborescence === array('') )? '' : '('.implode( '|', $arborescence ).')'.'/';
    
    return array( 'REGEXP', '^'.$reg.'[a-zA-Z0-9._-]+$' );
  }
}