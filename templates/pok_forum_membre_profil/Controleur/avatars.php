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

use pok\Apps\Fichier,
    pok\Apps\FichierModif,
    pok\Apps\Outils\Upload,
    pok\Apps\Outils\Session,
    pok\Controleur\Page AS CPage;

if( Session::connecter() ) 
{
  $donnees = array( 'fichier_nom' => $_SESSION['id'].'-avatar' );
  $file = new Upload('avatar');
  // si c'est pour enregistrer un avatar
	if( $file->existe() ) 
	{
    // poids maximum de 150Ko
    $file->max_size = 153600;
    // extension de fichier autorisé
    $file->extension = array( 'jpg' , 'jpeg' , 'gif' , 'png' );
    // largeur et hauteur max
    $file->max_width = $file->max_height = 100;
    
    $donnees['poids'] = $file->poids();
    $donnees['fichier_description'] = '';
    $donnees['extension'] = $file->ext();
    $donnees['telecharger'] = 0;
    
    $dirmove = ADRESSE_FICHIERS . '/' . $donnees['extension'];
    $copymove = $dirmove . '/' . $donnees['fichier_nom'] . '.' . $donnees['extension'];
    
    // s'il n'y a pas de répertoire "avatars" on le créé
    if( !file_exists($dirmove) )
      mkdir($dirmove);
    // on enregistre
		if( $file->charger($copymove) )
    {
      // on cherche s'il avait déjà mis un avatar avant
      $avatars = Fichier::fetchByFichierNom($donnees['fichier_nom']);
      // s'il y avait des anciens avatars on les supprimes
      foreach( $avatars AS $avatar )
        $avatar->supprimer();
      
      // ajoute la nouvelle avatar
      $avatarn = new FichierModif($donnees);
      if( $avatarn->ajouter() > 0 )
        CPage::redirect('@revenir', array('upload_ok'));
    }
    CPage::redirect('@revenir', array('upload_erreur'));
	}
  // si c'est pour supprimer l'avatar
	elseif( isset($_GET['suppr']) ) 
  {
    $avatarn = new FichierModif();
    $avatarn->addWhere('fichier_nom = "'.$donnees['fichier_nom'].'"');
    $avatarn->supprimer();
    
    CPage::redirect('@revenir');
	}
	else
    CPage::redirect( '@revenir', array('upload_champ_erreur') );
}
