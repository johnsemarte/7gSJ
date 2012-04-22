<?php
/**
 * Content for the show student group for project dialog box
 */
 
// Start the session handling system
session_start ();

// Return correct content type
header ('Content-type: application/json');

// Connect to the database
require_once '../db.php';

if (!isset($_GET['id']))	// Can't show a group if no id is given
	die ("Ikke anngitt hvilket prosjekt som det skal vises studentgruppe for!");

// Get the title of the project, the name of the group, the date the project group was last updated and 
// all participants from the group
// This query will return one row for each participant where the title of the project, the name of the group
// and the date information is the same for each row. Not optimal SQL but the only way to get all the 
// participants without an extra query
$sql = 'SELECT title, name, DATE_FORMAT(projectgroups.date, "%d/%m-%y") as `date`, participantid
        FROM projectrequest, projectgroups, groupparticipants, projects
		WHERE projectrequest.projectid=? AND projectrequest.priority="taken"
		AND projectrequest.groupid=projectgroups.id
		AND projects.id=projectrequest.projectid
		AND groupparticipants.groupid=projectrequest.groupid';
$sth = $db->prepare ($sql);
$sth->execute (array ($_GET['id']));		// Perform the query with the given project id
if (!$row = $sth->fetch())					// If no rows is returned this project has no registered group
	die ("Fant ikke noen gruppe for dette prosjektet");

// Print out all the information
?><div class="studentGroupForProject">
<h3><?php echo $row['title']; ?></h3>
Navn på gruppen : <b><?php echo $row['name']; ?></b><br/>&nbsp;<br/>
<label>Deltakere : </label><div id="groupParticipants"></div>
</div>
<p> &nbsp; </p>
<script type="text/javascript">
function findGroupParticipants () {		// This function is will be run from the dialog creation function
<?php
	do { 	// For each participant in the group, perform an ajax call to get the information from the user id ?>
	$.ajax ({	// Send the request to the server that has a database that is always up to date
		url : 'https://tvil.hig.no/json_services/getUserDetails.php',
		data : { 'uid': '<?php echo $row['participantid']; ?>' },	// Send the user id (the login name at HiG)
		crossDomain : true,		// This is a cross domain request
		type : 'POST',			// Use a POST request
		dataType : 'json',		// Specify that we will be receiving json encoded data
		success : function (data) {		// When data is returned
			// Append the information to the div tag with id groupParticipants
			$('#groupParticipants').append (data.givenname+' '+data.surename+'('+'<a href="mailto:'+data.email+'">'+data.email+'</a>)<br/>');
		}
	}); 
<?php
} while ($row = $sth->fetch());		// Continue until no more participants
?>
}
</script>
