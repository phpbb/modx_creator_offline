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

// First we need to know the MODX version for updating.
$modx_version = modx_version(get_attribute($modx_data, 'xmlns'));
if(!$modx_version)
{
	$warning['modx_version'] = 'modx_version';
}
// Then we get the one-liners that's suposed to "occur exactly once"
$license = get_tag($modx_data, 'license', false);
$version = get_tag($modx_data, 'mod-version', false);
if($modx_version == MODX_V1)
{
	// The old style version number for the MOD
	$tmp_arr['major'] = intval(get_tag($version, 'major', false));
	$tmp_arr['minor'] = intval(get_tag($version, 'minor', false));
	$tmp_arr['revision'] = intval(get_tag($version, 'revision', false));
	$tmp_arr['release'] = get_tag($version, 'release', false);
	$version = $tmp_arr['major'] . '.' . $tmp_arr['minor'] . '.' . $tmp_arr['revision'] . $tmp_arr['release'];
	unset($tmp_arr);
}

$target = get_tag($modx_data, 'target-version', false);
if($modx_version == MODX_V1)
{
	// The old style phpBB target version
	$tmp_arr['major'] = intval(get_tag($target, 'target-major', false));
	$tmp_arr['minor'] = intval(get_tag($target, 'target-minor', false));
	$tmp_arr['revision'] = intval(get_tag($target, 'target-revision', false));
	$target = $tmp_arr['major'] . '.' . $tmp_arr['minor'] . '.' . $tmp_arr['revision'];
	unset($tmp_arr);
}

$install_level = get_tag($modx_data, 'level', false);
$install_time = (int) (get_tag($modx_data, 'time', false) / 60);

// Then we get the single arrays, and their attributes...
$title = get_single_arr($modx_data, 'title', 'title');
$desc = get_single_arr($modx_data, 'description', 'desc');
$notes = get_single_arr($modx_data, 'author-notes', 'note');
$diy = get_single_arr($modx_data, 'diy-instructions', 'diy');

// Let's get the rest of the arrays and sort them temporarilly in arrays. so we can free $modx_data.
$author = get_tag($modx_data, 'author-group', false);
$links = get_tag($modx_data, 'link-group', false);
$history = get_tag($modx_data, 'history', false);

$modx = get_tag($modx_data, 'action-group', false);

// Let's save some memory
unset($modx_data);

$sql = get_tag($modx, 'sql', true, true);
$copy = get_tag($modx, 'copy');

// We'll need $modx later.
$modx_data = get_tag($modx, 'open', true, true);
unset($modx);

$tmp_arr = get_tag($author, 'author');
unset($author);
$cnt = 0;
foreach($tmp_arr as $value)
{
	$field_id = 'af_in' . $cnt++;
	$author[$field_id]['username'] = get_tag($value, 'username', false);
	$author[$field_id]['realname'] = get_tag($value, 'realname', false);
	$author[$field_id]['homepage'] = get_tag($value, 'homepage', false);
	$author[$field_id]['email'] = get_tag($value, 'email', false);
	$arr2 = get_empty_tag($value, 'contributions');
	$ccnt = 0;
	foreach($arr2 as $cval)
	{
		$contributor[$field_id][$ccnt]['status'] = get_attribute($cval, 'status');
		$contributor[$field_id][$ccnt]['position'] = get_attribute($cval, 'position');
		$contributor[$field_id][$ccnt]['from'] = get_attribute($cval, 'from');
		$contributor[$field_id][$ccnt]['to'] = get_attribute($cval, 'to');
		$ccnt++;
	}
}
unset($tmp_arr, $arr2);

$tmp_arr = get_tag($links, 'link', true, true);
unset($links);
$cnt = 0;
foreach($tmp_arr as $value)
{
	$field_id = 'lf_in' . $cnt++;
	$links[$field_id]['type'] = get_attribute($value, 'type');
	$links[$field_id]['href'] = get_attribute($value, 'href');
	$links[$field_id]['lang'] = get_attribute($value, 'lang');
	$links[$field_id]['title'] = get_tag($value, 'link', false);
}
unset($tmp_arr);

$tmp_arr = get_tag($history, 'entry');
unset($history);
$cnt = 0;
foreach($tmp_arr as $value)
{
	$field_id = 'hf_in' . $cnt++;
	$history[$field_id]['date'] = get_tag($value, 'date', false);
	$history[$field_id]['version'] = get_tag($value, 'rev-version', false);
	if($modx_version == MODX_V1)
	{
		// We need to get the old style change version
		$arr2['major'] = intval(get_tag($history[$field_id]['version'], 'major', false));
		$arr2['minor'] = intval(get_tag($history[$field_id]['version'], 'minor', false));
		$arr2['revision'] = intval(get_tag($history[$field_id]['version'], 'revision', false));
		$arr2['release'] = get_tag($history[$field_id]['version'], 'release', false);
		$history[$field_id]['version'] = $arr2['major'] . '.' . $arr2['minor'] . '.' . $arr2['revision'] . $arr2['release'];
		unset($arr2);
	}
	$arr2 = get_tag($value, 'changelog', true, true);
	$ccnt = 0;
	foreach($arr2 as $cval)
	{
		$cval_lang = get_attribute($cval, 'lang', 2);
		$arr3 = get_tag($cval, 'change', true, false);
		foreach($arr3 as $cval2)
		{
			$history[$field_id]['change'][$ccnt]['lang'] = $cval_lang;
			$history[$field_id]['change'][$ccnt]['data'] = $cval2;
			$ccnt++;
		}
	}
}
unset($tmp_arr, $arr2);

