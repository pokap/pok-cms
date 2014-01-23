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

final class Textarea extends Base
{
  // String :
  //   class de la <div> qui englobe l'ensemble du champ
  public $classname_detail = 'form inputtextarea';

  // String :
  //   "id" du champ
  private $id;
  //   le "name" du champ
  private $name;
  //   le "value" du champ
  private $value;
  //   description du champ
  private $text;

  // -------------------------------------
  // Void :
  //   initialise le textarea
  public function init( $id, $name, $value = '', $text = '' )
  {
    $this->id = $id;
    $this->name = $name;
    $this->text = $text;
    $this->value = $value;

    $this->html = '<div class="'.$this->classname_detail.'">'."\n\t";
  }

  // -------------------------------------
  // Void :
  //   insere le <label> de description
  public function description() {
    $this->html .= $this->createLabel( $this->id, $this->text );
  }

  // -------------------------------------
  // Void :
  //   créer le textarea, il n'exite pas de fonction présice pour créer un textarea
  public function input() {
    $this->html .= '<textarea id="'.$this->id.'" name="'.$this->name.'">'.$this->value.'</textarea>'."\n\t";
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