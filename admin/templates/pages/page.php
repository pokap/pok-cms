<div class="select"><p>
<?php

foreach( $arborescence AS $page )
{
  // s'il n'y a pas de description, on l'indique !
  if( empty($page['page_description']) )
    $page['page_description'] = 'Aucune description.';

  echo ' / <strong class="info"><a href="page.php?page=',$page['arborescence'],'">',$page['page_nom'],'</a><span><strong>Description :</strong><br />',$page['page_description'],'</span></strong>';
}

echo ' /</p></div>';

// --------------------------------------------------------------------------
//    MESSAGE D'ERREUR
// --------------------------------------------------------------------------
if( isset($_GET['deplaceok']) ) {
  echo '<div class="valide">Le d�placement a fonctionn� correctement !</div>';
}
elseif( isset($_GET['e_deplace']) ) {
  echo '<div class="erreur">Le d�placement a �chou� !</div>';
}
elseif( isset($_GET['createok']) ) {
  echo '<div class="valide">La cr�ation du dossier a fonctionn� !</div>';
}
elseif( isset($_GET['e_create']) )
{
  if( isset($_GET['exist']) )
    echo '<div class="erreur">Ce dossier existe d�j� !</div>';
  elseif( isset($_GET['manque']) )
    echo '<div class="erreur">Il manque des informations au dossier !</div>';
  else
    echo '<div class="erreur">La cr�ation du dossier a �chou� !</div>';
}
elseif( isset($_GET['modifok']) ) {
  echo '<div class="valide">La modification a fonctionn� correctement !</div>';
}
elseif( isset($_GET['e_modif']) ) {
  echo '<div class="erreur">La modification a �chou� !</div>';
}
