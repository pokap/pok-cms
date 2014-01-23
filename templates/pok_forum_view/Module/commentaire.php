<?php

use pok\Controleur\Page,
    pok\Apps\Outils\Session,
    pok\Apps\Membre,
    templates\pok_accueil\Controleur\fonctions AS faccueil,
    templates\pok_forum_view\Controleur\fonctions;

$offset = isset($offset)? $offset : 1;

foreach( $posts AS $num => $post )
{
  $count = '# ' . ($num + $offset);

?>
<div class="commentaire">
  <p id="p<?php echo $post['article_id'] ?>" class="pop"><a href="#p<?php echo $post['article_id'] ?>"><img src="<?php echo Page::getAdresse() ?>web/images/pok_forum_index/icon_post_target.gif" alt="+)"/></a><strong><?php echo $count ?></strong> Le <small><?php echo faccueil\date_forme($post['article_date_creer']) ?></small> :
    <a href="<?php echo Page::url( Page::$actuelle['arborescence'], $post['article_slug'], '&amp;quote='.$post['article_id'] ) ?>">Citer</a>
<?php

  if( Session::connecter() && ( $_SESSION['id'] == $post['article_auteur'] || $_SESSION['statut'] === Membre::ADMIN ) )
  {
  
?>
    | <a href="<?php echo Page::url( Page::$actuelle['arborescence'], $post['article_slug'], '&amp;editer='.$post['article_id'] ) ?>#edit">Editer</a>
    | <a href="#" onClick="verif('posting.php?page=<?php echo Page::$actuelle['page_id'];?>&amp;article=<?php echo $post['article_id'];?>&amp;jeton=<?php echo $_SESSION['jeton'] ?>')" style="color: #500;">Supprimer</a>
<?php

  }

?>
  </p>
  <dl>
    <dt style="float: left; width: 100px; text-align: center;">
      <div class="fiche_membre">
        <strong><a href="<?php echo Page::url($post['arbo_auteur']) ?>" style="color: #<?php echo $post['couleur_groupe'] ?>;"><?php echo $post['pseudo_auteur'] ?></a></strong><br /><br />
<?php

  if( !empty($post['url_avatar_auteurs']) )
  {
    echo '<img src="',Page::getAdresse(),'web/',$post['url_avatar_auteurs'],'" alt="avatar" title="avatar de ',$post['pseudo_auteur'],'" />';
  }

?>
      </div>
    </dt>
      <dd style="margin-left:120px;"><?php echo fonctions\bbcode($post['article_texte']) ?>
      <!-- affiche si le message à été modifier -->
      <?php if( strtotime($post['article_date_creer']) != strtotime($post['article_date_reviser']) ) echo '<br /><br /><small>Modifi&eacute; le ',faccueil\date_forme($post['article_date_reviser']),'</small>'; ?>
  </dl>
  <div style="clear: both;"></div>
</div>
<?php

}
