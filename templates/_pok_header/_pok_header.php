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

// initialise
use pok\Controleur\Page,
    systems\cfg\general;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
  <head>
    <title><?php echo Page::$actuelle['page_nom'];?> :: CMS Pok</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link rel="stylesheet" media="screen" type="text/css" href="<?php echo Page::getAdresse() ?>web/css/style.css" />
    <link rel="alternate" type="application/rss+xml" title="RSS" href="<?php echo Page::url('rss') ?>" />
    <link rel="icon" type="image/png" href="<?php echo Page::getAdresse() ?>web/favicon.png" />
  </head>
<?php
if( isset($_SESSION['statut']) && $_SESSION['statut'] === pok\Apps\Membre::ADMIN )
{
?>
  <body style="margin-top:25px;">
  <div class="baradmin">
    <a href="<?php echo Page::getAdresse() ?>admin/index.php">Adminitration</a>
    | <a href="<?php echo Page::getAdresse() ?>admin/article.php?page=<?php echo Page::$actuelle['page_id'] ?>&amp;cat=">Contenu</a>
    | <a href="<?php echo Page::getAdresse() ?>admin/page.php?page=<?php echo Page::$actuelle['arborescence'] ?>">Arborescence</a>
  </div>
<?php
}
else
{
?>
  <body>
<?php
}
?>
  <div id="header">
    <h1>POK</h1>
