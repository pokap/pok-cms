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

use pok\Apps\Groupe,
    pok\Apps\PageModif,
    pok\Apps\MembreGroupeModif,
    pok\Apps\Outils\Session,
    pok\Apps\Outils\Base\Fichier,
    pok\Controleur\Page AS CPage;

// droit réserver aux admin
if( Session::verifieJeton(0) && !empty($_GET['g']) )
{
	$gSql = new Groupe(array(array( 'groupe_id' => array( '=', $_GET['g'] ) )));
  if( $groupe = $gSql->publier() )
  {
    $page = new PageModif(array( 'page_id' => $groupe[0]['page_id'] ));
    $page->supprimer();
    
    $mg = new MembreGroupeModif(array(array( 'groupe_id' => $_GET['g'] )));
    $mg->supprimerGroupe();
    
    $groupe[0]->supprimer();
    
    Fichier::log('<ID:' . $_SESSION['id'] . '> supprime groupe n°' . $groupe[0]['groupe_id']);
  }
}
CPage::redirect('@revenir');
