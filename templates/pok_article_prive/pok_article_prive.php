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

use templates\pok_accueil\Controleur\fonctions,
    pok\Apps\Outils\Session,
    pok\Controleur\Page,
    pok\Template;

Template::importerFonctions('pok_accueil');

/*
  On inclue le template _header simplement.
  Si vous voulez héritez du controlleur du template _header,
  vous devez l'inclure dans le controlleur de ce template.
*/
Template::integrer('_pok_header');

?>
  <p>
    <a href="<?php echo Page::url(Page::$mere['arborescence']);?>"><?php echo Page::$mere['page_nom'];?></a>
<?php

// fil d'arine central
fonctions\affiche_fil_ariane($arborescence);
// voir message reçu
if( !empty($voirecu) )
  echo ' &raquo; ' , $voirecu['article_titre'];

?>
  </p>
</div>
<div class="content">
  <div class="page">
    <div id="corps">
      <h2>Article priv&eacute;</h2>
      <p>
        <a href="<?php echo Page::url(Page::$actuelle['arborescence']) ?>">Boite de r&eacute;ception</a>
        | <a href="<?php echo Page::url(Page::$actuelle['arborescence'], '', '&amp;envoie') ?>">Boite d'envoie</a>
        | <a href="<?php echo Page::url(Page::$actuelle['arborescence'], '', '&amp;brouillon') ?>">Brouillons</a>
        | <a href="<?php echo Page::url(Page::$actuelle['arborescence'], '', '&amp;nouveau') ?>">Nouveau message</a>
      </p>
