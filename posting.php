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

session_start();

use pok\Main,
    pok\Apps\Page,
    pok\Apps\Membre,
    pok\Apps\Article,
    pok\Apps\ArticleModif,
    pok\Apps\ArticleVuModif,
    pok\Apps\Outils\Cfg,
    pok\Apps\Outils\Session,
    pok\Apps\Outils\Base\Fichier,
    pok\Controleur\Page AS CPage,
    pok\Controleur\Droit AS CDroit,
    systems\cfg\general;

// Base du CMS
// La config "general" est automatiquement inclue
require('pok/Main.php');
// charge l'autoload
Main::autoLoad(array(
  ADRESSE_BASE
));

// on accepte pas les banni ici, ni les fauses informations
if( !Cfg::import('config') || Session::connecter() && $_SESSION['statut'] == Membre::BANNIE || !Session::verifieJeton(0) || !isset($_GET['page']) ) CPage::redirect('@revenir');

// informations
$page = Page::getByPageId($_GET['page']);
if( empty($page) )
  CPage::redirect('@revenir');

// Main
$load = new Main();

// connexion automatique avec cookie
Membre::cookieConnexion();
// contre attaque xss ou vol de session
if( !Session::fixeConnexion() )
  CPage::redirect('index.php?goto404');

// HACK
$plus = isset($_GET['plus'])? $_GET['plus'] : '';
$get = '';
$action = true; // exécute l'inscription de l'article

// définition des droits
$load->definiDroits($page['page_id']);

// variables infos :
define( 'POSTING_TEMPLATE', ADRESSE_TEMPLATES . '/' . $page['template'] . '/Controleur/posting.php' );

// Pour créer un article on doit :
// - Avoir un fichier posting.php dans le template
// - Pouvoir afficher le dossier
// - Pouvoir afficher les articles
// - Ne pas être banni
if( ( !file_exists(POSTING_TEMPLATE) || CDroit::$vlp === false ) && Session::connecter() && $_SESSION['statut'] !== Membre::ADMIN ) CPage::redirect('@revenir');

$article_slug_parent = '';
// Recherche de l'article parent
if( !empty($_GET['reference']) )
{
  $iarticle = Article::getByArticleId($_GET['reference']);
  if( !empty($iarticle) )
    $article_slug_parent = $iarticle['article_slug'];
  
  unset($iarticle);
}

