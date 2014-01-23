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

// Base pour le CMS
require('templates/init.php');

use pok\Controleur;
use pok\Apps\Page;
use pok\Apps\Formulaire;
use pok\Apps\FormulaireReponse;
use pok\Apps\FormulaireQuestion;
use pok\Apps\Outils\Cfg;

if( empty($_GET['article']) )
	Controleur\Page::redirect('admin/article.php');

// gestion global des formulaires
if( isset($_GET['reponse']) )
{
  // on charge la liste des templates
  admin\templates\Pages::parse( 'form', array(
    'infos_reponse' => FormulaireReponse::fetchByArticleId($_GET['article'])
  ), 'reponse' );
}
elseif( isset($_GET['stats']) )
{
  // données du formulaire
  $formulaire = FormulaireQuestion::fetchByArticleId($_GET['article']);
  // réponses du formulaire
  $infos_reponse = FormulaireReponse::fetchByArticleId($_GET['article']);
  
  $nombre_de_reponse = count($infos_reponse);
  
  // on charge la liste des templates
  admin\templates\Pages::parse( 'form', array(
    'result' => Formulaire::getStats( $formulaire, $infos_reponse ),
    'nombre_de_reponse' => count($infos_reponse)
  ), 'stats' );
}
elseif( !empty($_GET['viewreponse']) )
{
  // données du formulaire
  $formulaire = FormulaireQuestion::fetchByArticleId($_GET['article']);
  // réponse du formulaire
  // ne pas oubliez que la fonction renvoie un $_POST enregistrer
  $reponse  = new FormulaireReponse(array(array(
    'formulaire_reponse.article_id' => array( '=', $_GET['article'] ),
    'formulaire_reponse.fr_id'      => array( '=', $_GET['viewreponse'] )
  )));
  $reponse_user = $reponse->publier();
  // on change les valeurs
  foreach( $formulaire AS $cle => $reponse ) {
    $formulaire[$cle]['fq_option']['value'] = $reponse_user[0]['value'][$reponse['fq_label']];
  }
  unset($reponse);
  // le formulaire avec les réponses du membre
  admin\templates\Pages::parse( 'form', array(
    'html' => Formulaire::getHtmlResult($formulaire)
  ), 'voir_reponse' );
}
elseif( isset($_GET['creer']) )
{
  // on charge la liste des templates
  admin\templates\Pages::parse( 'form', array(), 'creer' );
}
elseif( !empty($_GET['modif']) )
{
  $form = new FormulaireQuestion(array(array(
    'formulaire_question.article_id' => array( '=', $_GET['article'] ),
    'formulaire_question.fq_id'      => array( '=', $_GET['modif'] )
  )));
  $formulaire = $form->publier();
  
  switch( $formulaire[0]['fq_inputype'] )
  {
    case 'checkbox':
      admin\templates\Pages::parse( 'form', array(
        'id' => $formulaire[0]['fq_id'],
        'label' => $formulaire[0]['fq_label'],
        'choix' => $formulaire[0]['fq_option']['choix'],
        'nb_choix' => count($formulaire[0]['fq_option']['choix']),
        'value' => $formulaire[0]['fq_option']['value'],
        'texte' => $formulaire[0]['fq_texte'],
        'nbchoix_mini' => $formulaire[0]['fq_option']['nbchoix_mini'],
        'nbchoix_max' => $formulaire[0]['fq_option']['nbchoix_max']
      ), 'modif_checkbox');
    break;
    case 'radio':
      admin\templates\Pages::parse( 'form', array(
        'id' => $formulaire[0]['fq_id'],
        'label' => $formulaire[0]['fq_label'],
        'choix' => $formulaire[0]['fq_option']['choix'],
        'nb_choix' => count($formulaire[0]['fq_option']['choix']),
        'value' => $formulaire[0]['fq_option']['value'],
        'texte' => $formulaire[0]['fq_texte']
      ), 'modif_radio');
    break;
    case 'select':
      admin\templates\Pages::parse( 'form', array(
        'id' => $formulaire[0]['fq_id'],
        'label' => $formulaire[0]['fq_label'],
        'choix' => $formulaire[0]['fq_option']['choix'],
        'nb_choix' => count($formulaire[0]['fq_option']['choix']),
        'value' => $formulaire[0]['fq_option']['value'],
        'texte' => $formulaire[0]['fq_texte']
      ), 'modif_select');
    break;
    default:
      admin\templates\Pages::parse( 'form', array(
        'id' => $formulaire[0]['fq_id'],
        'label' => $formulaire[0]['fq_label'],
        'value' => $formulaire[0]['fq_option']['value'],
        'str_mini' => $formulaire[0]['fq_option']['str_mini'],
        'str_max' => $formulaire[0]['fq_option']['str_max'],
        'texte' => $formulaire[0]['fq_texte'],
        'type' => $formulaire[0]['fq_option']['type'],
        'default_type' => array(
          'texte'   => 'Texte',
          'chiffre' => 'Chiffre',
          'email'   => 'E-mail',
          'phone'   => 'Téléphone'
        )
      ), 'modif');
    break;
  }
}
else
{
  // on charge la liste des templates
  admin\templates\Pages::parse( 'form', array(
    'formulaire' => FormulaireQuestion::fetchByArticleId($_GET['article'])
  ), 'list');
}
