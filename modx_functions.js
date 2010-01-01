/**
*
* @package MODX creator
* @version $Id$
* @copyright (c) 2009 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

var field_cnt = 1;
var file_cnt = 1;
var edit_cnt = 1;
var dl_cnt = 1;
var dt_cnt = 1;
var dd_cnt = 1;
var copy_field = '';

/**
* Change the input type depending on what is selected
*/
function get_select_change(value, dt_id, dd_id, file_name)
{
	var data_type = 0;
	var row_count;
	var info_img = '';
	var tmp_data = '';
	var tmp_id = dd_id + '_data';

	// Remove the datafield so that we can change to the one we want
	if (document.getElementById(dd_id + '_field'))
	{
		if(document.getElementById(dd_id + '_data').value)
		{
			tmp_data = document.getElementById(dd_id + '_data').value;
		}
		$('#' + dd_id + '_field').remove();
	}

	if($('#' + dd_id + '_info'))
	{
		$('#' + dd_id + '_info').remove();
	}

	if($('#' + dd_id + '-plus'))
	{
		$('#' + dd_id + '-plus').remove();
	}

	if($('#' + dd_id + '-minus'))
	{
		$('#' + dd_id + '-minus').remove();
	}

	switch(value)
	{
		case 'find':
			data_type = 1;
			info_img = '<img id="' + dd_id + '_info" class="action-arrows" src="./images/info_2.png" alt="Find explain" title="Find tags in the MODX file should be in the order that the find targets appear in the file. In other words, a processor of the MODX file should never need to go backwards in the file to locate all of the finds. When there are multiple finds within a single edit tag, the processor should handle all finds before any actions." />';
		break;

		case 'after-add':
		case 'before-add':
		case 'replace-with':
		case 'operation':
			data_type = 1;
			info_img = '<img id="' + dd_id + '_info" class="action-arrows" src="./images/info_12.png" alt="Action explain" title="the string to add before the find, add after the find, replace the find with, or the operation string." />';
		break;

		case 'inline-find':
		case 'inline-operation':
		case 'inline-replace-with':
		case 'inline-before-add':
		case 'inline-after-add':
			data_type = 2;
			info_img = '<img id="' + dd_id + '_info" class="action-arrows" src="./images/info_8.png" alt="Inline explain" title="Note that the inline tags may not contain line breaks." />';
		break;

		default:
			data_type = 3;
			info_img = '<img id="' + dd_id + '_info" class="action-arrows" src="./images/info_17.png" alt="Comment explain" title="Comment pertaining to this edit." />';
		break;
	}

	switch(data_type)
	{
		case 1:
			var element = '<span class="" id="' + dd_id + '_field">';
				element += '<textarea id="' + dd_id + '_data" name="' + file_name + '[data]" rows="' + count_rows(tmp_data, 85, 20, 4) + '">' + tmp_data + '</textarea>';
				element += '<img class="action-text2" id="' + dd_id + '-plus" src="./images/add.png" onclick="document.mainform.' + dd_id + '_data.rows+=4" alt="Add 4 rows" title="Add 4 rows to the textfield" />';
				element += '<img class="action-text3" id="' + dd_id + '-minus" src="./images/del.png" alt="Remove 4 rows" title="Remove 4 rows" onclick="if(document.mainform.' + dd_id + '_data.rows>7){document.mainform.' + dd_id + '_data.rows-=4}else{document.mainform.' + dd_id + '_data.rows-=(document.mainform.' + dd_id + '_data.rows-4)};" />';
			element += '</span>';
		break;

		case 2:
			var element = '<span id="' + dd_id + '_field">';
				element += '<textarea id="' + dd_id + '_data" name="' + file_name + '[data]" rows="1"  onKeypress="if((event.keyCode == 10) || (event.keyCode == 13)){return false;}">' + tmp_data + '</textarea>';
			element += '</span>';
		break;

		default:
			var element = '<span class="" id="' + dd_id + '_field">';
				element += '<textarea class="action-comment" id="' + dd_id + '_data" name="' + file_name + '[data]" rows="' + count_rows(tmp_data, 70, 20, 4) + '">' + tmp_data + '</textarea>';
				element += '<span class="" id="' + dd_id + '_lang">';
					element += lang_select(file_name + '[lang]');
				element += '</span>';
				element += '<img class="action-text2" id="' + dd_id + '-plus" src="./images/add.png" alt="Add 4 rows" onclick="document.mainform.' + dd_id + '_data.rows+=4" title="Add 4 rows" />';
				element += '<img class="action-text3" id="' + dd_id + '-minus" src="./images/del.png" alt="Remove 4 rows" title="Remove 4 rows" onclick="if(document.mainform.' + dd_id + '_data.rows>7){document.mainform.' + dd_id + '_data.rows-=4}else{document.mainform.' + dd_id + '_data.rows-=(document.mainform.' + dd_id + '_data.rows-4)};" />';
			element += '</span>';
		break;
	}

	$('#' + dt_id).append(info_img);
	$('#' + dd_id).prepend(element);
}

