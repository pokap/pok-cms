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

use pok\Apps\Page,
    pok\Apps\Membre,
    pok\Controleur\Droit;

// -------------------------------------
// String :
//   Limite un texte en caractère
function strlimit( $texte, $limit )
{
  $taille = strlen( $texte );
  return $taille > $limit ? substr( $texte, ( $taille - $limit ) ) : $texte;
}

// POUR LE PROFIL
if( $_WORK == 'edit' && isset($_POST['lieu'], $_POST['website'], $_POST['signature']) )
{
  $membre = Membre::getByMembreId($_SESSION['id']);
  
  if( !empty($membre) && Page::strSupDernierePage($page['arborescence']) == $membre['membre_arborescence'] )
  {
    $_POST['website'] = filter_var($_POST['website'], FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED);
    
    $_POST['article_texte'] = serialize(array(
      'lieu'      => htmlspecialchars( strlimit( $_POST['lieu'], 30 ), ENT_QUOTES ),
      'website'   => htmlspecialchars( $_POST['website'], ENT_QUOTES ),
      'signature' => nl2br(htmlspecialchars( strlimit( $_POST['signature'], 250 ), ENT_QUOTES ))
    ));
    // on donne le droit de modifier les articles
    Droit::$etla = true;
  }
}
// empêche le post
else
{
  $action = false;
}
