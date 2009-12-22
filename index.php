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

// The get the post vars
$preview = (isset($_POST['preview'])) ? true : false;
$dload = (isset($_POST['dload'])) ? true : false;
$submit_file = (isset($_POST['submit-file']) && $_FILES['upload-file']['size']) ? true : false;

$strip = (get_magic_quotes_gpc()) ? true : false;

$submit = ($preview || $dload) ? true : false;

$modx_data = '';
// If submit-file is clickd we'll check if we have a file.
if($submit_file && !$submit)
{
	// Did we get a file?
	if($_FILES['upload-file']['size'] > 0)
	{
		// Lets start with the extension...
		$extension = strtolower(array_pop(explode('.', $_FILES['upload-file']['name'])));
		$str = file_get_contents($_FILES['upload-file']['tmp_name'], 0, NULL, 0, 20);

		// We'll need to know what kind of file it is
		$submit_file = get_mod_type($str, $extension);

		if($submit_file)
		{
			// Let's get the rest of the file.
			$modx_data = file_get_contents($_FILES['upload-file']['tmp_name']);
			// Trim Win and MAC eol's
			$modx_data = str_replace(chr(13), '', $modx_data);
		}
	}
}

if($submit_file && $modx_data != '' && !$submit)
{
	if($submit_file == MODX)
	{
		include('./read_modx.php');
	}
	else if($submit_file == MOD)
	{
		include('./read_mod.php');
	}
	else
	{
		$submit_file = false;
	}
}

if(!$submit_file && $submit)
{
	$version = (isset($_POST['version'])) ? stripslashes(trim($_POST['version'])) : '';
	$target = (isset($_POST['target'])) ? stripslashes(trim($_POST['target'])) : '';
	$install_level = (isset($_POST['install_level'])) ? stripslashes(trim($_POST['install_level'])) : '';
	$install_time = (isset($_POST['install_time'])) ? intval(trim($_POST['install_time'])) : 0;
	$license = (isset($_POST['license'])) ? stripslashes(trim($_POST['license'])) : '';

	// Arrays
	$title = (isset($_POST['title'])) ? modx_stripslashes($_POST['title']) : false;
	$desc = (isset($_POST['desc'])) ? modx_stripslashes($_POST['desc']) : false;
	$notes = (isset($_POST['notes'])) ? modx_stripslashes($_POST['notes']) : false;
	$diy = (isset($_POST['diy'])) ? modx_stripslashes($_POST['diy']) : false;
	$copy = (isset($_POST['copy'])) ? modx_stripslashes($_POST['copy']) : false;

	$modx = (isset($_POST['modx'])) ? modx_stripslashes($_POST['modx']) : false;

	$author = (isset($_POST['author'])) ? modx_stripslashes($_POST['author']) : false;
	$links = (isset($_POST['links'])) ? modx_stripslashes($_POST['links']) : false;
	$contributor = (isset($_POST['contributor'])) ? modx_stripslashes($_POST['contributor']) : false;
	$history = (isset($_POST['history'])) ? modx_stripslashes($_POST['history']) : false;
	$sql = (isset($_POST['sql'])) ? modx_stripslashes($_POST['sql']) : false;
}

$install_level = (empty($install_level)) ? 'easy' : $install_level;
$install_time = (empty($install_time)) ? 0 : $install_time;
$reverse_history = (isset($_POST['reverse_history'])) ? true : false;
$license = (empty($license)) ? 'http://opensource.org/licenses/gpl-license.php GNU General Public License v2' : $license;

// Check the vars that are not cheched later.
if($submit)
{
	// Check that it's a valid version number
	// I'll ad better errors later.
	if($version == '')
	{
		$error['version'] = 'version';
	}
	else if(!preg_match('#(\d+)\.(\d+)\.\d+[a-z]?#', $version))
	{
		$error['version'] = 'version';
	}
	if($target == '')
	{
		$error['target'] = 'target';
	}
	else if(!preg_match('#(\d+)\.(\d+)\.\d+[a-z]?#', $target))
	{
		$error['target'] = 'target';
	}
	else if($target != PHPBB_LATEST)
	{
		$warning['target'] = 'target';
	}

	if($install_level == '')
	{
		$error['install_level'] = 'install_level';
	}
	if($install_time == 0)
	{
		$error['install_time'] = 'install_time';
	}
}

// Then do some checking.
$history_fields = $link_fields = $author_fields = $sql_fields = $title_fields = $desc_fields = $notes_fields = $diy_fields = $copy_fields = '';

// MOD title
if(!empty($title))
{
	$cnt = 0;
	foreach($title as $value)
	{
		$value2 = gen_value($value['title'], true);
		if($value2 != '')
		{
			$field_id = 'title_pre_' . $cnt++;
			$title_fields .= '<dd id="' . $field_id . '"><input type="text" name="title[' . $field_id . '][title]"' . (($cnt == 1) ? ' id="title"' : '') . ' size="53" maxlength="255" value="' . $value2 . '" />';
			$title_fields .= '<select name="title[' . $field_id . '][lang]">' . lang_select($value['lang']) . '</select>';
			$title_fields .= ($cnt > 1) ? '<img class="do-stuff" src="./images/delete.png" alt="" onclick="$(\'#' . $field_id . '\').remove()" title="Delete" />' : '';
			$title_fields .= '</dd>';
		}
	}
}

if(empty($title_fields))
{
	if($submit)
	{
		$error['title'] = 'title';
	}
	$title_fields = '<dd><input type="text" name="title[title_pre][title]" id="title" size="53" maxlength="255" value="" /><select name="title[title_pre][lang]">' . lang_select() . '</select></dd>';
}

