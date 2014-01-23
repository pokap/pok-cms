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

use pok\Apps\Models\Base\Requete\PDOFournie,
    systems\cfg\config;

class ArticleModif extends Models\ArticleModif
{
  // -------------------------------------
  // Void :
  //   Ajouter informations du article
  public function ajouter()
  {
    // connexion SQL
    PDOFournie::autoConnexion();
    $id = parent::ajouter();
    // déconnexion SQL
    PDOFournie::deconnexionAt(0);
    return $id;
  }
  
  // -------------------------------------
  // Void :
  //   Met à jour les informations du article
  public function modifier()
  {
    // connexion SQL
    PDOFournie::autoConnexion();
    parent::modifier();
    // déconnexion SQL
    PDOFournie::deconnexionAt(0);
  }
  
  // -------------------------------------
  // Void :
  //   supprime un article
  public function supprimer()
  {
    // connexion SQL
    PDOFournie::autoConnexion();
    parent::supprimer();
    // déconnexion SQL
    PDOFournie::deconnexionAt(0);
  }
  
  // -------------------------------------
  // Void :
  //   Modifier catégorie
  public static function modifierCategorie( $article_id, $cat_id )
  {
    $catSql = new CatRelation(array(array(
      'cat_relation.relation_id' => array( '=', $article_id ),
      'cat_relation.terms'       => array( '=', 'article' ),
      'categorie.taxon'          => array( array( '=', Categorie::TAXON_ARTICLE ), array( '=', Categorie::TAXON_BOTH ) )
    )));
    foreach( $catSql->publier() AS $cat ) {
      $cat->supprimer();
    }
    
    $cat = new CatRelationModif();
    $cat['relation_id'] = $article_id;
    $cat['cat_id'] = $cat_id;
    $cat['terms'] = 'article';
    $cat->ajouter();
  }
  
  // -------------------------------------
  // Bool :
  //   Gestion automatique d'une modification d'un article, a base des données :
  //   article_id, cat_id, tags, article_titre, article_date_creer, article_date_max, article_chapo, article_texte, brouillon, niveau_comments
  public static function autoModification( array $modif )
  {
    $article = new ArticleModif($modif);
    // met à jour l'article
    try
    {
      $article->modifier();
      // ajoute la categorie
      if( isset($modif['cat_id']) && $modif['cat_id'] > 0 )
        self::modifierCategorie( $modif['article_id'], $modif['cat_id'] );
      
      // gestion les tags
      // supprime les tags déjà présent
      $tags = new CatRelation(array(array(
        'cat_relation.relation_id' => array( '=', $modif['article_id'] ),
        'cat_relation.terms'       => array( '=', 'article' ),
        'categorie.taxon'          => array( '=', Categorie::TAXON_TAG )
      )));
      foreach( $tags->publier() AS $tag )
      {
        $cat = new CatRelationModif();
        $cat['relation_id'] = $modif['article_id'];
        $cat['cat_id'] = (int) $modif['cat_id'];
        $cat['terms'] = 'article';
        $cat->supprimer();
      }
      // l'ajoute doit être différent d'une suppression
      // ajoute les tags
      $cat = new CatRelationModif();
      if( isset($modif['tags']) && is_array($modif['tags']) )
      {
        foreach( $modif['tags'] AS $tag_id )
        {
          $cat['relation_id'] = $modif['article_id'];
          $cat['cat_id'] = $tag_id;
          $cat['terms'] = 'article';
          $cat->ajouter();
        }
      }
    }
    catch( pok\Exception $e ) {
      return false;
    }
    return true;
  }
  
  // -------------------------------------
  // Bool :
  //   Déplace un article du dossier à un autre
  public static function deplacer( $article_id, $page_id )
  {
    // connexion SQL
    PDOFournie::autoConnexion();
    try
    {
      // on lance la transaction
      PDOFournie::$INSTANCE->beginTransaction();
      // on fait les déplacements
      self::deplaceArticleRecursive( $article_id, $page_id );
      // si jusque là tout se passe bien on valide la transaction
      PDOFournie::$INSTANCE->commit();
    }
    catch( \Exception $e )
    {
      // on annule la transation
      PDOFournie::$INSTANCE->rollback();
    }
    PDOFournie::deconnexionAt(0);
  }

  // -------------------------------------
  // Void :
  //   Exactement comme Articles::deplaceArticle() par contre il permet d'effectuer des tests
  private static function deplaceArticleRecursive( $article_id, $page_id )
  {
    $nbreq = PDOFournie::$INSTANCE->exec('UPDATE ' . config\PREFIX . 'article SET page_id = ' . $page_id . ' WHERE article_id = ' . $article_id);
    // s'il y a eu un update
    if( $nbreq > 0 )
      // recherche des sous-articles & mise à jour
      foreach( PDOFournie::$INSTANCE->query('SELECT article_id FROM ' . config\PREFIX . 'article WHERE article_parent = ' . $article_id) AS $sous )
        self::deplaceArticleRecursive( $sous['article_id'], $page_id );
  }
}
