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


// ------------------------------------------
// Contruit chaque requete à partir d'un tableau
function requete_insert( $tableau, $donnees, $function )
{
  // contruit la requete
  $traitement_insert_sql = 'INSERT INTO `' . $_SESSION['prefix'] . $donnees . '` VALUES';
  // récupère les données à inclure
  $donnees_total = array();
  // chaque ligne
  foreach( $tableau AS $valeur )
  {
    $map = array_map( $function, $valeur );
    $donnees_total[] = '(' . implode( ',', $map ) . ')';
  }
  // assemble toutes les lignes
  $traitement_insert_sql .= implode( ',', $donnees_total ) . ';';

  return $traitement_insert_sql;
}

// ------------------------------------------
// installation de la bdd avec les infos dans la SESSIONS
function install_bdd( $personalise = array() )
{
  global $pdo;

  // ------------------------------------------
  // Toutes les tables à créer
  $liste_table = array(
    'article' => "
      `article_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `page_id` int(10) unsigned NOT NULL,
      `article_date_creer` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      `article_date_reviser` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      `article_date_max` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      `article_auteur` int(10) unsigned NOT NULL,
      `article_chapo` text COLLATE utf8_unicode_ci NOT NULL,
      `article_texte` text COLLATE utf8_unicode_ci NOT NULL,
      `article_titre` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
      `article_slug` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
      `article_niveau` smallint(6) NOT NULL,
      `brouillon` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
      `niveau_comments` tinyint(3) unsigned NOT NULL DEFAULT '0',
      `article_parent` int(10) unsigned NOT NULL,
      `count` smallint(5) unsigned NOT NULL DEFAULT '0',
      PRIMARY KEY (`article_id`),
      UNIQUE KEY `slug` (`page_id`,`article_slug`),
      KEY `recherche` (`article_titre`,`article_slug`)",
    'article_prive' => "
      `prive_article_id` int(10) unsigned NOT NULL,
      `prive_membre_id` int(10) unsigned NOT NULL,
      PRIMARY KEY (`prive_article_id`,`prive_membre_id`)",
    'article_vu' => "
      `av_membre_id` int(10) unsigned NOT NULL,
      `av_reference_id` int(10) unsigned NOT NULL,
      `av_article_id` int(10) unsigned NOT NULL,
      `av_poster` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
      PRIMARY KEY (`av_membre_id`,`av_reference_id`)",
    'categorie' => "
      `cat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `cat_nom` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
      `taxon` enum('page','article','both','tag') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'page',
      PRIMARY KEY (`cat_id`)",
    'cat_relation' => "
      `relation_id` int(10) unsigned NOT NULL,
      `cat_id` int(10) unsigned NOT NULL,
      `terms` enum('page','article') COLLATE utf8_unicode_ci NOT NULL,
      PRIMARY KEY (`relation_id`,`cat_id`,`terms`)",
    'droit' => "
      `cat_id` int(10) unsigned NOT NULL,
      `groupe_id` int(10) unsigned NOT NULL,
      `vlp` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'voir les pages',
      `euna` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'écrire un nouvel article',
      `raa` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'répondre aux articles',
      `blr` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'bloquer les réponses',
      `etla` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'éditer tous les articles',
      `stla` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'supprimer tous les articles',
      `ssa` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'supprimer son article',
      `mda` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'modifier la durée d''un article',
      PRIMARY KEY (`cat_id`,`groupe_id`)",
    'fichier' => "
      `fichier_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `fichier_nom` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
      `poids` int(10) unsigned NOT NULL,
      `fichier_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
      `extension` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
      `telecharger` smallint(7) unsigned NOT NULL DEFAULT '0',
      PRIMARY KEY (`fichier_id`),
      KEY `Recherche` (`fichier_nom`)",
    'formulaire_question' => "
      `fq_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `article_id` int(10) unsigned NOT NULL,
      `fq_inputype` enum('textarea','simple','select','radio','checkbox') COLLATE utf8_unicode_ci NOT NULL,
      `fq_ordre` smallint(3) unsigned NOT NULL,
      `fq_label` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
      `fq_texte` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
      `fq_option` text COLLATE utf8_unicode_ci NOT NULL,
      PRIMARY KEY (`fq_id`),
      UNIQUE KEY `posts_id` (`article_id`,`fq_label`)",
    'formulaire_reponse' => "
      `fr_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `article_id` int(10) unsigned NOT NULL,
      `membre_id` int(10) unsigned NOT NULL,
      `fr_value` text COLLATE utf8_unicode_ci NOT NULL,
      `fr_date` datetime NOT NULL,
      PRIMARY KEY (`fr_id`),
      UNIQUE KEY `posts_id` (`article_id`,`membre_id`)",
    'groupe' => "
      `groupe_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `page_id` int(10) unsigned NOT NULL,
      `couleur` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
      PRIMARY KEY (`groupe_id`)",
    'membre' => "
      `membre_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `membre_pseudo` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
      `page_id` int(10) unsigned NOT NULL,
      `membre_mdp` varchar(42) COLLATE utf8_unicode_ci NOT NULL,
      `membre_email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
      `membre_inscrit` datetime NOT NULL,
      `membre_visite` datetime NOT NULL,
      `statut` enum('M','A','B') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'M',
      `cle` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
      `valide` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
      PRIMARY KEY (`membre_id`),
      UNIQUE KEY `EMAIL` (`membre_email`),
      UNIQUE KEY `PSEUDO` (`membre_pseudo`)",
    'membre_groupe' => "
      `membre_id` int(10) unsigned NOT NULL,
      `groupe_id` int(10) unsigned NOT NULL,
      `principal` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
      PRIMARY KEY (`membre_id`,`groupe_id`)",
    'membre_newsletter' => "
      `membre_id` int(10) unsigned NOT NULL,
      `newsletter_id` int(10) unsigned NOT NULL,
      PRIMARY KEY (`newsletter_id`,`membre_id`)",
    'newsletter' => "
      `newsletter_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `page_id` int(10) unsigned NOT NULL,
      `newsletter_auto` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
      `newsletter_titre` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
      PRIMARY KEY (`newsletter_id`)",
    'page' => "
      `page_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `page_ordre` tinyint(4) NOT NULL,
      `page_nom` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
      `page_description` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
      `template` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'defauld',
      `nonlu` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
      `arborescence` text COLLATE utf8_unicode_ci NOT NULL,
      PRIMARY KEY (`page_id`)"
  );

  // connexion serveur sql
  try {
    $pdo = new PDO( 'mysql:host='.$_SESSION['server_sql'].';dbname='.$_SESSION['bdd_sql'], $_SESSION['user_sql'], $_SESSION['password_sql'] );
  }
  catch( PDOException $e ) {
    die('<p><b>Attention</b> : Connexion à la bdd impossible !</p><p>' . nl2br($e) . '</p>');
  }
  
  $insterf = function( $valeur ) {
    global $pdo;
    return is_array($valeur) && isset($valeur['sql']) ? $valeur['sql'] : $pdo->quote($valeur);
  };
  
  try
  {
    //on tente d'exécuter les requêtes suivantes dans une transactions
    //on lance la transaction
    $pdo->beginTransaction();
    
    // nos requêtes
    foreach( $liste_table AS $nom_table => $info_table )
    {
      // -- Structure de la table
      $pdo->query('CREATE TABLE IF NOT EXISTS `' . $_SESSION['prefix'] . $nom_table . '` ( ' . $info_table . ' ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;');

      // -- Contenu de la table
      if( isset($personalise[$nom_table]) && !empty($personalise[$nom_table]) )
      {
        if( !array_key_exists( 0, $personalise[$nom_table] ) )
          $personalise[$nom_table] = array($personalise[$nom_table]);
        
        $pdo->query(requete_insert( $personalise[$nom_table], $nom_table, $insterf ));
      }
    }
    
    //si jusque là tout se passe bien on valide la transaction
    $pdo->commit();
    
    $retour = true;
  }
  catch( Exception $e ) //en cas d'erreur
  {
    //on annule la transation
    $pdo->rollback();

    $retour = false;
  }
  
  // déconnexion sql
  $pdo = null;
  
  return $retour;
}
