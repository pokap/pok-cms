<?php

use pok\Apps\Groupe,
    pok\Apps\Membre,
    pok\Apps\Outils\Session,
    templates\pok_accueil\Controleur\fonctions;

if( Session::connecter() && ( $info_profil['membre_id'] == $_SESSION['id'] || $_SESSION['statut'] == Membre::ADMIN ) )
{

?>
<fieldset>
  <legend>Changez de mot de passe</legend>
  <form method="post" action="controleur.php?tpl=pok_membre_profil&amp;ctrl=modifier&amp;jeton=<?php echo $_SESSION['jeton'] ?>">
    <p>
    <label for="ancien_mdp">Ancien mot de passe :</label><br /><input type="password" name="ancien_mdp" id="ancien_mdp" /><br />
    <label for="nouveau_mdp">Nouveau mot de passe :</label><br /><input type="password" name="nouveau_mdp" id="nouveau_mdp" /><br />
    <label for="confirme_mdp">Confirmez mot de passe :</label><br /><input type="password" name="confirme_mdp" id="confirme_mdp" /><br />
    <input type="submit" value="Changez mon mot de passe" />
    </p>
  </form>
</fieldset>
<fieldset>
  <legend>Changez mon E-mail</legend>
  <form method="post" action="controleur.php?tpl=pok_membre_profil&amp;ctrl=modifier&amp;mail&amp;jeton=<?php echo $_SESSION['jeton'] ?>">
    <p>
    <label for="email_mdp">Mot de passe :</label><br /><input type="password" name="email_mdp" id="confirme_mdp" /><br />
    <label for="nouveau_email">Nouveau E-mail :</label><br /><input type="text" name="nouveau_email" id="nouveau_email" /><br />
    <label for="confirme_email">Confirmez E-mail :</label><br /><input type="text" name="confirme_email" id="confirme_email" /><br />
    <input type="submit" value="Changez mon E-mail" />
    </p>
  </form>
</fieldset>
<?php

}
else
{
  // déconseillez mais dans le cas, un peu obligatoire
  // ce genre de problème sera réglé dans une version plus récente
  $grSql = new Groupe(array(array(
    'membre_groupe.membre_id' => array( '=', $info_profil['membre_id'] ),
    'membre_groupe.principal' => array( '=', '0' )
  )));
  $liste_groupe = $grSql->publier();

?>
<div class="profil">
  <dl>
    <dt>Pseudo :</dt><dd><?php $info_profil['membre_pseudo'] ?></dd>
    <dt>Inscription :</dt><dd><?php fonctions\date_forme($info_profil['membre_inscrit']) ?></dd>
    <dt>Groupe principal :</dt><dd style="color:#<?php echo $info_profil['groupe_couleur'] ?>;"><?php echo $info_profil['groupe_nom'] ?></dd>
  </dl>
<?php
  
  if( !empty($liste_groupe) )
  {

?>
  <p><strong>Liste des groupes :</strong></p>
  <ul>
<?php

    foreach( $liste_groupe AS $groupe )
    {
      echo '<li style="color:#',$groupe['couleur'],';">',$groupe['groupe_nom'],'</li>';
    }

?>
  </ul>
<?php

  }

?>
</div>
<?php

}
