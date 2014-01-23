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

use templates\pok_accueil\Controleur\fonctions AS faccueil,
    templates\pok_forum_view\Controleur,
    templates\pok_forum_view\Controleur\fonctions,
    pok\Apps\Membre,
    pok\Apps\Outils\Session,
    pok\Controleur\Page,
    pok\Controleur\Droit,
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
faccueil\affiche_fil_ariane($arborescence);
// s'il y a un topic
if( !empty($topic) )
  echo ' &raquo; ' , $topic['article_titre'];

?>
  </p>
</div>
<div class="content">
  <script type="text/javascript">
    function verif( url ) {
      if( confirm( 'êtes-vous sûr de vouloir supprimer ?' ) ) {
        document.location.href = url;
      }
    }
  </script>
  <div class="page">
    <div id="corps">
<?php
/*
  Si l'utilisateur veux créer un nouveau sujet, c'est indiquer dans l'url
  Comme pour tous les possibilités d'affichage dans le corps, on passe par les $_GET
*/
if( isset($_GET['newtopic']) )
{
  /*
    On regarde si l'utilisateur a les droits pour créer un nouveau sujet
  */
  if( Droit::$euna || Session::connecter() && $_SESSION['statut'] === Membre::ADMIN )
  {

?>
  <h3 style="margin-top:20px;">Nouveau sujet</h3>
  <form method="post" action="posting.php?page=<?php echo Page::$actuelle['page_id'];?>&amp;reference=0&amp;jeton=<?php echo $_SESSION['jeton'] ?>">
    <p><label for="titre">Titre : </label><input type="text" name="titre" id="titre" style="width:100%;" /></p>
    <textarea id="bbcode" style="width:100%;" name="text"></textarea>
    <p align="center"><input type="submit" value="Cr&eacute;ez le nouveau sujet" /></p>
  </form>
<?php
  }
  else
  {
?>
	<p>Vous ne pouvez pas cr&eacute;er de nouveau sujet !</p>
<?php

  }
}
/* -------------------------------------
  S'il n'existe pas $_GET['article'], cela veux dire qu'on est à l'index des forums et qu'on liste tout !
*/
elseif( !isset($_GET['article']) )
{

?>
  <div style="float: right; margin-top: 8px;">Page : <?php echo faccueil\pagination( $numpage, $nb_topics ) ?></div>
  <p><a href="<?php echo Page::url(Page::$actuelle['arborescence'],'','&amp;newtopic') ?>"><img src="<?php echo Page::getAdresse() ?>web/images/pok_forum_index/newtopic.png" alt="Cr&eacute;er un nouveau sujet" title="Cr&eacute;er un nouveau sujet" /></a></p>
<?php

  if( !empty($topics) )
  {

?>
  <table style="width:100%">
    <tr>
      <th colspan="3">Topics</th>
      <th>R&eacute;ponses</th>
      <th>Visites</th>
      <th>Dernier post</th>
    </tr>
<?php

    foreach( $topics AS $topic )
    {

?>
    <tr>
      <td>
<?php
/*
<!-- Dans la cas ou on a activé le systeme lu/non-lu, on a affiche si ce forum a été lu ! -->
      {if( {*dossier_nonlu} )}
        <!-- Malheureusement on ne peut pas appeller dynamiquement la fonction puisque les données s'affiche en fonction de l'utilisateur -->
        <img src="images/pok_accueil/{ef=pok\article_vu( {*_topic.id_user_vu}, {*_topic.id_sous_vu}, {*_topic.dernier_posts_id}, {*_topic.sous_vu_poster} )}.png" />
      {/if}
*/
?>
      </td>
      <td> &raquo;
        <strong class="titre"><a href="<?php echo Page::url( Page::$actuelle['arborescence'], $topic['article_slug'] ) ?>"><?php echo $topic['article_titre'] ?></a></strong><br />
        <small>Par <b><a href="<?php echo Page::url($topic['arbo_auteur']) ?>" style="color: #<?php echo $topic['couleur_groupe'] ?>;"><?php echo $topic['pseudo_auteur'] ?></a></b> le <?php echo faccueil\date_forme($topic['article_date_creer']);?></small>
      </td>
      <td align="right"><img src="<?php echo Page::getAdresse() ?>web/images/pok_forum_index/icon_pages.png" alt="Page :" title="Page(s)" /> <?php echo faccueil\pagination( $numpage, ceil( $topic['count'] / Controleur::POST_PAR_PAGE ), $topic['article_slug'] ) ?></td>
      <td align="center"><?php echo $topic['count'] ?></td>
      <td align="center"><?php echo fonctions\sujet_vu($topic['article_id']) ?></td>
      <td align="center">
<?php
      if( !empty($topic['last_pseudo_auteur']) ) {
?>
        <small><strong style="color: #<?php echo $topic['last_couleur'] ?>"><?php echo $topic['last_pseudo_membre'] ?></strong>
<?php
      } else {
?>
        <small><strong style="color: #<?php echo $topic['last_couleur'] ?>"><?php echo $topic['pseudo_auteur'] ?></strong>
<?php
      }
?>
        <a href="<?php echo Page::url( Page::$actuelle['arborescence'], $topic['article_slug'], '&amp;numpage='.ceil( $topic['count'] / Controleur::TOPIC_PAR_PAGE ) ),'#p',$topic['last_article_id'] ?>"><img src="<?php echo Page::getAdresse() ?>web/images/pok_forum_index/lastpost.png" alt="Dernier post" title="Voir la derni&egrave;re r&eacute;ponse" /></a>
        <br /><?php echo $topic['article_date_creer'] ?></small>
      </td>
    </tr>
<?php
    }
?>
  </table>
<?php
  }
  else
  {
?>
   <p>Aucun sujet !</p>
<?php

  }
}
// Ici on regarde un sujet en particularité
else
{
  /* -------------------------------------
    Si on REPOND à un sujet
  */
  if( isset($_GET['repondre']) )
  {
    echo '<h2><a href="',Page::url( Page::$actuelle['arborescence'], $topic['article_slug'] ),'">',$topic['article_titre'],'</a></h2>';
    
    if( Droit::$raa && $topic['niveau_comments'] > 0 || Session::connecter() && $_SESSION['statut'] === Membre::ADMIN )
    {
    
?>
    <h3>Laisser une r&eacute;ponse</h3>
    <form method="post" action="posting.php?page=<?php echo Page::$actuelle['page_id'];?>&amp;reference=<?php echo $topic['article_id'] ?>&amp;jeton=<?php echo $_SESSION['jeton'] ?>">
      <textarea id="bbcode" style="width:100%;" name="text"></textarea>
      <p><input type="submit" value="R&eacute;pondre" /></p>
    </form>
<?php
    }
    else
    {
?>
    <p>Vous ne pouvez pas r&eacute;pondre &agrave; ce sujet.</p>
<?php

    }
  }
  /* -------------------------------------
    Si on EDIT à un sujet ou une réponse
  */
  elseif( !empty($editer) )
  {
    echo '<h2><a href="',Page::url( Page::$actuelle['arborescence'], $topic['article_slug'] ),'">',$topic['article_titre'],'</a></h2>';
    
    if( Droit::$raa && $topic['niveau_comments'] > 0 || Session::connecter() && $_SESSION['statut'] === Membre::ADMIN )
    {

?>
    <h3 id="edit">Editer un message</h3>
    <form method="post" action="posting.php?page=<?php echo Page::$actuelle['page_id'];?>&amp;reference=<?php echo $editer['article_parent'] ?>&amp;article=<?php echo $editer['article_id'] ?>&amp;jeton=<?php echo $_SESSION['jeton'] ?>">
<?php
      if( $editer['article_parent'] == 0 )
      {
?>
      <label for="titre">Titre :</label>
      <input type="text" name="titre" id="titre" value="<?php echo $editer['article_titre'] ?>" style="width:100%;" /><br />
<?php
      }
?>
      <textarea id="bbcode" style="width:100%;" name="text"><?php echo pok\Texte::br2nl($editer['article_texte']) ?></textarea>
      <p><input type="submit" value="Editer" /></p>
    </form>
<?php
    }
    else
    {
?>
    <p>Vous ne pouvez pas &eacute;diter ce sujet.</p>
<?php

    }
  }
  /* -------------------------------------
    Si on CITE une réponse
  */
  elseif( !empty($quote) )
  {
    echo '<h2><a href="',Page::url( Page::$actuelle['arborescence'], $topic['article_slug'] ),'">',$topic['article_titre'],'</a></h2>';

    if( Droit::$raa && $topic['niveau_comments'] > 0 || Session::connecter() && $_SESSION['statut'] === Membre::ADMIN )
    {

?>
    <h3>Laisser une r&eacute;ponse</h3>
      <form method="post" action="posting.php?page=<?php echo Page::$actuelle['page_id'];?>&amp;reference=<?php echo $topic['article_id'] ?>&amp;jeton=<?php echo $_SESSION['jeton'] ?>">
        <textarea id="bbcode" style="width:100%;" name="text">[quote=<?php echo $quote['pseudo_auteur'] ?>]<?php echo pok\Texte::br2nl($quote['article_texte']) ?>[/quote]</textarea>
        <p><input type="submit" value="R&eacute;pondre" /></p>
      </form>
<?php
    }
    else
    {
?>
    <p>Vous ne pouvez pas r&eacute;pondre &agrave; ce sujet.</p>
<?php

    }
  }
  /* -------------------------------------
    Sinon on affiche le sujet :
    on vérifie que le sujet existe bien
  */
  elseif( !empty($topic) )
  {
    // On ajoute un "vu" au sujet
    fonctions\ajoute_vu($topic['article_id']);
?>
<div id="page-forum">
  <h2><a href="<?php echo Page::url( Page::$actuelle['arborescence'], $topic['article_slug'] ) ?>"><?php echo $topic['article_titre'] ?></a></h2>
  <div style="float: right; margin-top: 8px;">Page : <?php echo faccueil\pagination( $numpage, $nb_post, $topic['article_slug'] ) ?></div>
  <p><a href="<?php echo Page::url( Page::$actuelle['arborescence'], $topic['article_slug'], '&amp;repondre' ) ?>"><img src="<?php echo Page::getAdresse() ?>web/images/pok_forum_index/post<?php echo ( ($topic['niveau_comments'] <= 0)? 'locked' : 'retry' ) ?>.png" alt="Laisser une r&eacute;ponse" title="Laisser une r&eacute;ponse" /></a></p>
  <!-- Dans notre cas, on n'affiche le sujet qu'à la première page -->
<?php
  
  if( !isset($_GET['numpage']) || isset($_GET['numpage']) && $_GET['numpage'] == 1 )
  {
    Template::integrer( 'pok_forum_view/commentaire', array(
      'posts'  => array($topic)
    ));
  }
  // On affiche la liste des réponses
  Template::integrer( 'pok_forum_view/commentaire', array(
    'posts'  => $posts,
    'offset' => $offset
  ));
  
?>
</div>
  
  <hr style="clear:both" />
  <p style="float:right;">
<?php

  if( Session::connecter() && $_SESSION['statut'] == Membre::ADMIN )
  {
    if( $topic['niveau_comments'] == 0 )
    {
?>
    <a href="controleur.php?tpl=pok_forum_view&amp;ctrl=lockedtopic&amp;article=<?php echo $topic['article_id'] ?>&amp;ouvrir"><img src="<?php echo Page::getAdresse() ?>web/images/pok_forum_index/opentopic.png" alt="R&eacute;ouvrir le sujet" title="R&eacute;ouvrir le sujet" /></a>
<?php
    }
    else
    {
?>
    <a href="controleur.php?tpl=pok_forum_view&amp;ctrl=lockedtopic&amp;article=<?php echo $topic['article_id'] ?>&amp;fermer"><img src="<?php echo Page::getAdresse() ?>web/images/pok_forum_index/closetopic.png" alt="Fermer le sujet" title="Fermer le sujet" /></a>
<?php
    }
  }
?>
    <a href="#menuhaut"><img src="<?php echo Page::getAdresse() ?>web/images/pok_forum_index/up.png" alt="up" title="Revenir au-dessus" /></a>
  </p>
  <p><a href="<?php echo Page::url( Page::$actuelle['arborescence'], $topic['article_slug'], '&amp;repondre' ) ?>"><img src="<?php echo Page::getAdresse() ?>web/images/pok_forum_index/post<?php echo ( ($topic['niveau_comments'] <= 0)? 'locked' : 'retry' ) ?>.png" alt="Laisser une r&eacute;ponse" title="Laisser une r&eacute;ponse" /></a></p>
<?php
  
  }
  /* -------------------------------------
    Fin de la vérification que le sujet existe
  */
  else
    echo '<p>Aucun sujet trouv&eacute; !</p>';
}

echo '</div>';

/* -------------------------------------
  On inclue le pied de la page
*/
Template::integrer('_pok_footer', array( 'chrono' => $chrono ));
