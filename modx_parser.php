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
 * All internal vars are protected and only accessible trough the public functions.
 */

define('MODX_10', 1);
define('MODX_12', 2);

class modx_parser
{
	// MODX strings
	protected $installation_level = '';
	protected $installation_time = '';
	protected $license = '';
	protected $mod_version = '';
	protected $modx_version = '';
	protected $target_version = '';

	// MODX arrays
	protected $action = array();
	protected $author = array();
	protected $author_notes = array();
	protected $copy = array();
	protected $description = array();
	protected $diy = array();
	protected $history = array();
	protected $link = array();
	protected $meta = array();
	protected $sql = array();
	protected $title = array();

	// Counters
	protected $cnt_action = 0;
	protected $cnt_author = 0;
	protected $cnt_author_notes = 0;
	protected $cnt_change = 0;
	protected $cnt_changelog = 0;
	protected $cnt_copy = 0;
	protected $cnt_description = 0;
	protected $cnt_diy = 0;
	protected $cnt_edit = 0;
	protected $cnt_history = 0;
	protected $cnt_link = 0;
	protected $cnt_meta = 0;
	protected $cnt_open = 0;
	protected $cnt_sql = 0;
	protected $cnt_title = 0;

	// Switches only used while parsing.
	protected $in_author = false;
	protected $in_copy = false;
	protected $in_entry = false;
	protected $in_history = false;
	protected $in_mod_version = false;

	// Temporary vars
	protected $tmp_data = '';
	protected $used_modx = 0;
	protected $version_major = 0;
	protected $version_minor = 0;
	protected $version_revision = 0;
	protected $version_release = '';
	protected $tmp_key = -1;

	// Public functions
	public function modx_parser($modx_data)
	{
		$xml_parser = xml_parser_create();
		// use case-folding so we are sure to find the tag in $map_array
		xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
		xml_set_element_handler($xml_parser, array($this, 'startElement'), array($this, 'endElement'));
		xml_set_character_data_handler($xml_parser, array($this, 'characterData'));

		if (!xml_parse($xml_parser, $modx_data))
		{
			die(sprintf("XML error: %s at line %d", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)));
		}
		xml_parser_free($xml_parser);

		// We need to set the counters back to zero.
		$this->cnt_action = 0;
		$this->cnt_author = 0;
		$this->cnt_author_notes = 0;
		$this->cnt_change = 0;
		$this->cnt_changelog = 0;
		$this->cnt_copy = 0;
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

	/**
	 * get_modx_installation_level
	 *
	 * Get the installation level for the MOD
	 * @param $string, bool
	 * @return string if $string is true or predefined int if $string is false
	 */
	public function get_modx_installation_level($string = false)
	{
		if ($string)
		{
			return($this->mod_version);
		}
		else
		{
			switch ($this->mod_version)
			{
				case 'easy':
					return(EASY);
				break;

				case 'advanced':
					return(ADVANCED);
				break;

				default:
					return(INTERMEDIATE);
				break;
			}
		}
	}

	/**
	 * get_modx_installation_time
	 *
	 * Get the installation time, in seconds or minutes.
	 * @param $seconds, bool
	 * @return int, minutes if $seconds is false otherwise seconds
	 */
	public function get_modx_installation_time($seconds = true)
	{
		if($seconds)
		{
			return($this->installation_time);
		}
		else
		{
			return($this->installation_time / 60);
		}
	}

	/**
	 * get_modx_license
	 *
	 * Get the license for the MOD
	 * @return string license
	 */
	public function get_modx_license()
	{
		return($this->license);
	}

	/**
	 * get_modx_mod_version
	 *
	 * @return string, the MOD version
	 */
	public function get_modx_mod_version()
	{
		return($this->mod_version);
	}

	/**
	 * get_modx_target
	 *
	 * @return string, the target phpBB version
	 */
	public function get_modx_target_version()
	{
		return($this->target_version);
	}

	/**
	 * get_modx_version
	 *
	 * @return string, the MODX version used in this file
	 */
	public function get_modx_version()
	{
		return($this->modx_version);
	}

