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

foreach( $forums AS $forum )
{
  
?>
<tr>
  <td><a href="<?php echo Page::url($forum['arborescence']) ?>"><?php echo $forum['page_nom'] ?></a></td>
  <td align="center"><?php echo $forum['count_post_first'] ?></td>
  <td align="center"><?php echo $forum['count_post_second'] ?></td>
  <td align="center">
<?php
  
  if( $forum['last_id'] != NULL )
  {
    echo '<b><a href="',Page::url($forum['last_arbo_auteur']),'" style="color: #',$forum['last_couleur'],'">',$forum['last_nom_auteur'],'</a></b>';

    if( $forum['last_parent'] == 0 )
      echo '<a href="',Page::url($forum['arborescence'], $forum['last_slug'] ),'">';
    else
      echo '<a href="',Page::url($forum['arborescence'], $forum['last_parent_slug'] ),'">';
?>
    <img src="<?php echo Page::getAdresse() ?>web/images/pok_forum_index/lastpost.png" title="Voir le dernier article" alt="last" /></a>
    <br /><small><?php echo $forum['last_date_creer'] ?></small>
<?php
  
  }
  else
    echo 'n/a';

?>
  </td>
</tr>
<?php

}

