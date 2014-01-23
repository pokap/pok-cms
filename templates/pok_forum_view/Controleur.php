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

namespace templates\pok_forum_view;

use pok\Apps,
    pok\Controleur\Page AS CPage,
    pok\Apps\Models\Base\Requete\PDOFournie,
    systems\cfg\config;

class Controleur extends \pok\Controleur
{
  const TOPIC_PAR_PAGE = 20;
  const POST_PAR_PAGE = 20;
  
  // Array :
  //   Information sur le sujet visité
  private $topic = array();
  
  // -------------------------------------
  // Void :
  //   invoquer, est toujours appeller et doit toujours exister
  public function __invoke()
  {
    // si on présise pas l'id du post
    // on affiche tout les articles
    if( empty($_GET['article']) ) {
      $this->allTopic();
    }
    else
    {
      // obligatoire pour les droits
      $this->single_post();
      // si on fait une citation
      if( !empty($_GET['quote']) )
        $this->quote();
      // si on édite sont post
      elseif( !empty($_GET['editer']) )
        $this->editer();
      // sinon on affiche toutes les réponses
      elseif( $this->topic > array() )
        $this->commentaires();
    }
    // nombre de topic total
    $this->assign( 'numpage', (!empty($_GET['numpage'])? $_GET['numpage'] : 1) );
  }

  // -------------------------------------
  // Void :
  //   tout les posts de la page
  private function allTopic()
  {
    $postSql = new Apps\Article(array(array(
      'article.article_parent' => array( '=', 0 ),
      'article.page_id'        => array( '=', CPage::$actuelle['page_id'], PDOFournie::NOT_QUOTE ),
      'article.article_date_reviser' => array( '<=', 'NOW()', PDOFournie::NOT_QUOTE ),
      'article.article_date_max'     => array( array( '>=', 'NOW()', PDOFournie::NOT_QUOTE ), array( 'IS NULL', null ) ),
    )));
    // dernier commentaire
    $postSql->addDonnee('dernier_sous_article.article_date_creer','dernier_posts_date_creer');
    // auteur du dernier commentaire
    $postSql->addJoin(config\PREFIX.Apps\Article::TABLE, 'dernier_sous_article', 'dernier_sous_article.article_id = (SELECT CASE WHEN MAX(postlast.article_id) IS NULL THEN article.article_id ELSE MAX(postlast.article_id) END FROM '.config\PREFIX.Apps\Article::TABLE.' AS postlast WHERE article.article_id = postlast.article_parent)' );
    $postSql->addJoin(config\PREFIX.Apps\Membre::TABLE, 'last_auteur', 'dernier_sous_article.article_auteur = last_auteur.membre_id');
    $postSql->addJoin(config\PREFIX.Apps\Page::TABLE, 'pageuser', 'last_auteur.page_id = pageuser.page_id');
    $postSql->addDonnee('dernier_sous_article.article_id','last_article_id');
    $postSql->addDonnee('pageuser.arborescence','last_arbo_membre');
    $postSql->addDonnee('last_auteur.membre_pseudo','last_pseudo_membre');
    
    $postSql->addJoin(config\PREFIX.Apps\MembreGroupe::TABLE, 'mg', 'mg.membre_id = last_auteur.membre_id AND mg.principal = "1"');
    $postSql->addJoin(config\PREFIX.Apps\Groupe::TABLE, 'gg', 'gg.groupe_id = mg.groupe_id');
    $postSql->addJoin(config\PREFIX.Apps\Page::TABLE, 'pg', 'pg.page_id = gg.page_id');
    $postSql->addDonnee('gg.couleur','last_couleur');
    $postSql->addDonnee('pg.arborescence','last_arbo_groupe');
    $postSql->addDonnee('pg.page_nom','last_nom_groupe');
    
    $postSql->addOrder('categorie.cat_nom DESC, dernier_sous_article.article_date_creer DESC');
    $postSql->setAlias('numpage');
    $postSql->limit(self::TOPIC_PAR_PAGE);
    
    // les topics
    $this->assign( 'topics', $postSql->publier() );
    // nombre de topic total
    $this->assign( 'nb_topics', ceil( $postSql->count() / self::TOPIC_PAR_PAGE ) );
  }

