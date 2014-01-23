<?php

// -------------------------------------
// String :
//  Implode récursive
function r_implode( $glue, $pieces )
{
  $retVal = array();
  
  foreach( $pieces AS $r_pieces )
  {
    if( is_array($r_pieces) )
      $retVal[] = 'array('.r_implode( $glue, $r_pieces ).')';
    else
    {
      if( is_numeric($r_pieces) )
        $retVal[] = $r_pieces;
      else
        $retVal[] = "'".$r_pieces."'";
    }
  }
  return implode( $glue, $retVal );
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
  <head>
    <title>DEV :: CMS Pok</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <style type="text/css">
body, code {
  font-family: Verdana;
  font-size: 12px;
}
body {
  background: #ccc;
  margin: 20px;
}
p { margin: 10px; }
#contenu {
  background: #fff url('web/images/pok_500/homer-doh.png') no-repeat top right;
  -moz-border-radius: 20px;
  padding: 10px 20px;
  width: 800px;
  margin: auto;
}
hr {
  border: none;
  border-top: 1px dashed #999;
}
.head {
  -moz-border-radius: 10px;
  border: 1px dashed #999;
  padding: 10px 20px;
  margin-bottom: 20px;
}
    </style>
  </head>
  <body>
    <div id="contenu">
      <h3>Erreur syntaxe :</h3>
      <p class="head">
        <b>Une exception a été gérée :</b><br />
        <u>Message :</u> <?php echo $this->getMessage();?>
      </p>
<?php

foreach( $this->getTrace() AS $trace )
{
  echo '<p>';
  
  // nom du fichier
  if( isset($trace['file']) ) echo '<b>Fichier :</b> ',$trace['file'],'<br />';
  // ligne
  if( isset($trace['line']) ) echo '<b>Ligne :</b> ',$trace['line'],'<br />';
  
  $fonction = '<?php '.( isset($trace['class'])? $trace['class'] : '' ).( isset($trace['type'])? $trace['type'] : '' ).$trace['function'];
  // liste des arguments
  if( !empty($trace['args']) )
  {
    if( is_array($trace['args']) )
      $fonction .= '( ' . r_implode( ', ' , $trace['args'] ) . ' )';
    else
      $fonction .= '(' . $trace['args'] . ')';
  }
  else
    $fonction .= '()';
  
  $fonction .= ' ?>';

?>
        <b>Fonction :</b> 
        <?php highlight_string($fonction) ?>
        <br />
        <hr />
      </p>
<?php

}

?>
    </div>
  </body>
</html>