// ------------------------------------------
// SI ON EDIT UN POST
if( Session::connecter() && isset($_GET['reference']) && !empty($_GET['article']) )
{
  $_WORK = 'edit';
  
  // récupère les infos
  $article = Article::getByArticleId($_GET['article']);
  if( empty($article) )
    CPage::redirect('@revenir');
  
  // require DES TRAITEMENTS DES POSTS
  include(POSTING_TEMPLATE);
  
  // SI ON A LE DROIT DE MODIFIER LE POST
  // SI ON EST ADMIN OU QU'ON A LES DROITS DE MODIFIER LES POSTS DU DOSSIER OU QU'ON EST L'AUTEUR
  if( CDroit::$etla || Session::connecter() && $_SESSION['statut'] === Membre::ADMIN || $article['article_auteur'] == $_SESSION['id'] )
  {
    // S'IL ON A PAS LE DROIT DE METTRE UN NIVEAU DE COMMENTAIRE, ON LE MET PAR DEFAULT
    if( $_SESSION['statut'] !== Membre::ADMIN && !CDroit::$blr )
      $_POST['niveau_comments'] = 0;
    
    // SI ON A LES DROITS DE MODIFIER LES DATES
    if( $_SESSION['statut'] !== Membre::ADMIN && !CDroit::$mda ) {
      $_POST['article_date_creer']   = date('Y-m-d H:i:s');
      $_POST['article_date_reviser'] = $_POST['article_date_creer'];
      $_POST['article_date_max']     = '0000-00-00 00:00:00';
    }
    // $action est un mot clé qui permet de bloquer la création ou la modification d'un article
    if( $action && $article['page_id'] == $page['page_id'] )
    {
      foreach( $_POST AS $champ => $valeur )
        $article[$champ] = $valeur;
      // met à jour l'article
      $article->modifier();
      // met à jour la catégorie
      if( !empty($_POST['categorie']) )
        ArticleModif::modifierCategorie( $article['article_id'], $_POST['categorie'] );
      // -- log
      Fichier::log('<ID:' . $_SESSION['id'] . '> edition article n°' . $article['article_id']);
    }
    if( function_exists( '__after_even' ) ) __after_even();
    
    $get .= '#p' . $article['article_id'];
  }
  // modifie le retour
  if( empty($_GET['reference']) )
    $article_slug_parent = $article['article_slug'];
  
  CPage::redirect(CPage::url( $page['arborescence'], $article_slug_parent, $plus.$get ));
}
// ------------------------------------------
// SI ON SUPPRIMER UN POST
elseif( Session::connecter() && !empty($_GET['article']) )
{
  $_WORK = 'delete';
  
  // récupère les infos
  $article = Article::getByArticleId($_GET['article']);
  if( empty($article) )
    CPage::redirect('@revenir');
  
  // require DES TRAITEMENTS DES POSTS
  include(POSTING_TEMPLATE);
  
  // $action est un mot clé qui permet de bloquer la création ou la modification d'un article
  if( $action && $article['page_id'] == $page['page_id'] && ( $_SESSION['statut'] === Membre::ADMIN || CDroit::$ssa && $article['article_auteur'] == $_SESSION['id'] || CDroit::$stla ) )
  {
    $article->supprimer();
    // -- log
    Fichier::log('<ID:' . $_SESSION['id'] . '> suppression article n°' . $article['article_id']);
  }
  CPage::redirect(CPage::url( $page['arborescence'], $article_slug_parent, $plus.$get ) );
}
// ------------------------------------------
// SINON ON ENVOIE UN NOUVEAU POST
elseif( isset($_GET['reference']) )
{
  // initialise
  $id_reference = intval($_GET['reference']);
  // ------------------------------------------
  // SI ON PEUT ECRIRE UN NOUVEAU POST EN REFERENCE 0, C'EST-A-DIRE UN SUJET
  if( $id_reference == 0 )
  {
    $_WORK = 'topic';
    // initialise
    $article = new ArticleModif();
    $article['page_id'] = $page['page_id'];
    // require DES TRAITEMENTS DES POSTS
    include(POSTING_TEMPLATE);
    
    // on vérifie les droits
    if( CDroit::$euna || Session::connecter() && $_SESSION['statut'] === Membre::ADMIN )
    {
      // S'IL ON A PAS LE DROIT DE METTRE UN NIVEAU DE COMMENTAIRE, ON LE MET PAR DEFAULT
      if( CDroit::$blr || !isset($_POST['niveau_comments']) )
        $_POST['niveau_comments'] = 0;
      
      // SI ON A LES DROITS DE MODIFIER LES DATES
      if( !CDroit::$mda ) {
        $_POST['article_date_creer']   = date('Y-m-d H:i:s');
        $_POST['article_date_reviser'] = $_POST['article_date_creer'];
        $_POST['article_date_max']     = '0000-00-00 00:00:00';
      }
      // Auteur
      if( Session::connecter() )
        $_POST['article_auteur'] = $_SESSION['id'];
      // $action est un mot clé qui permet de bloquer la création ou la modification d'un article
      if( $action )
      {
        foreach( $_POST AS $champ => $valeur )
          $article[$champ] = $valeur;
        // on créer l'article

        $id_new_post = $article->ajouter();
        // s'il n'y a pas d'erreur
        if( $id_new_post > 0 )
        {
          if( !empty($_POST['categorie']) )
          {
            $cat['relation_id'] = $id_new_post;
            $cat['cat_id'] = $_POST['categorie'];
            $cat['terms'] = 'article';
            $cat->ajouter();
          }
          // -- log
          Fichier::log( Session::connecter() ? '<ID:' . $_SESSION['id'] . '> creation article n°' . $id_new_post : '<IP:' . $_SERVER['REMOTE_ADDR'] . '> creation article n°' . $id_new_post );
          // Système lu / non-lu
          if( Session::connecter() && $page['nonlu'] )
          {
            // on indique qu'on la consulté et écrit
            $avu = new ArticleVuModif();
            $avu['av_membre_id']    = $_SESSION['id'];
            $avu['av_reference_id'] = $id_new_post;
            $avu['av_article_id']   = $id_new_post;
            $avu['av_poster']       = 1;
            $avu->setReplaceMode(true);
            $avu->ajouter();
            unset($avu);
          }
        }
        else  $get .= '&erreur';
      }
      if( function_exists( '__after_even' ) ) __after_even();
      
      CPage::redirect(CPage::url( $page['arborescence'], $article['article_slug'], $plus.$get ).'#p'.$id_new_post );
    }
    else
      CPage::redirect(CPage::url( $page['arborescence'], '', $plus.'&interdit' ) );
  }
  // ------------------------------------------
  // SINON SI ON REPOND A UN POST PRECEDENT
  elseif( $id_reference > 0 )
  {
    $_WORK = 'post';
    // initialise
    $article = new ArticleModif();
    $article['article_parent'] = $id_reference;
    $article['page_id'] = $page['page_id'];
    // require DES TRAITEMENTS DES POSTS
    include(POSTING_TEMPLATE);
    
    // on vérifie les droits
    if( CDroit::$raa || Session::connecter() && $_SESSION['statut'] === Membre::ADMIN )
    {
      if( !isset($_POST['niveau_comments']) )
        $_POST['niveau_comments'] = 0;
      // SI ON A LES DROITS DE MODIFIER LES DATES
      if( !CDroit::$mda ) {
        $_POST['article_date_creer']   = date('Y-m-d H:i:s');
        $_POST['article_date_reviser'] = $_POST['article_date_creer'];
        $_POST['article_date_max']     = '0000-00-00 00:00:00';
      }
      // Auteur
      if( Session::connecter() )
        $_POST['article_auteur'] = $_SESSION['id'];
      
      // $action est un mot clé qui permet de bloquer la création ou la modification d'un article
      if( $action )
      {
        foreach( $_POST AS $champ => $valeur )
          $article[$champ] = $valeur;
        
        // on créer un sous-article
        $id_new_post = $article->ajouter();
        
        if( $id_new_post > 0 )
        {
          // -- log
          Fichier::log( Session::connecter() ? '<ID:' . $_SESSION['id'] . '> creation sous-article n°' . $id_new_post . ', reference n°' . $id_reference : '<IP:' . $_SERVER['REMOTE_ADDR'] . '> creation sous-article n°' . $id_new_post . ', reference n°' . $id_reference );
          // Système lu / non-lu
          if( $page['nonlu'] )
          {
            // on indique qu'on la consulté et écrit
            $avu = new ArticleVuModif();
            $avu['av_membre_id']    = $_SESSION['id'];
            $avu['av_reference_id'] = $id_reference;
            $avu['av_article_id']   = $id_new_post;
            $avu['av_poster']       = 1;
            $avu->setReplaceMode(true);
            $avu->ajouter();
            unset($avu);
          }
        }
        else
          $get .= '&erreur';
      }
      if( function_exists( '__after_even' ) ) __after_even();
      
      CPage::redirect(CPage::url( $page['arborescence'], $article_slug_parent, $plus.$get ).'#p'.$id_new_post );
    }
    else
      CPage::redirect(CPage::url( $page['arborescence'], $article_slug_parent, $plus.'&interdit' ));
  }
  //SINON Y A UN TRUC QUI VA PAS !
  else
    CPage::redirect(CPage::url( $page['arborescence'], $article_slug_parent, $plus.'&traitement_incorrect' ));
}
else
  CPage::redirect(CPage::url( $page['arborescence'], $article_slug_parent, $plus.'&traitement_incorrect' ));
