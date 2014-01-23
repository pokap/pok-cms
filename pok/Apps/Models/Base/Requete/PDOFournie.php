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

namespace pok\Apps\Models\Base\Requete;
// Alias
use \systems\cfg\config;

/*
  Gère l'ensemble des fonctions des classes qui sont en rapport avec la Base de Données
  La class fonctionne avec PDO, on récupère une instance de PDO avec self::connexion() et on la supprime avec un simple self::deconnexion()
*/

// stock les instances
abstract class PDOFournie
{
  // \PDO :
  //   garde une instance de PDO
  public static $INSTANCE = null;
  
  // Bool :
  //   afficher les erreurs
  public static $ERREUR = false;
  //   force l'affichage des prochaines requetes sql
  public static $FORCE_VIEW_REQ = false;
  //   force l'affichage des erreurs des requetes sql
  public static $VIEW_ERROR_REQ = false;
  
  // String :
  //   POUR SAVOIR SI ON UTILISE L'ENCODAGE DE PDO
  const NOT_QUOTE = 'not_quote';
  
  // Array :
  //   Stock les instances
  protected static $instances = array();
  // Int :
  //   Nombre d'instance
  protected static $numInstance = 0;
  
  // -------------------------------------
  // Int :
  //   renvoie le nombre d'instance
  public static function getNumInstance() {
    return(self::$numInstance);
  }
  
  // -------------------------------------
  // Void :
  //   Renvoie la référence d'une instance de PDO existante
  public static function autoConnexion() {
    if( self::$INSTANCE === null )
      self::$INSTANCE = &self::connexionAt(0);
  }
  
  // -------------------------------------
  // Boolean :
  //   Vérifie que le numéro de connexion existe
  public static function issetConnexion( $num )
  {
    return( array_key_exists( $num, self::$instances ) && self::$instances[$num] !== null );
  }
  
  // -------------------------------------
  // \PDO :
  //   Renvoie la référence d'une instance de PDO
  public static function &connexion() {
    return($connect = &self::connexionAt(self::getNumInstance()));
  }
  
  // -------------------------------------
  // Void :
  //   Supprime la dernière connexion
  public static function deconnexion() {
    self::deconnexionAt(self::getNumInstance() - 1);
  }
  
  // -------------------------------------
  // \PDO :
  //   @int $num : Numéro de la connexion
  //   Créer une connexion d'après un numéro
  public static function &connexionAt( $num )
  {
    if( !self::issetConnexion($num) )
    {
      // renvoie une instance de PDO
      self::$instances[$num] = new \PDO( 'mysql:host='.config\PARAM_SERVEUR.';dbname='.config\PARAM_BDD, config\PARAM_USER, config\PARAM_PASSWORD );
      self::$numInstance++;
      return self::$instances[$num];
    }
    else return(self::$instances[$num]);
  }
  
  // -------------------------------------
  // Void :
  //   @int $num : Numéro de la connexion
  //   Supprime la connexion par rapport au numéro
  public static function deconnexionAt( $num )
  {
    if( isset(self::$instances[$num]) && self::$instances[$num] !== null )
    {
      self::$instances[$num] = null;
      self::$numInstance--;
    }
    else
      throw new \pok\Exception('<b>PDOFournie::deconnexionAt()</b> Connexion n°'.$num.' non enregistrer.');
  }
}
?>