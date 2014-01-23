<?php
###############################################################################
# LEGAL NOTICE                                                                #
###############################################################################
# Copyright (C) 2008/2010  Florent Denis                                      #
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

use pok\Main,
    pok\Apps\MembreNewsletter,
    pok\Apps\Outils\Mail,
    systems\cfg\general,
    systems\cfg\newsletter AS cfg_newsletter;

abstract class Newsletter
{
  public static function send( $page_id, $auto = false )
  {
    // init
    if( $auto )
      $membre = new MembreNewsletter(array(array(
        'membre.membre_email' => array( 'IS NOT NULL' ),
        'newsletter.newsletter_auto' => array( '=', 1 ),
        'newsletter.page_id' => array( '=', $page_id )
      )));
    else
      $membre = new MembreNewsletter(array(array(
        'membre.membre_email' => array( 'IS NOT NULL' ),
        'newsletter.page_id' => array( '=', $page_id )
      )));
    
    $membre_newsletter = $membre->publierMembre();
    // verifie que des membres utilise cette newsletter
    if( !empty($membre_newsletter) )
    {
      $load = new Main();
      $load->setPageId($page_id);
      // coupe les droits
      $load->setDroitsActive(false);
      // mail
      $mail = new Mail();
      // cache
      ob_start();
      // résultat
      try {
        $load->afficher();
      }
      catch( Exception $e ) {
        $e->afficher_erreur();
      }
      $mail->html = ob_get_contents();
      ob_end_clean();
      // configure le mail :
      // Sujet du mail
      $mail->subject = str_replace( array('%date'), array(date('d/m/Y')), cfg_newsletter\OBJET_MAIL );
      // récupère la liste des membres qui ont activé la newsletter
      // on envoye les mails :
      foreach( $membre_newsletter AS $membre )
        $mail->addTo($membre['membre_email']);
      // on définit les destinataires en copie cachée
      $mail->setHeadersBcc($mail->getTo());
      // on définit l'expéditeur
      $mail->setHeadersFrom(general\MAIL_FROM);
      // on envoie le mail
      return $mail->sendMail();
    }
    return false;
  }
}
