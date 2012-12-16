<?php
/**
*
* @package MODX creator
* @version $Id$
* @copyright (c) 2009 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
define('IN_MODX', true);

include('./constants.php');
include('./functions.php');
include('./template.php');

// The get the post vars
$preview = (isset($_POST['preview'])) ? true : false;
$dload = (isset($_POST['dload'])) ? true : false;
$submit_file = (isset($_POST['submit-file']) && $_FILES['upload-file']['size']) ? true : false;

define('STRIP', (get_magic_quotes_gpc()) ? true : false);

$submit = ($preview || $dload) ? true : false;

$template = new template();
$template->set_custom_template('.', 'default');

$modx_data = '';
// If submit-file is clicked we'll check if we have a file.
if ($submit_file && !$submit)
{
	// Did we get a file?
	if ($_FILES['upload-file']['size'] > 0)
	{
		// Lets start with the extension...
		$extension = substr(strrchr($_FILES['upload-file']['name'], '.'), 1);
		$str = file_get_contents($_FILES['upload-file']['tmp_name'], 0, NULL, 0, 200);

		// We'll need to know what kind of file it is
		$submit_file = get_mod_type($str, $extension);

		if ($submit_file)
		{
			// Let's get the rest of the file.
			$modx_data = file_get_contents($_FILES['upload-file']['tmp_name']);
			// Trim Win and MAC eol's
			$modx_data = str_replace(chr(13), '', $modx_data);
		}
	}
}

if ($submit_file && $modx_data != '' && !$submit)
{
	if ($submit_file == MODX)
	{
		include('./modx_parser.php');
		$parser = new modx_parser($modx_data);
	}
	else if ($submit_file == MOD)
	{
		include('./mod_parser.php');
		$parser = new mod_parser($modx_data);
	}
	else
	{
		$submit_file = false;
	}
}

if (!isset($parser))
{
	include('./post_parser.php');
	$parser = new post_parser($_POST);
}

$install_level = $parser->get_modx_installation_level(true);
$install_level = (empty($install_level)) ? 'easy' : $install_level;

$install_time = $parser->get_modx_installation_time(false);
$install_time = (empty($install_time)) ? 0 : $install_time;

$license = $parser->get_modx_license();
$license = (empty($license)) ? 'http://opensource.org/licenses/gpl-license.php GNU General Public License v2' : $license;

$version = $parser->get_modx_mod_version();
$target = $parser->get_modx_target_version();

$php_installer = $parser->get_modx_php_installer();

$u_github = $parser->get_modx_github();

// Check the vars that are not cheched later.
if ($submit)
{
	// Check that it's a valid version number
	// I'll ad better errors later.
	if ($version == '')
	{
		$error['version'] = 'version';
	}
	else if (!preg_match('#(\d+)\.(\d+)\.\d+[a-z]?#', $version))
	{
		$error['version'] = 'version';
	}
	if ($target == '')
	{
		$error['target'] = 'target';
	}
	else if (!preg_match('#(\d+)\.(\d+)\.\d+[a-z]?#', $target))
	{
		$error['target'] = 'target';
	}
	else if ($target != PHPBB_LATEST)
	{
		$warning['target'] = 'target';
	}

	if ($install_level == '')
	{
		$error['install_level'] = 'install_level';
	}
	if ($install_time == 0)
	{
		$error['install_time'] = 'install_time';
	}

	if (strpos($u_github, 'https://github.com/') !== 0)
	{
		$error['github'] = true;
	}
}

// MOD title
$cnt = 0;
while ($title = $parser->get_modx_title())
{
	$title['title'] = gen_value($title['title'], true);
	if ($title['title'] != '')
	{
		$field_id = 'title_pre_' . $cnt++;
		$template->assign_block_vars('title_row', array(
			'FIELD_ID' => $field_id,
			'VALUE' => $title['title'],
			'LANG' => lang_select($title['lang']),
		));
	}
}

if (!$cnt && $submit)
{
	$error['title'] = true;
}

// MOD description
$cnt = 0;
while ($desc = $parser->get_modx_description())
{
	$desc['desc'] = gen_value($desc['desc'], true);
	if ($desc['desc'] != '')
	{
		$field_id = 'desc_pre_' . $cnt++;
		$template->assign_block_vars('desc_row', array(
			'S_FIRST_ROW' => ($cnt == 1) ? true : false,
			'FIELD_ID' => $field_id,
			'VALUE' => $desc['desc'],
			'LANG' => lang_select($desc['lang']),
			'ROWS' => count_rows($desc['desc'], 73),
		));
	}
}

if (!$cnt && $submit)
{
	$error['desc'] = true;
}

// Author notes
$cnt = 0;
while ($note = $parser->get_modx_notes())
{
	$note['note'] = gen_value($note['note'], true);
	if ($note['note'] != '')
	{
		$field_id = 'note_pre_' . $cnt++;
		$template->assign_block_vars('notes_row', array(
			'S_FIRST_ROW' => ($cnt == 1) ? true : false,
			'FIELD_ID' => $field_id,
			'VALUE' => $note['note'],
			'LANG' => lang_select($note['lang']),
			'ROWS' => count_rows($note['note'], 73),
		));
	}
}

// Author fields...
$cnt = 0;
while ($author = $parser->get_modx_authors())
{
	$field_id = 'af_pre_' . $cnt++;
	$template->assign_block_vars('author_row', array(
		'FIELD_ID' => $field_id,
		'S_PHPBB_COM' => (!empty($author['phpbbcom'])) ? true : false,
		'USERNAME' => gen_value($author['username']),
		'REALNAME' => (isset($author['realname'])) ? gen_value($author['realname']) : '',
		'HOMEPAGE' => (isset($author['homepage'])) ? gen_value($author['homepage']) : '',
		'EMAIL' => (isset($author['email'])) ? gen_value($author['email']) : '',
	));
	if (!empty($author['contributions']))
	{
		$ccnt = 0;
		foreach ($author['contributions'] as $cval)
		{
			if (!empty($cval['status']) || !empty($cval['position']) || !empty($cval['from']) || !empty($cval['to']))
			{
				$contrib_id = 'afc_pre_' . $ccnt++;
				$template->assign_block_vars('author_row.contrib', array(
					'CONTRIB_ID' => $contrib_id,
					'STATUS' => $cval['status'],
					'POSITION' => (!empty($cval['position'])) ? gen_value($cval['position']) : '',
					'FROM' => (!empty($cval['from'])) ? gen_value($cval['from']) : '',
					'TO' => (!empty($cval['to'])) ? gen_value($cval['to']) : '',
				));
			}
		}
	}
}

if (!$cnt && $submit)
{
	$error['author'] = true;
}

// History
$cnt = 0;
while ($history = $parser->get_modx_history())
{
	$field_id = 'hf_pre_' . $cnt++;
	$template->assign_block_vars('history_row', array(
		'FIELD_ID' => $field_id,
		'VERSION' => gen_value($history['version']),
		'DATE' => gen_value($history['date']),
	));

	if (!empty($history['changelog']))
	{
		$ccnt = 0;
		foreach ($history['changelog'] as $cval)
		{
			$s_warning_change = false;
			if (trim($cval['change']) == '')
			{
				$warning['change'] = true;
				$s_warning_change = true;
			}
			$change_id = 'hfc_pre_' . $ccnt++;
			$template->assign_block_vars('history_row.changelog', array(
				'CHANGE_ID' => $change_id,
				'CHANGE' => gen_value($cval['change'], true),
				'S_WARNING_CHANGE' => $s_warning_change,
				'LANG' => lang_select($cval['lang']),
			));
		}
	}
}

// Links
$cnt = 0;
while ($links = $parser->get_modx_links())
{
	if (trim($links['title']) != '' || trim($links['href']) != '' || trim($links['type']) != '' || trim($links['lang']) != '')
	{
		$field_id = 'lf_pre_' . $cnt++;
		$link_row = array(
			'FIELD_ID' => $field_id,
			'TYPE' => $links['type'],
			'TITLE' => gen_value($links['title'], true),
			'LANG' => lang_select($links['lang']),
			'HREF' => gen_value($links['href'], true),
			'REALNAME' => (!empty($links['realname'])) ? gen_value($links['realname'], true) : '',
		);

		$template->assign_block_vars('link_row', $link_row);
	}
}

// File copy
$s_is_copy = false;
$cnt = 0;
if ($parser->count_copy())
{
	$field_id = 'fc_pre';
	$s_is_copy = true;

	while ($copy = $parser->get_modx_copy())
	{
		if (trim($copy['from']) != '')
		{
			$file_id = 'fcc_pre_' . $cnt++;
			$template->assign_block_vars('copy_row', array(
				'FILE_ID' => $file_id,
				'FROM' => gen_value($copy['from']),
				'TO' => gen_value($copy['to']),
			));
		}
	}
}

// Delete
$s_is_delete = false;
$cnt = 0;
if ($parser->count_delete())
{
	$field_id = 'fc_pre';
	$s_is_delete = true;

	while ($delete = $parser->get_modx_delete())
	{
		if (trim($delete) != '')
		{
			$file_id = 'fcc_pre_' . $cnt++;
			$template->assign_block_vars('delete_row', array(
				'FILE_ID' => $file_id,
				'FILE' => gen_value($delete),
			));
		}
	}
}

// SQL querys
$cnt = 0;
while ($sql = $parser->get_modx_sql())
{
	if (trim($sql['query']) != '')
	{
		$field_id = 'sql_pre_' . $cnt++;
		$template->assign_block_vars('sql_row', array(
			'FIELD_ID' => $field_id,
			'DBMS' => (empty($sql['dbms'])) ? 'sql-parser' : $sql['dbms'],
			'ROWS' => count_rows($sql['query'], 95),
			'QUERY' => gen_value($sql['query']),
		));
	}
}

// meta tags
$cnt = 0;
while ($meta = $parser->get_modx_meta())
{
	if (trim($meta['content']) != '' && $meta['content'] != META)
	{
		$field_id = 'meta_' . $cnt++;
		$template->assign_block_vars('meta_row', array(
			'FIELD_ID' => $field_id,
			'NAME' => gen_value($meta['name']),
			'CONTENT' => gen_value($meta['content']),
		));
	}
}

// DIY fields
$cnt = 0;
while ($diy = $parser->get_modx_diy())
{
	if (trim($diy['diy']) != '')
	{
		$field_id = 'diy_pre_' . $cnt++;
		$template->assign_block_vars('diy_row', array(
			'S_FIRST_ROW' => ($cnt == 1) ? true : false,
			'FIELD_ID' => $field_id,
			'ROWS' => count_rows($diy['diy'], 88),
			'DIY' => gen_value($diy['diy'], true),
			'LANG' => lang_select($diy['lang']),
		));
	}
}

// The Action fields...
$modx_fields = '';
// Sorting a-z, within directories
$parser->sort_modx_action();
$file_cnt = $edit_cnt = $dl_cnt = $dt_cnt = $dd_cnt = 0;
while ($modx = $parser->get_modx_action())
{
	if (!empty($modx) && !empty($modx['file']))
	{
		// Array 1, files
		$file_id = 'f_pre_' . $file_cnt++;
		$dt_id = 'dt_pre_' . $dt_cnt++;
		$template->assign_block_vars('file_row', array(
			'FILE_ID' => $file_id,
			'DT_ID' => $dt_id,
			'FILE' => gen_value($modx['file'], true),
		));

		foreach ($modx as $key2 => $value2)
		{
			// Array 2, edits We dont need the filenames here
			if (is_int($key2) || $key2 != 'file')
			{
				$edit_id = 'e_pre_' . $edit_cnt++;
				$template->assign_block_vars('file_row.edit', array(
					'EDIT_ID' => $edit_id,
				));

				foreach ($value2 as $key3 => $value3)
				{
					// Array 3, dl's The string fields
					if($value3['type'] != '' && isset($value3['data']))
					{
						$dl_id = 'dl_pre_' . $dl_cnt++;
						$dt_id = 'dt_pre_' . $dt_cnt++;
						$dd_id = 'dd_pre_' . $dd_cnt++;
						// Need to get the right input field here to view the right input and image...
						// We'll do it here so we don't have to do the checks twice
						$modx_select = '';
						if(strpos($value3['type'], 'inline') !== FALSE)
						{
							$textarea = false;
							// Inline stuff can only be one-liners
							sanitize_inlines($value3['data']);
							$modx_input = '<span id="' . $dd_id . '_field"><textarea class="inputbox" id="' . $dd_id . '_data" name="modx[' . $file_id . '][' . $edit_id . '][' . $dl_id . '][data]" rows="1" onKeypress="if((event.keyCode == 10) || (event.keyCode == 13)){return false;}">' . gen_value($value3['data']) . '</textarea></span>';
							$modx_img = '<img id="' . $dd_id . '_info" class="action-image" src="./images/info.png" alt="Inline explain" title="Note that the inline tags may not contain line breaks" />';
						}
						else if($value3['type'] != 'comment')
						{
							$textarea = true;
							$modx_input = '<span id="' . $dd_id . '_field"><textarea class="inputbox right-tools" id="' . $dd_id . '_data" name="modx[' . $file_id . '][' . $edit_id . '][' . $dl_id . '][data]" rows="' . count_rows($value3['data'], 85) . '">' . gen_value($value3['data']) . '</textarea></span>';
							if($value3['type'] == 'find')
							{
								$modx_img = '<img id="' . $dd_id . '_info" class="action-image" src="./images/info.png" alt="Find explain" title="Find tags in the MODX file should be in the order that the find targets appear in the file. In other words, a processor of the MODX file should never need to go backwards in the file to locate all of the finds. When there are multiple finds within a single edit tag, the processor should handle all finds before any actions." />';
							}
							else if($value3['type'] == 'remove')
							{
								$modx_img = '<img id="' . $dd_id . '_info" class="action-image" src="./images/info.png" alt="Find and delete explain" title="Remove tags should either be alone in the edit tag or preceded by one find to be sure to delete the right code." />';
							}
							else
							{
								$modx_img = '<img id="' . $dd_id . '_info" class="action-image" src="./images/info.png" alt="Action explain" title="The string to add before the find, add after the find, replace the find with, or the operation string." />';
							}
						}
						else
						{
							$textarea = true;
							// The comments needs a lang select
							$modx_input = '<span id="' . $dd_id . '_field"><textarea class="inputbox right-tools" id="' . $dd_id . '_data" name="modx[' . $file_id . '][' . $edit_id . '][' . $dl_id . '][data]" rows="' . count_rows($value3['data'], 70) . '">' . gen_value($value3['data']) . '</textarea>';
							$modx_select = '<span id="' . $dd_id . '_lang"><select name="modx[' . $file_id . '][' . $edit_id . '][' . $dl_id . '][lang]">' . lang_select($value3['lang']) . '</select></span></span>';
							$modx_img = '<img id="' . $dd_id . '_info" class="action-image" src="./images/info.png" alt="Comment explain" title="Comment pertaining to this edit" />';
						}

						$template->assign_block_vars('file_row.edit.action', array(
							'DL_ID' => $dl_id,
							'DT_ID' => $dt_id,
							'DD_ID' => $dd_id,
							'S_TEXTAREA' => $textarea,
							'MODX_INPUT' => $modx_input,
							'MODX_IMG' => $modx_img,
							'MODX_SELECT' => $modx_select,
							'TYPE' => $value3['type'],
						));
					}
				}
			}
		}
	}
}

if (($dload || $preview) && empty($error))
{
	include('./create_modx.php');
}

$template->assign_vars(array(
	'GITHUB'	=> $u_github,

	'INSTALL_LEVEL'	=> (isset($install_level)) ? $install_level : '',
	'INSTALL_TIME'	=> (isset($install_time)) ? $install_time : '',

	'LANG_SELECT'	=> lang_select(),
	'LICENSE'		=> (!empty($license)) ? $license : 'http://opensource.org/licenses/gpl-license.php GNU General Public License v2',

	'MOD_VERSION'	=> (isset($version)) ? $version : '',

	'PHP_INSTALLER'	=> (!empty($php_installer)) ? $php_installer : '',
	'PHPBB_LATEST'	=> PHPBB_LATEST,

	'S_ERROR_TITLE'		=> (isset($error['title'])) ? true : false,
	'S_ERROR_DESC'		=> (isset($error['desc'])) ? true : false,
	'S_ERROR_VERSION'	=> (isset($error['version'])) ? true : false,
	'S_ERROR_TARGET'	=> (isset($error['target'])) ? true : false,
	'S_ERROR_INSTALL_LEVEL'	=> (isset($error['install_level'])) ? true : false,
	'S_ERROR_INSTALL_TIME'	=> (isset($error['install_time'])) ? true : false,
	'S_ERROR_AUTHOR'	=> (isset($error['author'])) ? true : false,
	'S_ERROR_GITHUB'	=> (isset($error['github'])) ? true : false,
	'S_ERRORS'			=> (($dload || $preview) && !empty($error)) ? true : false,

	'S_IS_COPY'		=> $s_is_copy,
	'S_IS_DELETE'	=> $s_is_delete,
	'S_IN_MODX_CREATOR'	=> true,
	'S_SUBMIT'		=> ($submit) ? true : false,

	'S_WARNING_TARGET'	=> (isset($warning['target'])) ? true : false,
	'S_WARNINGS'		=> (($dload || $preview) && !empty($warning)) ? true : false,

	'TARGET_VERSION'	=> (isset($target)) ? $target : '',
));

$template->set_filenames(array(
	'body' => 'modx_creator.html',
));

$template->display('body');
