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

namespace pok\Apps;

use pok\Apps\Outils\Base\Fichier,
    systems\cfg\config;

abstract class Ressource
{
  public static $auto = false;
  
  private static $arbo_complete;
  
  // -------------------------------------
  // Bool :
  //   @string $arboc    : arborescence de la page concerné
  //   @int    $page_id  : identifiant de la page concerné
  //  [@string $template : nom du template à chercher, pour touses tapez "*"]
  //   Créer des sous-pages et articles automatiques
  public static function developper( $arboc, $page_id, $template = '*' )
  {
    self::$arbo_complete = $arboc;
    // on supprime la derniere page
    $arboc = Page::strSupDernierePage($arboc);
    
    // le template doit respecter la syntaxe et l'arborescence aussi
    if( (preg_match('`^[a-zA-Z0-9._-]+$`', $template) || $template === '*') && Page::format($arboc) )
    {
      // remplace les "/" pour l'exploiter en nom de fichier
      $arboc = str_replace( '/', '.', $arboc );
      
      if( self::$auto && file_exists( ADRESSE_TEMPLATES . '/' . $template . '/ressources/active.ini' ) )
      {
        $ini = parse_ini_file( ADRESSE_TEMPLATES . '/' . $template . '/ressources/active.ini' );
        $active = array_key_exists( $arboc, $ini ) && $ini[$arboc] == true;
      }
      // le base il est activer sinon on ne fait jamais rien :p
      else $active = true;
      
      // on lance tout le bordel !
      if( $active )
      {
        // chemin
        $all_file = glob( ADRESSE_TEMPLATES . '/' . $template . '/ressources/' . $arboc . '.xml' );
        if( $all_file != array() )
        {
          // pour récupérer la liste des pages ressources de chaque template
          foreach( $all_file AS $file )
          {
            $xml = new \SimpleXMLElement( $file, null, true );
            // s'il y a une erreur on arrête
            if( !self::developpeRecursive( $page_id, $xml ) )
              return false;
          }
          return true;
        }
      }
    }
    return false;
  }

  // -------------------------------------
  // Void :
  //   @int              $ID  : identidiant de la page concerné
  //   @SimpleXMLElement $xml : objet XML qui contient la liste des informations à créer
  //   créer des sous-pages et articles d'après un XML et identidiant de la page
  private static function developpeRecursive( $ID, \SimpleXMLElement $xml )
  {
    // -----------------------
    // SI ON CREER UN ARTICLE
    if( isset($xml->article) )
    {
      foreach( $xml->article AS $donnees )
      {
        $alldonnees = (array) $donnees;
        $informations = array_merge( $alldonnees, array( 'page_id' => $ID ));
        $article = new ArticleModif($informations);
        // créer l'article
        $article_id = $article->ajouter();
        if( $nouveau_id > 0 )
        {
          if( !empty($alldonnees['categorie']) )
          {
            // relation categorie
            $cat = new CatRelationModif(array(
              'relation_id' => $nouveau_id,
              'cat_id'      => $alldonnees['categorie'],
              'terms'       => 'article'
            ));
            $cat->ajouter();
          }
          Fichier::log('creation article automatique '.$article_id);
        }
        else
        {
          Fichier::log('ERREUR creation article du dossier '.$ID);
        }
      }
    }
    // -----------------------
    // SI ON CREER UN DOSSIER
    if( isset($xml->page) )
    {
      foreach( $xml->page AS $donnees )
      {
        $alldonnees = (array) $donnees;
        
        $informations = array_merge( (array) $donnees, array( 'arborescence' => str_replace( '~', self::$arbo_complete, $alldonnees['arborescence'] ) ));
        $page = new PageModif($informations);
        // créer la page
        $nouveau_id = $page->ajouter();
        if( $nouveau_id > 0 )
        {
          // relation categorie
          $cat = new CatRelationModif();
          $cat['relation_id'] = $nouveau_id;
          $cat['terms'] = 'page';
          if( empty($alldonnees['categorie']) ) {
            $cat['cat_id'] = array( '(SELECT ccr.cat_id FROM '.config\PREFIX.Categorie::TABLE.' AS ccr WHERE ccr.taxon = "'.Categorie::TAXON_BOTH.'" OR ccr.taxon = "'.Categorie::TAXON_PAGE.'" LIMIT 1)', Models\Base\Requete\PDOFournie::NOT_QUOTE );
          }
          else {
            $cat['cat_id'] = $alldonnees['categorie'];
          }
          $cat->ajouter();
          // log
          Fichier::log('creation page automatique '.$nouveau_id);
          
          // récupère de coté le conteu des sous-pages
          // créer les sous-pages et leurs contenus
          if( !empty($alldonnees['ressources']) )
            self::developpeRecursive( $nouveau_id, $alldonnees['ressources'] );
        }
        else
        {
          Fichier::log('ERREUR creation sous-page de la page '.$ID);
        }
      }
    }
  }
}