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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en" xml:lang="en">
<head>
	<meta name="description" content="phpbb modx install file creator" />
	<meta name="keywords" content="phpbb, mods, modx, creator, generator, install file, install, mod, peetra" />
	<meta name="verify-v1" content="OOUOiEIM9yL0DnDiT4ozmmmGNryXYVph+n83gcp0xao=" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="favicon.ico" />
	<link rel="stylesheet" href="creator.css" type="text/css" />
	<title>MODX-creator</title>
	<script type="text/javascript" src="jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="modx_functions.js"></script>
</head>
<body>

	<div id="wrap">
		<div id="header">
			<h1>MODX creator</h1>
			<span style="font-size: 12px;">This creator uses JavaScript, so you have to have it enabled for this to work.</span>
		</div>
		<div id="content">
			<form action="./index.php" method="post" id="mainform" name="mainform" enctype="multipart/form-data">
				<fieldset class="outer">
					<legend>Header group</legend>
					<?php echo $error_field; ?>
					<fieldset class="inner about-mod">
						<p style="text-align: center; font-size: 1em;">You can import a MOD text or MODX file.</p>
						<dl>
							<dt><label for="upload-file">Upload file:</label></dt>
							<dd>
								<input type="hidden" name="MAX_FILE_SIZE" value="307200" />
								<input type="file" name="upload-file" id="upload-file" accept="text/xml,text/plain" size="70" />
								<input type="submit" name="submit-file" value="Submit file" onclick="this.form.target='_self';" />
							</dd>
						</dl>
					</fieldset>

					<fieldset class="inner about-mod">
						<legend>About the MOD</legend>

						<!-- MOD title -->
						<dl id="title-field"<?php echo ((isset($error['title'])) ? ' class="error-dl"' : ''); ?>>
							<dt>
								<label for="title">Title:*</label>
								<img class="sign plus-sign" src="./images/plus.png" alt="Add title in some other language" title="Add title in some other language" onclick="add_title();" />
								<img class="sign" src="./images/info.png" alt="Info icon" title="One title in English is required, but you can use titles in other languages as well." />
							</dt>
							<?php echo $title_fields; ?>
						</dl>

						<!-- MOD description -->
						<dl id="desc-field"<?php echo ((isset($error['desc'])) ? ' class="error-dl"' : ''); ?>>
							<dt>
								<label for="desc">Description:*</label>
								<img class="sign plus-sign" src="./images/plus.png" alt="Add a description in another language." title="Add a description in another language." onclick="add_desc();" />
								<img class="sign" src="./images/info.png" alt="Info icon" title="One description in English is required. You can add descriptions in other languages as well. This needs to be descriptive; &quot;Install instructions for my MOD&quot; is not enough." />
							</dt>
							<?php echo $desc_fields; ?>
						</dl>

						<!-- MOD version -->
						<dl<?php echo ((isset($error['version'])) ? ' class="error-dl"' : ''); ?>>
							<dt>
								<label for="version">MOD version:*</label>
								<img class="sign" src="./images/info.png" alt="Info icon" title="All versions less than 1.0.0 are development versions" />
							</dt>
							<dd><input type="text" name="version" id="version" size="10" maxlength="25" value="<?php echo gen_value($version); ?>" /></dd>
						</dl>

						<!-- phpBB version -->
						<dl<?php echo ((isset($error['target'])) ? ' class="error-dl"' : ((isset($warning['target'])) ? ' class="warning-dl"' : '')); ?>>
							<dt>
								<label for="target">Target version:*</label>
								<img class="sign" src="./images/info.png" alt="Info icon" title="Version of phpBB for which the MOD was developed. This should be an exact version, for example 3.0.5." />
							</dt>
							<dd>
								<input type="text" name="target" id="target" size="10" maxlength="25" value="<?php echo gen_value($target); ?>" />
								The latest phpBB version is: <?php echo PHPBB_LATEST; ?>
							</dd>
						</dl>

						<!-- Installation level -->
						<dl<?php echo ((isset($error['install_level'])) ? ' class="error-dl"' : ''); ?>>
							<dt>
								<label for="install_level">Installation level:*</label>
							</dt>
							<dd>
								<select name="install_level" id="install_level">
									<option value="easy"<?php echo (($install_level == 'easy') ? ' selected="selected"' : ''); ?>>Easy</option>
									<option value="intermediate"<?php echo (($install_level == 'intermediate') ? ' selected="selected"' : ''); ?>>Intermediate</option>
									<option value="advanced"<?php echo (($install_level == 'advanced') ? ' selected="selected"' : ''); ?>>Advanced</option>
								</select>
							</dd>
						</dl>

						<!-- Installation time -->
						<dl<?php echo ((isset($error['install_time'])) ? ' class="error-dl"' : ''); ?>>
							<dt>
								<label for="install_time">Installation time:*</label>
								<img class="sign" src="./images/info.png" alt="Info icon" title="Estimated time needed to install this MOD by hand, in minutes." />
							</dt>
							<dd><input type="text" name="install_time" id="install_time" size="2" maxlength="3" value="<?php echo $install_time; ?>" /> Minutes</dd>
						</dl>

						<!-- License -->
						<dl>
							<dt>
								<label for="license">License:*</label>
								<img class="sign" src="./images/info.png" alt="Info icon" title="The name of the license the MOD is released under, along with a URL to the full text of that license. The MOD should be submitted under the GPL v2. Submitting MODs under GPL v3 isn’t allowed because phpBB3 is released under GPL v2 only and the GPL v3 isn’t compatible with v2. You are also allowed to submit it under a license compatible with GPL v2. Be sure to change the license within the textbox and include a copy of the license in license.txt." />
							</dt>
							<dd><input type="text" name="license" id="license" size="70" maxlength="255" value="<?php echo $license; ?>" /></dd>
						</dl>

						<!-- Author notes -->
						<dl id="notes-field">
							<dt>
								<label for="notes">Author notes:</label>
								<img class="sign plus-sign" src="./images/plus.png" alt="Add author notes" title="Add author notes" onclick="add_notes();" />
								<img class="sign" src="./images/info.png" alt="Info icon" title="You can add any notes in any language you want." />
							</dt>
							<?php echo $notes_fields; ?>
						</dl>

					</fieldset>

					<!-- Author fields -->
					<fieldset class="inner">
						<legend>About the author(s)</legend>
						<div id="authors">
							<?php echo $author_fields; ?>
						</div>
						<input type="button" value="Add author" onclick="add_author();" />
						<img class="sign" src="./images/info.png" alt="Info icon" title="The username is required for each author. One author is required. The other author fields are optional." />
					</fieldset>

					<!-- History fields -->
					<fieldset class="inner">
						<legend>History</legend>
						<div id="history-fields">
							<?php echo $history_fields; ?>
						</div>
						<input type="button" value="Add changelog entry" onclick="add_history();" />
						<img class="sign" src="./images/info.png" alt="Info icon" title="You can have as many changelog fields as you want, or none. If you choose to have a changelog, all fields are required. You must have at least one change per changelog entry, but can have as many as you want. If you want to reverse the changelog order to add more changelogs at the end, check the ’Reverse changelog order’-checkbox and hit preview." />
						<label style="font-size: .8em">RSort history newest version first
						<input type="checkbox" name="reverse_history" /></label>
					</fieldset>
				</fieldset>

				<fieldset id="links-copy-sql" class="outer">
					<legend>Links, File copy and SQL</legend>

					<!-- Link fields -->
					<fieldset class="inner">
						<legend>Links</legend>
						<div id="link-field">
							<?php echo $link_fields; ?>
						</div>
						<input type="button" value="Add link" onclick="add_link();" />
						<img class="sign" src="./images/info.png" alt="Info icon" title="Links are optional. You can have one or more of the following link types. Dependency – A MOD that is required before the current MOD can be installed. Parent – Link from a contrib, language, or template back to the parent MODX file. Template – Link to template-specific instructions. Language – Link to instructions for installing a non-English translation. Contrib – Link to a contribution, for example an additional feature or upgrade from an older version. Template-lang – Link to a template and language specific instruction. Text – A text file. PHP install file – A php install file that needs to be run in order to install the MOD. All fields are required." />
					</fieldset>

					<!-- File copy -->
					<fieldset class="inner">
						<legend>File copy</legend>
						<div id="copy-field">
							<?php echo $copy_fields; ?>
						</div>
						<input type="button" value="Add copy field" onclick="add_copy();" />
						<img class="sign" src="./images/info.png" alt="Info icon" title="Both from and to fields are required. You can have as many copy fields as you need. The from field must contain the relative path from the current MODX file to the source file. Example: root/survey.php. Can also be a wildcard match using *.* Example: root/*.*. The to field must contain the the relative path from the phpBB root to the destination of the file. Should be an exact filename if an exact file name was given in the from attribute, or a directory name if a wildcard was used." />
					</fieldset>

					<!-- SQL querys -->
					<fieldset class="inner">
						<legend>SQL</legend>
						<div id="sql-field">
							<?php echo $sql_fields; ?>
						</div>
						<input type="button" value="Add SQL query" onclick="add_sql();" />
						<img class="sign" src="./images/info.png" alt="Info icon" title="You can have as many SQL fields as you want. DBMS is the dialect of the query, it defaults to sql-parser and leave it at that if you are not sure it would be set to something else. You can have multiple queries in the same field separated by a line feed." />
					</fieldset>

				</fieldset>

				<fieldset class="outer actions-fieldset">
					<legend>Lets’s do some damage</legend>
					<div id="modx-field">
						<?php echo $modx_fields; ?>
					</div>
					<input type="button" value="Add file" onclick="act_file();" />
					<img class="sign" src="./images/info.png" alt="Info icon" title="Files will be ordered alphabetically within their directories (files first)." />
				</fieldset>

				<fieldset class="outer diy-fieldset">
					<legend>DIY, Do It Yourself</legend>
					<!-- Do It Yourseld -->
					<dl id="diy-field">
						<dt>
							<img class="sign plus-sign" src="./images/plus.png" alt="Add icon']; ?>" title="Add a DIY field" onclick="add_diy();" />
							<img class="sign" src="./images/info.png" alt="Info icon" title="You can have multiple DIY fields or none, Do It Yourself Instructions, or instructions that cannot be described accurately using the other MODX commands." />
						</dt>
						<?php echo $diy_fields; ?>
					</dl>

				</fieldset>

				<fieldset class="submit-buttons">
					<?php echo $meta_fields; ?>
					<input class="submit" type="submit" name="preview" value="View" onclick="this.form.target='_blank';" />
					<input class="submit" type="submit" name="dload" value="Download" onclick="this.form.target='_self';" />
				</fieldset>
			</form>
		</div>
	</div>

</body>
</html>
