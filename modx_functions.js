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
	if($(dd_id + '_field'))
	{
		if($(tmp_id).value)
		{
			tmp_data = $(tmp_id).value;
		}
		$(dd_id + '_field').remove();
	}
	if($(dd_id + '_info'))
	{
		$(dd_id + '_info').remove();
	}
	if($(dd_id + '-plus'))
	{
		$(dd_id + '-plus').remove();
	}
	if($(dd_id + '-minus'))
	{
		$(dd_id + '-minus').remove();
	}

	switch(value)
	{
		case 'find':
			data_type = 1;
			info_img = Builder.node('img', { id: dd_id + '_info',  className: 'action-arrows', src: './images/info_2.png', alt: 'Info icon', onmouseover: "Tip('Find tags in the MODX file should be in the order that the find targets appear in the file. In other words, a processor of the MODX file should never need to go backwards in the file to locate all of the finds. When there are multiple finds within a single edit tag, the processor should handle all finds before any actions.')", onmouseout: 'UnTip()' });
		break;

		case 'after-add':
		case 'before-add':
		case 'replace-with':
		case 'operation':
			data_type = 1;
			info_img = Builder.node('img', { id: dd_id + '_info',  className: 'action-arrows', src: './images/info_12.png', alt: 'Info icon', onmouseover: "Tip('The string to add before the find, add after the find, replace the find with, or the operation string described above.<br /><br />The syntax for the operation action is a bit obscure because it uses tokens. The find action would have a token like this<br />colspan=&quot;{:%1}&quot;<br />The operation action would look like this:<br />{:%1} + 1')", onmouseout: 'UnTip()' });
		break;

		case 'inline-find':
		case 'inline-operation':
		case 'inline-replace-with':
		case 'inline-before-add':
		case 'inline-after-add':
			data_type = 2;
			info_img = Builder.node('img', { id: dd_id + '_info',  className: 'action-arrows', src: './images/info_8.png', alt: 'Info icon', onmouseover: "Tip('Note that the tags may not contain line breaks')", onmouseout: 'UnTip()' });
		break;

		default:
			data_type = 3;
			info_img = Builder.node('img', { id: dd_id + '_info',  className: 'action-arrows', src: './images/info_17.png', alt: 'Info icon', onmouseover: "Tip('Comment pertaining to this edit')", onmouseout: 'UnTip()' });
		break;
	}

	switch(data_type)
	{
		case 1:
			var element = Builder.node('span', { id: dd_id + '_field' }, [
				Builder.node('textarea', { id: dd_id + '_data', name: file_name + '[data]', rows: count_rows(tmp_data, 85, 20, 4) }, tmp_data),
				Builder.node('img', { id: dd_id + '-plus',  className: 'action-text2', src: './images/add.png', onclick: 'document.mainform.' + dd_id + '_data.rows+=4', onmouseover: "Tip('Add 4 rows')", onmouseout: 'UnTip()' }),
				Builder.node('img', { id: dd_id + '-minus',  className: 'action-text3', src: './images/del.png', onclick: 'if(document.mainform.' + dd_id + '_data.rows>7){document.mainform.' + dd_id + '_data.rows-=4}else{document.mainform.' + dd_id + '_data.rows-=(document.mainform.' + dd_id + '_data.rows-4)};', onmouseover: "Tip('Remove 4 rows')", onmouseout: 'UnTip()' }),
			]);
		break;

		case 2:
			var element = Builder.node('span', { id: dd_id + '_field' }, [
				Builder.node('input', { id: dd_id + '_data', type: 'text', name: file_name + '[data]', size: '88', maxlength: '255', value: tmp_data }),
			]);
		break;

		default:
			var element = Builder.node('span', { id: dd_id + '_field' }, [
				Builder.node('textarea', { id: dd_id + '_data', className: 'action-comment', name: file_name + '[data]', rows: count_rows(tmp_data, 70, 20, 4) }, tmp_data),
				Builder.node('span', { id: dd_id + '_lang' }, lang_select(file_name + '[lang]')),
				Builder.node('img', { id: dd_id + '-plus',  className: 'action-text2', src: './images/add.png', onclick: 'document.mainform.' + dd_id + '_data.rows+=4', onmouseover: "Tip('Add 4 rows')", onmouseout: 'UnTip()' }),
				Builder.node('img', { id: dd_id + '-minus',  className: 'action-text3', src: './images/del.png', onclick: 'if(document.mainform.' + dd_id + '_data.rows>7){document.mainform.' + dd_id + '_data.rows-=4}else{document.mainform.' + dd_id + '_data.rows-=(document.mainform.' + dd_id + '_data.rows-4)};', onmouseover: "Tip('Remove 4 rows')", onmouseout: 'UnTip()' }),
			]);
		break;
	}

	$(dd_id).insert({top: element});
	$(dt_id).insert(info_img);
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
		var element = Builder.node('fieldset', { className: 'white', id: edit_id }, [
			Builder.node('legend', 'Edit', [
				Builder.node('img', { className: 'sign', src: './images/info.png', alt: 'Info icon', onmouseover: "Tip('Every discreet change to a file must be wrapped in its own edit tag, regardless of the number of children it contains.<br />All finds within an edit tag should be processed before any action tag.')", onmouseout: 'UnTip()' }),
				Builder.node('img', { className: 'do-stuff', src: './images/plus_up.png', alt: 'Arrow up icon', onclick: 'modx_add_field(\'' + obj_id + '\', \'' + edit_id + '\', \'edit\', \'above\', 1)', onmouseover: "Tip('Add a edit field above this edit field')", onmouseout: 'UnTip()' }),
				Builder.node('img', { className: 'do-stuff', src: './images/plus_down.png', alt: 'Arrow down icon', onclick: 'modx_add_field(\'' + obj_id + '\', \'' + edit_id + '\', \'edit\', \'below\', 1)', onmouseover: "Tip('Add a edit field below this edit field')", onmouseout: 'UnTip()' }),
				Builder.node('img', { className: 'do-stuff', src: './images/delete.png', alt: 'Delete icon', onclick: "$('" + edit_id + "').remove()", onmouseover: "Tip('Delete this edit')", onmouseout: 'UnTip()' }),
			]),
			Builder.node('p', 'NOTE: Each discreet change to a file must be wrapped in its own edit tag.'),

			Builder.node('dl', { id: dl_id, style : 'white-space: nowrap;' }, [
				Builder.node('dt', { id: dt_id }, [
					Builder.node('label', 'Type' + ':'),
					Builder.node('span', modx_select(obj_id + '[' + edit_id + '][' + dl_id + ']', dt_id, dd_id)),
					Builder.node('br'),
					Builder.node('img', { className: 'action-arrows', src: './images/plus_up.png', alt: 'Arrow up icon', onclick: 'modx_add_field(\'' + obj_id + new_edit + '\', \'' + dl_id + '\', \'dl\', \'above\', 0)', onmouseover: "Tip('Add action above')", onmouseout: 'UnTip()' }),
					Builder.node('img', { className: 'action-arrows', src: './images/plus_down.png', alt: 'Arrow down icon', onclick: 'modx_add_field(\'' + obj_id + new_edit + '\', \'' + dl_id + '\', \'dl\', \'below\', 0)', onmouseover: "Tip('Add action below')", onmouseout: 'UnTip()' }),
					Builder.node('img', { id: dd_id + '_info',  className: 'action-arrows', src: './images/info.png', alt: 'Info icon', onmouseover: "Tip('Select the type for this action')", onmouseout: 'UnTip()' }),
				]),
				Builder.node('dd', { id: dd_id }, [
					Builder.node('span', { id: dd_id + '_field' }, [
						Builder.node('input', { id: dd_id + '_data', type: 'text', name: obj_id + new_edit + '[' + dl_id + '][data]', disabled : 'disabled', size: '30', value: '' }),
					]),
					Builder.node('img', { className: 'do-stuff', src: './images/delete.png', alt: 'Delete icon', onclick: "$('" + dl_id + "').remove()", onmouseover: "Tip('Delete')", onmouseout: 'UnTip()' }),
				]),
			]),
		]);
	}
	else
	{
		var element = Builder.node('dl', { id: dl_id, style : 'white-space: nowrap;' }, [
			Builder.node('dt', { id: dt_id }, [
				Builder.node('label', 'Type' + ':'),
				Builder.node('span', modx_select(obj_id + '[' + dl_id + ']', dt_id, dd_id)),
				Builder.node('br'),
				Builder.node('img', { className: 'action-arrows', src: './images/plus_up.png', alt: 'Arrow up icon', onclick: "modx_add_field('" + obj_id + new_edit + "', '" + dl_id + "', 'dl', 'above', 0)", onmouseover: "Tip('Add action above')", onmouseout: 'UnTip()' }),
				Builder.node('img', { className: 'action-arrows', src: './images/plus_down.png', alt: 'Arrow down icon', onclick: 'modx_add_field(\'' + obj_id + new_edit + '\', \'' + dl_id + '\', \'dl\', \'below\', 0)', onmouseover: "Tip('Add action below')", onmouseout: 'UnTip()' }),
				Builder.node('img', { id: dd_id + '_info',  className: 'action-arrows', src: './images/info.png', alt: 'Info icon', onmouseover: "Tip('Select the type for the string field')", onmouseout: 'UnTip()' }),
			]),
			Builder.node('dd', { id: dd_id }, [
				Builder.node('span', { id: dd_id + '_field' }, [
					Builder.node('input', { id: dd_id + '_data', type: 'text', name: obj_id + '[' + dl_id + '][data]', disabled : 'disabled', size: '30', value: '' }),
				]),
				Builder.node('img', { className: 'do-stuff', src: './images/delete.png', alt: 'Delete icon', onclick: "$('" + dl_id + "').remove()", onmouseover: "Tip('Delete')", onmouseout: 'UnTip()' }),
			]),
		]);
	}

	if(position == 'above')
	{
		$(parent_id).insert({before: element});
	}
	else
	{
		$(parent_id).insert({after: element});
	}
}

