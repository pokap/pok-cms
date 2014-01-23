<?php
###############################################################################
# LEGAL NOTICE                                                                #
###############################################################################
# Copyright (C) 2008/2010  Florent Denis                                      #
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

namespace admin\templates;

class Pages
{
  public static $hidden_menu = false;
  
  public static function parse( $template_nom, array $arg = array(), $template = null )
  {
    if( $template === null )
      $template = $template_nom;
    else
      $template = $template_nom . '_' . $template;
    
    extract($arg);
    
    require(__DIR__.'/layout.php');
  }
  
  public static function getListOptionId( $select, $selected = 1 )
  {
    foreach( $select AS $id => $value ) { 
      if( $id == $selected )
        echo '<option value="' , $id , '" selected="selected">' , $value , '</option>';
      else
        echo '<option value="' , $id , '">' , $value , '</option>';
    }
  }
  
  public static function getListOption( $select, $selected = '' )
  {
    foreach( $select AS $id => $value ) { 
      if( $value == $selected )
        echo '<option value="' , $id , '" selected="selected">' , $value , '</option>';
      else
        echo '<option value="' , $id , '">' , $value , '</option>';
    }
  }
  
  public static function getListOptionCat( $select, $selected = 0 )
  {
    foreach( $select AS $value ) {
      if( $value['cat_id'] == $selected )
        echo '<option value="' , $value['cat_id'] , '" selected="selected">' , $value['cat_nom'] , '</option>';
      else
        echo '<option value="' , $value['cat_id'] , '">' , $value['cat_nom'] , '</option>';
    }
  }
  
  public static function get_list_page( $nb, $get_nom, $php, $gets = '' )
  {
    $nb_page = max( ceil( $nb / 30 ), 1 );
    
    $list = \pok\Controleur\Page::details( isset($_GET[$get_nom]) ? intval($_GET[$get_nom]) : 1, $nb_page );
    foreach( $list AS $num )
    {
      if( $num != '...' )
        echo '<a href="'.$php.'.php?' , $gets , $get_nom , '=' , $num , '">' , $num , '</a> ';
      else
        echo $num , ' ';
    }
  }
}