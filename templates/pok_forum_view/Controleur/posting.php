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

namespace templates\pok_forum_view\Controleur\posting;

use \pok\Apps\Membre,
    \templates\pok_forum_view\Controleur;
// -------------------------------------
// dans le cas où c'est un sujet ou une réponse
if( $_WORK == 'topic' || $_WORK == 'post' || $_WORK == 'edit' ) {
  // on supprime les grands espaces
  $_POST['article_texte'] = preg_replace( '`\s+`', ' ', $_POST['text'] );
}
// -------------------------------------
// dans le cas où c'est un sujet
if( $_WORK == 'topic' || $_WORK == 'edit' && empty($_GET['reference']) )
{
  // éviter que les membres choisisent la catégorie
  if( $_SESSION['statut'] != Membre::ADMIN || !isset($_POST['categorie']) )
    $_POST['categorie'] = null;
  // si on oublie le titre, on ajoute un "Sans titre" par défaut
  $_POST['article_titre'] = empty($_POST['titre'])? 'Sans titre' : $_POST['titre'];
  // réécrit le POST pour sécuriser
  $_POST = array(
    'article_titre' => htmlspecialchars( $_POST['titre'], ENT_QUOTES ),
    'article_texte' => nl2br(htmlspecialchars( $_POST['text'], ENT_QUOTES )),
    'categorie'     => $_POST['categorie']
  );
  // tous les sujets sont ouvert par défaut
  $_POST['niveau_comments'] = 1;
}
elseif( $_WORK == 'post' || $_WORK == 'edit' && !empty($_GET['reference']) )
{
  // réécrit le POST pour sécuriser
  $_POST = array(
    'article_texte' => nl2br(htmlspecialchars( $_POST['text'], ENT_QUOTES ))
  );
}

if( $_WORK == 'topic' )
  $_POST['article_niveau'] = 1;
elseif( $_WORK == 'post' )
  $_POST['article_niveau'] = 2;

// Pour revenir à la dernière page
if( $_WORK == 'edit' || $_WORK == 'post' )
{
  function __after_even()
  {
    global $get, $page;
    
    $article = pok\Apps\Article(array(array(
      'article.page_id'        => array( '=', $page['page_id'] ),
      'article.article_parent' => array( '=', $_GET['article'] )
    )));
    $get .= 'numpage=' . ceil( $article->count() / Controleur::TOPIC_PAR_PAGE );
  }
}
