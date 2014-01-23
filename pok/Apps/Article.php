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

use \systems\cfg\config;
use pok\Apps\Models\Base\Requete\PDOFournie;

class Article extends Models\Article
{
  // Boolean :
  //   Défini si on a join les tables de catégorie
  protected $join_cat = false;
  //   Défini si on a ajouter le chapo
  protected $use_chapo = false;
  
  // -------------------------------------
  // Array :
  //   La requete sql
  public function publier()
  {
    // initialise
    $donnees = array();
    
    // Pour la gestion des articles non-lu, on vérifie si l'utilisateur est connecté
    if( Outils\Session::connecter() )
    {
      // dans le cas d'une connexion existante, on ajoute les informations
      $this->addDonnee('CASE WHEN '.ArticleVu::TABLE.'.av_article_id IS NULL THEN 0 ELSE '.ArticleVu::TABLE.'.av_article_id END', 'id_sous_vu');
      $this->addDonnee('CASE WHEN '.ArticleVu::TABLE.'.av_poster IS NULL THEN 0 ELSE '.ArticleVu::TABLE.'.av_poster END', 'sous_vu_poster');
      $this->addJoin(config\PREFIX . ArticleVu::TABLE, ArticleVu::TABLE, ArticleVu::TABLE.'.av_reference_id = article.article_id AND '.ArticleVu::TABLE.'.av_membre_id = '.$_SESSION['id']);
    }
    else
    {
      $this->addDonnee(0,'id_sous_vu')->addDonnee(0,'sous_vu_poster');
      $this->addJoin(config\PREFIX . ArticleVu::TABLE, ArticleVu::TABLE, ArticleVu::TABLE.'.av_reference_id = article.article_id');
    }
    
    // l'auteur de l'article
    $this->addDonnee(Membre::TABLE.'.membre_pseudo','pseudo_auteur');
    $this->addDonnee(Membre::TABLE.'.page_id','page_id_auteur');
    $this->addDonnee(Membre::TABLE.'.membre_email','email_auteur');
    $this->addDonnee(Membre::TABLE.'.membre_inscrit','inscrit_auteur');
    $this->addDonnee(Membre::TABLE.'.membre_visite','visite_auteur');
    $this->addDonnee(Membre::TABLE.'.statut','statut_auteur');
    $this->addJoin( config\PREFIX.Membre::TABLE, Membre::TABLE, Membre::TABLE.'.membre_id = '.self::TABLE.'.article_auteur' );
    
    // page de l'auteur de l'article
    $this->addDonnee('page_membre.arborescence','arbo_auteur');
    $this->addJoin( config\PREFIX.Page::TABLE, 'page_membre', 'page_membre.page_id = '.Membre::TABLE.'.page_id');
    
    // dernier sous-article
    $this->addDonnee('dernier_sous_article.article_id','dernier_sous_article_id');
    $this->addJoin( config\PREFIX.self::TABLE, 'dernier_sous_article', 'dernier_sous_article.article_id = (SELECT CASE WHEN MAX(postlast.article_id) IS NULL THEN article.article_id ELSE MAX(postlast.article_id) END FROM '.config\PREFIX.self::TABLE.' AS postlast WHERE article.article_id = postlast.article_parent)' );
    $this->addJoin( config\PREFIX.Membre::TABLE, 'last_auteur', 'dernier_sous_article.article_auteur = last_auteur.membre_id');
    
    // categorie de l'article
    if( !$this->join_cat ) {
      $this->addJoin( config\PREFIX.CatRelation::TABLE, CatRelation::TABLE, CatRelation::TABLE.'.relation_id = '.Article::TABLE.'.article_id AND '.CatRelation::TABLE.'.terms = "article"');
      $this->addJoin( config\PREFIX.Categorie::TABLE, Categorie::TABLE, CatRelation::TABLE.'.cat_id = '.Categorie::TABLE.'.cat_id AND ( '.Categorie::TABLE.'.taxon = "article" OR '.Categorie::TABLE.'.taxon = "both" )');
      $this->join_cat = true;
    }
    $this->addDonnee(Categorie::TABLE.'.cat_id');
    $this->addDonnee(Categorie::TABLE.'.cat_nom');
    
    // page du groupe de l'auteur de l'article
    $this->addDonnee('page_groupe.page_nom','nom_groupe')->addDonnee('groupe.groupe_id','id_groupe')->addDonnee('groupe.couleur','couleur_groupe')->addDonnee('page_groupe.page_description','description_groupe')->addDonnee('page_groupe.arborescence','arbo_groupe');
    $this->addJoin( config\PREFIX.'membre_groupe', 'membre_groupe', 'membre_groupe.membre_id = membre.membre_id AND membre_groupe.principal = 1' );
    $this->addJoin( config\PREFIX.'groupe', 'groupe', 'groupe.groupe_id = membre_groupe.groupe_id' );
    $this->addJoin( config\PREFIX.Page::TABLE, 'page_groupe', 'page_groupe.page_id = groupe.page_id' );
    
    $this->addGroup( self::TABLE.'.article_id' );
    $this->addJoin( config\PREFIX.Page::TABLE, Page::TABLE, 'page.page_id = article.page_id');
    
    // connexion SQL
    PDOFournie::autoConnexion();
    // récupère les informations de la base de donnée
    $requete = parent::publier();
    $requete->execute();
    // déconnexion SQL
    PDOFournie::deconnexionAt(0);
    
    while( $enr = $requete->fetch(\PDO::FETCH_ASSOC) ) {
      $donnees[] = new ArticleModif($enr);
    }
    return $donnees;
  }
  
