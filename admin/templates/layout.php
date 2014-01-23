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

use pok\Controleur\Page;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
  <head>
    <title>Administration</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link rel="stylesheet" media="screen" type="text/css" href="templates/style.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Page::getAdresse() ?>/web/js/markitup/skins/markitup/style.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Page::getAdresse() ?>/web/js/markitup/sets/html_pok/style.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Page::getAdresse() ?>/web/js/jquery-autocomplete/jquery.autocomplete.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Page::getAdresse() ?>/web/js/lightbox/lightbox.css" />
    <link rel="icon" type="image/png" href="images/favicon.png" />
    <script type="text/javascript" src="<?php echo Page::getAdresse() ?>/web/js/lightbox/lightbox.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo Page::getAdresse() ?>/web/js/jquery-autocomplete/jquery.autocomplete.pack.js"></script>
    <script type="text/javascript">
    function verif( url ) {
      if( confirm( 'Attention cette opération est irréversible et détruira toutes les données qu\'il contient\nainsi que ses sous-données.\n\nêtes-vous sûr de vouloir le supprimer ?' ) ) {
        document.location.href = url;
      }
    }
    function sur( url ) {
      if( confirm( 'êtes-vous sûr de vouloir effectuer cette opération ?' ) ) {
        document.location.href = url;
      }
    }
    </script>
  </head>
  <body>
<?php
  
// affiche le menu si on ne le cache pas ! logique :p
if( !admin\templates\Pages::$hidden_menu )
{

?>
    <div class="title">&laquo;&laquo; <a href="../">Switch to index</a> | &laquo; <a href="./index.php">Back to Dashboard</a> <div class="on">Panneau Administrateur</div></div>
    <div id="menu">
      <ul id="onglets">
<?php

  $menu_array = array( 
    'Configuration' => 'general',
    'Publication' => 'article',
    'Accueil' => 'index',
    'Cat&eacute;gorie' => 'categorie',
    'Page' => 'page',
    'Membre' => 'membre',
    'Groupe' => 'groupe',
    'Fichier' => 'fichier'
  );
  foreach( $menu_array AS $nom => $fichier )
  {
    if( $template_nom == $fichier )
    {
      echo '<li class="active"><a href="',$fichier,'.php">',$nom,'</a></li>';
    }
    else
    {
      echo '<li><a href="',$fichier,'.php">',$nom,'</a></li>';
    }
  }

?>
      </ul>
    </div>
<?php

// fin de la partie de cache-cache
}

?>
    <div class="content">
      <?php include( __DIR__ . '/pages/' . $template . '.php' );?>
    </div>
  </body>
</html>