// MOD description
if(!empty($desc))
{
	$cnt = 0;
	foreach($desc as $value)
	{
		$value2 = gen_value($value['desc'], true);
		if($value2 != '')
		{
			$field_id = 'desc_pre_' . $cnt++;
			$desc_fields .= '<dd id="' . $field_id . '"><textarea name="desc[' . $field_id . '][desc]" id="desc_' . $field_id . '_desc" rows="' . count_rows($value2, 73) . '">';
			$desc_fields .= $value2;
			$desc_fields .= '</textarea><span><select name="desc[' . $field_id . '][lang]">' . lang_select($value['lang']) . '</select></span>';
			$desc_fields .= ($cnt > 1) ? '<img class="action-text1" src="./images/delete.png" alt="" onclick="$(\'#' . $field_id . '\').remove()" title="Delete" />' : '';
			$desc_fields .= '<img class="action-text2" src="./images/add.png" alt="" onclick="document.mainform.desc_' . $field_id . '_desc.rows+=4" title="Add 4 rows" />';
			$desc_fields .= '<img class="action-text3" src="./images/del.png" alt="" onclick="if(document.mainform.desc_' . $field_id . '_desc.rows>7){document.mainform.desc_' . $field_id . '_desc.rows-=4}else{document.mainform.desc_' . $field_id . '_desc.rows-=(document.mainform.desc_' . $field_id . '_desc.rows-4)};" title="Remove 4 rows" />';
			$desc_fields .= '</dd>';
		}
	}
}

if(empty($desc_fields))
{
	if($submit)
	{
		$error['desc'] = 'desc';
	}
	$desc_fields = '<dd><textarea name="desc[desc_pre][desc]" id="desc" cols="45" rows="5"></textarea><span><select name="desc[desc_pre][lang]">' . lang_select() . '</select></span>';
	$desc_fields .= '<img class="action-text2" src="./images/add.png" alt="" onclick="document.mainform.desc.rows+=4" title="Add 4 rows" />';
	$desc_fields .= '<img class="action-text3" src="./images/del.png" alt="" onclick="if(document.mainform.desc.rows>7){document.mainform.desc.rows-=4}else{document.mainform.desc.rows-=(document.mainform.desc.rows-4)};" title="Remove 4 rows" />';
	$desc_fields .= '</dd>';
}

// Author notes
if(!empty($notes))
{
	$cnt = 0;
	foreach($notes as $value)
	{
		$value2 = gen_value($value['note'], true);
		if($value2 != '')
		{
			$field_id = 'note_pre_' . $cnt++;
			$notes_fields .= '<dd id="' . $field_id . '"><textarea name="notes[' . $field_id . '][note]" id="notes_' . $field_id . '_note" rows="' . count_rows($value2, 73) . '">';
			$notes_fields .= $value2;
			$notes_fields .= '</textarea><span><select name="notes[' . $field_id . '][lang]">' . lang_select($value['lang']) . '</select></span>';
			$notes_fields .= '<img class="action-text1" src="./images/delete.png" alt="" onclick="$(\'#' . $field_id . '\').remove()" title="Delete" />';
			$notes_fields .= '<img class="action-text2" src="./images/add.png" alt="" onclick="document.mainform.notes_' . $field_id . '_note.rows+=4" title="Add 4 rows" />';
			$notes_fields .= '<img class="action-text3" src="./images/del.png" alt="" onclick="if(document.mainform.notes_' . $field_id . '_note.rows>7){document.mainform.notes_' . $field_id . '_note.rows-=4}else{document.mainform.notes_' . $field_id . '_note.rows-=(document.mainform.notes_' . $field_id . '_note.rows-4)};" title="Remove 4 rows" />';
			$notes_fields .= '</dd>';
		}
	}
}

if(empty($notes_fields))
{
	$notes_fields = '<dd id="notes_pre"><textarea name="notes[desc_pre][note]" id="notes_desc_pre_note" cols="40" rows="5"></textarea><span><select name="notes[desc_pre][lang]">' . lang_select() . '</select></span>';
	$notes_fields .= '<img class="action-text1" src="./images/delete.png" alt="" onclick="$(\'#notes_pre\').remove()" title="Delete" />';
	$notes_fields .= '<img class="action-text2" src="./images/add.png" alt="" onclick="document.mainform.notes_desc_pre_note.rows+=4" title="Add 4 rows" />';
	$notes_fields .= '<img class="action-text3" src="./images/del.png" alt="" onclick="if(document.mainform.notes_desc_pre_note.rows>7){document.mainform.notes_desc_pre_note.rows-=4}else{document.mainform.notes_desc_pre_note.rows-=(document.mainform.notes_desc_pre_note.rows-4)};" title="Remove 4 rows" />';
	$notes_fields .= '</dd>';
}

