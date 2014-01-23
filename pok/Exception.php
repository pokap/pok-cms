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

use \systems\cfg\general;

final class Exception extends \Exception
{
  public function __construct( $msg ) {
    parent::__construct( $msg );
  }
  
  public function afficher_erreur()
  {
    ob_end_clean();
    // On retourne un message d'erreur complet pour nos besoins.
    // avec le numero de ligne
    require( __DIR__ . '/../templates/' . general\TEMPLATE_500 . '/' . general\TEMPLATE_500 . '.php' );
  }
  
  public function afficher_erreur_dev()
  {
    ob_end_clean();
    // si on active le "dev-mode" il faut un fichier nommé pareil avec un suffixe "_dev" pour afficher les informations des erreurs
    require( __DIR__ . '/../templates/' . general\TEMPLATE_500 . '/' . general\TEMPLATE_500 . '_dev.php' );
  }
}
?>