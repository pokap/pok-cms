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

use pok\Apps\Page,
    pok\Apps\PageModif,
    pok\Apps\Outils\Session,
    pok\Apps\Outils\Base\Fichier,
    pok\Controleur\Page AS CPage;

// droit réserver aux admin
if( Session::verifieJeton(0) && isset($_GET['page'], $_GET['courant'], $_POST['deplacement_id'], $_POST['deplacement']) && Page::format($_GET['page']) )
{
  // hack
  $id_deplacement = intval($_POST['deplacement_id']);
  $deplacement = intval($_POST['deplacement']);
  // si on a spécifier un déplacement on prend celui-ci
  $id_dossier_deplace = $id_deplacement == 0 ? $deplacement : $id_deplacement;
  
  // récupère l'arborescence de la page
  $sqlPage = new Page(array(array( 'page_id' => array( '=', $id_dossier_deplace ) )));
  if( $newpage = $sqlPage->publier() )
  {
    $page = new PageModif(array(
      'page_id'      => $_GET['courant'],
      'arborescence' => (($newpage[0]['arborescence'] === '')? $newpage[0]['arborescence'] : $newpage[0]['arborescence'] . '/' ) . end(Page::explode($_GET['page']))
    ));
    $page->modifier();
    
    Fichier::log('<ID:' . $_SESSION['id'] . '> déplace dossier n°' . $_GET['courant'] . ' vers dans le dossier n°' . $id_dossier_deplace);
    CPage::redirect('admin/page.php?deplaceok&page=' . $newpage[0]['arborescence']);
  }
  else
    CPage::redirect('admin/page.php?e_deplace&page=' . $_GET['page']);
}
CPage::redirect('@revenir');
