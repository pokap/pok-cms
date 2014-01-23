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

use pok\Apps\MembreGroupeModif,
    pok\Apps\Outils\Session,
    pok\Apps\Outils\Base\Fichier,
    pok\Apps\Models\Base\Requete\PDOFournie,
    pok\Controleur\Page AS CPage,
    systems\cfg\config;

if( Session::verifieJeton(0) && isset($_GET['g'], $_GET['m']) && !empty($_GET['g']) && !empty($_GET['m']) )
{
  $groupe = new MembreGroupeModif(array(
    'groupe_id' => $_GET['g'],
    'membre_id' => $_GET['m']
  ));
  $groupe->supprimerMembre();
  
  Fichier::log('<ID:' . $_SESSION['id'] . '> supprime membre <ID:' . $_GET['m'] . '> du groupe n°' . $_GET['g']);
}
CPage::redirect('@revenir');
