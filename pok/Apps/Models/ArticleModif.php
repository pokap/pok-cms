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

namespace pok\Apps\Models;

use \systems\cfg\config;

class ArticleModif extends Base\Modifier
{
  // Int :
  //   Identifiant du article
  protected $article_id = array(null);
  //   Identifiant du dossier de l'article
  protected $page_id = array(null);
  // String :
  //   Date de création de l'article
  protected $article_date_creer = array(null);
  //   Date de modification de l'article
  protected $article_date_reviser = array(null);
  //   Date de fin de l'article
  protected $article_date_max = array(null);
  // Int :
  //   Identifiant de l'auteur de l'article
  protected $article_auteur = array(null);
  // String :
  //   Chapô de l'article
  protected $article_chapo = array(null);
  //   Contenu de l'article
  protected $article_texte = array(null);
  //   Titre de l'article
  protected $article_titre = array(null);
  //   Slug de l'article
  protected $article_slug = array(null);
  // Int :
  //   Niveau de sous-article de l'article
  protected $article_niveau = array(null);
  // Boolean :
  //   Si l'article est un brouillon
  protected $brouillon = array(null);
  // Int :
  //   Niveau de sous-article maximum
  protected $niveau_comments = array(null);
  //   Identifiant du parent de l'article
  protected $article_parent = array(null);
  //   Nombre sous-article de l'article
  protected $count = array(null);
  
  // -------------------------------------
  // Array :
  //   Renvoie la les attributs de la classe sauf donnees, soit la liste des champs de la table
  public static function getChamps() {
    return self::getChampsBy( get_class_vars(__CLASS__) );
  }
  
  // -------------------------------------
  // Int :
  //   Créer un article
  public function ajouter()
  {
    // Si on ne donne pas d'information sur le parenté des articles, on le fait nous même :)
    if( $this->article_parent[0] === null )
    {
      // s'il existe l'ID de l'article, on l'utilise pour enregistrer l'article en sous-article
      if( $this->article_id[0] !== null )
      {
        // ID devient donc le parent
        $this->article_parent = array( $this->article_id[0] );
        // on incrémente le niveau de l'article
        $this->article_niveau = array( '(SELECT subarticle.article_niveau + 1 FROM ' . config\PREFIX . Article::TABLE . ' AS subarticle WHERE subarticle.article_id = '.self::getAttributQuote($this->article_id).')', Base\PDOFournie::NOT_QUOTE );
        // on décrémente la possibilité de créer un sous-article
        if( $this->niveau_comments[0] === null )
          $this->niveau_comments = array( '(SELECT subarticle.niveau_comments - 1 FROM ' . config\PREFIX . Article::TABLE . ' AS subarticle WHERE subarticle.article_id = '.self::getAttributQuote($this->article_id).')', Base\PDOFournie::NOT_QUOTE );
        // initialise
        $this->article_id = array(null);
      }
      // on indique que l'article est mère
      else $this->article_parent = array(0);
    }
    // génère le slug de l'article
    $this->article_slug[0] = \pok\Texte::slugify($this->article_titre[0]);
    // récupère le nombre de fois que ce slug existe
    $nb_slug_pareil = (int) Base\Requete\PDOFournie::$INSTANCE->query('SELECT COUNT(*) FROM ' . config\PREFIX . Article::TABLE . ' WHERE ( article_slug = "'.$this->article_slug[0].'" OR article_slug REGEXP "^'.preg_quote($this->article_slug[0]).'-[0-9]+$" ) AND page_id = '.self::getAttributQuote($this->page_id))->fetchColumn();
    if( $nb_slug_pareil > 0 )
      $this->article_slug[0] .= '-' . $nb_slug_pareil;
    
    // mise à jour de l'article mère
    Base\Requete\PDOFournie::$INSTANCE->exec( 'UPDATE ' . config\PREFIX . Article::TABLE . ' SET count = count + 1 WHERE article_id = ' . self::getAttributQuote($this->article_parent) );
    // ajoute l'article
    Base\Requete\PDOFournie::$INSTANCE->exec( parent::ajouter( Article::TABLE, self::getChamps() ) );
    // récupère l'ID de l'article
    return (int) Base\Requete\PDOFournie::$INSTANCE->lastInsertId();
  }
  
  // -------------------------------------
  // Void :
  //   Mais à jour les informations du article
  public function modifier()
  {
    $this->baseClause();
    // si on ne modifie pas manuellement la date de modification, on le fait automatiquement
    if( $this->article_date_reviser[0] === null )
      $this->article_date_reviser = array( 'NOW()', Base\Requete\PDOFournie::NOT_QUOTE ); 
    
    // génère le slug de l'article
    if( !empty($this->article_titre[0]) )
    {
      $this->article_slug[0] = \pok\Texte::slugify($this->article_titre[0]);
      // récupère le nombre de fois que ce slug existe
      $nb_slug_pareil = (int) Base\Requete\PDOFournie::$INSTANCE->query('SELECT COUNT(*) FROM ' . config\PREFIX . Article::TABLE . ' WHERE article_slug = "'.$this->article_slug[0].'"')->fetchColumn();
      if( $nb_slug_pareil > 0 )
        $this->article_slug[0] .= '-' . $nb_slug_pareil;
    }
    // modifie donc la base de donnée
    Base\Requete\PDOFournie::$INSTANCE->exec( parent::modifier( Article::TABLE, self::getChamps() ) );
  }
  
  // -------------------------------------
  // Void :
  //   Supprime un article et met à jour l'article-parent, cela de façon récursive
  public function supprimer()
  {
    $this->baseClause();
    // recherche les sous articles et récupère la clause
    $article = new Article(array(array(
      Article::TABLE.'.article_parent' => array( '=', '(SELECT article_id FROM ' . config\PREFIX . Article::TABLE . $this->getWheres() . ')', Base\Requete\PDOFournie::NOT_QUOTE )
    )));
    $requete = $article->publier();
    $requete->execute();
    while( $enr = $requete->fetch(\PDO::FETCH_ASSOC) )
    {
      $article_modif = new ArticleModif($enr);
      $article_modif->supprimer();
    }
    // décrémente le nombre de sous-article de l'article mère
    Base\Requete\PDOFournie::$INSTANCE->exec('UPDATE ' . config\PREFIX . Article::TABLE . ' SET count = count - 1 WHERE article_id = (SELECT * FROM (SELECT article_parent FROM ' . config\PREFIX . Article::TABLE . $this->getWheres() . ')art)');
    
    // supprime l'article
    Base\Requete\PDOFournie::$INSTANCE->exec( parent::supprimer( Article::TABLE, self::getChamps() ) );
  }
  
  // -------------------------------------
  // Void :
  //   Vérifie la présence d'une clause, utile pour la modification ou la suppression
  public function baseClause()
  {
    if( $this->getWheres() == '' && $this->article_id[0] !== null )
      $this->addWhere(self::simpleCritereClause( 'article_id', array_merge( array('='), $this->article_id ) ));
  }
  
  // -------------------------------------
  // String :
  //   Représentation
  public function __toString()
  {
    return $this->article_titre[0];
  }
}
