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

use pok\Apps\Outils\Session,
    pok\Apps\Outils\Base\Fichier,
    pok\Apps\Models\Base\Requete\PDOFournie,
    pok\Controleur\Page AS CPage;

function save()
{
  // structure sql
  $structure = '';
  PDOFournie::autoConnexion();
  
  $show = PDOFournie::$INSTANCE->query( 'SHOW TABLES', PDO::FETCH_NUM );
  // on regarde tout les tables
  foreach( $show AS $table_in_bd )
  {
    // récupère la structure de la table
    $donnee_structure = PDOFournie::$INSTANCE->query( 'SHOW CREATE TABLE '.$table_in_bd[0], PDO::FETCH_NUM );
    $structure .= "\n\nDROP TABLE IF EXISTS `" . $table_in_bd[0] . "`;\n" . $donnee_structure[0][1] . ";\n\n";
    
    // récupère les données de la table
    foreach( PDOFournie::$INSTANCE->query( 'SELECT * FROM '.$table_in_bd[0], PDO::FETCH_NUM ) AS $all_donnees )
    {
      // all insert
      $structure .= 'INSERT INTO ' . $table_in_bd[0] . ' VALUES (';
      foreach( $all_donnees AS $donnees ) {
        // hack
        $structure .= "'" . addslashes($donnees) . "',";
      }
      $structure = substr( $structure, 0, -1 ) . ");\n";
    }
  }
  PDOFournie::deconnexionAt(0);
  
  return $structure;
}

if( Session::verifieJeton(0) )
{
	$structure = save();
  
  // enristrement
  if( file_put_contents( ADRESSE_SAVE . '/' . $_SERVER['REQUEST_TIME'] . '.txt', $structure ) )
  {
    Fichier::log('<ID:' . $_SESSION['id'] . '> enregistre la base de donnée');
    CPage::redirect('admin/general.php?saveok');
  }
  else
    CPage::redirect('admin/general.php?saverror');
}
CPage::redirect('@revenir');