/**
* Generate new fields for the actions
*/
function modx_add_field(obj_id, parent_id, sort, position, if_edit)
{
	var edit_id = 'e_' + edit_cnt++;
	var dl_id = 'edl_' + dl_cnt++;
	var dd_id = 'edd_' + dd_cnt++;
	var dt_id = 'edt_' + dt_cnt++;
	var new_edit = (if_edit) ? '[' + edit_id + ']' : '';

	if(sort == 'edit')
	{
		var element = '<fieldset class="white" id="' + edit_id + '">';
			element += '<legend>Edit';
				element += '<img class="sign" id="" src="./images/info.png" alt="Find info" title="Every discreet change to a file must be wrapped in its own edit tag, regardless of the number of children it contains. All finds within an edit tag should be processed before any action tag." />';
				element += '<img class="do-stuff" id="" src="./images/plus_up.png" alt="Arrow up icon" title="Add a edit field above this edit field" onclick="modx_add_field(\'' + obj_id + '\', \'' + edit_id + '\', \'edit\', \'above\', 1);" />';
				element += '<img class="do-stuff" id="" src="./images/plus_down.png" alt="Arrow down icon" title="Add a edit field below this edit field" onclick="modx_add_field(\'' + obj_id + '\', \'' + edit_id + '\', \'edit\', \'below\', 1)" />';
				element += '<img class="do-stuff" id="" src="./images/delete.png" alt="Delete icon" title="Delete this edit" onclick="$(\'#' + edit_id + '\').remove()" />';
			element += '</legend>';
			element += '<p class="">NOTE: Each discreet change to a file must be wrapped in its own edit tag.</p>';
			element += '<dl class="" id="' + dl_id + '" style="white-space: nowrap;">';
				element += '<dt class="" id="' + dt_id + '">';
					element += '<label>Type:</label>';
					element += '<span>';
						element += modx_select(obj_id + '[' + edit_id + '][' + dl_id + ']', dt_id, dd_id);
					element += '</span><br />';
					element += '<img class="action-arrows" id="" src="./images/plus_up.png" alt="Arrow up icon" title="" onclick="modx_add_field(\'' + obj_id + new_edit + '\', \'' + dl_id + '\', \'dl\', \'above\', 0)" />';
					element += '<img class="action-arrows" id="" src="./images/plus_down.png" alt="Arrow down icon" title="Add action below" onclick="modx_add_field(\'' + obj_id + new_edit + '\', \'' + dl_id + '\', \'dl\', \'below\', 0" />';
					element += '<img class="action-arrows" id="' + dd_id + '_info" src="./images/info.png" alt="Info icon" title="Select the type for this action" onclick="" />';
				element += '</dt>';
				element += '<dd class="" id="' + dd_id + '">';
					element += '<span class="" id="' + dd_id + '_field">';
						element += '<input type="text" name="' + obj_id + new_edit + '[' + dl_id + '][data]" class="" id="' + dd_id + '_data" disabled="disabled" size="30" value="" />';
					element += '</span>';
					element += '<img class="do-stuff" id="" src="./images/delete.png" alt="Delete icon" title="Delete" onclick="$(\'#' + dl_id + '\').remove()" />';
				element += '</dd>';
			element += '</dl>';
		element += '</fieldset>';
	}
	else
	{
		var element = '<dl class="" id="' + dl_id + '" style="white-space: nowrap;">';
			element += '<dt class="" id="' + dt_id + '">';
				element += '<label class="">Type:</label>';
				element += '<span class="" id="">';
					element += modx_select(obj_id + '[' + dl_id + ']', dt_id, dd_id);
				element += '</span><br />';
				element += '<img class="action-arrows" id="" src="./images/plus_up.png" alt="Arrow up icon" title="Add action above" onclick="modx_add_field(\'' + obj_id + new_edit + '\', \'' + dl_id + '\', \'dl\', \'above\', 0)" />';
				element += '<img class="action-arrows" id="" src="./images/plus_down.png" alt="Arrow down icon" title="Add action below" onclick="modx_add_field(\'' + obj_id + new_edit + '\', \'' + dl_id + '\', \'dl\', \'below\', 0)" />';
				element += '<img class="action-arrows" id="' + dd_id + '_info" src="./images/info.png" alt="Info icon" title="Select the type for the string field" onclick="" />';
			element += '</dt>';
			element += '<dd class="" id="' + dd_id + '">';
				element += '<span class="" id="' + dd_id + '_field">';
					element += '<input type="text" class="" id="' + dd_id + '_data" name="' + obj_id + '[' + dl_id + '][data]" size="30" value="" disabled="disabled" />';
				element += '</span>';
				element += '<img class="do-stuff" id="" src="./images/delete.png" alt="Delete icon" title="Delete" onclick="$(\'#' + dl_id + '\').remove()" />';
			element += '</dd>';
		element += '</dl>';
	}

	if(position == 'above')
	{
		$('#' + parent_id).before(element);
	}
	else
	{
		$('#' + parent_id).after(element);
	}
}

/**
* Generate the type select
*/
function modx_select(file_name, dt_id, dd_id)
{
	var element = '<select class="krav" name="' + file_name + '[type]" onchange="if(this.options[this.selectedIndex].value != \'-\'){ get_select_change(this.options[this.selectedIndex].value, \'' + dt_id + '\', \'' + dd_id + '\', \'' + file_name + '\') }">';
		element += '<option value="comment">Comment</option>';
		element += '<option value="find">Find</option>';
		element += '<option value="after-add">After add</option>';
		element += '<option value="before-add">Before add</option>';
		element += '<option value="replace-with">Replace with</option>';
		element += '<option value="operation">Operation</option>';
		element += '<option value="inline-find">Inline find</option>';
		element += '<option value="inline-after-add">Inline after add</option>';
		element += '<option value="inline-before-add">Inline before add</option>';
		element += '<option value="inline-replace-with">Inline replace with</option>';
		element += '<option value="inline-operation">Inline operation</option>';
		element += '<option value="-" selected="selected">Select type</option>';
	element += '</select>';

	return(element);
}