  // -------------------------------------
  // Int :
  //   Nombre d'article
  public function count()
  {
    // connexion SQL
    PDOFournie::autoConnexion();
    // récupère les informations de la base de donnée
    $num = parent::count();
    // déconnexion SQL
    PDOFournie::deconnexionAt(0);
    return $num;
  }
  
  // -------------------------------------
  // Void :
  //   Traitement des arguments pour la requète SQL
  protected function clause( array $arg )
  {
    // ------------------------
    // CATEGORIE & TAG
    if( array_key_exists( 'categorie', $arg ) && !empty($arg['categorie']) || array_key_exists( 'tag', $arg ) && !empty($arg['tag']) )
    {
      // si c'est la première fois qu'on demande la catégorie
      if( !$this->join_cat )
      {
        // on rajoute les tables qui contienne les catégories
        $this->addJoin( config\PREFIX.CatRelation::TABLE, CatRelation::TABLE, CatRelation::TABLE.'.relation_id = article.article_id' );
        // et on indique que c'est fait :)
        $this->join_cat = true;
        
        $joinon = CatRelation::TABLE.'.cat_id = '.Categorie::TABLE.'.cat_id AND '.CatRelation::TABLE.'.terms = "article"';
        // si c'est une categorie
        if( array_key_exists( 'categorie', $arg ) )
          // s'il y a quelque chose déjà
          $joinon .= ' AND ( '.Categorie::TABLE.'.taxon = "article" OR '.Categorie::TABLE.'.taxon = "both" )';
        // ou sinon c'est un tag
        else
          // on rajoute les informations
          $joinon .= 'AND ( '.Categorie::TABLE.'.taxon = "tag" )';
        
        $this->addJoin( config\PREFIX.Categorie::TABLE, Categorie::TABLE, $joinon );
        unset($joinon);
      }
    }
    // ------------------------
    // CHAPO
    if( !$this->use_chapo )
    {
      if( array_key_exists( 'extrait', $arg ) )
      {
        $this->addDonnee('LEFT( article.article_chapo, ' . $arg['extrait'] . ' ) AS article_chapo');
        unset($arg['extrait']);
      }
      else
        $this->addDonnee('article.article_chapo');
      // pour ne pas remettre plusieurs fois le chapo
      $this->use_chapo = true;
    }
    // ajoute les autres conditions
    parent::clause( $arg );
  }

  // -------------------------------------

  // Void :
  //   Traitement des arguments pour la requète SQL
  public function addOrder( $arg )
  {
    switch( $arg )
    {
      case 'id_post':
        $this->addOrder('article.article_id ASC');
      break;
      case '!id_post':
        $this->addOrder('article.article_id DESC');
      break;
      case 'id_dossier':
        $this->addOrder('article.page_id ASC');
      break;
      case '!id_dossier':
        $this->addOrder('article.page_id DESC');
      break;
      case 'date_creation':
        $this->addOrder('article.article_date_creer ASC');
      break;
      case '!date_creation':
        $this->addOrder('article.article_date_creer DESC');
      break;
      case 'date_update':
        $this->addOrder('article.article_date_reviser ASC');
      break;
      case '!date_update':
        $this->addOrder('article.article_date_reviser DESC');
      break;
      default: parent::addOrder($arg); break;
    }
  }
}
