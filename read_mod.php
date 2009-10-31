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

// The old style MOD files are probably better line by line.
$mod_arr = explode("\n", $modx_data);

$version = $target = $install_level = $install_time = $license = $title = $desc = $notes = $diy = $copy = $modx = $author = $history = $sql = '';

// Think it's faster too loop trough the array twice and get the anything except the action first.
$cnt = 0;
foreach($mod_arr as $key => $modx_data)
{
	// Check if it's only a separator.
	if(substr($modx_data, 0, 4) == '####' || trim($modx_data) == '#')
	{
		unset($mod_arr[$key]);
		continue;
	}

	// First the ones that only supposed to occur once in the MOD file.
	if(substr($modx_data, 0, 2) == '##')
	{
		if(substr($modx_data, 0, 3) == '###')
		{
			continue;
		}
		else
		{
			$field_id = 'p_' . $cnt++;
			switch($modx_data)
			{
				case (strpos(substr($modx_data, 0, 13), 'MOD Title') !== false && $title == ''):
					$title[$field_id]['title'] = trim(preg_replace(array('/##/', '/MOD/', '/Title/', '/:/'), '', $modx_data, 1));
					$title[$field_id]['lang'] = 'en';
				break;

				case (strpos(substr($modx_data, 0, 14), 'MOD Author') !== false):
					$tmp_str = preg_replace(array('/##/', '/MOD/', '/Author/', '/:/'), '', $modx_data, 1);
					// The author string also contains some other info. Lets remove that.
					$author[$field_id]['username'] = trim(strtok($tmp_str, '<'));
				break;

				case (strpos(substr($modx_data, 0, 20), 'MOD Description') !== false && $desc == ''):
					$desc[$field_id]['lang'] = 'en';
					$desc[$field_id]['desc'] = trim(preg_replace(array('/##/', '/MOD/', '/Description/', '/:/'), '', $modx_data, 1));

					// The description
					$ccnt = $key + 1;
					$tmp_str = $mod_arr[$ccnt++];
					$tmp_str = trim($tmp_str, '#');
					$tmp_str = trim($tmp_str);
					while($tmp_str != '' && strpos(substr($tmp_str, 0, 7), 'MOD') === false && strpos(substr($tmp_str, 0, 11), 'Install') === false)
					{
						$desc[$field_id]['desc'] .= $tmp_str;
						$tmp_str = $mod_arr[$ccnt++];
						$tmp_str = trim($tmp_str, '#');
						$tmp_str = trim($tmp_str);
					}
				break;

				case (strpos(substr($modx_data, 0, 15), 'MOD Version') !== false && $version == ''):
					$version = trim(preg_replace(array('/##/', '/MOD/', '/Version/', '/:/'), '', $modx_data, 1));
				break;

				case (strpos(substr($modx_data, 0, 22), 'Installation Level') !== false && $install_level == ''):
					$install_level = strtolower(trim(preg_replace(array('/##/', '/Installation/', '/Level/', '/:/'), '', $modx_data, 1)));
				break;

				case (strpos(substr($modx_data, 0, 22), 'Installation Time') !== false && $install_time == ''):
					$install_time = intval(preg_replace(array('/##/', '/Installation/', '/Time/', '/:/'), '', $modx_data, 1));
				break;

				case (strpos(substr($modx_data, 0, 18), 'Compatibility') !== false && $target == ''):
					$target = trim(preg_replace(array('/##/', '/Compatibility/', '/:/'), '', $modx_data, 1));
				break;

				case (strpos(substr($modx_data, 0, 19), 'phpBB Version') !== false && $target == ''):
					$target = trim(preg_replace(array('/##/', '/phpBB/', '/Version/', '/:/'), '', $modx_data, 1));
				break;

				case (strpos(substr($modx_data, 0, 12), 'License') !== false && $license == ''):
					$license = trim(preg_replace(array('/##/', '/License/', '/:/'), '', $modx_data, 1));
				break;

				default:
					// nothing
				break;
			}
		}
		unset($mod_arr[$key]);
	}
}

// a quick sort to get the keys in order and remove leftover rows...
foreach($mod_arr as $modx_data)
{
	if(substr($modx_data, 0, 2) != '##')
	{
		$tmp_arr[] = $modx_data;
	}
}
unset($mod_arr);
$mod_arr = $tmp_arr;
unset($tmp_arr);