/**
* Generate the type select
*/
function modx_select(file_name, dt_id, dd_id)
{
	var element = Builder.node('select', { className: 'krav', name: file_name + '[type]', onchange: 'if(this.options[this.selectedIndex].value != \'-\'){ get_select_change(this.options[this.selectedIndex].value, \'' + dt_id + '\', \'' + dd_id + '\', \'' + file_name + '\') }' }, [
		Builder.node('option', { value: 'comment' }, 'Comment'),
		Builder.node('option', { value: 'find' }, 'Find'),
		Builder.node('option', { value: 'after-add' }, 'After add'),
		Builder.node('option', { value: 'before-add' }, 'Before add'),
		Builder.node('option', { value: 'replace-with' }, 'Replace with'),
		Builder.node('option', { value: 'operation' }, 'Operation'),
		Builder.node('option', { value: 'inline-find' }, 'Inline find'),
		Builder.node('option', { value: 'inline-after-add' }, 'Inline after add'),
		Builder.node('option', { value: 'inline-before-add' }, 'Inline before add'),
		Builder.node('option', { value: 'inline-replace-with' }, 'Inline replace with'),
		Builder.node('option', { value: 'inline-operation' }, 'Inline operation'),
		Builder.node('option', { value: '-', selected: 'selected' }, 'Select type'),
	]);

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

	var element = Builder.node('fieldset', { className: 'inner', id: file_id }, [
		Builder.node('dl', { id: dl1_id }, [
			Builder.node('dt', [
				Builder.node('label', { htmlFor: 'modx-' + file_id + '-file' }, 'File to open' + ':'),
				Builder.node('img', { className: 'sign', src: './images/info.png', alt: 'Info icon', onmouseover: "Tip('Relative path from the phpBB root for the file to open. For example, viewforum.php or includes/functions.php')", onmouseout: 'UnTip()' }),
			]),
			Builder.node('dd', [
				Builder.node('input', { type: 'text', name: 'modx[' + file_id + '][file]', id: 'modx-' + file_id + '-file', size: '88', value: '' }),
				Builder.node('img', { className: 'do-stuff', src: './images/delete.png', alt: 'Delete icon', onclick: "$('" + file_id + "').remove()", onmouseover: "Tip('Delete this file')", onmouseout: 'UnTip()' }),
			]),
		]),
		Builder.node('fieldset', { className: 'white', id: edit_id }, [
			Builder.node('legend', 'Edit', [
				Builder.node('img', { className: 'sign', src: './images/info.png', alt: 'Info icon', onmouseover: "Tip('Every discreet change to a file must be wrapped in its own edit tag, regardless of the number of children it contains.<br />All finds within an edit tag should be processed before any action tag.')", onmouseout: 'UnTip()' }),
				Builder.node('img', { className: 'do-stuff', src: './images/plus_up.png', alt: 'Arrow up icon', onclick: 'modx_add_field(\'modx[' + file_id + ']\', \'' + edit_id + '\', \'edit\', \'above\', 1)', onmouseover: "Tip('Add edit above')", onmouseout: 'UnTip()' }),
				Builder.node('img', { className: 'do-stuff', src: './images/plus_down.png', alt: 'Arrow down icon', onclick: 'modx_add_field(\'modx[' + file_id + ']\', \'' + edit_id + '\', \'edit\', \'below\', 1)', onmouseover: "Tip('Add edit below')", onmouseout: 'UnTip()' }),
				Builder.node('img', { className: 'do-stuff', src: './images/delete.png', alt: 'Delete icon', onclick: "$('" + edit_id + "').remove()", onmouseover: "Tip('Delete this edit')", onmouseout: 'UnTip()' }),
			]),
			Builder.node('p', 'NOTE: Each discreet change to a file must be wrapped in its own edit tag.'),

			Builder.node('dl', { id: dl2_id, style : 'white-space: nowrap;' }, [
				Builder.node('dt', { id: dt_id }, [
					Builder.node('label', 'Type' + ':'),
					Builder.node('span', modx_select('modx[' + file_id + '][' + edit_id + '][' + dl2_id + ']', dt_id, dd_id)),
					Builder.node('br'),
					Builder.node('img', { className: 'action-arrows', src: './images/plus_up.png', alt: 'Arrow up icon', onclick: 'modx_add_field(\'modx[' + file_id + '][' + edit_id + ']\', \'' + dl2_id + '\', \'dl\', \'above\', 0)', onmouseover: "Tip('Add action above')", onmouseout: 'UnTip()' }),
					Builder.node('img', { className: 'action-arrows', src: './images/plus_down.png', alt: 'Arrow down icon', onclick: 'modx_add_field(\'modx[' + file_id + '][' + edit_id + ']\', \'' + dl2_id + '\', \'dl\', \'below\', 0)', onmouseover: "Tip('Add action below')", onmouseout: 'UnTip()' }),
					Builder.node('img', { id: dd_id + '_info',  className: 'action-arrows', src: './images/info.png', alt: 'Info icon', onmouseover: "Tip('Select the type for this action.')", onmouseout: 'UnTip()' }),
				]),
				Builder.node('dd', { id: dd_id }, [
					Builder.node('span', { id: dd_id + '_field' }, [
						Builder.node('input', { id: dd_id + '_data', type: 'text', name: 'modx[' + file_id + '][' + edit_id + '][' + dl2_id + '][data]', disabled : 'disabled', size: '30', value: '' }),
					]),
					Builder.node('img', { className: 'do-stuff', src: './images/delete.png', alt: 'Delete icon', onclick: "$('" + dl2_id + "').remove()", onmouseover: "Tip('Delete')", onmouseout: 'UnTip()' }),
				]),
			]),
		]),
	]);

	$('modx-field').insert(element);
}

