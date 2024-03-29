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
    pok\Apps\FormulaireQuestionModif,
    pok\Apps\Outils\Session,
    pok\Apps\Outils\Base\Fichier,
    pok\Controleur\Page AS CPage;

if( Session::verifieJeton(0) && !empty($_GET['article']) && !empty($_GET['id']) )
{
  if( isset($_GET['up']) )
    FormulaireQuestionModif::changeOrdre( $_GET['id'], $_GET['up'], true );
  elseif( isset($_GET['down']) )
    FormulaireQuestionModif::changeOrdre( $_GET['id'], $_GET['down'], false );
  else
    CPage::redirect('@revenir');
  
  Fichier::log('<ID:' . $_SESSION['id'] . '> modification de l\'ordre d\'affichage du formulaire n�' . $_GET['id']);
  CPage::redirect('admin/form.php?ok&article='.$_GET['article']);
}
CPage::redirect('@revenir');
