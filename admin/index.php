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
  
// on met à jour la liste des gens connecté et visiteurs
$infos_online = pok\Enligne::traiter();
// initialise
$list_membres_co = array();

if( !empty($infos_online[pok\Enligne::MEMBRE]) )
{
  // initialise
  // contient la liste des membres connecté ...
  $membre_ids = array();
  // ..., les collectes et utilise pour ...
  foreach( $infos_online[pok\Enligne::MEMBRE] AS $membre ) {
    $membre_ids[] = array( '=', $membre, PDO::PARAM_INT );
  }
  // ... récupérer toutes les informations d'un coup.
  $membre = new pok\Apps\Membre(array(array( 'membre.membre_id' => $membre_ids )));
  $list_membres_co = $membre->publier();
}

admin\templates\Pages::parse('index', array(
  'list_membres_co' => $list_membres_co,
  'visiteurs'       => $infos_online[pok\Enligne::VISITEUR]
));
?>