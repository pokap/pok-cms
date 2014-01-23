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

namespace templates\pok_rss;

use pok\Apps\Article,
    pok\Controleur\Page AS CPage;

class Controleur extends \pok\Controleur
{
  // ------------------------------------------
  // invoquer, est toujours appeller et doit toujours exister
  public function __invoke()
  {
    $mSql = new Article(array(array(
      'article.article_parent' => array( '=', 0 ),
      'article.page_id'        => array( '=', CPage::$mere['page_id'] ),
      'article.article_date_creer' => array( '<=', 'NOW()', 'not_quote' ),
      'article.article_date_max'   => array( array( 'IS NULL', null ), array( '>=', 'NOW()', 'not_quote' ) )
    )));
    $mSql->limit(10);
    $mSql->addOrder('!id_post');
    // la liste des 10 derniers articles
    $this->assign( 'articles', $mSql->publier() );
  }
}
