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

use pok\Apps\Membre,
    pok\Apps\Outils\Cfg,
    pok\Apps\Outils\Base\Fichier;

// -------------------------------------
// Array :
//   Convertie un objet SimpleXml en Array
function simplexml2array( $xml )
{
  // convertie l'objet en array, si l'objet est vide on le convertie en chaîne vide
  $xml = empty($xml) ? '' : (array) $xml;
  // si l'objet convertie avait des informations on relance la fonction
  if( is_array( $xml ) )
    foreach( $xml AS &$toarray )
      if( is_object($toarray) || is_array($toarray) )
        $toarray = simplexml2array( $toarray );

  return $xml;
}

// includes des fonctions
require(__DIR__ . '/up_bdd.php');

// initialisation
$erreurs = 0;

// traitement du formulaire de l'étape 1
if( isset( $_POST['etape1'] ) )
{
  // connexion serveur sql
  $link = @mysql_connect( $_POST['server'], $_POST['user'], $_POST['password'] );
  if( $link )
  {
    // connexion bdd sql
    $db_selected = mysql_select_db( $_POST['bdd'], $link );
    if( $db_selected )
    {
      $_SESSION['server_sql'] = $_POST['server'];
      $_SESSION['user_sql'] = $_POST['user'];
      $_SESSION['password_sql'] = $_POST['password'];
      $_SESSION['bdd_sql'] = $_POST['bdd'];
      $_SESSION['prefix'] = $_POST['prefix'];
    }
    else {
      $erreurs = 102; // connexion bdd mysql impossible
    }
    // déconnexion sql
    mysql_close($link);
  }
  else {
    $erreurs = 101; // connexion mysql impossible
  }
}
elseif( isset($_POST['etape2']) )
{
  if( !empty($_POST['pseudo']) && !empty($_POST['mdp']) && !empty($_POST['server_name']) &&  !empty($_POST['server_path']) )
  {
    $_SESSION['pseudo_admin'] = htmlspecialchars( $_POST['pseudo'], ENT_QUOTES );
    $_SESSION['mdp_admin'] = Membre::script( $_POST['mdp'], $_POST['prefix_salt'], $_POST['suffix_salt'], $_POST['crypt'] );
    $_SESSION['server_name'] = $_POST['server_name'];
    $_SESSION['server_path'] = $_POST['server_path'];
    $_SESSION['prefix_salt'] = $_POST['prefix_salt'];
    $_SESSION['suffix_salt'] = $_POST['suffix_salt'];
    $_SESSION['crypt'] = $_POST['crypt'];
  }
  else
    $erreurs = 201; // champ user oublier
}

