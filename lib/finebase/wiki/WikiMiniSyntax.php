<?php

/**
 * Syntaxe "minimale" de Wiki.
 *
 * @author	Amaury Bouchard <amaury.bouchard@finemedia.fr>
 * @copyright	© 2009, FineMedia
 * @package	FineBase
 * @subpackage	Wiki
 */
class WikiMiniSyntax extends WikiRendererConfig  {
	/** Liste des tags inline. */
	public $inlinetags= array(
		'WikiSyntaxStrong'
	);
	/** Liste des balises de type bloc reconnus par WikiRenderer. */
	public $bloctags = array(
		'WikiSyntaxList', 'WikiSyntaxParagraph'
	);
	/** Liste des tags qui sont remplacés tels quels. */
	public $simpletags = array('%%%' => '<br />');
	/** Nom de la classe qui prend en charge le reste du texte à l'intérieur des phrases. */
	public $textLineContainer = 'WikiHtmlTextLine';
}

?>
