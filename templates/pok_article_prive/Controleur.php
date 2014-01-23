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

namespace templates\pok_article_prive;

use pok\Apps\Article,
    pok\Apps\ArticlePrive,
    pok\Apps\Outils\Session,
    pok\Controleur\Page AS CPage;

class Controleur extends \pok\Controleur
{
  // ------------------------------------------
  // invoquer, est toujours appeller et doit toujours exister
  public function __invoke()
  {
    // On doit être connecté pour voir cette page
    if( !Session::connecter() ) CPage::redirect();
    
    // voirecu met de voir un message reçu en particulier
    if( !empty($_GET['voirecu']) )
    {
      // affiche l'article
      $this->boite_recepetion( 'voirecu', $_GET['voirecu'] );
      // affiche ces sous-articles
      $this->reponse($_GET['voirecu']);
      // affiche la liste des destinataires de l'article
      $this->liste_destinataire($_GET['voirecu']);
    }
    // affiche la page des messages envoyer
    elseif( isset($_GET['envoie']) )
    {
      // si on precise pas, on affiche la liste des messages
      if( empty($_GET['envoie']) )
        $this->boite_envoie('envoie');
      // sinon c'est un message en particulier
      else
      {
        $this->boite_envoie( 'envoie', false, $_GET['envoie'] );
        $this->liste_destinataire($_GET['envoie']);
      }
    }
    // si on choisie de voir les brouillons
    elseif( isset($_GET['brouillon']) )
    {
      // si le message brouillon n'est pas présisez, on les affiche tous
      if( empty($_GET['brouillon']) )
        $this->boite_envoie( 'brouillon', true );
      // sinon c'est un message spécifique
      else
        $this->boite_envoie( 'brouillon', true, $_GET['brouillon'] );
    }
    else
      $this->boite_recepetion('messrecu');
  }

  // -------------------------------------
  // Void
  //   Aussi simplement, on affiche les réponses qu'y on été rajouté au message
  //   comme un article banale
  private function reponse( $posts_id )
  {
    $reponses = new Article(array(array(
      'article.article_parent' => array( '=', $posts_id ),
      'article.page_id'        => array( '=', CPage::$actuelle['page_id'] )
    )));
    $this->assign( 'reponses', $reponses->publier() );
  }
  
  // -------------------------------------
  // Void
  //   affiche tous les messages de la boite d'envoie
  private function boite_envoie( $assign, $publier = false, $id_post = 0 )
  {
    // liste d'argument en plus
    // on indique le répertoire
    $arguments = array(
      'article.page_id'   => array( '=', CPage::$actuelle['page_id'] ),
      'article.brouillon' => array( '=', (int) $publier )
    );
    
    if( $id_post > 0 )
      $arguments['article.article_id'] = array( '=', $id_post );
    
    $message = Models\ArticlePrive::getMessageEnvoie($arguments);
    
    // si vide on reviens sur l'accueil
    if( empty($message) && $id_post > 0 ) CPage::redirect('@top');
    
    $this->assign( $assign, ( $id_post > 0 )? $message[0] : $message );
  }
  
  // -------------------------------------
  // Void
  //   affiche tous les messages de la boite de réception
  private function boite_recepetion( $assign, $id_post = 0 )
  {
    // liste d'argument en plus
    // on indique le répertoire
    $arguments = array( 'article.page_id' => array( '=', CPage::$actuelle['page_id'] ) );
    
    if( $id_post > 0 )
      $arguments['article.article_id'] = array( '=', $id_post );
    
    // message principal
    $message = Models\ArticlePrive::getMessageRecu($arguments);
    
    // si vide on reviens sur l'accueil
    if( empty($message) && $id_post > 0 ) CPage::redirect('@top');
    
    $this->assign( $assign, ( $id_post > 0 )? $message[0] : $message );
  }
  
  // -------------------------------------
  // Void
  //   créer la liste des destinataires du message
  private function liste_destinataire( $id_message )
  {
    $pliste = array();
    // récupère tous les infos des destinataires
    foreach( ArticlePrive::fetchByPriveArticleId( $id_message, 'publierMembre' ) AS $dest ) {
      $pliste[] = $dest['membre_pseudo'];
    }
    // s'il n'y a aucun destinataire, un message le signal
    $liste = empty($pliste)? 'Aucun destinataire !' : implode( ', ', $pliste );
    // on envoie tous dans le template
    $this->assign( 'liste_destinataire', $liste );
  }
}