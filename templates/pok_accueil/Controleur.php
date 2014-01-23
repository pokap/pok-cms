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

namespace templates\pok_accueil;

use \pok\Apps;
use \pok\Controleur\Page;
use \pok\Apps\Models\Base\Requete\PDOFournie;

class Controleur extends \pok\Controleur
{
  private $article_id = 0;
  
  // ------------------------------------------
  // invoquer, est toujours appeller et doit toujours exister
  public function __invoke()
  {
    // si on présise pas l'id du post
    if( !isset($_GET['article']) || $_GET['article'] === '' ) {
      $this->articles();
    }
    else
    {
      $this->simple_article();
      $this->commentaires();
    }
    
    $this->assign( 'numpage', isset($_GET['numpage'])? (int) $_GET['numpage'] : 1 );
  }

  // ------------------------------------------
  // tout les posts de la page
  private function articles()
  {
    $articles = new Apps\Article(array(
      array(
        'article.article_parent'       => array( '=', 0 ),
        'page.page_id'                 => array( '=', Page::$actuelle['page_id'], PDOFournie::NOT_QUOTE ),
        'article.article_date_reviser' => array( '<=', 'NOW()', PDOFournie::NOT_QUOTE ),
        'article.article_date_max'     => array( array( '>=', 'NOW()', PDOFournie::NOT_QUOTE ), array( 'IS NULL', null ) )
      )
    )); // ne pas oublier l'alias pour la limit
    $articles->setAlias('numpage');
    $articles->addOrder('!id_post');
    $articles->limit(10);
    
    $this->assign( 'all_article', $articles->publier() );
    $this->assign( 'nb_article', $articles->count() );
  }

  // ------------------------------------------
  // on regarde un seul post en particulier
  private function simple_article()
  {
    $article = new Apps\Article(array(
      array(
        'article.article_parent'       => array( '=', 0 ),
        'page.page_id'                 => array( '=', Page::$actuelle['page_id'], PDOFournie::NOT_QUOTE ),
        'article.article_date_reviser' => array( '<=', 'NOW()', PDOFournie::NOT_QUOTE ),
        'article.article_date_max'     => array( array( '>=', 'NOW()', PDOFournie::NOT_QUOTE ), array( 'IS NULL', null ) ),
        'article.article_slug'         => array( '=', $_GET['article'] )
      )
    ));
    $article->limit(1);
    $single_article = $article->publier();
    
    $this->article_id = $single_article[0]['article_id'];
    
    $this->assign( 'single_article', $single_article[0] );
    // on charge la liste des templates
    $this->assign( 'formulaire', Apps\FormulaireQuestion::fetchByArticleId($this->article_id) );
  }
  
  // ------------------------------------------
  // les commentaires du posts en particulier
  private function commentaires()
  {
    $commentaires = new Apps\Article(array(array( 'article.article_parent' => array( '=', $this->article_id ) )));
    $commentaires->setAlias('numpage');
    $commentaires->limit(20);
    
    $this->assign( 'all_commentaire', $commentaires->publier() );
  }
}
