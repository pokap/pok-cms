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

final class Checkbox extends Base
{
  // String :
  //   class de la <div> qui englobe l'ensemble du champ
  public $classname_detail = 'form inputcheckbox';
  //   class de la <div> qui affiche la description du champ
  public $classname_description = 'description';
  //   class de la <input /> du champ
  public $classname_input = 'inputcheckbox';

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
  //   initialise la checkbox
  public function init( $infos, $value_select = array(), $text = '' )
  {
    $this->infos = (array) $infos;
    $this->text = (string) $text;
    $this->value_select = (array) $value_select;

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
  //   créer tous les inputs et labels de la checkbox
  public function input()
  {
    foreach( $this->infos AS $check )
    {
      $this->html .= '<div class="inputcheckbox">'."\n\t";
      // on créer la input avec les informations correspondante
      $this->html .= $this->createInput( 'checkbox', $check['name'], $check['id'], $check['value'], in_array( $check['value'], $this->value_select ) );
      // comme la input, mais après pour avoir la case à coché à gauche de ça description
      $this->html .= $this->createLabel( $check['id'], $check['texte'] );
      $this->html .= '</div>'."\n\t";
    }
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
