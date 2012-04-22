$(document).data ('uid', null);
$(document).data ('userType', "unknown");
var loginDialog = null;

$(document).ready (function () {
	$('title').html ('Bacheloroppgaver ved IMT');		// Change title of browser
	$.ajax ({							// Check login status
		url: 'json/isLoggedIn.php',		// Script used to check login status
		success: function (data) {
			if (data.login)	{			// A user is not logged in
				$('body > section').load ('pages/welcome.html');
				updateUI ();										// Show appropriate display
			} else {						// A user is logged in
				$(document).data ('userType', data.userType);		// Update user type
				$(document).data ('uid', data.uid);					// Update user id
				$('#login a').html ('Logge av');					// Change logon to logoff
				$('#login').next().hide();							// Hide the create new user link
				updateUI ();										// Show appropriate display
			}
		}
	});
	
	// Display published and reviewed projects
	$('#showPublic').click (function() {
		$('body > section').load ('pages/availableProjects.php');
		return false;
	});
	
	$('#login').click (function() {			// Show login dialog box
		showLoginDialog ();
		// Prevent the link from being followed.
		return false;
	});
	$('#login').next().click (function() {	// The "Opprett bruker" link, create new external user
		showNewExternalUserDialog ();
		return false;
	});
	
	$('.external').next().children('ul > li:nth-child(1)').click (function() {	// External user, list projects
		$('body > section').load ('pages/externalUserProjects.php');
		return false;
	});
	$('.external').next().children('ul > li:nth-child(2)').click (function() {	// External user, new project
		$('body > section').load ('pages/newProject.php');
		return false;
	});
	$('.external').next().children('ul > li:nth-child(3)').click (function() {	// External user, my files
		$('body > section').load ('pages/externalUserFiles.php');
		return false;
	});
	$('.external').next().children('ul > li:nth-child(4)').click (function() {	// External user, edit userinfo
		$('body > section').load ('pages/externalUserEditInfo.php');
		return false;
	});
});	

/**
 * Show the login dialog, the contents of the dialog is from the file
 * dialogs/login.php. The script login(form) will be called to perform
 * the login.
 */
function showLoginDialog () {
	if ($(document).data ('uid')!=null) {	// If a user is logged in
		$('#login a').html ('Logge på');	// Change the log off to log in
		$('#login').next().show();			// Show the create new user link
		$(document).data ('uid', null);		// Clear the uid
		$(document).data ('userType', null);// Clear the user type
		updateUI();							// Update the user interface, set back to initial state
		return false;
	}
	if (loginDialog == null) {		// If the login dialog has not been previously created
		// Create and load the content of the login dialog, then make it a dialog
		loginDialog = $('<div></div>').load('dialogs/login.php', function () {
					// Run this code when the content of the dialog has loaded
					// When the log in button is pressed, run the login method
					$('form.login input[type="button"]').click (login);
					// Open the login dialog
					loginDialog.dialog('open');
		}).dialog({
			autoOpen: false,
			position: [500,200],
			width: 350,
			title: 'Logg på for å opprette/velge/vurdere bacheloroppgaver'
		});
	}
	// Find the dialog and open it (Note, this doesn't work when creating the dialog on the fly so we need both)
	$('.login').parent().dialog('open');
}

/**
 * Show the dialog for creating a new external user.
 * The content of the dialog is from dialogs/newExternal.php
 * The script createNewExternal(form) will be called to handle the form
 * data.
 */
function showNewExternalUserDialog () {
	// Create a new dialog
	var dialog = $('<div></div>').load('dialogs/newExternal.php', function () {
		$('.newExternal textarea.tinymce').tinymce({
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
 
			// Drop lists for link/image/media/template dialogs
			template_external_list_url : "lists/template_list.js",
			external_link_list_url : "lists/link_list.js",
			external_image_list_url : "lists/image_list.js",
			media_external_list_url : "lists/media_list.js",
			//file_browser_callback : 'tinyFileBrowser'
		});
	}).dialog({
		autoOpen: true,
		position: [200,100],
		width: 550,
		title: 'Opprett ny bedriftsbruker for å opprette bacheloroppgaver.',
		close: function (event, ui) {
			$(this).remove();
		}
	});
}

/**
 * Called from the NewExternal dialog, does basic form validation 
 * before posting the data to the script json/createNewExternal.php
 */
