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
    pok\Apps\ArticlePriveModif,
    pok\Apps\Models\Base\Requete\PDOFournie,
    templates\pok_article_prive\Models\ArticlePrive;

// ne pas oubliez de mettre un chapo sinon on ne peut pas mettre d'article
if( !isset($_POST['chapo']) ) $_POST['chapo'] = '';
// -------------------------------------
// on doit vérifier que nous avons bien des membres qui vont le recevoir
if( $_WORK == 'topic' || $_WORK == 'edit' )
{
  // si ce n'est pas un brouillon
  if( !isset($_POST['brouillon']) )
  {
    // sécurité
    $_POST['niveau_comments'] = 1;
    $_POST['brouillon'] = '0';
    
    // si on EDIT le brouillon
    if( $_WORK == 'edit' )
      $get .= '&brouillon';
    
    // on transforme la liste de pseudo en tableau
    $dest_pseudo_clean = str_replace( ' ', '', $_POST['chapo'] );
    // il faut la liste des destinataires
    if( !empty($dest_pseudo_clean) )
    {
      // développez la classe membres pour sortir plusieurs infos membres avec un tableau id
      $infos_membre = Membre::fetchByMembrePseudo(explode( ',', $dest_pseudo_clean ));
      
      $dest_id_array = array();
      // on récupère les ID
      foreach( $infos_membre AS $membre ) {
        $dest_id_array[] = $membre['membre_id'];
      }
      // s'il n'y a pas de pseudo, on n'envoie pas l'article
      if( empty($dest_id_array) )
      {
        $action = false;
        $get .= '&aucun_pseudo_messagerie&';
      }
      
      // -------------------------------------
      // Void :
      //   Gère l'après post, on indique quoi faire après création d'un article.
      //   Ici on doit indiquer qu'il faut ajouter la liste de destinataire.
      function __after_even()
      {
        global $action, $dest_id_array, $get;
        
        $id_post = (int) isset($GLOBALS['id_new_post']) ? $GLOBALS['id_new_post'] : $GLOBALS['article']['article_id'];
        // si on a créer l'article, c'est à dire qu'il y a au moins un membre bon
        if( $action && $id_post > 0 )
        {
          foreach( $dest_id_array AS $id_membre )
          {
            $prive = new ArticlePriveModif(array(
              'prive_article_id' => $id_post,
              'prive_membre_id'  => $id_membre
            ));
            $prive->ajouter();
          }
          $get .= '&ok';
        }
        else
          $get .= '&erreur&createdest';
      }
    }
    else
      $get .= '&aucun_pseudo_messagerie';
  }
  // si on fait un brouillon, on a pas besoin de mettre les destinataires encore !
  else
  {
    // sécurité
    $_POST['niveau_comments'] = 0;
    $_POST['brouillon'] = '1';
    $get .= '&brouillon';
  }
}
// -------------------------------------
// il faut vérifier qu'on puisse répondre
elseif( $_WORK == 'post' )
{
  $_POST['brouillon'] = '0';
  // il faut que seul les personnes qui ont reçu le message peuvent y répondre
  if( !ArticlePrive::isDestinataire($article['article_parent']) )
  {
    $action = false;
    $get .= '&erreur';
  }
  else
    $get .= '&voirecu='.$article['article_parent'];
}
// on ne doit pas pouvoir l'éditer ou le supprimer
else
  $action = false;

if( $action )
{
  $_POST['titre'] = empty($_POST['titre'])? '' : htmlspecialchars($_POST['titre']);
  // sécurité
  $_POST = array(
    'article_titre' => $_POST['titre'],
    'article_texte' => nl2br(htmlspecialchars($_POST['text'])),
    'article_date_creer'   => array( 'NOW()', PDOFournie::NOT_QUOTE ),
    'article_date_reviser' => array( 'NOW()', PDOFournie::NOT_QUOTE ),
    'article_date_max' => '0000-00-00 00:00:00',
    'brouillon' => $_POST['brouillon']
  );
}