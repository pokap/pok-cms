<div class="bouton_index">
  <a href="general.php"><img src="images/but_configuration.png" alt="configuration" title="Configuration" /><br />Configurations</a>
</div>
<div class="bouton_index">
  <a href="article.php"><img src="images/but_article.png" alt="article" title="Articles" /><br />Articles</a>
</div>
<div class="bouton_index">
  <a href="categorie.php"><img src="images/but_categorie.png" alt="categorie" title="Catégories" /><br />Catégories</a>
</div>
<div class="bouton_index">
  <a href="page.php"><img src="images/but_dossier.png" alt="dossier" title="Dossiers" /><br />Pages</a>
</div>
<div class="bouton_index">
  <a href="membre.php"><img src="images/but_membre.png" alt="membre" title="Membres" /><br />Membres</a>
</div>
<div class="bouton_index">
  <a href="groupe.php"><img src="images/but_groupe.png" alt="groupe" title="Groupes" /><br />Groupes</a>
</div>
<div class="bouton_index">
  <a href="fichier.php"><img src="images/but_upload.png" alt="upload" title="Uploads" /><br />Téléversement</a>
</div>
<hr style="clear:both" />
<p><strong>Listes des membres connectés les 5 dernières minutes :</strong><br />
<?php

foreach( $list_membres_co AS $membre )
{
  if( $membre['groupe_id'] > 2 ) echo '<strong>';

  echo '<a style="color: #',$membre['groupe_couleur'],';" href="./membre.php?modif&amp;m=',$membre['membre_id'],'">',$membre['membre_pseudo'],'</a>';
    
  if( $membre['groupe_id'] > 2 ) echo '</strong>';

  echo ', ';
}

?>
</p>
<div><strong>Listes des Visiteurs (IP) :</strong></div>
<?php

if( !empty( $visiteurs ) )
{
  echo '<ul>';
  foreach( $visiteurs AS $ip_visiteur ) {
    echo '<li>' , $ip_visiteur , '</li>';
  }
  echo '</ul>';
}
else
  echo '<div>Aucun visiteur.</div>';

?>
