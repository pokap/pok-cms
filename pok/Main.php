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

namespace pok;

// définition de constante global
//   Base du cms
define( 'ADRESSE_BASE', dirname(__DIR__) );
//   Fichiers uploads sur le serveur
define( 'ADRESSE_WEB', ADRESSE_BASE . '/web' );
//   Fichiers uploads sur le serveur
define( 'ADRESSE_FICHIERS', ADRESSE_WEB . '/fichiers' );
//   Base des processus du cms
define( 'ADRESSE_POK', ADRESSE_BASE . '/pok' );
//   Base des templates
define( 'ADRESSE_TEMPLATES', ADRESSE_BASE . '/templates' );
//   Contient tous les fichiers cache pour la gestion du cms
define( 'ADRESSE_SYSTEMS', ADRESSE_BASE . '/systems' );
//   Enregistre les personnes connecte
define( 'ADRESSE_ENLIGNE', ADRESSE_SYSTEMS . '/enligne' );
//   Contient les fichiers de configurations
define( 'ADRESSE_CFG', ADRESSE_SYSTEMS . '/cfg' );
//   Fichiers de logs du cms
define( 'ADRESSE_LOG', ADRESSE_SYSTEMS . '/logs' );
//   Fichiers de sauvegarde de la bdd
define( 'ADRESSE_SAVE', ADRESSE_SYSTEMS . '/save' );

// On utilise la fonction __autoload pour faire des chargements automatique des classes via la classe Loader
require_once( __DIR__ . '/Load/initialisation.php' );
// Configuration general
require_once( ADRESSE_CFG . '/general.php' );

use \pok\Apps\Models\Base\Requete;
use \systems\cfg;

/*
    Main :
  L'objet de base, il doit être toujours charger en debut d'une page php pour initialiser les informations
*/
final class Main
{
  // Objects :
  //   Class Template
  public $tpl;
  
  // Int
  //   nom du template
  private $page_id = 1;
  // String
  //   nom du template
  private $template = '';
  // Boolean :
  //   Active la gestion du droits
  private $droits_active = true;
  
  // Array :
  //   Liste des dossiers où rechercher les classes
  private static $liste_dossier_charger;
  
  // -------------------------------------
  // Void :
  //   @array $lists : la liste des répertoires
  //   Indiquer l'emplacement des fichiers à rechercher
  public static function autoLoad( array $lists )
  {
    // enregistre les repertoires de recherche
    self::$liste_dossier_charger = $lists;
    // enregistre la fonction comme __autoload()
    spl_autoload_register(__NAMESPACE__ .'\Main::setAutoLoad');
  }
  
  // -------------------------------------
  // Boolean :
  //   @string $nom : nom de la classe a charger
  //   Gestion de recherche des classes, appellez par l'autoload
  //   Utilise self::gestionLoader()
  public static function setAutoLoad( $nom )
  {
    if( !self::gestionLoader($nom) )
      throw new Exception('<b>pok\Main::gestionLoader</b> chemin "'.$nom.'" incorrect.');
  }
  
  // -------------------------------------
  // Boolean :
  //   @string $nom : nom de la classe à charger
  //   Gestion de recherche des classes, appellez par l'autoload
  public static function gestionLoader( $nom )
  {
    // refait le chemin :)
    $nom = str_replace( '\\', DIRECTORY_SEPARATOR, $nom );
    // pour chaque répertoire, recherche les classes
    foreach( self::$liste_dossier_charger AS $dirs )
    {
      if( file_exists( $dirs . DIRECTORY_SEPARATOR . $nom . '.php' ) )
      {
        require( $dirs . DIRECTORY_SEPARATOR . $nom . '.php' );
        return true;
      }
    }
    return false;
  }
  
  // -------------------------------------
  // Void :
  //   @boolean $valeur : valeur
  //   Initialise la variable "droits_active"
  public function setDroitsActive( $valeur ) {
    $this->droits_active = (boolean) $valeur;
  }
  
  // -------------------------------------
  // Void :
  //   @string $valeur : valeur
  //   Initialise la variable "template"
  public function setTemplate( $valeur ) {
    $this->template = (string) $valeur;
  }
  
  // -------------------------------------
  // Void :
  //   @int $valeur : valeur
  //   Initialise la variable "page_id"
  public function setPageId( $valeur ) {
    $this->page_id = (int) $valeur;
  }
  
  // -------------------------------------
  // Void :
  //   Récupère les droits du dossier courant
  public function definiDroits( $id_page )
  {
    $droit = new Apps\Droit();
    // ne pas faire plusieurs publication avec le même objet
    // récupère les droits
    if( Apps\Outils\Session::connecter() )
      $droits_utilisateur = $droit->publierPourMembreParPage($id_page);
    else
      $droits_utilisateur = $droit->publierPourVisiteurParPage($id_page);
    
    // plus besoin d'eux
    unset($droit, $droits_utilisateur['cat_id'], $droits_utilisateur['groupe_id']);
    // enregistre les données :
    // ensemble des droits par rapport à la catégorie du dossier courant
    foreach( array('vlp','euna','raa','blr','etla','stla','ssa','mda') AS $droit )
    {
      if( isset($droits_utilisateur[$droit]) ) {
        Controleur\Droit::${$droit} = (boolean) $droits_utilisateur[$droit];
      }
    }
  }

