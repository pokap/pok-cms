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

use \systems\cfg\config;
use \systems\cfg\general;
use pok\Apps\Models\Base\Requete\PDOFournie;
use pok\Texte;

class Membre extends Models\Membre
{
  const REUSSI   = 0;
  const NONACTIF = 2;
  const BADMDP   = 3;
  const INCONNU  = 4;
  
  // -------------------------------------
  // Array :
  //   La requete sql
  public function publier()
  {
    // initialise
    $donnees = array();
    // page du membre
    $this->addDonnee('page_membre.page_id','membre_page_id');
    $this->addDonnee('page_membre.page_nom','membre_page_nom');
    $this->addDonnee('page_membre.page_ordre','membre_page_ordre');
    $this->addDonnee('page_membre.page_description','membre_page_description');
    $this->addDonnee('page_membre.arborescence','membre_arborescence');
    $this->addDonnee('page_membre.template','membre_page_template');
    $this->addJoin( config\PREFIX.Page::TABLE, 'page_membre', 'page_membre.page_id = '.self::TABLE.'.page_id');
    // groupe du membre
    $this->addJoin( config\PREFIX.MembreGroupe::TABLE, MembreGroupe::TABLE, MembreGroupe::TABLE.'.membre_id = '.self::TABLE.'.membre_id AND '.MembreGroupe::TABLE.'.principal = \'1\'');
    // groupe
    $this->addDonnee(Groupe::TABLE.'.groupe_id','groupe_id');
    $this->addDonnee(Groupe::TABLE.'.couleur','groupe_couleur');
    $this->addJoin( config\PREFIX.Groupe::TABLE, Groupe::TABLE, Groupe::TABLE.'.groupe_id = '.MembreGroupe::TABLE.'.groupe_id');
    // page du groupe
    $this->addDonnee('page_groupe.page_id','groupe_page_id');
    $this->addDonnee('page_groupe.page_nom','groupe_nom');
    $this->addDonnee('page_groupe.page_description','groupe_description');
    $this->addDonnee('page_groupe.arborescence','groupe_arborescence');
    $this->addJoin( config\PREFIX.Page::TABLE, 'page_groupe', 'page_groupe.page_id = '.Groupe::TABLE.'.page_id');
    // connexion SQL
    PDOFournie::autoConnexion();
    // récupère les informations de la base de donnée
    $requete = parent::publier();
    
    $requete->execute();
    // déconnexion SQL
    PDOFournie::deconnexionAt(0);
    
    while( $enr = $requete->fetch(\PDO::FETCH_ASSOC) ) {
      $donnees[] = new MembreModif($enr);
    }
    return $donnees;
  }
  
  // -------------------------------------
  // Int :
  //   Nombre de membre
  public function count()
  {
    // connexion SQL
    PDOFournie::autoConnexion();
    // récupère les informations de la base de donnée
    $num = parent::count();
    // déconnexion SQL
    PDOFournie::deconnexionAt(0);
    return $num;
  }
  
  // -------------------------------------
  // Mixed :
  //   Informations sur un membre grâce au login et mot de passe
  //   INCONNU = membre inconnu, BANNI = banni, BADMDP = mauvais passe et NONACTIF = non-actif
  //   true si on est connecté
  public static function setSession( $login, $mdp, $cookie = false )
  {
    // le pseudo ne doit pas contenir de caractère comme \/:*?"<>| sinon on pourrait écraser des fichiers non voulu
    if( Page::format($login) )
    {
      // connexion SQL
      PDOFournie::autoConnexion();
      $login = PDOFournie::$INSTANCE->quote(Texte::slcs($login));
      // récupère les informations de la base de donnée
      $requete = PDOFournie::$INSTANCE->prepare('SELECT membre_id, membre_pseudo, statut, membre_visite, membre_mdp , valide
      FROM ' . config\PREFIX . Membre::TABLE . ' WHERE ' . general\USER_LOGIN_TYPE . ' LIKE '.$login.' LIMIT 1');
      $requete->execute();
      // déconnexion SQL
      PDOFournie::deconnexionAt(0);
      // si on trouve un utilisateur
      if( $membre = $requete->fetch(\PDO::FETCH_ASSOC) )
      {
        if( $membre['membre_mdp'] == $mdp )
        {
          if( $membre['valide'] )
          {
            // si le membre est banni
            if( $membre['statut'] == self::BANNIE )
              return self::BANNIE;
            // sinon on enregistre toutes les informations
            else
            {
              // on enregistre les sessions
              $_SESSION['id'] = (int) $membre['membre_id'];
              $_SESSION['pseudo'] = $membre['membre_pseudo'];
              $_SESSION['statut'] = $membre['statut'];
              $_SESSION['visite'] = strtotime($membre['membre_visite']);
              // on met à jour la dernière visite
              MembreModif::setLastVisit();
              // enregistre le cookie
              if( $cookie ) {
                setcookie( 'login', $login, $_SERVER['REQUEST_TIME'] + 1296000, general\PATH, general\DOMAINE, false, true );
                setcookie( 'password', $mdp, $_SERVER['REQUEST_TIME'] + 1296000, general\PATH, general\DOMAINE, false, true );
              }
              return self::REUSSI;
            }
          }
          // compte inactif
          else return self::NONACTIF;
        }
        // maivais mot de passe
        else return self::BADMDP;
      }
      // on renvoie l'erreur
      else return self::INCONNU;
    }
    else return self::INCONNU;
  }
  
  // -------------------------------------
  // Bool :
  //   Pour la connexion automatique avec cookie
  public static function cookieConnexion()
  {
    if( !Outils\Session::connecter() && !empty($_COOKIE['login']) && !empty($_COOKIE['password']) )
    {
      // on cherche les infos du membres
      if( self::setSession( $_COOKIE['login'], $_COOKIE['password'] ) === self::REUSSI )
      {
        // on supprime le fichier temporaire de l'ancien visiteur
        @unlink(ADRESSE_ENLIGNE . 'visiteur_' . $_SERVER['REMOTE_ADDR'] . '.tmp');
        return true;
      }
      else return false;
    }
    else return true;
  }
}
