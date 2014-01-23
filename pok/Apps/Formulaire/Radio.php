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

final class Radio extends Base
{
  // String :
  //   class de la <div> qui englobe l'ensemble du champ
  public $classname_detail = 'form inputradio';
  //   class de la <div> qui affiche la description du champ
  public $classname_description = 'description';
  //   class de la <input /> du champ
  public $classname_input = 'inputradio';

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
  //   initialise le radio
  public function init( $infos, $value_select, $text = '' )
  {
    $this->infos = (array) $infos;
    $this->text = (string) $text;
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
  //   cr�er tous les inputs et labels de la checkbox
  public function input()
  {
    foreach( $this->infos AS $radio )
    {
      $this->html .= '<div class="'.$this->classname_input.'">'."\n\t";
      // l'affiche fonctionne de la m�me mani�re que celle d'une checkbox
      $this->html .= $this->createInput( 'radio', $radio['name'], $radio['id'], $radio['value'], $radio['value'] == $this->value_select );
      $this->html .= $this->createLabel( $radio['id'], $radio['texte'] );
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
