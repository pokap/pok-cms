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

use pok\Texte,
    pok\Apps\MembreGroupeModif,
    pok\Apps\Outils\Session,
    pok\Apps\Outils\Base\Fichier,
    pok\Apps\Models\Base\Requete\PDOFournie,
    pok\Controleur\Page AS CPage,
    systems\cfg\config;

if( Session::verifieJeton(0) && !empty($_GET['g']) )
{
  $pseudo = Texte::slcs($_POST['membre']);
  
	$groupe = new MembreGroupeModif(array(
    'membre_id' => array( '(SELECT membre_id FROM '.config\PREFIX.'membre WHERE membre_pseudo = "'.$pseudo.'")', PDOFournie::NOT_QUOTE ),
    'groupe_id' => $_GET['g'],
    'principal' => 0
  ));
  $groupe->ajouter();
  
  Fichier::log('<ID:' . $_SESSION['id'] . '> ajoute membre <PSEUDO:'.$pseudo.'> dans groupe n°' . $_GET['g']);
}
CPage::redirect('@revenir');
