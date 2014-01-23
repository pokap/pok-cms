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
require('templates/init.php');

use pok\Apps\Outils\Session;
use pok\Apps\Outils\Base\Fichier;
use pok\Apps\Models\Base\Requete\PDOFournie;

$resultat = false;

if( isset($_GET['save']) )
{
  if( Session::verifieJeton(0) )
  {
    if( $sql_save = file_get_contents( ADRESSE_SAVE . '/' . $_GET['save'] ) )
    {
      $pdo = &PDOFournie::connexionAt(0);
      $resultat = (boolean) $pdo->query($sql_save);
      PDOFournie::deconnexionAt(0);
    }
  }
}

// on charge la liste des templates
admin\templates\Pages::parse( 'restauration', array(
  'resultat'    => &$resultat,
  'liste_saves' => Fichier::lister( ADRESSE_TEMPLATES, Fichier::FILE, array('.svn') )
));
