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

namespace pok\Apps;

use pok\Texte\Format;

abstract class Formulaire
{
  // CONSTANTE
  // Int :
  //  Vous en avez sélectionné trop de checkbox
  const TROP_SELECTION = 0;
  //  Vous en avez pas assez sélectionné de checkbox
  const PEU_SELECTION = 1;
  //  Vous devez mettre un email
  const MAIL = 2;
  //  Vous devez mettre un chiffre
  const CHIFFRE = 3;
  //  Vous devez mettre un numéro de téléphone
  const TELEPHONE = 4;
  //  Vous avez mis trop de caractères
  const TROP_STR = 5;
  //  Vous n'avez pas mis assez de caractères
  const PEU_STR = 6;
  //  Vous avez oublié un champ
  const OUBLIE = 7;
  
  // Array :
  //   Contient la liste des phrases représentatifs des erreurs renvoyer par self::verifIssetForm()
  public static $erreur_texte = array(
    'Vous en avez s&eacute;lectionn&eacute; trop',
    'Vous en avez pas assez s&eacute;lectionn&eacute;',
    'Vous devez mettre un email',
    'Vous devez mettre un chiffre',
    'Vous devez mettre un num&eacute;ro de t&eacute;l&eacute;phone',
    'Vous avez mis trop de caract&egrave;res',
    'Vous n\'avez pas mis assez de caract&egrave;res',
    'Vous avez oubli&eacute; un champ'
  );
  // Bool :
  //   Affiche les messages d'erreur
  public static $erreur_afficher = true;
  // Array :
  //   Balises pour les messages d'erreur
  public static $erreur_balise = array('<p class="form_erreur">','','</p>');
  // Array :
  //   Liste des champs où se trouve une erreur
  public static $erreur_liste = array();
  // String :
  //   Nom des class à rajouter aux erreurs
  public static $classname_erreur = '';
  // String :
  //   Nom des class à rajouter valider
  public static $classname_valide = '';
  
  // -------------------------------------
  // Array :
  //   Statistique des réponses
  public static function getStats( array $formulaire, array $reponses )
  {
    // initialise
    $stats = array();
    foreach( $formulaire AS $form )
    {
      // enregistre les informations
      $stats[$form['fq_label']]['texte'] = $form['fq_texte'];
      // si on a plusieurs choix
      if( isset($form['fq_option']['choix']) ) {
        // initialise
        $stats[$form['fq_label']]['valeur'] = array();
        foreach( $form['fq_option']['choix'] AS $key => $choix )
        {
          $stats[$form['fq_label']]['valeur'][$key]['texte'] = $choix;
          $stats[$form['fq_label']]['valeur'][$key]['valeur'] = 0;
        }
      }
      else
        $stats[$form['fq_label']]['valeur'] = 0;
    }
    // on remplie
    foreach( $reponses AS $info ) {
      // value contient les réponses
      foreach( $info['fr_value'] AS $label => $value ) {
        // si c'est un choix
        if( is_array($stats[$label]['valeur']) ) {
          // si c'est un checkbox
          if( is_array($value) ) {
            foreach( $value AS $alias )
              $stats[$label]['valeur'][$alias]['valeur']++;
          }
          else
            $stats[$label]['valeur'][$value]['valeur']++;
        }
        else
          $stats[$label]['valeur']++;
      }
    }
    return $stats;
  }

