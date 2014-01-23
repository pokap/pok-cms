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

pok\Template::importerFonctions('pok_accueil');

use templates\pok_accueil\Controleur\fonctions;
use pok\Controleur\Page;
use pok\Controleur\Droit;
use pok\Template;
use pok\Apps\Outils\Session;

/*
  On inclue le template _header simplement.
  Si vous voulez héritez du controlleur du template _header,
  vous devez l'inclure dans le controlleur de ce template.
*/

Template::integrer('_pok_header');

/* -------------------------------------
  Permet de vérifier si on a bien à faire à un article.
  Dans le controller on demande d'afficher un post qui à la référence 0, si on demande un commentaire,
  il n'aura pas la référence 0, donc single_article sera vide.
*/
?>
    <p>
      <a href="<?php echo Page::url(Page::$mere['arborescence']);?>"><?php echo Page::$mere['page_nom'];?></a>
<?php

// fil d'arine central
fonctions\affiche_fil_ariane($arborescence);

?>
    </p>
  </div>
  <div class="content">
    <div class="page">
      <div id="corps">

    <h3>Liste des membres :</h3>

    <ul>
<?php

/* -------------------------------------
  Liste des membres récupérer par page
*/
foreach( $liste_membre AS $membre )
{

?>
      <li><a href="<?php echo Page::url($membre['arborescence']) ?>"><?php echo $membre['page_nom'] ?></a></li>
<?php
}
?>
    </ul>
  </div>
<?php

/* -------------------------------------
  On inclue le pied de la page
*/
Template::integrer('_pok_footer', array( 'chrono' => $chrono ));
