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

/**
 * This got a bit hackish.
 * This file converts the data from the parser to the arrays used by the Creator.
 * This file is however intended to be removed in the future and the Creator rewritten to use the parsers directly.
 * First I need to add a new MOD template parser and a parser for the post data.
 */

$license = $parser->get_modx_license();
$version = $parser->get_modx_mod_version();
$target = $parser->get_modx_target_version();
$install_level = $parser->get_modx_installation_level(true);
$install_time = $parser->get_modx_installation_time(false);
while($temp_data = $parser->get_modx_title())
{
	$temp_data['title'] = $temp_data['data'];
	unset($temp_data['data']);
	$title[] = $temp_data;
}

while($temp_data = $parser->get_modx_description())
{
	$temp_data['desc'] = $temp_data['data'];
	unset($temp_data['data']);
	$desc[] = $temp_data;
}

while($temp_data = $parser->get_modx_notes())
{
	$temp_data['note'] = $temp_data['data'];
	unset($temp_data['data']);
	$notes[] = $temp_data;
}

while($temp_data = $parser->get_modx_diy())
{
	$temp_data['diy'] = $temp_data['data'];
	unset($temp_data['data']);
	$diy[] = $temp_data;
}

while($temp_data = $parser->get_modx_authors())
{
	if (!empty($temp_data['contributions']))
	{
		$contributor[] = $temp_data['contributions'];
	}
	unset($temp_data['contributions']);
	$author[] = $temp_data;
}

while($temp_data = $parser->get_modx_links())
{
	$links[] = $temp_data;
}

while($temp_data = $parser->get_modx_history())
{
	// Need to change the array to be compatible with how the creator works.
	foreach ($temp_data['changelog'] as &$value)
	{
		foreach ($value['change'] as &$value2)
		{
			$temp_data['change'][] = array(
				'lang' => $value['lang'],
				'data' => $value2,
			);
		}
		unset($value['lang']);
	}
	unset($value, $value2);
	unset($temp_data['changelog']);

	$history[] = $temp_data;
}

while($temp_data = $parser->get_modx_meta())
{
	if(!empty($temp_data['content']))
	{
		$meta[] = $temp_data;
	}
}

while($temp_data = $parser->get_modx_sql())
{
	if(!empty($temp_data['data']))
	{
		$temp_data['query'] = $temp_data['data'];
		unset($temp_data['data']);
		$sql[] = $temp_data;
	}
}

while($temp_data = $parser->get_modx_copy())
{
	if(!empty($temp_data['from']))
	{
		$copy[0][] = $temp_data;
	}
}

while($temp_data = $parser->get_modx_action())
{
	$modx[] = $temp_data;
}
