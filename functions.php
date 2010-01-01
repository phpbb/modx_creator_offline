<?php
/**
*
* @package MODX creator
* @version $Id$
* @copyright (c) 2009 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* @ignore
*/
if(!defined('IN_MODX'))
{
	exit;
}

/**
* write_element()
*
* Writes a XML element with attributes
* @param object $xml
* @param string $name
* @param string $text
* @param array $attributes
*/
function write_element($name, $text, $attributes = false, $cdata = true)
{
	global $xml;

	if($text == '' && $attributes == false)
	{
		// nothing to write
		return;
	}

/**
 * This doesn't work as intended.
 * I'll comment it for now and return later.
	if(!version_compare(PHP_VERSION, '6.0.0-dev', '>=') && get_magic_quotes_gpc())
	{
		$text = stripslashes($text);
	}
*/

	$xml->startElement($name);
	if($attributes != false)
	{
		foreach($attributes as $key => $value)
		{
			if($value != '')
			{
				$xml->writeAttribute($key, $value);
			}
		}
	}
	if($text != '')
	{
		if($cdata)
		{
			$xml->writeCdata($text);
		}
		else
		{
			$xml->text($text);
		}
	}
	$xml->endElement();
}

/**
* count_rows()
*
* Counts the number of rows needed for <textarea>'s
* @param string $string, the string to count
* @param int $len, the number of chars per row
* @param int $min, the minimum rows to return
* @return int
*/
function count_rows($string, $len, $max = 20, $min = 4)
{
	$arr = explode("\n", $string);
	$rows = count($arr);
	foreach($arr as $value)
	{
		$str_len = strlen($value);
		if($str_len > $len)
		{
			$sum = ceil($str_len / $len) -1;
			$rows += $sum;
		}
	}
	$rows = ($rows <= $min) ? $min : $rows;
	$rows = ($rows >= $max) ? $max : $rows;
	return($rows);
}

/**
 * sanitize_inlines()
 *
 * Remove newlines from inline edits and finds.
 * @param $data, the inline string to sanitize.
 */
function sanitize_inlines(&$data)
{
	// On some systems \r comes before \n and I bet some systems only uses \r
	$data = str_replace("\r", "\n", $data);
	if ($pos = strpos($data, "\n") !== false)
	{
		$data = substr($data, 0, $pos);
	}
}

/**
* gen_value()
*
* runs $data trought htmlspecialchars(). It's in a function to save code
* @param string $data, the string to do things to
* @param bool $trim, true = trim() $data
* @return string
*/
function gen_value(&$data, $trim = false)
{
	$data = ($trim) ? trim($data) : $data;
	$data = htmlspecialchars($data);
	return($data);
}

/**
* get_mod_type()
*
* Checks and returns the type of the uploaded file
* @param string $str, the 100 first bytes from the file
* @param string $extension, the file extension.
* @return int (or bool on error)
*/
function get_mod_type($str, $extension)
{
	$extension = strtolower($extension);
	if($extension == 'xml')
	{
		// It's trying to be a MODX file, so we'll check for <?xml in the file
		if(strpos($str, '<?xml') !== false)
		{
			return(MODX);
		}
	}
	else if($extension == 'txt' || $extension == 'mod')
	{
		$str = strtolower($str);
		// The file is trying to tell us it's a MOD file
		if((substr($str, 0, 10) == '##########' || strpos($str, 'easymod') !== false) && strpos($str, 'mod') !== false)
		{
			return(MOD);
		}
	}
	return(false);
}