// Author fields...
$cnt = 0;
if(!empty($author))
{
	foreach($author as $key => $value)
	{
		$field_id = 'af_pre_' . $cnt++;
		$author_fields .= '<fieldset class="white" id="' . $field_id . '_a"><dl><dt class="author-rows"><label for="author-' . $field_id . '-username">Username:*</label></dt>';
		$author_fields .= '<dd class="author-rows"><input type="text" name="author[' . $field_id . '][username]" id="author-' . $field_id . '-username" size="40" maxlength="255" value="' . gen_value($value['username']) . '" />' . (($cnt > 1) ? '<img class="do-stuff" src="./images/delete.png" alt="" onclick="$(\'#' . $field_id . '_a\').remove()" title="Delete this author" />' : '') . '</dd></dl>';
		$author_fields .= '<dl><dt class="author-rows"><label for="author-' . $field_id . '-phpbbcom">Not phpBB.com:</label></dt>';
		$author_fields .= '<dd class="author-rows"><label><input type="checkbox" name="author[' . $field_id . '][phpbbcom]" id="author-' . $field_id . '-phpbbcom"' . ((!empty($value['phpbbcom'])) ? ' checked="checked"' : '') . ' /><span style="font-size: 12px;">(Check here if this author is not registered at phpbb.com.)</span></label></dd></dl>';
		$author_fields .= '<dl><dt class="author-rows"><label for="author-' . $field_id . '-realname">Real name:</label></dt>';
		$author_fields .= '<dd class="author-rows"><input type="text" name="author[' . $field_id . '][realname]" id="author-' . $field_id . '-realname" size="40" maxlength="255" value="' . ((isset($value['realname'])) ? gen_value($value['realname']) : '') . '" /></dd></dl>';
		$author_fields .= '<dl><dt class="author-rows"><label for="author-' . $field_id . '-homepage">Homepage:</label></dt>';
		$author_fields .= '<dd class="author-rows"><input type="text" name="author[' . $field_id . '][homepage]" id="author-' . $field_id . '-homepage" size="40" maxlength="255" value="' . ((isset($value['homepage'])) ? gen_value($value['homepage']) : '') . '" /></dd></dl>';
		$author_fields .= '<dl><dt class="author-rows"><label for="author-' . $field_id . '-email">E-mail:</label></dt>';
		$author_fields .= '<dd class="author-rows"><input type="text" name="author[' . $field_id . '][email]" id="author-' . $field_id . '-email" size="40" maxlength="255" value="' . ((isset($value['email'])) ? gen_value($value['email']) : '') . '" /></dd></dl><fieldset id="' . $field_id . '" style="border: none;">';
		if(!empty($contributor) && sizeof($contributor[$key]))
		{
			$ccnt = 0;
			foreach($contributor[$key] as $cval)
			{
				if($cval['status'] != '' || $cval['position'] != '' || $cval['from'] != '' || $cval['to'] != '')
				{
					$temp_id = 'afc_pre_' . $ccnt++;
					$author_fields .= '<fieldset class="noborder" id="' . $temp_id . '"><dl><dt class="author-rows"><label for="contributor-' . $field_id . '-' . $temp_id . '-status">Status:</label></dt><dd class="author-rows"><select name="contributor[' . $field_id . '][' . $temp_id . '][status]" id="contributor-' . $field_id . '-' . $temp_id . '-status">';
					$author_fields .= '<option value="past"' . (($cval['status'] == 'past') ? ' selected="selected"' : '') . '>Past</option>';
					$author_fields .= '<option value="current"' . (($cval['status'] == 'current') ? ' selected="selected"' : '') . '>Current</option>';
					$author_fields .= '</select><img class="do-stuff" src="./images/delete.png" alt="" onclick="$(\'#' . $temp_id . '\').remove()" title="Delete" /></dd></dl><dl><dt class="author-rows"><label for="contributor-' . $field_id . '-' . $temp_id . '-position">Position:</label></dt><dd class="author-rows">';
					$author_fields .= '<input type="text" name="contributor[' . $field_id . '][' . $temp_id . '][position]" id="contributor-' . $field_id . '-' . $temp_id . '-position" size="40" maxlength="255" value="' . ((isset($cval['position'])) ? gen_value($cval['position']) : '') . '" />';
					$author_fields .= '</dd></dl><dl><dt class="author-rows"><label for="contributor-' . $field_id . '-' . $temp_id . '-from">From date:</label></dt><dd class="author-rows">';
					$author_fields .= '<input type="text" name="contributor[' . $field_id . '][' . $temp_id . '][from]" id="contributor-' . $field_id . '-' . $temp_id . '-from" size="40" maxlength="255" value="' . ((isset($cval['from'])) ? gen_value($cval['from']) : '') . '" />';
					$author_fields .= '</dd></dl><dl><dt class="author-rows"><label for="contributor-' . $field_id . '-' . $temp_id . '-to">To date:</label></dt><dd class="author-rows">';
					$author_fields .= '<input type="text" name="contributor[' . $field_id . '][' . $temp_id . '][to]" id="contributor-' . $field_id . '-' . $temp_id . '-to" size="40" maxlength="255" value="' . ((isset($cval['to'])) ? gen_value($cval['to']) : '') . '" />';
					$author_fields .= '</dd></dl></fieldset>';
				}
			}
		}
		$author_fields .= '</fieldset><input type="button" value="Add contribution" onclick="add_contributor(\'' . $field_id . '\');" />' . (($cnt == 1) ? '<img class="sign" src="./images/info.png" alt="Info icon" title="The contributor fields are optional and every author can have several contributor fields.<br />If you choose to add contributor fields, the only field required is the status. The other are optional." />' : '');
		$author_fields .= '</fieldset>' . "\n";
	}
}

