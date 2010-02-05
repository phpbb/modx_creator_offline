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
if (!defined('IN_MODX'))
{
	exit;
}

$xml = new XMLWriter();
$xml->openMemory();
$xml->setIndent(true);
$xml->setIndentString("\t");

// The header
$xml->startDocument('1.0', 'UTF-8', 'yes');
$xml->writePi('xml-stylesheet', 'type="text/xsl" href="modx.prosilver.en.xsl"');
$xml->writeComment('NOTICE: Please open this file in your web browser. If presented with a security warning, you may safely tell it to allow the blocked content.');
$xml->writeComment('For security purposes, please check: http://www.phpbb.com/mods/ for the latest version of this MOD.\nAlthough MODs are checked before being allowed in the MODs Database there is no guarantee that there are no security problems within the MOD.\nNo support will be given for MODs not found within the MODs Database which can be found at http://www.phpbb.com/mods/');

// <mod>
$xml->startElement('mod');
$xml->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
$xml->writeAttribute('xmlns', 'http://www.phpbb.com/mods/xml/' . MODX_LATEST);


// <header>
$xml->startElement('header');

// Need to reset the counters.
$parser->modx_reset();

// <meta>
// Start with the own meta tag
write_element('meta', '', array(
	'name' => 'generator',
	'content' => META,
), false, false);

while ($meta = $parser->get_modx_meta())
{
	write_element('meta', '', array(
		'name' => $meta['name'],
		'content' => $meta['content'],
	), false, false);
}
// </meta>

// <license>
write_element('license', $license);

// <title>
while ($title = $parser->get_modx_title())
{
	$text = trim($title['title']);
	if ($text != '')
	{
		write_element('title', $text, array('lang' => $title['lang']));
	}
}
// </title>

// <description>
while ($desc = $parser->get_modx_description())
{
	$text = trim($desc['desc']);
	if ($text != '')
	{
		write_element('description', $text, array('lang' => $desc['lang']));
	}
}
// </description>

// <author-notes>
while ($notes = $parser->get_modx_notes())
{
	$text = trim($notes['note']);
	if ($text != '')
	{
		write_element('author-notes', $text, array('lang' => $notes['lang']));
	}
}
// </author-notes>

// <author-group>
$xml->startElement('author-group');
while ($author = $parser->get_modx_authors())
{
	if (trim($author['username']) != '')
	{
		// <author>
		$xml->startElement('author');
		write_element('realname', trim($author['realname']));
		write_element('username', trim($author['username']), ((isset($author['phpbbcom'])) ? array('phpbbcom' => 'no') : false));
		write_element('homepage', trim($author['homepage']));
		write_element('email', trim($author['email']));
		if (!empty($author['contributions']))
		{
			// <contributions-group>
			$xml->startElement('contributions-group');
			foreach($author['contributions'] as $cval)
			{
				// <contributions>
				write_element('contributions', '', array(
					'status' => $cval['status'],
					'from' => trim($cval['from']),
					'to' => trim($cval['to']),
					'position' => trim($cval['position']),
				));
				// </contributions>
			}
			$xml->endElement();
			// </contributions-group>
		}
		$xml->endElement();
		// </author>
	}
}
$xml->endElement();
// </author-group>

// <mod-version>
write_element('mod-version', trim($version), false, false);

// <installation>
$xml->startElement('installation');
write_element('level', $install_level, false, false);
write_element('time', $install_time * 60, false, false);
write_element('target-version', trim($target), false, false);
$xml->endElement();
// </installation>

// <history>
if ($parser->count_history())
{
	$xml->startElement('history');
	while ($history = $parser->get_modx_history())
	{
		if (trim($history['version']) != '' && trim($history['date']) != ''  && !empty($history['changelog']))
		{
			// <entry>
			$xml->startElement('entry');
			write_element('date', trim($history['date']), false, false);
			write_element('rev-version', trim($history['version']), false, false);

			$hist_entry = array();
			// We need to sort the change array by language.
			foreach($history['changelog'] as $ckey => $cvalue)
			{
				$hist_entry[$cvalue['lang']][] = $cvalue['change'];
			}

			// Now dump out the sorted history
			foreach($hist_entry as $ckey => $cvalue)
			{
				// <changelog>
				$xml->startElement('changelog');
				$xml->writeAttribute('lang', $ckey);
				foreach($cvalue as $entry)
				{
					write_element('change', trim($entry));
				}
				$xml->endElement();
				// </changelog>
			}
			unset($hist_entry);
			$xml->endElement();
			// </entry>
		}
	}
	$xml->endElement();
}
// </history>

// <link-group>
if ($parser->count_link())
{
	$xml->startElement('link-group');

	while ($links = $parser->get_modx_links())
	{
		if (trim($links['title']) != '' && trim($links['href']) != '')
		{
			$link_arr = array(
				'type' => $links['type'],
				'href' => trim($links['href']),
				'lang' => $links['lang'],
			);

			if (!empty($links['realname']))
			{
				$link_arr = array_merge($link_arr, array('realname' => $links['realname']));
			}

			write_element('link', trim($links['title']), $link_arr, false, false);
		}
	}
	$xml->endElement();
}
// </link-group>

$xml->endElement();
// </header>

// <action-group>
$xml->startElement('action-group');