function act_file()
{
	var file_id = 'f_' + file_cnt++;
	var edit_id = 'e_' + edit_cnt++;
	var dl1_id = 'edl_' + dl_cnt++;
	var dl2_id = 'edl_' + dl_cnt++;
	var dd_id = 'edd_' + dd_cnt++;
	var dt_id = 'edt_' + dt_cnt++;

	var element = '<fieldset class="inner" id="' + file_id + '">';
		element += '<dl class="" id="' + dl1_id + '">';
			element += '<dt class="" id="">';
				element += '<label class="" for="modx-' + file_id + '-file">File to open:</label>';
				element += '<img class="sign" id="" src="./images/info.png" alt="Info icon" title="Relative path from the phpBB root for the file to open. For example, viewforum.php or includes/functions.php" onclick="" />';
			element += '</dt>';
			element += '<dd class="" id="">';
				element += '<input type="text" class="" id="modx-' + file_id + '-file" name="modx[' + file_id + '][file]" size="88" value="" />';
				element += '<img class="do-stuff" id="" src="./images/delete.png" alt="Delete icon" title="Delete this file" onclick="$(\'#' + file_id + '\').remove()" />';
			element += '</dd>';
		element += '</dl>';
		element += '<fieldset class="white" id="' + edit_id + '">';
			element += '<legend class="">Edit';
				element += '<img class="sign" id="" src="./images/info.png" alt="Info icon" title="Every discreet change to a file must be wrapped in its own edit tag, regardless of the number of children it contains. All finds within an edit tag should be processed before any action tag." onclick="" />';
				element += '<img class="do-stuff" id="" src="./images/plus_up.png" alt="Arrow up icon" title="Add edit above" onclick="modx_add_field(\'modx[' + file_id + ']\', \'' + edit_id + '\', \'edit\', \'above\', 1)" />';
				element += '<img class="do-stuff" id="" src="./images/plus_down.png" alt="Arrow down icon" title="Add edit below" onclick="modx_add_field(\'modx[' + file_id + ']\', \'' + edit_id + '\', \'edit\', \'below\', 1)" />';
				element += '<img class="do-stuff" id="" src="./images/delete.png" alt="Delete icon" title="Delete this edit" onclick="$(\'#' + edit_id + '\').remove()" />';
			element += '</legend>';
			element += '<p class="">NOTE: Each discreet change to a file must be wrapped in its own edit tag.</p>';
			element += '<dl class="" id="' + dl2_id + '" style="white-space: nowrap;">';
				element += '<dt class="" id="' + dt_id + '">';
					element += '<label class="" for="">Type:</label>';
					element += '<span class="" id="">';
						element += modx_select('modx[' + file_id + '][' + edit_id + '][' + dl2_id + ']', dt_id, dd_id);
					element += '</span><br />';
					element += '<img class="action-arrows" id="" src="./images/plus_up.png" alt="Arrow up icon" title="Add action above" onclick="modx_add_field(\'modx[' + file_id + '][' + edit_id + ']\', \'' + dl2_id + '\', \'dl\', \'above\', 0)" />';
					element += '<img class="action-arrows" id="" src="./images/plus_down.png" alt="Arrow down icon" title="Add action below" onclick="modx_add_field(\'modx[' + file_id + '][' + edit_id + ']\', \'' + dl2_id + '\', \'dl\', \'below\', 0)" />';
					element += '<img class="action-arrows" id="' + dd_id + '_info" src="./images/info.png" alt="Info icon" title="Select the type for this action." onclick="" />';
				element += '</dt>';
				element += '<dd class="" id="' + dd_id + '">';
					element += '<span class="" id="' + dd_id + '_field">';
						element += '<input type="text" class="" id="' + dd_id + '_data" name="modx[' + file_id + '][' + edit_id + '][' + dl2_id + '][data]" size="30" value="" disabled="disabled" />';
					element += '</span>';
					element += '<img class="do-stuff" id="" src="./images/delete.png" alt="Delete icon" title="Delete" onclick="$(\'#' + dl2_id + '\').remove()" />';
				element += '</dd>';
			element += '</dl>';
		element += '</fieldset>';
	element += '</fieldset>';

	$('#modx-field').append(element);
}

