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
use pok\Apps\Groupe;
use pok\Apps\Membre;
use pok\Apps\MembreGroupe;
use pok\Apps\Droit;
use pok\Apps\Outils\Cfg;
use systems\cfg\general;

Cfg::import('general');

function get_list_page( $page, $groupe )
{
  if( isset( $_GET['modif'], $_GET['g'] ) )
  {
    $get_origine = 'modif&amp;g='.$_GET['g'].'&amp;';
    $nb_membre_groupe = $groupe->getNumGroupeMembre( $groupe->groupe_id );
    $nb_page = $nb_membre_groupe == 0 ? 1 : ceil( $nb_membre_groupe / 30 );
  }
  else
  {
    $get_origine = '';
    $nb_page = ceil( $groupe->getNumGroupe() / 30 );
  }

  $list = pok\getListPage( $page, $nb_page );

  if( isset( $_GET['trie'], $_GET['champ'] ) )
    $gets = 'trie='.$_GET['trie'].'&amp;champ='.$_GET['champ'].'&amp;';
  else
    $gets = '';

  foreach( $list AS $num ) {
    if( $num != '...' )
      echo '<a href="groupe.php?'.$get_origine.$gets.'page_liste_groupe=',$num,'">',$num,'</a> ';
    else
      echo $num,' ';
  }
}

$categorie = new Categorie(array(array(
  Categorie::TABLE.'.taxon' => array( array( '=', Categorie::TAXON_BOTH ), array( '=', Categorie::TAXON_PAGE ) )
)));
$cat_list = $categorie->publier();

$tab_mode = array( Membre::MEMBRE => 'Membre', Membre::ADMIN => 'Admin', Membre::BANNIE => 'Bannie' );

if( isset( $_GET['droit'], $_GET['g'] ) && !empty($_GET['g']) )
{
  /* Pour ajouter un droit, vous devez mettre son nom sql dans le tableau $tab_droits
    et ensuite le rajouter dans le html : <th> avec la boucle "for"
    puis, modifier "create_droit.php et la fonction "upStatut" dans "Droits.class.php",
    la fonction "getCategorieRules" dans "Categorie.class.php"
    et pour finir tout autres fonctions de la lib pour qu'ils renvoient cette nouvelle valeur
  */
  $rules = new Droit(array(array( 'groupe_id' => array( '=', $_GET['g'] ) )));
  $cat_rules = $rules->publierCategorie();
  
  $all_rules = array();
  foreach( $cat_rules AS $droit ) {
    $all_rules[$droit['cat_id']] = $droit;
  }
  
  $tab_droits = array( 'vlp', 'euna', 'raa', 'blr', 'etla', 'stla', 'ssa', 'mda' );
  
  admin\templates\Pages::parse( 'groupe', array(
    'tab_droits' => &$tab_droits,
    'cat_list'   => &$cat_list,
    'all_rules'  => &$all_rules
  ), 'droit');
}
elseif( isset( $_GET['modif'], $_GET['g'] ) && !empty($_GET['g']) )
{
  $clause = array(array(
    Groupe::TABLE.'.groupe_id' => array( '=', $_GET['g'] )
  ));
  $groupe = new Groupe($clause);
  $membre_groupe = new MembreGroupe($clause);
  
  admin\templates\Pages::parse( 'groupe', array(
    'groupe'        => $groupe->publier(),
    'list_membres'  => $membre_groupe->publierMembre(),
    'nb_membre'     => $membre_groupe->count(),
    'cat_list'      => &$cat_list,
    'tab_mode'      => &$tab_mode
  ), 'modif');
}
else
{
  // on récupère les informations s'il y a eu une erreur de formulaire
  if( isset($_SESSION['fgroupe']) )
  {
    $info_groupe = $_SESSION['fgroupe'];
    unset($_SESSION['fgroupe']);
  }
  else
  {
    $info_groupe = array(
      'cat_id'    => 0,
      'nom'       => '',
      'couleur'   => '000000',
      'ordre'     => 0,
      'template'  => general\TEMPLATE_GROUPE
    );
  }

  $groupe = new Groupe();
  $groupe->setAlias('page');
  
  admin\templates\Pages::parse( 'groupe', array(
    'list_groupe' => $groupe->publier(),
    'nb_groupe'   => $groupe->count(),
    'info_groupe' => &$info_groupe,
    'cat_list'    => &$cat_list,
    'page'        => (isset($_GET['page']))? intval($_GET['page']) : 1
  ), 'list');
}
