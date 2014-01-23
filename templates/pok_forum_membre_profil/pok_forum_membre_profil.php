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

use templates\pok_accueil\Controleur\fonctions;
use templates\pok_forum_view\Controleur\fonctions AS forumfonctions;
use pok\Controleur\Page;
use pok\Controleur\Droit;
use pok\Template;
use pok\Apps\Membre;
use pok\Apps\Outils\Session;

Template::importerFonctions('pok_accueil');
Template::importerFonctions('pok_forum_view');

/*
  On inclue le template _header simplement.
  Si vous voulez héritez du controlleur du template _header,
  vous devez l'inclure dans le controlleur de ce template.
*/
Template::integrer('_pok_header');

/*
  Permet de vérifier si on a bien à faire à un article.
  Dans le controller on demande d'afficher un post qui à la référence 0, si on demande un commentaire,
  il n'aura pas la référence 0, donc single_article sera vide.
*/
?>
    <p>
      <a href="<?php echo Page::url(Page::$mere['arborescence']);?>"><?php echo Page::$mere['page_nom'];?></a>
<?php

// fil d'arine central
fonctions\affiche_fil_ariane($arborescence);

?>
    </p>
  </div>
  <div class="content">
    <div class="page">
      <div id="corps">
      <h2>Profil de <?php echo Page::$actuelle['page_nom'] ?></h2>
<?php

  if( Session::connecter() && ( $info_profil['membre_id'] == $_SESSION['id'] || $_SESSION['statut'] == Membre::ADMIN ) )
  {

?>
  <fieldset>
    <legend>Vos Informations</legend>
    <form method="post" action="posting.php?page=<?php echo Page::$actuelle['page_id'] ?>&amp;reference=0&amp;article=<?php echo $profil['id'] ?>&amp;jeton=<?php echo $_SESSION['jeton'] ?>">
      <p>
    	<label for="lieu" class="label_membre">Lieu :</label><br /><input class="input_membre" type="text" name="lieu" id="lieu" value="<?php echo $profil['lieu'] ?>" /><br />
    	<label for="website" class="label_membre">Site internet :</label><br /><input class="input_membre" type="text" name="website" id="website" value="<?php echo $profil['website'] ?>" /><br />
    	<label for="signature">Signature (250 caract&egrave;res maximum) :</label><br />
      <textarea name="signature" style="width:90%;height:75px;margin:auto;" id="signature"><?php echo pok\Texte::br2nl($profil['signature']) ?></textarea><br />
      <input type="submit" value="Enregistrer mes informations" />
      </p>
    </form>
  </fieldset>
  <fieldset>
    <legend>Avatars</legend>
<?php

    if( !empty($avatar) )
    {

?>
      <p style="float:right;">
        <img src="<?php echo Page::getAdresse() ?>web/fichiers/<?php echo $avatar['extension'],'/',$avatar['fichier_nom'],'.',$avatar['extension'] ?>" title="<?php echo $avatar['fichier_nom'] ?>" alt="avatar de <?php echo $info_profil['membre_pseudo'] ?>" />
      </p>
      <p>
        <a href="controleur.php?tpl=pok_forum_membre_profil&amp;ctrl=avatars&amp;jeton=<?php echo $_SESSION['jeton'] ?>" style="color:#500;">Supprimer l'avatar</a>
      </p>
<?php

    }

?>
    <p>L'image ne doit pas d&eacute;passer 100px/100px et 150Ko.</p>
    <form action="controleur.php?tpl=pok_forum_membre_profil&amp;ctrl=avatars&amp;jeton=<?php echo $_SESSION['jeton'] ?>" method="post" enctype="multipart/form-data">
      <p>
    	<input type="file" name="avatar" /><br />
      <input type="submit" value="Envoyer comme avatar" />
      </p>
    </form>
  </fieldset>
<?php

  }
  else
  {

?>
  <div class="profil">
    <dl>
    <dt>Lieu :</dt><dd><?php echo $profil['lieu'] ?></dd>
    <dt>Site internet :</dt><dd><a href="<?php echo $profil['website'] ?>"><?php echo $profil['website'] ?></a></dd>
    <dt>Signature :</dt><dd><?php echo forumfonctions\bbcode($profil['signature']) ?></dd>
    </dl>
	</div>
<?php

  }
  
  //Template::integrer('pok_membre_profil/change_information', array('info_profil' => $info_profil));

?>
</div>
<?php

/* -------------------------------------
  On inclue le pied de la page
*/
Template::integrer('_pok_footer', array( 'chrono' => $chrono ));
