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

class ArticlePrive extends Models\ArticlePrive
{
  // -------------------------------------
  // Array :
  //   La requete sql
  public function publier()
  {
    // initialise
    $donnees = array();
    
    // connexion SQL
    PDOFournie::autoConnexion();
    // récupère les informations de la base de donnée
    $requete = parent::publier();
    $requete->execute();
    // déconnexion SQL
    PDOFournie::deconnexionAt(0);
    
    while( $enr = $requete->fetch(\PDO::FETCH_ASSOC) ) {
      $donnees[] = new ArticlePriveModif($enr);
    }
    return $donnees;
  }
  
  // -------------------------------------
  // Array :
  //   La requete sql concentré sur l'article
  public function publierArticle()
  {
    // ajoute les données d'articles
    foreach( ArticleModif::getChamps() AS $champ )
      $this->addDonnee( Article::TABLE.'.'.$champ );
    // l'article
    $this->addJoin( config\PREFIX.Article::TABLE, Article::TABLE, Article::TABLE.'.article_id = '.self::TABLE.'.prive_article_id' );
    
    // l'auteur de l'article
    $this->addDonnee(Membre::TABLE.'.membre_pseudo','pseudo_auteur');
    $this->addDonnee(Membre::TABLE.'.page_id','page_id_auteur');
    $this->addDonnee(Membre::TABLE.'.membre_email','email_auteur');
    $this->addDonnee(Membre::TABLE.'.membre_inscrit','inscrit_auteur');
    $this->addDonnee(Membre::TABLE.'.membre_visite','visite_auteur');
    $this->addDonnee(Membre::TABLE.'.statut','statut_auteur');
    $this->addJoin( config\PREFIX.Membre::TABLE, Membre::TABLE, Membre::TABLE.'.membre_id = '.Article::TABLE.'.article_auteur' );
    
    return $this->publier();
  }
  
  // -------------------------------------
  // Array :
  //   La requete sql concentré sur les membres qui partage l'article
  public function publierMembre()
  {
    // ajoute les données d'articles
    foreach( MembreModif::getChamps() AS $champ )
      $this->addDonnee( Membre::TABLE.'.'.$champ );
    // l'auteur
    $this->addJoin( config\PREFIX.Membre::TABLE, Membre::TABLE, Membre::TABLE.'.membre_id = '.self::TABLE.'.prive_membre_id' );
    
    return $this->publier();
  }
}
