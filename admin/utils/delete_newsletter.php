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

use pok\Apps\NewsletterModif,
    pok\Apps\MembreNewsletterModif,
    pok\Apps\Outils\Session,
    pok\Apps\Outils\Base\Fichier,
    pok\Controleur\Page AS CPage;

if( Session::verifieJeton(0) && !empty($_GET['n']) )
{
  $n = intval($_GET['n']);
  $newsletter = new NewsletterModif(array( 'newsletter_id' => $n ));
  $newsletter->supprimer();
  // relation membre
  $membre = new MembreNewsletterModif(array( 'newsletter_id' => $n ));
  $membre->supprimer();
  
  Fichier::log('<ID:' . $_SESSION['id'] . '> supprime newsletter n°' . $n);
}
CPage::redirect('@revenir');