  // -------------------------------------
  // String :
  //   Code Html du rendu final du formulaire
  public static function getHtmlResult( array $formulaire )
  {
    // contient le code html
    $html = '';
    // on parcour le tableau qui contient les informations sur chaque champs du formulaire
    foreach( $formulaire AS $infos_form )
    {
      // initialise
      $infos = array();
      // exemple : Formulaire\Select
      $formformat = 'pok\Apps\Formulaire\\'.ucfirst($infos_form['fq_inputype']);
      // récupère l'objet correspondant : Textarea, Simple, Select, ...
      $nf = new $formformat();

      // si on spécifie une class pour la <div> qui contient l'input !
      if( isset(self::$erreur_liste[$infos_form['fq_label']]) && self::$classname_erreur != '' )
        $nf->classname_detail .= ' ' . self::$classname_erreur;
      elseif( !isset(self::$erreur_liste[$infos_form['fq_label']]) && self::$classname_valide != '' )
        $nf->classname_detail .= ' ' . self::$classname_valide;
      
      // si le champs et un <textarea> ou un simple <input type="text"/>
      if( $infos_form['fq_inputype'] == 'textarea' || $infos_form['fq_inputype'] == 'simple' ) {
        
        $nf->init( $infos_form['fq_label'], $infos_form['fq_label'], $infos_form['fq_option']['value'], $infos_form['fq_texte'] );
      }
      else
      {
        // comme select demande des infos différemment des autres 
        if( $infos_form['fq_inputype'] == 'select' )
        {
          foreach( $infos_form['fq_option']['choix'] AS $choix_key => $choix_value )
            $infos[] = array( 'value' => $choix_key, 'texte' => $choix_value );
          // on envoie tout ça !
          $nf->init( $infos, $infos_form['fq_option']['value'], $infos_form['fq_texte'], $infos_form['fq_label'], $infos_form['fq_label'] );
        }
        elseif( !empty($infos_form['fq_option']) && array_key_exists( 'choix', $infos_form['fq_option'] ) )
        {
          // la checkbox et radio demande plus d'information que le select
          foreach( $infos_form['fq_option']['choix'] AS $choix_key => $choix_value )
            $infos[] = array( 'name' => $infos_form['fq_label'], 'id' => $choix_key, 'value'=> $choix_key, 'texte' => $choix_value );
          // comme select à peu près
          $nf->init( $infos, $infos_form['fq_option']['value'], $infos_form['fq_texte'] );
        }
      }
      // on créer les champs et leur descriptifs
      $nf->description();
      $nf->input();

      // s'il y a une erreur détecté
      if( isset(self::$erreur_liste[$infos_form['fq_label']]) )
      {
        // on ajoute peut-être un le texte d'erreur
        if( self::$erreur_afficher )
          $html .= self::$erreur_balise[0] . self::$erreur_texte[self::$erreur_liste[$infos_form['fq_label']]] . self::$erreur_balise[1];
        // il faut quand même afficher le formulaire
        $html .= $nf->result();
        // on ajoute la fin du message d'erreur
        if( self::$erreur_afficher )
          $html .= self::$erreur_balise[2];
      }
      // sinon on enregistre simplement le résultat
      else
      {
        $html .= $nf->result();
      }
    }
    return $html;
  }

  // -------------------------------------
  // Int/Bool :
  //   Renvoie un booléen TRUE si aucun problème, par contre renvoie un chiffre en cas d'erreur
  public static function verifIssetForm( $reponse )
  {
    // si la réponse du formulaire exite, sinon c'est qu'il y a un gros problème
    if( isset($_POST[$reponse['fq_label']]) )
    {
      // si c'est un checkbox
      if( $reponse['fq_inputype'] == 'checkbox' )
      {
        // on compte le nombre de réponse total
        $nb_ckeck_reponse = count($_POST[$reponse['fq_label']]);
        // et on vérifie que ça correspond au nombre voulu
        if( $nb_ckeck_reponse > $reponse['fq_option']['nbchoix_max'] )
          return self::TROP_SELECTION;
        elseif( $nb_ckeck_reponse < $reponse['fq_option']['nbchoix_mini'] )
          return self::PEU_SELECTION;
      }
      // sinon si c'est un simple input ou un textarea
      elseif( $reponse['fq_inputype'] == 'simple' || $reponse['fq_inputype'] == 'textarea' )
      {
        // Calcule la taille de la chaîne
        $nb_str = strlen($_POST[$reponse['fq_label']]);
        // dans le cas où on demande que l'utilisateur rentre des donénes spéciaux
        if( isset($reponse['fq_option']['type']) )
        {
          switch($reponse['fq_option']['type'])
          {
            case 'email':
              if( filter_var( $_POST[$reponse['fq_label']], FILTER_VALIDATE_EMAIL ) === false ) return self::MAIL;
            break;
            case 'chiffre':
              if( !ctype_digit($_POST[$reponse['fq_label']]) ) return self::CHIFFRE;
            break;
            case 'phone':
              if( !Format::telephone($_POST[$reponse['fq_label']]) ) return self::TELEPHONE;
            break;
          }
        }
        // on vérifie que le texte correspond au bon nombre de caractère voulu
        if( $reponse['fq_option']['str_max'] < $nb_str )
          return self::TROP_STR;
        elseif( $reponse['fq_option']['str_mini'] > $nb_str )
          return self::PEU_STR;
      }
      return true;
    }
    // il est possible qu'il manque un le checkbox quand il y a aucun choix
    else
    {
      if( $reponse['fq_inputype'] == 'checkbox' )
      {
        // par contre il faut qu'on demande aucune donnée sinon c'est qu'on a oublier
        if( $reponse['fq_option']['nbchoix_mini'] == 0 )
          return true;
        else
          return self::PEU_SELECTION;
      }
      else return self::OUBLIE;
    }
  }
}