/**
* Add author field(s)
*/
function add_author()
{
	var field_id = 'author_' + field_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = Builder.node('fieldset', { id: dd_id, className: 'white' }, [
		Builder.node('dl', [
			Builder.node('dt', { className: 'author-rows' }, [
				Builder.node('label', { htmlFor: 'author-' + field_id + '-username' }, 'Username' + ':*')
			]),
			Builder.node('dd', { className: 'author-rows' }, [
				Builder.node('input', { type: 'text', name: 'author[' + field_id + '][username]', id: 'author-' + field_id + '-username', size: '40', maxlength: '255', value: '' }),
				Builder.node('img', { className: 'do-stuff', src: './images/delete.png', alt: 'Delete icon', onclick: "$('" + dd_id + "').remove()", onmouseover: "Tip('Delete this author')", onmouseout: 'UnTip()' }),
			]),
		]),

		Builder.node('dl', [
			Builder.node('dt', { className: 'author-rows' }, [
				Builder.node('label', { htmlFor: 'author-' + field_id + '-phpbbcom' }, 'Not phpBB.com' + ':')
			]),
			Builder.node('dd', { className: 'author-rows' }, [
				Builder.node('label', [
					Builder.node('input', { type: 'checkbox', name: 'author[' + field_id + '][phpbbcom]', id: 'author-' + field_id + '-phpbbcom'}),
					Builder.node('span', { style: 'font-size: 12px;'}, '(Check here if this author is not registered at phpbb.com.)'),
				]),
			]),
		]),

		Builder.node('dl', [
			Builder.node('dt', { className: 'author-rows' }, [
				Builder.node('label', { htmlFor: 'author-' + field_id + '-realname' }, 'Real name' + ':')
			]),
			Builder.node('dd', { className: 'author-rows' }, [
				Builder.node('input', { type: 'text', name: 'author[' + field_id + '][realname]', id: 'author-' + field_id + '-realname', size: '40', maxlength: '255', value: '' }),
			]),
		]),

		Builder.node('dl', [
			Builder.node('dt', { className: 'author-rows' }, [
				Builder.node('label', { htmlFor: 'author-' + field_id + '-homepage' }, 'Homepage' + ':')
			]),
			Builder.node('dd', { className: 'author-rows' }, [
				Builder.node('input', { type: 'text', name: 'author[' + field_id + '][homepage]', id: 'author-' + field_id + '-homepage', size: '40', maxlength: '255', value: '' }),
			]),
		]),

		Builder.node('dl', [
			Builder.node('dt', { className: 'author-rows' }, [
				Builder.node('label', { htmlFor: 'author-' + field_id + '-email' }, 'E-mail' + ':')
			]),
			Builder.node('dd', { className: 'author-rows' }, [
				Builder.node('input', { type: 'text', name: 'author[' + field_id + '][email]', id: 'author-' + field_id + '-email', size: '40', maxlength: '255', value: '' }),
			]),
		]),

		Builder.node('fieldset', { id: field_id , style: 'border: none;'}),
		Builder.node('input', { type: 'button', value: 'Add contribution', onclick: "add_contributor('" + field_id + "');" }),
	]);

	$('authors').insert(element);
}