/**
* Add author field(s)
*/
function add_author()
{
	var field_id = 'author_' + field_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = '<fieldset class="white" id="' + dd_id + '">';
		element += '<dl class="" id="">';
			element += '<dt class="author-rows" id="">';
				element += '<label class="" for="author-' + field_id + '-username">Username:*</label>';
			element += '</dt>';
			element += '<dd class="author-rows" id="">';
				element += '<input type="text" class="" id="author-' + field_id + '-username" name="author[' + field_id + '][username]" size="40" value="" maxlength="255" />';
				element += '<img class="do-stuff" id="" src="./images/delete.png" alt="Delete icon" title="Delete this author" onclick="$(\'#' + dd_id + '\').remove()" />';
			element += '</dd>';
		element += '</dl>';

		element += '<dl class="" id="">';
			element += '<dt class="author-rows" id="">';
				element += '<label class="" for="author-' + field_id + '-phpbbcom">Not phpBB.com:</label>';
			element += '</dt>';
			element += '<dd class="author-rows" id="">';
				element += '<label class="" for="">';
					element += '<input type="checkbox" id="author-' + field_id + '-phpbbcom" name="author[' + field_id + '][phpbbcom]" />';
					element += '<span style="font-size: 12px;" id="">(Check here if this author is not registered at phpbb.com.)</span>';
				element += '</label>';
			element += '</dd>';
		element += '</dl>';

		element += '<dl class="" id="">';
			element += '<dt class="author-rows" id="">';
				element += '<label class="" for="author-' + field_id + '-realname">Real name:</label>';
			element += '</dt>';
			element += '<dd class="author-rows" id="">';
				element += '<input type="text" class="" id="author-' + field_id + '-realname" name="author[' + field_id + '][realname]" size="40" maxlength="255" value="" />';
			element += '</dd>';
		element += '</dl>';

		element += '<dl class="" id="">';
			element += '<dt class="author-rows" id="">';
				element += '<label class="" for="author-' + field_id + '-homepage">Homepage:</label>';
			element += '</dt>';
			element += '<dd class="author-rows" id="">';
				element += '<input type="text" class="" id="author-' + field_id + '-homepage" name="author[' + field_id + '][homepage]" size="40" maxlength="255" value="" />';
			element += '</dd>';
		element += '</dl>';

		element += '<dl class="" id="">';
			element += '<dt class="author-rows" id="">';
				element += '<label class="" for="author-' + field_id + '-email">E-mail:</label>';
			element += '</dt>';
			element += '<dd class="author-rows" id="">';
				element += '<input type="text" class="" id="author-' + field_id + '-email" name="author[' + field_id + '][email]" size="40" maxlength="255" value="" />';
			element += '</dd>';
		element += '</dl>';

		element += '<fieldset style="border: none;" id="' + field_id + '"></fieldset>';
		element += '<input type="button" class="" id="" onclick="add_contributor(\'' + field_id + '\');" value="Add contribution" />';
	element += '</fieldset>';

	$('#authors').append(element);
}

/**
* Add a contributor-field
*/
function add_contributor(field_id)
{
	var temp = 'tm_' + field_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = '<fieldset class="noborder" id="' + dd_id + '">';
		element += '<dl class="" id="">';
			element += '<dt class="author-rows" id="">';
				element += '<label class="" for="contributor-' + field_id + '-' + temp + '-status">Status:*</label>';
			element += '</dt>';
			element += '<dd class="author-rows" id="">';
				element += '<select class="" name="author[' + field_id + '][contributions][' + temp + '][status]" id="contributor-' + field_id + '-' + temp + '-status">';
					element += '<option value="past">Past</option>';
					element += '<option value="current">Current</option>';
				element += '</select>';
				element += '<img class="do-stuff" id="" src="./images/delete.png" alt="Delete icon" title="Delete" onclick="$(\'#' + dd_id + '\').remove()" />';
			element += '</dd>';
		element += '</dl>';

		element += '<dl class="" id="">';
			element += '<dt class="author-rows" id="">';
				element += '<label class="" for="contributor-' + field_id + '-' + temp + '-position">Position:</label>';
			element += '</dt>';
			element += '<dd class="author-rows" id="">';
				element += '<input type="text" class="" id="contributor-' + field_id + '-' + temp + '-position" name="author[' + field_id + '][contributions][' + temp + '][position]" size="40" maxlength="255" value="" />';
			element += '</dd>';
		element += '</dl>';

		element += '<dl class="" id="">';
			element += '<dt class="author-rows" id="">';
				element += '<label class="" for="contributor-' + field_id + '-' + temp + '-from">From date:</label>';
			element += '</dt>';
			element += '<dd class="author-rows" id="">';
				element += '<input type="text" class="" id="contributor-' + field_id + '-' + temp + '-from" name="author[' + field_id + '][contributions][' + temp + '][from]" size="40" maxlength="255" value="" />';
			element += '</dd>';
		element += '</dl>';

		element += '<dl class="" id="">';
			element += '<dt class="author-rows" id="">';
				element += '<label class="" for="contributor-' + field_id + '-' + temp + '-to">To date:</label>';
			element += '</dt>';
			element += '<dd class="author-rows" id="">';
				element += '<input type="text" class="" id="contributor-' + field_id + '-' + temp + '-to" name="author[' + field_id + '][contributions][' + temp + '][to]" size="40" maxlength="255" value="" />';
			element += '</dd>';
		element += '</dl>';
	element += '</fieldset>';

	$('#' + field_id).append(element);
}

/**
* Add description
*/
function add_desc()
{
	var field_id = 'desc_' + field_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = '<dd class="" id="' + dd_id + '">';
		element += '<textarea class="" id="desc_' + field_id + '_desc" name="desc[' + field_id + '][desc]" rows="5"></textarea>';
		element += '<span class="" id="">';
			element += lang_select('desc[' + field_id + '][lang]');
		element += '</span>';
		element += '<img class="action-text1" id="" src="./images/delete.png" alt="Delete icon" title="Delete" onclick="$(\'#' + dd_id + '\').remove()" />';
		element += '<img class="action-text2" id="' + dd_id + '-plus" src="./images/add.png" alt="Add 4 rows" title="Add 4 rows" onclick="document.mainform.desc_' + field_id + '_desc.rows+=4" />';
		element += '<img class="action-text3" id="' + dd_id + '-minus" src="./images/del.png" alt="Remove 4 rows" title="Remove 4 rows" onclick="if(document.mainform.desc_' + field_id + '_desc.rows>7){document.mainform.desc_' + field_id + '_desc.rows-=4}else{document.mainform.desc_' + field_id + '_desc.rows-=(document.mainform.desc_' + field_id + '_desc.rows-4)};" />';
	element += '</dd>';

	$('#desc-field').append(element);
}

