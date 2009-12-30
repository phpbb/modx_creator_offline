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
 * This class is to output what the parsers collect
 */

class parser_outdata
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

	// Public functions
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
}