  // ------------------------------------------
  // on regarde un seul post en particulier
  private function single_post()
  {
    $postSql = new Apps\Article(array(array(
      'article.article_parent' => array( '=', 0 ),
      'article.article_slug'   => array( '=', $_GET['article'] ),
      'article.page_id'        => array( '=', CPage::$actuelle['page_id'], PDOFournie::NOT_QUOTE ),
      'article.article_date_reviser' => array( '<=', 'NOW()', PDOFournie::NOT_QUOTE ),
      'article.article_date_max'     => array( array( '>=', 'NOW()', PDOFournie::NOT_QUOTE ), array( 'IS NULL', null ) ),
    )));
    self::setAvatar($postSql);
    
    if( $comm = $postSql->publier() )
    {
      $this->topic = $comm[0];
      /*
      foreach( $comm AS $num => $post )
        if( !empty($post['contenu_profil_auteurs']) )
          $comm[$num][] = array(unserialize($post['contenu_profil_auteurs']));
      */
    }
    $this->assign( 'topic', $this->topic );
  }

  // ------------------------------------------
  // les commentaires du posts en particulier
  private function commentaires()
  {
    if( isset($_GET['numpage']) && $_GET['numpage'] > 1 )
    {
      $limit = self::POST_PAR_PAGE;
      $i = 1 + ( $_GET['numpage'] - 1 ) * self::POST_PAR_PAGE;
    }
    else
    {
      $limit = self::POST_PAR_PAGE - 1;
      $i = 2;
    }
    $postSql = new Apps\Article(array(array(
      'article.article_parent' => array( '=', $this->topic['article_id'] ),
      'article.page_id' => array( '=', CPage::$actuelle['page_id'], PDOFournie::NOT_QUOTE ),
      'article.article_date_reviser' => array( '<=', 'NOW()', PDOFournie::NOT_QUOTE ),
      'article.article_date_max'     => array( array( '>=', 'NOW()', PDOFournie::NOT_QUOTE ), array( 'IS NULL', null ) ),
    )));
    $postSql->limit(0,$limit);
    $postSql->setAlias('numpage');
    self::setAvatar($postSql);
    
    $comm = $postSql->publier();
    /*
    foreach( $comm AS $num => $post ) {
      $comm[$num]['num'] = $i;
      if( !empty($post['contenu_profil_auteurs']) )
        $post += unserialize($post['contenu_profil_auteurs']);
      $i++;
    }
    */
    $this->assign( 'offset', $i );
    $this->assign( 'posts', $comm );
    $this->assign( 'nb_post', ceil( ($postSql->count() + 1) / self::POST_PAR_PAGE ) );
  }
  
  // ------------------------------------------
  // contenu du post à editer
  private function editer() {
    $this->assign( 'editer', Apps\Article::getByArticleId($_GET['editer']) );
  }
  // ------------------------------------------
  // contenu du post à citer
  private function quote() {
    $this->assign( 'quote', Apps\Article::getByArticleId($_GET['quote']) );
  }
  
  // ------------------------------------------
  static private function setAvatar( Apps\Article $art )
  {
    $art->addJoin( config\PREFIX.Apps\Fichier::TABLE, 'avatar', 'avatar.fichier_id = (SELECT filepp.fichier_id FROM '.config\PREFIX.Apps\Fichier::TABLE.' AS filepp WHERE filepp.fichier_nom = CONCAT( membre.membre_id, \'-avatar\' ) ORDER BY fichier_id DESC LIMIT 1)' );
    $art->addDonnee('CASE WHEN avatar.fichier_id IS NULL THEN NULL ELSE CONCAT( \'fichiers/\', avatar.extension , \'/\', avatar.fichier_nom, \'.\', avatar.extension ) END url_avatar_auteurs');
    //$art->addJoin( config\PREFIX.Apps\Article::TABLE, profil ON profil.page_id = (SELECT syspm.page_id FROM '.config\PREFIX.Apps\Page::TABLE.' AS syspm WHERE syspm.arborescence = CONCAT( page_membre.arborescence, "/profil_forum")
    //$art->addDonnee('profil.article_texte','contenu_profil_auteurs');
  }
}

