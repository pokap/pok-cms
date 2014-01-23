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

// Base pour le CMS
require('../templates/init.php');

use pok\Apps\MembreNewsletterModif,
    pok\Apps\Outils\Session,
    pok\Apps\Outils\Base\Fichier,
    pok\Controleur\Page AS CPage;

if( Session::verifieJeton(0) && !empty($_GET['m']) && !empty($_GET['n']) )
{
  $membre     = intval($_GET['m']);
  $newsletter = intval($_GET['n']);
  
  $nSql = new MembreNewsletterModif(array(
    'membre_id' => $membre,
    'newsletter_id' => $newsletter
  ));
  $nSql->supprimer();
  
  Fichier::log('<ID:' . $_SESSION['id'] . '> enleve newsletter n°'.$newsletter.' au membre n°'.$membre);
}
CPage::redirect('@revenir');
