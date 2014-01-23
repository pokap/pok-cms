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

use pok\Texte,
    pok\Apps\GroupeModif,
    pok\Apps\MembreGroupeModif,
    pok\Apps\Page,
    pok\Apps\PageModif,
    pok\Apps\CatRelationModif,
    pok\Apps\Outils\Session,
    pok\Apps\Outils\Base\Fichier,
    pok\Controleur\Page AS CPage;

if( Session::verifieJeton(0) )
{
  if( isset($_GET['modif']) && !empty($_GET['groupe']) && !empty($_GET['page']) )
  {
    $groupe = new GroupeModif(array(
      'groupe_id' => $_GET['groupe'],
      'couleur'   => $_POST['couleur']
    ));
    // modifie les données du groupe
    $groupe->modifier();
    // modifie la représentation du groupe
    $page = new PageModif(array(
      'page_id'    => $_GET['page'],
      'page_nom'   => htmlspecialchars($_POST['nom']),
      'page_ordre' => $_POST['ordre'],
      'template'   => htmlspecialchars($_POST['template'])
    ));
    $page->modifier();
    // modifie la relation de categorie
    $cat = new CatRelationModif(array( 'cat_id' => $_POST['cat_id'] ));
    $cat->addWhere(CatRelationModif::simpleCritereClause( 'relation_id', array( '=', $_GET['page'] ) ));
    $cat->addWhere(CatRelationModif::simpleCritereClause( 'terms', array( '=', 'page' ) ));
    $cat->modifier();
    
		Fichier::log('<ID:' . $_SESSION['id'] . '> modification groupe n°' . $_GET['groupe']);
    CPage::redirect('admin/groupe.php?modif&g='.$id_groupe);
	}
  elseif( !empty($_GET['head']) && !empty($_GET['groupe']) )
  {
    // enleve toutes les principals pour mettre le nouveau
    $membregroupe = new MembreGroupeModif(array(
      'membre_id' => $_GET['head'],
      'principal' => 0
    ));
    $membregroupe->modifier();
    // ajoute le groupe
    $membregroupe = new MembreGroupeModif(array(
      'membre_id' => $_GET['head'],
      'groupe_id' => $_GET['groupe'],
      'principal' => 1
    ));
    $membregroupe->setReplaceMode(true);
    $membregroupe->ajouter();
    
		Fichier::log('<ID:' . $_SESSION['id'] . '> met groupe n°' . $_GET['groupe'] . ' groupe principal membre <ID:'.$_GET['head'].'>');
    CPage::redirect('admin/groupe.php?modif&ok&g='.$_GET['groupe']);
  }
	else
  {
    // initialise pour la gestion d'erreur
    $groupe_id = 0;
    // récupère les informations de la page des groupes
    $page = new Page(array(array( 'page.page_id' => array( '=', 3 ) )));
    if( $invit = $page->publier() )
    {
    	$nom = htmlspecialchars($_POST['nom']);
      // enregistre la page référence du groupe
      $page = new PageModif(array(
        'page_ordre'       => $_POST['ordre'],
        'page_nom'         => $nom,
        'page_description' => 'Groupe ' . $nom,
        'template'         => htmlspecialchars($_POST['template']),
        'arborescence'     => $invit[0]['arborescence'].'/'.Texte::slcs($nom)
      ));
      $page_id = $page->ajouter();
      if( $page_id > 0 )
      {
        // enregistre dans la catégorie
        $cat = new CatRelationModif(array(
          'relation_id'  => $page_id,
          'categorie_id' => $_POST['cat_id'],
          'terms'        => 'page'
        ));
        $cat->ajouter();
        // puis inscrit le groupe
        $groupe = new GroupeModif(array(
          'page_id' => $page_id,
          'couleur' => $_POST['couleur']
        ));
        $groupe_id = $groupe->ajouter();
      }
    }
		if( $groupe_id > 0 )
    {
      Fichier::log('<ID:' . $_SESSION['id'] . '> creation groupe n°' . $groupe_id);
      CPage::redirect('admin/groupe.php?creerok');
    }
    else
    {
      // on enregistre les informations écrite
      $_SESSION['fgroupe'] = $_POST;
      CPage::redirect('admin/groupe.php?e_creer');
    }
	}
}
CPage::redirect('@revenir');
