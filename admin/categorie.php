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

$taxon_list = array( Categorie::TAXON_PAGE, Categorie::TAXON_ARTICLE, Categorie::TAXON_BOTH, Categorie::TAXON_TAG );

if( isset($_GET['modifier']) )
{
  admin\templates\Pages::parse( 'categorie', array(
    'taxon_list' => &$taxon_list
  ), 'modif');
}
else
{
  $img_trie = array(
    'cat_id' => '',
    'cat_nom' => '',
    'taxon' => ''
  );
  $trie = array();
  $trie['DESC'] = 'ASC';
  $trie['ASC'] = 'DESC';
  
  if( isset($_GET['trie']) )
  {
    if( $_GET['trie'] == 'DESC' ) {
      $img_trie[$_GET['champ']] = '<img src="images/s_desc.png" title="trier croissant" alt="trie" />';
    }
    else {
      if( isset($_GET['champ']) ) {
        $img_trie[$_GET['champ']] = '<img src="images/s_asc.png" title="trier décroissant" alt="trie" />';
      }
    }
  }
  else
  {
    $_GET['trie'] = 'ASC';
    $_GET['champ'] = 'cat_id';
  }
  
  // REQUETE
  $catSql = new Categorie();
  $catSql->addOrder( 'categorie.' . $_GET['champ'] . ' ' . $_GET['trie'] );
  $cat_list = $catSql->publier();
  
  admin\templates\Pages::parse( 'categorie', array(
    'trie'        => &$trie,
    'cat_list'    => &$cat_list,
    'img_trie'    => &$img_trie,
    'taxon_array' => &$taxon_list
  ), 'list');
}
?>