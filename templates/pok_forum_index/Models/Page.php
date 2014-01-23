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

namespace templates\pok_forum_index\Models;

use \pok\Apps,
    \pok\Apps\Outils\Session,
    \systems\cfg\config;

class Page extends Apps\Page
{
  // -------------------------------------
  // Array :
  //   La requete sql
  public function publier()
  {
    // à completer
    // on cherche le nombre de topic du forum
    $this->addJoin(config\PREFIX.Apps\Article::TABLE, 'premier', 'premier.page_id = page.page_id AND premier.article_parent = 0');
    $this->addDonnee('COUNT( DISTINCT premier.article_id ) AS count_post_first');
    // on cherche le nombre de post du forum
    $this->addDonnee('SUM( DISTINCT premier.count ) AS count_post_second');
    // informations des derniers articles
    $this->addJoin(config\PREFIX.Apps\Article::TABLE, 'post', 'post.article_id = (SELECT postl.article_id FROM '.config\PREFIX.Apps\Article::TABLE.' AS postl WHERE postl.page_id = page.page_id ORDER BY postl.article_id DESC LIMIT 1)');
    $this->addDonnee('post.article_date_creer AS last_date_creer');
    $this->addDonnee('post.article_parent AS last_parent');
    $this->addDonnee('post.article_id AS last_id');
    $this->addDonnee('post.article_slug AS last_slug');
    $this->addDonnee('post.article_titre AS last_titre');
    // informations de l'article parent des derniers articles
    $this->addJoin(config\PREFIX.Apps\Article::TABLE, 'postparent', 'postparent.article_id = post.article_parent');
    $this->addDonnee('post.article_slug AS last_parent_slug');
    // informations sur les membres des derniers articles
    $this->addJoin(config\PREFIX.Apps\Membre::TABLE, 'user', 'user.membre_id = post.article_auteur');
    $this->addJoin(config\PREFIX.Apps\Page::TABLE, 'userpage', 'userpage.page_id = user.page_id');
    $this->addDonnee('userpage.arborescence AS last_arbo_auteur');
    $this->addDonnee('user.membre_pseudo AS last_nom_auteur');
    // informations sur les groupes des membres des derniers articles
    $this->addJoin(config\PREFIX.Apps\MembreGroupe::TABLE, 'mg', 'mg.membre_id = user.membre_id AND mg.principal = "1"');
    $this->addJoin(config\PREFIX.Apps\Groupe::TABLE, 'gg', 'gg.groupe_id = mg.groupe_id');
    $this->addDonnee('gg.couleur AS last_couleur');
    // nombre de topic non lu
    if( Session::connecter() )
    {
      // pour retrouver le nombre de topic non lu
      $this->addJoin(config\PREFIX.Apps\ArticleVu::TABLE, 'pv', 'pv.av_membre_id = ' . $_SESSION['id']);
      $this->addJoin(config\PREFIX.Apps\Article::TABLE, 'ppv', 'pv.av_reference_id = ppv.article_id AND pv.av_article_id < (SELECT MAX(ppvt.article_id) FROM '.config\PREFIX.Apps\Article::TABLE.' AS ppvt WHERE ppvt.article_id = ppv.article_parent) AND ppv.page_id = page.page_id');
      $this->addDonnee('COUNT(ppv.article_id) AS nb_topic_non_lu');
    }
    else
      $this->addDonnee('0 AS nb_topic_non_lu');
    
    // récupère les informations de la base de donnée
    $donnees = parent::publier();
    
    return $donnees;
  }
}

