<?php
/**
 * Dialog used to edit a project.
 */
 
// Start the session handling system
session_start ();

// Connect to the database
require_once ("../db.php");

// Only allow this for external users
if (!isset ($_SESSION['uid'])||($_SESSION['type']!='external'))
	die ('Not logged in as an external user');
	
// Get information about this project
$sql = 'SELECT * FROM projects WHERE id=? and owner=?';
$sth = $db->prepare ($sql);
$sth->execute (array ($_GET['id'], $_SESSION['uid']));
if (!$row = $sth->fetch())		// No information found for the given ID
	die ("Fant ikke prosjektet");
?>
<form class="editProject" onsubmit="return false;">
<label for="status">Status på prosjektet:</label><?php
// Give different messages and options depending on the status of the project
if ($row['status'] == 'draft')	// If a project is in draft, give the option to submit it
	echo ('<img src="LnF/DocumentDraw.png"/> Utkast &nbsp; &nbsp; <input type="checkbox" name="status" value="submitted"/> Lever endelig versjon<br/>');
else if ($row['status'] == 'submitted')	// If a project is submited, give the opiton to withdraw it back to draft mode
	echo ('<img src="LnF/DocumentIn.png"/> Endelig versjon &nbsp; &nbsp; <input type="checkbox" name="status" value="draft"/> Endre tilbake til utkast<br/>');
else if ($row['status'] == 'need reworking')	// If a project has been checked and found that it need more work, give the option to say that it is now rewritten. Also, comments from the collegium can be viewed
	echo ('<img src="LnF/RedFlag.png" title="Se kommentarer fra fagmiljøet"/> Trenger flere detaljer &nbsp; &nbsp; <input type="checkbox" name="status" value="reworked"/> Lever bearbeidet versjon<br/><label>&nbsp;</label><a href="javascript:viewCollegiumComments();">Se kommentarer fra fagmiljøet</a><br/>');
else if ($row['status'] == 'reworked')			// A reworked/rewritten project has no options
	echo ('<img src="LnF/GreenFlag.png"> Bearbeidet utgave er levert<br/>');
else if ($row['status'] == 'cleared')			// A project that has the OK from the collegium has no options
	echo ('<img src="LnF/GoalFlag.png"> Klarert av fagmiljøet<br/>');
else if ($row['status'] == 'denied')			// A project that has been refused by the collegium has no options but the user can view the comments from the collegium
	echo ('<img src="LnF/DeleteDocument.png"> Oppgaven er avvist av fagmiljøet<br/><label>&nbsp;</label><a href="javascript:viewCollegiumComments();">Se kommentarer fra fagmiljøet</a><br/>');
else if ($row['status'] == 'given')				// A project that has been assigned to a student group has no options but the user can view the group that the project has been assigned to
	echo ('<img src="LnF/Users.png"> Oppgaven er gitt til en studentgruppe<br/><label>&nbsp;</label><a href="javascript:viewProjectGroupForProject();">Se studentgruppen</a><br/>');
?>
	
