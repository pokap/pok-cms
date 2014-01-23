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

error_reporting(0);
session_start();

// Base du CMS
// La config "general" est automatiquement inclue
require('pok/Main.php');
// charge l'autoload
pok\Main::autoLoad(array(
  ADRESSE_BASE
));

use pok\Enligne;
use pok\Controleur\Page as CPage;
use pok\Apps\Page;
use pok\Apps\Membre;
use pok\Apps\Outils\Session;
use pok\Apps\Outils\Base\Fichier;
use systems\cfg\general;

//Ensuite on vérifie que la variable $_SESSION['id'] existe.
if( Session::connecter() )
{
  // -- log
  Fichier::log('<ID:' . $_SESSION['id'] . '> deconnexion');
  
  //Destruction des variables SESSION.
  $_SESSION = array();
  session_destroy();
  
  // on supprime le fichier temporaire de l'ancien visiteur
  Enligne::revise();
  //Destruction des variables COOKIE.
  setcookie( 'login', '', $_SERVER['REQUEST_TIME'] - 3600, general\PATH, general\DOMAINE );
  setcookie( 'password', '', $_SERVER['REQUEST_TIME'] - 3600, general\PATH, general\DOMAINE );
}

//On est pas connecté, alors on redirige le visiteur sur la page d'accueil.
CPage::redirect('@revenir');

