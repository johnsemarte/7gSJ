<?php
/**
 * Script used to let external users upload files.
 */

// Start the session handling system
session_start ();

// Connect to the database
require_once ("../db.php");

// We must be logged in as external user
if (!isset ($_SESSION['uid'])||($_SESSION['type']!='external'))
	die ('Not logged in as an external user');

if (is_uploaded_file ($_FILES['upload']['tmp_name'])) {		// We have uploaded a file
	// Get file content and all meta data about the file
	$contents = file_get_contents ($_FILES['upload']['tmp_name']);
	$mime = $_FILES['upload']['type'];
	$size = $_FILES['upload']['size'];
	$descr = $_POST['descr'];
	$owner = $_SESSION['uid'];
	$name = $_FILES['upload']['name'];
	// Check to see if a file with the same name exists
	$sql = 'SELECT id FROM documents WHERE name=? and owner=?';
	$sth = $db->prepare ($sql);
	$sth->execute (array ($name, $owner));
	if ($row = $sth->fetch())		// We have a duplicate file name, store the id of the existing file
		$data['existing'] = $row['id'];
	$sth->closeCursor();			// Reuse sth
	// Insert the new file into the database
	$sql = 'INSERT INTO documents (name, owner, descr, size, `date`, mime, content) VALUES (?, ?, ?, ?, now(), ?, ?)';
	$sth = $db->prepare ($sql);
	$sth->execute (array ($name, $owner, $descr, $size, $mime, $contents));
	if ($sth->rowCount ()==0)		// No rows was inserted, something must be wrong
		response (json_encode (array ('error'=>'Kunne ikke legge til filen i databasen')));
	else {							// A row was inserted
		// Find the id of the newly inserted file
		$sth = $db->prepare ('SELECT LAST_INSERT_ID() as id');
		$sth->execute ();
		if ($row = $sth->fetch()) {	// We found the id of the newly inserted file
			$data['id'] = $row['id'];
			response (json_encode ($data));	// Wrap the json encoded data in a call to the javascript method fileUploaded
		} else {					// This shouldn't really happen, but if it does, give an error message
			$data['error'] = 'Klarte ikke å hente ID for opplastet fil';
			response (json_encode ($data));	// Wrap the json encoded data in a call to the javascript method fileUploaded
		}
	}
}

// Wrap the json encoded data in a call to the javascript method fileUploaded
response (json_encode (array ('error'=>'Ingen fil lastet opp')));
			
function response ($data) {	// Wrap the content of data in a call to the javascript method fileUploaded	
	// The method fileUploaded exists in the owner window of the iframe that this script gets loaded into
	die ("<script type='text/javascript'>\nwindow.top.window.fileUploaded ('".$data."');\n</script>\n");
}?>