/**
* Add a contributor-field
*/
function add_contributor(field_id)
{
	var temp = 'tm_' + field_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = Builder.node('fieldset', { id: dd_id, className: 'noborder' }, [
		Builder.node('dl', [
			Builder.node('dt', { className: 'author-rows' }, [
				Builder.node('label', { htmlFor: 'contributor-' + field_id + '-' + temp + '-status' }, 'Status' + ':*')
			]),
			Builder.node('dd', { className: 'author-rows' }, [
				Builder.node('select', { name: 'contributor[' + field_id + '][' + temp + '][status]', id: 'contributor-' + field_id + '-' + temp + '-status' }, [
					Builder.node('option', { value: 'past' }, 'Past'),
					Builder.node('option', { value: 'current' }, 'Current'),
				]),
				Builder.node('img', { className: 'do-stuff', src: './images/delete.png', alt: 'Delete icon', onclick: "$('" + dd_id + "').remove()", onmouseover: "Tip('Delete')", onmouseout: 'UnTip()' }),
			]),
		]),

		Builder.node('dl', [
			Builder.node('dt', { className: 'author-rows' }, [
				Builder.node('label', { htmlFor: 'contributor-' + field_id + '-' + temp + '-position' }, 'Position' + ':')
			]),
			Builder.node('dd', { className: 'author-rows' }, [
				Builder.node('input', { type: 'text', name: 'contributor[' + field_id + '][' + temp + '][position]', id: 'contributor-' + field_id + '-' + temp + '-position', size: '40', maxlength: '255', value: '' }),
			]),
		]),

		Builder.node('dl', [
			Builder.node('dt', { className: 'author-rows' }, [
				Builder.node('label', { htmlFor: 'contributor-' + field_id + '-' + temp + '-from' }, 'From date' + ':')
			]),
			Builder.node('dd', { className: 'author-rows' }, [
				Builder.node('input', { type: 'text', name: 'contributor[' + field_id + '][' + temp + '][from]', id: 'contributor-' + field_id + '-' + temp + '-from', size: '40', maxlength: '255', value: '' }),
			]),
		]),

		Builder.node('dl', [
			Builder.node('dt', { className: 'author-rows' }, [
				Builder.node('label', { htmlFor: 'contributor-' + field_id + '-' + temp + '-to' }, 'To date' + ':')
			]),
			Builder.node('dd', { className: 'author-rows' }, [
				Builder.node('input', { type: 'text', name: 'contributor[' + field_id + '][' + temp + '][to]', id: 'contributor-' + field_id + '-' + temp + '-to', size: '40', maxlength: '255', value: '' }),
			]),
		]),
	]);

	$(field_id).insert(element);
}

/**
* Add description
*/
function add_desc()
{
	var field_id = 'desc_' + field_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = Builder.node('dd', { id: dd_id },
	[
		Builder.node('textarea', { name: 'desc[' + field_id + '][desc]', id: 'desc_' + field_id + '_desc', rows: '5' }),
		Builder.node('span', lang_select('desc[' + field_id + '][lang]')),
		Builder.node('img', { className: 'action-text1', src: './images/delete.png', alt: 'Delete icon', onclick: "$('" + dd_id + "').remove()", onmouseover: "Tip('Delete')", onmouseout: 'UnTip()' }),
		Builder.node('img', { id: dd_id + '-plus',  className: 'action-text2', src: './images/add.png', onclick: 'document.mainform.desc_' + field_id + '_desc.rows+=4', onmouseover: "Tip('Add 4 rows')", onmouseout: 'UnTip()' }),
		Builder.node('img', { id: dd_id + '-minus',  className: 'action-text3', src: './images/del.png', onclick: 'if(document.mainform.desc_' + field_id + '_desc.rows>7){document.mainform.desc_' + field_id + '_desc.rows-=4}else{document.mainform.desc_' + field_id + '_desc.rows-=(document.mainform.desc_' + field_id + '_desc.rows-4)};', onmouseover: "Tip('Remove 4 rows')", onmouseout: 'UnTip()' }),
	]);

	$('desc-field').insert(element);
}

