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
 * Parses the post data and outputs the data through parser_outdata.
 * Need to have it in a class to be compatible with the data expected by the creator.
 */

include('./parser_outdata.php');

class post_parser extends parser_outdata
{
	// Switches only used while parsing.
	protected $in_author = false;
	protected $in_copy = false;
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
	protected $cnt_contrib = -1;

	// Public function
	public function post_parser($post_data)
	{
		$this->cnt_action = -1;
		$this->cnt_edit = -1;
		$this->cnt_open = -1;

		// The strings
		$this->mod_version = (isset($post_data['version'])) ? stripslashes(trim($post_data['version'])) : '';
		$this->target_version = (isset($post_data['target'])) ? stripslashes(trim($post_data['target'])) : '';
		$this->installation_level = (isset($post_data['install_level'])) ? stripslashes(trim($post_data['install_level'])) : '';
		$this->installation_time = (isset($post_data['install_time'])) ? intval(trim($post_data['install_time'])) : 0;
		$this->installation_time = (!empty($this->installation_time)) ? $this->installation_time * 60 : 0;
		$this->license = (isset($post_data['license'])) ? stripslashes(trim($post_data['license'])) : '';

		// Arrays
		$this->title = (isset($post_data['title'])) ? $this->modx_stripslashes($post_data['title']) : array();
		$this->title = $this->int_keys($this->title);

		$this->description = (isset($post_data['desc'])) ? $this->int_keys($this->modx_stripslashes($post_data['desc'])) : array();
		$this->description = $this->int_keys($this->description);

		$this->author_notes = (isset($post_data['notes'])) ? $this->modx_stripslashes($post_data['notes']) : array();
		$this->author_notes = $this->int_keys($this->author_notes);

		$this->diy = (isset($post_data['diy'])) ? $this->modx_stripslashes($post_data['diy']) : array();
		$this->diy = $this->int_keys($this->diy);

		$this->copy = (isset($post_data['copy'])) ? $this->modx_stripslashes($post_data['copy']) : array();
		$this->copy = $this->int_keys($this->copy);

		$tmp_arr = (isset($post_data['meta'])) ? $this->modx_stripslashes($post_data['meta']) : array();
		foreach ($tmp_arr as $value)
		{
			// We don't want to double the meta tag from this app.
			if (isset($value['content']) && $value['content'] != META)
			{
				$this->meta[] = $value;
			}
		}


		$tmp_arr = (isset($post_data['modx'])) ? $this->modx_stripslashes($post_data['modx']) : array();
		foreach ($tmp_arr as $value)
		{
			foreach ($value as $key2 => $value2)
			{
				if ($key2 == 'file')
				{
					$this->cnt_open++;
					$this->cnt_edit = -1;
					$this->action[$this->cnt_open]['file'] = $value2;
				}
				else
				{
					$this->cnt_edit++;
					$this->cnt_action = -1;
					foreach ($value2 as $value3)
					{
						$this->cnt_action++;
						$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action] = array(
							'type' => (isset($value3['type'])) ? $value3['type'] : '',
							'data' => (isset($value3['data'])) ? $value3['data'] : '',
						);
					}
				}
			}
		}

		$this->author = (isset($post_data['author'])) ? $this->modx_stripslashes($post_data['author']) : array();
		$this->author = $this->int_keys($this->author);

		$this->link = (isset($post_data['links'])) ? $this->modx_stripslashes($post_data['links']) : array();
		$this->link = $this->int_keys($this->link);

		$this->history = (isset($post_data['history'])) ? $this->modx_stripslashes($post_data['history']) : array();
		$this->history = $this->int_keys($this->history);
		if (isset($post_data['reverse_history']))
		{
			$this->history = array_reverse($this->history, true);
		}

		$this->sql = (isset($post_data['sql'])) ? $this->modx_stripslashes($post_data['sql']) : array();
		$this->sql = $this->int_keys($this->sql);

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

	// private functions.

	/**
	 * int_keys
	 *
	 * Converts array keys to numeric.
	 * The output class needs the
	 */
	private function int_keys($in_arr)
	{
		$out_arr = array();
		foreach ($in_arr as $row)
		{
			$out_arr[] = $row;
		}
		return($out_arr);
	}

	/**
	* modx_stripslashes()
	*
	* A stripslashes that handles arrays. \r also gets removed.
	* @param array $in_arr, the array with arrays that needs to be stripped
	* @return array, the stripped array
	*/
	private function modx_stripslashes(&$array)
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
				$this->modx_stripslashes($array[$key]);
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

}