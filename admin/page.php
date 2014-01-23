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

use pok\Apps\Categorie;
use pok\Apps\Page;

$dossier_arbo = isset($_GET['page']) ? $_GET['page'] : '';

// REQUETE
$pageSql = new Categorie(array(array(
  'categorie.taxon' => array( array( '=', Categorie::TAXON_BOTH ), array( '=', Categorie::TAXON_PAGE ) )
)));
$cat_list = $pageSql->publier();
// REQUETE
// infos sur les sous-pages
$pSql = new Page(array(array('page.arborescence' => Page::getReferenceClause(array($dossier_arbo)))));
$rep_arbo_absolu = $pSql->publier();
// REQUETE
$myRepertoire = Page::getByArborescence($dossier_arbo);

// REQUETE
$clause = array();
foreach( Page::getFilAriane($dossier_arbo) AS $arboParent ) {
  $clause[] = array( '=', $arboParent );
}
$pageSql = new Page(array(array( Page::TABLE.'.arborescence' => $clause )));
$arborescence = $pageSql->publier();

// --------------------------------------------------------------------------
//    RESSOURCES
// --------------------------------------------------------------------------
if( isset($_GET['ressources']) && !empty($_GET['tpl']) )
{
  $nb_subdos = count($arborescence);
  $info_subdos = ( $nb_subdos > 1 )? $arborescence[$nb_subdos - 1]['page_id'] : 1;
  
  admin\templates\Pages::parse( 'page', array(
    'info_subdos'  => &$info_subdos,
    'arborescence' => &$arborescence,
    'myRepertoire' => &$myRepertoire
  ), 'ressource');
}
// --------------------------------------------------------------------------
//    MODIFICATION
// --------------------------------------------------------------------------
elseif( isset($_GET['modifier'], $_GET['page'], $_GET['return']) )
{
  admin\templates\Pages::parse( 'page', array(
    'infos'        => &$myRepertoire,
    'arborescence' => &$arborescence,
    'cat_list'     => &$cat_list
  ), 'modif');
}
else
{
  // on récupère les informations s'il y a eu une erreur de formulaire
  if( isset($_SESSION['fdossier']) )
  {
    $info_dossier = $_SESSION['fdossier'];
    unset($_SESSION['fdossier']);
  }
  else
  {
    $info_dossier = array(
      'cat_id'           => $myRepertoire['cat_id'],
      'page_nom'         => '',
      'page_description' => '',
      'page_ordre'       => 0,
      'template'         => '',
      'arborescence'    => &$arborescence,
      'nonlu'            => 0
    );
  }
  
  admin\templates\Pages::parse( 'page', array(
    'rep_arbo_absolu' => &$rep_arbo_absolu,
    'myRepertoire'    => &$myRepertoire,
    'info_dossier'    => &$info_dossier,
    'arborescence'    => &$arborescence,
    'cat_list'        => &$cat_list,
    'dossier_arbo'    => &$dossier_arbo
  ), 'list');
}
