<?php
/* -------------------------------------
  Liste du menu.
    "$liste_menu" est créer directement avec le résultat de "templates\_pok_info\Module::liste_menu()" s'il existe
*/
  if( $liste_menu > array() )
  {
?>
    <h3>Menu : </h3>
    <ul>
<?php
    foreach( $liste_menu AS $page )
    {
?>
      <li><a href="<?php echo pok\Controleur\Page::url($page['arborescence']);?>"><?php echo $page['page_nom'];?></a></li>
<?php
    }
?>
    </ul>
<?php
  }
?>