/**
* Add copy-field
*/
function add_copy()
{
	var field_id = 'fc_' + field_cnt++;
	var temp = 'tm_' + field_cnt++;
	var dd_id = 'dd_' + dd_cnt++;
	var dd_id2 = 'dd_' + dd_cnt++;

	var element = Builder.node('fieldset', { className: 'white', id: dd_id }, [

		Builder.node('dl', { id: field_id }, [
			Builder.node('dt', { className: 'copy-rows' }, [
				Builder.node('label', { htmlFor: 'copy-' + field_id + '-' + temp + '-from' }, 'Copy: (from -&gt; to)'),
				Builder.node('img', { className: 'sign plus-sign', src: './images/plus.png', alt: 'Add file copy', title: 'Add file copy', onclick: "add_file_copy('" + field_id + "');" }),
				Builder.node('img', { className: 'do-stuff', src: './images/delete.png', alt: 'Delete icon', onclick: "$('" + dd_id + "').remove()", onmouseover: "Tip('Delete')", onmouseout: 'UnTip()' }),
			]),
			Builder.node('dd', { className: 'copy-rows', id: dd_id2 }, [
				Builder.node('input', { type: 'text', name: 'copy[' + field_id + '][' + temp + '][from]', id: 'copy-' + field_id + '-' + temp + '-from', size: '85', maxlength: '255', value: '' }),
				Builder.node('span', ' -> '),
				Builder.node('input', { type: 'text', name: 'copy[' + field_id + '][' + temp + '][to]', size: '85', maxlength: '255', value: '' }),
				Builder.node('img', { className: 'do-stuff', src: './images/delete.png', alt: 'Delete icon', onclick: "$('" + dd_id2 + "').remove()", onmouseover: "Tip('Delete')", onmouseout: 'UnTip()' }),
			]),
		]),

	]);

	$('copy-field').insert(element);
}

/**
* Add copy-field
*/
function add_file_copy(field_id)
{
	temp = 'tm_' + field_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = Builder.node('dd', { className: 'copy-rows', id: dd_id }, [
		Builder.node('span', { style: 'margin-top: 3px' }, [
			Builder.node('input', { type: 'text', name: 'copy[' + field_id + '][' + temp + '][from]', size: '85', maxlength: '255', value: '' }),
			Builder.node('span', ' -> '),
			Builder.node('input', { type: 'text', name: 'copy[' + field_id + '][' + temp + '][to]', size: '85', maxlength: '255', value: '' }),
			Builder.node('img', { className: 'do-stuff', src: './images/delete.png', alt: 'Delete icon', onclick: "$('" + dd_id + "').remove()", onmouseover: "Tip('Delete')", onmouseout: 'UnTip()' }),
		]),
	]);

	$(field_id).insert(element);
}

/**
* Add a history-field
*/
function add_history()
{
	var field_id = 'hf_' + field_cnt++;
	var temp = 'logdd_' + dd_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = Builder.node('fieldset', { className: 'white', id: dd_id }, [
		Builder.node('dl', [
			Builder.node('dt', { className: 'history-rows' }, [
				Builder.node('label', { htmlFor: 'history-' + field_id + '-version' }, 'Version' + ':*')
			]),
			Builder.node('dd', { className: 'history-rows' }, [
				Builder.node('input', { type: 'text', name: 'history[' + field_id + '][version]', id: 'history-' + field_id + '-version', size: '10', maxlength: '255', value: '' }),
				Builder.node('img', { className: 'do-stuff', src: './images/delete.png', alt: 'Delete icon', onclick: "$('" + dd_id + "').remove()", onmouseover: "Tip('Delete this entry')", onmouseout: 'UnTip()' }),
			]),
		]),

		Builder.node('dl', [
			Builder.node('dt', { className: 'history-rows' }, [
				Builder.node('label', { htmlFor: 'history-' + field_id + '-date' }, 'Date' + ':*')
			]),
			Builder.node('dd', { className: 'history-rows' }, [
				Builder.node('input', { type: 'text', name: 'history[' + field_id + '][date]', id: 'history-' + field_id + '-date', size: '20', maxlength: '255', value: '' }),
			]),
		]),

		Builder.node('fieldset', { id: field_id , style: 'border: none;'}, [
			Builder.node('dl', [
				Builder.node('dt', { className: 'history-rows' }, [
					Builder.node('label', { htmlFor: 'history-' + field_id + '-change-' + temp + '-data' }, 'Change' + ':*'),
				]),
				Builder.node('dd', { className: 'history-rows' }, [
					Builder.node('input', { type: 'text', name: 'history[' + field_id + '][change][' + temp + '][data]', id: 'history-' + field_id + '-change-' + temp + '-data', size: '80', maxlength: '255', value: '' }),
					Builder.node('span', lang_select('history[' + field_id + '][change][' + temp + '][lang]')),
				]),
			]),
		]),

		Builder.node('input', { type: 'button', value: 'Add change', onclick: "add_history_change('" + field_id + "');" }),
	]);

	$('history-fields').insert(element);
}

/**
* Add a change to the history
*/
function add_history_change(field_id)
{
	var temp = 'logdd_' + dd_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = Builder.node('dl', { id: dd_id }, [
		Builder.node('dt', { className: 'history-rows' }, [
			Builder.node('label', { htmlFor: 'history-' + field_id + '-change-' + temp + '-data' }, 'Change' + ':')
		]),
		Builder.node('dd', { className: 'history-rows' }, [
			Builder.node('input', { type: 'text', name: 'history[' + field_id + '][change][' + temp + '][data]', id: 'history-' + field_id + '-change-' + temp + '-data', size: '80', maxlength: '255', value: '' }),
			Builder.node('span', lang_select('history[' + field_id + '][change][' + temp + '][lang]')),
			Builder.node('img', { className: 'do-stuff', src: './images/delete.png', alt: 'Delete icon', onclick: "$('" + dd_id + "').remove()", onmouseover: "Tip('Delete')", onmouseout: 'UnTip()' }),
		]),
	]);

	$(field_id).insert(element);
}