function createNewExternal (form) {
	if (form.altKey!=undefined)	// For some reason this method get called twice
		return;

	if (form.password.value.length<6) {		// Password must be at least six characters
		alert ("Passordet må være minst 6 tegn.");
		form.password.focus;
		return;
	} else if (form.password.value!=form.password1.value) {	// The passwords must match
		alert ("De to passordene er ulike.");
		form.password.focus;
		return;
	} else if (form.email.value.length<6) {	// We need an email, this is the login name
		alert ("Du må oppgi en epost adresse, denne vil være ditt brukernavn på systemet.");
		form.email.focus;
		return;
	}
	var data = { // All data from the form
		name : form.name.value,
		givenname : form.givenname.value,
		surename : form.surename.value,
		email : form.email.value,
		password : form.password.value,
		officephone : form.officephone.value,
		cellphone : form.cellphone.value,
		address : form.address1.value,
		address1 : form.address2.value,
		postal : form.postal.value,
		description : $('.newExternal textarea[name="bedriftsbeskrivelse"]').html() };
	$.ajax ({
		url : 'json/createNewExternal.php',
		type : 'POST',
		data : data,
		success : function (data) {
			if (data.ok) {
				$(document).data ('uid', data.uid);
				$(document).data ('userType', 'external');
				$('.newExternal').parent().dialog('close');
				$('.newExternal').parent().remove();	// Remove the dialog from memory
				// Change logon to logoff
				$('#login a').html ('Logge av');
				$('#login').next().hide();
				updateUI ();
			} else if (data.userExists) {
				alert ('Brukernavnet eksisterer allerede. Har du glemt passordet?');
			} else
				alert (data.error);
		}
	});
}

/**
 * Used to allow the user to insert local (from database) links
 * in the tinyMCE editor
 */
function tinyFileBrowser (field_name, url, type, win) {
  cmsURL = 'fileLibrary/fileList.php';
  if (cmsURL.indexOf("?") < 0) {
    //add the type as the only query parameter
    cmsURL = cmsURL + "?type=" + type;
  } else {
    //add the type as an additional query parameter
    // (PHP session ID is now included if there is one at all)
    cmsURL = cmsURL + "&type=" + type;
  }
  
  if (type=='image')
	title = 'Velg et bilde';
  else
    title = 'Velg en fil';
 
  tinyMCE.activeEditor.windowManager.open({		// Open file picker dialog
    file : cmsURL,
    title : title,
    width : 630,  
    height : 411,
    resizable : "no",
    inline : "yes", 
    close_previous : "yes",
		popup_css : false
  }, {
    window : win,
    input : field_name
  });
  return false;
}

/**
 * Update the user interface, show the menu options according to 
 * the type of user logged in.
 */
function updateUI () {
	// Hide user spesific menu items
	$('nav > section.student').hide();
	$('nav > section.student').next().hide()
	$('nav > section.external').hide();
	$('nav > section.external').next().hide();
	$('nav > section.employee').hide();
	$('nav > section.employee').next().hide();
	if ($(document).data ('userType')=='student') { // If a student is logged in, display student menu
		$('nav > section.student').show();
		$('nav > section.student').next().show()
	} else if ($(document).data ('userType')=='external') {	// Show the menu for external users
		$('nav > section.external').show();
		$('nav > section.external').next().show();
		$('body > section').load ('pages/welcomeExternal.html');
	} else if ($(document).data ('userType')=='employee') {		// Show both employee and external menues
		$('nav > section.employee').show();
		$('nav > section.employee').next().show();
	} else
		$('body > section').load ('pages/welcome.html');
}

/**
 * This function is called when the user clicks the "Logg inn" button
 * in the login dialog.
 * This method will check the username/password again HiG users,
 * if no match is found here the method loginExternal will be called
 * to check if a external user is login in.
 * 
 * @param form a reference to the form with the username/password
 */
function login (form) {
	if (form.altKey!=undefined)	// For some reason this method get called twice
		return;
	// Get the username and password from the form
	var uname = form.username.value;
	var pwd = form.password.value;
	$.ajax ({			// Use an Ajax call to check the user credentials
		url : 'json/login.php',
		data : { 'uname': uname, 'pwd': pwd },
		crossDomain : true,
		type : 'POST',
		dataType : 'json',
		success : function (data) {
			if (data.login_error) {				// If no user matches the username/pwd
				// Try login as external (non HiG) user
				loginExternal (uname, pwd);
				return;
			}
			// User credentials confirmed, store uid
			$(document).data ('uid', data.uid);
			// Set userType that has logged in
			if (data.employee=='y')
				$(document).data ('userType', 'employee');
			else
				$(document).data ('userType', 'student');
			// Hide the login dialog box
			$('.login').parent().dialog('close');
			// Change logon to logoff
			$('#login a').html ('Logge av');
			$('#login').next().hide();
			// Update the UI to show new options
			updateUI ();
		}
	});
}

/**
 * This method will check to see if the given user credentials belong to 
 * an external (non HiG) user.
 *
 * If no user with the given credentials is found an error message will 
 * be displayed in the dialog box.
 *
 * @param uname the username
 * @param pwd the password
 */
function loginExternal (uname, pwd) {
	$.ajax ({
		// Use a system spesific script to check credentials
		url : 'json/externalUserLogin.php',
		data : { 'uname' : uname, 'pwd' : pwd },
		type : 'POST',
		dataType : 'json',
		success : function (data) {
			if (data.login_error!=undefined) {				// If no user matches the credentials
				// Show the error message
				$('form.login .error').show ();
				return;
			}
			//User credentials confirmed, store the uid
			$(document).data ('uid', data.uid);
			$(document).data ('userType', 'external');
			// Hide the login dialog box
			$('.login').parent().dialog('close');
			// Change logon to logoff
			$('#login a').html ('Logge av');
			$('#login').next().hide();
			// Update the UI to show new options
			updateUI ();
		}
	});
}