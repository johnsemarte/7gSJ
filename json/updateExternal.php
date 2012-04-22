<?php
/**
 * Script is called to store changes in the database after company information has been edited.
 */
 
 
// Start the session handling system
session_start ();

// Return correct content type
header ('Content-type: application/json');

// Connect to the database
require_once '../db.php';

// Only allow this for external users
if (!isset ($_SESSION['uid'])||($_SESSION['type']!='external'))
	die ('Not logged in as an external user');

if ($_POST['oldpwd']!='') {
	$sql = 'UPDATE externalusers SET password=? WHERE id=? AND password=?';
	$sth = $db->prepare ($sql);
	$sth->execute (array ($_POST['newpwd'], $_SESSION['uid'], $_POST['oldpwd']));
	if ($sth->rowCount()==0)
		die (json_encode (array ('error'=>'Feil på det gamle passordet')));
}
// SQL statement to update other user information
$sql = 'UPDATE externalusers set companyname=?, givenname=?, surename=?, email=?, officephone=?, cellphone=?, address1=?, address2=?, postal=?, description=? WHERE id=?';
$sth = $db->prepare ($sql);
// Update all other user information
$sth->execute (array ($_POST['name'], $_POST['givenname'], $_POST['surename'], $_POST['email'], $_POST['officephone'], 
                      $_POST['cellphone'], $_POST['address'], $_POST['address1'], $_POST['postal'],
					  $_POST['description'], $_SESSION['uid']));
echo json_encode (array ('ok'=>'OK'));
?>