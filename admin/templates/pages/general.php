<?php

use \systems\cfg\general;

// les GET =)
if( isset( $_GET['saveok'] ) )
  echo '<div class="valide">La sauvegarde de la Bdd à bien été faite.</div>';
elseif( isset( $_GET['saverror'] ) )
  echo '<div class="erreur">Une erreur est survenu pendant la sauvgarde de la Bdd.</div>';

?>
	<div class="select">
    <p>
      <a class="bouton" href="./template.php">Gestion des templates</a>
      | <a class="bouton" href="./utils/save.php">Sauvegarder la Bdd</a>
      <a class="bouton" href="./restauration.php">Restauration Bdd</a>
    </p>
  </div>
	<fieldset style="margin: 10px 50px;">
		<legend>Modifier la configuration</legend>
		<form method="post" action="./utils/general_cfg.php?jeton=<?php echo $_SESSION['jeton'];?>">
			<dl>
        <dt style="margin-top:10px"><label><strong class="info">? <span><b><u>Définition :</u></b><br />Active/Désactive la newsletter.</span></strong>Newsletter :</label></dt>
        <dd>
<?php
  
  if( general\NEWSLETTER ) {
    echo '<input type="radio" name="NEWSLETTER" checked="checked" id="cfg_active_newsletter" value="1" /> Active
    <input type="radio" name="NEWSLETTER" id="cfg_active_newsletter" value="0" /> Désactive';
  }
  else {
    echo '<input type="radio" name="NEWSLETTER" id="cfg_active_newsletter" value="1" /> Active
    <input type="radio" name="NEWSLETTER" checked="checked" id="cfg_active_newsletter" value="0" /> Désactive';
  }

?>
        </dd>

        <dt style="margin-top:10px">
          <label for="cfg_USER_LOGIN_TYPE"><strong class="info">? <span><b><u>Définition :</u></b><br />Type de connexion basé sur le login.<br />
            <b>Pseudo</b> : on demande le pseudo en login.<br />
            <b>E-Mail</b> : on utilise l'e-mail du membre.</span></strong>Type de login de connexion :
          </label>
        </dt>
        <dd>
          <select id="cfg_USER_LOGIN_TYPE" name="USER_LOGIN_TYPE">
<?php

  foreach( array( 'membre_pseudo' => 'Pseudo', 'membre_email' => 'E-Mail' ) AS $value => $option ) {
    if( general\USER_LOGIN_TYPE == $value )
      echo '<option value="' , $value , '" selected="selected">' , $option , '</option>';
    else
      echo '<option value="' , $value , '">' , $option , '</option>';
  }

?>
          </select>
        </dd>

        <dt style="margin-top:10px">
          <label for="cfg_USER_ENABLE_MODE"><strong class="info">? <span><b><u>Définition :</u></b><br />Mode d'activation d'un utilisateur.<br />
            <b>Administateur</b> : c'est l'administrateur qui valide l'utilisateur.<br />
            <b>Utilisateur</b> : l'utilisateur doit activer son compte avec une clé.<br />
            <b>Aucun</b> : validation automatique.</span></strong>Utilisateur active :
          </label>
        </dt>
        <dd>
          <select id="cfg_USER_ENABLE_MODE" name="USER_ENABLE_MODE">
<?php

  foreach( array( 'Administateur', 'Utilisateur', 'Aucun' ) AS $value => $option ) {
    if( general\USER_ENABLE_MODE == $value )
      echo '<option value="' , $value , '" selected="selected">' , $option , '</option>';
    else
      echo '<option value="' , $value , '">' , $option , '</option>';
  }

?>
          </select>
        </dd>

        <dt style="margin-top:10px"><label for="cfg_TEMPLATE_404"><strong class="info">? <span><b><u>Définition :</u></b><br />Template par défauld pour les accès interdit.</span></strong>Template d'interdiction :</label></dt>
        <dd>
          <select name="TEMPLATE_404" id="cfg_TEMPLATE_404">
<?php
  foreach( $liste_templates AS $template ) {
    if( $template == general\TEMPLATE_404 )
      echo '<option value="' , $template , '" selected="selected">' , $template , '</option>';
    elseif( $template[0] != '_' )
      echo '<option value="' , $template , '">' , $template , '</option>';
  }
?>
          </select>
        </dd>

        <dt style="margin-top:10px"><label for="cfg_TEMPLATE_MEMBRE"><strong class="info">? <span><b><u>Définition :</u></b><br />Template par défauld défini automatiquement à un sous-dossier du dossier Membres.</span></strong>Template des membres :</label></dt>
        <dd>
          <select name="TEMPLATE_MEMBRE" id="cfg_TEMPLATE_MEMBRE">
<?php
  foreach( $liste_templates AS $template ) {
    if( $template == general\TEMPLATE_MEMBRE )
      echo '<option value="' , $template , '" selected="selected">' , $template , '</option>';
    elseif( $template[0] != '_' )
      echo '<option value="' , $template , '">' , $template , '</option>';
  }
?>
          </select>
        </dd>

        <dt style="margin-top:10px"><label for="cfg_TEMPLATE_GROUPE"><strong class="info">? <span><b><u>Définition :</u></b><br />Template par défauld défini automatiquement à un sous-dossier du dossier Groupes.</span></strong>Template des groupes :</label></dt>
        <dd>
          <select name="TEMPLATE_GROUPE" id="cfg_TEMPLATE_GROUPE">
<?php
  foreach( $liste_templates AS $template ) {
    if( $template == general\TEMPLATE_GROUPE )
      echo '<option value="' , $template , '" selected="selected">' , $template , '</option>';
    elseif( $template[0] != '_' )
      echo '<option value="' , $template , '">' , $template , '</option>';
  }
?>
          </select>
        </dd>

        <dt style="margin-top:10px"><label for="cfg_MAIL_FROM"><strong class="info">? <span><b><u>Définition :</u></b><br />Adresse e-mail d'envoie pour la newsletter.</span></strong>Adresse d'envoie :</label></dt>
        <dd><input type="text" name="MAIL_FROM" id="cfg_MAIL_FROM" value="<?php echo general\MAIL_FROM;?>" /></dd>

        <dt style="margin-top:10px"><label for="cfg_PATH"><strong class="info">? <span><b><u>Définition :</u></b><br />Répertoire du site.</span></strong>Répertoire du site :</label></dt>
        <dd><input type="text" name="PATH" id="cfg_PATH" value="<?php echo general\PATH;?>" /></dd>

        <dt style="margin-top:10px"><label for="cfg_DOMAINE"><strong class="info">? <span><b><u>Définition :</u></b><br />Nom de domaine du site, on n'est pas obligé de mettre le www, juste .monsite.com</span></strong>Nom de domaine du site :</label></dt>
        <dd><input type="text" name="DOMAINE" id="cfg_DOMAINE" value="<?php echo general\DOMAINE;?>" /></dd>
      </dl>
			<p><input type="submit" value="Soumettre" /></p>
		</form>
	</fieldset>