if(empty($author_fields))
{
	if($submit)
	{
		$error['author'] = 'author';
	}
	$author_fields .= '<fieldset class="white"><dl' . ((isset($error['author'])) ? ' class="error-dl"' : '') . ' ><dt class="author-rows"><label for="author-af_pre-username">Username:*</label></dt>';
	$author_fields .= '<dd class="author-rows"><input type="text" name="author[af_pre][username]" id="author-af_pre-username" size="40" maxlength="255" value="" /></dd></dl>';
	$author_fields .= '<dl><dt class="author-rows"><label for="author-af_pre-phpbbcom">Not phpbb.com:</label></dt>';
	$author_fields .= '<dd class="author-rows"><label><input type="checkbox" name="author[af_pre][phpbbcom]" id="author-af_pre-phpbbcom" /><span style="font-size: 12px;">(Check here if this author is not registered at phpbb.com.)</span></label></dd></dl>';
	$author_fields .= '<dl><dt class="author-rows"><label for="author-af_pre-realname">Real name:</label></dt>';
	$author_fields .= '<dd class="author-rows"><input type="text" name="author[af_pre][realname]" id="author-af_pre-realname" size="40" maxlength="255" value="" /></dd></dl>';
	$author_fields .= '<dl><dt class="author-rows"><label for="author-af_pre-homepage">Homepage:</label></dt>';
	$author_fields .= '<dd class="author-rows"><input type="text" name="author[af_pre][homepage]" id="author-af_pre-homepage" size="40" maxlength="255" value="" /></dd></dl>';
	$author_fields .= '<dl><dt class="author-rows"><label for="author-af_pre-email">E-mail:</label></dt>';
	$author_fields .= '<dd class="author-rows"><input type="text" name="author[af_pre][email]" id="author-af_pre-email" size="40" maxlength="255" value="" /></dd></dl><fieldset id="af_pre" style="border: none;"></fieldset>';
	$author_fields .= '<input type="button" value="Add contribution" onclick="add_contributor(\'af_pre\');" /><img class="sign" src="./images/info.png" alt="Info icon" title="The contributor fields are optional and every author can have several contributor fields.<br />If you choose to add contributor fields, the only field required is the status. The other are optional." /></fieldset>' . "\n";
}

// Links
$cnt = 0;
if(!empty($links))
{
	foreach($links as $value)
	{
		if(trim($value['title']) != '' || trim($value['href']) != '' || trim($value['type']) != '' || trim($value['lang']) != '')
		{
			$field_id = 'lf_pre_' . $cnt++;
			$link_fields .= '<fieldset class="white" id="' . $field_id . '"><dl><dt class="link-rows"><label for="links-' . $field_id . '-type">Type:*</label></dt><dd class="link-rows"><select name="links[' . $field_id . '][type]" id="links-' . $field_id . '-type">';
			$link_fields .= '<option value="contrib"' . (($value['type'] == 'contrib') ? ' selected="selected"' : '') . '>Contrib</option>';
			$link_fields .= '<option value="dependency"' . (($value['type'] == 'dependency') ? ' selected="selected"' : '') . '>Dependency</option>';
			$link_fields .= '<option value="language"' . (($value['type'] == 'language') ? ' selected="selected"' : '') . '>Language</option>';
			$link_fields .= '<option value="parent"' . (($value['type'] == 'parent') ? ' selected="selected"' : '') . '>Parent</option>';
			$link_fields .= '<option value="template-lang"' . (($value['type'] == 'template-lang') ? ' selected="selected"' : '') . '>Template lang</option>';
			$link_fields .= '<option value="template"' . (($value['type'] == 'template') ? ' selected="selected"' : '') . '>Template</option>';
			$link_fields .= '</select><img class="do-stuff" src="./images/delete.png" alt="" onclick="$(\'#' . $field_id . '\').remove()" title="Delete this entry" /></dd></dl><dl><dt class="link-rows"><label for="links-' . $field_id . '-title">Link title:*</label></dt><dd class="link-rows">';
			$link_fields .= '<input name="links[' . $field_id . '][title]" id="links-' . $field_id . '-title" size="80" maxlength="255" value="' . gen_value($value['title'], true) . '" type="text" />';
			$link_fields .= '<select name="links[' . $field_id . '][lang]">' . lang_select($value['lang']) . '</select></span>';
			$link_fields .= '</dd></dl><dl><dt class="link-rows"><label for="links-' . $field_id . '-href">URL:*</label></dt><dd class="link-rows">';
			$link_fields .= '<input name="links[' . $field_id . '][href]" id="links-' . $field_id . '-href" size="80" maxlength="255" value="' . gen_value($value['href'], true) . '" type="text" />';
			$link_fields .= '</dd></dl></fieldset>';
		}
	}
}

