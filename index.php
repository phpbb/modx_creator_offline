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
if ($submit_file && !$submit)
{
	// Did we get a file?
	if ($_FILES['upload-file']['size'] > 0)
	{
		// Lets start with the extension...
		$extension = strtolower(array_pop(explode('.', $_FILES['upload-file']['name'])));
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
}

// Then do some checking.
$history_fields = $link_fields = $author_fields = $sql_fields = $title_fields = $desc_fields = $notes_fields = $diy_fields = $copy_fields  = $meta_fields = '';

// MOD title
$cnt = 0;
while ($title = $parser->get_modx_title())
{
	$title['title'] = gen_value($title['title'], true);
	if ($title != '')
	{
		$field_id = 'title_pre_' . $cnt++;
		$title_fields .= '<dd id="' . $field_id . '"><input type="text" name="title[' . $field_id . '][title]"' . (($cnt == 1) ? ' id="title"' : '') . ' size="53" maxlength="255" value="' . $title['title'] . '" />';
		$title_fields .= '<select name="title[' . $field_id . '][lang]">' . lang_select($title['lang']) . '</select>';
		$title_fields .= ($cnt > 1) ? '<img class="do-stuff" src="./images/delete.png" alt="" onclick="$(\'#' . $field_id . '\').remove()" title="Delete" />' : '';
		$title_fields .= '</dd>';
	}
}

if (empty($title_fields))
{
	if ($submit)
	{
		$error['title'] = 'title';
	}
	$title_fields = '<dd><input type="text" name="title[pre][title]" id="title" size="53" maxlength="255" value="" /><select name="title[0][lang]">' . lang_select() . '</select></dd>';
}

// MOD description
$cnt = 0;
while ($desc = $parser->get_modx_description())
{
	$desc['desc'] = gen_value($desc['desc'], true);
	if ($desc != '')
	{
		$field_id = 'desc_pre_' . $cnt++;
		$desc_fields .= '<dd id="' . $field_id . '"><textarea name="desc[' . $field_id . '][desc]" id="desc_' . $field_id . '_desc" rows="' . count_rows($desc['desc'], 73) . '">';
		$desc_fields .= $desc['desc'];
		$desc_fields .= '</textarea><span><select name="desc[' . $field_id . '][lang]">' . lang_select($desc['lang']) . '</select></span>';
		$desc_fields .= ($cnt > 1) ? '<img class="action-text1" src="./images/delete.png" alt="" onclick="$(\'#' . $field_id . '\').remove()" title="Delete" />' : '';
		$desc_fields .= '<img class="action-text2" src="./images/add.png" alt="" onclick="document.mainform.desc_' . $field_id . '_desc.rows+=4" title="Add 4 rows" />';
		$desc_fields .= '<img class="action-text3" src="./images/del.png" alt="" onclick="if(document.mainform.desc_' . $field_id . '_desc.rows>7){document.mainform.desc_' . $field_id . '_desc.rows-=4}else{document.mainform.desc_' . $field_id . '_desc.rows-=(document.mainform.desc_' . $field_id . '_desc.rows-4)};" title="Remove 4 rows" />';
		$desc_fields .= '</dd>';
	}
}

if (empty($desc_fields))
{
	if ($submit)
	{
		$error['desc'] = 'desc';
	}
	$desc_fields = '<dd><textarea name="desc[desc_pre][desc]" id="desc" cols="45" rows="5"></textarea><span><select name="desc[desc_pre][lang]">' . lang_select() . '</select></span>';
	$desc_fields .= '<img class="action-text2" src="./images/add.png" alt="" onclick="document.mainform.desc.rows+=4" title="Add 4 rows" />';
	$desc_fields .= '<img class="action-text3" src="./images/del.png" alt="" onclick="if(document.mainform.desc.rows>7){document.mainform.desc.rows-=4}else{document.mainform.desc.rows-=(document.mainform.desc.rows-4)};" title="Remove 4 rows" />';
	$desc_fields .= '</dd>';
}

// Author notes
$cnt = 0;
while ($note = $parser->get_modx_notes())
{
	$note['note'] = gen_value($note['note'], true);
	if ($note != '')
	{
		$field_id = 'note_pre_' . $cnt++;
		$notes_fields .= '<dd id="' . $field_id . '"><textarea name="notes[' . $field_id . '][note]" id="notes_' . $field_id . '_note" rows="' . count_rows($note['note'], 73) . '">';
		$notes_fields .= $note['note'];
		$notes_fields .= '</textarea><span><select name="notes[' . $field_id . '][lang]">' . lang_select($note['lang']) . '</select></span>';
		$notes_fields .= '<img class="action-text1" src="./images/delete.png" alt="" onclick="$(\'#' . $field_id . '\').remove()" title="Delete" />';
		$notes_fields .= '<img class="action-text2" src="./images/add.png" alt="" onclick="document.mainform.notes_' . $field_id . '_note.rows+=4" title="Add 4 rows" />';
		$notes_fields .= '<img class="action-text3" src="./images/del.png" alt="" onclick="if(document.mainform.notes_' . $field_id . '_note.rows>7){document.mainform.notes_' . $field_id . '_note.rows-=4}else{document.mainform.notes_' . $field_id . '_note.rows-=(document.mainform.notes_' . $field_id . '_note.rows-4)};" title="Remove 4 rows" />';
		$notes_fields .= '</dd>';
	}
}

if (empty($notes_fields))
{
	$notes_fields = '<dd id="notes_pre"><textarea name="notes[desc_pre][note]" id="notes_desc_pre_note" cols="40" rows="5"></textarea><span><select name="notes[desc_pre][lang]">' . lang_select() . '</select></span>';
	$notes_fields .= '<img class="action-text1" src="./images/delete.png" alt="" onclick="$(\'#notes_pre\').remove()" title="Delete" />';
	$notes_fields .= '<img class="action-text2" src="./images/add.png" alt="" onclick="document.mainform.notes_desc_pre_note.rows+=4" title="Add 4 rows" />';
	$notes_fields .= '<img class="action-text3" src="./images/del.png" alt="" onclick="if(document.mainform.notes_desc_pre_note.rows>7){document.mainform.notes_desc_pre_note.rows-=4}else{document.mainform.notes_desc_pre_note.rows-=(document.mainform.notes_desc_pre_note.rows-4)};" title="Remove 4 rows" />';
	$notes_fields .= '</dd>';
}

// Author fields...
$cnt = 0;
while ($author = $parser->get_modx_authors())
{
	$field_id = 'af_pre_' . $cnt++;
	$author_fields .= '<fieldset class="white" id="' . $field_id . '_a"><dl><dt class="author-rows"><label for="author-' . $field_id . '-username">Username:*</label></dt>';
	$author_fields .= '<dd class="author-rows"><input type="text" name="author[' . $field_id . '][username]" id="author-' . $field_id . '-username" size="40" maxlength="255" value="' . gen_value($author['username']) . '" />' . (($cnt > 1) ? '<img class="do-stuff" src="./images/delete.png" alt="" onclick="$(\'#' . $field_id . '_a\').remove()" title="Delete this author" />' : '') . '</dd></dl>';
	$author_fields .= '<dl><dt class="author-rows"><label for="author-' . $field_id . '-phpbbcom">Not phpBB.com:</label></dt>';
	$author_fields .= '<dd class="author-rows"><label><input type="checkbox" name="author[' . $field_id . '][phpbbcom]" id="author-' . $field_id . '-phpbbcom"' . ((!empty($author['phpbbcom'])) ? ' checked="checked"' : '') . ' /><span style="font-size: 12px;">(Check here if this author is not registered at phpbb.com.)</span></label></dd></dl>';
	$author_fields .= '<dl><dt class="author-rows"><label for="author-' . $field_id . '-realname">Real name:</label></dt>';
	$author_fields .= '<dd class="author-rows"><input type="text" name="author[' . $field_id . '][realname]" id="author-' . $field_id . '-realname" size="40" maxlength="255" value="' . ((isset($author['realname'])) ? gen_value($author['realname']) : '') . '" /></dd></dl>';
	$author_fields .= '<dl><dt class="author-rows"><label for="author-' . $field_id . '-homepage">Homepage:</label></dt>';
	$author_fields .= '<dd class="author-rows"><input type="text" name="author[' . $field_id . '][homepage]" id="author-' . $field_id . '-homepage" size="40" maxlength="255" value="' . ((isset($author['homepage'])) ? gen_value($author['homepage']) : '') . '" /></dd></dl>';
	$author_fields .= '<dl><dt class="author-rows"><label for="author-' . $field_id . '-email">E-mail:</label></dt>';
	$author_fields .= '<dd class="author-rows"><input type="text" name="author[' . $field_id . '][email]" id="author-' . $field_id . '-email" size="40" maxlength="255" value="' . ((isset($author['email'])) ? gen_value($author['email']) : '') . '" /></dd></dl><fieldset id="' . $field_id . '" style="border: none;">';
	if (!empty($author['contributions']))
	{
		$ccnt = 0;
		foreach($author['contributions'] as $cval)
		{
			if ($cval['status'] != '' || $cval['position'] != '' || $cval['from'] != '' || $cval['to'] != '')
			{
				$temp_id = 'afc_pre_' . $ccnt++;
				$author_fields .= '<fieldset class="noborder" id="' . $temp_id . '"><dl><dt class="author-rows"><label for="contributor-' . $field_id . '-' . $temp_id . '-status">Status:</label></dt><dd class="author-rows"><select name="author[' . $field_id . '][contributions][' . $temp_id . '][status]" id="contributor-' . $field_id . '-' . $temp_id . '-status">';
				$author_fields .= '<option value="past"' . (($cval['status'] == 'past') ? ' selected="selected"' : '') . '>Past</option>';
				$author_fields .= '<option value="current"' . (($cval['status'] == 'current') ? ' selected="selected"' : '') . '>Current</option>';
				$author_fields .= '</select><img class="do-stuff" src="./images/delete.png" alt="" onclick="$(\'#' . $temp_id . '\').remove()" title="Delete" /></dd></dl><dl><dt class="author-rows"><label for="contributor-' . $field_id . '-' . $temp_id . '-position">Position:</label></dt><dd class="author-rows">';
				$author_fields .= '<input type="text" name="author[' . $field_id . '][contributions][' . $temp_id . '][position]" id="contributor-' . $field_id . '-' . $temp_id . '-position" size="40" maxlength="255" value="' . ((isset($cval['position'])) ? gen_value($cval['position']) : '') . '" />';
				$author_fields .= '</dd></dl><dl><dt class="author-rows"><label for="contributor-' . $field_id . '-' . $temp_id . '-from">From date:</label></dt><dd class="author-rows">';
				$author_fields .= '<input type="text" name="author[' . $field_id . '][contributions][' . $temp_id . '][from]" id="contributor-' . $field_id . '-' . $temp_id . '-from" size="40" maxlength="255" value="' . ((isset($cval['from'])) ? gen_value($cval['from']) : '') . '" />';
				$author_fields .= '</dd></dl><dl><dt class="author-rows"><label for="contributor-' . $field_id . '-' . $temp_id . '-to">To date:</label></dt><dd class="author-rows">';
				$author_fields .= '<input type="text" name="author[' . $field_id . '][contributions][' . $temp_id . '][to]" id="contributor-' . $field_id . '-' . $temp_id . '-to" size="40" maxlength="255" value="' . ((isset($cval['to'])) ? gen_value($cval['to']) : '') . '" />';
				$author_fields .= '</dd></dl></fieldset>';
			}
		}
	}
	$author_fields .= '</fieldset><input type="button" value="Add contribution" onclick="add_contributor(\'' . $field_id . '\');" />' . (($cnt == 1) ? '<img class="sign" src="./images/info.png" alt="Info icon" title="The contributor fields are optional and every author can have several contributor fields.<br />If you choose to add contributor fields, the only field required is the status. The other are optional." />' : '');
	$author_fields .= '</fieldset>' . "\n";
}

if (empty($author_fields))
{
	if ($submit)
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

// History
$cnt = 0;
while ($history = $parser->get_modx_history())
{
	$temp_fields = $version_warnig = $date_warnig = '';
	$history['version'] = trim($history['version']);
	$history['date'] = trim($history['date']);
	if ($history['version'] != '' || $history['date'] != '' || !empty($history['changelog']))
	{
		if ($history['version'] == '' || !preg_match('#(\d+)\.(\d+)\.\d+[a-z]?#', $history['version']))
		{
			$warning['history']['version'] = 'version';
			$version_warnig = ' class="warning-dl"';
		}
		if ($history['date'] == '')
		{
			$warning['history']['version'] = 'version';
			$date_warnig = ' class="warning-dl"';
		}
		$field_id = 'hf_pre_' . $cnt++;
		$temp_fields .= '<fieldset  class="white" id="d' . $field_id . '"><dl' . $version_warnig . '><dt class="history-rows"><label for="history-' . $field_id . '-version">Version:*</label></dt>';
		$temp_fields .= '<dd class="history-rows"><input name="history[' . $field_id . '][version]" id="history-' . $field_id . '-version" size="10" maxlength="255" value="' . gen_value($history['version']) . '" type="text" /><img class="do-stuff" src="./images/delete.png" alt="" onclick="$(\'#d' . $field_id . '\').remove()" title="Delete" /></dd></dl>';
		$temp_fields .= '<dl' . $date_warnig . '><dt class="history-rows"><label for="history-' . $field_id . '-date">Date:*</label></dt>';
		$temp_fields .= '<dd class="history-rows"><input name="history[' . $field_id . '][date]" id="history-' . $field_id . '-date" size="20" maxlength="255" value="' . gen_value($history['date']) . '" type="text" /></dd></dl>';
		$temp_fields .= '<fieldset id="' . $field_id . '" style="border: none ;">';
		$ccnt = 0;
		$temp_data = '';
		foreach($history['changelog'] as $cval)
		{
			if (trim($cval['change']) != '')
			{
				$temp_id = 'hfc_pre_' . $ccnt++;
				$temp_data .= '<dl id="' . $temp_id . '"><dt class="history-rows"><label for="history-' . $field_id . '-change-' . $temp_id . '-data">Change:' . (($ccnt == 1) ? '*' : '') . '</label></dt><dd class="history-rows">';
				$temp_data .= '<input name="history[' . $field_id . '][changelog][' . $temp_id . '][change]" id="history-' . $field_id . '-change-' . $temp_id . '-data" size="80" maxlength="255" value="' . gen_value($cval['change'], true) . '" type="text" />';
				$temp_data .= '<span><select name="history[' . $field_id . '][changelog][' . $temp_id . '][lang]">' . lang_select($cval['lang']) . '</select></span>';
				$temp_data .= ($ccnt > 1) ? '<img class="do-stuff" src="./images/delete.png" alt="" onclick="$(\'#' . $temp_id . '\').remove()" title="Delete" />' : '';
				$temp_data .= '</dd></dl>';
			}
		}
	}
	if ($history['version'] != '' || $history['date'] != '' || $temp_data != '')
	{
		if ($temp_data == '')
		{
			$temp_id = 'hfc_pre_' . $ccnt++;
			$temp_data .= '<dl class="warning-dl" id="' . $temp_id . '"><dt><label for="history-' . $field_id . '-change-' . $temp_id . '-data">Change:' . (($ccnt == 1) ? '*' : '') . '</label></dt><dd>';
			$temp_data .= '<input name="history[' . $field_id . '][changelog][' . $temp_id . '][change]" id="history-' . $field_id . '-change-' . $temp_id . '-data" size="40" maxlength="255" value="" type="text" />';
			$temp_data .= '<span><select name="history[' . $field_id . '][changelog][' . $temp_id . '][lang]">' . lang_select() . '</select></span>';
			$temp_data .= ($ccnt > 1) ? '<img class="do-stuff" src="./images/delete.png" alt="" onclick="$(\'#' . $temp_id . '\').remove()" title="Delete" />' : '';
			$temp_data .= '</dd></dl>';
		}
		$history_fields .= $temp_fields . $temp_data . '</fieldset><input value="Add change" onclick="add_history_change(\'' . $field_id . '\');" type="button" /></fieldset>';
	}
}
unset($temp_fields, $temp_data, $version_warnig, $date_warnig);

// Links
$cnt = 0;
while ($links = $parser->get_modx_links())
{
	if (trim($links['title']) != '' || trim($links['href']) != '' || trim($links['type']) != '' || trim($links['lang']) != '')
	{
		$field_id = 'lf_pre_' . $cnt++;
		$link_fields .= '<fieldset class="white" id="' . $field_id . '"><dl><dt class="link-rows"><label for="links-' . $field_id . '-type">Type:*</label></dt><dd class="link-rows"><select name="links[' . $field_id . '][type]" id="links-' . $field_id . '-type">';
		$link_fields .= '<option value="contrib"' . (($links['type'] == 'contrib') ? ' selected="selected"' : '') . '>Contrib</option>';
		$link_fields .= '<option value="dependency"' . (($links['type'] == 'dependency') ? ' selected="selected"' : '') . '>Dependency</option>';
		$link_fields .= '<option value="language"' . (($links['type'] == 'language') ? ' selected="selected"' : '') . '>Language</option>';
		$link_fields .= '<option value="parent"' . (($links['type'] == 'parent') ? ' selected="selected"' : '') . '>Parent</option>';
		$link_fields .= '<option value="template-lang"' . (($links['type'] == 'template-lang') ? ' selected="selected"' : '') . '>Template lang</option>';
		$link_fields .= '<option value="template"' . (($links['type'] == 'template') ? ' selected="selected"' : '') . '>Template</option>';
		$link_fields .= '<option value="text"' . (($links['type'] == 'text') ? ' selected="selected"' : '') . '>Text file</option>';
		$link_fields .= '<option value="php-installer"' . (($links['type'] == 'php-installer') ? ' selected="selected"' : '') . '>PHP install file</option>';
		$link_fields .= '</select><img class="do-stuff" src="./images/delete.png" alt="" onclick="$(\'#' . $field_id . '\').remove()" title="Delete this entry" /></dd></dl><dl><dt class="link-rows"><label for="links-' . $field_id . '-title">Link title:*</label></dt><dd class="link-rows">';
		$link_fields .= '<input name="links[' . $field_id . '][title]" id="links-' . $field_id . '-title" size="80" maxlength="255" value="' . gen_value($links['title'], true) . '" type="text" />';
		$link_fields .= '<select name="links[' . $field_id . '][lang]">' . lang_select($links['lang']) . '</select></span>';
		$link_fields .= '</dd></dl><dl><dt class="link-rows"><label for="links-' . $field_id . '-href">URL:*</label></dt><dd class="link-rows">';
		$link_fields .= '<input name="links[' . $field_id . '][href]" id="links-' . $field_id . '-href" size="80" maxlength="255" value="' . gen_value($links['href'], true) . '" type="text" />';
		$link_fields .= (!empty($links['realname'])) ? '<input name="links[' . $field_id . '][realname]" value="' . gen_value($links['realname'], true) . '" type="hidden" />' : '';
		$link_fields .= '</dd></dl></fieldset>';
	}
}

// File copy
$is_copy = false;
$cnt = 0;
while ($copy = $parser->get_modx_copy())
{
	$field_id = 'fc_pre_' . $cnt++;
	if (trim($copy['from']) != '')
	{
		$is_copy = true;
		$copy_fields .= ($cnt == 1) ? '<fieldset class="white" id="dd' . $field_id . '"><dl id="' . $field_id . '"><dt class="copy-rows"><label for="copy-' . $field_id . '-from">Copy: (from -&gt; to)</label><img class="sign plus-sign" src="./images/plus.png" alt="" title="" onclick="add_file_copy(\'' . $field_id . '\');" /></dt>' : '';
		$copy_fields .= '<dd class="copy-rows" id="row' . $field_id . '"><input name="copy[' . $field_id . '][from]" id="copy-' . $field_id . '-from" size="85" maxlength="255" value="' . gen_value($copy['from']) . '" type="text" />';
		$copy_fields .= '<span> -&gt; </span>';
		$copy_fields .= '<input name="copy[' . $field_id . '][to]" size="85" maxlength="255" value="' . gen_value($copy['to']) . '" type="text" /><img class="do-stuff" src="./images/delete.png" alt="" onclick="$(\'#row' . $field_id . '\').remove()" title="Delete" /></dd>';
	}
}
$copy_fields = ($is_copy) ? $copy_fields . (($cnt > 0) ? '</dl></fieldset>' : '') : '';

// SQL querys
$cnt = 0;
while ($sql = $parser->get_modx_sql())
{
	if (trim($sql['query']) != '')
	{
		$field_id = 'sql_pre_' . $cnt++;
		$dbms = ($sql['dbms'] == '' || empty($sql['dbms'])) ? 'sql-parser' : $sql['dbms'];
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
		$sql_fields .= '<textarea name="sql[' . $field_id . '][query]" id="sql_' . $field_id . '_query"  class="sql-rows" rows="' . count_rows($sql['query'], 95) . '">' . gen_value($sql['query']) . '</textarea>';
		$sql_fields .= '<img class="action-text1" src="./images/delete.png" alt="" onclick="$(\'#' . $field_id . '\').remove()" title="Delete" />';
		$sql_fields .= '<img class="action-text2" src="./images/add.png" alt="" onclick="document.mainform.sql_' . $field_id . '_query.rows+=4" title="Add 4 rows" />';
		$sql_fields .= '<img class="action-text3" src="./images/del.png" alt="" onclick="if(document.mainform.sql_' . $field_id . '_query.rows>7){document.mainform.sql_' . $field_id . '_query.rows-=4}else{document.mainform.sql_' . $field_id . '_query.rows-=(document.mainform.sql_' . $field_id . '_query.rows-4)};" title="Remove 4 rows" />';
		$sql_fields .= '</dd></dl></fieldset>';
	}
}

// meta tags
$is_meta = false;
$cnt = 0;
while ($meta = $parser->get_modx_meta())
{
	if (trim($meta['content']) != '' && $meta['content'] != META)
	{
		$is_meta = true;
		$temp_id = 'meta_' . $cnt++;
		$meta_fields .= '<input type="hidden" name="meta[' . $temp_id . '][name]" value="' . gen_value($meta['name']) . '" />';
		$meta_fields .= '<input type="hidden" name="meta[' . $temp_id . '][content]" value="' . gen_value($meta['content']) . '" />';
	}
}
$meta_fields = ($is_meta) ? $meta_fields : '';

// DIY fields
$cnt = 0;
while ($diy = $parser->get_modx_diy())
{
	if (trim($diy['diy']) != '')
	{
		$field_id = 'diy_pre_' . $cnt++;
		$diy_fields .= '<dd id="' . $field_id . '"><textarea name="diy[' . $field_id . '][diy]" id="diy_' . $field_id . '_diy" rows="' . count_rows($diy['diy'], 88) . '">';
		$diy_fields .= gen_value($diy['diy'], true);
		$diy_fields .= '</textarea><span><select name="diy[' . $field_id . '][lang]">' . lang_select($diy['lang']) . '</select></span><img class="action-text1" src="./images/delete.png" alt="" onclick="$(\'#' . $field_id . '\').remove()" title="Delete" />';
		$diy_fields .= '<img id="' . $field_id . '-plus" class="action-text2" src="./images/add.png" alt="" onclick="document.mainform.diy_' . $field_id . '_diy.rows+=4" title="Add 4 rows" />';
		$diy_fields .= '<img id="' . $field_id . '-minus" class="action-text3" src="./images/del.png" alt="" onclick="if(document.mainform.diy_' . $field_id . '_diy.rows>7){document.mainform.diy_' . $field_id . '_diy.rows-=4}else{document.mainform.diy_' . $field_id . '_diy.rows-=(document.mainform.diy_' . $field_id . '_diy.rows-4)};" title="Remove 4 rows" />';
		$diy_fields .= '</dd>';
	}
}

if (empty($diy_fields))
{
	$diy_fields = '<dd id="diy_0"><textarea name="diy[desc_pre][diy]" id="diy_01" rows="5"></textarea><span><select name="diy[desc_pre][lang]">' . lang_select() . '</select></span><img class="action-text1" src="./images/delete.png" alt="" onclick="$(\'#diy_0\').remove()" title="Delete" /><img id="diy0-plus" class="action-text2" src="./images/add.png" alt="" onclick="document.mainform.diy_01.rows+=4" title="Add 4 rows" /><img id="diy0-minus" class="action-text3" src="./images/del.png" alt="" onclick="if(document.mainform.diy_01.rows>7){document.mainform.diy_01.rows-=4}else{document.mainform.diy_01.rows-=(document.mainform.diy_01.rows-4)};" title="Remove 4 rows" /></dd>';
}

// The Action fields...
$modx_fields = '';
$parser->sort_modx_action();
$file_cnt = $edit_cnt = $dl_cnt = $dt_cnt = $dd_cnt = 0;
while ($modx = $parser->get_modx_action())
{
	// Array 1, files
	$file_id = 'f_pre_' . $file_cnt++;
	$dt_id = 'dt_pre_' . $dt_cnt++;
	$modx_fields .= '<fieldset class="inner" id="' . $file_id . '"><dl id="' . $dt_id . '"><dt><label>File to open:</label><img class="sign" src="./images/info.png" alt="" title="Relative path from the phpBB root for the file to open. For example, viewforum.php or includes/functions.php" /></dt>';
	$modx_fields .= '<dd><input name="modx[' . $file_id . '][file]" size="88" value="' . gen_value($modx['file'], true) . '" type="text" /><img class="do-stuff" src="./images/delete.png" alt="" onclick="$(\'#' . $file_id . '\').remove()" title="Delete this file" /></dd></dl>';

	foreach($modx as $key2 => $value2)
	{
		// Array 2, edits We dont need the filenames here
		if (is_int($key2) || $key2 != 'file')
		{
			$edit_id = 'e_pre_' . $edit_cnt++;
			$modx_fields .= '<fieldset class="white" id="' . $edit_id . '"><legend>Edit<img class="sign" src="./images/info.png" alt="" title="Every discreet change to a file must be wrapped in its own edit tag, regardless of the number of children it contains.&lt;br /&gt;All finds within an edit tag should be processed before any action tag." />';
			$modx_fields .= '<img class="do-stuff" src="./images/plus_up.png" alt="" onclick="modx_add_field(\'modx[' . $file_id . ']\', \'' . $edit_id . '\', \'edit\', \'above\', 1)" title="Add an edit field above this edit field" />';
			$modx_fields .= '<img class="do-stuff" src="./images/plus_down.png" alt="" onclick="modx_add_field(\'modx[' . $file_id . ']\', \'' . $edit_id . '\', \'edit\', \'below\', 1)" title="Add an edit field below this edit field" />';
			$modx_fields .= '<img class="do-stuff" src="./images/delete.png" alt="" onclick="$(\'#' . $edit_id . '\').remove()" title="Delete this edit" /></legend><p>NOTE: Each discreet change to a file must be wrapped in its own edit tag.</p>';

			foreach($value2 as $key3 => $value3)
			{
				// Array 3, dl's The string fields
				if ($value3['type'] != '' && isset($value3['data']))
				{
					$dl_id = 'dl_pre_' . $dl_cnt++;
					$dt_id = 'dt_pre_' . $dt_cnt++;
					$dd_id = 'dd_pre_' . $dd_cnt++;

					// Need to get the right input field here to view the right input and image...
					// We'll do it here so we don't have to do the checks twice
					if (strpos($value3['type'], 'inline') !== FALSE)
					{
						$textarea = false;
						// Inline stuff can only be one-liners
						sanitize_inlines($value3['data']);
						$modx_input = '<span id="' . $dd_id . '_field"><textarea id="' . $dd_id . '_data" name="modx[' . $file_id . '][' . $edit_id . '][' . $dl_id . '][data]" rows="0" onKeypress="if((event.keyCode == 10) || (event.keyCode == 13)){return false;}">' . gen_value($value3['data']) . '</textarea></span>';
						$modx_img = '<img id="' . $dd_id . '_info" class="action-arrows" src="./images/info_8.png" alt="" title="Note that the inline tags may not contain line breaks" />';
					}
					else if ($value3['type'] != 'comment')
					{
						$textarea = true;
						$modx_input = '<span id="' . $dd_id . '_field"><textarea class="action-texts" id="' . $dd_id . '_data" name="modx[' . $file_id . '][' . $edit_id . '][' . $dl_id . '][data]" rows="' . count_rows($value3['data'], 85) . '">' . gen_value($value3['data']) . '</textarea></span>';
						if ($value3['type'] == 'find')
						{
							$modx_img = '<img id="' . $dd_id . '_info" class="action-arrows" src="./images/info_2.png" alt="" title="Find tags in the MODX file should be in the order that the find targets appear in the file. In other words, a processor of the MODX file should never need to go backwards in the file to locate all of the finds. When there are multiple finds within a single edit tag, the processor should handle all finds before any actions." />';
						}
						else
						{
							$modx_img = '<img id="' . $dd_id . '_info" class="action-arrows" src="./images/info_12.png" alt="" title="The string to add before the find, add after the find, replace the find with, or the operation string described above.&lt;br /&gt;&lt;br /&gt;The syntax for the operation action is a bit obscure because it uses tokens. The find action would have a token like this&lt;br /&gt;colspan=&quot;{:%1}&quot;&lt;br /&gt;The operation action would look like this:&lt;br /&gt;{:%1} + 1" />';
						}
					}
					else
					{
						$textarea = true;
						// The comments needs a lang select
						$modx_input = '<span id="' . $dd_id . '_field"><textarea class="action-comment" id="' . $dd_id . '_data" name="modx[' . $file_id . '][' . $edit_id . '][' . $dl_id . '][data]" rows="' . count_rows($value3['data'], 70) . '">' . gen_value($value3['data']) . '</textarea>';
						$modx_input .= '<span id="' . $dd_id . '_lang"><select name="modx[' . $file_id . '][' . $edit_id . '][' . $dl_id . '][lang]">' . lang_select($value3['lang']) . '</select></span></span>';
						$modx_img = '<img id="' . $dd_id . '_info" class="action-arrows" src="./images/info_17.png" alt="" title="Comment pertaining to this edit" />';
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
					$modx_fields .= '<br /><img class="action-arrows" src="./images/plus_up.png" alt="" onclick="modx_add_field(\'modx[' . $file_id . '][' . $edit_id . ']\', \'' . $dl_id . '\', \'dl\', \'above\')" title="Add action above" />';
					$modx_fields .= '<img class="action-arrows" src="./images/plus_down.png" alt="" onclick="modx_add_field(\'modx[' . $file_id . '][' . $edit_id . ']\', \'' . $dl_id . '\', \'dl\', \'below\')" title="Add action below" />';
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

$modx_url = $error_field = '';
if ($dload || $preview)
{
	if (!empty($error))
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