	/**
	 * get_modx_authors
	 *
	 * Get the MOD authors. Loop this to get all authors.
	 *
	 * $author[] = array(
	 * 	'realname',
	 * 	'username',
	 * 	'phpbbcom',
	 * 	'homepage',
	 * 	'email',
	 * 	'contributions'[] => array(
	 *  	'status',
	 *  	'from',
	 *  	'to',
	 *  	'position',
	 * 	),
	 * );
	 *
	 * @return array, the MOD author array or false when there is no more.
	 */
	public function get_modx_authors()
	{
		if ($this->cnt_author < sizeof($this->author))
		{
			return($this->author[$this->cnt_author++]);
		}
		else
		{
			return(false);
		}
	}

	/**
	 * get_modx_copy
	 *
	 * Get the copy actions for this MOD. Loop this to get all.
	 *
	 * $copy[] = array(
	 * 	'from',
	 * 	'to',
	 * );
	 *
	 * @return array, the copy actions, or false when done.
	 */
	public function get_modx_copy()
	{
		if ($this->cnt_copy < sizeof($this->copy))
		{
			return($this->copy[$this->cnt_copy++]);
		}
		else
		{
			return(false);
		}
	}

	/**
	 * get_modx_description
	 *
	 * Get the MOD descriptions. Loop this to get the all.
	 *
	 * $description[] = array(
	 * 	'lang',
	 * 	'data',
	 * );
	 *
	 * @param $lang, string with language code. Returns false if $lang is not found.
	 * @return string or false if $lang is set.
	 * @return array with the description or false if $lang is specified and don't exist.
	 */
	public function get_modx_description($lang = '')
	{
		if ($lang != '')
		{}
		else
		{
			if ($this->cnt_description < sizeof($this->description))
			{
				return($this->description[$this->cnt_description++]);
			}
			else
			{
				return(false);
			}
		}
	}

	/**
	 * get_modx_history
	 *
	 * Get the MOD history. Loop this for all.
	 *
	 * $history[] = array(
	 * 	'date',
	 * 	'version',
	 * 	'changelog'[] => array(
	 * 		'lang',
	 * 		'change'[] => change
	 * 	),
	 * );
	 *
	 * @param $lang, string with language code. Returns false if $lang is not found.
	 * @return string or false if $lang is set.
	 * @return array, the MOD history or false when done.
	 */
	public function get_modx_history($lang = '')
	{
		if ($lang != '')
		{}
		else
		{
			if ($this->cnt_history < sizeof($this->history))
			{
				return($this->history[$this->cnt_history++]);
			}
			else
			{
				return(false);
			}
		}
	}

	/**
	 * get_modx_links
	 *
	 * Get the links for the MOD. Loop this to get them all.
	 *
	 * $link[] = array(
	 * 	'type',
	 * 	'href',
	 * 	'lang',
	 * 	'realname',
	 * 	'data',
	 * );
	 *
	 * @param $lang, string with language code. Returns false if $lang is not found.
	 * @return array with the selected lang or false if $lang is set.
	 * @return array with the link or false when there are no more links.
	 */
	public function get_modx_links($lang = '')
	{
		if ($lang != '')
		{}
		else
		{
			if ($this->cnt_link < sizeof($this->link))
			{
				return($this->link[$this->cnt_link++]);
			}
			else
			{
				return(false);
			}
		}
	}

	/**
	 * get_modx_meta
	 *
	 * Get the meta tags. Loop this to get all.
	 *
	 * $meta[] = array(
	 * 	'name',
	 * 	'content',
	 * );
	 *
	 * @return array or false if there are no more.
	 */
	public function get_modx_meta()
	{
		if ($this->cnt_meta < sizeof($this->meta))
		{
			return($this->meta[$this->cnt_meta++]);
		}
		else
		{
			return(false);
		}
	}

	/**
	 * get_modx_notes
	 *
	 * Get the author notes for this MOD. Loop to get them all.
	 *
	 * $author_notes[] = array(
	 * 	'lang',
	 * 	'data',
	 * );
	 *
	 * @param $lang, string with language code. Returns false if $lang is not found.
	 * @return string or false if $lang is set.
	 * @return array or false if done.
	 */
	public function get_modx_notes($lang = '')
	{
		if ($lang != '')
		{}
		else
		{
			if ($this->cnt_author_notes < sizeof($this->author_notes))
			{
				return($this->author_notes[$this->cnt_author_notes++]);
			}
			else
			{
				return(false);
			}
		}
	}

