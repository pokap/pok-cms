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

namespace templates\pok_forum_membre_profil;

use pok\Apps\Page,
    pok\Apps\Membre,
    pok\Apps\Fichier,
    pok\Apps\Article,
    pok\Controleur\Page AS CPage;

class Controleur extends \pok\Controleur
{
  // ------------------------------------------
  // invoquer, est toujours appeller et doit toujours exister
  public function __invoke()
  {
    $arbo = Page::strSupDernierePage(CPage::$actuelle['arborescence']);
    // initialise
    // contient les infos du membre
    $profilSql = new Membre(array(array(
      'page_membre.arborescence' => array( '=', $arbo )
    )));
    if( $info_profil = $profilSql->publier() )
    {
      $profilSql = new Article(array(array(
        'article.article_titre' => array( '=', 'profil_forum_index' ),
        'article.page_id'       => array( '=', CPage::$actuelle['page_id'] )
      )));
      if( $profil = $profilSql->publier() )
      {
        // récupère les informations
        $informations = ( $tabtx = @unserialize($profil[0]['article_texte']) ) ? $tabtx : array( 'lieu' => '', 'website' => '', 'pseudo' => '', 'signature' => '' );
        $informations['id'] = $profil[0]['article_id'];
        
        unset( $tabtx, $profil, $profilSql );
        
        $this->assign( 'info_profil', $info_profil[0] );
        $this->assign( 'avatar', Fichier::getByFichierNom($info_profil[0]['membre_id'].'-avatar') );
        $this->assign( 'profil', $informations );
      }
      else
        throw new \pok\Exception('Aucun article avec le titre "profil_forum_index" dans la page "'.CPage::$actuelle['arborescence'].'"');
    }
    else
      throw new \pok\Exception('Aucun membre correspond &agrave; la page "'.$arbo.'"');
  }
}