$file_cnt = $edit_cnt = $dl_cnt = $sql_cnt = $copy_cnt = $diy_cnt = 0;
$file_id = $edit_id = $dl_id = '';
$in_edit = $action_done = $in_action = false;
foreach($mod_arr as $key => $modx_data)
{
	$field_id = 'a_' . $cnt++;
	if(substr($modx_data, 0, 1) == '#' && strpos($modx_data, '#--') !== false)
	{
		$check_str = strtolower($modx_data);
		if(strpos($check_str, 'sql') !== false)
		{
			$in_action = false;
			// SQL
			$sql_id = 'sql_' . $sql_cnt++;
			$sql[$sql_id]['dbms'] = '';
			$sql[$sql_id]['query'] = '';

			$ccnt = $key + 1;
			while(isset($mod_arr[$ccnt]) && substr($mod_arr[$ccnt], 0, 1) != '#')
			{
				if(trim($mod_arr[$ccnt]) == '')
				{
					$sql[$sql_id]['query'] = rtrim($sql[$sql_id]['query']);
					$sql_id = 'sql_' . $sql_cnt++;
					$sql[$sql_id]['dbms'] = '';
					$sql[$sql_id]['query'] = '';
				}
				else
				{
					$sql[$sql_id]['query'] .= $mod_arr[$ccnt] . "\n";
				}
				$ccnt++;
			}
			$sql[$sql_id]['query'] = rtrim($sql[$sql_id]['query']);
		}
		else if(strpos($check_str, 'copy') !== false)
		{
			$in_action = false;
			// copy
			$copy_id = 'copy_' . $copy_cnt++;
			$ccnt = $key + 1;
			$i = 0;
			while(isset($mod_arr[$ccnt]) && substr($mod_arr[$ccnt], 0, 1) != '#')
			{
				if(trim($mod_arr[$ccnt]) != '')
				{
					$copy_field = 'cpy_' . $i++;
					$tmp_str = trim($mod_arr[$ccnt]);
					$dummy = strtok($tmp_str, ' ');
					$copy[$copy_id][$copy_field]['from'] = trim(strtok(' '));
					$dummy = strtok(' ');
					$copy[$copy_id][$copy_field]['to'] = trim(strtok(' '));
				}
				$ccnt++;
			}
		}
		else if(strpos($check_str, 'diy') !== false)
		{
			$in_action = false;
			$diy_id = 'diy_' . $diy_cnt++;
			$diy[$diy_id]['lang'] = 'en';
			$diy[$diy_id]['diy'] = '';

			$ccnt = $key + 1;
			while(isset($mod_arr[$ccnt]) && substr($mod_arr[$ccnt], 0, 1) != '#')
			{
				$diy[$diy_id]['diy'] .= $mod_arr[$ccnt] . "\n";
				$ccnt++;
			}
			$diy[$diy_id]['diy'] = rtrim($diy[$diy_id]['diy']);
		}
		else if(strpos($check_str, 'open') !== false)
		{
			$in_edit = $in_action = false;
			$action_done = true;
			$file_id = 'file_' . $file_cnt++;
			$ccnt = $key + 1;
			while(isset($mod_arr[$ccnt]) && substr($mod_arr[$ccnt], 0, 1) != '#')
			{
				$tmp_str = trim($mod_arr[$ccnt]);
				if($tmp_str != '')
				{
					$modx[$file_id]['file'] = $tmp_str;
				}
				$ccnt++;
			}
		}
		// The rest of them also needs a open file so $file_id can't be empty
		else if((strpos($check_str, 'in-line find') !== false || strpos($check_str, 'inline find') !== false) && $file_id != '' && $in_edit)
		{
			$in_action = true;
			$dl_id = 'dl_' . $dl_cnt++;
			$modx[$file_id][$edit_id][$dl_id]['type'] = 'inline-find';
			$modx[$file_id][$edit_id][$dl_id]['data'] = '';
		}
		else if(strpos($check_str, 'find') !== false && $file_id != '')
		{
			$in_action = true;
			if(!$action_done)
			{
				// This is just a additional find whithin the same edit.
				$dl_id = 'dl_' . $dl_cnt++;
				$modx[$file_id][$edit_id][$dl_id]['type'] = 'find';
				$modx[$file_id][$edit_id][$dl_id]['data'] = '';
			}
			else
			{
				// a new edit.
				$action_done = false;
				$in_edit = true;
				$edit_id = 'edit_' . $edit_cnt++;
				$dl_id = 'dl_' . $dl_cnt++;
				$modx[$file_id][$edit_id][$dl_id]['type'] = 'find';
				$modx[$file_id][$edit_id][$dl_id]['data'] = '';
			}
		}
		else if((strpos($check_str, 'in-line after') !== false || strpos($check_str, 'inline after') !== false) && $file_id != '' && $in_edit)
		{
			$action_done = true;
			$in_action = true;
			$dl_id = 'dl_' . $dl_cnt++;
			$modx[$file_id][$edit_id][$dl_id]['type'] = 'inline-after-add';
			$modx[$file_id][$edit_id][$dl_id]['data'] = '';
		}
		else if((strpos($check_str, 'in-line before') !== false || strpos($check_str, 'inline before') !== false) && $file_id != '' && $in_edit)
		{
			$action_done = true;
			$in_action = true;
			$dl_id = 'dl_' . $dl_cnt++;
			$modx[$file_id][$edit_id][$dl_id]['type'] = 'inline-before-add';
			$modx[$file_id][$edit_id][$dl_id]['data'] = '';
		}
		else if((strpos($check_str, 'in-line replace') !== false || strpos($check_str, 'inline replace') !== false) && $file_id != '' && $in_edit)
		{
			$action_done = true;
			$in_action = true;
			$dl_id = 'dl_' . $dl_cnt++;
			$modx[$file_id][$edit_id][$dl_id]['type'] = 'inline-replace-with';
			$modx[$file_id][$edit_id][$dl_id]['data'] = '';
		}
		else if((strpos($check_str, 'in-line operation') !== false || strpos($check_str, 'inline operation') !== false) && $file_id != '' && $in_edit)
		{
			$action_done = true;
			$in_action = true;
			$dl_id = 'dl_' . $dl_cnt++;
			$modx[$file_id][$edit_id][$dl_id]['type'] = 'inline-operation';
			$modx[$file_id][$edit_id][$dl_id]['data'] = '';
		}
		else if((strpos($check_str, 'after') !== false || strpos($check_str, 'after') !== false) && $file_id != '' && $in_edit)
		{
			$action_done = true;
			$in_action = true;
			$dl_id = 'dl_' . $dl_cnt++;
			$modx[$file_id][$edit_id][$dl_id]['type'] = 'after-add';
			$modx[$file_id][$edit_id][$dl_id]['data'] = '';
		}
		else if((strpos($check_str, 'before') !== false || strpos($check_str, 'before') !== false) && $file_id != '' && $in_edit)
		{
			$action_done = true;
			$in_action = true;
			$dl_id = 'dl_' . $dl_cnt++;
			$modx[$file_id][$edit_id][$dl_id]['type'] = 'before-add';
			$modx[$file_id][$edit_id][$dl_id]['data'] = '';
		}
		else if((strpos($check_str, 'replace') !== false || strpos($check_str, 'replace') !== false) && $file_id != '' && $in_edit)
		{
			$action_done = true;
			$in_action = true;
			$dl_id = 'dl_' . $dl_cnt++;
			$modx[$file_id][$edit_id][$dl_id]['type'] = 'replace-with';
			$modx[$file_id][$edit_id][$dl_id]['data'] = '';
		}
		else if((strpos($check_str, 'operation') !== false || strpos($check_str, 'operation') !== false) && $file_id != '' && $in_edit)
		{
			$action_done = true;
			$in_action = true;
			$dl_id = 'dl_' . $dl_cnt++;
			$modx[$file_id][$edit_id][$dl_id]['type'] = 'operation';
			$modx[$file_id][$edit_id][$dl_id]['data'] = '';
		}
		else
		{
			$in_action = false;
		}
	}
	else if($in_action)
	{
		$modx[$file_id][$edit_id][$dl_id]['data'] .= $modx_data . "\n";
	}
}
