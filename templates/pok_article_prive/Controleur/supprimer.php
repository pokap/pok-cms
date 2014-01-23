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

use pok\Apps\Article,
    pok\Apps\ArticlePriveModif,
    pok\Apps\Outils\Session,
    pok\Controleur\Page AS CPage,
    templates\pok_article_prive\Models\Messagerie;

if( Session::connecter() && !empty($_GET['mess']) )
{
  // s'il y a un article qui correspond
  if( $article = Article::getByArticleId($_GET['mess']) )
  {
    // vérifie si c'est moi qui est envoyer ce message
    // si c'est le cas on utilise une fonction différente
    if( $article['article_auteur'] == $_SESSION['id'] )
    {
      $mess = new ArticlePriveModif(array( 'prive_article_id' => $_GET['mess'] ));
      // supprime tous les relations des messages et destinataires
      $mess->supprimer();
      // supprime tous les messages
      $article->supprimer();
    }
    elseif( Messagerie::isDestinataire($article['article_id']) )
    {
      $mess = new ArticlePriveModif(array(
        'prive_article_id' => $_GET['mess'],
        'prive_membre_id'  => $_SESSION['id'],
      ));
      $mess->supprimer();
    }
  }
}
// si rien, alors c'est qu'il y a une erreur
CPage::redirect('@revenir', array('delete' => 'erreur'));
