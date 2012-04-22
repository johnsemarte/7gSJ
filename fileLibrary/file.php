<?php
/**
 * Script used to return a file from the database
 */
 
// Connect to the database
require_once '../db.php';

if (!isset ($_GET['id']))		// No id given ?
	die ("Fant ikke filen");	// Give an error message
	
// Get the mime type, content, size and file name from the database
$sql = 'SELECT mime, content, size, name FROM documents WHERE id=?';
$sth = $db->prepare ($sql);
$sth->execute (array($_GET['id']));	// Get information about the given file
if ($row = $sth->fetch()) {			// If we found the file
	header ('Content-type: '.$row['mime']);		// Set the correct mime type
	header ('Content-length: '.$row['size']);	// Set the content length
	// If we follow a link to get this file, get the save as dialog
	header ("Content-Disposition: attachment; filename=\"{$row['name']}\"");
	echo $row['content'];						// Send the content of the file
} else								// No file found
	echo ("Fant ikke filen");		// Send an "error message"
?>