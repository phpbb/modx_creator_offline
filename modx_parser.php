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
if (!defined('IN_MODX'))
{
	exit;
}

/**
 * Parses modx files and outputs the data through parser_outdata.
 */

include('./parser_outdata.php');

class modx_parser extends parser_outdata
{
	// Switches only used while parsing.
	protected $in_author = false;
	protected $in_copy = false;
	protected $in_delete = false;
	protected $in_entry = false;
	protected $in_history = false;
	protected $in_mod_version = false;

	// Temporary vars for parsing
	protected $tmp_data = '';
	protected $used_modx = 0;
	protected $version_major = 0;
	protected $version_minor = 0;
	protected $version_revision = 0;
	protected $version_release = '';
	protected $tmp_key = -1;
	protected $lang = '';

	protected $parser;
	//protected $modx_arr = array();
	//protected $modx_row = array();
	//protected $modx_size = 0;
	//protected $modx_pos = 0;

	// Public function
	public function modx_parser($modx_data)
	{
		$this->parser = xml_parser_create('UTF-8');
		// Set some parser settings.
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($this->parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
		xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parser_set_option($this->parser, XML_OPTION_SKIP_TAGSTART, 0);

		xml_parse_into_struct($this->parser, $modx_data, $modx_arr);

		$modx_size = sizeof($modx_arr);

		// Set some local switches.
		$in_mod_version = $in_header = $in_author = $in_history = $in_installation = $in_target = $in_entry = false;

		foreach ($modx_arr as $modx_pos => $modx_row)
		{
			// The rows gets so long. And this makes the converting easier. :)
			$attrs = (isset($modx_row['attributes'])) ? $modx_row['attributes'] : '';

			switch ($modx_row['tag'])
			{
				case 'mod':
					if ($modx_row['type'] == 'open')
					{
						$this->used_modx = $this->check_modx_version($attrs['xmlns']);
					}
				break;

				// header strings
				case 'header';
					$in_header = ($modx_row['type'] == 'open') ? true : false;
				break;

				case 'level':
					$this->installation_level = (isset($modx_row['value'])) ? $modx_row['value'] : '';
				break;

				case 'license':
					$this->license = (isset($modx_row['value'])) ? $modx_row['value'] : '';
				break;

				case 'time':
					$this->installation_time = (isset($modx_row['value'])) ? $modx_row['value'] : '';
				break;

				// Single arrays
				case 'author-notes':
					$this->author_notes[] = array(
						'lang' => (isset($attrs['lang']) && $this->check_iso_lang($attrs['lang'])) ? $attrs['lang'] : 'en',
						'note' => (isset($modx_row['value'])) ? $modx_row['value'] : '',
					);
				break;

				case 'description':
					$this->description[] = array(
						'lang' => (isset($attrs['lang']) && $this->check_iso_lang($attrs['lang'])) ? $attrs['lang'] : 'en',
						'desc' => (isset($modx_row['value'])) ? $modx_row['value'] : '',
					);
				break;

				case 'diy-instructions':
					$this->diy[] = array(
						'lang' => (!empty($attrs['lang']) && $this->check_iso_lang($attrs['lang'])) ? $attrs['lang'] : 'en',
						'diy' => (!empty($modx_row['value'])) ? $modx_row['value'] : '',
					);
				break;

				case 'copy';
					$this->in_copy = ($modx_row['type'] == 'open') ? true : false;
				break;

				case 'delete';
					$this->in_delete = ($modx_row['type'] == 'open') ? true : false;
				break;

				case 'file':
					if ($this->in_copy)
					{
						$this->copy[] = array(
							'from' => (!empty($attrs['from'])) ? $attrs['from'] : '',
							'to' => (!empty($attrs['to'])) ? $attrs['to'] : '',
						);
					}
					else if ($this->in_delete && !empty($attrs['name']))
					{
						$this->delete[] = $attrs['name'];
					}
				break;

				case 'link':
					$this->link[] = array(
						'type' => (isset($attrs['type'])) ? $attrs['type'] : '',
						'href' => (isset($attrs['href'])) ? $attrs['href'] : '',
						'lang' => (isset($attrs['lang']) && $this->check_iso_lang($attrs['lang'])) ? $attrs['lang'] : 'en',
						'realname' => (isset($attrs['realname'])) ? $attrs['realname'] : '',
						'title' => (isset($modx_row['value'])) ? $modx_row['value'] : '',
					);
				break;

				case 'meta':
					if (isset($attrs['content']) && $attrs['content'] != META)
					{
						$this->meta[] = array(
							'name' => (isset($attrs['name'])) ? $attrs['name'] : '',
							'content' => $attrs['content'],
						);
					}
				break;

				case 'php-installer':
					$this->php_installer = (isset($modx_row['value'])) ? $modx_row['value'] : '';
				break;

				case 'sql':
					$this->sql[] = array(
						'dbms' => (!empty($attrs['dbms'])) ? $attrs['dbms'] : 'sql-parser',
						'query' => (!empty($modx_row['value'])) ? $modx_row['value'] : '',
					);
				break;

				case 'title':
					$this->title[] = array(
						'lang' => (isset($attrs['lang']) && $this->check_iso_lang($attrs['lang'])) ? $attrs['lang'] : 'en',
						'title' => (isset($modx_row['value'])) ? $modx_row['value'] : '',
					);
				break;

				// Author stuff
				case 'author':
					if ($modx_row['type'] == 'open')
					{
						$in_author = true;
						$this->author[$this->cnt_author] = array();
					}
					else if ($modx_row['type'] == 'close')
					{
						$in_author = false;
						$this->cnt_author++;
					}
				break;

				case 'contributions':
					if ($in_author)
					{
						$this->author[$this->cnt_author]['contributions'][] = array(
							'status' => (isset($attrs['status'])) ? $attrs['status'] : '',
							'from' => (isset($attrs['from'])) ? $attrs['from'] : '',
							'to' => (isset($attrs['to'])) ? $attrs['to'] : '',
							'position' => (isset($attrs['position'])) ? $attrs['position'] : '',
						);
					}
				break;

				case 'email':
					if ($in_author)
					{
						$this->author[$this->cnt_author]['email'] = (isset($modx_row['value'])) ? $modx_row['value'] : '';
					}
				break;

				case 'homepage':
					if ($in_author)
					{
						$this->author[$this->cnt_author]['homepage'] = (isset($modx_row['value'])) ? $modx_row['value'] : '';
					}
				break;

				case 'realname':
					if($in_author)
					{
						$this->author[$this->cnt_author]['realname'] = (isset($modx_row['value'])) ? $modx_row['value'] : '';
					}
				break;

				case 'username':
					if ($in_author)
					{
						$this->author[$this->cnt_author]['username'] = (isset($modx_row['value'])) ? $modx_row['value'] : '';
						if (isset($attrs['phpbbcom']))
						{
							$this->author[$this->cnt_author]['phpbbcom'] = true;
						}
					}
				break;

				// Version strings
				case 'mod-version':
					if ($this->used_modx == MODX_10 && $modx_row['type'] == 'open')
					{
						$in_mod_version = true;
					}
					else if ($this->used_modx == MODX_10 && $modx_row['type'] == 'close')
					{
						$this->mod_version = (isset($version_major)) ? $version_major : '';
						$this->mod_version .= (isset($version_minor)) ? '.' . $version_minor : '';
						$this->mod_version .= (isset($version_revision)) ? '.' . $version_revision : '';
						$this->mod_version .= (isset($version_release)) ? '.' . $version_release : '';

						// Empty these since they are used by others.
						$version_major = $version_minor = $version_revision = $version_release = '';
						$in_mod_version = false;
					}
					else if ($this->used_modx == MODX_12)
					{
						$this->mod_version = (isset($modx_row['value'])) ? $modx_row['value'] : '';
					}
				break;

				case 'major':
					$version_major = (isset($modx_row['value'])) ? $modx_row['value'] : '';
				break;

				case 'minor':
					$version_minor = (isset($modx_row['value'])) ? $modx_row['value'] : '';
				break;

				case 'release':
					$version_release = (isset($modx_row['value'])) ? $modx_row['value'] : '';
				break;

				case 'revision':
					$version_revision = (isset($modx_row['value'])) ? $modx_row['value'] : '';
				break;

				case 'target-major':
					$version_major = (!empty($modx_row['value'])) ? $modx_row['value'] : '';
				break;

				case 'target-minor':
					$version_minor = (!empty($modx_row['value'])) ? $modx_row['value'] : '';
				break;

				case 'target-release':
					$version_release = (!empty($modx_row['value'])) ? $modx_row['value'] : '';
				break;

				case 'target-revision':
					$version_revision = (!empty($modx_row['value'])) ? $modx_row['value'] : '';
				break;

				case 'target-version':
					if ($this->used_modx == MODX_10 && $modx_row['type'] == 'open')
					{
						$in_target = true;
					}
					else if ($this->used_modx == MODX_10 && $modx_row['type'] == 'close')
					{
						$this->target_version = (isset($version_major)) ? $version_major : '';
						$this->target_version .= (isset($version_minor)) ? '.' . $version_minor : '';
						$this->target_version .= (isset($version_revision)) ? '.' . $version_revision : '';
						$this->target_version .= (isset($version_release)) ? '.' . $version_release : '';

						$version_major = $version_minor = $version_revision = $version_release = '';
						$in_target = false;
					}
					else if ($this->used_modx == MODX_12)
					{
						$this->target_version = (isset($modx_row['value'])) ? $modx_row['value'] : '';
					}
				break;

				// History
				case 'change':
					if ($in_entry)
					{
						$this->history[$this->cnt_history]['changelog'][] = array(
							'lang' => $this->lang,
							'change' => (isset($modx_row['value'])) ? $modx_row['value'] : '',
						);
					}
				break;

				case 'changelog':
					if ($in_entry)
					{
						if ($modx_row['type'] == 'open')
						{
							$this->lang = (isset($attrs['lang']) && $this->check_iso_lang($attrs['lang'])) ? $attrs['lang'] : 'en';
						}
					}
				break;

				case 'date':
					if($in_entry)
					{
						$this->history[$this->cnt_history]['date'] = (isset($modx_row['value'])) ? $modx_row['value'] : '';
					}
				break;

				case 'entry':
					if ($modx_row['type'] == 'open')
					{
						$this->history[$this->cnt_history] = array();
						$in_entry = true;
					}
					else
					{
						$this->cnt_history++;
						$this->cnt_changelog = 0;
						$in_entry = false;
					}
				break;

				case 'rev-version':
					if($in_entry)
					{
						if ($this->used_modx == MODX_10 && $modx_row['type'] == 'close')
						{
							$this->history[$this->cnt_history]['version'] = (isset($version_major)) ? $version_major : '';
							$this->history[$this->cnt_history]['version'] .= (isset($version_minor)) ? '.' . $version_minor : '';
							$this->history[$this->cnt_history]['version'] .= (isset($version_revision)) ? '.' . $version_revision : '';
							$this->history[$this->cnt_history]['version'] .= (isset($version_release)) ? '.' . $version_release : '';

							$version_major = $version_minor = $version_revision = $version_release = '';
						}
						else if ($this->used_modx == MODX_12)
						{
							$this->history[$this->cnt_history]['version'] = (isset($modx_row['value'])) ? $modx_row['value'] : '';
						}
					}
				break;

				// The damage part
				case 'action':
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action++] = array(
						'type' => (isset($attrs['type'])) ? $attrs['type'] : '',
						'data' =>  (!empty($modx_row['value'])) ? $modx_row['value'] : '',
					);
				break;

				case 'comment':
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action++] = array(
						'lang' => (!empty($attrs['lang']) && $this->check_iso_lang($attrs['lang'])) ? $attrs['lang'] : 'en',
						'type' => 'comment',
						'data' =>  (!empty($modx_row['value'])) ? $modx_row['value'] : '',
					);
				break;

				case 'edit':
 					if ($modx_row['type'] == 'open')
					{
						$this->action[$this->cnt_open][$this->cnt_edit] = array();
					}
					else
					{
						$this->cnt_edit++;
						$this->cnt_action = 0;
					}
				break;

				case 'find':
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action++] = array(
						'type' => 'find',
						'data' =>  (!empty($modx_row['value'])) ? $modx_row['value'] : '',
					);
				break;

				case 'remove':
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action++] = array(
						'type' => 'remove',
						'data' =>  (!empty($modx_row['value'])) ? $modx_row['value'] : '',
					);
				break;

