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

session_start();

// Base du CMS
// La config "general" est automatiquement inclue
require('pok/Main.php');
// charge l'autoload
pok\Main::autoLoad(array(
  ADRESSE_BASE
));

use pok\Enligne;
use pok\Controleur\Page as CPage;
use pok\Apps\Page;
use pok\Apps\Membre;
use pok\Apps\Outils\Session;
use pok\Apps\Outils\Cfg;
use pok\Apps\Outils\Base\Fichier;
use systems\cfg\general;


$page = isset($_GET['page']) && Page::format($_GET['page']) ? $_GET['page'] : '';
$article = isset($_GET['p']) ? $_GET['p'] : '';
$plus = isset($_GET['plus']) ? $_GET['plus'] : '';

// Oublie d'un champ
if( !Cfg::import('config') || empty($_POST['login']) || empty($_POST['password']) ) {
  CPage::redirect(CPage::url( $page, $article, $plus.'&connexion=champ_oublier' ));
}
else
{
  // Acces interdit : le membre est déjà connecté
  if( Session::connecter() ) {
    CPage::redirect(CPage::url( $page, $article, $plus.'&connexion=deja_connect' ));
  }
  else
  {
    // si on mémorise, on créer un cookie
    $reaction = Membre::setSession( $_POST['login'], Membre::scriptmdp($_POST['password']), isset($_POST['memorise']) );
    // renvoie l'information de connexion
    if( $reaction === Membre::REUSSI )
    {
      // -- log
      Fichier::log('<IP:' . $_SERVER['REMOTE_ADDR'] . '> connexion <ID:' . $_SESSION['id'] . '>');
      // on supprime le fichier temporaire de l'ancien visiteur
      Enligne::revise();
      CPage::redirect(CPage::url( $page, $article, $plus.'&connexion=ok' ));
    }
    else
    {
      switch($reaction)
      {
        case Membre::INCONNU:
          CPage::redirect(CPage::url( $page, $article, $plus.'&connexion=membre_inconnu' ));
        break;
        case Membre::BANNIE:
          CPage::redirect(CPage::url( $page, $article, $plus.'&connexion=banni' ));
        break;
        case Membre::BADMDP:
          CPage::redirect(CPage::url( $page, $article, $plus.'&connexion=mdp_erreur' ));
        break;
        case Membre::NONACTIF:
          CPage::redirect(CPage::url( $page, $article, $plus.'&connexion=innactif' ));
        break;
        default:
          CPage::redirect(CPage::url( $page, $article, $plus.'&connexion=erreur' ));
        break;
      }
    }
  }
}
CPage::redirect('@revenir');
