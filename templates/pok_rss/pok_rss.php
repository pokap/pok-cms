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

header("Content-Type: text/xml;charset=utf-8");

use pok\Controleur\Page AS CPage,
    systems\cfg\general;

?>
<rss version="2.0">
  <channel>
    <title><?php echo CPage::$mere['page_nom'] ?></title>
    <link>http://<?php echo general\DOMAINE , general\PATH;?></link>
    <description><?php echo CPage::$mere['page_description'] ?></description>
    <language>fr-FR</language>
<?php

foreach( $articles AS $article )
{

?>
    <item>
      <title><?php echo $article['article_titre'] ?></title>
      <link>http://<?php echo general\DOMAINE , general\PATH , CPage::url( CPage::$mere['arborescence'], $article['article_slug'] ) ?></link>
      <description><?php echo strip_tags($article['article_chapo']) ?></description>
      <pubDate><?php echo $article['article_date_creer'] ?></pubDate>
    </item>
<?php

}

?>
  </channel>
</rss>