/**
* Add link-field
*/
function add_link()
{
	var field_id = 'lf_' + field_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = Builder.node('fieldset', { className: 'white', id: dd_id }, [
		Builder.node('dl', [
			Builder.node('dt', { className: 'link-rows' }, [
				Builder.node('label', { htmlFor: 'links-' + field_id + '-type' }, 'Type' + ':*')
			]),
			Builder.node('dd', { className: 'link-rows' }, [
				Builder.node('select', { name: 'links[' + field_id + '][type]', id: 'links-' + field_id + '-type]' }, [
					Builder.node('option', { value: 'contrib' }, 'Contrib'),
					Builder.node('option', { value: 'dependency'}, 'Dependency'),
					Builder.node('option', { value: 'language' }, 'Language'),
					Builder.node('option', { value: 'parent' }, 'Parent'),
					Builder.node('option', { value: 'template-lang' }, 'Template lang'),
					Builder.node('option', { value: 'template' }, 'Template'),
				]),
				Builder.node('img', { className: 'do-stuff', src: './images/delete.png', alt: 'Delete icon', onclick: "$('" + dd_id + "').remove()", onmouseover: "Tip('Delete this entry')", onmouseout: 'UnTip()' }),
			]),
		]),

		Builder.node('dl', [
			Builder.node('dt', { className: 'link-rows' }, [
				Builder.node('label', { htmlFor: 'links-' + field_id + '-title' }, 'Link title' + ':*')
			]),
			Builder.node('dd', { className: 'link-rows' }, [
				Builder.node('input', { type: 'text', name: 'links[' + field_id + '][title]', id: 'links-' + field_id + '-title', size: '80', maxlength: '255', value: '' }),
				Builder.node('span', lang_select('links[' + field_id + '][lang]')),
			]),
		]),

		Builder.node('dl', [
			Builder.node('dt', { className: 'link-rows' }, [
				Builder.node('label', { htmlFor: 'links-' + field_id + '-href' }, 'URL' + ':*')
			]),
			Builder.node('dd', { className: 'link-rows' }, [
				Builder.node('input', { type: 'text', name: 'links[' + field_id + '][href]', id: 'links-' + field_id + '-href', size: '80', maxlength: '255', value: '' }),
			]),
		]),
	]);

	$('link-field').insert(element);
}

/**
* Add notes
*/
function add_notes()
{
	var field_id = 'notes_' + field_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = Builder.node('dd', { id: dd_id },
	[
		Builder.node('textarea', { name: 'notes[' + field_id + '][note]', id: 'notes_' + field_id + '_note', rows: '5' }),
		Builder.node('span', lang_select('notes[' + field_id + '][lang]')),
		Builder.node('img', { className: 'action-text1', src: './images/delete.png', alt: 'Delete icon', onclick: "$('" + dd_id + "').remove()", onmouseover: "Tip('Delete')", onmouseout: 'UnTip()' }),
		Builder.node('img', { id: dd_id + '-plus',  className: 'action-text2', src: './images/add.png', onclick: 'document.mainform.notes_' + field_id + '_note.rows+=4', onmouseover: "Tip('Add 4 rows')", onmouseout: 'UnTip()' }),
		Builder.node('img', { id: dd_id + '-minus',  className: 'action-text3', src: './images/del.png', onclick: 'if(document.mainform.notes_' + field_id + '_note.rows>7){document.mainform.notes_' + field_id + '_note.rows-=4}else{document.mainform.notes_' + field_id + '_note.rows-=(document.mainform.notes_' + field_id + '_note.rows-4)};', onmouseover: "Tip('Remove 4 rows')", onmouseout: 'UnTip()' }),
	]);

	$('notes-field').insert(element);
}

/**
* Add diy
*/
function add_diy()
{
	var field_id = 'diy_' + field_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = Builder.node('dd', { id: dd_id },
	[
		Builder.node('textarea', { name: 'diy[' + field_id + '][diy]', id: 'diy_' + field_id + '_diy', rows: '5' }),
		Builder.node('span', lang_select('diy[' + field_id + '][lang]')),
		Builder.node('img', { className: 'action-text1', src: './images/delete.png', alt: 'Delete icon', onclick: "$('" + dd_id + "').remove()", onmouseover: "Tip('Delete')", onmouseout: 'UnTip()' }),
		Builder.node('img', { id: dd_id + '-plus',  className: 'action-text2', src: './images/add.png', onclick: 'document.mainform.diy_' + field_id + '_diy.rows+=4', onmouseover: "Tip('Add 4 rows')", onmouseout: 'UnTip()' }),
		Builder.node('img', { id: dd_id + '-minus',  className: 'action-text3', src: './images/del.png', onclick: 'if(document.mainform.diy_' + field_id + '_diy.rows>7){document.mainform.diy_' + field_id + '_diy.rows-=4}else{document.mainform.diy_' + field_id + '_diy.rows-=(document.mainform.diy_' + field_id + '_diy.rows-4)};', onmouseover: "Tip('Remove 4 rows')", onmouseout: 'UnTip()' }),
	]);

	$('diy-field').insert(element);
}

/**
* Add SQL-field
*/
function add_sql()
{
	var field_id = 'sql_' + field_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = Builder.node('fieldset', { className: 'white', id: dd_id }, [
		Builder.node('dl', [
			Builder.node('dt', { className: 'sql-rows' }, [
				Builder.node('label', { htmlFor: 'sql-' + field_id + '-dbms' }, 'DBMS' + ':')
			]),
			Builder.node('dd', { className: 'sql-rows' }, [
				Builder.node('select', { name: 'sql[' + field_id + '][dbms]', id: 'sql-' + field_id + '-dbms' }, [
					Builder.node('option', { value: 'mysql'}, 'MySQL'),
					Builder.node('option', { value: 'mysql4'}, 'MySQL 4'),
					Builder.node('option', { value: 'mysql_40'}, 'MySQL 4.0'),
					Builder.node('option', { value: 'mysql_41'}, 'MySQL 4.1'),
					Builder.node('option', { value: 'mysqli'}, 'MySQLi'),
					Builder.node('option', { value: 'mssaccess'}, 'MSS Access'),
					Builder.node('option', { value: 'oracle'}, 'Oracle'),
					Builder.node('option', { value: 'postgres'}, 'PostgreSQL'),
					Builder.node('option', { value: 'db2'}, 'DB2'),
					Builder.node('option', { value: 'firebird'}, 'FireBird'),
					Builder.node('option', { value: 'sqlite'}, 'SQLite'),
					Builder.node('option', { value: 'sql-parser', selected : 'selected'}, 'SQL Parser (' + 'Default' + ')'),
				]),
			]),
		]),

		Builder.node('dl', [
			Builder.node('dt', { className: 'sql-rows' }, [
				Builder.node('label', { htmlFor: 'sql-' + field_id + '-query' }, 'Query' + ':*')
			]),
			Builder.node('dd', { className: 'sql-rows' }, [
				Builder.node('textarea', { className: 'sql-rows', name: 'sql[' + field_id + '][query]', id: 'sql_' + field_id + '_query', rows: '5' }),
				Builder.node('img', { className: 'action-text1', src: './images/delete.png', alt: 'Delete icon', onclick: "$('" + dd_id + "').remove()", onmouseover: "Tip('Delete')", onmouseout: 'UnTip()' }),
				Builder.node('img', { id: dd_id + '-plus',  className: 'action-text2', src: './images/add.png', onclick: 'document.mainform.sql_' + field_id + '_query.rows+=4', onmouseover: "Tip('Add 4 rows')", onmouseout: 'UnTip()' }),
				Builder.node('img', { id: dd_id + '-minus',  className: 'action-text3', src: './images/del.png', onclick: 'if(document.mainform.sql_' + field_id + '_query.rows>7){document.mainform.sql_' + field_id + '_query.rows-=4}else{document.mainform.sql_' + field_id + '_query.rows-=(document.mainform.sql_' + field_id + '_query.rows-4)};', onmouseover: "Tip('Remove 4 rows')", onmouseout: 'UnTip()' }),
			]),
		]),
	]);

	$('sql-field').insert(element);
}

