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

namespace pok\Apps\Outils\Base;

abstract class Fichier
{
  // Int :
  //   DEFINITION POUR LES FONCTIONS
  const BOTH = 0;
  const FILE = 1;
  const DIR  = 2;
  
  // -------------------------------------
  // Bool :
  //   @string $contenu : texte à ajouter au log
  //   Renvoie le contenu d'un fichier, FALSE s'il n'existe pas
  public static function log( $contenu )
  {
    return self::ajouter( ADRESSE_LOG . '/' . date('m.d.Y') . '.txt', '['.date('H:i:s').'] '.$contenu );
  }
  
  // -------------------------------------
  // String :
  //   @string $contenu : texte à ajouter au log
  //   Renvoie l'extension d'un fichier
  public static function ext( $name )
  {
    return strtolower( substr( strrchr( $name, '.' ), 1 ) );
  }

  // -------------------------------------
  // retour la valeur octal
  public static function octetSize( $size, $round = 2 )
  {
    $tabOctet = array( 'octets', 'Ko', 'Mo', 'Go', 'To' );
    $i = 0;
    while( floor( $size / 1024 ) > 0 ) {
      $size /= 1024;
      ++$i;
    }
    return round( $size, $round ).' '.$tabOctet[$i];
  }
  // -------------------------------------
  // retour la valeur octal sans virgule (cette fonction est presque 2x plus rapide que "octet_size()" )
  public static function octetSizeBit( $size )
  {
    $tabOctet = array( 'octets', 'Ko', 'Mo', 'Go', 'To' );
    $i = 0;
    while( $size >= 1024 ) {
      $size = $size >> 10;
      ++$i;
    }
    return $size.' '.$tabOctet[$i];
  }

  // -------------------------------------
  // Array :
  //  @string $cheminc : nom et chemin du dossier
  //  @int    $dirfile : PARAM_DIR pour voir que les dossiers, PARAM_FILE pour les fichiers, PARAM_BOTH pour les deux.
  //  @array  $exclure : liste des éléments à exclure de la recherche
  //  Renvoie une liste des nom des éléments contenu dans un répertoire
  public static function lister( $cheminc, $dirfile = Fichier::BOTH, array $exclure = array() )
  {
    // initialisation
    $repertoire = array();
    // ouverture du répertoire
    if( $dh = opendir($cheminc) ) {
      // fouille le dossier
      while( ( $file = readdir($dh) ) !== false )
      {
        if( $file != '.' && $file != '..' && !in_array( $file, $exclure ) )
        {
          // si on ne veut que les fichiers
          if( $dirfile === Fichier::FILE ) {
            if( is_file( $cheminc.'/'.$file ) )
              $repertoire[] = $file;
          }
          // sinon si on ne veut que les repertoires
          elseif( $dirfile === Fichier::DIR ) {
            if( is_dir( $cheminc.'/'.$file ) )
              $repertoire[] = $file;
          }
          else
            $repertoire[] = $file;
        }
      }
      // ne pas oublier de fermer
      closedir($dh);
      sort($repertoire);
      
      return $repertoire;
    }
    else return false;
  }

  // -------------------------------------
  // Bool :
  //   @string $haystack : nom du dossier
  //   Vérifie une chaine de caractère pour un nom de répertoire ou de fichier
  public static function valide( $haystack )
  {
    // on compare la taille du segment sans les caractères banni avec l'original
    return( strcspn($haystack, '\/:*?"<>|') == strlen($haystack) );
  }
  
  // -------------------------------------
  // Bool :
  //   @string $cheminc : nom et chemin du fichier
  //   @string $contenu : nom du fichier
  //   Vide et réinscrit un fichier
  public static function nouveau( $cheminc, $contenu )
  {
    // On ouvre le fichier
    if( $fichier = @fopen( $cheminc, 'w' ) )
    {
      // On écrit le contenu
      fputs( $fichier, $contenu );
      // ferme le fichier$contenu
      return fclose($fichier);
    }
    else return false;
  }

  // -------------------------------------
  // Bool :
  //   @string $cheminc : nom et chemin du fichier
  //   @int    $origine : nombre d'origine dans le nouveau fichier
  //   Incremente le chiffre contenu dans un fichier
  public static function incremente( $cheminc, $origine = 1 )
  {
    if( file_exists( $cheminc ) )
    {
      // On ouvre le fichier
      if( $fichier = @fopen( $cheminc, 'r+' ) )
      {
        // On récupère son contenu dans la variable $infos_tentatives
        $contenu = (int) fgets($fichier);
        // On remet le curseur au début du fichier
        fseek($fichier, 0);
        // On écrit le nouveau nombre
        // si le contenu est vide, on imcremente comme si c'était un 0
        fputs( $fichier, $contenu + 1 );
        // ferme le fichier
        return fclose($fichier);
      }
      else return false;
    }
    // si le fichier n'existe pas, on en créer un !
    else return self::nouveau( $cheminc, $origine );
  }
  
  // -------------------------------------
  // String :
  //   @string $cheminc : nom et chemin du fichier
  //   Renvoie le contenu d'un fichier
  public static function renvoie( $cheminc )
  {
    $contenu = '';
    // On ouvre le fichier
    if( $fichier = @fopen( $cheminc, 'r' ) )
    {
      // On récupère son contenu dans la variable $contenu
      while( !feof($fichier) ) {
        $contenu .= fgets($fichier);
      }
      // ferme le fichier
      fclose($fichier);
    }
    // renvoie le contenu
    return $contenu;
  }
  
  // -------------------------------------
  // Bool :
  //   @string $cheminc : nom et chemin du fichier
  //   @string $contenu : nom du fichier
  //   Ajoute une ligne dans un fichier
  public static function ajouter( $cheminc, $contenu )
  {
    // On ouvre le fichier
    if( $fichier = @fopen( $cheminc, 'a' ) )
    {
      // On écrit le contenu
      fputs( $fichier, $contenu . "\n" );
      // ferme le fichier
      return fclose($fichier);
    }
    else return false;
  }
}
?>
