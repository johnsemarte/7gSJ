<?php
/**
 * Page used to show a presentation of a given company (or company for logged in user.)
 */
 
// Start the session handling system
session_start ();

// Connect to the database
require_once ("../db.php");

if (!isset($_GET['id'])) {					// User did not request a particular company
	if (!isset($_SESSION['uid']))			// No external user logged in
		die ("Ingen bedrift å presentere");	// No idea what to present
	else
		$id = $_SESSION['uid'];				// Present the currently logged in company
} else
	$id = $_GET['id'];						// Present the requested company information

if (isset($_GET['back']))					// If a value for back is present, create a link to go back
	$back = "<a style='float: right' href='javascript: back(\"{$_GET['back']}\"); return false;>Tilbake</a>";
else										// If not leave it blank
	$back = '';
	
// SQL to get the information about a given company
$sql = 'SELECT * FROM externalusers WHERE id=?';
$sth = $db->prepare ($sql);
$sth->execute (array ($id));
if (!$row = $sth->fetch())		// No company to present????????
	die ("Fant ikke bedriften du ønsker å få presentert");

echo $back;
?>
<div class="companyPresentation">
<h1><?php echo $row['companyname']; ?></h1>
<i>Kontaktperson</i><br/>
<label>Navn:</label><?php echo $row['givenname'].' '.$row['surename'];?><br clear="both"/>
<label>Epost:</label><?php echo $row['email']; ?><br clear="both"/>
<label>Telefon (jobb):</label><?php echo $row['officephone']; ?><br clear="both"/>
<label>Telefon (mobil):</label><?php echo $row['cellphone']; ?><br clear="both"/>
<label>Adresse:</label><?php echo $row['address1']; ?><br clear="both"/>
<?php
if ($row['address2']!='') { ?>
<label>&nbsp;</label><?php echo $row['address2']; ?><br clear="both"/>
<?php } ?>
<label>&nbsp;</label><?php echo $row['postal']; ?><br clear="both"/>
Kort beskrivelse av bedriften:<br/>
<div class="description">
<?php echo $row['description']; ?>
</div>
</div>
<script type="text/javascript">
function back (url) {		// If a back link is displayed, clicking it will trigger this script
	$('body > section').load (url);
}
</script>
