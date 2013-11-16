<?php

/**
 * paragraphe			2 sauts de ligne
 * <hr/>			----
 * liste ordonnée		un ou plusieurs dièses (#) en début de ligne
 * liste non ordonnée		une ou plusieurs étoiles (*) en début de ligne
 * titre de niveau 1		=TITRE=
 * titre de niveau 2		==SOUS-TITRE==
 * titre de niveau 6		======SOUS-TITRE======
 * texte préformaté		un espace au début de chaque ligne du texte préformaté
 * italic			''TEXTE''
 * gras				**TEXTE**
 * souligné			__TEXTE__
 * barré			--TEXTE--
 * retour à la ligne		%%%
 * lien interne			[[NOM_PAGE]]
 *				[[NOM_PAGE|NOM_LIEN]]
 *				[[NOM_PAGE|NOM_LIEN|DESCRIPTION]]
 *				[[NOM_PAGE|NOM_LIEN|DESCRIPTION|LANGUE]]
 *	=> le préfix des pages internes se définit avec la constante WIKIRENDERER_INTERNAL_LINKS_PREFIX
 * lien externe			[URL]
 *				[URL NOM_LIEN]
 *
 * @todo	tableaux nowiki
 *
 * @package	FineBase
 * @subpackage	Wiki
 * @author	Laurent Jouanneau <jouanneau@netcourrier.com>
 * @author	Amaury Bouchard <amaury@amaury.net>
 * @copyright	2003-2006 Laurent Jouanneau, Amaury Bouchard
 * @link	http://wikirenderer.berlios.de
 * @version	$Id$
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public 2.1
 * License as published by the Free Software Foundation.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

// ===================================== déclarations des tags inlines

class WikiSyntaxStrong extends WikiTagXhtml {
	protected $name = 'strong';
	public $beginTag = "**";
	public $endTag = "**";
}

class WikiSyntaxEm extends WikiTagXhtml {
	protected $name = 'em';
	public $beginTag = "''";
	public $endTag = "''";
}

class WikiSyntaxUnderline extends WikiTagXhtml {
	protected $name = 'u';
	public $beginTag = "__";
	public $endTag = "__";
}

class WikiSyntaxStrikeout extends WikiTagXhtml {
	protected $name = 's';
	public $beginTag = "--";
	public $endTag = "--";
}

class WikiSyntaxSuperscript extends WikiTagXhtml {
	protected $name = 'sup';
	public $beginTag = "^^";
	public $endTag = "^^";
}

class WikiSyntaxSubscript extends WikiTagXhtml {
	protected $name = 'sub';
	public $beginTag = ",,";
	public $endTag = ",,";
}

class WikiSyntaxLink extends WikiTagXhtml {
	protected $name = 'a';
	public $beginTag = '[[';
	public $endTag = ']]';
	protected $attribute = array('$$', 'href');
	public $separators = array('|');

	public function getContent() {
		$name = $href = '';
		switch ($this->separatorCount) {
		case 0:
			$name = $href = $this->wikiContentArr[0];
			$name = (strlen($name) > 40) ? (substr($name, 0, 40) . "...") : $name;
			break;
		case 1:
			$name = $this->wikiContentArr[0];
			$href = $this->wikiContentArr[1];
			break;
		}
		$isEmail = false;
		if (strpos($href, '@') !== false) {
			$isEmail = true;
			$href = "mailto:$href";
		}
		// création du code HTML résultant
		$result = '<a href="' . htmlspecialchars($href) . '"';
		if (!$isEmail)
			$result .= ' target="_blank"';
		$result .= '>' . htmlspecialchars($name) . '</a>';
		return ($result);
	}
}

class WikiSyntaxImage extends WikiTagXhtml {
	protected $name = 'image';
	public $beginTag = '{{';
	public $endTag = '}}';
	protected $attribute = array('alt', 'src');
	public $separators = array('|');

	public function getContent() {
		$alt = $href = '';
		switch ($this->separatorCount) {
		case 0:
			$href = $this->wikiContentArr[0];
			break;
		case 1:
			$alt = $thiw->wikiContentArr[0];
			$href = $this->wikiContentArr[1];
			break;
		}
		$result = '<img src="' . htmlspecialchars($href) . '" ';
		if (!empty($alt))
			$result .= ' alt="' . htmlspecialchars($alt) . '" ';
		$result .= '/>';
		return ($result);
	}
}

// ===================================== déclaration des différents bloc wiki

/** traite les signes de types titre */
class WikiSyntaxTitle extends WikiRendererBloc {
	public $type = 'title';
	protected $regexp = "/^(={1,6}).*={1,6}\s*$/";
	protected $_closeNow = true;

	public function getRenderedLine() {
		$text = $this->_detectMatch[0];
		for ($i = 6; $i >= 1; --$i) {
			$j = $i + 2;
			$h = str_repeat('=', $i);
			$text = preg_replace("/^{$h}(.+){$h}\\s*$/m", "<h{$j}>\\1</h{$j}>\\2", $text);
		}
		return ($text);
		$hx = strlen($this->_detectMatch[1]);
		return ("<h$hx>" . $this->_renderInlineTag($this->_detectMatch[2]) . "</h$hx>");
	}
}

/** traite les signes de types hr */
class WikiSyntaxHr extends WikiRendererBloc {
	public $type = 'hr';
	protected $regexp = "/^\-{4}\-*$";
	protected $_closeNow = true;

	public function detect($string) {
		if (substr($string, 0, 4) == '----')
			return (true);
		return (false);
	}
	public function getRenderedLine(){
		return ('<hr />');
	}
}