// History
$cnt = 0;
if(!empty($history))
{
	if($reverse_history)
	{
		$history = array_reverse($history, true);
	}
	foreach($history as $value)
	{
		$temp_fields = $version_warnig = $date_warnig = '';
		$value['version'] = trim($value['version']);
		$value['date'] = trim($value['date']);
		if($value['version'] != '' || $value['date'] != '' || !empty($value['change']))
		{
			if($value['version'] == '' || !preg_match('#(\d+)\.(\d+)\.\d+[a-z]?#', $value['version']))
			{
				$warning['history']['version'] = 'version';
				$version_warnig = ' class="warning-dl"';
			}
			if($value['date'] == '')
			{
				$warning['history']['version'] = 'version';
				$date_warnig = ' class="warning-dl"';
			}
			$field_id = 'hf_pre_' . $cnt++;
			$temp_fields .= '<fieldset  class="white" id="d' . $field_id . '"><dl' . $version_warnig . '><dt class="history-rows"><label for="history-' . $field_id . '-version">Version:*</label></dt>';
			$temp_fields .= '<dd class="history-rows"><input name="history[' . $field_id . '][version]" id="history-' . $field_id . '-version" size="10" maxlength="255" value="' . gen_value($value['version']) . '" type="text" /><img class="do-stuff" src="./images/delete.png" alt="" onclick="$(\'#d' . $field_id . '\').remove()" title="Delete" /></dd></dl>';
			$temp_fields .= '<dl' . $date_warnig . '><dt class="history-rows"><label for="history-' . $field_id . '-date">Date:*</label></dt>';
			$temp_fields .= '<dd class="history-rows"><input name="history[' . $field_id . '][date]" id="history-' . $field_id . '-date" size="20" maxlength="255" value="' . gen_value($value['date']) . '" type="text" /></dd></dl>';
			$temp_fields .= '<fieldset id="' . $field_id . '" style="border: none ;">';
			$ccnt = 0;
			$temp_data = '';
			foreach($value['change'] as $cval)
			{
				if(trim($cval['data']) != '')
				{
					$temp_id = 'hfc_pre_' . $ccnt++;
					$temp_data .= '<dl id="' . $temp_id . '"><dt class="history-rows"><label for="history-' . $field_id . '-change-' . $temp_id . '-data">Change:' . (($ccnt == 1) ? '*' : '') . '</label></dt><dd class="history-rows">';
					$temp_data .= '<input name="history[' . $field_id . '][change][' . $temp_id . '][data]" id="history-' . $field_id . '-change-' . $temp_id . '-data" size="80" maxlength="255" value="' . gen_value($cval['data'], true) . '" type="text" />';
					$temp_data .= '<span><select name="history[' . $field_id . '][change][' . $temp_id . '][lang]">' . lang_select($cval['lang']) . '</select></span>';
					$temp_data .= ($ccnt > 1) ? '<img class="do-stuff" src="./images/delete.png" alt="" onclick="$(\'#' . $temp_id . '\').remove()" title="Delete" />' : '';
					$temp_data .= '</dd></dl>';
				}
			}
		}
		if($value['version'] != '' || $value['date'] != '' || $temp_data != '')
		{
			if($temp_data == '')
			{
				$temp_id = 'hfc_pre_' . $ccnt++;
				$temp_data .= '<dl class="warning-dl" id="' . $temp_id . '"><dt><label for="history-' . $field_id . '-change-' . $temp_id . '-data">Change:' . (($ccnt == 1) ? '*' : '') . '</label></dt><dd>';
				$temp_data .= '<input name="history[' . $field_id . '][change][' . $temp_id . '][data]" id="history-' . $field_id . '-change-' . $temp_id . '-data" size="40" maxlength="255" value="" type="text" />';
				$temp_data .= '<span><select name="history[' . $field_id . '][change][' . $temp_id . '][lang]">' . lang_select() . '</select></span>';
				$temp_data .= ($ccnt > 1) ? '<img class="do-stuff" src="./images/delete.png" alt="" onclick="$(\'#' . $temp_id . '\').remove()" title="Delete" />' : '';
				$temp_data .= '</dd></dl>';
			}
			$history_fields .= $temp_fields . $temp_data . '</fieldset><input value="Add change" onclick="add_history_change(\'' . $field_id . '\');" type="button" /></fieldset>';
		}
	}
}
unset($temp_fields, $temp_data, $version_warnig, $date_warnig);

// SQL querys
$cnt = 0;
if(!empty($sql))
{
	foreach($sql as $value)
	{
		if(trim($value['query']) != '')
		{
			$field_id = 'sql_pre_' . $cnt++;
			$dbms = ($value['dbms'] == '' || empty($value['dbms'])) ? 'sql-parser' : $value['dbms'];
			$sql_fields .= '<fieldset class="white" id="' . $field_id . '"><dl><dt class="sql-rows"><label for="sql-' . $field_id . '-dbms">DBMS:</label></dt><dd class="sql-rows">';
			$sql_fields .= '<select name="sql[' . $field_id . '][dbms]" id="sql-' . $field_id . '-dbms">';
			$sql_fields .= '<option value="mysql"' . (($dbms == 'mysql') ? ' selected="selected"' : '') . '>MySQL</option>';
			$sql_fields .= '<option value="mysql4"' . (($dbms == 'mysql4') ? ' selected="selected"' : '') . '>MySQL 4</option>';
			$sql_fields .= '<option value="mysql_40"' . (($dbms == 'mysql_40') ? ' selected="selected"' : '') . '>MySQL 4.0</option>';
			$sql_fields .= '<option value="mysql_41"' . (($dbms == 'mysql_41') ? ' selected="selected"' : '') . '>MySQL 4.1</option>';
			$sql_fields .= '<option value="mysqli"' . (($dbms == 'mysqli') ? ' selected="selected"' : '') . '>MySQLi</option>';
			$sql_fields .= '<option value="mssaccess"' . (($dbms == 'mssaccess') ? ' selected="selected"' : '') . '>MSS Access</option>';
			$sql_fields .= '<option value="oracle"' . (($dbms == 'oracle') ? ' selected="selected"' : '') . '>Oracle</option>';
			$sql_fields .= '<option value="postgres"' . (($dbms == 'postgres') ? ' selected="selected"' : '') . '>PostgreSQL</option>';
			$sql_fields .= '<option value="db2"' . (($dbms == 'db2') ? ' selected="selected"' : '') . '>DB2</option>';
			$sql_fields .= '<option value="firebird"' . (($dbms == 'firebird') ? ' selected="selected"' : '') . '>FireBird</option>';
			$sql_fields .= '<option value="sqlite"' . (($dbms == 'sqlite') ? ' selected="selected"' : '') . '>SQLite</option>';
			$sql_fields .= '<option value="sql-parser"' . (($dbms == 'sql-parser') ? ' selected="selected"' : '') . '>SQL Parser (default)</option>';
			$sql_fields .= '</select></dd></dl><dl><dt class="sql-rows"><label for="sql-' . $field_id . '-query">Query:*</label></dt><dd class="sql-rows">';
			$sql_fields .= '<textarea name="sql[' . $field_id . '][query]" id="sql_' . $field_id . '_query"  class="sql-rows" rows="' . count_rows($value['query'], 95) . '">' . gen_value($value['query']) . '</textarea>';
			$sql_fields .= '<img class="action-text1" src="./images/delete.png" alt="" onclick="$(\'#' . $field_id . '\').remove()" title="Delete" />';
			$sql_fields .= '<img class="action-text2" src="./images/add.png" alt="" onclick="document.mainform.sql_' . $field_id . '_query.rows+=4" title="Add 4 rows" />';
			$sql_fields .= '<img class="action-text3" src="./images/del.png" alt="" onclick="if(document.mainform.sql_' . $field_id . '_query.rows>7){document.mainform.sql_' . $field_id . '_query.rows-=4}else{document.mainform.sql_' . $field_id . '_query.rows-=(document.mainform.sql_' . $field_id . '_query.rows-4)};" title="Remove 4 rows" />';
			$sql_fields .= '</dd></dl></fieldset>';
		}
	}
}

