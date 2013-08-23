<?php

/**
 * Text manipulation utilities.
 *
 * @author	Amaury Bouchard <amaury@amaury.net>
 * @copyright	© 2013, Amaury Bouchard
 * @package	SkrivArk
 * @subpackage	Lib
 */
class TextUtil {
	/**
	 * Checks the syntax of an HTML stream.
	 * @param       string  $html   HTML content to check.
	 * @return      bool    True if the syntax is correct, False otherwise.
	 */
	static public function isValidHtmlSyntax($html) {
		if (empty($html))
			return (true);
		$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html lang="fr-FR">
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
				</head>
				<body>
					' . $html . '
				</body>
			</html>';
		try {
			libxml_use_internal_errors(true);
			$xmlObj = new \SimpleXMLElement($html);
		} catch (\Exception $e) {
			libxml_clear_errors();
			return (false);
		}
		unset($xmlObj);
		return (true);
	}
	/**
	 * Normalize a text by removing all special characters.
	 * @param	string	$txt	Text to transform.
	 * @return	string	The normalized text.
	 * @see		http://fr.wikipedia.org/wiki/Apostrophe_%28typographie%29
	 * @see		http://fr.wikipedia.org/wiki/Guillemet
	 */
	static public function normalizeText($txt) {
		// special characters
		$mask = array("\"", '«', '»', "“", "”", "„", "‟", '‹', '›', "″", "‶", "‴", "‷", "⁗",
			      "'", "’", "ʼ", "ʻ", "ʽ", "ʾ", "ʿ", "'", "ˈ", "՚", "‘", "‚", "‛", "`", "´", "′", "‵",
			      '?', '!', '«', '»', '~', '−', '–', "´", '¯', '¬', '!', '#', '$', '%', '(', ')', '×', '÷', '*',
			      ',', '.', '·', '⋅', '/', ':', ';', '<', '=', '>', "`", '¿', '+', '¢', '£', '¤', '¥', '¦', '§',
			      '±', '°', '¹', '²', '³', 'µ', 'º', '©', '®', '™', '¡', '…', " ", " ", " ", " ");
		$txt = str_replace($mask, ' ', $txt);
		// vowels
		$mask = array('à', 'á', 'â', 'ã', 'ä', 'å', '@', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å');
		$txt = str_replace($mask, 'a', $txt);
		$mask = array('é', 'è', 'ê', 'ë', '€', 'È', 'É', 'Ê', 'Ë');
		$txt = str_replace($mask, 'e', $txt);
		$mask = array('í', 'ï', 'ì', 'î', 'Ì', 'Í', 'Î', 'Ï');
		$txt = str_replace($mask, 'i', $txt);
		$mask = array('ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø');
		$txt = str_replace($mask, 'o', $txt);
		$mask = array('ù', 'ú', 'û', 'ü', 'Ù', 'Ú', 'Û', 'Ü');
		$txt = str_replace($mask, 'u', $txt);
		$mask = array('ý', 'ÿ', 'Ý', 'Ÿ');
		$txt = str_replace($mask, 'y', $txt);
		// consonants
		$mask = array('ç', 'Ç');
		$txt = str_replace($mask, 'c', $txt);
		$mask = array('ð', 'Ð');
		$txt = str_replace($mask, 'd', $txt);
		$mask = array('ñ', 'Ñ');
		$txt = str_replace($mask, 'n', $txt);
		// digraphs
		$mask = array('æ', 'Æ');
		$txt = str_replace($mask, 'ae', $txt);
		$mask = array('œ', 'Œ');
		$txt = str_replace($mask, 'oe', $txt);
		$mask = array('ǳ', 'ǆ', 'ǲ', 'Ǳ', 'ǅ', 'Ǆ');
		$txt = str_replace($mask, 'dz', $txt);
		$mask = array('ĳ', 'Ĳ');
		$txt = str_replace($mask, 'ij', $txt);
		$mask = array('ǉ', 'ǈ', 'Ǉ');
		$txt = str_replace($mask, 'lj', $txt);
		$mask = array('ǌ', 'ǋ', 'Ǌ');
		$txt = str_replace($mask, 'nj', $txt);
		$mask = array('ß', 'ẞ');
		$txt = str_replace($mask, 'ss', $txt);
		$mask = '&';
		$txt = str_replace($mask, 'et', $txt);
		// remove multiple spaces
		$txt = preg_replace("/\s+/", ' ', $txt);
		$txt = strtolower($txt);
		// remove other weird characters
		$txt = preg_replace("/[^a-z0-9- \+]/", '', $txt);
		// return
		return ($txt);
	}
	/**
	 * Transform a title into an URL.
	 * @param	string	$txt	Title to transform.
	 * @return	string	The transformed title.
	 */
	static public function titleToUrl($txt) {
		$txt = self::normalizeText($txt);
		$mask = array(' ', '&nbsp;', '&#160;', '_');
		$txt = str_replace($mask, '-', $txt);
		$txt = preg_replace('/-+/', '-', $txt);
		$txt = trim($txt, '-');
		$txt = trim($txt);
		$txt = empty($txt) ? '-' : $txt;
		return ($txt);
	}
	/**
	 * Returns a human-readable JSON-encoded string.
	 * @param	mixed	$data	Data to encode.
	 * @param	int	$indent	(optional) Indent level.
	 * @return	string	The encoded string.
	 */
	static public function JsonEncode($data, $indent=0) {
		$result = '';
		$indent++;
		if (is_null($data)) {
			$result = 'null';
		} else if (is_bool($data)) {
			$result = $data ? 'true' : 'false';
		} else if (is_int($data) || is_float($data)) {
			$result =  (string)$data;
		} else if (is_string($data)) {
			$result =  '"' . addcslashes($data, '"/') . '"';
		} else if (is_array($data)) {
			// keys check
			$numericKeys = true;
			$i = 0;
			foreach ($data as $key => $subdata) {
				if (!is_numeric($key) || $key != $i) {
					$numericKeys = false;
					break;
				}
				$i++;
			}
			// écriture
			$result = ($numericKeys ? "[" : "{") . "\n";
			$loopNbr = 1;
			foreach ($data as $key => $subdata) {
				$result .= self::_indent($indent);
				if (!$numericKeys)
					$result .= '"' . addcslashes($key, '"\'') . '": ';
				$result .= self::JsonEncode($subdata, $indent);
				if ($loopNbr < count($data))
					$result .= ',';
				$result .= "\n";
				$loopNbr++;
			}
			$result .= self::_indent($indent - 1);
			$result .= $numericKeys ? ']' : '}';
		} else
			throw new Exception("Non-scalar data\n" . print_r($data, true));
		$indent--;
		return ($result);
	}

	/* ************ PRIVATE METHODS ************** */
	/**
	 * Add the given number of tabulations.
	 * @param	int	$nbr	Number of indentations.
	 * @return	string	The needed tabulations.
	 */
	static private function _indent($nbr) {
		if ($nbr > 0)
			return (str_repeat("\t", $nbr));
		return ('');
	}
}

?>