  // -------------------------------------
  // Void :
  //   Récupère tous les autres informations du dossier, affiche le template
  public function afficher()
  {
    // pour le chronometrage du temps d'exécution
    $chrono = microtime(true);
    // -------------------------------------
    // récupère l'arborescence du site
    // si l'arborescence n'est pas vide
    if( array_key_exists( 'page', $_GET ) && Apps\Page::format($_GET['page']) && $_GET['page'] != '' )
    {
      $clause = array();
      foreach( Apps\Page::getFilAriane($_GET['page']) AS $arboParent ) {
        $clause[] = array( '=', $arboParent );
      }
      $page = new Apps\Page(array(array( Apps\Page::TABLE.'.arborescence' => $clause )));
    }
    // sinon c'est qu'on est à la racine du site
    else {
      $page = new Apps\Page(array(array( Apps\Page::TABLE.'.page_id' => array( '=', $this->page_id, Requete\PDOFournie::NOT_QUOTE ) )));
    }
    // dans l'order croissant pour avoir du parent à l'enfant
    $page->addOrder(Apps\Page::TABLE.'.arborescence ASC');
    // récupére les pages dans "$pagess"
    $pagess = $page->publier();
    unset($page);
    // si aucune page trouvée
    if( empty($pagess) )
    {
      // si aucun template défini
      if( $this->template === '' ) {
        $this->setTemplate(cfg\general\TEMPLATE_404);
      }
    }
    else
    {
      // récupére la première page
      Controleur\Page::$mere = array_shift($pagess);
      // récupére la page actuel
      if( empty($pagess) )
        Controleur\Page::$actuelle = &Controleur\Page::$mere;
      else
        Controleur\Page::$actuelle = array_pop($pagess);
      // -------------------------------------
      // si la page n'existe pas
      if( array_key_exists( 'page', $_GET ) && Controleur\Page::$actuelle['arborescence'] != $_GET['page'] )
      {
        $this->droits_active = false;
        $this->setTemplate(cfg\general\TEMPLATE_404);
      }
      // -------------------------------------
      // récupére les droits du dossier
      if( $this->droits_active ) {
        $this->definiDroits(Controleur\Page::$actuelle['page_id']);
      }
      // -------------------------------------
      // sélection du template
      if( $this->template === '' )
      {
        if( !Controleur\Droit::$vlp || isset($_GET['goto404']) )
          $this->setTemplate(cfg\general\TEMPLATE_404);
        else
          $this->setTemplate(Controleur\Page::$actuelle['template']);
      }
      // -------------------------------------
      // Système lu / non-lu
      if( Controleur\Page::$actuelle['nonlu'] && !empty($_GET['article']) && Apps\Outils\Session::connecter() )
      {
        // sécurité pour la recherche de l'article à partir du slug
        Requete\PDOFournie::autoConnexion();
        $slugclause = Requete\Clause::critereClause( 'article_slug', '=', $_GET['article'] );
        Requete\PDOFournie::deconnexion();
        
        $article = new Apps\ArticleVuModif();
        $article['av_membre_id'] = $_SESSION['id'];
        $article['av_reference_id'] = array( '(SELECT article_id FROM ' . cfg\config\PREFIX . Apps\Article::TABLE . ' WHERE ' . $slugclause . ' LIMIT 1)', Requete\PDOFournie::NOT_QUOTE );
        $article->ajouter();
        unset($article);
      }
    }
    // on inclue la class
    $controleur_template = '\templates\\'.$this->template.'\Controleur';
    $controleur = new $controleur_template();
    // la méthode "__invoke" doit exister sinon on revoit une exception
    if( !method_exists( $controleur, '__invoke' ) )
      throw new \pok\Exception('La méthode "__invoke" n\'existe pas dans le controleur <b>\templates\\'.$this->template.'\Controleur</b>.');
    else
    {
      // Initialise le template pour le controleur
      $tpl = new Template();
      $controleur->setTemplate($tpl);
      
      $tpl->assign( 'arborescence', $pagess );
      unset($pagess);
      
      // -------------------------------------
      //  RECUPERATION DES INFO FORMULAIRE
      if( array_key_exists( 'form', $_SESSION ) )
      {
        if( array_key_exists( 'rep', $_SESSION['form'] ) )
          $tpl->assign( 'form', $_SESSION['form']['rep'] );
        
        if( array_key_exists( 'erreur', $_SESSION['form'] ) )
          Apps\Formulaire::$erreur_liste = $_SESSION['form']['erreur'];
        
        unset($_SESSION['form']);
      }
      // chronomêtre
      $tpl->assign( 'chrono', $chrono );
      // Exécute le controleur du template juste avant de l'afficher
      $controleur();
      // on lance de template
      $tpl->parse($this->template);
    }
  }
}
