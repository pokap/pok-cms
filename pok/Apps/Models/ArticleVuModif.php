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

namespace pok\Apps\Models;

class ArticleVuModif extends Base\Modifier
{
  // Int :
  //   Identifiant du membre
  protected $av_membre_id = array(null);
  //   Identifiant de l'article
  protected $av_reference_id = array(null);
  //   Identifiant du dernier sous-article vu
  protected $av_article_id = array(null);
  // Boolean :
  //   Nombre sous-article de l'article
  protected $av_poster = array(null);
  
  // -------------------------------------
  // Array :
  //   Renvoie la les attributs de la classe sauf donnees, soit la liste des champs de la table
  public static function getChamps() {
    return self::getChampsBy( get_class_vars(__CLASS__) );
  }
  
  // -------------------------------------
  // Int :
  //   Ajoute une ligne pour indiquer qu'un article est vu
  public function ajouter()
  {
    Base\Requete\PDOFournie::$INSTANCE->exec( parent::ajouter( ArticleVu::TABLE, self::getChamps() ) );
    // récupère le nouvelle ID
    return (int) Base\Requete\PDOFournie::$INSTANCE->lastInsertId();
  }
  
  // -------------------------------------
  // Void :
  //   Mais à jour les informations des articles vus
  public function modifier()
  {
    $this->baseClause();
    Base\Requete\PDOFournie::$INSTANCE->exec( parent::modifier( ArticleVu::TABLE, self::getChamps() ) );
  }
  
  // -------------------------------------
  // Void :
  //   Supprime un article vu
  public function supprimer()
  {
    $this->baseClause();
    Base\Requete\PDOFournie::$INSTANCE->exec( parent::supprimer( ArticleVu::TABLE, self::getChamps() ) );
  }
  
  // -------------------------------------
  // Void :
  //   Vérifie la présence d'une clause, utile pour la modification ou la suppression
  public function baseClause()
  {
    if( $this->getWheres() == '' && $this->av_reference_id[0] !== null )
      $this->addWhere(self::simpleCritereClause( 'av_reference_id', array_merge( array('='), $this->av_reference_id ) ));
  }
}