/** traite les signes de types liste */
class WikiSyntaxList extends WikiRendererBloc {
	public $type = 'list';
	protected $_previousTag;
	protected $_firstItem = false;
	protected $_firstTagLen;
	//protected $regexp = "/^\s*([\*#]+)(.*)/";
	protected $regexp = "/^([\*#]+)(.*)/";

	/**
	 * test si la chaine correspond au debut ou au contenu d'un bloc
	 * @param string   $string
	 * @return boolean   true: appartient au bloc
	 */
	public function detect($string, $inBloc=false) {
		if (!preg_match($this->regexp, $string, $this->_detectMatch))
			return (0);
		if ($inBloc !== true && substr($string, 0, 2) == '**' && strpos(substr($string, 2), '**') !== false)
			return (0);
		return (1);
	}
	public function open() {
		$this->_previousTag = $this->_detectMatch[1];
		$this->_firstTagLen = strlen($this->_previousTag);
		$this->_firstItem = true;
		if (substr($this->_previousTag, -1, 1) == '#')
			return ("<ol>\n");
		return ("<ul>\n");
	}
	public function close() {
		$t = $this->_previousTag;
		$str = '';
		for ($i = strlen($t); $i >= $this->_firstTagLen; $i--)
			$str.= ($t{$i-1} == '#') ? "</li></ol>\n" : "</li></ul>\n";
		return ($str);
	}
	public function getRenderedLine() {
		$t = $this->_previousTag;
		$d = strlen($t) - strlen($this->_detectMatch[1]);
		$str = '';
		if ($d > 0) { // on remonte d'un ou plusieurs cran dans la hierarchie...
			$l = strlen($this->_detectMatch[1]);
			for ($i = strlen($t); $i > $l; $i--)
				$str.= ($t{$i-1} == '#') ? "</li></ol>\n" : "</li></ul>\n";
			$str.= "</li>\n<li>";
			$this->_previousTag = substr($this->_previousTag,0,-$d); // pour être sur...
		} elseif ($d < 0) { // un niveau de plus
			$c = substr($this->_detectMatch[1], -1, 1);
			$this->_previousTag .= $c;
			$str = ($c == '#') ? '<ol><li>' : '<ul><li>';
		} else {
			$str = ($this->_firstItem) ? '<li>' : "</li>\n<li>";
		}
		$this->_firstItem = false;
		return ($str . $this->_renderInlineTag($this->_detectMatch[2]));
	}
}

/** traite les signes de type paragraphe */
class WikiSyntaxParagraph extends WikiRendererBloc {
	public $type = 'p';
	protected $_openTag = '<p>';
	protected $_closeTag = '</p>';
	// attribut utilisé pour gérer les retours charriots dans les paragraphes
	private $_firstLine = true;

	/** Détection des paragraphes. */
	public function detect($string) {
		if (empty($string))
			return (false);
		if (!preg_match("/^\s*\*{2}.*\*{2}\s*.*$/", $string) && preg_match("/^\s*[\*#\-\!\| \t>;<=].*/", $string))
			return (false);
		$this->_detectMatch = array($string, $string);
		return (true);
	}
	/** Traitement du texte à l'intérieur d'un paragraphe. */
	protected function _renderInlineTag($string) {
		$string = $this->engine->inlineParser->parse($string);
		// gestion des retours-charriot dans les paragraphes
		$string = (!$this->_firstLine) ? "<br />$string" : $string;
		$this->_firstLine = false;
		return ($string);
	}
}

/**
 * traite les signes de types table
 */
class WikiSyntaxTable extends WikiRendererBloc {
	public $type = 'table';
	protected $regexp = "/^\s*[!\|] ?(.*)/";
	protected $_openTag = '<table border="1">';
	protected $_closeTag = '</table>';
	protected $_colcount = 0;

	public function open() {
		$this->_colcount = 0;
		return ($this->_openTag);
	}
	public function getRenderedLine() {
		$str = '';
		$text = ' ' . $this->_detectMatch[0];
		$prevPos = 0;
		$prevType = '';
		$loop = true;
		while ($loop) {
			if (($posTh = strpos($text, ' ! ', $prevPos)) === false &&
			    ($posTd = strpos($text, ' | ', $prevPos)) === false) {
				$posTh = false;
				$posTd = strlen($text);
				$loop = false;
			}
			if ($posTh === false || (is_int($posTd) && $posTd < $posTh)) {
				$pos = $posTd;
				$type = 'td';
			} else {
				$pos = $posTh;
				$type = 'th';
			}
			if ($prevPos) {
				$cell = substr($text, $prevPos, $pos - $prevPos);
				$str .= "<$prevType>" . $this->_renderInlineTag(trim($cell)) . "</$prevType>";
			}
			$prevPos = $pos + 3;
			$prevType = $type;
		}
		return ("<tr>$str</tr>");
	}
}

/** traite les signes de types pre (pour afficher du code..) */
class WikiSyntaxPre extends WikiRendererBloc {
	// récupéré depuis la syntaxe Classic WikiRenderer
	public $type='pre';
	protected $regexp="/^\s(.*)/";
	protected $_openTag='<pre>';
	protected $_closeTag='</pre>';

	public function getRenderedLine() {
		return (htmlspecialchars($this->_detectMatch[1]));
	}
}

/** version alternative des blocs de texte préformatés */
class WikiSyntaxPreAlt extends WikiRendererBloc {
	public $type = 'pre';

	/**
	 * Teste si la chaîne correspond au début ou au contenu d'un bloc
	 * @param	string	$string
	 * @return	bool	$inBloc	True si appartient au bloc.
	 */
	public function detect($string, $inBloc=false) {
		if ($string[0] === '[' && $string[1] === '[' && $string[2] === '[')
			return (1);
		return (0);
	}
	public function getContent() {
		$result = '<pre';
		switch ($this->separatorCount) {
		case 
		}
	}
}

?>