<?php
/* -------------------------------------
  Affiche les messages d'erreur
*/
if( isset($_GET['erreur']) )
{
?>
  <div class="avertissement">
    Une erreur est survenu.
<?php
  if( isset($_GET['aucun_pseudo_messagerie']) )
  {
?>
    <br />Vous n'avez mis aucun destinataire.
<?php
  }
  elseif( isset($_GET['delete']) )
  {
?>
    <br />Le message n'a pas pu être supprimé.
<?php
  }
?>
  </div>
<?php

}
elseif( isset($_GET['ok']) )
{
  echo '<div class="valide">L\'action &agrave; bien fonctionner.</div>';
}
/* -------------------------------------
  Pour savoir ce que l'on fait dans la boite,
  genre si on regarde nos messages reçu, envoyer ou
  si on créer un message, on supprime, on regarde toussa ..
*/
if( !empty($voirecu) )
{
/* -------------------------------------
  VOIR UN MESSAGE EN DETAIL / MESSAGE RECU
*/
?>
<p style="float: right;">| <a href="<?php echo Page::url(Page::$actuelle['arborescence'], '', array('repondre' => $voirecu['article_id'])) ?>">R&eacute;pondre</a> | <a href="controleur.php?tpl=pok_messagerie&amp;ctrl=supprimer&amp;mess=<?php echo $voirecu['article_id'] ?>&amp;jeton=<?php echo $_SESSION['jeton'] ?>">x</a> |</p>
<div id="mess-<?php echo $voirecu['article_id'] ?>" class="post">
  <h3><?php echo $voirecu['article_titre'] ?></h3>
  <small>Le <?php echo fonctions\date_forme($voirecu['article_date_creer']) ?></small><br />
  <strong><?php if( $voirecu['article_auteur'] > 0 ) echo '<a href="',Page::url($voirecu['arbo_auteur']),'">',$voirecu['pseudo_auteur'],'</a>'; else echo $voirecu['pseudo_auteur']; ?></strong>, <?php echo $liste_destinataire ?>
  <div class="entry"><?php echo $voirecu['article_titre'] ?></div>
</div>
<?php
  /* -------------------------------------
    on affiche les réponses
  */
  if( !empty($reponses) )
  {
    echo '<blockquote>';
    
    foreach( $reponses AS $rep )
    {

?>
  <hr />
  <strong><?php if( $rep['article_auteur'] > 0 ) echo '<a href="',Page::url($rep['arbo_auteur']),'">',$rep['pseudo_auteur'],'</a>'; else echo $rep['pseudo_auteur']; ?></strong>
  <small>Le <?php echo $rep['article_date_creer'] ?></small>
  <div class="entry"><?php echo $rep['article_texte'] ?></div>
<?php

    }
  }
  
  echo '</blockquote>';
}
elseif( isset($_GET['nouveau']) )
{
/* -------------------------------------
  Nouveau message
*/
?>
    <h3>Nouveau message</h3>
    <form method="post" action="posting.php?page=<?php echo Page::$actuelle['page_id'];?>&amp;reference=0&amp;jeton=<?php echo $_SESSION['jeton'] ?>">
      <p>
      Si vous mettez plusieurs destinataires, s&eacute;parez les pseudos par une virgule : Pseudo, Pseudo, ...<br />
      <label for="brouillon">Brouillon : </label><input type="checkbox" name="brouillon" id="brouillon" /><br />
      <label for="chapo">Destinataire(s): </label><input type="text" id="chapo" name="chapo" /><br />
      <label for="titre">Titre : </label><input type="text" name="titre" id="titre" /><br />
      <textarea name="text" style="width:400px;height:150px;"></textarea>
      </p>
      <input type="submit" value="Envoyer" />
    </form>
<?php

}
elseif( isset($_GET['envoie']) )
{
/* -------------------------------------
  Boite d'envoie
*/
?>
<h3>Boite d'envoie</h3>
<?php

  if( empty($_GET['envoie']) )
  {
    if( !empty($envoie) )
    {
    
?>
  <ul class="boite_reception">
<?php

      // boucle qui affiche la liste des messages
      foreach( $envoie AS $recu )
      {

?>
    <li>
      &Agrave; <a href="<?php echo Page::url(Page::$actuelle['arborescence'], '', array('envoie' => $recu['article_id'] )) ?>"><?php echo $recu['article_titre'] ?></a>
    </li>
<?php

      }

?>
  </ul>
<?php
    
    }
    else
    {
?>
  <p>Vous n'avez aucun message !</p>
<?php
    }
  }
  else
  {

?>
<p style="float: right;">|<a href="./controleur.php?tpl=pok_messagerie&amp;ctrl=supprimer&amp;mess=<?php echo $envoie['article_id'] ?>&amp;jeton=<?php echo $_SESSION['jeton'] ?>">x</a>|</p>
<div id="mess-{$envoie.0.id}" class="post">
  <h3><?php echo $envoie['article_titre'] ?></h3>
  <small><?php echo $envoie['article_date_creer'] ?></small><br />
  <strong><?php if( $envoie['article_auteur'] > 0 ) echo '<a href="',Page::url($envoie['arbo_auteur']),'">',$envoie['pseudo_auteur'],'</a>'; else echo $envoie['pseudo_auteur']; ?></strong>
  <?php echo $liste_destinataire ?>
  <div class="entry"><?php echo $envoie['article_texte'] ?></div>
</div>
<?php

  }
}
elseif( isset($_GET['brouillon']) )
{
/* -------------------------------------
  Les brouillons
*/
?>
<h3>Brouillons</h3>
<?php

  if( empty($_GET['brouillon']) )
  {
    if( !empty($brouillon) )
    {
?>
  <ul class="boite_reception">
<?php

      // boucle qui affiche la liste des messages
      foreach( $brouillon AS $recu )
      {
?>
    <li>
      <a href="<?php echo Page::url(Page::$actuelle['arborescence'], '', array('brouillon' => $recu['article_id'] )) ?>"><?php echo $recu['article_titre'] ?></a>
    </li>
<?php
      }

?>
  </ul>
<?php

    }
    else
    {
?>
  <p>Vous n'avez aucun message !</p>
<?php
    }
  }
  else
  {

?>
    <p style="float: right;">| <a href="<?php echo Page::url(Page::$actuelle['arborescence'], '', array('repondre' => $brouillon['article_id'] )) ?>">R&eacute;pondre</a> | <a href="./controleur.php?tpl=pok_messagerie&amp;ctrl=supprimer&amp;mess=<?php echo $brouillon['article_id'] ?>&amp;jeton=<?php echo $_SESSION['jeton'] ?>">x</a> |</p>
    <form method="post" action="posting.php?page=<?php echo Page::$actuelle['page_id'] ?>&amp;reference=0&amp;article=<?php echo $brouillon['article_id'] ?>&amp;jeton=<?php echo $_SESSION['jeton'] ?>">
      <p>
      Si vous mettez plusieurs destinataires, s&eacute;parez les pseudos par une virgule : Pseudo, Pseudo, ...<br />
      <label for="brouillon">Brouillon : </label><input type="checkbox" checked="checked" name="brouillon" id="brouillon" /><br />
      <label for="chapo">Destinataire(s): </label><input type="text" id="chapo" name="chapo" value="<?php echo $brouillon['article_chapo'] ?>" /><br />
      <label for="titre">Titre : </label><input type="text" name="titre" id="titre" value="<?php echo $brouillon['article_titre'] ?>" /><br />
      <textarea name="text" style="width:400px;height:150px;"><?php echo $brouillon['article_texte'] ?></textarea>
      </p>
      <input type="submit" value="Envoyer" />
    </form>
<?php

  }
}
elseif( !empty($_GET['repondre']) )
{
/* -------------------------------------
  Répondre
*/
?>
    <h3>R&eacute;pondre</h3>
    <form id="postform" method="post" action="posting.php?page=<?php echo Page::$actuelle['page_id'] ?>&amp;reference=<?php echo $_GET['repondre'] ?>&amp;jeton=<?php echo $_SESSION['jeton'] ?>">
      <p><textarea name="text" style="width:400px;height:150px;"></textarea></p>
      <input type="submit" value="R&eacute;pondre" />
    </form>
<?php

}
else
{
/* -------------------------------------
  BOITE DE RECEPTION
*/
?>
<h3>Boite de r&eacute;ception</h3>
<?php
  
  if( !empty($messrecu) )
  {

?>
  <ul class="boite_reception">
<?php

    // Boucle qui affiche la liste des messages
    foreach( $messrecu AS $recu )
    {

?>
      <li>
        De <a href="<?php echo Page::url(Page::$actuelle['arborescence'], '', '&amp;voirecu='.$recu['article_id']) ?>"><?php echo $recu['pseudo_auteur'] ?> | <?php echo $recu['article_titre'] ?></a>
      </li>
<?php
    }
?>
  </ul>
<?php

  }
  else
  {

?>
  <p>Vous n'avez aucun message !</p>
<?php

  // Fin de la boite de reception
  }
// Fin du choix de la boite
}

?>
</div>
<?php

/* -------------------------------------
  On inclue le pied de la page
*/
Template::integrer('_pok_footer', array( 'chrono' => $chrono ));
