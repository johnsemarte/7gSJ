<?php
/**
 * Script is called to store a new project in the database
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

// SQL statement to insert new user into table externalusers
$sql = 'INSERT INTO projects (owner, title, `date`, altTitle, shortTitle, description, status) VALUES (?, ?, now(), ?, ?, ?, "draft")';
$sth = $db->prepare ($sql);
// Send the statement to the database
$sth->execute (array ($_SESSION['uid'], $_POST['name'], $_POST['altName'], $_POST['shortName'], $_POST['description']));
if ($sth->rowCount()==1) {	// If the project was inserted into the database
	if (isset($_POST['attachedFiles'])) {	// Files is attached to the project
		$sql = 'SELECT LAST_INSERT_ID() as id';	// Find the id of the project
		foreach ($db->query ($sql) as $row) {	// Will only run once, shorthand coding standard
			$projectId = $row['id'];			// Get the project id
			// SQL statement to connect the document to the project
			$sql = 'INSERT INTO projectdocuments (projectid, documentid) VALUES (?, ?)';
			$sth = $db->prepare ($sql);			// Prepare the statement, might run multiple times
			foreach ($_POST['attachedFiles'] as $fileId)		// Get each document id
				$sth->execute (array ($projectId, $fileId));	// Add a row for each document
		}
	}
	die (json_encode (array ('ok'=>'OK')));	// json encode the response and send
} else {					// No project was inserted ????
	die (json_encode (array ('error'=>'Kunne ikke opprette prosjektet, kontakt administrator')));
}
?>