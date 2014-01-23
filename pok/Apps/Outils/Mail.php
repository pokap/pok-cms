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

namespace pok\Apps\Outils;

class Mail
{
  // String :
  //   sujet
  public $subject = '';
  //   text
  public $text = '';
  //   html
  public $html = '';
  // Array :
  //   destinataire
  public $to = array();
  
  // String :
  //   header
  protected $headersTo = '';
  //   header
  protected $headersFrom = '';
  //   header
  protected $headersCc = '';
  //   header
  protected $headersBcc = '';
  //   html
  protected $message;
  // Array :
  //   listes des fichiers
  protected $files = array();
  
  // String :
  //   frontiere
  private $frontiere = '';
  //   header
  private $headers = '';
  
  // -------------------------------------
  // Void
  //   Ajout des destinataires
  public function addTo( $to )
  {
    // on ajoute avec un tableau
    $this->to += (array) $to;
  }
  // -------------------------------------
  // Void
  //   Supprimer un destinataire
  public function deleteTo( $to )
  {
    // on vérifie que $this->to n'est pas vide
    if( !empty( $this->to ) ) {
      Base\Tableau::deleteByValue( $to, $this->to ) or die( '<b>E_POK_MAIL_TO_DELETE</b> Erreur <i>deleteTo</i> : Le destinataire n\'existe pas !' );
    }
    else
      die( '<b>E_POK_MAIL_TO_EMPTY</b> Erreur <i>deleteTo</i> : La liste des destinataires est vide !' );
  }
  // -------------------------------------
  // String :
  //   Retourne les destinataires
  public function getTo()
  {
    return implode( ', ', $this->to );
  }
  
  // -------------------------------------
  // Void :
  //   Ajout des fichiers
  public function addFile( $chemin, $type )
  {
    // $path_parts = pathinfo( '/www/htdocs/index.html' );
    // echo $path_parts['dirname'], "\n";   // /www/htdocs
    // echo $path_parts['basename'], "\n";  // index.html
    // echo $path_parts['extension'], "\n"; // html
    // echo $path_parts['filename'], "\n";  // index
    $this->files += array_merge( pathinfo( $chemin ), array( 'type' => $type ) );
  }
  // -------------------------------------
  // Void :
  //   Reset l'ensemble des fichiers
  public function resetFile() {
    $this->files = array();
  }
  // -------------------------------------
  // Array :
  //   Retourne l'ensemble des fichiers
  public function getFile() {
    return $this->files;
  }
  
  // -------------------------------------
  // Void :
  //   Défini les destinations dans l'en-tête additionnels
  public function setHeadersTo( $to ) {
    $this->headersTo = 'To: ' . $to . "\r\n";
  }
  // -------------------------------------
  // Void :
  //   Défini l'envoie dans l'en-tête additionnels
  public function setHeadersFrom( $from ) {
    $this->headersFrom = 'From: ' . $from . "\r\n";
  }
  // -------------------------------------
  // Void :
  //   Défini copie conforme
  public function setHeadersCc( $cc ) {
    $this->headersCc = 'Cc: ' . $cc . "\r\n";
  }
  // -------------------------------------
  // Void :
  //   Défini copie conforme caché
  public function setHeadersBcc( $bcc ) {
    $this->headersBcc = 'Bcc: ' . $bcc . "\r\n";
  }
  
