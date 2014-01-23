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

/*
    Controller :
  Gère les fonctions pratiques de chaque controlleur de template.
*/
abstract class Controleur
{
  // Object :
  //   Class Template
  private $tpl = null;

  // -------------------------------------
  // Template :
  //   Renvoie l'objet \pok\Template
  private function getTemplate()
  {
    // si aucun template utilisé
    if( $this->tpl === null )
      $this->tpl = new Template();
    
    return( $this->tpl );
  }
  
  // -------------------------------------
  // Void :
  //   @Template $tpl : instance de \pok\Template
  //   initialise la class messagerie
  public function setTemplate( Template &$tpl ) {
    $this->tpl = $tpl;
  }
  
  // -------------------------------------
  // Void :
  //   Enregistre des variables pour le template
  protected function assign( $nom, $valeur ) {
    $this->getTemplate()->assign( $nom, $valeur );
  }
  
  // -------------------------------------
  // Void :
  //   Enregistre des tableaux pour le template
  protected function loop( array $table ) {
    $this->getTemplate()->loop( $table );
  }
  
  // -------------------------------------
  // Mixed :
  //   Recherche dans un tableau du template une valeur
  protected function search( $key, $nom ) {
    return $this->getTemplate()->search( $key, $nom );
  }
  
  // -------------------------------------
  // Void :
  //   Supprime une variable du template
  protected function supp( $nom ) {
    $this->getTemplate()->supp( $nom );
  }
  
  // -------------------------------------
  // Array :
  //   Renvoie une variable du template
  protected function view( $nom ) {
    return $this->getTemplate()->view( $nom );
  }
}
?>