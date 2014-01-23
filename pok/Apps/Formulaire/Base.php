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

abstract class Base
{
  // String :
  //   Code html du formulaire
  protected $html;
  
  // -------------------------------------
  // String :
  //   balise <input /> personnalisable
  protected function createInput( $type, $name, $id, $value, $select = false )
  {
    // si on sp�cifie un ID
    $id = $id != '' ? ' id="'.$id.'"' :'';
    // si c'est une checkbox, on ajoute un [] au nom pour avoir des sous-tableaux dans le POST
    $name .= $type == 'checkbox' ? '[]' :'';
    // si on indique qu'il est cocher en plus !
    $check = ($type == 'checkbox' || $type == 'radio') && $select ? ' checked="checked"' : '';

    return '<input type="'.$type.'" name="'.$name.'"'.$id.' value="'.$value.'"'.$check.' />'."\n\t";
  }

  // -------------------------------------
  // String :
  //   balise <option></option> personnalisable, pour les Selects
  protected function createOption( $value, $text, $select = false )
  {
    // si on indique qui est s�lectionner en plus !
    $sel = $select == true ? ' selected="selected"' : '';

    return '<option value="'.$value.'"'.$sel.'>'.$text.'</option>'."\n\t";
  }

  // -------------------------------------
  // String :
  //   balise <label></label> personnalisable
  protected function createLabel( $id, $text ) {
    return '<label for="'.$id.'">'.$text.'</label>'."\n\t";
  }

  // -------------------------------------
  // String :
  //   renvoie l'ensemble du code html g�n�r�
  protected function getHtml() {
    return $this->html;
  }
}
?>