/**
* Add title
*/
function add_title()
{
	var field_id = 'title_' + field_cnt++;
	var dd_id = 'dd_' + dd_cnt++;

	var element = Builder.node('dd', { id: dd_id },
	[
		Builder.node('input', { type: 'text', name: 'title[' + field_id + '][title]', size: '53', maxlength: '255', value: '' }),
		Builder.node('span', lang_select('title[' + field_id + '][lang]')),
		Builder.node('img', { className: 'do-stuff', src: './images/delete.png', alt: 'Delete icon', onclick: "$('" + dd_id + "').remove()", onmouseover: "Tip('Delete')", onmouseout: 'UnTip()' }),
	]);

	$('title-field').insert(element);
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
	var element = Builder.node('select', { name: field_id }, [
		Builder.node('option', { value: 'ab' }, 'Abkhazian'),
		Builder.node('option', { value: 'aa' }, 'Afar'),
		Builder.node('option', { value: 'af' }, 'Afrikaans'),
		Builder.node('option', { value: 'sq' }, 'Albanian'),
		Builder.node('option', { value: 'am' }, 'Amharic'),
		Builder.node('option', { value: 'ar' }, 'Arabic'),
		Builder.node('option', { value: 'hy' }, 'Armenian'),
		Builder.node('option', { value: 'as' }, 'Assamese'),
		Builder.node('option', { value: 'ay' }, 'Aymara'),
		Builder.node('option', { value: 'az' }, 'Azerbaijani'),
		Builder.node('option', { value: 'ba' }, 'Bashkir'),
		Builder.node('option', { value: 'eu' }, 'Basque'),
		Builder.node('option', { value: 'bn' }, 'Bengali'),
		Builder.node('option', { value: 'dz' }, 'Bhutani'),
		Builder.node('option', { value: 'bh' }, 'Bihari'),
		Builder.node('option', { value: 'bi' }, 'Bislama'),
		Builder.node('option', { value: 'br' }, 'Breton'),
		Builder.node('option', { value: 'bg' }, 'Bulgarian'),
		Builder.node('option', { value: 'my' }, 'Burmese'),
		Builder.node('option', { value: 'be' }, 'Byelorussian'),
		Builder.node('option', { value: 'km' }, 'Cambodian'),
		Builder.node('option', { value: 'ca' }, 'Catalan'),
		Builder.node('option', { value: 'zh' }, 'Chinese'),
		Builder.node('option', { value: 'co' }, 'Corsican'),
		Builder.node('option', { value: 'hr' }, 'Croatian'),
		Builder.node('option', { value: 'cs' }, 'Czech'),
		Builder.node('option', { value: 'da' }, 'Danish'),
		Builder.node('option', { value: 'nl' }, 'Dutch'),
		Builder.node('option', { value: 'en', selected : 'selected' }, 'English'),
		Builder.node('option', { value: 'eo' }, 'Esperanto'),
		Builder.node('option', { value: 'et' }, 'Estonian'),
		Builder.node('option', { value: 'fo' }, 'Faeroese'),
		Builder.node('option', { value: 'fj' }, 'Fiji'),
		Builder.node('option', { value: 'fi' }, 'Finnish'),
		Builder.node('option', { value: 'fr' }, 'French'),
		Builder.node('option', { value: 'fy' }, 'Frisian'),
		Builder.node('option', { value: 'gl' }, 'Galician'),
		Builder.node('option', { value: 'ka' }, 'Georgian'),
		Builder.node('option', { value: 'de' }, 'German'),
		Builder.node('option', { value: 'el' }, 'Greek'),
		Builder.node('option', { value: 'kl' }, 'Greenlandic'),
		Builder.node('option', { value: 'gn' }, 'Guarani'),
		Builder.node('option', { value: 'gu' }, 'Gujarati'),
		Builder.node('option', { value: 'ha' }, 'Hausa'),
		Builder.node('option', { value: 'iw' }, 'Hebrew'),
		Builder.node('option', { value: 'hi' }, 'Hindi'),
		Builder.node('option', { value: 'hu' }, 'Hungarian'),
		Builder.node('option', { value: 'is' }, 'Icelandic'),
		Builder.node('option', { value: 'in' }, 'Indonesian'),
		Builder.node('option', { value: 'ia' }, 'Interlingua'),
		Builder.node('option', { value: 'ik' }, 'Inupiak'),
		Builder.node('option', { value: 'ga' }, 'Irish'),
		Builder.node('option', { value: 'it' }, 'Italian'),
		Builder.node('option', { value: 'ja' }, 'Japanese'),
		Builder.node('option', { value: 'jw' }, 'Javanese'),
		Builder.node('option', { value: 'kn' }, 'Kannada'),
		Builder.node('option', { value: 'ks' }, 'Kashmiri'),
		Builder.node('option', { value: 'kk' }, 'Kazakh'),
		Builder.node('option', { value: 'rw' }, 'Kinyarwanda'),
		Builder.node('option', { value: 'ky' }, 'Kirghiz'),
		Builder.node('option', { value: 'rn' }, 'Kirundi'),
		Builder.node('option', { value: 'ko' }, 'Korean'),
		Builder.node('option', { value: 'ku' }, 'Kurdish'),
		Builder.node('option', { value: 'lo' }, 'Laothian'),
		Builder.node('option', { value: 'la' }, 'Latin'),
		Builder.node('option', { value: 'lv' }, 'Lettish'),
		Builder.node('option', { value: 'ln' }, 'Lingala'),
		Builder.node('option', { value: 'lt' }, 'Lithuanian'),
		Builder.node('option', { value: 'mk' }, 'Macedonian'),
		Builder.node('option', { value: 'mg' }, 'Malagasy'),
		Builder.node('option', { value: 'ms' }, 'Malay'),
		Builder.node('option', { value: 'ml' }, 'Malayalam'),
		Builder.node('option', { value: 'mt' }, 'Maltese'),
		Builder.node('option', { value: 'mi' }, 'Maori'),
		Builder.node('option', { value: 'mr' }, 'Marathi'),
		Builder.node('option', { value: 'mo' }, 'Moldavian'),
		Builder.node('option', { value: 'mn' }, 'Mongolian'),
		Builder.node('option', { value: 'na' }, 'Nauru'),
		Builder.node('option', { value: 'ne' }, 'Nepali'),
		Builder.node('option', { value: 'no' }, 'Norwegian'),
		Builder.node('option', { value: 'oc' }, 'Occitan'),
		Builder.node('option', { value: 'or' }, 'Oriya'),
		Builder.node('option', { value: 'om' }, 'Oromo'),
		Builder.node('option', { value: 'ps' }, 'Pashto'),
		Builder.node('option', { value: 'fa' }, 'Persian'),
		Builder.node('option', { value: 'pl' }, 'Polish'),
		Builder.node('option', { value: 'pt' }, 'Portuguese'),
		Builder.node('option', { value: 'pa' }, 'Punjabi'),
		Builder.node('option', { value: 'qu' }, 'Quechua'),
		Builder.node('option', { value: 'rm' }, 'Rhaeto-Romance'),
		Builder.node('option', { value: 'ro' }, 'Romanian'),
		Builder.node('option', { value: 'ru' }, 'Russian'),
		Builder.node('option', { value: 'sm' }, 'Samoan'),
		Builder.node('option', { value: 'sg' }, 'Sangro'),
		Builder.node('option', { value: 'sa' }, 'Sanskrit'),
		Builder.node('option', { value: 'gd' }, 'Scots Gaelic'),
		Builder.node('option', { value: 'sr' }, 'Serbian'),
		Builder.node('option', { value: 'sh' }, 'Serbo-Croatian'),
		Builder.node('option', { value: 'st' }, 'Sesotho'),
		Builder.node('option', { value: 'tn' }, 'Setswana'),
		Builder.node('option', { value: 'sn' }, 'Shona'),
		Builder.node('option', { value: 'sd' }, 'Sindhi'),
		Builder.node('option', { value: 'si' }, 'Singhalese'),
		Builder.node('option', { value: 'ss' }, 'Siswati'),
		Builder.node('option', { value: 'sk' }, 'Slovak'),
		Builder.node('option', { value: 'sl' }, 'Slovenian'),
		Builder.node('option', { value: 'so' }, 'Somali'),
		Builder.node('option', { value: 'es' }, 'Spanish'),
		Builder.node('option', { value: 'su' }, 'Sudanese'),
		Builder.node('option', { value: 'sw' }, 'Swahili'),
		Builder.node('option', { value: 'sv' }, 'Swedish'),
		Builder.node('option', { value: 'tl' }, 'Tagalog'),
		Builder.node('option', { value: 'tg' }, 'Tajik'),
		Builder.node('option', { value: 'ta' }, 'Tamil'),
		Builder.node('option', { value: 'tt' }, 'Tatar'),
		Builder.node('option', { value: 'te' }, 'Tegulu'),
		Builder.node('option', { value: 'th' }, 'Thai'),
		Builder.node('option', { value: 'bo' }, 'Tibetan'),
		Builder.node('option', { value: 'ti' }, 'Tigrinya'),
		Builder.node('option', { value: 'to' }, 'Tonga'),
		Builder.node('option', { value: 'ts' }, 'Tsonga'),
		Builder.node('option', { value: 'tr' }, 'Turkish'),
		Builder.node('option', { value: 'tk' }, 'Turkmen'),
		Builder.node('option', { value: 'tw' }, 'Twi'),
		Builder.node('option', { value: 'uk' }, 'Ukrainian'),
		Builder.node('option', { value: 'ur' }, 'Urdu'),
		Builder.node('option', { value: 'uz' }, 'Uzbek'),
		Builder.node('option', { value: 'vi' }, 'Vietnamese'),
		Builder.node('option', { value: 'vo' }, 'Volapuk'),
		Builder.node('option', { value: 'cy' }, 'Welsh'),
		Builder.node('option', { value: 'wo' }, 'Wolof'),
		Builder.node('option', { value: 'xh' }, 'Xhosa'),
		Builder.node('option', { value: 'ji' }, 'Yiddish'),
		Builder.node('option', { value: 'yo' }, 'Yoruba'),
		Builder.node('option', { value: 'zu' }, 'Zulu'),
		Builder.node('option', { value: 'en' }, 'English'),
	]);

	return(element);
}