/**
* Add copy-field
*/
function add_copy()
{
	var a_new_one = false;
	if (copy_field == '')
	{
		copy_field = 'fc_' + field_cnt++;
		a_new_one = true;
	}

	var field_id = 'fc_' + field_cnt++;
	var temp = 'tm_' + field_cnt++;
	var dd_id = 'dd_' + dd_cnt++;
	var dd_id2 = 'dd_' + dd_cnt++;
	var element = '';

	if(a_new_one)
	{
		element += '<fieldset class="white" id="' + dd_id + '">';
			element += '<dl class="" id="' + copy_field + '">';
				element += '<dt class="copy-rows" id="">';
					element += '<label class="" for="copy-' + copy_field + '-' + temp + '-from">Copy: (from -&gt; to)</label>';
					element += '<img class="sign plus-sign" id="" src="./images/plus.png" alt="Add file copy" title="Add file copy" onclick="add_file_copy(\'' + copy_field + '\');" />';
					element += '<img class="do-stuff" id="" src="./images/delete.png" alt="Delete icon" title="Delete" onclick="$(\'#' + dd_id + '\').remove(); copy_field = \'\';" />';
				element += '</dt>';
	}

			element += '<dd class="copy-rows" id="' + dd_id2 + '">';
				element += '<input type="text" class="" id="copy-' + copy_field + '-' + temp + '-from" name="copy[' + temp + '][from]" size="85" maxlength="255" value="" />';
				element += '<span class="" id=""> -> </span>';
				element += '<input type="text" class="" id="" name="copy[' + temp + '][to]" size="85" maxlength="255" value="" />';
				element += '<img class="do-stuff" id="" src="./images/delete.png" alt="Delete icon" title="Delete" onclick="$(\'#' + dd_id2 + '\').remove()" />';
			element += '</dd>';

	if(a_new_one)
	{
			element += '</dl>';
		element += '</fieldset>';

		$('#copy-field').append(element);
	}
	else
	{
		$('#' + copy_field).append(element);
	}
}

/**
* Add copy-field
*/
function add_file_copy(field_id)
{
	temp = 'tm_' + field_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = '<dd class="copy-rows" id="' + dd_id + '">';
		element += '<span style="margin-top: 3px" id="">';
			element += '<input type="text" class="" id="" name="copy[' + field_id + '][' + temp + '][from]" size="85" maxlength="255" value="" />';
			element += '<span class="" id=""> -> </span>';
			element += '<input type="text" class="" id="" name="copy[' + field_id + '][' + temp + '][to]" size="85" maxlength="255" value="" />';
			element += '<img class="do-stuff" id="" src="./images/delete.png" alt="Delete icon" title="Delete" onclick="$(\'#' + dd_id + '\').remove()" />';
		element += '</span>';
	element += '</dd>';

	$('#' + field_id).append(element);
}

/**
* Add a history-field
*/
function add_history()
{
	var field_id = 'hf_' + field_cnt++;
	var temp = 'logdd_' + dd_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = '<fieldset class="white" id="' + dd_id + '">';
		element += '<dl class="" id="">';
			element += '<dt class="history-rows" id="">';
				element += '<label class="" for="history-' + field_id + '-version">Version:*</label>';
			element += '</dt>';
			element += '<dd class="history-rows" id="">';
				element += '<input type="text" class="" id="history-' + field_id + '-version" name="history[' + field_id + '][version]" size="10" maxlength="255" value="" />';
				element += '<img class="do-stuff" id="" src="./images/delete.png" alt="Delete icon" title="Delete this entry" onclick="$(\'#' + dd_id + '\').remove()" />';
			element += '</dd>';
		element += '</dl>';

		element += '<dl class="" id="">';
			element += '<dt class="history-rows" id="">';
				element += '<label class="" for="history-' + field_id + '-date">Date:*</label>';
			element += '</dt>';
			element += '<dd class="history-rows" id="">';
				element += '<input type="text" class="" id="history-' + field_id + '-date" name="history[' + field_id + '][date]" size="20" maxlength="255" value="" />';
			element += '</dd>';
		element += '</dl>';

		element += '<fieldset style="border: none;" id="' + field_id + '">';
			element += '<dl class="" id="">';
				element += '<dt class="history-rows" id="">';
					element += '<label class="" for="history-' + field_id + '-change-' + temp + '-data">Change:*</label>';
				element += '</dt>';
				element += '<dd class="history-rows" id="">';
					element += '<input type="text" class="" id="history-' + field_id + '-change-' + temp + '-data" name="history[' + field_id + '][changelog][' + temp + '][change]" size="80" maxlength="255" value="" />';
					element += '<span class="" id="">' + lang_select('history[' + field_id + '][changelog][' + temp + '][lang]') + '</span>';
				element += '</dd>';
			element += '</dl>';
		element += '</fieldset>';

		element += '<input type="button" onclick="add_history_change(\'' + field_id + '\');" value="Add change" />';
	element += '</fieldset>';

	$('#history-fields').append(element);
}

