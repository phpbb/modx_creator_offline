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

define('PHPBB_LATEST', '3.0.6');
define('MODX_LATEST', 'modx-1.2.3.xsd');

define('MOD', 1);
define('MODX', 2);
define('MODX_V1', 1);
define('MODX_V2', 2);

define('EASY', 1);
define('INTERMEDIATE', 2);
define('ADVANCED', 3);

define('EDIT', 1);
define('FIND', 2);
define('ACTION', 3);
define('INLINE', 4);
define('OUTLINE', 5);

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

?>