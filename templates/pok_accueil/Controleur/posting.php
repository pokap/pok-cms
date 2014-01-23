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

namespace templates\pok_accueil\Controleur\posting;

use pok\Apps\Article,
    pok\Apps\Models\Base\Requete\PDOFournie,
    pok\Controleur\Page AS CPage;

// nous allons faire un syst�me anti-flood :
// si on r�pond � un article, il faut d'abord que notre pr�c�dente r�ponse soit espac�e de 30sec
if( $_WORK == 'post' )
{
  $aSql = new Article(array( array(
    'article_parent' => array( '=', $id_reference ),
    'page_id'        => array( '=', $page['page_id'] ),
    'article_date_creer' => array( '>', 'NOW() - INTERVAL 30 SECOND', PDOFournie::NOT_QUOTE ),
    'article_auteur' => array( '=', $_SESSION['id'] )
  )));
  $aSql->addOrder('!id_post');
  $aSql->limit(1);
  $last_post = $aSql->publier();
  if( !empty($last_post) ) $action = false;
  
  // on refait le POST pour s�curis� :
  //  - pour ne pas faire de faille xss
  //  - pour ne pas cr�er de sous-commentaire
  $_POST = array(
    'article_texte' => nl2br(htmlspecialchars( $_POST['texte'], ENT_QUOTES ))
  );
}
// pour ne pas changer d'autres donn�es
elseif( $_WORK == 'edit' )
{
  // on ne modifie que le texte
  $_POST = array(
    'article_texte' => nl2br(htmlspecialchars( $_POST['texte'], ENT_QUOTES ))
  );
}
else
  CPage::redirect('@revenir');
