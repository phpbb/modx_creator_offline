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

include('./parser_outdata.php');

/**
 * A class to parse old mod template files.
 * Its not pretty, but will not need to be updated either.
 * I need this to easier upgrade the other parts of the creator.
 */

class mod_parser extends parser_outdata
{
	// Switches only used while parsing.
	protected $in_action = false;
	protected $in_author = false;
	protected $in_copy = false;
	protected $in_edit = false;
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
	protected $mod_arr = array();

	public function mod_parser($modx_data)
	{
		// Let's split this to lines for easier parsing.
		$mod_arr = explode("\n", $modx_data);
		unset($modx_data);

		// Need to start these differently in here.
		$this->cnt_action = -1;
		$this->cnt_edit = -1;
		$this->cnt_open = -1;


		// Think it's faster too loop trough the array twice and get the anything except the action first.
		$cnt = 0;
		foreach ($mod_arr as $key => $data)
		{
			// Check if it's only a separator.
			if (substr($data, 0, 4) == '####' || trim($data) == '#')
			{
				unset($mod_arr[$key]);
				continue;
			}

			// First the ones that only supposed to occur once in the MOD file.
			if(substr($data, 0, 2) == '##')
			{
				if(substr($data, 0, 3) == '###')
				{
					continue;
				}
				else
				{
					$field_id = 'p_' . $cnt++;
					switch($data)
					{
						case (strpos(substr($data, 0, 13), 'MOD Title') !== false && empty($this->title)):
							$this->title[0]['data'] = trim(preg_replace(array('/##/', '/MOD/', '/Title/', '/:/'), '', $data, 1));
							$this->title[0]['lang'] = 'en';
						break;

						case (strpos(substr($data, 0, 14), 'MOD Author') !== false):
							$tmp_str = preg_replace(array('/##/', '/MOD/', '/Author/', '/:/'), '', $data, 1);
							// The author string also contains some other info. Lets remove that.
							$this->author[0]['username'] = trim(strtok($tmp_str, '<'));
						break;

						case (strpos(substr($data, 0, 20), 'MOD Description') !== false && empty($this->description)):
							$this->description[0]['lang'] = 'en';
							$this->description[0]['data'] = trim(preg_replace(array('/##/', '/MOD/', '/Description/', '/:/'), '', $data, 1));

							// The description
							$ccnt = $key + 1;
							$tmp_str = $mod_arr[$ccnt++];
							$tmp_str = trim($tmp_str, '#');
							$tmp_str = trim($tmp_str);
							while($tmp_str != '' && strpos(substr($tmp_str, 0, 7), 'MOD') === false && strpos(substr($tmp_str, 0, 11), 'Install') === false)
							{
								$this->description[0]['data'] .= $tmp_str;
								$tmp_str = $mod_arr[$ccnt++];
								$tmp_str = trim($tmp_str, '#');
								$tmp_str = trim($tmp_str);
							}
						break;

						case (strpos(substr($data, 0, 15), 'MOD Version') !== false && empty($this->mod_version)):
							$this->mod_version = trim(preg_replace(array('/##/', '/MOD/', '/Version/', '/:/'), '', $data, 1));
						break;

						case (strpos(substr($data, 0, 22), 'Installation Level') !== false && empty($this->installation_level)):
							$this->installation_level = strtolower(trim(preg_replace(array('/##/', '/Installation/', '/Level/', '/:/'), '', $data, 1)));
						break;

						case (strpos(substr($data, 0, 22), 'Installation Time') !== false && empty($this->installation_time)):
							$this->installation_time = intval(preg_replace(array('/##/', '/Installation/', '/Time/', '/:/'), '', $data, 1));
						break;

						case (strpos(substr($data, 0, 18), 'Compatibility') !== false && empty($this->target_version)):
							$this->target_version = trim(preg_replace(array('/##/', '/Compatibility/', '/:/'), '', $data, 1));
						break;

						case (strpos(substr($data, 0, 19), 'phpBB Version') !== false && empty($this->target_version)):
							$this->target_version = trim(preg_replace(array('/##/', '/phpBB/', '/Version/', '/:/'), '', $data, 1));
						break;

						case (strpos(substr($data, 0, 12), 'License') !== false && empty($this->license)):
							$this->license = trim(preg_replace(array('/##/', '/License/', '/:/'), '', $data, 1));
						break;

						default:
							continue;
						break;
					}
				}
				unset($mod_arr[$key]);
			}
		}

		// a quick sort to get the keys in order and remove leftover rows...
		foreach($mod_arr as $data)
		{
			if(substr($data, 0, 2) != '##')
			{
				$tmp_arr[] = $data;
			}
		}
		unset($mod_arr);
		$mod_arr = $tmp_arr;
		unset($tmp_arr);

		// The damage part...
		foreach($mod_arr as $key => $data)
		{
			$field_id = 'a_' . $cnt++;
			if(substr($data, 0, 1) == '#' && strpos($data, '#--') !== false)
			{
				$inline = false;
				$check_str = strtolower($data);
				if(strpos($check_str, 'sql') !== false)
				{
					$this->in_action = false;
					// SQL
					$this->sql[$this->cnt_sql]['dbms'] = '';
					$this->sql[$this->cnt_sql]['data'] = '';

					$ccnt = $key + 1;
					while(isset($mod_arr[$ccnt]) && substr($mod_arr[$ccnt], 0, 1) != '#')
					{
						if(trim($mod_arr[$ccnt]) == '')
						{
							$this->sql[$this->cnt_sql]['data'] = trim($this->sql[$this->cnt_sql]['data']);
							$this->cnt_sql++;
							$this->sql[$this->cnt_sql]['data'] = '';
							$this->sql[$this->cnt_sql]['dbms'] = '';
						}
						else
						{
							$this->sql[$this->cnt_sql]['data'] .= $mod_arr[$ccnt] . "\n";
						}
						$ccnt++;
					}
					$this->sql[$this->cnt_sql]['data'] = trim($this->sql[$this->cnt_sql]['data']);
					$this->cnt_sql++;
				}
				else if(strpos($check_str, 'copy') !== false)
				{
					$this->in_action = false;
					// copy
					$ccnt = $key + 1;
					while(isset($mod_arr[$ccnt]) && substr($mod_arr[$ccnt], 0, 1) != '#')
					{
						if(trim($mod_arr[$ccnt]) != '')
						{
							$tmp_str = trim($mod_arr[$ccnt]);
							$dummy = strtok($tmp_str, ' ');
							$this->copy[$this->cnt_copy]['from'] = trim(strtok(' '));
							$dummy = strtok(' ');
							$this->copy[$this->cnt_copy]['to'] = trim(strtok(' '));
							$this->cnt_copy++;
						}
						$ccnt++;
					}
				}
				else if(strpos($check_str, 'diy') !== false)
				{
					$this->in_action = false;
					$this->diy[$this->cnt_diy]['lang'] = 'en';
					$this->diy[$this->cnt_diy]['data'] = '';

					$ccnt = $key + 1;
					while(isset($mod_arr[$ccnt]) && substr($mod_arr[$ccnt], 0, 1) != '#')
					{
						$this->diy[$this->cnt_diy]['data'] .= $mod_arr[$ccnt] . "\n";
						$ccnt++;
					}
					$this->diy[$this->cnt_diy]['data'] = rtrim($this->diy[$this->cnt_diy]['data']);
				}
				else if(strpos($check_str, 'open') !== false)
				{
					$this->in_action = $this->in_edit = false;
					$action_done = true;
					$ccnt = $key + 1;
					while(isset($mod_arr[$ccnt]) && substr($mod_arr[$ccnt], 0, 1) != '#')
					{
						$tmp_str = trim($mod_arr[$ccnt]);
						if($tmp_str != '')
						{
							$this->cnt_open++;
							$this->action[$this->cnt_open]['file'] = $tmp_str;
						}
						$ccnt++;
					}
				}
				// The rest of them also needs a open file so $file_id can't be empty
				else if((strpos($check_str, 'in-line find') !== false || strpos($check_str, 'inline find') !== false) && $this->in_edit && $this->cnt_open > -1 && $this->cnt_edit > -1)
				{
					$inline = true;
					$this->in_action = true;
					$this->cnt_action++;
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action]['type'] = 'inline-find';
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action]['data'] = '';
				}
				else if(strpos($check_str, 'find') !== false && $this->cnt_open > -1)
				{
					$this->in_action = true;
					if(!$action_done)
					{
						// This is just a additional find whithin the same edit.
						$this->cnt_action++;
						$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action]['type'] = 'find';
						$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action]['data'] = '';
					}
					else
					{
						// a new edit.
						$this->cnt_action++;
						$this->cnt_edit++;
						$action_done = false;
						$this->in_edit = true;
						$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action]['type'] = 'find';
						$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action]['data'] = '';
					}
				}
				else if((strpos($check_str, 'in-line after') !== false || strpos($check_str, 'inline after') !== false) && $this->cnt_open > -1 && $this->in_edit)
				{
					$inline = true;
					$action_done = true;
					$this->in_action = true;
					$this->cnt_action++;
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action]['type'] = 'inline-after-add';
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action]['data'] = '';
				}
				else if((strpos($check_str, 'in-line before') !== false || strpos($check_str, 'inline before') !== false) && $this->cnt_open > -1 && $this->in_edit)
				{
					$inline = true;
					$action_done = true;
					$this->in_action = true;
					$this->cnt_action++;
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action]['type'] = 'inline-before-add';
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action]['data'] = '';
				}
				else if((strpos($check_str, 'in-line replace') !== false || strpos($check_str, 'inline replace') !== false) && $this->cnt_open > -1 && $this->in_edit)
				{
					$inline = true;
					$action_done = true;
					$this->in_action = true;
					$this->cnt_action++;
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action]['type'] = 'inline-replace-with';
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action]['data'] = '';
				}
				else if((strpos($check_str, 'in-line operation') !== false || strpos($check_str, 'inline operation') !== false) && $this->cnt_open > -1 && $this->in_edit)
				{
					$inline = true;
					$action_done = true;
					$this->in_action = true;
					$this->cnt_action++;
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action]['type'] = 'inline-operation';
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action]['data'] = '';
				}
				else if((strpos($check_str, 'after') !== false || strpos($check_str, 'after') !== false) && $this->cnt_open > -1 && $this->in_edit)
				{
					$action_done = true;
					$this->in_action = true;
					$this->cnt_action++;
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action]['type'] = 'after-add';
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action]['data'] = '';
				}
				else if((strpos($check_str, 'before') !== false || strpos($check_str, 'before') !== false) && $this->cnt_open > -1 && $this->in_edit)
				{
					$action_done = true;
					$this->in_action = true;
					$this->cnt_action++;
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action]['type'] = 'before-add';
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action]['data'] = '';
				}
				else if((strpos($check_str, 'replace') !== false || strpos($check_str, 'replace') !== false) && $this->cnt_open > -1 && $this->in_edit)
				{
					$action_done = true;
					$this->in_action = true;
					$this->cnt_action++;
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action]['type'] = 'replace-with';
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action]['data'] = '';
				}
				else if((strpos($check_str, 'operation') !== false || strpos($check_str, 'operation') !== false) && $this->cnt_open > -1 && $this->in_edit)
				{
					$action_done = true;
					$this->in_action = true;
					$this->cnt_action++;
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action]['type'] = 'operation';
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action]['data'] = '';
				}
				else
				{
					$this->in_action = false;
				}
			}
			else if($this->in_action)
			{
				if ($inline)
				{
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action]['data'] .= trim($data);
				}
				else
				{
					$this->action[$this->cnt_open][$this->cnt_edit][$this->cnt_action]['data'] .= $data . "\n";
				}
			}
		}

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
}