/**
* Add a change to the history
*/
function add_history_change(field_id)
{
	var temp = 'logdd_' + dd_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = '<dl class="" id="' + dd_id + '">';
		element += '<dt class="history-rows" id="">';
			element += '<label class="" for="history-' + field_id + '-change-' + temp + '-data">Change:</label>';
		element += '</dt>';
		element += '<dd class="history-rows" id="">';
			element += '<input type="text" class="" id="history-' + field_id + '-change-' + temp + '-data" name="history[' + field_id + '][changelog][' + temp + '][change]" size="80" maxlength="255" value="" />';
			element += '<span>' + lang_select('history[' + field_id + '][changelog][' + temp + '][lang]') + '</span>';
			element += '<img class="do-stuff" id="" src="./images/delete.png" alt="Delete icon" title="Delete" onclick="$(\'#' + dd_id + '\').remove()" />';
		element += '</dd>';
	element += '</dl>';

	$('#' + field_id).append(element);
}

/**
* Add link-field
*/
function add_link()
{
	var field_id = 'lf_' + field_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = '<fieldset class="white" id="' + dd_id + '">';
		element += '<dl class="" id="">';
			element += '<dt class="link-rows" id="">';
				element += '<label class="" for="links-' + field_id + '-type">Type:*</label>';
			element += '</dt>';
			element += '<dd class="link-rows" id="">';
				element += '<select name="links[' + field_id + '][type]" id="links-' + field_id + '-type">';
					element += '<option value="contrib" selected="selected">Contrib</option>';
					element += '<option value="dependency">Dependency</option>';
					element += '<option value="language">Language</option>';
					element += '<option value="parent">Parent</option>';
					element += '<option value="template-lang">Template_lang</option>';
					element += '<option value="template">Template</option>';
				element += '</select>';
				element += '<img class="do-stuff" id="" src="./images/delete.png" alt="Delete icon" title="Delete this entry" onclick="$(\'#' + dd_id + '\').remove()" />';
			element += '</dd>';
		element += '</dl>';

		element += '<dl class="" id="">';
			element += '<dt class="link-rows" id="">';
				element += '<label class="" for="links-' + field_id + '-title">Link title:*</label>';
			element += '</dt>';
			element += '<dd class="link-rows" id="">';
				element += '<input type="text" class="" id="links-' + field_id + '-title" name="links[' + field_id + '][title]" size="80" maxlength="255" value="" />';
				element += '<span>' + lang_select('links[' + field_id + '][lang]') + '</span>';
			element += '</dd>';
		element += '</dl>';

		element += '<dl class="" id="">';
			element += '<dt class="link-rows" id="">';
				element += '<label class="" for="links-' + field_id + '-href">URL:*</label>';
			element += '</dt>';
			element += '<dd class="link-rows" id="">';
				element += '<input type="text" class="" id="links-' + field_id + '-href" name="links[' + field_id + '][href]" size="80" maxlength="255" value="" />';
			element += '</dd>';
		element += '</dl>';
	element += '</fieldset>';

	$('#link-field').append(element);
}

/**
* Add notes
*/
function add_notes()
{
	var field_id = 'notes_' + field_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = '<dd class="" id="' + dd_id + '">';
		element += '<textarea class="" id="notes_' + field_id + '_note" name="notes[' + field_id + '][note]" rows="5"></textarea>';
		element += '<span>' + lang_select('notes[' + field_id + '][lang]') + '</span>';
		element += '<img class="action-text1" id="" src="./images/delete.png" alt="Delete icon" title="Delete" onclick="$(\'#' + dd_id + '\').remove()" />';
		element += '<img class="action-text2" id="' + dd_id + '-plus" src="./images/add.png" alt="" title="Add 4 rows" onclick="document.mainform.notes_' + field_id + '_note.rows+=4" />';
		element += '<img class="action-text3" id="' + dd_id + '-minus" src="./images/del.png" alt="" title="Remove 4 rows" onclick="if(document.mainform.notes_' + field_id + '_note.rows>7){document.mainform.notes_' + field_id + '_note.rows-=4}else{document.mainform.notes_' + field_id + '_note.rows-=(document.mainform.notes_' + field_id + '_note.rows-4)};" />';
	element += '</dd>';

	$('#notes-field').append(element);
}

/**
* Add diy
*/
function add_diy()
{
	var field_id = 'diy_' + field_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = '<dd class="" id="' + dd_id + '">';
		element += '<textarea class="" id="diy_' + field_id + '_diy" name="diy[' + field_id + '][diy]" rows="5"></textarea>';
		element += '<span>' + lang_select('diy[' + field_id + '][lang]') + '</span>';
		element += '<img class="action-text1" id="" src="./images/delete.png" alt="Delete icon" title="Delete" onclick="$(\'#' + dd_id + '\').remove()" />';
		element += '<img class="action-text2" id="' + dd_id + '-plus" src="./images/add.png" alt="Add 4 rows" title="Add 4 rows" onclick="document.mainform.diy_' + field_id + '_diy.rows+=4" />';
		element += '<img class="action-text3" id="' + dd_id + '-minus" src="./images/del.png" alt="Remove 4 rows" title="Remove 4 rows" onclick="if(document.mainform.diy_' + field_id + '_diy.rows>7){document.mainform.diy_' + field_id + '_diy.rows-=4}else{document.mainform.diy_' + field_id + '_diy.rows-=(document.mainform.diy_' + field_id + '_diy.rows-4)};" />';
	element += '</dd>';

	$('#diy-field').append(element);
}

