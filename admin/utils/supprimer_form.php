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

use pok\Apps\FormulaireQuestionModif,
    pok\Apps\FormulaireReponseModif,
    pok\Apps\Outils\Session,
    pok\Apps\Outils\Base\Fichier,
    pok\Controleur\Page AS CPage;

if( Session::verifieJeton(0) && !empty($_GET['article']) )
{
  $post_id = intval($_GET['article']);

	if( !empty($_GET['delete']) )
  {
    $form = new FormulaireQuestionModif(array( 'fq_id' => $_GET['delete'] ));
    $form->supprimer();
    
    Fichier::log('<ID:' . $_SESSION['id'] . '> supprime le formulaire n°' . $_GET['delete'] . ' de l\'article n°' . $post_id);
    CPage::redirect('admin/form.php?ok&article=' . $post_id);
	}
  elseif( !empty($_GET['rep']) )
  {
    $form = new FormulaireReponseModif(array( 'fr_id' => $_GET['rep'] ));
    $form->supprimer();
    
    pok\addlog('<ID:' . $_SESSION['id'] . '> supprime le réponse n°' . $_GET['rep'] . ' du formulaire de l\'article n°' . $post_id);
    CPage::redirect('admin/form.php?reponse&ok&article=' . $post_id);
  }
}
CPage::redirect('@revenir');
