<?php
/**
 * Page used to display a list of external users files
 * Also allows for uploading new files/new versions of files.
 */

// Start the session handling system
session_start ();

// Connect to the database
require_once ("../db.php");

// Only allow this for external users
if (!isset ($_SESSION['uid'])||($_SESSION['type']!='external'))
	die ('Not logged in as an external user');

// Get the id, status, title, date and a flag indicating if there are any comments from the collegium for all projects for this user
$sql = 'SELECT id, status, title, DATE_FORMAT(`date`, "%d/%m-%y") AS `date`, collegiumComments!="" as comments FROM projects WHERE owner = ? ORDER BY date';
$sth = $db->prepare ($sql);
$sth->execute (array ($_SESSION['uid']));		// Get project information for the logged in user
$projects = $sth->fetchAll();						// Get all projects at once
?><h2>Liste over dine prosjekter</h2>
<table class="projectlist">
<thead>
<tr><th>Status</th><th>Navn</th><th>Dato</th><th>Kommentarer</th></tr>
</thead>
<tbody>
<?php
foreach ($projects as $row) {		// Go through one project at a time
	// Set the status image according the the status of the project
	if ($row['status']=='draft')
		$status = '<img src="LnF/DocumentDraw.png" title="Utkast"/>';
	else if ($row['status']=='submitted')
		$status = '<img src="LnF/DocumentIn.png" title="Levert prosjektforslag"/>';
	else if ($row['status']=='need reworking')
		$status = '<img src="LnF/RedFlag.png" title="Prosjektforslag trenger bearbeidelse"/>';
	else if ($row['status']=='reworked')
		$status = '<img src="LnF/GreenFlag.png" title="Prosjektforslag er bearbeidet"/>';
	else if ($row['status']=='cleared')
		$status = '<img src="LnF/GoalFlag.png" title="Prosjektforslag er godkjent"/>';
	else if ($row['status']=='denied')
		$status = '<img src="LnF/DeleteDocument.png" title="Prosjektforslag er avvist"/>';
	else if ($row['status']=='given')
		$status = '<img src="LnF/Users.png" title="Prosjektforslag er tildelt en prosjektgruppe"/>';
	if ($row['comments'])	// If there are comments on the project, show a link to open a dialog with the comments
		$comments = '<a href="javascript:viewCollegiumComments('.$row['id'].');">Se kommentarer</a>';
	else					// No comments from the collegium
		$comments = 'Ingen kommentarer';
	// Display one row for each project, make the project title a link to edit the project
	echo "<tr><td>$status</td><td><a href='javascript:editProject({$row['id']});' title='Rediger prosjektet'>{$row['title']}</a></td><td>{$row['date']}</td><td>$comments</td></tr>\n";
}
?>
</tbody>
</table>
<script type="text/javascript">
function editProject (id) {		// User clicked a project title
	$('body > section').load ('pages/editProject.php?id='+id);	// Load the editing page for the chosen project
}

function viewCollegiumComments(id) {	// User click link to view comments from the collegium
	// Create and open a dialog to show the comments from the collegium
	var dialog = $('<div></div>').load('dialogs/collegiumCommentsForProject.php?id='+id).dialog({
		autoOpen: true,
		position: [200,100],
		width: 550,
		title: 'Fagmiljøets kommentarer til oppgaven.',
		close: function (event, ui) {	// When the dialog is closed, remove the dialog
			$(this).remove();
		}
	});
}
</script>