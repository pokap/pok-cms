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

use pok\Apps\Membre,
    pok\Apps\MembreModif,
    pok\Apps\Outils\Session,
    pok\Apps\Outils\Base\Fichier,
    pok\Controleur\Page AS Cpage;

// -------------------------------------
// on fait une requete pour savoir s'il ne sait pas trompé de mot de passe !
// si la requete renvoie des informations c'est que tout les infos sont bonnes !
function verif_membre( $mdp )
{
  $infos = new Membre(array(array(
    'membre.membre_id'  => array( '=', $_SESSION['id'] ),
    'membre.membre_mdp' => array( '=', Membre::scriptmdp($mdp) ),
  )));
  $me = $infos->publier();
  return !empty($me);
}

if( Session::connecter() )
{
  // Si on change le mot de passe
  if( isset($_GET['mdp']) )
  {
    // on vérifie qu'on a bien choisie le nouveau mot de passe
    if( verif_membre($_POST['ancien_mdp']) )
    {
      if( $_POST['nouveau_mdp'] == $_POST['confirme_mdp'] )
      {
        $membres = new MembreModif(array(
          'membre_id'  => $_SESSION['id'],
          'membre_mdp' => Membre::scriptmdp($_POST['nouveau_mdp'])
        ));
        $membres->modifier();
        
        Fichier::log('<ID:' . $_SESSION['id'] . '> modification mot de passe');
        Cpage::redirect( '@revenir', array('modifmdp') );
      }
    }
  }
  // Si on change l'e-mail
  elseif( isset($_GET['mail']) )
  {
    // on vérifie qu'on a bien choisie le nouveau mot de passe
    if( filter_var($_POST['nouveau_email'], FILTER_VALIDATE_EMAIL) && $_POST['nouveau_email'] == $_POST['confirme_email'] )
    {
      if( verif_membre($_POST['email_mdp']) )
      {
        $membres = new MembreModif(array(
          'membre_id'    => $_SESSION['id'],
          'membre_email' => $_POST['nouveau_email']
        ));
        $membres->modifier();
        
        Fichier::log('<ID:' . $_SESSION['id'] . '> modification email');
        Cpage::redirect( '@revenir', array('modifemail') );
      }
    }
  }
  // si rien, alors c'est qu'il y a une erreur
  Cpage::redirect( '@revenir', array('modifmembrerreur') );
}
