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
require('../templates/init.php');

use pok\Texte,
    pok\Apps\FormulaireQuestionModif,
    pok\Apps\Outils\Session,
    pok\Apps\Outils\Base\Fichier,
    pok\Apps\Models\Base\Requete\PDOFournie,
    pok\Controleur\Page AS CPage;

if( Session::verifieJeton(0) && !empty($_GET['article']) )
{
  // On creer un champ normal
  if( isset($_GET['normal']) )
  {
      // on doit réécrire le tableau
      $formulaire = array(
        'fq_inputype'  => 'Simple',
        'fq_label'     => htmlspecialchars($_POST['normal_label']),
        'fq_texte'     => htmlspecialchars($_POST['normal_desc']),
        'fq_option'    => array(array(
          'str_mini'    => $_POST['normal_strmini'],
          'str_max'     => $_POST['normal_strmax'],
          'value'       => htmlspecialchars($_POST['normal_value']),
          'type'        => $_POST['normal_type']
        ))
      );
    }
  // On creer un champ checkbox
  elseif( isset($_GET['checkbox']) )
  {
      // on doit réécrire le tableau
      $formulaire = array(
        'fq_inputype'  => 'Checkbox',
        'fq_label'     => htmlspecialchars($_POST['checkbox_label']),
        'fq_texte'     => htmlspecialchars($_POST['checkbox_desc']),
        'fq_option'    => array(array(
          'nbchoix_mini' => $_POST['checkbox_selmini'],
          'nbchoix_max'  => $_POST['checkbox_selmax'],
          'value'        => array(),
          'choix'        => array()
        ))
      );
      // on enregistre les choix
      foreach( $_POST['choix'] AS $choix )
      {
        if( !empty($choix['alias']) || !empty($choix['texte']) )
        {
          $formulaire['fq_option'][0]['choix'][$choix['alias']] = $choix['texte'];
          // on en profite pour les sélectionners
          if( $choix['value'] )
            $formulaire['fq_option'][0]['value'][] = $choix['alias'];
        }
      }
    }
  // On creer un champ radio
  elseif( isset($_GET['radio']) )
  {
    // on doit réécrire le tableau
    $formulaire = array(
      'fq_inputype'  => 'Radio',
      'fq_label'     => htmlspecialchars($_POST['radio_label']),
      'fq_texte'     => htmlspecialchars($_POST['radio_desc']),
      'fq_option'    => array(array(
        'value'       => '',
        'choix'       => array()
      ))
    );
    foreach( $_POST['choix'] AS $choix )
      if( !empty($choix['alias']) || !empty($choix['texte']) )
        $formulaire['fq_option'][0]['choix'][$choix['alias']] = $choix['texte'];
    
    // on en profite pour les sélectionners
    if( !empty($_POST['choix'][$_POST['value']]['alias']) && !empty($_POST['choix'][$_POST['value']]['texte']) )
      $formulaire['fq_option'][0]['value'] = $_POST['choix'][$_POST['value']]['alias'];
    else
      $formulaire['fq_option'][0]['value'] = $_POST['choix'][0]['alias'];
  }
  // On creer un champ checkbox
  elseif( isset($_GET['select']) )
  {
      $formulaire = array(
        'fq_inputype'  => 'Select',
        'fq_label'     => htmlspecialchars($_POST['select_label']),
        'fq_texte'     => htmlspecialchars($_POST['select_desc']),
        'fq_option'    => array(array(
          'value'       => '',
          'choix'       => array()
        ))
      );
      // c'est exactement comme radio
      foreach( $_POST['choix'] AS $choix )
        if( !empty($choix['alias']) || !empty($choix['texte']) )
          $formulaire['fq_option'][0]['choix'][$choix['alias']] = $choix['texte'];
      
      if( !empty($_POST['choix'][$_POST['value']]['alias']) && !empty($_POST['choix'][$_POST['value']]['texte']) )
        $formulaire['fq_option'][0]['value'] = $_POST['choix'][$_POST['value']]['alias'];
      else
        $formulaire['fq_option'][0]['value'] = $_POST['choix'][0]['alias'];
    }
  // On creer un champ textarea
  elseif( isset($_GET['textarea']) )
  {
      $formulaire = array(
        'fq_inputype'  => 'Textarea',
        'fq_label'     => htmlspecialchars($_POST['textarea_label']),
        'fq_texte'     => htmlspecialchars($_POST['textarea_desc']),
        'fq_option'    => array(array(
          'str_mini'    => $_POST['textarea_strmini'],
          'str_max'     => $_POST['textarea_strmax'],
          'value'       => htmlspecialchars($_POST['textarea_value'])
        ))
      );
    }
  else
    CPage::redirect('admin/form.php?ereurinput&article='.$_GET['article']);
  
  if( isset($_GET['modif']) && !empty($_GET['modif']) )
  {
    $form = new FormulaireQuestionModif(array_merge( $formulaire, array(
      'fq_id'      => $_GET['modif'],
      'article_id' => $_GET['article']
    ) ));
    // on enregistre et on gère l'erreur
    $form->modifier();
    
    Fichier::log('<ID:' . $_SESSION['id'] . '> modifie formulaire n°' . $_GET['modif']);
    CPage::redirect('admin/form.php?ok&article='.$_GET['article']);
  }
  else
  {
    $form = new FormulaireQuestionModif(array_merge( $formulaire, array( 'article_id' => $_GET['article'] ) ));
    $id = $form->ajouter();
    // on enregistre et on gère l'erreur
    if( $id > 0 )
    {
      Fichier::log('<ID:' . $_SESSION['id'] . '> creer formulaire type "' . $formulaire['fq_inputype'] . '" n°' . $id . ' de l\'article n°' . $_GET['article']);
      CPage::redirect('admin/form.php?ok&article='.$_GET['article']);
    }
    else
      CPage::redirect('admin/form.php?creer&erreur&article='.$_GET['article']);
  }
}
CPage::redirect('@revenir');
