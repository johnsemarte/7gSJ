<?php
/**
 * Script used to check if this is a user registered in the
 * bachelor thesis database, i.e. NOT a HiG user
 */

// Start the session handling system
session_start ();

// Connect to the database
require_once ("../db.php");

// No need to try to log in if no username/password are set
if ((!isset ($_POST['uname']))||(!isset ($_POST['pwd'])))
	die (json_encode (array ('login_error' => 'Missing username/password')));

// SQL query to check for a given user
$sql = 'SELECT * FROM externalusers WHERE email=:uname AND password=:pwd';
$sth = $db->prepare ($sql);
// Set the username in the query
$sth->bindParam (':uname', $_POST['uname']);
$pwd = md5 ($_POST['pwd']);
// Set the password in the query
$sth->bindParam (':pwd', $pwd);
// Perform the query on the database
$sth->execute ();
if ($row = $sth->fetch()) {		// We have a user
	// Send the userid back to the script
	echo json_encode (array ('uid' => $row['id']));
	// Store the user details in the session object
	$_SESSION['uid'] = $row['id'];
	$_SESSION['type'] = 'external';
} else							// No user matched the credential given
	echo json_encode (array ('login_error' => 'Bad username/password'));
?>