/**
* Add SQL-field
*/
function add_sql()
{
	var field_id = 'sql_' + field_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = '<fieldset class="white" id="' + dd_id + '">';
		element += '<dl class="" id="">';
			element += '<dt class="sql-rows" id="">';
				element += '<label class="" for="sql-' + field_id + '-dbms">DBMS:</label>';
			element += '</dt>';
			element += '<dd class="sql-rows" id="">';
				element += '<select name="sql[' + field_id + '][dbms]" id="sql-' + field_id + '-dbms">';
					element += '<option value="mysql_40">MySQL 4.0</option>';
					element += '<option value="mysql_41">MySQL 4.1</option>';
					element += '<option value="mssaccess">MSSQL</option>';
					element += '<option value="oracle">Oracle</option>';
					element += '<option value="postgres">PostgreSQL</option>';
					element += '<option value="firebird">FireBird</option>';
					element += '<option value="sqlite">SQLite</option>';
					element += '<option value="sql-parser" selected="selected">SQL Parser (Default)</option>';
				element += '</select>';
			element += '</dd>';
		element += '</dl>';

		element += '<dl class="" id="">';
			element += '<dt class="sql-rows" id="">';
				element += '<label class="" for="sql-' + field_id + '-query">Query:*</label>';
			element += '</dt>';
			element += '<dd class="sql-rows" id="">';
				element += '<textarea class="sql-rows" id="sql_' + field_id + '_query" name="sql[' + field_id + '][query]" rows="5"></textarea>';
				element += '<img class="action-text1" id="" src="./images/delete.png" alt="Delete icon" title="Delete" onclick="$(\'#' + dd_id + '\').remove()" />';
				element += '<img class="action-text2" id="' + dd_id + '-plus" src="./images/add.png" alt="Add 4 rows" title="Add 4 rows" onclick="document.mainform.sql_' + field_id + '_query.rows+=4" />';
				element += '<img class="action-text3" id="' + dd_id + '-minus" src="./images/del.png" alt="Remove 4 rows" title="Remove 4 rows" onclick="if(document.mainform.sql_' + field_id + '_query.rows>7){document.mainform.sql_' + field_id + '_query.rows-=4}else{document.mainform.sql_' + field_id + '_query.rows-=(document.mainform.sql_' + field_id + '_query.rows-4)};" />';
			element += '</dd>';
		element += '</dl>';
	element += '</fieldset>';

	$('#sql-field').append(element);
}

/**
* Add title
*/
function add_title()
{
	var field_id = 'title_' + field_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = '<dd class="" id="' + dd_id + '">';
		element += '<input type="text" class="" id="" name="title[' + field_id + '][title]" size="53" maxlength="255" value="" />';
		element += '<span>' + lang_select('title[' + field_id + '][lang]') + '</span>';
		element += '<img class="do-stuff" id="" src="./images/delete.png" alt="Delete icon" title="Delete" onclick="$(\'#' + dd_id + '\').remove()" />';
	element += '</dd>';

	$('#title-field').append(element);
}

/**
* Counts the number of rows needed for <textarea>'s
*/
function count_rows(in_string, row_len, max_rows, min_rows)
{
	var newline = String.fromCharCode(10);
	var str_arr = new Array();
	str_arr = in_string.split(newline);
	var str_len = 0;
	var str_rows = 0;
	var str_sum = 0;

	for(var i in str_arr)
	{
		if(!isNaN(i))
		{
			str_rows++;
			str_len = str_arr[i].length;
			if(str_len > row_len)
			{
				str_sum = Math.ceil(str_len / row_len) -1;
				str_rows += str_sum;
			}
		}
	}

	str_rows = (str_rows <= min_rows) ? min_rows : str_rows;
	str_rows = (str_rows >= max_rows) ? max_rows : str_rows;
	return(str_rows);
}