// File copy
$is_copy = false;
$cnt = 0;
if(!empty($copy))
{
	foreach($copy as $value)
	{
		$field_id = 'fc_pre_' . $cnt++;

		$ccnt = 0;
		foreach($value as $cval)
		{
			if(trim($cval['from']) != '')
			{
				$is_copy = true;
				$temp_id = 'fcc_pre_' . $ccnt++;
				$copy_fields .= ($ccnt == 1) ? '<fieldset class="white" id="dd' . $field_id . '"><dl id="' . $field_id . '"><dt class="copy-rows"><label for="copy-' . $field_id . '-' . $temp_id . 'from">Copy: (from -&gt; to)</label><img class="sign plus-sign" src="./images/plus.png" alt="" title="" onclick="add_file_copy(\'' . $field_id . '\');"><img class="do-stuff" src="./images/delete.png" alt="" onclick="$(\'#dd' . $field_id . '\').remove()" title="Delete entry" /></dt>' : '';
				$copy_fields .= '<dd class="copy-rows" id="' . $temp_id . '"><input name="copy[' . $field_id . '][' . $temp_id . '][from]" id="copy-' . $field_id . '-' . $temp_id . '-from" size="85" maxlength="255" value="' . gen_value($cval['from']) . '" type="text">';
				$copy_fields .= '<span> -&gt; </span>';
				$copy_fields .= '<input name="copy[' . $field_id . '][' . $temp_id . '][to]" size="85" maxlength="255" value="' . gen_value($cval['to']) . '" type="text"><img class="do-stuff" src="./images/delete.png" alt="" onclick="$(\'#' . $temp_id . '\').remove()" title="Delete" /></dd>';
			}
		}
		$copy_fields .= ($ccnt > 0) ? '</dl></fieldset>' : '';
	}
}
$copy_fields = ($is_copy) ? $copy_fields : '';

// DIY fields
if(!empty($diy))
{
	$cnt = 0;
	foreach($diy as $value)
	{
		if(trim($value['diy']) != '')
		{
			$field_id = 'diy_pre_' . $cnt++;
			$diy_fields .= '<dd id="' . $field_id . '"><textarea name="diy[' . $field_id . '][diy]" id="diy_' . $field_id . '_diy" rows="' . count_rows($value['diy'], 88) . '">';
			$diy_fields .= gen_value($value['diy'], true);
			$diy_fields .= '</textarea><span><select name="diy[' . $field_id . '][lang]">' . lang_select($value['lang']) . '</select></span><img class="action-text1" src="./images/delete.png" alt="" onclick="$(\'#' . $field_id . '\').remove()" title="Delete" />';
			$diy_fields .= '<img id="' . $field_id . '-plus" class="action-text2" src="./images/add.png" alt="" onclick="document.mainform.diy_' . $field_id . '_diy.rows+=4" title="Add 4 rows" />';
			$diy_fields .= '<img id="' . $field_id . '-minus" class="action-text3" src="./images/del.png" alt="" onclick="if(document.mainform.diy_' . $field_id . '_diy.rows>7){document.mainform.diy_' . $field_id . '_diy.rows-=4}else{document.mainform.diy_' . $field_id . '_diy.rows-=(document.mainform.diy_' . $field_id . '_diy.rows-4)};" title="Remove 4 rows" />';
			$diy_fields .= '</dd>';
		}
	}
}

if(empty($diy_fields))
{
	$diy_fields = '<dd id="diy_0"><textarea name="diy[desc_pre][diy]" id="diy_01" rows="5"></textarea><span><select name="diy[desc_pre][lang]">' . lang_select() . '</select></span><img class="action-text1" src="./images/delete.png" alt="" onclick="$(\'#diy_0\').remove()" title="Delete" /><img id="diy0-plus" class="action-text2" src="./images/add.png" alt="" onclick="document.mainform.diy_01.rows+=4" title="Add 4 rows" /><img id="diy0-minus" class="action-text3" src="./images/del.png" alt="" onclick="if(document.mainform.diy_01.rows>7){document.mainform.diy_01.rows-=4}else{document.mainform.diy_01.rows-=(document.mainform.diy_01.rows-4)};" title="Remove 4 rows" /></dd>';
}

