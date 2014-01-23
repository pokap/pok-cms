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

use pok\Main;
use pok\Exception;
use pok\Controleur;

if( isset($_GET['view'], $_GET['deplacement'], $_GET['deplacement_id']) && preg_match('`^[a-zA-Z0-9._-]+$`', $_GET['view']) ) 
{
  $load = new Main();
  $load->setPageId( empty($_GET['deplacement_id'])? $_GET['deplacement'] : $_GET['deplacement_id'] );
  // soumet le template
  $load->setTemplate($_GET['view']);
  // cache
  ob_start();
  // résultat
  try {
    $load->afficher();
  }
  catch( Exception $e ) {
    $e->afficher_erreur_dev();
  }
  $final = ob_get_contents();
  ob_end_clean();
  
  // on remet les liens comme il faut
  echo str_replace(
    array('</head>', '</body>', Controleur\Page::url(Controleur\Page::$actuelle['arborescence'])), array(
    '<style type="text/css">
    body { margin-bottom: 165px; }
    </style>
    </head>',
    '<div style="width:100%;height:100px;position:fixed;bottom:0;left:0;background:black;padding:30px 50px;color:#BBB;border-top:5px solid #333;">
  <div style="border-left:5px solid #00F;padding: 10px 50px;">
  <p><b><a href="./template.php?view='.$_GET['view'].'" style="color:#33F;"><u>&laquo; REVENIR À LA FICHE DU TEMPLATE.</u></a></b> <b style="color:red;">&laquo;</b></p>
  <h1 style="margin-top:0;border:none;">Test du template "'.$_GET['view'].'" sur la page "/'.Controleur\Page::$actuelle['arborescence'].'"</h1>
  </div>
</div>
</body>',
    'admin/templatetest.php?view='.$_GET['view'].'&deplacement='.$_GET['deplacement'].'&deplacement_id='.$_GET['deplacement_id'] ),
    $final
  );
}
else
  Controleur\Page::redirect('template.php');