  // -------------------------------------
  // Bool :
  //   Envoyer un mail
  public function sendMail()
  {
    $this->headers = 'MIME-Version: 1.0' . "\r\n";
    // si le mail est html et text
    if( !empty( $this->html ) && !empty( $this->text ) )
    {
      // génère la frontière entre le Html et le texte
      $this->headers .= 'Content-Type: multipart/alternative; boundary="' . $this->getFrontiere() . '"';
      $this->headers .= $this->headersTo;
      $this->headers .= $this->headersFrom;
      $this->headers .= $this->headersCc;
      $this->headers .= $this->headersBcc;
      
      // message texte
      $this->message  = 'This is a multi-part message in MIME format.' . "\r\n\r\n";
      $this->message .= '--' . $this->getFrontiere() . '--' . "\r\n"; 
      $this->message .= 'Content-Type: text/plain; charset="iso-8859-1"' . "\r\n"; 
      $this->message .= 'Content-Transfer-Encoding: 8bit' . "\r\n"; 
      $this->message .= $this->text . "\r\n\r\n"; 
      
      // message html
      $this->message .= '--' . $this->getFrontiere() . "\r\n"; 
      $this->message .= 'Content-Type: text/html; charset="iso-8859-1"' . "\r\n"; 
      $this->message .= 'Content-Transfer-Encoding: 8bit' . "\r\n"; 
      $this->message .= $this->html . "\r\n\r\n";
    }
    elseif( !empty( $this->html ) )
    {
      // adaptation du headers du mail
      $this->headers .= 'Content-Type: text/html; charset="iso-8859-1"' . "\r\n"; 
      $this->headers .= $this->headersTo;
      $this->headers .= $this->headersFrom;
      $this->headers .= $this->headersCc;
      $this->headers .= $this->headersBcc;
      $this->headers .= 'Content-Transfer-Encoding: 8bit' . "\r\n";
      $this->message  = $this->html;
    }
    elseif( !empty( $this->text ) )
    {
      $this->headers .= 'Content-Type: text/plain; charset="iso-8859-1"' . "\r\n"; 
      $this->headers .= $this->headersTo;
      $this->headers .= $this->headersFrom;
      $this->headers .= $this->headersCc;
      $this->headers .= $this->headersBcc;
      $this->headers .= 'Content-Transfer-Encoding: 8bit' . "\r\n"; 
      $this->message  = $this->text;
    }
    else // si le message est vide
      die( '<b>E_POK_MAIL_MESSAGE_EMPTY</b> Erreur <i>sendMail</i> : Le message de l\'E-mail n\'est pas défini !' );
    
    // si le message est vide
    if( empty( $this->subject ) )
      die( '<b>E_POK_MAIL_SUBJECT_EMPTY</b> Erreur <i>sendMail</i> : Le sujet de l\'E-mail n\'est pas défini !' );
    
    // liste des destinataires
    $destinataires = $this->getTo();
    // on ajoute les pièces jointes
    $this->message .= $this->jointFile();
    
    return mail( $destinataires, $this->subject, $this->message, $this->headers );
  }
  
  // -------------------------------------
  // String :
  //   formate le message pour les pièces jointes
  private function jointFile()
  {
    // initialisation
    $attachement = '';
    // parcours la liste de fichiers à joindre
    foreach( $this->files AS $file )
    {
      $attachement .= '--' . $this->getFrontiere() . "\r\n";
      $attachement .= 'Content-Type: ' . $file['type'] . ' name=' . $file['basename'] . "\r\n"; 
      $attachement .= 'Content-Transfer-Encoding: base64' . "\r\n"; 
      $attachement .= 'Content-Disposition: attachment; filename=' . $file['filename'] . "\r\n\r\n"; 
      
      // On lit le fichier présent sur le serveur
      // "rb" permet de lire des fichiers en mode binaire (utile sous windows)
      $fd = fopen( $file['dirname'] . '/' . $file['basename'], 'rb' ); 
      $contenu = fread( $fd, filesize( $file['dirname'] . '/' . $file['basename'] ) ); 
      fclose( $fd );
      // encodage en base64 pour que le fichier soit lisible
      $attachement .= chunk_split( base64_encode( $contenu ) ); 
    }
    return $attachement;
  }
  
  // -------------------------------------
  // Void
  //   génère la frontière entre le Html, texte et pièce jointe
  private function getFrontiere()
  {
    if( $this->frontiere == '' )
      $this->frontiere = '----=_Part_' . md5( uniqid( mt_rand() ) );
    
    return $this->frontiere;
  }
}
?>