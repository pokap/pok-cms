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

namespace templates\pok_forum_view\Controleur\fonctions;

// Alias namespace
use \pok\Apps\Outils\Base\Fichier,
    \pok\Controleur\Page;

// -------------------------------------
// Int :
//   nombre de visite sur un sujet, utilise un fichier pour ça
function sujet_vu( $sujet_id )
{
  if( $vu = Fichier::renvoie( __DIR__ . '/../sujetsvu/' . intval($sujet_id) . '.txt' ) )
    return $vu;
  else return 0;
}

// -------------------------------------
// Int :
//   nombre de visite sur un sujet, utilise un fichier pour ça
function ajoute_vu( $sujet_id ) {
  return Fichier::incremente( __DIR__ . '/../sujetsvu/' . intval($sujet_id) . '.txt' );
}

// -------------------------------------
// String :
//   BBCODE
function bbcode( $texte )
{
  // -------------------------------------
	// barre horizontale
	$texte = str_replace("[/]", "<hr width = \"100%\" size = \"1\" />", $texte);
	$texte = str_replace("[hr]", "<hr width = \"100%\" size = \"1\" />", $texte);
	// gras
	$texte = str_replace("[b]", "<strong>", $texte);
	$texte = str_replace("[/b]", "</strong>", $texte);
	// italique
	$texte = str_replace("[i]", "<em>", $texte);
	$texte = str_replace("[/i]", "</em>", $texte);
	// soulignement
	$texte = str_replace("[u]", "<ins>", $texte);
	$texte = str_replace("[/u]", "</ins>", $texte);
  // -------------------------------------
	// texte à centré
	$texte = str_replace("[center]", "<div style=\"text-align:center;\">", $texte);
	$texte = str_replace("[/center]", "</div>", $texte);
	// texte à droite
	$texte = str_replace("[right]", "<div style=\"text-align:right;\">", $texte);
	$texte = str_replace("[/right]", "</div>", $texte);
	// texte à gauche
	$texte = str_replace("[left]", "<div style=\"text-align:left;\">", $texte);
	$texte = str_replace("[/left]", "</div>", $texte);
	// texte justifier
	$texte = str_replace("[justify]", "<div style=\"text-align:justify;\">", $texte);
	$texte = str_replace("[/justify]", "</div>", $texte);
  // -------------------------------------
	// couleur
	$texte = preg_replace('`\[color= ?(([[:alpha:]]+)|(#[[:digit:][:alpha:]]{6})) ?\]`', '<span style="color:$1;">', $texte);
	$texte = str_replace('[/color]', '</span>', $texte);
  // -------------------------------------
	// taille des caractères
	$texte = preg_replace('`\[size=([[:digit:]]+)\]`', '<span style="font-size:$1px">', $texte);
	$texte = str_replace('[/size]', '</span>', $texte);
  // -------------------------------------
  // listes
	$texte = str_replace('[list]', '<ul>', $texte);
	$texte = str_replace('[/list]', '</ul>', $texte);
  $texte = preg_replace('`\[\*\]([^\]]+)\n`', '<li>$1</li>', $texte);
  // -------------------------------------
	// lien
  $texte = preg_replace('`\[url\](.+)\[/url]`', '<a href="\1">\1</a>', $texte);
  $texte = preg_replace('`\[url=(\w+://[^\]]+)\](.+)\[/url]`', '<a href="\1">\2</a>', $texte);
  // -------------------------------------
	// mail
  $texte = preg_replace('`\[email\](.+)\[/email]`', '<a href="mailto:$1">$1</a>', $texte);
  $texte = preg_replace('`\[email=(.+)\](.+)\[/email\]`', '<a href="mailto:$1">$2</a>', $texte);
  // -------------------------------------
	// image
  $texte = preg_replace('`\[img\](.+)\[/img]`', '<img src="$1" alt="image" />', $texte);
  // -------------------------------------
	// quote
	$texte = preg_replace( '`\[quote\](.+)\[/quote\]`isU', '<div class="quote"><strong>Citation :</strong><br />$1</div>', $texte );
  $texte = preg_replace( '`\[quote=(.+)\](.+)\[/quote\]`isU', '<div class="quote"><strong>Citation de $1 :</strong><br />$2</div>', $texte );
  // -------------------------------------
	// Smiley
	$texte = str_replace(':D', '<img src="js/markitup/sets/bbcode/images/emoticon-happy.png" alt=":D" />', $texte);
	$texte = str_replace(':(', '<img src="js/markitup/sets/bbcode/images/emoticon-unhappy.png" alt=":(" />', $texte);
	$texte = str_replace(':o', '<img src="js/markitup/sets/bbcode/images/emoticon-surprised.png" alt=":o" />', $texte);
	$texte = str_replace(':p', '<img src="js/markitup/sets/bbcode/images/emoticon-tongue.png" alt=":p" />', $texte);
	$texte = str_replace(';)', '<img src="js/markitup/sets/bbcode/images/emoticon-wink.png" alt=";)" />', $texte);
	$texte = str_replace(':)', '<img src="js/markitup/sets/bbcode/images/emoticon-smile.png" alt=":)" />', $texte);

	return $texte;
}
