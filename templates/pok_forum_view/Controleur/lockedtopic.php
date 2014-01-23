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
    pok\Apps\ArticleModif,
    pok\Apps\Outils\Session;

if( !empty($_GET['article']) && ( isset($_GET['fermer']) || isset($_GET['ouvrir']) ) && Session::connecter() && $_SESSION['statut'] == Membre::ADMIN )
{
  $post = new ArticleModif(array(
    'article_id'      => $_GET['article'],
    'niveau_comments' => ( isset($_GET['fermer'])? 0 : 1 )
  ));
  $post->modifier();
}