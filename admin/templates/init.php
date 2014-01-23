<?php
session_start();

use pok\Apps\Outils\Session,
    pok\Apps\Outils\Cfg;

// Base du CMS
require(__DIR__ . '/../../pok/Main.php');
// charge l'autoload
pok\Main::autoLoad(array(
  ADRESSE_BASE
));
Cfg::import('config');

// token pour les failles de type CSRF
Session::creerJeton();
// connexion automatique avec cookie
pok\Apps\Membre::cookieConnexion();

// contre attaque xss ou vol de session
if( !Session::fixeConnexion() )
  pok\Controleur\Page::redirect('../index.php?template_acces_interdit_view');

if( !Session::connecter() || $_SESSION['statut'] != pok\Apps\Membre::ADMIN ) header('location: connexion.php');
