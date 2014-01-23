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
    pok\Apps\Fichier,
    pok\Apps\Outils\Cfg,
    pok\Apps\Outils\Session,
    pok\Apps\Outils\Base\Fichier AS BFichier,
    pok\Controleur\Page AS CPage,
    pok\Controleur\Droit AS CDroit;

// Base du CMS
// La config "general" est automatiquement inclue
require('pok/Main.php');
// charge l'autoload
Main::autoLoad(array(
  ADRESSE_BASE
));

if( !Cfg::import('config') || empty($_GET['file']) || !Session::connecter() ) CPage::redirect('@revenir');

$fSql = new Fichier(array(array( 'fichier_id' => array( '=', $_GET['file'] ) )))
if( !$ifichier = $fSql->publier() )
  CPage::redirect('@revenir');
// informations
$fichier = $ifichier[0];
// sert à rien
unset( $fSql, $ifichier );
if( empty($fichier) ) CPage::redirect('@revenir');

$monfichier = ADRESSE_FICHIERS . '/' . $fichier['extension'] . '/' . $fichier['fichier_id'] . '.' . $fichier['extension'];

if( !file_exists($monfichier) )
  CPage::redirect('@revenir');
else
{
  /* Après avoir vérifié que le fichier existe (l'id est bien dans la BDD) et
  après avoir sélectionné les informations sur le fichier dans la BDD (dans $mon_fichier) */
  $fichier['telecharger']++;
  $fichier->modifier();
  
  // Création des headers, pour indiquer au navigateur qu'il s'agit d'un fichier à télécharger
  header('Content-Description: File Transfer');
  header('Content-Type: application/octet-stream');
  header('Content-Transfer-Encoding: binary'); //Transfert en binaire (fichier)
  header('Content-Disposition: attachment; filename="' . $fichier['fichier_nom'] . '"'); //Nom du fichier
  header('Content-Length: ' . $fichier['poids']); //Taille du fichier
  
  // Envoi du fichier dont le chemin est passé en paramètre
  readfile($monfichier);
}