// etape 2
if( isset($_SESSION['server_sql']) && !isset($_SESSION['pseudo_admin']) )
{
  // définition
  $num_etape = 2;

  if( isset($_POST['etape2']) )
  {
    $pseudo = $_POST['pseudo'];
    $mdp = $_POST['mdp'];
    $server_name = $_POST['server_name'];
    $server_path = $_POST['server_path'];
    $prefix_salt = $_POST['prefix_salt'];
    $suffix_salt = $_POST['suffix_salt'];
    $crypt = $_POST['crypt'];
  }
  else
  {
    // initialise
    $pseudo = $mdp = '';
    $server_name = $_SERVER['SERVER_NAME'];
    $server_path = $_SERVER['REQUEST_URI'];
    $prefix_salt = pok\Texte::generateur();
    $suffix_salt = pok\Texte::generateur();
    $crypt = '$1$'.pok\Texte::generateur(6).'$';
  }
  
  include(__DIR__.'/tpl/etape_2.php');
}
// etape récupitulatif
elseif( isset($_SESSION['server_sql']) && isset($_SESSION['pseudo_admin']) )
{
  // définition
  $num_etape = 3;

  $norme = array(
    'article' => array('article_id','page_id','article_date_creer','article_date_reviser','article_date_max','article_auteur','article_chapo','article_texte','article_titre','article_slug','article_niveau','brouillon','niveau_comments','article_parent','count'),
    'article_prive' => array('prive_article_id','prive_membre_id'),
    'article_vu' => array('av_membre_id','av_reference_id','av_article_id','av_poster'),
    'categorie' => array('cat_id','cat_nom','taxon'),
    'cat_relation' => array('relation_id','cat_id','terms'),
    'droit' => array('cat_id','groupe_id','vlp','euna','raa','blr','etla','stla','ssa','mda'),
    'fichier' => array('fichier_id','fichier_nom','poids','fichier_description','extension','telecharger'),
    'formulaire_question' => array('fq_id','article_id','fq_inputype','fq_ordre','fq_label','fq_texte','fq_option'),
    'formulaire_reponse' => array('fr_id','article_id','membre_id','fr_value','fr_date'),
    'groupe' => array('groupe_id','page_id','couleur'),
    'membre' => array('membre_id','membre_pseudo','page_id','membre_mdp','membre_email','membre_inscrit','membre_visite','statut','cle','valide'),
    'membre_groupe' => array('membre_id','groupe_id','principal'),
    'membre_newsletter' => array('membre_id','newsletter_id'),
    'newsletter' => array('newsletter_id','page_id','newsletter_auto','newsletter_titre'),
    'page' => array('page_id','page_ordre','page_nom','page_description','template','nonlu','arborescence')
  );
  $strscs_pseudo_admin = pok\Texte::slcs($_SESSION['pseudo_admin']);
  $xml = simplexml_load_file(__DIR__.'/install.xml');
  $insertbdd = simplexml2array($xml);
  // supprime les commentaires
  if( isset($insertbdd['comment']) ) unset($insertbdd['comment']);
  // vérifie la synthaxe du document
  foreach( $norme AS $table => $champ ) {
    // si la table est spécifier dans le document
    if( isset($insertbdd[$table]) ) {
      // si elle contient plusieurs données
      if( isset($insertbdd[$table][0]) ) {
        // on fouille chaque donnnées
        foreach( $insertbdd[$table] AS $cle => $insert )  {
          // on vérifie chaque champ de la donnée
          foreach( $champ AS $valeur )
          {
            if( !isset($insert[$valeur]) )
              die('<h1>Erreur syntaxe xml</h1><p>La donnees <b>'.$valeur.'</b> est manquante dans la table <b>'.$table.'</b> numéro <b>'.$cle.'</b></p>');
            // remplace les informations
            if( is_string($insert[$valeur]) )
              $insertbdd[$table][$cle][$valeur] = str_replace( array('%MDP_ADMIN%','%PSEUDO_ADMIN%','%STRSCS_PSEUDO_ADMIN%'), array($_SESSION['mdp_admin'],$_SESSION['pseudo_admin'],$strscs_pseudo_admin), $insert[$valeur] );
          }
        }
      }
      // s'il n'y a qu'un donnée
      else {
        // on vérifie chaque champ de la donnée
        foreach( $champ AS $valeur )
        {
          if( !isset($insertbdd[$table][$valeur]) )
            die('<h1>Erreur syntaxe xml</h1><p>La donnees </b>'.$valeur.'</b> est manquante dans la table <b>'.$table.'</b></p>');
          // remplace les informations
          if( is_string($insertbdd[$table][$valeur]) )
            $insertbdd[$table][$valeur] = str_replace( array('%MDP_ADMIN%','%PSEUDO_ADMIN%','%STRSCS_PSEUDO_ADMIN%'), array($_SESSION['mdp_admin'],$_SESSION['pseudo_admin'],$strscs_pseudo_admin), $insertbdd[$table][$valeur] );
        }
        // on refait la donnée pour quel resemble au autre
        $insertbdd[$table] = array($insertbdd[$table]);
      }
    }
  }
  // définition des vaiables
  $installer = install_bdd($insertbdd);
  $code_config = '';

  if( $installer )
  {
    @chmod( ADRESSE_CFG, 0755 );
    
    $code_in_config = <<<END
<?php
namespace systems\cfg\config;
const PARAM_SERVEUR = '$_SESSION[server_sql]'; // Adresse serveur de la base de donnee
const PARAM_USER = '$_SESSION[user_sql]'; // Utilisateur de la base de donnee
const PARAM_PASSWORD = '$_SESSION[password_sql]'; // Mot de passe de la base de donnee
const PARAM_BDD = '$_SESSION[bdd_sql]'; // Nom de la base de donnee
const PREFIX = '$_SESSION[prefix]'; // Prefixe des tables de la base de donnee
const PREFIX_SALT = '$_SESSION[prefix_salt]'; // Prefixe de sécurité d'authentification
const SUFFIX_SALT = '$_SESSION[suffix_salt]'; // Suffixe de sécurité d'authentification
const CRYPT = '$_SESSION[crypt]'; // Crypt pour les mot de passe de membre
END;
    // si on enregistre directement sur le serveur
    // si ça n'a pas fonctionner, on envoie le code à afficher
    if( !Fichier::nouveau( ADRESSE_CFG . '/config.php', $code_in_config ) ) {
      $code_config = nl2br(htmlspecialchars($code_in_config));
    }
    
    // on ouvre le cfg
    $cfg = new Cfg('general');
    $cfg->ouvre();
    // on modifie les infos
    $cfg->remplace( 'DOMAINE', $_SESSION['server_name'] );
    $cfg->remplace( 'PATH', $_SESSION['server_path'] );
    // ferme et enregistre
    $cfg->sauvegarde();
  }

  include(__DIR__.'/tpl/etape_3.php');
}
// etape 1
else
{
  // définition
  $num_etape = 1;

  if( isset($_POST['etape1']) )
  {
    $server = $_POST['server'];
    $user = $_POST['user'];
    $password = $_POST['password'];
    $bdd = $_POST['bdd'];
    $prefix = $_POST['prefix'];
  }
  else
  {
    // initialise
    $server = $user = $password = $bdd = '';
    $prefix = 'pok_';
  }

  $extension_necessaire = array('PDO','SimpleXML');
  $extension_non_installer = array();
  // test les extensions
  foreach( $extension_necessaire AS $extension ) {
    if( !extension_loaded($extension) )
      $extension_non_installer[] = $extension;
  }

  include(__DIR__.'/tpl/etape_1.php');
}
