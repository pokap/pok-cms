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

namespace templates\pok_article_prive\Models;

use pok\Apps\Article,
    pok\Apps\ArticlePrive,
    pok\Apps\Outils\Session,
    pok\Apps\Models\Base\Requete\PDOFournie,
    systems\cfg\config;

class ArticlePrive
{
  // -------------------------------------
  // Array :
  //   liste des messages que j'ai reçu et que je n'ai pas supprimé
  public static function getMessageRecu( array $arguments = array() )
  {
    // il faut être connecté obligatoirement
    if( Session::connecter() )
    {
      $arguments['article.article_date_reviser'] = array( '<=', 'NOW()', PDOFournie::NOT_QUOTE );
      $arguments['article.article_date_max']     = array( array( '>=', 'NOW()', PDOFournie::NOT_QUOTE ), array( 'IS NULL', null ) );
      $arguments['article.brouillon']            = array( '=', "'0'", PDOFournie::NOT_QUOTE );
      // on fusionne le tableau envoyer + les données initiales
      // on recherche tous les articles dont on est le destinataire + ceux qu'on a rédiger et qu'on a répondu
      $messages = new Article(array(
        array_merge( $arguments, array(
        'article.article_parent'        => array( '=', 0, PDOFournie::NOT_QUOTE ),
        'article_prive.prive_membre_id' => array( '=', $_SESSION['id'] )
        )),
        array_merge( $arguments, array(
        'article.article_auteur'        => array( '=', $_SESSION['id'] ),
        'article.count'                 => array( '>', 0, PDOFournie::NOT_QUOTE )
        ))
      ));
      // On doit reçevoir seulement ceux dont on est le destinataire, on doit fusionner les tables.
      $messages->addJoin( config\PREFIX.ArticlePrive::TABLE , ArticlePrive::TABLE, ArticlePrive::TABLE.'.prive_article_id = '.Article::TABLE.'.article_id' );
      
      $infos_messages = $messages->publier();
      if( empty($infos_messages) ) return array();
      
      /*// contruit le bout de requete sql qui contient la liste des articles
      $all_id_post = $this->floopSql( 'mess.mes_posts_id', $infos_messages, function($article){
        return $article['id'];
      });
      
      // liste des destinataires
      $liste_emetteur = $this->getUserMessage( $all_id_post . ' AND dest.user_id = ' . $_SESSION['id'] );
      // on relie la liste des destinataires et celle des messages
      $this->fusionArticleUser( $infos_messages, $liste_emetteur, 'emetteur' );
      */
      return $infos_messages;
    }
    else return array();
  }
  
  // -------------------------------------
  // Array :
  //   liste des messages que j'ai envoyer et que je n'ai pas supprimé
  public static function getMessageEnvoie( array $arguments = array() )
  {
    // il faut être connecté obligatoirement
    if( Session::connecter() )
    {
      // on fusionne le tableau envoyer + les données initiales
      $argbase = array(
        'article.article_auteur' => array( '=', $_SESSION['id'] ),
        'article.article_parent' => array( '=', 0 )
      );
      
      // Recherche tout les messages envoyer
      $messages = new Article(array(array_merge( $argbase, $arguments )));
      $infos_messages = $messages->publier();
      if( empty($infos_messages) ) return array();
      
      // contruit le bout de requete sql qui contient la liste des articles
      /*$all_id_post = $this->floopSql( 'mess.mes_posts_id', $infos_messages, function($article){
        return $article['id'];
      });

      // liste des destinataires
      $liste_destinataire = $this->getUserMessage( $all_id_post );
      // on relie la liste des destinataires et celle des messages
      $this->fusionArticleUser( $infos_messages, $liste_destinataire, 'destinataire' );*/

      return $infos_messages;
    }
    else return array();
  }

  // -------------------------------------
  // Bool :
  //   Vérifie si on fait partie des destinataires d'un message
  public static function isDestinataire( $id_post )
  {
    if( Session::connecter() )
    {
      // initialise
      $ids = array();
      // on récupère la liste des destinataires
      foreach( ArticlePrive::fetchByPriveArticleId($id_post) AS $membres )
        $ids[] = $membres['prive_membre_id'];
      
      // maintenant on vérifie qu'on est dedant
      return in_array( $_SESSION['id'], $ids );
    }
    else return false;
  }
}

