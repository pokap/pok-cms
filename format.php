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
    pok\Apps\Article,
    pok\Apps\Membre,
    pok\Apps\Formulaire,
    pok\Apps\FormulaireQuestion,
    pok\Apps\FormulaireReponseModif,
    pok\Apps\Outils\Cfg,
    pok\Apps\Outils\Session,
    pok\Apps\Outils\Base\Fichier,
    pok\Controleur\Page AS CPage,
    pok\Controleur\Droit AS CDroit;

// Base du CMS
// La config "general" est automatiquement inclue
require('pok/Main.php');
// charge l'autoload
Main::autoLoad(array(
  ADRESSE_BASE
));

if( !Cfg::import('config') )
  CPage::redirect('@revenir');

// Main
$load = new Main();

// connexion automatique avec cookie
Membre::cookieConnexion();
// contre attaque xss ou vol de session
if( !Session::fixeConnexion() )
  CPage::redirect('./index.php?template_acces_interdit_view');

// hack
$id_article = !empty($_GET['article'])? intval($_GET['article']) : 0;
$plus = isset($_GET['plus']) ? $_GET['plus'] : '';
$get = '';
$action = true;

// récupére l'info
$page = Page::getByPageId($_GET['page']);
if( empty($page) )
  CPage::redirect('@revenir');

// Information du les droits de l'utilisateur sur le dossier
$load->definiDroits($page['page_id']);

// variables infos :
define( 'FORM_TEMPLATE', ADRESSE_TEMPLATES . '/' . $page['template'] . '/Controleur/form.php' );

// pour enregistrer les données du formulaire (dans le cas où il se trompe)
//$_SESSION['form']['valeur'] = $_POST;

// Pour envoyer un formulaire on doit :
// - Avoir un fichier form.php dans le template
// - Pouvoir afficher le dossier
// - Ne pas être banni
if( !file_exists(FORM_TEMPLATE) || !CDroit::$vlp || !Session::connecter() || Session::connecter() && $_SESSION['statut'] == Membre::BANNIE || $id_article <= 0 || !Session::verifieJeton(0) ) {
  CPage::redirect('@revenir');
}

// initialise
$erreurs = array();
$type_erreur = true;
$article = Article::getByArticleId($id_article);
if( empty($article) )
  CPage::redirect('@revenir');

// on regarde si on a une erreur dans chaque champ
foreach( FormulaireQuestion::fetchByArticleId($id_article) AS $forms )
{
  $type_erreur = Formulaire::verifIssetForm($forms);
  
  if( $type_erreur !== true )
    $erreurs[$forms['fq_label']] = $type_erreur;
}
// s'il y a eu des erreus
if( $erreurs > array() )
{
  // on renvoie les données
  $_SESSION['form']['rep'] = $_POST;
  $_SESSION['form']['erreur'] = $erreurs;
  
  CPage::redirect(CPage::url( $page['arborescence'], $article['article_slug'], $plus.'&format_erreur' ));
}
// on ajoute le controller de formulaire du template
require(FORM_TEMPLATE);

// si on ne change pas
if( $action )
{
  $formulaire = new FormulaireReponseModif(array(
    'article_id' => $id_article,
    'membre_id'  => $_SESSION['id'],
    'fr_value'   => serialize($_POST),
    'fr_date'    => array( 'NOW()', 'not_quote' )
  ));
  $formulaire->setReplaceMode(true);
  $formulaire->ajouter();
  
  // s'il n'y a aucune erreur
  Fichier::log('<ID:' . $_SESSION['id'].'> reponse formulaire n°' . $id_article);
  
  CPage::redirect(CPage::url( $page['arborescence'], $article['article_slug'], $plus.$get.'&format_ok' ));
}
else
  CPage::redirect(CPage::url( $page['arborescence'], $article['article_slug'], $plus.'&form_erreur' ));