$tmp_arr = $sql;
unset($sql);
foreach($tmp_arr as $value)
{
	$arr2 = get_single_arr($value, 'sql', 'query', 'dbms');
	$arr2['in_0']['dbms'] = ($arr2['in_0']['dbms'] == '') ? 'sql-parser' : $arr2['in_0']['dbms'];
	trim_cdata($arr2['in_0']['query']);
	$sql[] = $arr2['in_0'];
}
unset($tmp_arr, $arr2);

$tmp_arr = $copy;
unset($copy);
$cnt = 0;
foreach($tmp_arr as $value)
{
	$field_id = 'cf_in' . $cnt++;
	$arr2 = get_empty_tag($value, 'file');
	$ccnt = 0;
	foreach($arr2 as $cval)
	{
		$copy[$field_id][$ccnt]['from'] = get_attribute($cval, 'from');
		$copy[$field_id][$ccnt]['to'] = get_attribute($cval, 'to');
		$ccnt++;
	}
}
unset($tmp_arr, $arr2);

// $modx_data is already split into files.
$cnt = 0;
foreach($modx_data as $value)
{
	$file_id = 'file_' . $cnt++;
	$modx[$file_id]['file'] = get_attribute($value, 'src');
	$tmp_arr = get_tag($value, 'edit');
	$edit_cnt = 0;
	foreach($tmp_arr as $cval)
	{
		$edit_id = 'edit_' . $edit_cnt++;
		$dl_id = 0;
		// First we'll check for comments...
		$arr2 = get_tag($cval, 'comment', true, true);
		if(!empty($arr2))
		{
			foreach($arr2 as $cval2)
			{
				// modx[' . $file_id . '][' . $edit_id . '][' . $dl_id . ']
				$arr3 = get_single_arr($cval2, 'comment', 'data');
				trim_cdata($arr3['in_0']['data']);
				$modx[$file_id][$edit_id][$dl_id++] = array_merge($arr3['in_0'], array('type' => 'comment'));
			}
		}
		unset($arr2, $arr3);

		// Then finds...
		$arr2 = get_tag($cval, 'find', true);
		if(!empty($arr2))
		{
			foreach($arr2 as $cval2)
			{
				// modx[' . $file_id . '][' . $edit_id . '][' . $dl_id . ']
				$modx[$file_id][$edit_id][$dl_id]['type'] = 'find';
				$modx[$file_id][$edit_id][$dl_id++]['data'] = trim_cdata($cval2);
			}
		}
		unset($arr2, $arr3);

		// Then actions...
		$arr2 = get_tag($cval, 'action', true, true);
		if(!empty($arr2))
		{
			foreach($arr2 as $cval2)
			{
				// modx[' . $file_id . '][' . $edit_id . '][' . $dl_id . ']
				$dd_id = $dl_id;
				$modx[$file_id][$edit_id][$dl_id]['type'] = get_attribute($cval2, 'type');
				$modx[$file_id][$edit_id][$dl_id++]['data'] = trim_cdata(get_tag($cval2, 'action', false));
			}
		}
		unset($arr2, $arr3);

		// Any inline stuff?
		$arr2 = get_tag($cval, 'inline-edit', true, true);
		if(!empty($arr2))
		{
			foreach($arr2 as $cval2)
			{
				// Ok, lets start with the inline-finds, we don't have to bother with inline-comments... Yet.
				$arr3 = get_tag($cval2, 'inline-find', true);
				if(!empty($arr3))
				{
					foreach($arr3 as $cval3)
					{
						$dd_id = $dl_id;
						$modx[$file_id][$edit_id][$dl_id]['type'] = 'inline-find';
						$modx[$file_id][$edit_id][$dl_id++]['data'] = trim_cdata($cval3);
					}
				}
				unset($arr3);

				$arr3 = get_tag($cval2, 'inline-action', true, true);
				if(!empty($arr3))
				{
					foreach($arr3 as $cval3)
					{
						// modx[' . $file_id . '][' . $edit_id . '][' . $dl_id . ']
						$dd_id = $dl_id;
						$modx[$file_id][$edit_id][$dl_id]['type'] = get_attribute($cval3, 'type');
						$modx[$file_id][$edit_id][$dl_id]['type'] = 'inline-' . $modx[$file_id][$edit_id][$dl_id]['type'];
						$modx[$file_id][$edit_id][$dl_id++]['data'] = trim_cdata(get_tag($cval3, 'inline-action', false));
					}
				}
				unset($arr3);
			}
		}
		unset($arr2, $arr3);
	}
}
