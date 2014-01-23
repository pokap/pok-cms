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

use pok\Apps\Models\Base\Requete\PDOFournie;
use \systems\cfg\config;

class PageModif extends Base\Modifier
{
  const ERREUR = 0;
  const EXISTE = -1;
  
  // Int :
  //   Identifiant du dossier
  protected $page_id = array(null);
  //   Ordre d'affichage du dossier
  protected $page_ordre = array(null);
  // String :
  //   Nom du dossier
  protected $page_nom = array(null);
  //   Description du dossier
  protected $page_description = array(null);
  //   Template du dossier
  protected $template = array(null);
  // Boolean :
  //   Si le dossier à le système lu/non-lu d'activé
  protected $nonlu = array(null);
  // String :
  //   Arborescence du dossier
  protected $arborescence = array(null);
  
  // -------------------------------------
  // Array :
  //   Renvoie la les attributs de la classe sauf donnees, soit la liste des champs de la table
  public static function getChamps() {
    return self::getChampsBy( get_class_vars(__CLASS__) );
  }
  
  // -------------------------------------
  // Int :
  //   Créer une page
  public function ajouter()
  {
    if( Page::format($this->arborescence[0]) )
    {
      // sécurité
      // recherche s'il y a déjà un dossier qui aurait la même arborescence
      $exist = (boolean) PDOFournie::$INSTANCE->query('SELECT COUNT(*) FROM ' . config\PREFIX . Page::TABLE . ' WHERE '.self::simpleCritereClause( 'arborescence', array_merge( array('='), $this->arborescence ) ))->fetchColumn();
      if( !$exist )
      {
        // ajoute la page
        PDOFournie::$INSTANCE->exec( parent::ajouter( Page::TABLE, self::getChamps() ) );
        // récupère l'ID de la page
        return (int) PDOFournie::$INSTANCE->lastInsertId();
      }
      else return self::EXISTE;
    }
    else return self::ERREUR;
  }
  
  // -------------------------------------
  // Void :
  //   Mais à jour les informations de la page
  public function modifier()
  {
    $this->baseClause();
    
    // si on modifie l'arborescence, on le fait aussi pour toutes les sous-pages
    if( $this->arborescence[0] != '' && Page::format($this->arborescence[0]) )
    {
      // initialise
      $clause = $this->getWheres();
      $racine = !((boolean) $this->arborescence[0]);
      
      // récupère l'arborescence de la table qu'on modifie
      $ancienneArbo = PDOFournie::$INSTANCE->query('SELECT arborescence FROM ' . config\PREFIX . Page::TABLE . $clause . ' LIMIT 1')->fetchColumn();
      $pareil = strpos( $this->arborescence[0], $ancienneArbo );
      
      // il ne faut pas qu'on puisse envoyez notre page dans un de ces propres sous-page
      if( $pareil === 0 && Page::strSupDernierePage($this->arborescence[0]) != Page::strSupDernierePage($ancienneArbo) )
      {
        // si la destination est la même ça génère une erreur aussi, alors on vérifie qu'ils sont pareil
        throw new \pok\Exception('<p>L\'arborescence <b>'.$this->arborescence[0].'</b> est mauvaise.</p>');
        return false;
      }
      else
      {
        // récupère chaque sous-page pour changer leur arborescence
        $ext = PDOFournie::$INSTANCE->prepare('SELECT page_id, arborescence FROM ' . config\PREFIX . Page::TABLE . ' WHERE arborescence LIKE CONCAT( (SELECT arborescence FROM ' . config\PREFIX . Page::TABLE . ' WHERE ' . self::simpleCritereClause( 'page_id', array_merge( array('='), $this->page_id ) ).'),"%")');
        $ext->execute();
        while( $enr = $ext->fetch(\PDO::FETCH_ASSOC) )
        {
          // change l'arborescence
          $nouvelArbo = str_replace( $ancienneArbo, $this->arborescence[0], $enr['arborescence'] );
          // si on met la page à la racine du site, on enlève le '/' devant
          if( $racine ) $nouvelArbo = substr( $nouvelArbo, 1 );
          
          PDOFournie::$INSTANCE->exec('UPDATE ' . config\PREFIX . Page::TABLE . ' SET arborescence = CONCAT("' . $nouvelArbo . '", (SELECT * FROM (SELECT CASE WHEN COUNT(*) = 0 THEN "" ELSE CONCAT( "-", COUNT(*) ) END FROM ' . config\PREFIX . Page::TABLE . ' WHERE arborescence = "' . $nouvelArbo . '" AND page_id != ' . $enr['page_id'] . ' LIMIT 1) sys) ) WHERE page_id = ' . $enr['page_id']);
        }
      }
      // on supprime pour évider de modifier ce champ
      $this->arborescence = array(null);
    }
    // modifie donc la base de donnée
    PDOFournie::$INSTANCE->exec( parent::modifier( Page::TABLE, self::getChamps() ) );
  }
  
  // -------------------------------------
  // Void :
  //   Supprime une page et ces sous-pages
  public function supprimer()
  {
    $this->baseClause();
    // initialise
    $clause = $this->getWheres();
    $this->clearWheres();
    // modifie la clause
    $pages = PDOFournie::$INSTANCE->query('SELECT page_id FROM ' . config\PREFIX . Page::TABLE . ' WHERE arborescence LIKE CONCAT( (SELECT * FROM(SELECT arborescence FROM ' . config\PREFIX . Page::TABLE . $clause . ' LIMIT 1)subpage),"%")');
    foreach( $pages AS $page )
    {
      // supprime les relations categorie
      $categorie = new CatRelationModif(array(
        'relation_id' => $page['page_id'],
        'terms'       => 'page'
      ));
      $categorie->supprimer();
      // supprimer les articles
      $article = new ArticleModif(array( 'page_id' => $page['page_id'] ));
      $article->supprimer();
      // supprime la page
      PDOFournie::$INSTANCE->exec('DELETE FROM ' . config\PREFIX . 'page WHERE page_id = '.$page['page_id']);
    }
  }
  
  // -------------------------------------
  // Void :
  //   Vérifie la présence d'une clause, utile pour la modification ou la suppression
  public function baseClause()
  {
    if( $this->getWheres() == '' && $this->page_id[0] !== null )
      $this->addWhere(self::simpleCritereClause( 'page_id', array_merge( array('='), $this->page_id ) ));
  }
  
  // -------------------------------------
  // Array :
  //   Représentation
  public function __toString()
  {
    return static::getByArticle();
  }
}
