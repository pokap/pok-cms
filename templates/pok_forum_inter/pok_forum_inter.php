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

use templates\pok_accueil\Controleur\fonctions;
use pok\Controleur\Page;
use pok\Template;

Template::importerFonctions('pok_accueil');

/*
  On inclue le template _header simplement.
  Si vous voulez héritez du controlleur du template _header,
  vous devez l'inclure dans le controlleur de ce template.
*/
Template::integrer('_pok_header');

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
      <table style="width:100%;margin-top:20px;">
        <tr>
          <th><a href="<?php echo Page::url(Page::$actuelle['arborescence']) ?>"><?php echo Page::$actuelle['page_nom'] ?></a></th>
          <th style="width:45px;">Sujets</th>
          <th style="width:65px;">Réponses</th>
          <th style="width:125px;">-</th>
        </tr>
        <?php Template::integrer('pok_forum_index/inter', array( 'forums' => $forums )) ?>
      </table>
    </div>
<?php

/* -------------------------------------
  On inclue le pied de la page
*/
Template::integrer('_pok_footer', array( 'chrono' => $chrono ));

