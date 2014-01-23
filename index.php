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

// Alias
use pok\Apps\Outils;

// -------------------------------------
// Nous commencons par un test de configuration
// Si la config n'existe pas, nous devons installer le cms =)
if( !Outils\Cfg::import('config') ) {
  include('install/install.php');
}
else
{
  // token pour les failles de type CSRF
  Outils\Session::creerJeton();
  // connexion automatique avec cookie
  pok\Apps\Membre::cookieConnexion();
  
  // contre attaque xss ou vol de session
  if( !Outils\Session::fixeConnexion() )
    pok\Controleur\Page::redirect('./index.php?template_acces_interdit_view');

  $load = new pok\Main();
  
  // LE RESTE
  try {
    $load->afficher();
  }
  catch( pok\Exception $e ) {
    $e->afficher_erreur();
  }
}
?>