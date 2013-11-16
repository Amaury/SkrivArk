<?php

/**
 * Syntaxe "complète" de Wiki.
 *
 * @author	Amaury Bouchard <amaury.bouchard@finemedia.fr>
 * @copyright	© 2009, FineMedia
 * @package	FineBase
 * @subpackage	Wiki
 * @version	$Id$
 */
class WikiFullSyntax extends WikiRendererConfig  {
	/** Liste des tags inline. */
	public $inlinetags= array(
		'WikiSyntaxStrong', 'WikiSyntaxEm', 'WikiSyntaxUnderline', 'WikiSyntaxStrikeout',
		'WikiSyntaxSuperscript', 'WikiSyntaxSubscript', 'WikiSyntaxLink', 'WikiSyntaxImage'
	);
	/** Liste des balises de type bloc reconnus par WikiRenderer. */
	public $bloctags = array(
		'WikiSyntaxTitle', 'WikiSyntaxHr', 'WikiSyntaxList', 'WikiSyntaxParagraph',
		'WikiSyntaxTable', 'WikiSyntaxPre'
	);
	/** Liste des tags qui sont remplacés tels quels. */
	public $simpletags = array('%%%' => '<br />');
	/** Nom de la classe qui prend en charge le reste du texte à l'intérieur des phrases. */
	public $textLineContainer = 'WikiHtmlTextLine';
}

?>
