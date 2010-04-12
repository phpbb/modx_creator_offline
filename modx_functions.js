/**
* JavaScript for modx-creator
*/

var field_cnt = 1;
var file_cnt = 1;
var edit_cnt = 1;
var dl_cnt = 1;
var dt_cnt = 1;
var dd_cnt = 1;

/**
* Change the input type depending on what is selected
*/
function get_select_change(value, dt_id, dd_id, file_name, dl5_id)
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

	if($('#' + dd_id + '_toolbox'))
	{
		$('#' + dd_id + '_toolbox').remove();
	}

	if($('#' + dd_id + '_delete_tool'))
	{
		$('#' + dd_id + '_delete_tool').remove();
	}


	switch(value)
	{
		case 'find':
			data_type = 1;
			info_img = '<img id="' + dd_id + '_info" class="sign" src="./images/info.png" alt="Find explain" title="Find tags in the MODX file should be in the order that the find targets appear in the file. In other words, a processor of the MODX file should never need to go backwards in the file to locate all of the finds. When there are multiple finds within a single edit tag, the processor should handle all finds before any actions." />';
		break;

		case 'remove':
			data_type = 1;
			info_img = '<img id="' + dd_id + '_info" class="sign" src="./images/info.png" alt="Find and delete explain" title="Remove tags should either be alone in the edit tag or preceded by one find to be sure to delete the right code." />';
		break;

		case 'after-add':
		case 'before-add':
		case 'replace-with':
		case 'operation':
			data_type = 1;
			info_img = '<img id="' + dd_id + '_info" class="sign" src="./images/info.png" alt="Action explain" title="the string to add before the find, add after the find, replace the find with, or the operation string." />';
		break;

		case 'inline-find':
		case 'inline-remove':
		case 'inline-after-add':
		case 'inline-before-add':
		case 'inline-replace-with':
		case 'inline-operation':
			data_type = 2;
			info_img = '<img id="' + dd_id + '_info" class="sign" src="./images/info.png" alt="Inline explain" title="Note that the inline tags may not contain line breaks." />';
		break;

		default:
			data_type = 3;
			info_img = '<img id="' + dd_id + '_info" class="sign" src="./images/info.png" alt="Comment explain" title="Comment pertaining to this edit." />';
		break;
	}

	switch(data_type)
	{
		case 1:
			var element = '<div id="' + dd_id + '_field">';
				element += '<textarea class="inputbox right-tools" id="' + dd_id + '_data" name="' + file_name + '[data]" rows="' + count_rows(tmp_data, 85, 20, 4) + '">' + tmp_data + '</textarea>';
				element += '<div class="right-tools" id="' + dd_id + '_toolbox">';
					element += '<img id="' + dd_id + '_delete_tool" class="action-image" src="./images/delete.png" alt="Delete" onclick="$(\'#' + dl5_id + '\').remove()" />';
					element += '<img id="' + dd_id + '-plus" class="action-image" src="./images/plus.png" alt="Add 4 rows" onclick="document.forms[\'modxform\'].' + dd_id + '_data.rows+=4" title="Add 4 rows to the textfield" />';
					element += '<img id="' + dd_id + '-minus" class="action-image" src="./images/del.png" alt="Remove 4 rows" onclick="if(document.forms[\'modxform\'].' + dd_id + '_data.rows>7){document.forms[\'modxform\'].' + dd_id + '_data.rows-=4}else{document.forms[\'modxform\'].' + dd_id + '_data.rows-=(document.forms[\'modxform\'].' + dd_id + '_data.rows-4)};" title="Remove 4 rows from the textfield" />';
				element += '</div>';
			element += '</div>';
		break;

		case 2:
			var element = '<span id="' + dd_id + '_field">';
				element += '<textarea class="inputbox" id="' + dd_id + '_data" name="' + file_name + '[data]" rows="1" onKeypress="if((event.keyCode == 10) || (event.keyCode == 13)){return false;}">' + tmp_data + '</textarea>';
				element += '<img id="' + dd_id + '_delete_tool" class="action-image" src="./images/delete.png" alt="Delete" onclick="$(\'#' + dl5_id + '\').remove()" />';
			element += '</span>';
		break;

		default:
			var element = '<div id="' + dd_id + '_field">';
				element += '<textarea class="inputbox right-tools" id="' + dd_id + '_data" name="' + file_name + '[data]" rows="' + count_rows(tmp_data, 70, 20, 4) + '">' + tmp_data + '</textarea>';
				element += '<div class="right-tools" id="' + dd_id + '_toolbox">';
					element += '<img id="' + dd_id + '_delete_tool" class="action-image" src="./images/delete.png" alt="Delete" onclick="$(\'#' + dl5_id + '\').remove()" />';
					element += '<img id="' + dd_id + '-plus" class="action-image" src="./images/plus.png" alt="Add 4 rows" onclick="document.forms[\'modxform\'].' + dd_id + '_data.rows+=4" title="Add 4 rows to the textfield" />';
					element += '<img id="' + dd_id + '-minus" class="action-image" src="./images/del.png" alt="Remove 4 rows" onclick="if(document.forms[\'modxform\'].' + dd_id + '_data.rows>7){document.forms[\'modxform\'].' + dd_id + '_data.rows-=4}else{document.forms[\'modxform\'].' + dd_id + '_data.rows-=(document.forms[\'modxform\'].' + dd_id + '_data.rows-4)};" title="Remove 4 rows from the textfield" />';
				element += '</div>';
				element += '<div id="' + dd_id + '_lang">' + lang_select(file_name + '[lang]') + '</div>';
			element += '</div>';
		break;
	}

	$('#' + dd_id).append(element);
	$('#' + dt_id + '_options').append(info_img);
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
		var element = '<fieldset class="modx-level2" id="' + edit_id + '">';
			element += '<legend class="sub-legend"> Edit';
				element += ' <img class="sign" src="./images/info.png" alt="Info icon" title="Every discreet change to a file must be wrapped in its own edit tag, regardless of the number of children it contains. All finds within an edit tag should be processed before any action tag." />';
				element += '<img class="action-image" src="./images/delete.png" alt="Delete" onclick="$(\'#' + edit_id + '\').remove()" /> ';
				element += '<button type="button" class="button1" onclick="modx_add_field(\'' + obj_id + '\', \'' + edit_id + '\', \'edit\', \'above\', 1)">Add edit above</button> ';
				element += '<button type="button" class="button1" onclick="modx_add_field(\'' + obj_id + '\', \'' + edit_id + '\', \'edit\', \'below\', 1)">Add edit below</button>';
			element += ' <strong>Each FIND typically requires to start a new EDIT</strong></legend>';
			element += '<p style="font-size: 1em;">NOTE: Each discreet change to a file must be wrapped in its own edit tag.</p>';
			element += '<dl id="' + dl_id + '">';
				element += '<dt id="' + dt_id + '">';
					element += '<label>Type: </label>';
					element += '<span>' + modx_select(obj_id + '[' + edit_id + '][' + dl_id + ']', dt_id, dd_id, dl_id) + '</span>';
					element += '<div id="' + dt_id + '_options" style="margin-top: 5px">';
						element += '<img class="action-image" src="./images/plus_up.png" alt="Add action above" onclick="modx_add_field(\'' + obj_id + new_edit + '\', \'' + dl_id + '\', \'dl\', \'above\', 0);" title="Add an action field above this field" /> ';
						element += '<img class="action-image" src="./images/plus_down.png" onclick="modx_add_field(\'' + obj_id + new_edit + '\', \'' + dl_id + '\', \'dl\', \'below\', 0);" title="Add an action field below this field" /> ';
						element += '<img id="' + dd_id + '_info" class="sign" src="./images/info.png" title="Select type for this action field." />';
					element += '</div>';
				element += '</dt>';
				element += '<dd id="' + dd_id + '">';
					element += '<span id="' + dd_id + '_field">';
						element += '<input class="inputbox autowidth" id="' + dd_id + '_data" type="text" name="' + obj_id + new_edit + '[' + dl_id + '][data]" disabled="disabled" size="30" value="" />';
					element += '</span>';
					element += '<img id="' + dd_id + '_delete_tool" class="action-image" src="./images/delete.png" alt="Delete" onclick="$(\'#' + dl_id + '\').remove()" />';
				element += '</dd>';
			element += '</dl>';
		element += '</fieldset>';
	}
	else
	{
		var element = '<dl id="' + dl_id + '">';
			element += '<dt id="' + dt_id + '">';
				element += '<label>Type: </label>';
				element += '<span>' + modx_select(obj_id + '[' + dl_id + ']', dt_id, dd_id, dl_id) + '</span>';
				element += '<div id="' + dt_id + '_options" style="margin-top: 5px">';
					element += '<img class="action-image" src="./images/plus_up.png" alt="Add action above" onclick="modx_add_field(\'' + obj_id + new_edit + '\', \'' + dl_id + '\', \'dl\', \'above\', 0);" title="Add action above" /> ';
					element += '<img class="action-image" src="./images/plus_down.png" alt="Add action below" onclick="modx_add_field(\'' + obj_id + new_edit + '\', \'' + dl_id + '\', \'dl\', \'below\', 0)" title="Add an action field below this field" /> ';
					element += '<img id="' + dd_id + '_info" class="sign" src="./images/info.png" alt="Type explain" title="Select type for this action field" />';
				element += '</div>';
			element += '</dt>';
			element += '<dd id="' + dd_id + '">';
				element += '<span id="' + dd_id + '_field">';
					element += '<input class="inputbox autowidth" id="' + dd_id + '_data" type="text" name="' + obj_id + '[' + dl_id + '][data]" disabled="disabled" size="30" value="" />';
				element += '</span>';
				element += '<img id="' + dd_id + '_delete_tool" class="action-image" src="./images/delete.png" alt="Delete" onclick="$(\'#' + dl_id + '\').remove();" />';
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
function modx_select(file_name, dt_id, dd_id, dl_id)
{
	var element = '<select class="krav" name="' + file_name + '[type]" onchange="if(this.options[this.selectedIndex].value != \'-\'){ get_select_change(this.options[this.selectedIndex].value, \'' + dt_id + '\', \'' + dd_id + '\', \'' + file_name + '\', \'' + dl_id + '\') }">';
		element += '<option value="-" selected="selected">Select type</option>';
		element += '<option value="comment">Comment</option>';
		element += '<option value="find">Find</option>';
		element += '<option value="after-add">After add</option>';
		element += '<option value="before-add">Before add</option>';
		element += '<option value="replace-with">Replace with</option>';
		element += '<option value="operation">Operation</option>';
		element += '<option value="remove">Remove</option>';
		element += '<option value="inline-find">Inline find</option>';
		element += '<option value="inline-after-add">Inline after add</option>';
		element += '<option value="inline-before-add">Inline before add</option>';
		element += '<option value="inline-replace-with">Inline replace with</option>';
		element += '<option value="inline-operation">Inline operation</option>';
		element += '<option value="inline-remove">Inline remove</option>';
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

	var element = '<fieldset class="modx-level1 fields2 file-edit" id="' + file_id + '">';
		element += '<dl id="' + dl1_id + '">';
			element += '<dt>';
				element += '<label for="modx-' + file_id + '-file">File to open:</label>';
				element += '<img class="sign" src="./images/info.png" alt="File explain" title="Relative path from the phpBB root for the file to open. For example, viewforum.php or includes/functions.php" />';
			element += '</dt>';
			element += '<dd>';
				element += '<input class="inputbox medium" type="text" name="modx[' + file_id + '][file]" id="modx-' + file_id + '-file" size="88" value="" />';
				element += '<img class="action-image" src="./images/delete.png" alt="Delete" onclick="$(\'#' + file_id + '\').remove()" />';
			element += '</dd>';
		element += '</dl>';

		element += '<hr /><br />';

		element += '<fieldset class="modx-level2" id="' + edit_id + '">';
			element += '<legend>Edit';
				element += ' <img class="sign" src="./images/info.png" alt="Edit explain" title="Every discreet change to a file must be wrapped in its own edit tag, regardless of the number of children it contains. All finds within an edit tag should be processed before any action tag." /> ';
				element += '<img class="action-image" src="./images/delete.png" alt="Delete" onclick="$(\'#' + edit_id + '\').remove()" />';
				element += '<button type="button" class="button1" onclick="modx_add_field(\'modx[' + file_id + ']\', \'' + edit_id + '\', \'edit\', \'above\', 1);">Add edit above</button> ';
				element += '<button type="button" class="button1" onclick="modx_add_field(\'modx[' + file_id + ']\', \'' + edit_id + '\', \'edit\', \'below\', 1)">Add edit below</button>';
			element += ' <strong>Each FIND typically requires to start a new EDIT</strong></legend>';
			element += '<p style="font-size: 1em">NOTE: Each discreet change to a file must be wrapped in its own edit tag.</p>';

			element += '<dl id="' + dl2_id + '">';
				element += '<dt id="' + dt_id + '">';
					element += '<label>Type:</label>';
					element += '<span>' + modx_select('modx[' + file_id + '][' + edit_id + '][' + dl2_id + ']', dt_id, dd_id, dl2_id) + '</span>';
					element += '<div id="' + dt_id + '_options" style="margin-top: 5px">';
						element += ' <img class="action-image" src="./images/plus_up.png" alt="Add field above" onclick="modx_add_field(\'modx[' + file_id + '][' + edit_id + ']\', \'' + dl2_id + '\', \'dl\', \'above\', 0);" title="Add an action field above this field" />';
						element += ' <img class="action-image" src="./images/plus_down.png" alt="Add field below" onclick="modx_add_field(\'modx[' + file_id + '][' + edit_id + ']\', \'' + dl2_id + '\', \'dl\', \'below\', 0);" title="Add action below" />';
						element += ' <img id="' + dd_id + '_info" class="sign" src="./images/info.png" alt="Type explain" title="Select the type for the action field." />';
					element += '</div>';
				element += '</dt>';
				element += '<dd id="' + dd_id + '">';
					element += '<span id="' + dd_id + '_field">';
						element += '<input class="inputbox autowidth" id="' + dd_id + '_data" type="text" name="modx[' + file_id + '][' + edit_id + '][' + dl2_id + '][data]" disabled="disabled" size="30" value="" />';
					element += '</span>';
					element += '<img id="' + dd_id + '_delete_tool" class="action-image" src="./images/delete.png" alt="Delete" onclick="$(\'#' + dl2_id + '\').remove();" />';
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

	var element = '<fieldset class="modx-level1 fields2" id="' + field_id + '">';
		element += '<dl>';
			element += '<dt class="author-rows"><label for="author-' + field_id + '-username">Username:*</label>';
			element += '<dd class="author-rows">';
				element += '<input class="inputbox autowidth" type="text" name="author[' + field_id + '][username]" id="author-' + field_id + '-username" size="40" maxlength="255" value="" />';
				element += '<img class="action-image" src="./images/delete.png" alt="Delete" onclick="$(\'#' + field_id + '\').remove()" />';
			element += '</dd>';
		element += '</dl>';

		element += '<dl>';
			element += '<dt class="author-rows"><label for="author-' + field_id + '-phpbbcom">Not phpBB.com:</label></dt>';
			element += '<dd class="author-rows"><label>';
				element += '<input type="checkbox" name="author[' + field_id + '][phpbbcom]" id="author-' + field_id + '-phpbbcom" />';
				element += '<span style="font-size: 12px;">(Check here if this author is not registered at phpBB.com.)</span>';
			element += '</label></dd>';
		element += '</dl>';

		element += '<dl>';
			element += '<dt class="author-rows"><label for="author-' + field_id + '-realname">Real name:</label></dt>';
			element += '<dd class="author-rows">';
				element += '<input class="inputbox autowidth" type="text" name="author[' + field_id + '][realname]" id="author-' + field_id + '-realname" size="40" maxlength="255" value="" />';
			element += '</dd>';
		element += '</dl>';

		element += '<dl>';
			element += '<dt class="author-rows"><label for="author-' + field_id + '-homepage">www:</label></dt>';
			element += '<dd class="author-rows">';
				element += '<input class="inputbox autowidth" type="text" name="author[' + field_id + '][homepage]" id="author-' + field_id + '-homepage" size="40" maxlength="255" value="" />';
			element += '</dd>';
		element += '</dl>';
		element += '<dl>';
			element += '<dt class="author-rows"><label for="author-' + field_id + '-email">E-mail:</label></dt>';
			element += '<dd class="author-rows">';
				element += '<input class="inputbox autowidth" type="text" name="author[' + field_id + '][email]" id="author-' + field_id + '-email" size="40" maxlength="255" value="" />';
			element += '</dd>';
		element += '</dl>';
		element += '<div id="' + field_id + '-pre"></div>';

		element += '<input class="button2" type="button" value="Add contribution" onclick="add_contributor(\'' + field_id + '\');" /> <img class="sign" src="./images/info.png" alt="Info icon" title="The contributor fields are optional and every author can have several contributor fields. If you choose to add contributor fields, the only field required is the status. The others are optional." />';
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

	var element = '<fieldset class="modx-level2" id="' + dd_id + '">';
		element += '<dl>';
			element += '<dt class="author-rows"><label for="contributor-' + field_id + '-' + temp + '-status">Status:</label></dt>';
			element += '<dd class="author-rows">';
				element += '<select name="author[' + field_id + '][contributions][' + temp + '][status]" id="contributor-' + field_id + '-' + temp + '-status">';
					element += '<option value="past">Past</option>';
					element += '<option value="current" selected="selected">Current</option>';
				element += '</select>';
				element += ' <img class="action-image" src="./images/delete.png" alt="Delete" onclick="$(\'#' + dd_id + '\').remove()" />';
			element += '</dd>';
		element += '</dl>';

		element += '<dl>';
			element += '<dt class="author-rows"><label for="contributor-' + field_id + '-' + temp + '-position">Position:</label></dt>';
			element += '<dd class="author-rows">';
				element += '<input type="text" name="author[' + field_id + '][contributions][' + temp + '][position]" id="contributor-' + field_id + '-' + temp + '-position" size="40" maxlength="255" value="" />';
			element += '</dd>';
		element += '</dl>';

		element += '<dl>';
			element += '<dt class="author-rows"><label for="contributor-' + field_id + '-' + temp + '-from">From:</label></dt>';
			element += '<dd class="author-rows">';
				element += '<input type="text" name="author[' + field_id + '][contributions][' + temp + '][from]" id="contributor-' + field_id + '-' + temp + '-from" size="40" maxlength="255" value="" />';
			element += '</dd>';
		element += '</dl>';

		element += '<dl>';
			element += '<dt class="author-rows"><label for="contributor-' + field_id + '-' + temp + '-to">To:</label></dt>';
			element += '<dd class="author-rows">';
				element += '<input type="text" name="author[' + field_id + '][contributions][' + temp + '][to]" id="contributor-' + field_id + '-' + temp + '-to" size="40" maxlength="255" value="" />';
			element += '</dd>';
		element += '</dl>';
	element += '</fieldset>';

	$('#' + field_id + '-pre').append(element);
}

/**
* Add description
*/
function add_desc()
{
	var field_id = 'desc_' + field_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = '<dd id="' + dd_id + '"><br />';
		element += '<textarea class="inputbox right-tools" name="desc[' + field_id + '][desc]" id="desc_' + field_id + '_desc" rows="5"></textarea>';
		element += '<div class="right-tools">';
			element += '<img class="action-image" src="./images/delete.png" alt="Delete" onclick="$(\'#' + dd_id + '\').remove()" />';
			element += '<img class="action-image" src="./images/plus.png" alt="Add 4 rows" onclick="document.forms[\'modxform\'].desc_' + field_id + '_desc.rows+=4" title="Add 4 rows to the textfield" />';
			element += '<img class="action-image" src="./images/del.png" alt="Remove  4 rows" onclick="if(document.forms[\'modxform\'].desc_' + field_id + '_desc.rows>7){document.forms[\'modxform\'].desc_' + field_id + '_desc.rows-=4}else{document.forms[\'modxform\'].desc_' + field_id + '_desc.rows-=(document.forms[\'modxform\'].desc_' + field_id + '_desc.rows-4)};" title="Remove 4 rows from the textfield" />';
		element += '</div>';
		element += '<div>' + lang_select('desc[' + field_id + '][lang]') + '</div';
	element += '</dd>';

	$('#desc-field').append(element);
}

/**
* Add the main copy-field
*/
function add_copy()
{
	var field_id = 'fc_' + field_cnt++;
	var temp = 'tm_' + field_cnt++;
	var dd_id = 'dd_' + dd_cnt++;
	var dd_id2 = 'dd_' + dd_cnt++;

	var element = '<fieldset class="modx-level2 fields2" id="dd-copy">';
		element += '<dl id="dl-copy">';
			element += '<dt class="copy-rows">';
				element += '<label>Copy: (from &raquo; to)</label>';
				element += '<img class="action-image" src="./images/plus.png" alt="Add file" title="Add file" onclick="add_file_copy();" /><img class="action-image" src="./images/delete.png" alt="Delete" onclick="$(\'#dd-copy\').remove(); document.getElementById(\'addCopyField\').style.display=\'\';" />';
			element += '</dt>';

			element += '<dd class="copy-rows" id="' + dd_id2 + '">';
				element += '<input class="inputbox copy-to" name="copy[' + temp + '][from]" size="85" maxlength="255" value="" type="text" /> &raquo; ';
				element += '<input class="inputbox" name="copy[' + temp + '][to]" size="85" maxlength="255" value="" type="text" />';
				element += '<img class="action-image" src="./images/delete.png" alt="Delete" onclick="$(\'#' + dd_id2 + '\').remove()" />';
			element += '</dd>';
		element += '</dl>';
	element += '</fieldset>';

	$('#copy-field').append(element);
}

/**
* Add file copy
*/
function add_file_copy()
{
	temp = 'tm_' + field_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = '<dd class="copy-rows" id="' + dd_id + '">';
		element += '<input class="inputbox copy-to" name="copy[' + temp + '][from]" size="85" maxlength="255" value="" type="text" /> &raquo; ';
		element += '<input class="inputbox" name="copy[' + temp + '][to]" size="85" maxlength="255" value="" type="text" />';
		element += '<img class="action-image" src="./images/delete.png" alt="Delete" onclick="$(\'#' + dd_id + '\').remove()" />';
	element += '</dd>';

	$('#dl-copy').append(element);
}

/**
* Add the main delete-field
*/
function add_delete()
{
	var field_id = 'fc_' + field_cnt++;
	var temp = 'tm_' + field_cnt++;
	var dd_id = 'dd_' + dd_cnt++;
	var dd_id2 = 'dd_' + dd_cnt++;

	var element = '<fieldset class="modx-level2 fields2" id="dd-delete">';
		element += '<dl id="dl-delete">';
			element += '<dt class="copy-rows">';
				element += '<label>delete:</label>';
				element += '<img class="action-image" src="./images/plus.png" alt="Add file" title="Add file" onclick="add_file_delete();" /><img class="action-image" src="./images/delete.png" alt="Delete" onclick="$(\'#dd-delete\').remove(); document.getElementById(\'addDeleteField\').style.display=\'\';" />';
			element += '</dt>';

			element += '<dd class="copy-rows" id="' + dd_id2 + '">';
				element += '<input class="inputbox copy-to" name="delete[' + temp + ']" size="85" maxlength="255" value="" type="text" />';
				element += '<img class="action-image" src="./images/delete.png" alt="Delete" onclick="$(\'#' + dd_id2 + '\').remove()" />';
			element += '</dd>';
		element += '</dl>';
	element += '</fieldset>';

	$('#delete-field').append(element);
}

/**
* Add file delete
*/
function add_file_delete()
{
	temp = 'tm_' + field_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = '<dd class="copy-rows" id="' + dd_id + '">';
		element += '<input class="inputbox copy-to" name="delete[' + temp + ']" size="85" maxlength="255" value="" type="text" />';
		element += '<img class="action-image" src="./images/delete.png" alt="Delete" onclick="$(\'#' + dd_id + '\').remove()" />';
	element += '</dd>';

	$('#dl-delete').append(element);
}

/**
* Add a history-field
*/
function add_history()
{
	var field_id = 'hf_' + field_cnt++;
	var temp = 'logdd_' + dd_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = '<fieldset class="modx-level1 fields2" id="' + dd_id + '">';
		element += '<dl>';
			element += '<dt class="history-rows"><label for="history-' + field_id + '-version">Version:*</label></dt>';
			element += '<dd class="history-rows">';
				element += '<input class="inputbox autowidth" name="history[' + field_id + '][version]" id="history-' + field_id + '-version" size="10" maxlength="255" value="" type="text" />';
				element += '<img class="action-image" src="./images/delete.png" alt="" onclick="$(\'#' + dd_id + '\').remove()" />';
			element += '</dd>';
		element += '</dl>';

		element += '<dl>';
			element += '<dt class="history-rows"><label for="history-' + field_id + '-date">Date:*</label></dt>';
			element += '<dd class="history-rows">';
				element += '<input class="inputbox autowidth" name="history[' + field_id + '][date]" id="history-' + field_id + '-date" size="20" maxlength="255" value="" type="text" />';
				element += '<span style="font-size: 12px;"> (The date format needs to be YYYY-MM-DD)</span>';
			element += '</dd>';
		element += '</dl>';

		element += '<div id="' + field_id + '">';
			element += '<hr class="dashed" />';
			element += '<dl>';
				element += '<dt class="history-rows">';
					element += '<label for="history-' + field_id + '-change-' + temp + '-data">Change:*</label>';
				element += '</dt>';
				element += '<dd class="history-rows">';
					element += '<input class="inputbox medium" name="history[' + field_id + '][changelog][' + temp + '][change]" id="history-' + field_id + '-change-' + temp + '-data" size="80" maxlength="255" value="" type="text" />';
					element += '<span>' + lang_select('history[' + field_id + '][changelog][' + temp + '][lang]') + '</span>';
				element += '</dd>';
			element += '</dl>';
		element += '</div>';

		element += '<input class="button2" value="Add change" onclick="add_history_change(\'' + field_id + '\');" type="button" />';
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

	var element = '<div id="' + dd_id + '">';
		element += '<hr class="dashed" />';
		element += '<dl>';
			element += '<dt class="history-rows">';
				element += '<label for="history-' + field_id + '-change-' + temp + '-data">Change:*</label>';
			element += '</dt>';
			element += '<dd class="history-rows">';
				element += '<input class="inputbox medium" name="history[' + field_id + '][changelog][' + temp + '][change]" id="history-' + field_id + '-change-' + temp + '-data" size="80" maxlength="255" value="" type="text" />';
				element += '<span>' + lang_select('history[' + field_id + '][changelog][' + temp + '][lang]') + '</span>';
				element += ' <img class="action-image" src="./images/delete.png" alt="" onclick="$(\'#' + dd_id + '\').remove()" />';
			element += '</dd>';
		element += '</dl>';
	element += '</div>';

	$('#' + field_id).append(element);
}

/**
* Add link-field
*/
function add_link()
{
	var field_id = 'lf_' + field_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = '<fieldset class="modx-level2" id="' + dd_id + '">';
		element += '<dl>';
			element += '<dt><label for="links-' + field_id + '-type">Type:*</label></dt>';
			element += '<dd>';
				element += '<select name="links[' + field_id + '][type]" id="links-' + field_id + '-type">';
					element += '<option value="contrib" selected="selected">Contrib</option>';
					element += '<option value="dependency">Dependency</option>';
					element += '<option value="language">Language</option>';
					element += '<option value="parent">Parent</option>';
					element += '<option value="template-lang">Template_lang</option>';
					element += '<option value="template">Template</option>';
					element += '<option value="text">Text file</option>';
					element += '<option value="uninstall">Uninstall instructions</option>';
				element += '</select>';
				element += ' <img class="action-image" src="./images/delete.png" alt="" onclick="$(\'#' + dd_id + '\').remove()" />';
			element += '</dd>';
		element += '</dl>';

		element += '<dl>';
			element += '<dt><label for="links-' + field_id + '-title">Link title:*</label></dt>';
			element += '<dd>';
				element += '<input class="inputbox medium" name="links[' + field_id + '][title]" id="links-' + field_id + '-title" size="80" maxlength="255" value="" type="text" />';
				element += lang_select('links[' + field_id + '][lang]');
			element += '</dd>';
		element += '</dl>';

		element += '<dl>';
			element += '<dt><label for="links-' + field_id + '-href">href:*</label></dt>';
			element += '<dd>';
				element += '<input class="inputbox medium" name="links[' + field_id + '][href]" id="links-' + field_id + '-href" size="80" maxlength="255" value="" type="text" />';
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

	var element = '<dd id="' + dd_id + '"><br />';
		element += '<textarea class="inputbox right-tools" name="notes[' + field_id + '][note]" id="notes_' + field_id + '_note" rows="4"></textarea>';
		element += '<div class="right-tools">';
			element += '<img class="action-image" src="./images/delete.png" alt="Delete" onclick="$(\'#' + dd_id + '\').remove();" />';
			element += '<img class="action-image" src="./images/plus.png" alt="Add 4 rows" onclick="document.forms[\'modxform\'].notes_' + field_id + '_note.rows+=4" title="Add 4 rows to the textfield" />';
			element += '<img class="action-image" src="./images/del.png" alt="Scale" onclick="if(document.forms[\'modxform\'].notes_' + field_id + '_note.rows>7){document.forms[\'modxform\'].notes_' + field_id + '_note.rows-=4}else{document.forms[\'modxform\'].notes_' + field_id + '_note.rows-=(document.forms[\'modxform\'].notes_' + field_id + '_note.rows-4)};" title="Remove 4 rows from the textfield)" />';
		element += '</div>';
		element += lang_select('notes[' + field_id + '][lang]');
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

	var element = '<dd id="' + dd_id + '"><br />';
		element += '<textarea class="inputbox right-tools" name="diy[' + field_id + '][diy]" id="diy_' + field_id + '_diy" rows="4"></textarea>';
		element += '<div class="right-tools">';
			element += '<img class="action-image" src="./images/delete.png" alt="Delete" onclick="$(\'#' + dd_id + '\').remove()" />';
			element += '<img id="' + field_id + '-plus" class="action-image" src="./images/plus.png" alt="Make bigger" onclick="document.forms[\'modxform\'].diy_' + field_id + '_diy.rows+=4" title="Add 4 rows to the textfield" />';
			element += '<img id="' + field_id + '-minus" class="action-image" src="./images/del.png" alt="Make smaller" onclick="if(document.forms[\'modxform\'].diy_' + field_id + '_diy.rows>7){document.forms[\'modxform\'].diy_' + field_id + '_diy.rows-=4}else{document.forms[\'modxform\'].diy_' + field_id + '_diy.rows-=(document.forms[\'modxform\'].diy_' + field_id + '_diy.rows-4)};" title="Remove 4 rows from the textfield" />';
		element += '</div>';
		element += lang_select('diy[' + field_id + '][lang]');
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

	var element = '<fieldset class="modx-level2 fields2" id="' + dd_id + '">';
		element += '<dl>';
			element += '<dt class="sql-rows"><label for="sql-' + field_id + '-dbms">DBMS:</label></dt>';
			element += '<dd class="sql-rows">';
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

		element += '<dl>';
			element += '<dt class="sql-rows"><label for="sql-' + field_id + '-query">Query:*</label></dt>';
			element += '<dd class="sql-rows">';
				element += '<textarea class="inputbox right-tools" name="sql[' + field_id + '][query]" id="sql_' + field_id + '_query" rows="4"></textarea>';
				element += '<div class="right-tools">';
					element += '<img class="action-image" src="./images/delete.png" alt="Delete" onclick="$(\'#' + dd_id + '\').remove()" />';
					element += '<img class="action-image" src="./images/plus.png" alt="Add rows" onclick="document.forms[\'modxform\'].sql_' + field_id + '_query.rows+=4" title="Add 4 rows to the textfield" />';
					element += '<img class="action-image" src="./images/del.png" alt="Remove rows" onclick="if(document.forms[\'modxform\'].sql_' + field_id + '_query.rows>7){document.forms[\'modxform\'].sql_' + field_id + '_query.rows-=4}else{document.forms[\'modxform\'].sql_' + field_id + '_query.rows-=(document.forms[\'modxform\'].sql_' + field_id + '_query.rows-4)};" title="Remove 4 rows from the textfield" />';
				element += '</div>';
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

	var element = '<dd id="' + dd_id + '">';
	element += '<input class="inputbox medium" type="text" name="title[' + field_id + '][title]" size="53" maxlength="255" value="" /> ';
	element += lang_select('title[' + field_id + '][lang]');
	element += ' <img class="action-image" src="./images/delete.png" alt="Delete" onclick="$(\'#' + dd_id + '\').remove();" />';
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
