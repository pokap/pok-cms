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
    pok\Apps\Membre,
    pok\Apps\MembreModif,
    pok\Apps\MembreGroupeModif,
    pok\Apps\Page,
    pok\Apps\PageModif,
    pok\Apps\CatRelationModif,
    pok\Apps\Outils\Session,
    pok\Apps\Outils\Base\Fichier,
    pok\Controleur\Page AS CPage,
    systems\cfg\config;

if( Session::verifieJeton(0) )
{
	if( !empty($_GET['m']) )
  {
    // modifie le membre
    if( $membre = Membre::getByMembreId($_GET['m']) )
    {
      if( !empty($_POST['mdp']) )
        $membre['membre_mdp'] = Membre::scriptmdp($_POST['mdp']);
      
      $membre['membre_pseudo'] = htmlspecialchars($_POST['pseudo']);
      $membre['membre_email'] =  $_POST['email'];
      $membre['membre_inscrit'] = $_POST['inscrit'];
      $membre['statut'] = $_POST['statut'];
      $membre['valide'] = $_POST['valide'];
      $membre->modifier();
      
      // modifie la page du membre
      if( $page = Page::getByPageId($membre['page_id']) )
      {
        $arborescence = Page::strSupDernierePage($page['arborescence']) . ( Page::issetPlus2Page($page['arborescence'])? '/' : '' ) . Texte::slcs($membre['membre_pseudo']);
        
        $page['page_nom'] = $membre['membre_pseudo'];
        $page['arborescence'] = $arborescence;
        $page['template'] = $_POST['template'];
        
        $page->modifier();
      }
      if( !empty($_POST['cat_id']) )
      {
        $cat = new CatRelationModif(array( 'cat_id' => $_POST['cat_id'] ));
        $cat->addWhere('relation_id = ' . $membre['page_id']);
        $cat->addWhere('terms = "page"');
        $cat->modifier();
      }
    }
    Fichier::log('<ID:' . $_SESSION['id'] . '> modification membre n°' . $_GET['m']);
    CPage::redirect('admin/membre.php?modif&m=' . $_GET['m']);
	}
	else
  {
    $membre_id = 0;
    $pseudo = htmlspecialchars($_POST['pseudo']);
    // modifie la page du membre
    $pageSql = new Page(array(array( 'page_id' => array( '=', 2 ) )));
    $page = $pageSql->publier();
    
    $page_membre = new PageModif(array(
      'page_ordre'        => $_POST['ordre'],
      'page_nom'          => $pseudo,
      'page_description'  => 'Profil de ' . $pseudo,
      'template'          => $_POST['template'],
      'arborescence'      => $page[0]['arborescence'] . '/' . Texte::slcs($pseudo)
    ));
    $page_id = $page_membre->ajouter();
    if( $page_id > 0 )
    {
      $cat = new CatRelationModif(array( 
        'cat_id'      => $_POST['cat_id'],
        'relation_id' => $page_id,
        'terms'       => 'page',
      ));
      $cat->ajouter();
      
      $membre = new MembreModif(array(
        'membre_pseudo' => $pseudo,
        'page_id'       => $page_id,
        'membre_mdp'    => Membre::scriptmdp($_POST['mdp']),
        'membre_email'  => $_POST['email'],
        'membre_inscrit'=> date('Y-m-d H:i:s'),
        'membre_visite' => date('Y-m-d H:i:s'),
        'statut'        => $_POST['statut'],
        'valide'        => 0
      ));
      if( $membre_id = $membre->ajouter() )
      {
        $groupe = new MembreGroupeModif(array(
          'membre_id' => $membre_id,
          'groupe_id' => 2,
          'principal' => 1
        ));
        $groupe->ajouter();
      }
    }
    
    $membre = new Membre();
    $nbpage = ceil( $membre->count() / 30 );

    if( $membre_id > 0 )
    {
      Fichier::log('<ID:' . $_SESSION['id'] . '> creation membre n°' . $membre_id);
      CPage::redirect('admin/membre.php?page='.$nbpage);
    }
    else
    {
      // on enregistre les informations écrite
      $_SESSION['fmembre'] = $_POST;
      CPage::redirect('admin/membre.php?erreur='.$membre_id.'&page='.$nbpage);
    }
	}
}
CPage::redirect('admin/membre.php');
