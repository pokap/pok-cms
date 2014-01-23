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
use pok\Apps\Membre;
use pok\Apps\MembreGroupe;
use pok\Apps\MembreNewsletter;
use pok\Apps\Newsletter;
use pok\Apps\Outils\Cfg;
use systems\cfg\general;

Cfg::import('general');

$categorie = new Categorie(array(array(
  'taxon' => array( array( '=', Categorie::TAXON_BOTH ), array( '=', Categorie::TAXON_PAGE ) )
)));
$cat_list = $categorie->publier();

$tab_mode = array( Membre::MEMBRE => 'Membre', Membre::ADMIN => 'Admin', Membre::BANNIE => 'Bannie' );

$erreur = array();
// -------------------------------------------------
// si on prendre une identité
if( isset($_GET['prisedidentite']) )
{
  if( $_GET['m'] > 1 )
  {
    //$info_membre_pris = end($membres->boucleMembre('prise',array(array('ID'=>$_GET['m']))));
    // on prend l'ID du membre
    $_SESSION['id'] = $_GET['m'];
    //$_SESSION['pseudo'] = $info_membre_pris['pseudo'];
    //$_SESSION['sys'] = $info_membre_pris['sys_id'];
    //$_SESSION['last_visit'] = $info_membre_pris['user_visit'];
    $erreur = '<div class="avertissement">Vous venez de prendre l\'identité (ID) du membre n°' . $_SESSION['id'] . '</div>';
    //unset($info_membre_pris);
  }
  else
    $erreur = '<div class="erreur">Vous ne pouvez pas prendre l\'identité du premier membre.</div>';
}

if( isset($_GET['modif']) && !empty($_GET['m']) )
{
  $membreSql = new Membre(array(array(
    Membre::TABLE.'.membre_id' => array( '=', $_GET['m'] ) )
  ));
  
  $groupeSql = new MembreGroupe(array(array(
    MembreGroupe::TABLE.'.membre_id' => array( '=', $_GET['m'] ),
    MembreGroupe::TABLE.'.principal' => array( '=', 0 )
  )));
  
  admin\templates\Pages::parse( 'membre', array(
    'membre'    => $membreSql->publier(),
    'groupes'   => $groupeSql->publierGroupe(),
    'tab_mode'  => &$tab_mode,
    'erreur'    => &$erreur,
    'cat_list'  => &$cat_list
  ), 'modif');
}
elseif( isset($_GET['newsletter']) && !empty($_GET['m']) )
{
  $nlSql = new MembreNewsletter(array(array(
    MembreNewsletter::TABLE.'.membre_id' => array( '=', $_GET['m'] )
  )));
  
  $nSql = new Newsletter();
  
  admin\templates\Pages::parse( 'membre', array(
    'membre'     => Membre::getByMembreId($_GET['m']),
    'membre_newsletters' => $nlSql->publierNewsletter(),
    'newsletter' => $nSql->publier(),
    'tab_mode'   => &$tab_mode,
    'erreur'     => &$erreur,
    'cat_list'   => &$cat_list
  ), 'newsletter');
}
else
{
  // on récupère les informations s'il y a eu une erreur de formulaire
  if( isset($_SESSION['fmembre']) )
  {
    $info_membre = $_SESSION['fmembre'];
    unset($_SESSION['fmembre']);
  }
  else
  {
    $info_membre = array(
      'pseudo'     => '',
      'mdp'        => '',
      'email'      => '',
      'statut'     => Membre::MEMBRE,
      'newsletter' => 0,
      'cat_id'     => 2,
      'ordre'      => 0,
      'template'   => general\TEMPLATE_MEMBRE
    );
  }
  
  $img_trie = array(
    'id' => '',
    'pseudo' => '',
    'email' => '',
    'inscrit' => '',
    'statut' => '',
    'valide' => ''
  );
  $trie = array();
  $trie['DESC'] = 'ASC';
  $trie['ASC'] = 'DESC';
  
  $membre = new Membre();
  $membre->setAlias('page');
  
  if( isset($_GET['trie']) )
  {
    if( $_GET['trie'] == 'DESC' )
    {
      $img_trie[$_GET['champ']] = '<img src="images/s_desc.png" title="trier croissant" alt="trie" />';
    }
    else
    {
      if( isset($_GET['champ']) )
      {
        $img_trie[$_GET['champ']] = '<img src="images/s_asc.png" title="trier décroissant" alt="trie" />';
      }
    }
  }
  else
  {
    $_GET['trie'] = 'ASC';
  }
  $list_membres = $membre->publier();
  
  admin\templates\Pages::parse( 'membre', array(
    'nb_membres'   => $membre->count(),
    'list_membres' => $membre->publier(),
    'tab_mode'     => &$tab_mode,
    'info_membre'  => &$info_membre,
    'cat_list'     => &$cat_list,
    'img_trie'     => &$img_trie,
    'trie'         => &$trie,
    'erreur'       => &$erreur,
    'page'         => (isset($_GET['page']))? $_GET['page'] : 1
  ), 'list');
}