	/**
	 * get_modx_query
	 *
	 * Get the querys for this MOD. Loop to get all.
	 *
	 * $sql[] = array(
	 * 	'dbms',
	 * 	'data',
	 * );
	 *
	 * @return array or false when done.
	 */
	public function get_modx_sql($dbms = '')
	{
		if ($dbms != '')
		{}
		else
		{
			if ($this->cnt_sql < sizeof($this->sql))
			{
				return($this->sql[$this->cnt_sql++]);
			}
			else
			{
				return(false);
			}
		}
	}

	/**
	 * get_modx_title
	 *
	 * Get the MOD title. Loop for all titles.
	 *
	 * $title[] = array(
	 * 	'lang',
	 * 	'data',
	 * );
	 *
	 * @param $lang, string with language code. Returns false if $lang is not found.
	 * @return string or false if $lang is set.
	 * @return array or false when done
	 */
	public function get_modx_title($lang = '')
	{
		if ($lang != '')
		{}
		else
		{
			if ($this->cnt_title < sizeof($this->title))
			{
				return($this->title[$this->cnt_title++]);
			}
			else
			{
				return(false);
			}
		}
	}

	/**
	 * get_modx_diy
	 *
	 * Get the MOD DIY. Loop for all DIYs.
	 *
	 * $diy[] = array(
	 * 	'lang',
	 * 	'data',
	 * );
	 *
	 * @param $lang, string with language code. Returns false if $lang is not found.
	 * @return string or false if $lang is set.
	 * @return array or false when done
	 */
	public function get_modx_diy($lang = '')
	{
		if ($lang != '')
		{}
		else
		{
			if ($this->cnt_diy < sizeof($this->diy))
			{
				return($this->diy[$this->cnt_diy++]);
			}
			else
			{
				return(false);
			}
		}
	}

	/**
	 * get_modx_action
	 *
	 * Get the action in order.
	 *
	 * $action[] = array(
	 * 	'src',
	 * 	x[] => array( // x == int
	 * 		'type',
	 * 		'data',
	 * 	),
	 * );
	 *
	 * @return array with change, string with file name or false when done
	 */
	public function get_modx_action()
	{
		if ($this->cnt_action < sizeof($this->action))
		{
			return($this->action[$this->cnt_action++]);
		}
		else
		{
			return(false);
		}
	}

	// Private functions.
	private function startElement($parser, $name, $attrs)
	{
		switch ($name)
		{
			case 'action':
				$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action] = array(
					'type' => $attrs['type'],
				);
			break;

			case 'author':
				$this->in_author = true;
				$this->author[$this->cnt_author] = array();
			break;

			case 'author-notes':
				$this->author_notes[$this->cnt_author_notes] = array(
					'lang' => (isset($attrs['lang']) && $this->check_iso_lang($attrs['lang'])) ? $attrs['lang'] : 'en',
				);
			break;

			case 'changelog':
				if ($this->in_entry)
				{
					$lang = (isset($attrs['lang']) && $this->check_iso_lang($attrs['lang'])) ? $attrs['lang'] : 'en';
					// Do we already have a entry for this lang and version?
					$this->tmp_key = $this->cnt_changelog;
					if (!empty($this->history[$this->cnt_history]['changelog']))
					{
						foreach ($this->history[$this->cnt_history]['changelog'] as $key => $value)
						{
							if (!empty($value['lang']) && $value['lang'] == $lang)
							{
								$this->tmp_key = $key;
								break;
							}
						}
					}

					// Else we need a new array
					if ($this->tmp_key == $this->cnt_changelog)
					{
						$this->history[$this->cnt_history]['changelog'][$this->tmp_key] = array(
							'lang' => $lang,
							'data' => array(),
						);
					}
				}
			break;

			case 'comment':
				$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action] = array(
					'lang' => (isset($attrs['lang']) && $this->check_iso_lang($attrs['lang'])) ? $attrs['lang'] : 'en',
					'type' => 'comment',
				);
			break;

			case 'contributions':
				$this->author[$this->cnt_author]['contributions'][] = array(
					'status' => (isset($attrs['status'])) ? $attrs['status'] : '',
					'from' => (isset($attrs['from'])) ? $attrs['from'] : '',
					'to' => (isset($attrs['to'])) ? $attrs['to'] : '',
					'position' => (isset($attrs['position'])) ? $attrs['position'] : '',
				);
			break;

