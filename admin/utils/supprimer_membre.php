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

use pok\Apps\Membre,
    pok\Apps\PageModif,
    pok\Apps\Outils\Session,
    pok\Apps\Outils\Base\Fichier,
    pok\Controleur\Page AS CPage;

if( Session::verifieJeton(0) && !empty($_GET['m']) )
{
  $mSql = new Membre(array(array( 'membre_id' => array( '=', $_GET['m'] ) )));
  if( $membre = $mSql->publier() )
  {
    $page = new PageModif(array( 'page_id' => $membre[0]['page_id'] ));
    $page->supprimer();
    
    $groupe = new MembreGroupeModif(array( 'membre_id' => $membre[0]['membre_id'] ));
    $groupe->supprimer();
    
    $membre[0]->supprimer();
    
    Fichier::log('<ID:' . $_SESSION['id'] . '> supprime membre <ID:' . $membre[0]['membre_id'] . '>');
    CPage::redirect('admin/membre.php?page=' . ceil( ($mSql->count() - 1) / 30 ));
  }
}
CPage::redirect('@revenir');
