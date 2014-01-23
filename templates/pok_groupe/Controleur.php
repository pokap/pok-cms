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

namespace templates\pok_groupe;

use \pok\Apps\Page;
use \pok\Controleur\Page AS CPage;

class Controleur extends \pok\Controleur
{
  // ------------------------------------------
  // invoquer, est toujours appeller et doit toujours exister
  public function __invoke()
  {
    // infos sur les sous-pages
    $pageSql = new Page(array(array(
      'page.arborescence' => Page::getReferenceClause(array(CPage::$actuelle['arborescence']))
    )));
    $this->assign( 'liste_groupe', $pageSql->publier() );
  }
}