<label for="name">Navn på prosjektet</label><input type="text" name="name" value="<?php echo $row['title']; ?>" title="Fullt navn på prosjektet, dette kan endres i løpet av prosessen."><br clear="both"/>
<label for="altname">Engelsk navn på prosjektet</label><input type="text" name="altname" value="<?php echo $row['altTitle']; ?>" title="Alle prosjekter skal også ha engelsk tittel."><br clear="both"/>
<label for="shortname">Forkortelse</label><input class="short" type="text" name="shortname" value="<?php echo $row['shortTitle']; ?>" title="Forkortelse for prosjektet (brukes blant annet i URL for prosjektside for prosjektet.)"><br clear="both"/>
<label for="description" style="text-align: left; width: 200px">Beskrivelse av bedriften</label><br clear="both"/>
<textarea class="tinymce" name="description"><?php echo $row['description']; ?></textarea>
<div>
<?php
// Show an overview of all files, check the ones that has been marked as attachements for this project
$sql = 'SELECT id, name, size, DATE_FORMAT(`date`, "%d/%m-%y") AS `date`, descr FROM documents WHERE owner = ? AND newVersion=0';
$sth = $db->prepare ($sql);
$sth->execute (array ($_SESSION['uid']));		// Run the query on the database
$files = $sth->fetchAll();						// Get all files at once
?><h3>Velg filer som skal legges som vedlegg til dette prosjektet</h2>
<table class="filelist">
<thead>
<tr><th>Filnavn</th><th>Størrelse</th><th>Dato</th><th>Beskrivelse</th></tr>
</thead>
<tbody>
<?php
// Query to check if a given document is attached to this project
$sql = 'SELECT * FROM projectdocuments WHERE projectid=? and documentid=?';
$sth = $db->prepare ($sql);
foreach ($files as $row) {		// Go through all files for this user
	// Calculate MB/KB sizes if appropriate
	if ($row['size']>1024*1024)	// This file is in the MB range
		$row['size'] = sprintf ("%1.2fMB", $row['size']/(1024*1024));
	else if ($row['size']>1024)	// This file is in the KB range
		$row['size'] = sprintf ("%1.2fKB", $row['size']/1024);
	$sth->execute (array($_GET['id'], $row['id']));	// Check to see if this file is attached to this project
	if ($row1 = $sth->fetch())	// If this file is attached to this project, place a checkmark in the checkbox
		echo "<tr><td><nobr><input type='checkbox' checked='CHECKED' name='selectedFiles' value='{$row['id']}'/> <a href='file.php?id={$row['id']}' target='_blank'>{$row['name']}</a></nobr></td><td>{$row['size']}</td><td>{$row['date']}</td><td>{$row['descr']}</td></tr>\n";
	else						// If this file is not attached to this project, do not place a checkmark in the checkbox
		echo "<tr><td><nobr><input type='checkbox' name='selectedFiles' value='{$row['id']}'/> <a href='file.php?id={$row['id']}' target='_blank'>{$row['name']}</a></nobr></td><td>{$row['size']}</td><td>{$row['date']}</td><td>{$row['descr']}</td></tr>\n";
}
?>
</tbody>
</table>
</div>
<input type="button" value="Lagre endringer i prosjektet" onclick="javascript:saveUpdatedProject(this.form);"/>
</form>
<script type="text/javascript">
function saveUpdatedProject (form) {		// This function is called when the save changes button is pressed
	var attachedFiles = new Array ();		// Create an array containing id's for all checked files
	$('.filelist input:checked').each (function (index) {
		attachedFiles[attachedFiles.length] = this.value;
	});
	
	var data = {	// Create an array of all data to post
		id: <?php echo $_GET['id']; ?>,		// The id of the project
		status: $('.editProject input[name="status"]:checked').val(),	// The value of the selected status (if any)
		name: form.name.value,				// The name of the project
		altName: form.altname.value,		// English title of this project
		shortName: form.shortname.value,	// Short form of the name
		description: $('.editProject textarea[name="description"]').html(),		// Description of the project
		attachedFiles : attachedFiles		// The attached files
	};
	$.ajax ({		// Use ajax to save data
		url: 'json/saveUpdatedProject.php',		// Script used to save data for the project
		data: data,								// Data to be stored (see above)
		type: 'POST',
		success: function (data) {				// When the data is saved
			if (data.error)						// If an error occured
				alert (data.error);				// Give the user a notice as to the error
			else								// Everything went fine, reload the projects overview page
				$('body > section').load ('pages/externalUserProjects.php');
		}
	});
}

$('.editProject textarea.tinymce').tinymce({	// Initialize the tinymce editor when the page loads
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

	file_browser_callback : 'tinyFileBrowser'	// Script used to display a dialog to let the user select files from the database
												// See bachelorthesis.js for this script
});

function viewCollegiumComments() {		// Called when the user clicks the link to display the comments from the collegium
	// Create the dialog and load the appropriate information
	var dialog = $('<div></div>').load('dialogs/collegiumCommentsForProject.php?id=<?php echo $_GET['id']; ?>').dialog({
		autoOpen: true,							// Open the dialog immediately
		position: [200,100],					// Where to open the dialog
		width: 550,								// Width of the dialog
		title: 'Fagmiljøets kommentarer til oppgaven.',
		close: function (event, ui) {			// When the dialog closes, remove the dialog
			$(this).remove();					// Remove this dialog (Important, we don't want a bunch of dialogs floating around.)
		}
	});
}

function viewProjectGroupForProject() {	// Called when the user clicks the link to display the student group for a project
	// Create the dialog and load the appropriate information
	var dialog = $('<div></div>').load('dialogs/studentGroupForProject.php?id=<?php echo $_GET['id']; ?>', function () { findGroupParticipants() ; }).dialog({
		autoOpen: true,
		position: [200,100],
		width: 550,
		title: 'Studentgruppe som er tildelt oppgaven.',
		close: function (event, ui) {			// When the dialog closes, remove the dialog
			$(this).remove();
		}
	});
}
</script>