/**
* Generate the language select
*/
function lang_select(field_id)
{
	var element = '<select name="' + field_id + '">';
	element += '<option value="ab">Abkhazian</option>';
	element += '<option value="aa">Afar</option>';
	element += '<option value="af">Afrikaans</option>';
	element += '<option value="sq">Albanian</option>';
	element += '<option value="am">Amharic</option>';
	element += '<option value="ar">Arabic</option>';
	element += '<option value="hy">Armenian</option>';
	element += '<option value="as">Assamese</option>';
	element += '<option value="ay">Aymara</option>';
	element += '<option value="az">Azerbaijani</option>';
	element += '<option value="ba">Bashkir</option>';
	element += '<option value="eu">Basque</option>';
	element += '<option value="bn">Bengali</option>';
	element += '<option value="dz">Bhutani</option>';
	element += '<option value="bh">Bihari</option>';
	element += '<option value="bi">Bislama</option>';
	element += '<option value="br">Breton</option>';
	element += '<option value="bg">Bulgarian</option>';
	element += '<option value="my">Burmese</option>';
	element += '<option value="be">Byelorussian</option>';
	element += '<option value="km">Cambodian</option>';
	element += '<option value="ca">Catalan</option>';
	element += '<option value="zh">Chinese</option>';
	element += '<option value="co">Corsican</option>';
	element += '<option value="hr">Croatian</option>';
	element += '<option value="cs">Czech</option>';
	element += '<option value="da">Danish</option>';
	element += '<option value="nl">Dutch</option>';
	element += '<option value="en" selected="selected">English</option>';
	element += '<option value="eo">Esperanto</option>';
	element += '<option value="et">Estonian</option>';
	element += '<option value="fo">Faeroese</option>';
	element += '<option value="fj">Fiji</option>';
	element += '<option value="fi">Finnish</option>';
	element += '<option value="fr">French</option>';
	element += '<option value="fy">Frisian</option>';
	element += '<option value="gl">Galician</option>';
	element += '<option value="ka">Georgian</option>';
	element += '<option value="de">German</option>';
	element += '<option value="el">Greek</option>';
	element += '<option value="kl">Greenlandic</option>';
	element += '<option value="gn">Guarani</option>';
	element += '<option value="gu">Gujarati</option>';
	element += '<option value="ha">Hausa</option>';
	element += '<option value="iw">Hebrew</option>';
	element += '<option value="hi">Hindi</option>';
	element += '<option value="hu">Hungarian</option>';
	element += '<option value="is">Icelandic</option>';
	element += '<option value="in">Indonesian</option>';
	element += '<option value="ia">Interlingua</option>';
	element += '<option value="ik">Inupiak</option>';
	element += '<option value="ga">Irish</option>';
	element += '<option value="it">Italian</option>';
	element += '<option value="ja">Japanese</option>';
	element += '<option value="jw">Javanese</option>';
	element += '<option value="kn">Kannada</option>';
	element += '<option value="ks">Kashmiri</option>';
	element += '<option value="kk">Kazakh</option>';
	element += '<option value="rw">Kinyarwanda</option>';
	element += '<option value="ky">Kirghiz</option>';
	element += '<option value="rn">Kirundi</option>';
	element += '<option value="ko">Korean</option>';
	element += '<option value="ku">Kurdish</option>';
	element += '<option value="lo">Laothian</option>';
	element += '<option value="la">Latin</option>';
	element += '<option value="lv">Lettish</option>';
	element += '<option value="ln">Lingala</option>';
	element += '<option value="lt">Lithuanian</option>';
	element += '<option value="mk">Macedonian</option>';
	element += '<option value="mg">Malagasy</option>';
	element += '<option value="ms">Malay</option>';
	element += '<option value="ml">Malayalam</option>';
	element += '<option value="mt">Maltese</option>';
	element += '<option value="mi">Maori</option>';
	element += '<option value="mr">Marathi</option>';
	element += '<option value="mo">Moldavian</option>';
	element += '<option value="mn">Mongolian</option>';
	element += '<option value="na">Nauru</option>';
	element += '<option value="ne">Nepali</option>';
	element += '<option value="no">Norwegian</option>';
	element += '<option value="oc">Occitan</option>';
	element += '<option value="or">Oriya</option>';
	element += '<option value="om">Oromo</option>';
	element += '<option value="ps">Pashto</option>';
	element += '<option value="fa">Persian</option>';
	element += '<option value="pl">Polish</option>';
	element += '<option value="pt">Portuguese</option>';
	element += '<option value="pa">Punjabi</option>';
	element += '<option value="qu">Quechua</option>';
	element += '<option value="rm">Rhaeto-Romance</option>';
	element += '<option value="ro">Romanian</option>';
	element += '<option value="ru">Russian</option>';
	element += '<option value="sm">Samoan</option>';
	element += '<option value="sg">Sangro</option>';
	element += '<option value="sa">Sanskrit</option>';
	element += '<option value="gd">Scots Gaelic</option>';
	element += '<option value="sr">Serbian</option>';
	element += '<option value="sh">Serbo-Croatian</option>';
	element += '<option value="st">Sesotho</option>';
	element += '<option value="tn">Setswana</option>';
	element += '<option value="sn">Shona</option>';
	element += '<option value="sd">Sindhi</option>';
	element += '<option value="si">Singhalese</option>';
	element += '<option value="ss">Siswati</option>';
	element += '<option value="sk">Slovak</option>';
	element += '<option value="sl">Slovenian</option>';
	element += '<option value="so">Somali</option>';
	element += '<option value="es">Spanish</option>';
	element += '<option value="su">Sudanese</option>';
	element += '<option value="sw">Swahili</option>';
	element += '<option value="sv">Swedish</option>';
	element += '<option value="tl">Tagalog</option>';
	element += '<option value="tg">Tajik</option>';
	element += '<option value="ta">Tamil</option>';
	element += '<option value="tt">Tatar</option>';
	element += '<option value="te">Tegulu</option>';
	element += '<option value="th">Thai</option>';
	element += '<option value="bo">Tibetan</option>';
	element += '<option value="ti">Tigrinya</option>';
	element += '<option value="to">Tonga</option>';
	element += '<option value="ts">Tsonga</option>';
	element += '<option value="tr">Turkish</option>';
	element += '<option value="tk">Turkmen</option>';
	element += '<option value="tw">Twi</option>';
	element += '<option value="uk">Ukrainian</option>';
	element += '<option value="ur">Urdu</option>';
	element += '<option value="uz">Uzbek</option>';
	element += '<option value="vi">Vietnamese</option>';
	element += '<option value="vo">Volapuk</option>';
	element += '<option value="cy">Welsh</option>';
	element += '<option value="wo">Wolof</option>';
	element += '<option value="xh">Xhosa</option>';
	element += '<option value="ji">Yiddish</option>';
	element += '<option value="yo">Yoruba</option>';
	element += '<option value="zu">Zulu</option>';
	element += '<option value="en">English</option>';
	element += '</select>';

	return(element);
}
