<?php
/**
 * Content for the dialog used to show the comments from the collegium on a project.
 */

// Start the session handling system
session_start ();

// Return correct content type
header ('Content-type: application/json');

// Connect to the database
require_once '../db.php';

if (!isset($_GET['id']))		// We need to know what project we should show the comments for
	die ("Ikke anngitt hvilket prosjekt som det skal vises kommentarer for!");

// Get the title of the project, the date the comment was last updated, the actual comment and basic information
// about the company
$sql = 'SELECT title, DATE_FORMAT(`date`, "%d/%m-%y") as `date`, collegiumComments, companyname, givenname, surename 
        FROM projects, externalusers 
		WHERE projects.id=? AND projects.owner=externalusers.id';
$sth = $db->prepare ($sql);
$sth->execute (array ($_GET['id']));	// Get information for the given project
if (!$row = $sth->fetch())				// No such project found?
	die ("Fant ikke dette prosjektet");	// Give an error message
?><!-- Display the found information -->
<h3><?php echo $row['title']; ?></h3>
<b>Oppdragsgiver : </b><?php echo $row['companyname']; ?><br/>
Kontaktperson : <?php echo $row['givenname'].' '.$row['surename']; ?><br/>
Siste oppdatert : <?php echo $row['date']; ?><br/>
<p>
<b>Kommentarer fra fagmiljøet : </b><br/><?php echo $row['collegiumComments']; ?>
</p>
