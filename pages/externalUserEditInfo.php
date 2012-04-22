<?php
/**
 * Page used to display a list of external users files
 * Also allows for uploading new files/new versions of files.
 */

// Start the session handling system
session_start ();

// Connect to the database
require_once ("../db.php");

// Only allow this for external users
if (!isset ($_SESSION['uid'])||($_SESSION['type']!='external'))
	die ('Not logged in as an external user');

$sql = 'SELECT * from externalusers WHERE id=?';
$sth = $db->prepare ($sql);
$sth->execute (array ($_SESSION['uid']));
if (!$row = $sth->fetch())		// No user with this uid was found
	die ("<h2>Fant ikke informasjon om bruker</h2><p>Dersom du mener dette er feil vennligst kontakt administrator.</p>");
?><h2>Redigere informasjon om ekstern bruker</h2>
<form class="editExternalUserInfo" onsubmit="return false;">
<label for="name">Navn på bedriften</label><input type="text" value="<?php echo $row['companyname'];?>" name="name" title="Navn på bedriften som vil stå som oppdragsgiver for bacheloroppgaven(e)."><br/>
<label>Kontaktperson : </label><br clear="both"/>
<label for="givenname">Fornavn</label><input class="short" type="text" value="<?php echo $row['givenname'];?>" name="givenname" title="Fornavn på kontaktperson i bedriften.">
<label for="surename" class="short">Etternavn</label><input class="short" type="text" value="<?php echo $row['surename'];?>" name="surename" title="Etternavn på kontaktperson i bedriften."><br clear="both"/>
<label for="email">Epost</label><input type="email" value="<?php echo $row['email'];?>" name="email" title="Epost adresse til kontaktperson, vil også være brukernavn ved pålogging."><br/>
<label for="password">Gjeldende passord</label><input class="short" type="password" name="password" title="Passord for pålogging (trengs ikke dersom du ikke bytter passord.)"><br clear="both"/>
<label for="password1">Nytt passord</label><input class="short" type="password" name="password1" title="Nytt passordet."/>
<label for="password2" class="short">Bekreft passord</label><input class="short" type="password" name="password2" title="Bekreft nytt passordet."/><br clear="both"/>
<label for="officephone">Telefon (jobb)</label><input class="short" type="text" value="<?php echo $row['officephone'];?>" name="officephone" title="Telefonnummer til kontaktperson på jobb.">
<label for="cellphone" class="short">Telefon (mobil)</label><input class="short" type="text" value="<?php echo $row['cellphone'];?>" name="cellphone" title="Telefonnummer til kontaktperson, mobil."><br clear="both"/>
<label for="address1">Adresse</label><input type="text" value="<?php echo $row['address1'];?>" name="address1" title="Adresse til bedriften."><br/>
<label for="address2">Adresse</label><input type="text" value="<?php echo $row['address2'];?>" name="address2" title="Adresse til bedriften."><br/>
<label for="postal">Postnr/sted</label><input type="text" value="<?php echo $row['postal'];?>" name="postal" title="Postnummer og sted for bedriften."><br/>
<label for="bedriftsbeskrivelse" style="text-align:left; width: 200px">Beskrivelse av bedriften</label><br clear="both"/>
<textarea class="tinymce" name="bedriftsbeskrivelse"><?php echo $row['description'];?></textarea><br/>
<input type="button" value="Lagre endringer" onclick="javascript:saveNewExternalUserInfo(this.form)"/>
</form>
<script type="text/javascript">
function saveNewExternalUserInfo (form) {	// This script get called when the user clicks the button to save the changes
	if (form.password1.value.length>0&&form.password.value.length==0) {			// Perform basic password validation
		alert ("For å endre passordet så må du oppgi det gamle passordet.");
		form.password.focus();
		return;
	} else if (form.password1.value.length>0&&form.password1.value.length<6) {	// At least six characters
		alert ("Det nye passordet er for kort.");
		form.password1.focus();
		return;
	} else if (form.password1.value!=form.password2.value) {					// The passwords must match
		alert ("Det er skrivefeil i et av de nye passordene (de er ikke like.)");
		form.password1.focus();
		return;
	}
	var data = {								// Set all data to be stored
		name: form.name.value,					// Company name
		givenname: form.givenname.value,
		surename: form.surename.value,
		email: form.email.value,
		newpwd: form.password1.value,			// New password
		oldpwd: form.password.value,			// Existing password
		officephone: form.officephone.value,
		cellphone: form.cellphone.value,
		address: form.address1.value,
		address1: form.address2.value,
		postal: form.postal.value,
		description: $('.editExternalUserInfo textarea.tinymce').html()	// Company description
	};
	$.ajax ({
		url: 'json/updateExternal.php',		// Script used to update changes
		data: data,
		type: 'POST',
		success: function (data) {			// When the script completes
			if (data.error)					// Was there an error	
				alert (data.error);			// Show error message
			else							// Everything went well, load the company presentation
				$('body > section').load ('pages/companyPresentation.php');
		}
	});
}

$('.editExternalUserInfo textarea.tinymce').tinymce({		// Init the tinymce editor when the page loads
	language : 'nb', 
	// Location of TinyMCE script
	script_url : 'tinymce/jscripts/tiny_mce/tiny_mce_gzip.php',

	// General options
	theme : "advanced",
	plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,advlist,spellchecker",
 
	// Theme options
	theme_advanced_buttons1 : "spellchecker,iespell,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,|,forecolor,backcolor",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
 
	spellchecker_languages : "Norwegian=no,+English=en",

	// Example content CSS (should be your site CSS)
	content_css : "higstyles.css",
 
	file_browser_callback : 'tinyFileBrowser'	// Script used to display a dialog to let the user select files from the database
												// See bachelorthesis.js for this script
});