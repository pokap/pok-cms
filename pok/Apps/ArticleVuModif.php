<?php
###############################################################################
# LEGAL NOTICE                                                                #
###############################################################################
# Copyright (C) 2008/2010  Florent Denis                                      #
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

namespace pok\Apps;

use pok\Apps\Models\Base\Requete\PDOFournie,
    systems\cfg\config;

class ArticleVuModif extends Models\ArticleVuModif
{
  // -------------------------------------
  // Void :
  //   Met à jour le dernier article vu par rapport à son référent.
  //   Il se base toujours sur l'article.
  public function ajouter()
  {
    // connexion SQL
    PDOFournie::autoConnexion();
    
    if( $this->av_poster[0] === null ) {
      $select = '(SELECT pvp.av_poster FROM '.config\PREFIX.ArticleVu::TABLE.' AS pvp WHERE pvp.av_membre_id = '.self::getAttributQuote($this->av_membre_id).' AND pvp.av_reference_id = '.self::getAttributQuote($this->av_reference_id).')';
      $this->av_poster = array( '(SELECT CASE WHEN '.$select.' IS NULL THEN 0 ELSE '.$select.' END)', PDOFournie::NOT_QUOTE );
    }
    if( $this->av_article_id[0] === null ) {
      $select = '(SELECT article_id FROM '.config\PREFIX.Article::TABLE.' WHERE article_parent = '.self::getAttributQuote($this->av_reference_id).' ORDER BY article_id DESC LIMIT 1)';
      $this->av_article_id = array( '(SELECT CASE WHEN '.$select.' IS NULL THEN '.self::getAttributQuote($this->av_reference_id).' ELSE '.$select.' END)', PDOFournie::NOT_QUOTE );
    }
    $this->setReplaceMode(true);
    // récupére les informations de la base de donnée
    parent::ajouter();
    // déconnexion SQL
    PDOFournie::deconnexionAt(0);
  }
  
  // -------------------------------------
  // Void :
  //   Met à jour les informations
  public function modifier()
  {
    // connexion SQL
    PDOFournie::autoConnexion();
    parent::modifier();
    // déconnexion SQL
    PDOFournie::deconnexionAt(0);
  }
  
  // -------------------------------------
  // Void :
  //   supprime
  public function supprimer()
  {
    // connexion SQL
    PDOFournie::autoConnexion();
    parent::supprimer();
    // déconnexion SQL
    PDOFournie::deconnexionAt(0);
  }
}