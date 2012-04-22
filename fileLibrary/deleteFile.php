<?php
/**
 * Script used remove a file from the database.
 */

// Start the session handling system
session_start ();

// Connect to the database
require_once ("../db.php");

// We must be logged in as external user
if (!isset ($_SESSION['uid'])||($_SESSION['type']!='external'))
	die ('Not logged in as an external user');

// Remove this document from the database
$sql = 'DELETE FROM documents WHERE id=? AND owner=?';
$sth = $db->prepare ($sql);
// Make sure that we do not remove anyone elses documents
$sth->execute (array ($_POST['id'], $_SESSION['uid']));

$id = $_POST['id'];

do {	// We should also remove any old versions of this document
	// Find the id of the old document (if any)
	// The old document will have a reference to the newer documents id
	$sql = 'SELECT id FROM documents WHERE newVersion=?';
	$sth = $db->prepare ($sql);
	$sth->execute (array ($id));
	if ($row = $sth->fetch())		// If old document is found, save its id
		$id = $row['id'];
	else
		$id = 0;					// If not, set id to 0
	if ($id > 0) {					// id > 0 means old version is found
		$sth->closeCursor();		// Reuse sth
		// Delete the old document
		$sql = 'DELETE FROM documents WHERE id=? and owner=?';
		$sth = $db->prepare ($sql);
		$sth->execute (array ($id, $_SESSION['uid']));
	}
} while ($id > 0);		// Run as long as old document is found
?>