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
    pok\Apps\Ressource,
    pok\Apps\Page,
    pok\Apps\PageModif,
    pok\Apps\CatRelationModif,
    pok\Apps\Outils\Session,
    pok\Apps\Outils\Base\Fichier,
    pok\Controleur\Page AS CPage;

// droit réserver aux admin
if( Session::verifieJeton(0) )
{
  // change le lu / non-lu
  if( isset($_GET['nonlu']) && !empty($_GET['courant']) && isset($_GET['page']) && Page::format($_GET['page']) )
  {
    if( $invit = Page::getByPageId($_GET['courant']) )
    {
      $invit['nonlu'] = ($_GET['nonlu'])? false : true;
      $invit->modifier();
      // log
      Fichier::log('<ID:' . $_SESSION['id'] . '> modification -nonlu- dossier n°' . $_GET['courant']);
    }
  }
  // Les ressources
  elseif( !empty($_GET['ressources']) )
  {
    foreach( $_POST['sources'] AS $id => $template ) {
      Ressource::developper( $_GET['ressources'], $id, $template );
    }
    CPage::redirect('@revenir');
  }
  elseif( !empty($_GET['courant']) && isset($_GET['page']) && Page::format($_GET['page']) )
  {
    $courant = intval($_GET['courant']);
    if( $courant > 1 && $invit = Page::getByPageId($courant) )
    {
      if( !empty($_POST['resume']) && Page::format($_POST['resume']) )
      {
        $plusde2page = Page::issetPlus2Page($invit['arborescence']);
        
        $_POST['arborescence'] = Page::strSupDernierePage($invit['arborescence']);
        
        if( $plusde2page )
          $_POST['arborescence'] .= '/' . $_POST['resume'];
        else
          $_POST['arborescence'] = $_POST['resume'];
      }
      else
      {
        if( $invit['arborescence'] !== '' )
          $_POST['arborescence'] = $invit['arborescence'] . '/' . Texte::slcs($_POST['page_nom']);
        else
          $_POST['arborescence'] = Texte::slcs($_POST['page_nom']);
      }
      $_POST['page_nom'] = htmlspecialchars($_POST['page_nom']);
      $_POST['page_description'] = htmlspecialchars($_POST['page_description']);
      // ------------------------------
      // MODIFIER
      if( isset($_GET['modif']) )
      {
        $page = new PageModif(array_merge( array( 'page_id' => $courant ), $_POST ));
        $page->modifier();
        // modifie la relation de categorie
        if( isset($_POST['cat_id']) )
        {
          $cat = new CatRelationModif(array( 'cat_id' => $_POST['cat_id'] ));
          $cat->addWhere(CatRelationModif::simpleCritereClause( 'relation_id', array( '=', $courant ) ));
          $cat->addWhere('terms = "page"');
          $cat->modifier();
        }
        // log
        Fichier::log('<ID:' . $_SESSION['id'] . '> modification page n°' . $courant);
        CPage::redirect('admin/page.php?page=' . Page::strSupDernierePage($_POST['arborescence']));
      }
      // ------------------------------
      // CREER
      else
      {
        $page = new PageModif($_POST);
        // créer la page
        $info = $page->ajouter();
        // s'il n'y a pas d'erreur
        if( $info > 0 )
        {
          // ajoute la relation de categorie
          if( !isset($_POST['cat_id']) )
            $_POST['cat_id'] = 1;
            
          $cat = new CatRelationModif(array(
            'cat_id'      => $_POST['cat_id'],
            'relation_id' => $info,
            'terms'       => 'page'
          ));
          $cat->ajouter();
          // log
          Fichier::log('<ID:' . $_SESSION['id'] . '> creation page n°' . $info);

          CPage::redirect('admin/page.php?createok&page=' . $_GET['page']);
        }
        else
        {
          // on enregistre les informations écrite
          $_SESSION['fdossier'] = $_POST;
          // si la page existe déjà
          if( $info === PageModif::EXISTE )
            CPage::redirect('admin/page.php?e_create&exist&page=' . $_GET['page']);
          // autres erreurs
          else
            CPage::redirect('admin/page.php?e_create&error&page=' . $_GET['page']);
        }
      }
    }
	}
}
CPage::redirect('@revenir');
