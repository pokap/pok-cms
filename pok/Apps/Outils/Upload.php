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

class Upload
{
  // String :
  //   emplacement du dossier où travailler
  protected $name;
  // Int :
  //   poids maximum
	protected $max_size = 0;
  // Array :
  //   extention autoriser
	protected $extension = array();
  // pour les images :
  // Int :
  //   largeur maximal
  protected $max_width = 0;
  // Int :
  //   hauteur maximal
  protected $max_height = 0;

  public function __construct( $name )
  {
    $this->name = (string) $name;
  }

  // -------------------------------------
  // Bool :
  //   Vérifie l'existance d'un fichier
  public function existe()
  {
    return( isset($_FILES[$this->name]) && empty($_FILES[$this->name]['error']) );
  }
  
  // -------------------------------------
  // Void :
  //   @string $attribut : Le nom de l'attribut
  //   @mixed  $valeur   : La valeur à assigner
  //   Permet d'assigner une valeur à un attribut
  public function __set( $attribut, $valeur )
  {
    switch($attribut)
    {
      case 'max_size':
        if( $valeur >= 0 )
          $this->max_size = (int) $valeur;
        else
          throw new \Exception('<b>pok\Outils\Upload::max_size</b> doit etre positif ou nulle');
      break;
      case 'extension':
        $this->extension = (array) $valeur;
      break;
      case 'max_width':
        if( $valeur >= 0 )
          $this->max_width = (int) $valeur;
        else
          throw new \Exception('<b>pok\Outils\Upload::max_width</b> doit etre positif ou nulle');
      break;
      case 'max_height':
        if( $valeur >= 0 )
          $this->max_height = (int) $valeur;
        else
          throw new \Exception('<b>pok\Outils\Upload::max_height</b> doit etre positif ou nulle');
      break;
      default:
        throw new \Exception('l\'acces a <b>pok\Outils\Upload::'.$attribut.'</b> est impossible');
      break;
    }
  }
  // -------------------------------------
  // Mixed :
  //   @string $attribut : Le nom de l'attribut
  //   Renvoie l'attribut spécifié en paramètre
  public function __get( $attribut )
  {
    if( property_exists( __CLASS__, $attribut ) && $attribut != 'name' )
      return $this->$attribut;
    else
      throw new \Exception('l\'acces a <b>pok\Outils\Upload::'.$attribut.'</b> est impossible');
  }
  
  // -------------------------------------
  // Int :
  //   Le poids d'un fichier
  public function poids()
  {
    return $_FILES[$this->name]['size'];
  }
  
  // -------------------------------------
  // Bool :
  //   Vérifie le poids d'un fichier
  public function poidsOk()
  {
    return( $this->max_size == 0 || $this->poids() <= $this->max_size );
  }
  
  // -------------------------------------
  // String :
  //   Renvoie l'extension d'un fichier charger
  public function ext()
  {
    return Base\Fichier::ext($_FILES[$this->name]['name']);
  }
  
  // -------------------------------------
  // Bool :
  //   Vérifie le type du fichier
  public function type( $type )
  {
    return strstr( $_FILES[$this->name]['type'], $type );
  }
  
  // -------------------------------------
  // Bool :
  //  Renvoie si l'extension est accepté
  public function extension()
  {
    // on test si l'extension correspond à la liste établie
    return( in_array( $this->ext(), $this->extension ) || empty($this->extension) );
  }
  
  // -------------------------------------
  // Bool :
  //  Vérifie la taille d'une image
  public function taille()
  {
    // renvoie la taille de l'image, [0] = largeur, [1] = hauteur
    $image_sizes = getimagesize( $_FILES[$this->name]['tmp_name'] );
    return ( $this->max_width == 0 || $image_sizes[0] <= $this->max_width ) && ( $this->max_height == 0 || $image_sizes[1] <= $this->max_height );
  }
  
  // -------------------------------------
  // Bool :
  //  Si le fichier est valide
  public function valide()
  {
    return $this->existe() && $this->taille() && $this->extension() && $this->poidsOk();
  }
  
  // -------------------------------------
  // Bool :
  //  Bouge un fichier uploaded
  public function charger( $move )
  {
    if( $this->valide() )
      return move_uploaded_file( $_FILES[$this->name]['tmp_name'], $move );
    else
      return false;
  }
}
