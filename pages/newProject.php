<?php
/**
 * Dialog used to create a new project.
 */
 
// Start the session handling system
session_start ();

// Connect to the database
require_once ("../db.php");

// Only allow this for external users
if (!isset ($_SESSION['uid'])||($_SESSION['type']!='external'))
	die ('Not logged in as an external user');
?>
<form class="newProject" onsubmit="return false;">
<label for="name">Navn på prosjektet</label><input type="text" name="name" title="Fullt navn på prosjektet, dette kan endres i løpet av prosessen."><br clear="both"/>
<label for="altname">Engelsk navn på prosjektet</label><input type="text" name="altname" title="Alle prosjekter skal også ha engelsk tittel."><br clear="both"/>
<label for="shortname">Forkortelse</label><input class="short" type="text" name="shortname" title="Forkortelse for prosjektet (brukes blant annet i URL for prosjektside for prosjektet.)"><br clear="both"/>
<label for="description" style="text-align: left; width: 200px">Beskrivelse av bedriften</label><br clear="both"/>
<textarea class="tinymce" name="description"></textarea>
<div>
<?php
// Show a list of all files for this user, let the user select which files to attach as attachements to this project
$sql = 'SELECT id, name, size, DATE_FORMAT(`date`, "%d/%m-%y") AS `date`, descr FROM documents WHERE owner = ? AND newVersion=0';
$sth = $db->prepare ($sql);
$sth->execute (array ($_SESSION['uid']));	// Get files for currently logged in user
$files = $sth->fetchAll();					// Get all files in one go
?><h3>Velg filer som skal legges som vedlegg til dette prosjektet</h2>
<table class="filelist">
<thead>
<tr><th>Filnavn</th><th>Størrelse</th><th>Dato</th><th>Beskrivelse</th></tr>
</thead>
<tbody>
<?php
foreach ($files as $row) {			// Loop through the files, one at time
	// Format the filesize nicely, with MB/KB
	if ($row['size']>1024*1024)		// This file is in the MB range
		$row['size'] = sprintf ("%1.2fMB", $row['size']/(1024*1024));
	else if ($row['size']>1024)		// This file is in the KB range
		$row['size'] = sprintf ("%1.2fKB", $row['size']/1024);
	// One line for each file, add a checkbox to let the user indicate what files to set as attachements for this project
	echo "<tr><td><nobr><input type='checkbox' name='selectedFiles' value='{$row['id']}'/> <a href='file.php?id={$row['id']}' target='_blank'>{$row['name']}</a></nobr></td><td>{$row['size']}</td><td>{$row['date']}</td><td>{$row['descr']}</td></tr>\n";
}
?>
</tbody>
</table>
</div>
<input type="button" value="Opprett nytt prosjekt" onclick="javascript:createNewProject(this.form);"/>
</form>
<script type="text/javascript">
function createNewProject (form) {		// This function is called when the user clicks the "Create new project" button
	var attachedFiles = new Array ();	// Create an array containing the id's of all selected files
	$('.filelist input:checked').each (function (index) {	// Find the selected files (attachement for this project)
		attachedFiles[attachedFiles.length] = this.value;	// Add the id to the array
	});
	
	var data = {							// Create an array containing data about the project to be created
		name: form.name.value,				// The name of the project
		altName: form.altname.value,		// The alternate name (english) of the project
		shortName: form.shortname.value,	// The short form of the name (url version)
		description: $('.newProject textarea[name="description"]').html(),	// Description
		attachedFiles : attachedFiles		// An array of all attached files (see above)
	};
	$.ajax ({								
		url: 'json/createNewProject.php',	// use this script to save the new project
		data: data,							// The data about the project (see above)
		type: 'POST',						// Use POST method
		success: function (data) {			// When the project is created
			if (data.error)					// Check for errors	
				alert (data.error);			// If an error occured, alert the user
			else							// Success, load the project overview page
				$('body > section').load ('pages/externalUserProjects.php');
		}
	});
}

$('.newProject textarea.tinymce').tinymce({	// Initialize the tinymce editor when the page loads
	language : 'nb', 
	// Location of TinyMCE script
	script_url : 'tinymce/jscripts/tiny_mce/tiny_mce_gzip.php',

	// General options
	theme : "advanced",
	plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,advlist,spellchecker",
 
	// Theme options
	theme_advanced_buttons1 : "spellchecker,iespell,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,|,forecolor,backcolor",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
 
	spellchecker_languages : "Norwegian=no,+English=en",

	// Example content CSS (should be your site CSS)
	content_css : "higstyles.css",

	file_browser_callback : 'tinyFileBrowser'
});
</script>