				case 'inline-action':
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action++] = array(
						'type' => (isset($attrs['type'])) ? 'inline-' . $attrs['type'] : '',
						'data' =>  (!empty($modx_row['value'])) ? $modx_row['value'] : '',
					);
				break;

				case 'inline-find':
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action++] = array(
						'type' => 'inline-find',
						'data' =>  (!empty($modx_row['value'])) ? $modx_row['value'] : '',
					);
				break;

				case 'inline-remove':
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action++] = array(
						'type' => 'inline-remove',
						'data' =>  (!empty($modx_row['value'])) ? $modx_row['value'] : '',
					);
				break;

				case 'open':
 					if ($modx_row['type'] == 'open')
					{
						$this->action[$this->cnt_open] = array(
							'file' => (isset($attrs['src'])) ? $attrs['src'] : '',
						);
					}
					else
					{
						$this->cnt_open++;
						$this->cnt_edit = 0;
					}
				break;
			}
		}

		xml_parser_free($this->parser);

		// We need to set the counters back to zero.
		$this->cnt_action = 0;
		$this->cnt_author = 0;
		$this->cnt_author_notes = 0;
		$this->cnt_change = 0;
		$this->cnt_changelog = 0;
		$this->cnt_copy = 0;
		$this->cnt_delete = 0;
		$this->cnt_description = 0;
		$this->cnt_diy = 0;
		$this->cnt_edit = 0;
		$this->cnt_history = 0;
		$this->cnt_link = 0;
		$this->cnt_meta = 0;
		$this->cnt_open = 0;
		$this->cnt_sql = 0;
		$this->cnt_title = 0;
	}

	// Private functions.
	/**
	 * Checks the MODX version. There are differences in the version strings from 1.0.x to 1.2.x.
	 */
	private function check_modx_version($version_string)
	{
		if (strpos($version_string, 'modx-1.0') !== false)
		{
			return(MODX_10);
		}
		else
		{
			return(MODX_12);
		}
	}

	/**
	 * check_iso_lang()
	 *
	 * Checks if the submitted lang code is valid.
	 * @param $lang, string containing the language code
	 *
	 * @return bool, true if the code is valid.
	 */
	private function check_iso_lang($lang)
	{
		$lang_codes = array('ab', 'aa', 'af', 'sq', 'am', 'ar', 'hy', 'as', 'ay', 'az', 'ba', 'eu', 'bn', 'dz', 'bh', 'bi', 'br', 'bg', 'my', 'be', 'km', 'ca', 'zh', 'co', 'hr', 'cs', 'da', 'nl', 'en', 'eo', 'et', 'fo', 'fj', 'fi', 'fr', 'fy', 'gl', 'ka', 'de', 'el', 'kl', 'gn', 'gu', 'ha', 'iw', 'hi', 'hu', 'is', 'in', 'ia', 'ik', 'ga', 'it', 'ja', 'jw', 'kn', 'ks', 'kk', 'rw', 'ky', 'rn', 'ko', 'ku', 'lo', 'la', 'lv', 'ln', 'lt', 'mk', 'mg', 'ms', 'ml', 'mt', 'mi', 'mr', 'mo', 'mn', 'na', 'ne', 'no', 'oc', 'or', 'om', 'ps', 'fa', 'pl', 'pt', 'pa', 'qu', 'rm', 'ro', 'ru', 'sm', 'sg', 'sa', 'gd', 'sr', 'sh', 'st', 'tn', 'sn', 'sd', 'si', 'ss', 'sk', 'sl', 'so', 'es', 'su', 'sw', 'sv', 'tl', 'tg', 'ta', 'tt', 'te', 'th', 'bo', 'ti', 'to', 'ts', 'tr', 'tk', 'tw', 'uk', 'ur', 'uz', 'vi', 'vo', 'cy', 'wo', 'xh', 'ji', 'yo', 'zu');

		// What language are we gonna use
		$return = in_array($lang, $lang_codes);

		return($return);
	}

}
