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
require('templates/init.php');

use pok\Apps\Page;
use pok\Apps\Categorie;
use pok\Apps\CatRelation;
use pok\Apps\Article;

// initialise
$preselected_page = isset($_GET['page']) ? $_GET['page'] : 1;
$preselected_cat = isset($_GET['cat']) ? $_GET['cat'] : 0;
$reference = isset($_GET['ref']) ? intval($_GET['ref']) : 0;

// REQUETE
$catSql = new Categorie(array(array(
  'categorie.taxon' => array( array( '=', Categorie::TAXON_BOTH ), array( '=', Categorie::TAXON_ARTICLE ) )
)));
$select_cat = $catSql->publier();

// ON CREER UN NOUVELLE ARTICLE
if( isset($_GET['new']) )
{
  // initialise les donnees par default de la rédaction d'article
  $tag = array();
  $donnees_article = array(
    'cat_id'              => $preselected_cat,
    'article_titre'       => '',
    'article_date_creer'  => date('Y-m-d H:i:s'),
    'article_date_max'    => '',
    'article_chapo'       => '',
    'article_texte'       => '',
    'niveau_comments'     => 0,
    'brouillon'           => null
  );
  // si un erreur c'est produite, on reprend les données qu'on avait inscrite
  if( isset($_SESSION['article']) )
  {
    foreach( $donnees_article AS $cle => $donnee ) {
      if( isset($_SESSION['article'][$cle]) && $_SESSION['article'][$cle] != $donnee ) {
        $donnees_article[$cle] = $_SESSION['article'][$cle];
      }
    }
    unset($_SESSION['article']);
  }
  // REQUETE
  $tagSql = new Categorie(array(array( 'categorie.taxon' => array( '=', Categorie::TAXON_TAG ) )));
  
  admin\templates\Pages::parse( 'article', array(
    'donnees_article' => &$donnees_article,
    'tag'             => &$tag,
    'select_tag'      => $tagSql->publier(),
    'reference'       => &$reference,
    'select_cat'      => &$select_cat,
    'preselected_dossier' => $preselected_page,
    'preselected_cat' => $preselected_cat
  ), 'creer');
}
// ON MODIFIE UN ARTICLE
elseif( isset($_GET['modif']) )
{
  $article = array();
  $tag = array();
  
  // si un erreur c'est produite, on reprend les données qu'on avait inscrite
  if( isset($_SESSION['article']) )
  {
    $article[0] = array(
      'cat_id'              => $preselected_cat,
      'article_titre'       => '',
      'article_date_creer'  => date('Y-m-d H:i:s'),
      'article_date_max'    => '',
      'article_chapo'       => '',
      'article_texte'       => '',
      'niveau_comments'     => 0,
      'brouillon'           => null
    );
    
    foreach( $article[0] AS $cle => $donnee ) {
      if( isset($_SESSION['article'][$cle]) && $_SESSION['article'][$cle] != $donnee ) {
        $article[0][$cle] = $_SESSION['article'][$cle];
      }
    }
    unset($_SESSION['article']);
  }
  else
  {
    $articleSql = new Article(array(array(
      'article.article_id' => array( '=', $_GET['article'] )
    )));
    $articleSql->setLimit(1);
    $article = $articleSql->publier();
    
    // REQUETE
    $catSql = new CatRelation(array(array(
      'cat_relation.relation_id' => array( '=', $_GET['article'] ),
      'cat_relation.terms'       => array( '=', 'article' ),
      'categorie.taxon'          => array( '=', Categorie::TAXON_TAG )
    )));
    foreach( $catSql->publier() AS $tagi ) {
      $tag[] = $tagi['cat_id'];
    }
  }
  // REQUETE
  $tagSql = new Categorie(array(array( 'categorie.taxon' => array( '=', Categorie::TAXON_TAG ) )));
  
  $html_active = preg_match('`\<[a-zA-Z_-\s="]+\>`', $article[0]['article_chapo']) || preg_match('`\<[a-zA-Z_-\s="]+\>`', $article[0]['article_texte']);
  
  if( !$html_active ) {
    $article[0]['article_texte'] = pok\Texte::br2nl($article[0]['article_texte']);
    $article[0]['article_chapo'] = pok\Texte::br2nl($article[0]['article_chapo']);
  }
  
  admin\templates\Pages::parse( 'article', array(
    'article'     => &$article[0],
    'tag'         => &$tag,
    'select_tag'  => $tagSql->publier(),
    'reference'   => &$reference,
    'html_active' => &$html_active,
    'select_cat'  => &$select_cat,
    'preselected_dossier' => $preselected_page,
    'preselected_cat' => $preselected_cat
  ), 'modif');
}
else
{
  $clause = array(
    'article.article_parent' => array( '=', $reference ),
    'page.page_id'           => array( '=', $preselected_page ),
    'extrait'                => 50
  );
  if( $preselected_cat > 0 )
    $clause['categorie.cat_id'] = array( '=', $preselected_cat );
  // la requete
  $article = new Article(array($clause));
  $article->limit( 0, 30 );
  $article->setAlias('numpagearticle');
  
  // url de la page
  $url_page  = $reference > 0 ? 'ref=' . $reference . '&amp;' : '';
  $url_page .= 'page=' . $preselected_page . '&amp;cat=' . $preselected_cat . '&amp;';
  
  admin\templates\Pages::parse( 'article', array(
    'articles'   => $article->publier(),
    'nb_article' => $article->count(),
    'url_page'   => $url_page,
    'preselected_dossier' => $preselected_page,
    'preselected_cat'     => $preselected_cat,
    'select_arbo'   => Page::publierListeFilter($preselected_page),
    'select_cat'    => $select_cat,
    'reference'     => $reference
  ), 'list');
}