			case 'copy':
				$this->in_copy = true;
			break;

			case 'description':
				$this->description[$this->cnt_description] = array(
					'lang' => (isset($attrs['lang']) && $this->check_iso_lang($attrs['lang'])) ? $attrs['lang'] : 'en',
				);
			break;

			case 'diy-instructions':
				$this->diy[$this->cnt_diy] = array(
					'lang' => (isset($attrs['lang']) && $this->check_iso_lang($attrs['lang'])) ? $attrs['lang'] : 'en',
				);
			break;

			case 'edit':
				$this->action[$this->cnt_open][$this->cnt_edit] = array();
			break;

			case 'entry':
				if ($this->in_history)
				{
					$this->history[$this->cnt_history] = array();
					$this->in_entry = true;
				}
			break;

			case 'file':
				if ($this->in_copy)
				{
					$this->copy[] = array(
						'from' => $attrs['from'],
						'to' => $attrs['to'],
					);
				}
			break;

			case 'history':
				$this->in_history = true;
			break;

			case 'inline-action':
				$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action] = array(
					'type' => 'inline-' . $attrs['type'],
				);
			break;

			case 'link':
				$this->link[$this->cnt_link] = array(
					'type' => (isset($attrs['type'])) ? $attrs['type'] : '',
					'href' => (isset($attrs['href'])) ? $attrs['href'] : '',
					'lang' => (isset($attrs['lang']) && $this->check_iso_lang($attrs['lang'])) ? $attrs['lang'] : 'en',
				);

				if (!empty($attrs['realname']))
				{
					$this->link[$this->cnt_link]['realname'] = $attrs['realname'];
				}
			break;

			case 'meta':
				$this->meta[] = array(
					'name' => $attrs['name'],
					'content' => $attrs['content'],
				);
			break;

			case 'mod':
				// This should be first. We need the modx version.
				$this->modx_version = $attrs['xmlns'];
				$this->used_modx = $this->check_modx_version($this->modx_version);
			break;

			case 'open':
				$this->action[$this->cnt_open] = array(
					'file' => (isset($attrs['src'])) ? $attrs['src'] : '',
				);
			break;

			case 'sql':
				$this->sql[$this->cnt_sql] = array(
					'dbms' => (!empty($attrs['dbms'])) ? $attrs['dbms'] : 'sql-parser',
				);
			break;

			case 'title':
				$this->title[$this->cnt_title] = array(
					'lang' => (isset($attrs['lang']) && $this->check_iso_lang($attrs['lang'])) ? $attrs['lang'] : 'en',
				);
			break;

			case 'username':
				$this->author[$this->cnt_author]['phpbbcom'] = (isset($attrs['phpbbcom'])) ? true : false;
			break;
		}
	}

	private function endElement($parser, $name)
	{
		switch ($name)
		{
			case 'action':
				$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action++]['data'] = $this->tmp_data;
			break;

			case 'author':
				$this->in_author = false;
				$this->cnt_author++;
			break;

			case 'author-notes':
				$this->author_notes[$this->cnt_author_notes]['data'] = $this->tmp_data;
				$this->cnt_author_notes++;
			break;

			case 'change':
				if ($this->in_entry)
				{
					$this->history[$this->cnt_history]['changelog'][$this->tmp_key]['change'][] = $this->tmp_data;
				}
			break;

			case 'changelog':
				if ($this->tmp_key == $this->cnt_changelog)
				{
					$this->cnt_changelog++;
				}
			break;

			case 'copy':
				$this->in_copy = false;
			break;

			case 'comment':
				$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action++]['data'] = $this->tmp_data;
			break;

			case 'date':
				if($this->in_history)
				{
					$this->history[$this->cnt_history]['date'] = $this->tmp_data;
				}
			break;

			case 'description':
				$this->description[$this->cnt_description]['data'] = $this->tmp_data;
				$this->cnt_description++;
			break;

			case 'diy-instructions':
				$this->diy[$this->cnt_diy++]['data'] = $this->tmp_data;
			break;

			case 'edit':
				$this->cnt_edit++;
				$this->cnt_action = 0;
			break;

			case 'email':
				if($this->in_author)
				{
					$this->author[$this->cnt_author]['email'] = $this->tmp_data;
				}
			break;

			case 'entry':
				if($this->in_history)
				{
					$this->cnt_history++;
					$this->in_entry = false;
					$this->cnt_changelog = 0;
				}
			break;

			case 'find':
				$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action++] = array(
					'type' => 'find',
					'data' => $this->tmp_data,
				);
			break;

			case 'history':
				$this->in_history = false;
			break;

			case 'homepage':
				if($this->in_author)
				{
					$this->author[$this->cnt_author]['homepage'] = $this->tmp_data;
				}
			break;

			case 'inline-action':
				$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action++]['data'] = $this->tmp_data;
			break;

			case 'inline-find':
				$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action++] = array(
					'type' => 'inline-find',
					'data' => $this->tmp_data,
				);
			break;

			case 'level':
				$this->installation_level = $this->tmp_data;
			break;

			case 'license':
				$this->license = $this->tmp_data;
			break;

			case 'link':
				$this->link[$this->cnt_link]['title'] = $this->tmp_data;
				$this->cnt_link++;
			break;

			case 'major':
				$this->version_major = $this->tmp_data;
			break;

			case 'minor':
				$this->version_minor = $this->tmp_data;
			break;

			case 'mod-version':
				if($this->used_modx == MODX_10)
				{
					$this->mod_version = $this->version_major;
					$this->mod_version .= '.' . $this->version_minor;
					$this->mod_version .= '.' . $this->version_revision;
					$this->mod_version .= ($this->version_release != '') ? '.' . $this->version_release : '';

					// Set them back to defaut for the history.
					$this->version_major = $this->version_minor = $this->version_revision = 0;
					$this->version_release = '';
				}
				else
				{
					$this->mod_version = $this->tmp_data;
				}
			break;

			case 'open':
				$this->cnt_open++;
				$this->cnt_edit = 0;
			break;

			case 'realname':
				if($this->in_author)
				{
					$this->author[$this->cnt_author]['realname'] = $this->tmp_data;
				}
			break;

			case 'release':
				$this->version_release = $this->tmp_data;
			break;

			case 'rev-version':
				if($this->in_history)
				{
					if($this->used_modx == MODX_10)
					{
						$this->tmp_data = $this->version_major;
						$this->tmp_data .= '.' . $this->version_minor;
						$this->tmp_data .= '.' . $this->version_revision;
						$this->tmp_data .= ($this->version_release != '') ? '.' . $this->version_release : '';
						$this->history[$this->cnt_history]['version'] = $this->tmp_data;

						// Set them back to defaut for the history.
						$this->version_major = $this->version_minor = $this->version_revision = 0;
						$this->version_release = '';
					}
					else
					{
						$this->history[$this->cnt_history]['version'] = $this->tmp_data;
					}
				}
			break;

			case 'revision':
				$this->version_revision = $this->tmp_data;
			break;

			case 'sql':
				$this->sql[$this->cnt_sql++]['data'] = $this->tmp_data;
			break;

			case 'target-major':
				$this->version_major = $this->tmp_data;
			break;

			case 'target-minor':
				$this->version_minor = $this->tmp_data;
			break;

			case 'target-release':
				$this->version_release = $this->tmp_data;
			break;

			case 'target-revision':
				$this->version_revision = $this->tmp_data;
			break;

			case 'target-version':
				if($this->used_modx == MODX_10)
				{
					$this->target_version = $this->version_major;
					$this->target_version .= '.' . $this->version_minor;
					$this->target_version .= '.' . $this->version_revision;
					$this->target_version .= ($this->version_release != '') ? '.' . $this->version_release : '';

					// Set them back to defaut for the history.
					$this->version_major = $this->version_minor = $this->version_revision = 0;
					$this->version_release = '';
				}
				else
				{
					$this->target_version = $this->tmp_data;
				}
			break;

			case 'time':
				$this->installation_time = $this->tmp_data;
			break;

			case 'title':
				$this->title[$this->cnt_title]['data'] = $this->tmp_data;
				$this->cnt_title++;
			break;

			case 'username':
				if($this->in_author)
				{
					$this->author[$this->cnt_author]['username'] = $this->tmp_data;
				}
			break;
		}
		$this->tmp_data = '';
	}

	private function characterData($parser, $data)
	{
		$this->tmp_data = $data;
	}

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
