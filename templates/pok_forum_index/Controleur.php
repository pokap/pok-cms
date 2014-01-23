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

namespace templates\pok_forum_index;

use \pok\Apps,
    \pok\Apps\Outils\Session,
    \pok\Apps\Models\Base\Requete\PDOFournie,
    \pok\Controleur\Page,
    \systems\cfg\config;

class Controleur extends \pok\Controleur
{
  // -------------------------------------
  // Void :
  //   invoquer, est toujours appeller et doit toujours exister
  public function __invoke()
  {
    // initialise
    $forums = array();
    // on récupère les sous-pages
    $section_ids = array();

    // -------------------------------------
    //   SECTIONS
    // -------------------------------------
    // on recherche l'ensemble des sous-pages
    $psSql = new Apps\Page(array(array( 'page.arborescence' => Apps\Page::getReferenceClause(array(Page::$actuelle['arborescence'])))));
    $sections = $psSql->publier();
    // on récupère les arbo des sous-pages
    foreach( $sections AS $ids )
      $section_ids[] = Models\Page::getReferenceClause(array($ids['arborescence']));
    
    // -------------------------------------
    //   FORUMS
    // -------------------------------------
    if( !empty($section_ids) )
    {
      $foruSql = new Models\Page(array(array( 'page.arborescence' => $section_ids )));
      $forums = $foruSql->publier();
      // on fusionne l'ensemble des informations
      foreach( $sections AS &$section )
      {
        // initialise
        $section_forums = array();
        // on recherche les forums
        foreach( $forums AS $key => $forum )
        {
          if( strstr($forum['arborescence'], $section['arborescence']) )
          {
            $section_forums[] = $forum;
            // pour éviter de refaire des boucles sans intérêts
            unset($forums[$key]);
          }
        }
        $section['forums'] = array($section_forums);
      }
      unset($section);
    }
    $this->assign('forums', $sections);
  }
}

