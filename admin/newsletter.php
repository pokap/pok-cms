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

use pok\Apps\Page;
use pok\Apps\Newsletter;
use pok\Apps\Outils\Cfg;

Cfg::import('general');
Cfg::import('newsletter');

if( !empty($_GET['n']) )
  admin\templates\Pages::parse( 'newsletter', array(
    'select_arbo' => Page::publierListeFilter(''),
    'newsletter' => Newsletter::getByNewsletterId($_GET['n'])
  ), 'modif');
else
  admin\templates\Pages::parse( 'newsletter', array(
    'select_arbo' => Page::publierListeFilter(''),
    'newsletters' => Newsletter::fetchAll()
  ), 'list');
