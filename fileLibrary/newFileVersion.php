<?php
/**
 * Script used create a new version of an existing file.
 * The existing file will get a link to the new file in the newVersion field
 */

// Start the session handling system
session_start ();

// Connect to the database
require_once ("../db.php");

// No need to try to log in if no username/password are set
if (!isset ($_SESSION['uid'])||($_SESSION['type']!='external'))
	die ('Not logged in as an external user');

$sql = 'UPDATE documents SET newVersion=? WHERE id=? AND owner=?';
$sth = $db->prepare ($sql);
	
$sth->execute (array ($_POST['newId'], $_POST['existing'], $_SESSION['uid']));
?>