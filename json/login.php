<?php
/**
 * Script used to log in to the system as emplyee/student.
 * Uses the script http://tvil.hig.no/json_services/checkUserLogin.php
 * to check if this is a user on registered at HiG.
 * If a registered user then we set the session variables "uid" and "type".
 * Send the response from the script unaltered back to the caller of this script
 */
 
// Set content type to simplify handling at the receiving end
header ('Content-type: application/json');
// Start the session
session_start ();

// If no username/password is set this can never work
if ((!isset ($_POST['uname']))||(!isset ($_POST['pwd'])))
	die (json_encode (array ('login_error' => 'Missing username/password')));

// Send a POST request with the username and password.
// See http://wezfurlong.org/blog/2006/nov/http-post-from-php-without-curl/
// for details about the do_post_request
$result = trim (do_post_request ('http://tvil.hig.no/json_services/checkUserLogin.php',  http_build_query($_POST)));

// If the response contains the uid:user id element then we have a successfull login
if (strpos ($result, 'uid":"')>0) {
	// Get the user id from the json string
	$uid = substr ($result, 11, strpos ($result, '"', 12)-11);
	// Emplyee or student
	$emplyee = strpos ($result, '"employee":"y"')>0;
	// Set the session variables
	$_SESSION['uid'] = $uid;
	$_SESSION['type'] = $emplyee?'employee':'student';
}

echo $result;

/**
 * From : http://wezfurlong.org/blog/2006/nov/http-post-from-php-without-curl/
 */
function do_post_request($url, $data, $optional_headers = null) { 
  $params = array('http' => array('method' => 'POST', 
                                  'content' => $data 
                                 )
				 ); 
  if ($optional_headers!== null) { 
    $params['http']['header'] = $optional_headers; 
  } 
  $ctx = stream_context_create($params); 
  $fp = @fopen($url, 'rb', false, $ctx); 
  if (!$fp) { 
    throw new Exception("Problem with $url, $php_errormsg"); 
  } 
  $response = @stream_get_contents($fp); 
  if ($response === false) { 
    throw new Exception("Problem reading data from $url, $php_errormsg"); 
  } 
  return $response; 
}
?>