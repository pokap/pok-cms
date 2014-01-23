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

namespace pok;

class Template
{
  // Array :
  //   Tableau contenant les variables
  private $vars = array();
  
  // -------------------------------------
  // Void :
  //   Initialise la variable "EXCEPTION_ACTIVE"
  public static function setExceptionActive( $valeur ) {
    self::$EXCEPTION_ACTIVE = (boolean) $valeur;
  }
  
  // -------------------------------------
  // Void :
  //   Enregistre des variables pour le template
  public function assign( $nom, $valeur ) {
    $this->vars[$nom] = $valeur;
  }
  
  // -------------------------------------
  // Void :
  //   Enregistre des tableaux pour le template
  public function loop( array $table ) {
    $this->vars = array_merge( $this->vars, $table );
  }
  
  // -------------------------------------
  // Mixed :
  //   Recherche dans un tableau du template une valeur
  public function search( $key, $nom ) {
    return array_search( $key, $this->vars[$nom] );
  }
  
  // -------------------------------------
  // Void :
  //   Supprime une variable du template
  public function supp( $nom ) {
    unset( $this->vars[$nom] );
  }
  
  // -------------------------------------
  // Array :
  //   Renvoie une variable du template
  public function view( $nom )
  {
    if( array_key_exists( $nom, $this->vars ) )
      return $this->vars[$nom];
    else
      return null;
  }
  
  // -------------------------------------
  // Void :
  //   @string $mt  : chemin de la page
  //  [@array  $arg : liste des variables passées]
  //   Inclue une page dans un template pok_accueil
  public static function integrer( $mt, array $arg = array() )
  {
    // récupère la racine
    $schrstr = strstr( $mt, '/', true );
    // si on utilise un module
    $module = ( $schrstr !== false )? true : false;
    // racine complete
    $racine = ($module)? '' : $mt . '/';
    // contruit le chemin du fichier à inclure
    $chemin = ADRESSE_TEMPLATES . '/' . $racine . str_replace( '/', '/Module/', $mt ) . '.php';
    // vérifie le chemin et l'existence du fichier
    if( strstr( $mt, '..' ) === false && file_exists($chemin) )
    {
      // si un controleur de module existe, et qu'on ne passe pas de paramêtre
      if( $module && file_exists(ADRESSE_TEMPLATES . '/' . $schrstr . '/Module.php') )
      {
        // le chemin de la class & le nom de la fonction
        $class = '\templates\\'.$schrstr.'\Module';
        $fonction = substr( $mt, strrpos( $mt, '/' ) + 1, strlen($mt) );
        // enregistre la résultat de la fonction dans une variable qui a le même nom du module
        if( method_exists( $class, $fonction ) )
          ${$fonction} = $class::$fonction();
      }
      // si on ne choisie pas la fonction pour avoir les arguments
      // on le récupère par $arg
      if( $arg > array() )
        extract($arg);
      // inclue la page
      include($chemin);
    }
    else
      throw new Exception('<b>pok\Template::integrer</b> chemin "'.$chemin.'" incorrect.');
  }
  
  // -------------------------------------
  // Boolean :
  //   @string $tpl : nom du template
  //   Inclue les fonctions d'un template
  public function importerFonctions( $tpl )
  {
    if( Apps\Outils\Base\Fichier::valide($tpl) )
    {
      $chemin = ADRESSE_TEMPLATES . '/' . $tpl . '/Controleur/fonctions.php';
      // s'il existe
      if( file_exists($chemin) )
      {
        // extrait les variables pour y avoir accès
        extract($this->vars);
        include_once($chemin);
        return true;
      }
      else return false;
    }
    else
      throw new Exception('<b>pok\Template::importerFonctions</b> nom du template "'.$tpl.'" incorrect.');
  }
  
  // -------------------------------------
  // Boolean :
  //   @string $file : fichier du template
  //   Gère un template
  public function parse( $tpl )
  {
    // inclure les fonctions du template
    $this->importerFonctions($tpl);
    // le lien vers le fichier tpl
    $chemin_tpl = ADRESSE_TEMPLATES . '/' . $tpl . '/' . $tpl . '.php';
    // on vérifie que le template existe ou pas
    if( Apps\Outils\Base\Fichier::valide($tpl) && file_exists($chemin_tpl) )
    {
      // extrait les variables pour y avoir accès
      extract($this->vars);
      // temps d'exécution
      $chrono = round( microtime(true) - $chrono, 5 );
      // affiche la page template
      // mise en cache temporaire pour check s'il y a une erreur dans le template
      ob_start();
      include($chemin_tpl);
      $result = ob_get_contents();
      ob_end_clean();
      // si aucune exception, on affiche
      echo $result;
    }
    else
      throw new Exception('<b>E_POK_TEMPLATE</b> <i>parse</i> Le fichier "'.$chemin_tpl.'" n\'existe pas.');
  }
}
