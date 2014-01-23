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
use pok\Controleur\Page;
use pok\Controleur\Droit;
use pok\Template;
use pok\Apps\Outils\Session;
use pok\Apps\Formulaire;

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
// titre de l'article
if( isset($single_article) && $single_article > array() )
  echo ' &raquo; ',$single_article['article_titre'];

?>
    </p>
  </div>
  <div class="content">
    <div class="page">
      <div id="corps">
<?php

/* -------------------------------------
  Si on ne précise pas l'ID de l'article,
  on afficle la liste des articles.
*/
if( isset($all_article) )
{
  /* -------------------------------------
    On vérifie déjà qu'il y a des articles.
    Ensuite on liste les articles grâce à la boucle foreach,
    il est important de comprendre qu'on doit mettre devant la variable "AS" le "_",
    pour différencier d'une variable "global".
    Pour utiliser les variables de la boucles,
    il y a simplement le "_" à mettre entre le nom de la variable et son indice.
  */
  if( $all_article > array() )
  {
    foreach( $all_article AS $article )
    {
?>
  <div id="post-<?php echo $article['article_id'];?>" class="post">
    <h2>&raquo; <a href="<?php echo Page::url( Page::$actuelle['arborescence'], $article['article_slug'] );?>"><?php echo $article['article_titre'];?></a></h2> 
    <small>Le <?php echo fonctions\date_forme($article['article_date_creer']);?> par <a href="<?php echo Page::url( $article['arbo_auteur'] );?>"><?php echo $article['pseudo_auteur'];?></a></small>
    <div class="entry">
      <?php echo $article['article_chapo'] , $article['article_texte'];?>
    </div>
    <q>
<?php
    
      if( Page::$actuelle['nonlu'] )
      {
        // Malheureusement on ne peut pas appeller dynamiquement la fonction puisque les données s'affiche en fonction de l'utilisateur
        echo '<img src="web/images/pok_accueil/' , fonctions\article_vu( $article['id_sous_vu'], $article['dernier_sous_article_id'], $article['sous_vu_poster'] ) , '.png" /> ';
      }
      
      // Ouvre la balise <a> pour renvoyer sur la page des commentaires
      echo '<a href="',Page::url( Page::$actuelle['arborescence'], $article['article_slug'] ),'#comm">';
      
      // Affiche le nombre de commentaire
      if( $article['count'] > 1 )
        echo $article['count'],' commentaires !';
      elseif( $article['count'] == 1 )
        echo '1 commentaire !';
      else
        echo 'Aucun commentaire !';
      
?>
      </a>
    </q>
  </div>
<?php
  
      // pagination
      echo '<p align="right">Page : ',fonctions\pagination( $numpage, ceil( $nb_article / 10 ) ),'</p>';
    }
  }
  else
  {
    echo '<p>Aucune news !</p>';
  }
}
/* -------------------------------------
  Sinon on affiche l'article avec ces commentaires
*/
else
{
  // Pour éviter les gros messages d'erreurs
  if( isset($single_article) && $single_article > array() )
  {
    /* -------------------------------------
      Comme il n'y a qu'un article (logique),
      on affiche directement le contenu sans passer par un foreach
    */
    
?>
  <div id="post-<?php echo $single_article['article_id'];?>" class="post">
    <h2><a href="<?php echo Page::url( Page::$actuelle['arborescence'], $single_article['article_slug'] );?>"><?php echo $single_article['article_titre'];?></a></h2> 
    <small>Le <?php echo fonctions\date_forme($single_article['article_date_creer']);?> par <a href="<?php echo Page::url( $single_article['arbo_auteur'] );?>"><?php echo $single_article['pseudo_auteur'];?></a></small>
    <div class="entry">
      <?php echo $single_article['article_chapo'] , $single_article['article_texte'];?>
    </div>
  </div>
<?php
  
    // affichage du formulaire
    if( !empty($formulaire) )
    {
      echo '<form action="./format.php?page=',Page::$actuelle['page_id'],'&amp;article=',$single_article['article_id'],'&amp;jeton=',$_SESSION['jeton'],'" method="post">',
              Formulaire::getHtmlResult($formulaire);
      
?>
    <p><input type="submit" value="R&eacute;pondre au formulaire" /></p>
  </form>
<?php
    
    }
  
?>
  <h3>Commentaires</h3>
  <p align="right">Page : <?php echo fonctions\pagination( $numpage, ceil( $single_article['count'] / 20 ), $single_article['article_slug'] ) ?></p>
<?php
  
    foreach( $all_commentaire AS $commentaire )
    {
      /* -------------------------------------
        On recommence une boucle qui affiche les articles lier à "l'article mère", dont l'ID est dans URL
          > voir le controlleur.
      */
      
?>
  <p id="p<?php echo $commentaire['article_id'];?>">
    <ins># <?php echo $commentaire['article_id'];?> <strong><?php echo $commentaire['article_titre'];?></strong> par <a href="<?php echo Page::url( $commentaire['arbo_auteur'] );?>"><?php echo $commentaire['pseudo_auteur'];?></a></ins>
    <br /><small>Le <?php echo fonctions\date_forme($commentaire['article_date_creer']);?> :</small><br />
    <blockquote>
<?php
    
      echo $commentaire['article_texte'];
      
      // si on est l'auteur du message ou qu'on est admin
      if( Session::connecter() && ( Droit::$etla || $_SESSION['id'] == $commentaire['article_auteur'] || $_SESSION['statut'] == pok\Apps\Membre::ADMIN ) )
      {
        echo '<br />________________<br />
        <a href="',Page::url( Page::$actuelle['arborescence'], $single_article['article_slug'], '&editer='.$commentaire['article_id'].'#edit' ),'">Editer</a>';
      }
    
?>
    </blockquote>
  </p>
<?php
    
    }
    // On inclue la possibilité de mettre des commentaires
    Template::integrer( 'pok_accueil/ajout_commentaire', array(
      'article' => $single_article
    ));
  }
  /* -------------------------------------
    Si aucun article n'a été trouvé
  */
  else {
    echo '<p>Article inconnu !</p>';
  }
}
?>
</div>
<?php

/* -------------------------------------
  On inclue le pied de la page
*/
Template::integrer('_pok_footer', array(
  'article_slug'  => (isset($single_article)? $single_article['article_slug'] : null),
  'chrono'        => $chrono
));
