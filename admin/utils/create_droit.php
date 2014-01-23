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
    pok\Apps\DroitModif,
    pok\Apps\Outils\Session,
    pok\Apps\Outils\Base\Fichier,
    pok\Apps\Models\Base\Requete\PDOFournie,
    pok\Controleur\Page AS CPage;

// droit réserver aux admin
if( Session::verifieJeton(0) && !empty($_GET['g']) )
{
  $id_groupe = intval($_GET['g']);
  
  foreach( $_POST AS $id_cat => $droit )
  {
    $droits = new DroitModif(array(
      'cat_id'    => $id_cat,
      'groupe_id' => $_GET['g'],
      'vlp'  => $droit['vlp'],
      'euna' => $droit['euna'],
      'raa'  => $droit['raa'],
      'blr'  => $droit['blr'],
      'etla' => $droit['etla'],
      'stla' => $droit['stla'],
      'ssa'  => $droit['ssa'],
      'mda'  => $droit['mda']
    ));
    $droits->setReplaceMode(true);
		$droits->ajouter();
	}
  // log
  Fichier::log('<ID:' . $_SESSION['id'] . '> modifie droits groupe n°' . $id_groupe);
}
CPage::redirect('@revenir');
