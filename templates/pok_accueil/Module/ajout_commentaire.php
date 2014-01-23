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
/*
  Permet d'afficher un textarea pour rajouter un commentaire à un article avec un aperçu
  On n'a aussi la possibilit&eacute; d'&eacute;diter son article

  On v&eacute;rifie que l'article n'est pas bloqu&eacute;.
  Ensuite qu'on à le droit de rèpondre aux articles.
  Et pour finir soit on &eacute;dite, donc que le GET existe
    > voir le controleur.
  Sinon on met le formulaire pour laisser un message.

  Dans tout les cas pour cr&eacute;er un message il faut respect&eacute; scrupuleusement les GET à mettre avec posting.php
    > voir la documentation sur la cr&eacute;ation d'un formulaire de publication d'article
*/

use pok\Apps\Outils\Session,
    pok\Controleur\Page,
    pok\Controleur\Droit;
  
if( !empty($ajout_commentaire) && Session::connecter() && ( Droit::$etla || $_SESSION['id'] == $ajout_commentaire['article_auteur'] || $_SESSION['statut'] == pok\Apps\Membre::ADMIN ) )
{
  
?>
    <h3 id="edit">Editez votre message</h3>
    <form method="post" action="posting.php?page=<?php echo Page::$actuelle['page_id'];?>&amp;reference=<?php echo $article['article_id'];?>&amp;article=<?php echo $ajout_commentaire['article_id'];?>&amp;jeton=<?php echo $_SESSION['jeton'];?>">
      <p><textarea name="texte" style="width: 400px; height: 150px;"><?php echo $ajout_commentaire['article_texte'];?></textarea></p>
      <input type="submit" value="Editez" />
    </form>
<?php

}
elseif( $article['niveau_comments'] > 0 && pok\Controleur\Droit::$raa )
{
  /* -------------------------------------
    Il se faut qu'on demande un affichage du r&eacute;sultat avant de poster
  */
  if( isset($_POST['apercu']) )
  {
    
?>
    <hr />
    <h4 id="apercu">Aperçu</h4>
    <blockquote>
      <?php echo htmlspecialchars( $_POST['texte'], ENT_QUOTES );?>
    </blockquote>
<?php

  }
    
?>
    <h3>Laisser une r&eacute;ponse</h3>
    <form id="postform" method="post" action="posting.php?page=<?php echo Page::$actuelle['page_id'];?>&amp;reference=<?php echo $article['article_id'];?>&amp;jeton=<?php echo $_SESSION['jeton'];?>">
      <p><textarea name="texte" style="width:400px;height:150px;"><?php if( isset($_POST['texte']) ) echo $_POST['texte'];?></textarea></p>
      <input type="submit" value="R&eacute;pondre" />
      <input type="submit" onclick="document.getElementById('postform').action = '#apercu';" name="apercu" value="Aper&ccedil;u" />
    </form>
<?php

}
else
{
  echo '<p>Vous ne pouvez pas mettre de commentaire</p>';
}
