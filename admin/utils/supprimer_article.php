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

// Base pour le CMS
require('../templates/init.php');

use pok\Apps\ArticleModif,
    pok\Apps\ArticleVuModif,
    pok\Apps\CatRelationModif,
    pok\Apps\Outils\Session,
    pok\Apps\Outils\Base\Fichier,
    pok\Controleur\Page AS CPage;

if( Session::verifieJeton(0) && !empty($_GET['article']) )
{
  $id_article = intval($_GET['article']);
  // supprime l'article
  $article = new ArticleModif(array( 'article_id' => $id_article ));
  $article->supprimer();
  // supprime les relations avec les categories
  $catrelation = new CatRelationModif(array(
    'relation_id' => $id_article,
    'terms'       => 'article'
  ));
  $catrelation->supprimer();
  // supprimes les articles vu
  $articlevu = new ArticleVuModif();
  $articlevu->addWhere('av_article_id = '.$id_article.' OR av_reference_id = '.$id_article);
  $articlevu->supprimer();
  
  Fichier::log('<ID:' . $_SESSION['id'] . '> supprime l\'article n°' . $id_article);
}
CPage::redirect('@revenir');
?>
