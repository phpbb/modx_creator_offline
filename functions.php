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

	if(!version_compare(PHP_VERSION, '6.0.0-dev', '>=') && get_magic_quotes_gpc())
	{
		$text = stripslashes($text);
	}

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
* get_tag()
*
* @param string $modx_data, the string from the modx file
* @param string $tag, tag name
* @param bool $arr, true = return array, false = return string
* @param bool $attributes, true = sends back the matches too, false = sends back the data between the matches
* @return string or array, the data from the MODX-tags
*/
function get_tag($modx_data, $tag, $arr = true, $attributes = false)
{
	$attribute = ($attributes) ? 0 : 1;
	$end_gt = ($attributes) ? ' ' : '>';
	preg_match_all('/<' . $tag . '\b[^>]*>(.*?)<\/' . $tag . '>/s', $modx_data, $out_arr, PREG_PATTERN_ORDER);
	if($arr)
	{
		return($out_arr[$attribute]);
	}
	else if(isset($out_arr[$attribute][0]))
	{
		return($out_arr[$attribute][0]);
	}
}

/**
* get_empty_tag()
*
* @param string $data, the string from the modx file
* @param string $tag, tag name
* @return array, the data from the empty MODX-tags <tag /> or <tag></tag>
*/
function get_empty_tag($data, $tag)
{
	preg_match_all('|<' . $tag . '(.*?)>|', $data, $out_arr, PREG_PATTERN_ORDER);
	$return = (isset($out_arr[0])) ? $out_arr[0] : '';
	return($return);
}

/**
* strip_tags
*
* Strips unvanted stuff from fields...
* <contributions-group></contributions-group>, <copy></copy> and so on.
* @param string $data, or array, the string to remove tags from
* @param string $tag, the tag to remove
* @return string
*/
function strip_tag_group($data, $tag)
{
	$data = preg_replace('<' . $tag . '>', '', $data);
	$data = preg_replace('</' . $tag . '>', '', $data);
	return($data);
}

/**
* get_attribute()
*
* @param string $data, the string to get the attribute from
* @param string $attribute, the attribute to get
* @param int $len, how many chars to return, 0 = all
* @return string, the attributes
*/
function get_attribute($data, $attribute, $len = 0)
{
	preg_match('|' . $attribute . '="(.[^"]*)|', $data, $out_arr);
	if(isset($out_arr[1]))
	{
		$out_arr[1] = (strlen($out_arr[1]) > $len && $len > 0) ? substr($out_arr[1], 0, $len) : $out_arr[1];
		trim_cdata($out_arr[1]);
		return($out_arr[1]);
	}
	return('');
}

/**
* get_tagdata()
*
* @param string $data, the string to get the data from
* @param string $tag, tag name
* @return string, the data from strings with attributes
*/
function get_tagdata($data, $tag)
{
	preg_match('/>(.*?)<\/' . $tag . '>/s', $data, $out_arr);
	$return = (isset($out_arr[1])) ? $out_arr[1] : '';
	trim_cdata($return);
	return($return);
}

/**
* get_single_arr()
*
* @param string $data, the string to get the data from
* @param string $tag, tag name
* @param string $data_name, the fieldname for the data field in the returned array
* @param string $attribute, the attribute name if not name.
* @return array for those fields that only have data and one attribute.
*/
function get_single_arr($data, $tag, $data_name, $attribute = '')
{
	$out_arr = array();
	$temp_arr = get_tag($data, $tag, true, true);

	$cnt = 0;
	foreach($temp_arr as $value)
	{
		$field_id = 'in_' . $cnt++;
		// The lang attribute first Max 2 chars
		if($attribute == '')
		{
			$out_arr[$field_id]['lang'] = get_attribute($value, 'lang', 2);
		}
		else
		{
			$out_arr[$field_id][$attribute] = get_attribute($value, $attribute);
		}
		// Then the data
		$out_arr[$field_id][$data_name] = get_tagdata($value, $tag);
	}
	trim_cdata($out_arr);
	return($out_arr);
}

/**
* trim_cdata
*
* Removes <![CDATA[]]> from strings
* @param string
* @return string
*/
function trim_cdata(&$data)
{
	if($data == '' || $data == '<![CDATA[]]>')
	{
		$data = '';
		return($data);
	}
	else
	{
		if(is_array($data))
		{
			foreach($data as $key => $value)
			{
				trim_cdata($data[$key]);
			}
		}
		else
		{
			preg_match('/<\!\[CDATA\[(.*?)\]\]>/s', $data, $out_arr);
			// $out_arr[1] Will be empty if there is no CDATA
			$data = (empty($out_arr[1])) ? $data : $out_arr[1];
			return($data);
		}
	}
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
	if ($pos = strpos($data, "\n") !== false )
	{
		$data = substr($data, 0, $pos);
	}
}

/**
* generate_value()
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
* modx_version()
*
* Returns the version of the imported MODX file
* @param string $xmlns, the version string from the file.
* @return bool
*/
function modx_version($xmlns)
{
	if(preg_match('#modx\\-1\\.0(\\.\d)*\\.xsd#s', $xmlns))
	{
		return(MODX_V1);
	}
	if(preg_match('#modx\\-1\\.2(\\.\d)\\.xsd#s', $xmlns))
	{
		return(MODX_V2);
	}
	return(false);
}

/**
* modx_stripslashes()
*
* A stripslashes that handles arrays. \r also gets removed.
* It's intended to be used only with arrays, but has the is_array() check just to be sure.
* @param array $in_arr, the array with arrays that needs to be stripped
* @return array, the stripped array
*/
function modx_stripslashes(&$array)
{
	global $strip;
	if(!is_array($array))
	{
		$array = str_replace(chr(13), '', $array);
		return(stripslashes($array));
	}

	foreach($array as $key => $value)
	{
		if(is_array($value))
		{
			modx_stripslashes($array[$key]);
		}
		else
		{
			$value = str_replace(chr(13), '', $value);
			if($strip)
			{
				$array[$key] = stripslashes($value);
			}
		}
	}
	return($array);
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
