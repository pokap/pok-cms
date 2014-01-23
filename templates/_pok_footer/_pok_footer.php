    <div id="sidebar">
<?php

  pok\Template::integrer('_pok_footer/liste_menu');

  pok\Template::integrer('_pok_footer/menu_connexion');
  
?>
    </div>

	  <div id="footer">
		<p>© Copyright 2009 POK. Tous droits réservés.<br />
    Page généré en <?php echo number_format( $chrono * 1000, 2, ',', ' ') ?> ms</p>
	  </div>
  </div>
	</div>
  </body>
</html>
