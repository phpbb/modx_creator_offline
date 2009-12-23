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

// <meta>
$meta[] = array(
	'name' => 'generator',
	'content' => META,
);

foreach ($meta as $value)
{
	write_element('meta', '', array(
		'name' => $value['name'],
		'content' => $value['content'],
	), false, false);
}
// </meta>

// <license>
write_element('license', $license);

// <title>
foreach($title as $value)
{
	$text = trim($value['title']);
	if($text != '')
	{
		write_element('title', $text, array('lang' => $value['lang']));
	}
}
// </title>

// <description>
foreach($desc as $value)
{
	$text = trim($value['desc']);
	if($text != '')
	{
		write_element('description', $text, array('lang' => $value['lang']));
	}
}
// </description>

// <author-notes>
if($notes)
{
	foreach($notes as $value)
	{
		$text = trim($value['note']);
		if($text != '')
		{
			write_element('author-notes', $text, array('lang' => $value['lang']));
		}
	}
}
// </author-notes>

// <author-group>
$xml->startElement('author-group');
foreach($author as $key => $value)
{
	if(trim($value['username']) != '')
	{
		// <author>
		$xml->startElement('author');
		write_element('realname', trim($value['realname']));
		write_element('username', trim($value['username']), ((isset($value['phpbbcom'])) ? array('phpbbcom' => 'no') : false));
		write_element('homepage', trim($value['homepage']));
		write_element('email', trim($value['email']));
		if(!empty($contributor[$key]))
		{
			// <contributions-group>
			$xml->startElement('contributions-group');
			foreach($contributor[$key] as $cval)
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

// <installation>'
$xml->startElement('installation');
write_element('level', $install_level, false, false);
write_element('time', $install_time * 60, false, false);
write_element('target-version', trim($target), false, false);
$xml->endElement();
// </installation>

if($history)
{
	// <history>
	$xml->startElement('history');
	foreach($history as $value)
	{
		if(trim($value['version']) != '' && trim($value['date']) != ''  && !empty($value['change']))
		{
			// <entry>
			$xml->startElement('entry');
			write_element('date', trim($value['date']), false, false);
			write_element('rev-version', trim($value['version']), false, false);

			$hist_entry = array();
			// We need to sort the change array by language.
			foreach($value['change'] as $ckey => $cvalue)
			{
				$hist_entry[$cvalue['lang']][] = $cvalue['data'];
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
	// </history>
}

if(!empty($links))
{
	// <link-group>
	$xml->startElement('link-group');

	foreach($links as $value)
	{
		if(trim($value['title']) != '' && trim($value['href']) != '')
		{
			write_element('link', trim($value['title']), array(
				'type' => $value['type'],
				'href' => trim($value['href']),
				'lang' => $value['lang'],
			), false, false);
		}
	}
	$xml->endElement();
	// </link-group>
}

$xml->endElement();
// </header>

// <action-group>
$xml->startElement('action-group');

// SQL
if($sql)
{
	foreach($sql as $value)
	{
		if(trim($value['dbms']) != '' && trim($value['query']) != '')
		{
			write_element('sql', trim($value['query']), (($value['dbms'] != 'sql-parser') ? array('dbms' => $value['dbms']) : false));
		}
	}
}

// Copy
if($copy)
{
	foreach($copy as $value)
	{
		// <copy>
		$xml->startElement('copy');
		foreach($value as $cval)
		{
			write_element('file', '', array(
				'from' => trim($cval['from']),
				'to' => trim($cval['to']),
			), false, false);
		}
		$xml->endElement();
		// </copy>
	}
}

// And the damage...
if($modx)
{
	foreach($files as $key => $value)
	{
		if(trim($value) != '')
		{
			// <open>
			$xml->startElement('open');
			$xml->writeAttribute('src', trim($value));
			foreach($modx[$key] as $key2 => $value2)
			{
				// Array 2 edits We dont need the filenames here
				if($key2 != 'file')
				{
					// <edit>
					$xml->startElement('edit');
					$inline = $inline_action = $action = false;

					foreach($value2 as $key3 => $value3)
					{
						// Array 3, the string fields
						if($value3['type'] != '' && $value3['type'] != '-')
						{
							if($value3['type'] == 'inline-find' && $inline && $inline_action)
							{
								// </inline-edit>
								$xml->endElement();

								// <inline-edit>
								$xml->startElement('inline-edit');
								$inline = true;
								$inline_action = false;
							}
							else if(strpos($value3['type'], 'inline') !== FALSE && !$inline)
							{
								// <inline-edit>
								$xml->startElement('inline-edit');
								$inline = true;
								$inline_action = false;
							}
							else if(strpos($value3['type'], 'inline') === FALSE && $inline)
							{
								// </inline-edit>
								$xml->endElement();
								$inline = false;
								$inline_action = false;
							}

							if(strpos($value3['type'], 'inline') !== FALSE)
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
										if(isset($value2[$i]))
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

									if($stop)
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

					if($inline)
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
}

// DIY
if($diy)
{
	foreach($diy as $value)
	{
		if(trim($value['diy']) != '')
		{
			// <diy-instructions>
			write_element('diy-instructions', trim($value['diy']), array('lang' => $value['lang']));
		}
	}
}

// If the file exists it will be overwritten.

// </action-group>
$xml->endElement();

// </mod>
$xml->endElement();

$modx_data = $xml->outputMemory();

if($preview)
{
	header('content-type: text/xml');
	echo $modx_data;
	exit;
}
else if($dload)
{
	header("Content-type: file");
	header('Content-Disposition: attachment;filename=install_mod.xml');
	header('Pragma: no-cache');
	header('Expires: 0');

	echo $modx_data;
	exit;
}
