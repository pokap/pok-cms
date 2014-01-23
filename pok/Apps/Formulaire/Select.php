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

namespace pok\Apps\Formulaire;

final class Select extends Base
{
  // String :
  //   class de la <div> qui englobe l'ensemble du champ
  public $classname_detail = 'form inputselect';
  //   class de la <div> qui affiche la description du champ
  public $classname_description = 'description';

  // String :
  //   description du champ
  private $text;
  // Array :
  //   ensemble des informations lier aux checkbox et label correspondant
  private $infos;
  //   ensemble des noms de la checkbox pre-selectionner
  private $value_select;

  // -------------------------------------
  // Void :
  //   initialise le select
  public function init( $infos, $value_select = '', $text = '', $id = '', $name = '' )
  {
    // la façon de fonctionnement ressemble au radio, mais pour un select on demande beaucoup plus d'infos simple
    $this->infos = (array) $infos;
    $this->text = (string) $text;
    $this->id = (string) $id;
    $this->name = (string) $name;
    $this->value_select = (string) $value_select;

    $this->html = '<div class="'.$this->classname_detail.'">'."\n\t";
  }

  // -------------------------------------
  // Void :
  //   insere la <div> de description
  public function description() {
    $this->html .= '<div class="'.$this->classname_description.'">'.$this->text.'</div>'."\n\t";
  }

  // -------------------------------------
  // Void :
  //   créer tous les options avec le select
  public function input()
  {
    $this->html .= '<select id="'.$this->id.'" name="'.$this->name.'">'."\n\t";
    // créer chaque option du select
    foreach( $this->infos AS $select ) {
      $this->html .= $this->createOption( $select['value'], $select['texte'], $select['value'] == $this->value_select );
    }
    $this->html .= '</select>'."\n\t";
  }

  // -------------------------------------
  // String :
  //   retourne le resultat final
  public function result()
  {
    $this->html .= '</div>'."\n\t";
    return $this->getHtml();
  }
}
?>
