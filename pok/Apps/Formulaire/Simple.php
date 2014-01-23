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

// permet de créer de simple input de type text
final class Simple extends Base
{
  // String :
  //   class de la <div> qui englobe l'ensemble du champ
  public $classname_detail = 'form inputsimple';

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
  //   initialise de l'input
  public function init( $id, $name, $value = '', $text = '' )
  {
    $this->id = $id;
    $this->name = $name;
    $this->value = $value;
    $this->text = $text;

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
  //   créer l'input
  public function input() {
    $this->html .= $this->createInput( 'text', $this->name, $this->id, $this->value );
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