// The Action fields...
$modx_fields = '';

if(!empty($modx))
{
	$filenames = $directories = $files = array();
	foreach($modx as $key => $value)
	{
		$files[$key] = $value['file'];
		$filenames[$key] = basename($value['file']);
		$directories[$key] = dirname($value['file']);
	}
	array_multisort($directories, SORT_STRING, $filenames, SORT_STRING, $files);

	// Lets save some memory
	unset($filenames, $directories);

	// And finaly... The Action fields again...
	$file_cnt = $edit_cnt = $dl_cnt = $dt_cnt = $dd_cnt = 0;
	foreach($files as $key => $value)
	{
		if($value)
		{
			// Array 1, files
			$file_id = 'f_pre_' . $file_cnt++;
			$dt_id = 'dt_pre_' . $dt_cnt++;
			$modx_fields .= '<fieldset class="inner" id="' . $file_id . '"><dl id="' . $dt_id . '"><dt><label>File to open:</label><img class="sign" src="./images/info.png" alt="" title="Relative path from the phpBB root for the file to open. For example, viewforum.php or includes/functions.php"></dt>';
			$modx_fields .= '<dd><input name="modx[' . $file_id . '][file]" size="88" value="' . gen_value($modx[$key]['file'], true) . '" type="text"><img class="do-stuff" src="./images/delete.png" alt="" onclick="$(\'#' . $file_id . '\').remove()" title="Delete this file" /></dd></dl>';

			foreach($modx[$key] as $key2 => $value2)
			{
				// Array 2, edits We dont need the filenames here
				if($key2 != 'file')
				{
					$edit_id = 'e_pre_' . $edit_cnt++;
					$modx_fields .= '<fieldset class="white" id="' . $edit_id . '"><legend>Edit<img class="sign" src="./images/info.png" alt="" title="Every discreet change to a file must be wrapped in its own edit tag, regardless of the number of children it contains.&lt;br /&gt;All finds within an edit tag should be processed before any action tag.">';
					$modx_fields .= '<img class="do-stuff" src="./images/plus_up.png" alt="" onclick="modx_add_field(\'modx[' . $file_id . ']\', \'' . $edit_id . '\', \'edit\', \'above\', 1)" title="Add an edit field above this edit field">';
					$modx_fields .= '<img class="do-stuff" src="./images/plus_down.png" alt="" onclick="modx_add_field(\'modx[' . $file_id . ']\', \'' . $edit_id . '\', \'edit\', \'below\', 1)" title="Add an edit field below this edit field">';
					$modx_fields .= '<img class="do-stuff" src="./images/delete.png" alt="" onclick="$(\'#' . $edit_id . '\').remove()" title="Delete this edit"></legend><p>NOTE: Each discreet change to a file must be wrapped in its own edit tag.</p>';

					foreach($value2 as $key3 => $value3)
					{
						// Array 3, dl's The string fields
						if($value3['type'] != '' && isset($value3['data']))
						{
							$dl_id = 'dl_pre_' . $dl_cnt++;
							$dt_id = 'dt_pre_' . $dt_cnt++;
							$dd_id = 'dd_pre_' . $dd_cnt++;

							// Need to get the right input field here to view the right input and image...
							// We'll do it here so we don't have to do the checks twice
							if(strpos($value3['type'], 'inline') !== FALSE)
							{
								$textarea = false;
								// Inline stuff can only be one-liners
								// On some systems \r comes before \n and I bet some systems only uses \r
								$data = str_replace("\r", "\n", $value3['data']);
								if ($pos = strpos($value3['data'], "\n") !== false )
								{
									$data = substr($value3['data'], 0, $pos);
								}
								else
								{
									$data = $value3['data'];
								}
								$modx_input = '<span id="' . $dd_id . '_field"><textarea id="' . $dd_id . '_data" name="modx[' . $file_id . '][' . $edit_id . '][' . $dl_id . '][data]" rows="0" onKeypress="if((event.keyCode == 10) || (event.keyCode == 13)){return false;}">' . gen_value($data) . '</textarea></span>';
								$modx_img = '<img id="' . $dd_id . '_info" class="action-arrows" src="./images/info_8.png" alt="" title="Note that the inline tags may not contain line breaks">';
							}
							else if($value3['type'] != 'comment')
							{
								$textarea = true;
								$modx_input = '<span id="' . $dd_id . '_field"><textarea class="action-texts" id="' . $dd_id . '_data" name="modx[' . $file_id . '][' . $edit_id . '][' . $dl_id . '][data]" rows="' . count_rows($value3['data'], 85) . '">' . gen_value($value3['data']) . '</textarea></span>';
								if($value3['type'] == 'find')
								{
									$modx_img = '<img id="' . $dd_id . '_info" class="action-arrows" src="./images/info_2.png" alt="" title="Find tags in the MODX file should be in the order that the find targets appear in the file. In other words, a processor of the MODX file should never need to go backwards in the file to locate all of the finds. When there are multiple finds within a single edit tag, the processor should handle all finds before any actions.">';
								}
								else
								{
									$modx_img = '<img id="' . $dd_id . '_info" class="action-arrows" src="./images/info_12.png" alt="" title="The string to add before the find, add after the find, replace the find with, or the operation string described above.&lt;br /&gt;&lt;br /&gt;The syntax for the operation action is a bit obscure because it uses tokens. The find action would have a token like this&lt;br /&gt;colspan=&quot;{:%1}&quot;&lt;br /&gt;The operation action would look like this:&lt;br /&gt;{:%1} + 1">';
								}
							}
							else
							{
								$textarea = true;
								// The comments needs a lang select
								$modx_input = '<span id="' . $dd_id . '_field"><textarea class="action-comment" id="' . $dd_id . '_data" name="modx[' . $file_id . '][' . $edit_id . '][' . $dl_id . '][data]" rows="' . count_rows($value3['data'], 70) . '">' . gen_value($value3['data']) . '</textarea>';
								$modx_input .= '<span id="' . $dd_id . '_lang"><select name="modx[' . $file_id . '][' . $edit_id . '][' . $dl_id . '][lang]">' . lang_select($value3['lang']) . '</select></span></span>';
								$modx_img = '<img id="' . $dd_id . '_info" class="action-arrows" src="./images/info_17.png" alt="" title="Comment pertaining to this edit">';
							}

							$modx_fields .= '<dl id="' . $dl_id . '" style="white-space: nowrap;"><dt id="' . $dt_id . '"><label>Type:</label><span>';

							$modx_fields .= '<select class="krav" name="modx[' . $file_id . '][' . $edit_id . '][' . $dl_id . '][type]" onchange="if(this.options[this.selectedIndex].value != \'-\'){ get_select_change(this.options[this.selectedIndex].value, \'' . $dt_id . '\', \'' . $dd_id . '\', \'modx[' . $file_id . '][' . $edit_id . '][' . $dl_id . ']\') }">';
							$modx_fields .= '<option value="comment"' . (($value3['type'] == 'comment') ? ' selected="selected"' : '') . '>Comment</option>';
							$modx_fields .= '<option value="find"' . (($value3['type'] == 'find') ? ' selected="selected"' : '') . '>Find</option>';
							$modx_fields .= '<option value="after-add"' . (($value3['type'] == 'after-add') ? ' selected="selected"' : '') . '>After add</option>';
							$modx_fields .= '<option value="before-add"' . (($value3['type'] == 'before-add') ? ' selected="selected"' : '') . '>Before add</option>';
							$modx_fields .= '<option value="replace-with"' . (($value3['type'] == 'replace-with') ? ' selected="selected"' : '') . '>Replace with</option>';
							$modx_fields .= '<option value="operation"' . (($value3['type'] == 'operation') ? ' selected="selected"' : '') . '>Operation</option>';
							$modx_fields .= '<option value="inline-find"' . (($value3['type'] == 'inline-find') ? ' selected="selected"' : '') . '>Inline find</option>';
							$modx_fields .= '<option value="inline-after-add"' . (($value3['type'] == 'inline-after-add') ? ' selected="selected"' : '') . '>Inline after add</option>';
							$modx_fields .= '<option value="inline-before-add"' . (($value3['type'] == 'inline-before-add') ? ' selected="selected"' : '') . '>Inline before add</option>';
							$modx_fields .= '<option value="inline-replace-with"' . (($value3['type'] == 'inline-replace-with') ? ' selected="selected"' : '') . '>Inline replace with</option>';
							$modx_fields .= '<option value="inline-operation"' . (($value3['type'] == 'inline-operation') ? ' selected="selected"' : '') . '>Inline operation</option>';
							$modx_fields .= '<option value="-"' . (($value3['type'] == '-') ? ' selected="selected"' : '') . '>Select type</option></select></span>';
							$modx_fields .= '<br /><img class="action-arrows" src="./images/plus_up.png" alt="" onclick="modx_add_field(\'modx[' . $file_id . '][' . $edit_id . ']\', \'' . $dl_id . '\', \'dl\', \'above\')" title="Add action above">';
							$modx_fields .= '<img class="action-arrows" src="./images/plus_down.png" alt="" onclick="modx_add_field(\'modx[' . $file_id . '][' . $edit_id . ']\', \'' . $dl_id . '\', \'dl\', \'below\')" title="Add action below">';
							$modx_fields .= $modx_img;
							$modx_fields .= '</dt><dd id="' . $dd_id . '"><span>';

							$modx_fields .= $modx_input;

							$modx_fields .= '<img class="action-text1" src="./images/delete.png" alt="" onclick="$(\'#' . $dl_id . '\').remove()" title="Delete" />';
							$modx_fields .= ($textarea) ? '<img id="' . $dd_id . '-plus" class="action-text2" src="./images/add.png" alt="" onclick="document.mainform.' . $dd_id . '_data.rows+=4" title="Add 4 rows" />' : '';
							$modx_fields .= ($textarea) ? '<img id="' . $dd_id . '-minus" class="action-text3" src="./images/del.png" alt="" onclick="if(document.mainform.' . $dd_id . '_data.rows>7){document.mainform.' . $dd_id . '_data.rows-=4}else{document.mainform.' . $dd_id . '_data.rows-=(document.mainform.' . $dd_id . '_data.rows-4)};" title="Remove 4 rows" />' : '';
							$modx_fields .= '</span></dd></dl>';
						}
					}
					$modx_fields .= '</fieldset>';
				}
			}
			$modx_fields .= '</fieldset>';
		}
	}
}

$modx_url = $error_field = '';
if($dload || $preview)
{
	if(!empty($error))
	{
		$error_field = '<span id="error-span">There were errors</span>';
	}
	else
	{
		include('./create_modx.php');
	}
}

$error_field = ($error_field != '') ? '<div class="error-div">' . $error_field . '</div>' : '';

include('./template.php');
