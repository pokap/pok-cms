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

use pok\Texte,
    pok\Apps\Page,
    pok\Apps\Membre,
    pok\Apps\PageModif,
    pok\Apps\CatRelationModif,
    pok\Apps\MembreModif,
    pok\Apps\MembreGroupeModif,
    pok\Apps\Outils\Session,
    pok\Apps\Outils\Mail,
    pok\Controleur\Page AS CPage,
    systems\cfg\general;

if( !Session::connecter() && isset($_GET['arbo'], $_POST['pseudo'], $_POST['mdp'], $_POST['mdp_confirme'], $_POST['email']) )
{
  if( $_POST['mdp'] == $_POST['mdp_confirme'] && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) !== false )
  {
    // il ne faut pas oublier d'initialise ces variables
    // car si l'utilisateur ajoute ces données, il pourra se mettre admin facilement
    $membre_id = 0;
    // modifie la page du membre
    $pageSql = new Page(array(array( 'page_id' => array( '=', 2 ) )));
    $page = $pageSql->publier();
    
    $page_membre = new PageModif(array(
      'page_ordre'        => 0,
      'page_nom'          => $_POST['pseudo'],
      'page_description'  => 'Profil de ' . $_POST['pseudo'],
      'template'          => general\TEMPLATE_MEMBRE,
      'arborescence'      => $page[0]['arborescence'] . '/' . Texte::slcs($_POST['pseudo'])
    ));
    $page_id = $page_membre->ajouter();
    if( $page_id > 0 )
    {
      $cat = new CatRelationModif(array( 
        'cat_id'      => 2, // profil
        'relation_id' => $page_id,
        'terms'       => 'page',
      ));
      $cat->ajouter();
      // mode de création d'un membre
      switch(general\USER_ENABLE_MODE)
      {
        // le membre active son compte
        case 1:
          $valide = 0;
          $cle = md5(uniqid(Texte::generateur(8), true));
        break;
        // le membre est directement inscrit
        case 2:
          $valide = 1;
          $cle = null;
        break;
        // l'admin doit activer le compte de l'utilisateur
        default:
          $valide = 0;
          $cle = null;
        break;
      }
      $membre = new MembreModif(array(
        'membre_pseudo' => $_POST['pseudo'],
        'page_id'       => $page_id,
        'membre_mdp'    => Membre::scriptmdp($_POST['mdp']),
        'membre_email'  => $_POST['email'],
        'membre_inscrit'=> date('Y-m-d H:i:s'),
        'membre_visite' => date('Y-m-d H:i:s'),
        'statut'        => 'M',
        'cle'           => $cle,
        'valide'        => $valide
      ));
      if( $membre_id = $membre->ajouter() )
      {
        $groupe = new MembreGroupeModif(array(
          'membre_id' => $membre_id,
          'groupe_id' => 2,
          'principal' => 1
        ));
        $groupe->ajouter();

        $mail = new Mail();
        // sujet du mail
        $mail->subject = 'Inscription -pok-';
        // corps du mail
        $mail->html = '<h1>Bienvenue sur le site de POK</h1>
        <p>Vous pouvez dès maintenant vous connectez sur le site :<br />
        <a href="http://' . general\DOMAINE . general\PATH . '">http://' . general\DOMAINE . general\PATH . '</a></p>
        <p>Voici vos indantifiants :<br />
        Login : ' . htmlspecialchars( $_POST['pseudo'], ENT_QUOTES ) . '<br />
        Mot de passe : ' . $_POST['mdp'] . '</p>';

        // si on doit activer le compte
        if( general\USER_ENABLE_MODE == 1 )
        {
          $mail->html .= "\n".'<p>Votre compte est actuellement inactif. Vous ne pouvez pas l\'utiliser tant que vous n\'aurez pas visité le lien suivant:<br />
          <a href="http:// '. general\DOMAINE . general\PATH . 'controleur.php?tpl=pok_inscription&amp;ctrl=valide&amp;arbo=' . $_POST['arbo'] . '&amp;cle=' . $cle . '">Valider votre compte</a></p>';
        }

        // destinataire du mail :
        $mail->addTo($_POST['email']);
        // emetteur du mail :
        $mail->setHeadersFrom(general\MAIL_FROM);
        // on envoie le mail
        $mail->sendMail();

        CPage::redirect(CPage::url( $_POST['arbo'],'','&inscri_ok' ));
      }
    }
    // si rien, alors c'est qu'il y a une erreur
    CPage::redirect(CPage::url( $_POST['arbo'],'','&inscri_erreur' ));
  }
  else {
    CPage::redirect(CPage::url( $_POST['arbo'],'','&droit_erreur' ));
  }
}

