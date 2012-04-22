<?php
/**
 * Script is called when a new external user is created
 */

// Start the session handling system
session_start ();

// Return correct content type
header ('Content-type: application/json');

// Connect to the database
require_once '../db.php';

// SQL statement to insert new user into table externalusers
$sql = 'INSERT INTO externalusers (companyname, givenname, surename, email, password, officephone, cellphone, address1, address2, postal, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
$sth = $db->prepare ($sql);
// Send the statement to the database
$sth->execute (array ($_POST['name'], $_POST['givenname'], $_POST['surename'], $_POST['email'], md5($_POST['password']), 
                      $_POST['officephone'], $_POST['cellphone'], $_POST['address'], $_POST['address1'], $_POST['postal'],
					  $_POST['description']));
if ($sth->rowCount()==1) {	// If a user was inserted into the database
	$sql = 'SELECT LAST_INSERT_ID() as id';		// Get the id of the newly created user
	foreach ($db->query ($sql) as $row)			// Will only run once, just a shorthand coding 
		$id = $row['id'];						// Get the id 
	$_SESSION['uid'] = $row['id'];				// Set session variables, user is also logged on
	$_SESSION['userType'] = 'external';			// Set session variables, user is also logged on
	die (json_encode (array ('ok'=>'OK', 'id'=>$id)));	// json encode the response and send
} else {					// No user was created, try to figure out why
	$sql = 'SELECT * FROM externalusers WHERE email=?';		// Check to see if the email is already registered
	$sth = $db->prepare ($sql);
	$sth->execute (array ($_POST['email']));
	if ($row = $sth->fetch())									// Yes, the email was already in the system
		die (json_encode (array ('error'=>'Epost adressen er allerede registrert i systemet', 'userExists'=>'true')));
	else													// Some other weird reason
		die (json_encode (array ('error'=>'Kunne ikke opprette brukeren, kontakt administrator')));
}
?>