/**
* Make options for the language-select with the selected language set.
*
* @param string $lang, the language that should be selected as default
* @return string, the language select
*/
function lang_select($lang = 'en')
{
	$target_lang = array(
		'ab' => 'Abkhazian',
		'aa' => 'Afar',
		'af' => 'Afrikaans',
		'sq' => 'Albanian',
		'am' => 'Amharic',
		'ar' => 'Arabic',
		'hy' => 'Armenian',
		'as' => 'Assamese',
		'ay' => 'Aymara',
		'az' => 'Azerbaijani',
		'ba' => 'Bashkir',
		'eu' => 'Basque',
		'bn' => 'Bengali',
		'dz' => 'Bhutani',
		'bh' => 'Bihari',
		'bi' => 'Bislama',
		'br' => 'Breton',
		'bg' => 'Bulgarian',
		'my' => 'Burmese',
		'be' => 'Byelorussian',
		'km' => 'Cambodian',
		'ca' => 'Catalan',
		'zh' => 'Chinese',
		'co' => 'Corsican',
		'hr' => 'Croatian',
		'cs' => 'Czech',
		'da' => 'Danish',
		'nl' => 'Dutch',
		'en' => 'English',
		'eo' => 'Esperanto',
		'et' => 'Estonian',
		'fo' => 'Faeroese',
		'fj' => 'Fiji',
		'fi' => 'Finnish',
		'fr' => 'French',
		'fy' => 'Frisian',
		'gl' => 'Galician',
		'ka' => 'Georgian',
		'de' => 'German',
		'el' => 'Greek',
		'kl' => 'Greenlandic',
		'gn' => 'Guarani',
		'gu' => 'Gujarati',
		'ha' => 'Hausa',
		'iw' => 'Hebrew',
		'hi' => 'Hindi',
		'hu' => 'Hungarian',
		'is' => 'Icelandic',
		'in' => 'Indonesian',
		'ia' => 'Interlingua',
		'ik' => 'Inupiak',
		'ga' => 'Irish',
		'it' => 'Italian',
		'ja' => 'Japanese',
		'jw' => 'Javanese',
		'kn' => 'Kannada',
		'ks' => 'Kashmiri',
		'kk' => 'Kazakh',
		'rw' => 'Kinyarwanda',
		'ky' => 'Kirghiz',
		'rn' => 'Kirundi',
		'ko' => 'Korean',
		'ku' => 'Kurdish',
		'lo' => 'Laothian',
		'la' => 'Latin',
		'lv' => 'Lettish',
		'ln' => 'Lingala',
		'lt' => 'Lithuanian',
		'mk' => 'Macedonian',
		'mg' => 'Malagasy',
		'ms' => 'Malay',
		'ml' => 'Malayalam',
		'mt' => 'Maltese',
		'mi' => 'Maori',
		'mr' => 'Marathi',
		'mo' => 'Moldavian',
		'mn' => 'Mongolian',
		'na' => 'Nauru',
		'ne' => 'Nepali',
		'no' => 'Norwegian',
		'oc' => 'Occitan',
		'or' => 'Oriya',
		'om' => 'Oromo',
		'ps' => 'Pashto',
		'fa' => 'Persian',
		'pl' => 'Polish',
		'pt' => 'Portuguese',
		'pa' => 'Punjabi',
		'qu' => 'Quechua',
		'rm' => 'Rhaeto-Romance',
		'ro' => 'Romanian',
		'ru' => 'Russian',
		'sm' => 'Samoan',
		'sg' => 'Sangro',
		'sa' => 'Sanskrit',
		'gd' => 'Scots Gaelic',
		'sr' => 'Serbian',
		'sh' => 'Serbo-Croatian',
		'st' => 'Sesotho',
		'tn' => 'Setswana',
		'sn' => 'Shona',
		'sd' => 'Sindhi',
		'si' => 'Singhalese',
		'ss' => 'Siswati',
		'sk' => 'Slovak',
		'sl' => 'Slovenian',
		'so' => 'Somali',
		'es' => 'Spanish',
		'su' => 'Sudanese',
		'sw' => 'Swahili',
		'sv' => 'Swedish',
		'tl' => 'Tagalog',
		'tg' => 'Tajik',
		'ta' => 'Tamil',
		'tt' => 'Tatar',
		'te' => 'Tegulu',
		'th' => 'Thai',
		'bo' => 'Tibetan',
		'ti' => 'Tigrinya',
		'to' => 'Tonga',
		'ts' => 'Tsonga',
		'tr' => 'Turkish',
		'tk' => 'Turkmen',
		'tw' => 'Twi',
		'uk' => 'Ukrainian',
		'ur' => 'Urdu',
		'uz' => 'Uzbek',
		'vi' => 'Vietnamese',
		'vo' => 'Volapuk',
		'cy' => 'Welsh',
		'wo' => 'Wolof',
		'xh' => 'Xhosa',
		'ji' => 'Yiddish',
		'yo' => 'Yoruba',
		'zu' => 'Zulu',
	);

	// What language are we gonna use
	$lang = trim($lang);
	$lang = (isset($lang[$target_lang])) ? $lang : 'en';
	$language_options = '';
	foreach($target_lang as $key => $value)
	{
		$language_options .= '<option value="' . $key . '"' . (($key == $lang) ? ' selected="selected"' : '') . '>' . $value . '</option>';
	}
	return($language_options);
}

?>
