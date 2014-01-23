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

use pok\Newsletter,
    pok\Apps\Page,
    pok\Apps\ArticleModif,
    pok\Apps\ArticleVuModif,
    pok\Apps\Categorie,
    pok\Apps\CatRelation,
    pok\Apps\CatRelationModif,
    pok\Apps\Outils\Session,
    pok\Apps\Outils\Cfg,
    pok\Apps\Outils\Base\Fichier,
    pok\Controleur\Page AS CPage,
    systems\cfg\general,
    systems\cfg\newsletter AS cfg_newsletter;

Cfg::import('general');

if( Session::verifieJeton(0) && isset($_GET['d'], $_GET['r']) )
{
  // ID reference
  $id_ref = intval($_GET['r']);
  // encodage automatique
  if( $_POST['encode'] != 'html' )
  {
    $_POST['chapo'] = nl2br(htmlspecialchars( $_POST['chapo'], ENT_NOQUOTES ));
    $_POST['text'] = nl2br(htmlspecialchars( $_POST['text'], ENT_NOQUOTES ));
  }
  // si on edit
  if( !empty($_GET['article']) )
  {
    $modif = array(
      'article_id'         => $_GET['article'],
      'cat_id'             => $_POST['cat'],
      'tags'               => $_POST['tag'],
      'article_titre'      => $_POST['titre'],
      'article_date_creer' => $_POST['date'],
      'article_date_max'   => $_POST['date_max'],
      'article_chapo'      => $_POST['chapo'],
      'article_texte'      => $_POST['text'],
      'brouillon'          => isset($_POST['brouillon'])? true : false,
      'niveau_comments'    => $_POST['niveau_comments']
    );
    if( !ArticleModif::autoModification($modif) )
    {
      $_SESSION['article'] = $modif;
      CPage::redirect('admin/article.php?modif&p=' . $_GET['article'] . '&erreur=edit');
    }
    else
    {
      Fichier::log('<ID:' . $_SESSION['id'] . '> edition article n°' . $_GET['article']);
      CPage::redirect('admin/article.php?page=' . $_GET['d'] . '&cat=');
    }
  }
  else
  {
    $modif = array(
      'page_id'            => $_GET['d'],
      'article_parent'     => $id_ref,
      'article_titre'      => $_POST['titre'],
      'article_auteur'     => $_SESSION['id'],
      'article_date_creer' => $_POST['date'],
      'article_date_reviser' => $_POST['date'],
      'article_date_max'   => $_POST['date_max'],
      'article_chapo'      => $_POST['chapo'],
      'article_texte'      => $_POST['text'],
      'brouillon'          => isset($_POST['brouillon'])? 1 : 0,
      'niveau_comments'    => $_POST['niveau_comments']
    );
    $article = new ArticleModif($modif);
    $id_new_post = $article->ajouter();
    // si c'est un sous-article
    if( $id_ref > 0 )
    {
      if( $id_new_post > 0 )
      {
        Fichier::log('<ID:' . $_SESSION['id'] . '> creation sous-article n°' . $id_new_post . ', reference n°' . $id_ref);
        // Class Dossiers
        // Recherche des données de la page
        $info_page = Page::getByPageId($_GET['d']);
        // Système lu / non-lu
        if( !empty($info_page) && $info_page['nonlu'] )
        {
          // on indique qu'on la consulté et écrit
          $vu = new ArticleVuModif(array(
            'av_membre_id'    => $_SESSION['id'],
            'av_reference_id' => $id_ref,
            'av_article_id'   => $_GET['article'],
            'av_poster'       => 1
          ));
          $vu->setReplaceMode(true);
          $vu->ajouter();
        }
        CPage::redirect('admin/article.php?page='.$_GET['d'].'&cat=&ref='.$id_ref);
      }
      else
      {
        $_SESSION['article'] = $modif;
        CPage::redirect('admin/article.php?new&page='.$_GET['d'].'&cat=&ref='.$id_ref.'&erreur=souscreer');
      }
    }
    else
    {
      // s'il n'y a pas d'erreur
      if( $id_new_post > 0 )
      {
        Fichier::log('<ID:' . $_SESSION['id'] . '> creation article n°' . $id_new_post);
        // on ajoute les tags de l'article
        // ajoute les tags
        $cat = new CatRelationModif();
        if( isset($_POST['tag']) && is_array($_POST['tag']) )
        {
          foreach( $_POST['tag'] AS $tag_id )
          {
            $cat['relation_id'] = $id_new_post;
            $cat['cat_id'] = $tag_id;
            $cat['terms'] = 'article';
            $cat->ajouter();
          }
        }
        if( !empty($_POST['cat']) )
        {
          $cat['relation_id'] = $id_new_post;
          $cat['cat_id'] = $_POST['cat'];
          $cat['terms'] = 'article';
          $cat->ajouter();
        }
        // Class Dossiers
        // Recherche des données du dossier
        $info_page = Page::getByPageId($_GET['d']);
        // Système lu / non-lu
        if( !empty($info_page) )
        {
          if( $info_page['nonlu'] )
          {
            // on indique qu'on la consulté et écrit
            $vu = new ArticleVuModif(array(
              'av_membre_id'    => $_SESSION['id'],
              'av_reference_id' => 0,
              'av_article_id'   => $id_new_post,
              'av_poster'       => 1
            ));
            $vu->setReplaceMode(true);
            $vu->ajouter();
          }
          // on envoie la newsletter s'il est active
          if( general\NEWSLETTER )
          {
            // configuration newsletter
            Cfg::import('newsletter');
            // et si c'est le même dossier
            Newsletter::send( $info_page['page_id'], true );
            Fichier::log('newsletter create');
          }
        }
        CPage::redirect('admin/article.php?page='.$_GET['d'].'&cat=');
      }
      else
      {
        $_SESSION['article'] = $modif;
        CPage::redirect('admin/article.php?new&page='.$_GET['d'].'&cat=&erreur=creer');
      }
    }
  }
}
CPage::redirect('@revenir');
