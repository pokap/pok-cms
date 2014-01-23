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

namespace pok\Apps\Outils;

class Cfg
{
  // String :
  //   Emplacement du dossier où travailler
  protected $cfg;
  //   Nom du fichier cfg
  protected $titre;
  // Array :
  //   Contenu du cfg
  protected $ressource = array();
  
  // Array :
  //   Liste des cfg importé
  static protected $cfgs = array();
  
  // -------------------------------------
  // Void :
  //   @string $nom : nom du fichier
  //   Initialise la class
  public function __construct( $nom )
  {
    if( Base\Fichier::valide($nom) )
    {
      $this->titre = (string) $nom;
      $this->cfg = ADRESSE_CFG . '/' . $this->titre . '.php';
    }
    else throw new \Exception('<b>pok\Outils\Cfg::__construct()</b> le nom du fichier est incorrect');
  }
  
  // -------------------------------------
  // Bool :
  //   @string $nom : nom du fichier
  //   Si TRUE, inclue un fichier
  public static function import( $nom )
  {
    if( Base\Fichier::valide($nom) && !in_array( $nom, self::$cfgs ) && file_exists( ADRESSE_CFG . '/' . $nom . '.php' ) )
    {
      require_once( ADRESSE_CFG . '/' . $nom . '.php' );
      self::$cfgs[] = $nom;
      return true;
    }
    else return false;
  }
  
  // -------------------------------------
  // Bool :
  //   Récupère un fichier cfg
  public function ouvre()
  {
    $this->intialise();
    // On ouvre le fichier
    if( $fichier = @fopen( $this->cfg, 'r' ) )
    {
      // On récupère son contenu dans la variable $contenu
      while( !feof($fichier) )
      {
        $contenu = trim(fgets($fichier));
        if( $contenu != '<?php' && $contenu != 'namespace systems\cfg\\'.$this->titre.';' )
        {
          // récupère la clé
          $cle = substr( strstr( $contenu, ' =', true ), 6 );
          // enregistre a partir de la clé
          $this->ressource[$cle] = array();
          // ajoute les informations
          $this->ressource[$cle][0] = substr( strstr( strstr( $contenu, '=' ), ';', true ), 3, -1 );
          $this->ressource[$cle][1] = substr( strstr( $contenu, '//' ), 3 );
        }
      }
      // ferme le fichier
      fclose($fichier);
      return true;
    }
    else return false;
  }
  
  // -------------------------------------
  // Void :
  //   supprime le contenu du cfg courant
  public function intialise() {
    $this->ressource = array();
  }
  
  // -------------------------------------
  // Void :
  //   Sauvegarde le fichier
  public function sauvegarde()
  {
    // debut de fichier
    $contenu = '<?php'."\n".'namespace systems\cfg\\' . $this->titre . ';' . "\n";
    // ajoute les constantes
    foreach( $this->ressource AS $nom => $valetcom )
    {
      if( is_string($nom) && !empty($nom) )
      {
        $commentaire = ( $valetcom[1] !== '' )? ' // ' . $valetcom[1] : '';
        // ajoute la constante
        $valeur = is_numeric($valetcom[0])? $valetcom[0] : '"' . $valetcom[0] . '"';
        $contenu .= 'const ' . $nom . ' = ' . $valeur . ';' . $commentaire . "\n";
      }
    }
    // refait la configuration
    return Base\Fichier::nouveau( $this->cfg, $contenu );
  }
  
  // -------------------------------------
  // Bool :
  //   @string $nom : nom du fichier
  //   Creer un fichier cfg
  public function creer( $nom )
  {
    $fichier = ADRESSE_CFG . '/' . $nom . '.php';
    // vérifie l'existance du fichier et le créer
    if( Base\Fichier::valide($nom) && !file_exists($fichier) ) {
      return Base\Fichier::nouveau( $fichier, '<?php'."\n".'namespace systems\cfg\\'.$this->titre.';' );
    }
    return true;
  }
  
  // -------------------------------------
  // Bool :
  //   Supprime un fichier cfg
  public function detruire()
  {
    // vérifie l'existance du fichier et le suppprime
    if( file_exists($this->cfg) ) {
      return @unlink($this->cfg);
    }
    return true;
  }
  
  // -------------------------------------
  // Boolean :
  //   @string $nom         : nom de la constante
  //   @string $valeur      : valeur de la constante
  //  [@string $commentaire : commentaire de la constante]
  //   Ajoute une constante
  public function ajoute( $nom, $valeur, $commentaire = '' )
  {
    $nom = strtoupper($nom);
    // s'il existe déjà, il ne faut pas l'écraser
    if( !array_key_exists( $nom, $this->ressource ) )
    {
      // initialise
      $this->ressource[$nom] = array();
      // ajoute les informations
      $this->ressource[$nom][0] = $valeur;
      $this->ressource[$nom][1] = $commentaire;
      return true;
    }
    else return false;
  }
  
  // -------------------------------------
  // Void :
  //   @string $nom : nom de la constante
  //   Supprime une constante
  public function supprime( $nom )
  {
    $nom = strtoupper($nom);
    // on enleve une constante
    unset($this->ressource[$nom]);
  }
  
  // -------------------------------------
  // Void :
  //   @string $nom    : nom de la constante
  //   @string $valeur : valeur de la constante
  //   Remplace la valeur d'une constante
  public function remplace( $nom, $valeur, $commentaire = null  )
  {
    // toujours en majuscule
    $nom = strtoupper($nom);
    if( !$this->ajoute( $nom, $valeur, $commentaire ) )
    {
      $this->ressource[$nom][0] = $valeur;
      // on change le commentaire s'il n'est pas vide
      if( $commentaire !== null )
        $this->ressource[$nom][1] = $commentaire;
    }
  }
}
?>
