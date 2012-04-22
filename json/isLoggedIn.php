<?php
/**
 * Script used to check if a user is logged in.
 * If a user is logged in we return the userid and usertype.
 * If no user is logged in a login status with value 'Not logged in' is returned.
 */
 
// Set content type to simplify handling at the receiving end
header ('Content-type: application/json');
// Start the session
session_start ();

if (isset ($_SESSION['uid']))
	echo json_encode (array ('uid'=>$_SESSION['uid'], 'userType'=>$_SESSION['type']));
else
	echo json_encode (array ('login'=>'Not logged in'));
?>