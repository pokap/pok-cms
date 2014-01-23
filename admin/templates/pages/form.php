<div class="select">
  <p>
  <strong><a href="./article.php?modif&amp;article=<?php echo $_GET['article'];?>">&laquo; Revenir &agrave; l'article</a></strong>
  | <a href="./form.php?article=<?php echo $_GET['article'];?>">Accueil</a>
  | <a href="./form.php?creer&amp;article=<?php echo $_GET['article'];?>">Cr&eacute;er</a>
  | <a href="./form.php?reponse&amp;article=<?php echo $_GET['article'];?>">R&eacute;ponses</a>
  | <a href="./form.php?stats&amp;article=<?php echo $_GET['article'];?>">Statistiques</a>
  </p>
</div>
<?php

if( isset($_GET['ok']) )
  echo '<div class="valide">L\'action sur le champ &agrave; fonctionn&eacute;.</div>';
elseif( isset($_GET['erreur']) )
  echo '<div class="erreur">Une erreur est survenu !</div>';