// <sql>
if ($parser->count_sql())
{
	while ($sql = $parser->get_modx_sql())
	{
		if (trim($sql['dbms']) != '' && trim($sql['query']) != '')
		{
			write_element('sql', trim($sql['query']), (($sql['dbms'] != 'sql-parser') ? array('dbms' => $sql['dbms']) : false));
		}
	}
}
// </sql>

// <copy>
if ($parser->count_copy())
{
	$xml->startElement('copy');
	while ($copy = $parser->get_modx_copy())
	{
		write_element('file', '', array(
			'from' => trim($copy['from']),
			'to' => trim($copy['to']),
		), false, false);
	}
	$xml->endElement();
}
// </copy>

// And the damage...
if ($parser->count_action())
{
	while ($action = $parser->get_modx_action())
	{
		// <open>
		$xml->startElement('open');
		$xml->writeAttribute('src', trim($action['file']));
		foreach ($action as $key2 => $value2)
		{
			// Array 2 edits We dont need the filenames here
			if (is_int($key2) || $key2 != 'file')
			{
				// <edit>
				$xml->startElement('edit');
				$inline = $inline_action = $action = false;

				foreach($value2 as $key3 => $value3)
				{
					// Array 3, the string fields
					if ($value3['type'] != '' && $value3['type'] != '-')
					{
						if ($value3['type'] == 'inline-find' && $inline && $inline_action)
						{
							// </inline-edit>
							$xml->endElement();

							// <inline-edit>
							$xml->startElement('inline-edit');
							$inline = true;
							$inline_action = false;
						}
						else if (strpos($value3['type'], 'inline') !== FALSE && !$inline)
						{
							// <inline-edit>
							$xml->startElement('inline-edit');
							$inline = true;
							$inline_action = false;
						}
						else if (strpos($value3['type'], 'inline') === FALSE && $inline)
						{
							// </inline-edit>
							$xml->endElement();
							$inline = false;
							$inline_action = false;
						}

						if (strpos($value3['type'], 'inline') !== FALSE)
						{
							// Remove newlines from inlines.
							sanitize_inlines($value3['data']);
						}

						// Now lets make the real changes...
						switch($value3['type'])
						{
							case 'inline-find':
								write_element('inline-find', $value3['data']);
								$action = true;
							break;

							case 'inline-after-add':
								write_element('inline-action', $value3['data'], array('type' => 'after-add'));
								$inline_action = true;
								$action = true;
							break;

							case 'inline-before-add':
								write_element('inline-action', $value3['data'], array('type' => 'before-add'));
								$inline_action = true;
								$action = true;
							break;

							case 'inline-replace-with':
								write_element('inline-action', $value3['data'], array('type' => 'replace-with'));
								$inline_action = true;
								$action = true;
							break;

							case 'inline-operation':
								write_element('inline-action', $value3['data'], array('type' => 'operation'));
								$inline_action = true;
								$action = true;
							break;

							case 'find':
								if ($action)
								{
									// There has been a action or inline-* since the edit was opened.
									// Each find should typically start a new edit.
									// Let's assume the user doesn't know what (s)he is doing and start a new edit.
									if ($inline)
									{
										// </inline-edit>
										$xml->endElement();
										$inline = $inline_action = false;
									}
									// </edit>
									$xml->endElement();
									$action = false;
									// <edit>
									$xml->startElement('edit');
								}
								write_element('find', $value3['data']);
							break;

							case 'comment':
								if ($action)
								{
									$stop = false;
									// If this comment is followed by a find it should start a new edit.
									$i = $key + 1;
									if (isset($value2[$i]))
									{
										if ($value2[$i]['type'] == 'find')
										{
											$stop = true;
										}
										else if ($value2[$key3 + 1]['type'] == 'comment')
										{
											// We need to check the next element that is not a comment.
											while (isset($value2[$i]) && $value2[$i]['type'] == 'comment')
											{
												$i++;
											}
											$stop = ($value2[$i]['type'] == 'find') ? true : false;
										}
									}
								}

								if ($stop)
								{
									if ($inline)
									{
										// </inline-edit>
										$xml->endElement();
										$inline = $inline_action = false;
									}
									// </edit>
									$xml->endElement();
									$action = false;
									// <edit>
									$xml->startElement('edit');
								}
								write_element('comment', $value3['data'], array('lang' => ((isset($value3['lang'])) ? $value3['lang'] : 'en')));
							break;

							default:
								write_element('action', $value3['data'], array('type' => $value3['type']));
								$action = true;
							break;
						}
					}
				}

				if ($inline)
				{
					// </inline-edit>
					$xml->endElement();
				}
				// </edit>
				$xml->endElement();
			}
		}
		// </open>
		$xml->endElement();
	}
}

// DIY
if ($parser->count_diy())
{
	while ($diy = $parser->get_modx_diy())
	{
		if (trim($diy['diy']) != '')
		{
			// <diy-instructions>
			write_element('diy-instructions', trim($diy['diy']), array('lang' => $diy['lang']));
		}
	}
}

// If the file exists it will be overwritten.

// </action-group>
$xml->endElement();

// </mod>
$xml->endElement();

$modx_data = $xml->outputMemory();

if ($preview)
{
	header('content-type: text/xml');
	echo $modx_data;
	exit;
}
else if ($dload)
{
	header("Content-type: file");
	header('Content-Disposition: attachment;filename=install_mod.xml');
	header('Pragma: no-cache');
	header('Expires: 0');

	echo $modx_data;
	exit;
}
