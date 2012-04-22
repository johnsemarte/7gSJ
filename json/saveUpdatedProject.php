<?php
/**
 * Script is used to store changes to a project in the database after it has been edited.
 */
 
 
// Start the session handling system
session_start ();

// Return correct content type
header ('Content-type: application/json');

// Connect to the database
require_once '../db.php';

// Only allow this for external users
if (!isset ($_SESSION['uid'])||($_SESSION['type']!='external'))
	die (json_encode(array ('error'=>'Not logged in as an external user')));

if (isset ($_POST['status'])) {	// Update the status only if status is set
	// SQL statement to update the status of the project
	$sql = 'UPDATE projects set status=? WHERE owner=? and id=?';
	$sth = $db->prepare ($sql);
	// Send the statement to the database
	$sth->execute (array ($_POST['status'], $_SESSION['uid'], $_POST['id']));
}

// SQL statement to update other project information
$sql = 'UPDATE projects set title=?, altTitle=?, shortTitle=?, description=?, date=now() WHERE owner=? and id=?';
$sth = $db->prepare ($sql);
$sth->execute (array ($_POST['name'], $_POST['altName'], $_POST['shortName'], $_POST['description'], $_SESSION['uid'], $_POST['id']));

// Remove all previously attached files
$sql = 'DELETE FROM projectdocuments WHERE projectid=?';
$sth = $db->prepare ($sql);
$sth->execute (array ($_POST['id']));

// Reattach currently selected documents
if (isset($_POST['attachedFiles'])) {	// Files is attached to the project
	$projectId = $_POST['id'];			// Get the project id
	// SQL statement to connect the document to the project
	$sql = 'INSERT INTO projectdocuments (projectid, documentid) VALUES (?, ?)';
	$sth = $db->prepare ($sql);			// Prepare the statement, might run multiple times
	foreach ($_POST['attachedFiles'] as $fileId)		// Get each document id
		$sth->execute (array ($projectId, $fileId));	// Add a row for each document
}
die (json_encode (array ('ok'=>'OK')));	// json encode the response and send
?>