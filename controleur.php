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

session_start();

use pok\Main,
    pok\Apps\Membre,
    pok\Apps\Outils\Cfg,
    pok\Apps\Outils\Session,
    pok\Apps\Outils\Base\Fichier,
    pok\Controleur\Page AS CPage;

// Base du CMS
// La config "general" est automatiquement inclue
require('pok/Main.php');
// charge l'autoload
Main::autoLoad(array(
  ADRESSE_BASE
));

// permet de faciliter l'acces à son propre template
define( 'REPERTOIRE_TEMPLATE', ADRESSE_TEMPLATES . '/' . $_GET['tpl']  );
// on doit avoir le template et le fichiers en questions
// le template est un nom de répertoire et le controller un fichier
// donc il faut absolument sécurisé pour ne pas include un fichier non désiré
if( Session::verifieJeton(0) && Cfg::import('config') && isset($_GET['tpl'], $_GET['ctrl']) && preg_match('`^[a-zA-Z0-9._-]+$`', $_GET['tpl']) && preg_match('`^[a-zA-Z0-9._-]+$`', $_GET['ctrl']) && file_exists(REPERTOIRE_TEMPLATE . '/Controleur/' . $_GET['ctrl'] . '.php') )
{
  // connexion automatique avec cookie
  Membre::cookieConnexion();
  
  if( Session::connecter() && $_SESSION['statut'] == Membre::BANNIE )
    CPage::redirect('@revenir');
  
  // contre attaque xss ou vol de session
  if( !Session::fixeConnexion() )
    CPage::redirect('./index.php?goto404');
  
  // on inclue le controllers
  include(REPERTOIRE_TEMPLATE . '/Controleur/' . $_GET['ctrl'] . '.php');
}
// on renvoie à l'accueil du site
CPage::redirect('@revenir');

