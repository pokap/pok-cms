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

class MembreNewsletter extends Models\MembreNewsletter
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
      $donnees[] = new MembreNewsletterModif($enr);
    }
    return $donnees;
  }
  
  // -------------------------------------
  // Array :
  //   La requete sql centré sur les newsletters
  public function publierNewsletter()
  {
    $this->addDonnee(Newsletter::TABLE.'.newsletter_id','newsletter_id');
    $this->addDonnee(Newsletter::TABLE.'.newsletter_auto','newsletter_auto');
    $this->addDonnee(Newsletter::TABLE.'.newsletter_titre','newsletter_titre');
    $this->addJoin( config\PREFIX.Newsletter::TABLE, Newsletter::TABLE, Newsletter::TABLE.'.newsletter_id = '.self::TABLE.'.newsletter_id' );
    // page
    $this->addDonnee(Page::TABLE.'.page_id','page_id');
    $this->addDonnee(Page::TABLE.'.page_ordre','page_ordre');
    $this->addDonnee(Page::TABLE.'.page_nom','page_nom');
    $this->addDonnee(Page::TABLE.'.page_description','page_description');
    $this->addDonnee(Page::TABLE.'.template','template');
    $this->addDonnee(Page::TABLE.'.nonlu','nonlu');
    $this->addDonnee(Page::TABLE.'.arborescence','arborescence');
    $this->addJoin( config\PREFIX.Page::TABLE, Page::TABLE, Newsletter::TABLE.'.page_id = '.Page::TABLE.'.page_id' );
    // joindre la tables newsletter pour les clauses
    $this->addJoin( config\PREFIX.Membre::TABLE, Membre::TABLE, Membre::TABLE.'.membre_id = '.self::TABLE.'.membre_id' );
    // utilise le publier de base, ça simplifie ^^
    return $this->publier();
  }
  
  // -------------------------------------
  // Array :
  //   La requete sql centré sur les membres
  public function publierMembre()
  {
    $this->addDonnee(Membre::TABLE.'.membre_pseudo','pseudo_auteur');
    $this->addDonnee(Membre::TABLE.'.page_id','page_id_auteur');
    $this->addDonnee(Membre::TABLE.'.membre_email','email_auteur');
    $this->addDonnee(Membre::TABLE.'.membre_inscrit','inscrit_auteur');
    $this->addDonnee(Membre::TABLE.'.membre_visite','visite_auteur');
    $this->addDonnee(Membre::TABLE.'.statut','statut_auteur');
    $this->addJoin( config\PREFIX.Membre::TABLE, Membre::TABLE, Membre::TABLE.'.membre_id = '.self::TABLE.'.membre_id' );
    // joindre la tables newsletter pour les clauses
    $this->addJoin( config\PREFIX.Newsletter::TABLE, Newsletter::TABLE, Newsletter::TABLE.'.newsletter_id = '.self::TABLE.'.newsletter_id' );
    // utilise le publier de base, ça simplifie ^^
    return $this->publier();
  }
}
