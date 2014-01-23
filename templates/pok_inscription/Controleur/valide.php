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
    pok\Apps\Outils\Session,
    pok\Controleur\Page AS CPage;

if( !Session::connecter() && isset($_GET['cle'], $_GET['arbo']) )
{
  // on import la class Membres et on l'instancie
  $mSql = new Membre(array(array(
    'cle'    => array( '=', $_GET['cle'] ),
    'valide' => array( '=', 0 )
  )));
  if( $membre = $mSql->publier() )
  {
    $membre[0]['valide'] = '1';
    $membre[0]->modifier();
    CPage::redirect(CPage::url( $_GET['arbo'],'','&valide_ok' ));
  }
}
// si rien, alors c'est qu'il y a une erreur
CPage::redirect(CPage::url('','','&valide_erreur'));

