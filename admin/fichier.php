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

use pok\Apps\Fichier;
use pok\Apps\FichierModif;
use pok\Apps\Outils\Upload;
use pok\Apps\Outils\Session;
use pok\Apps\Outils\Base\Fichier AS Outils;

$file = new Fichier();
$file->setAlias('page');
$file->limit( 0, 30 );

$charge = new Upload('fichier');

if( $charge->existe() && Session::verifieJeton(0) )
{
  $fichier = new FichierModif(array(
    'fichier_nom'         => $_POST['titre'],
    'poids'               => $charge->poids(),
    'fichier_description' => $_POST['description'],
    'extension'           => $charge->ext()
  ));
  $id = $fichier->ajouter();
  $charge->charger(ADRESSE_FICHIERS . '/' . $charge->ext() . '/' . $id . '.' . $charge->ext());
}
elseif( isset($_GET['suppr']) && Session::verifieJeton(0) && !empty($_GET['f']) )
{
  $id_fichier = $_GET['f'];
  $fSql = new Fichier(array(array( 'fichier_id' => array( '=', $id_fichier ) )));
  if( $fichier = $fSql->publier() )
  {
    unlink(ADRESSE_FICHIERS . '/' . $fichier[0]['extension'] . '/' . $id_fichier . '.' . $fichier[0]['extension']);
    $fichier[0]->supprimer();
  }
}

admin\templates\Pages::parse( 'fichier', array(
  'mes_fichiers' => $file->publier(),
  'nb_fichier'   => $file->count()
));

