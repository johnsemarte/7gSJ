<!DOCTYPE html>
<!-- Content for the select file from database dialog used by tinymce
  -- List the files for the current user, if used from the insert image
  -- command, only images is displayed. If used from the insert link
  -- command all files are listed. 
  -->
<html> 
<head>
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="../tinymce/jscripts/tiny_mce/tiny_mce_popup.js"></script>
<script type="text/javascript">
 
function fileClicked (id) {	// This method is called whenever the user click on a file
    FileBrowserDialogue.submit (id);	// Call the submit method, see below
}
 
var FileBrowserDialogue = {	// Configuration for the browser dialog
  init : function () { 		// Call this when the dialog opens
    $.ajax ({
		url: '../json/fileList.php',	// Get a list of all the users files
		data: { type: '<?php echo $_GET['type']; ?>' },	// image or other
		type: 'POST',
		success: function (data) {		// When the list of files is returned
			for (i in data)				// This is an array, loop through it
				// Append an element for each file in the list. 
				// Append it as a link that will trigger the fileClicked function
				$('.filelist').append ('<a href="javascript:fileClicked('+data[i].id+');"><span class="name">'+data[i].name+'</span><span class="date">'+data[i].date+'</span><span class="size">'+data[i].size+'</span><span class="descr"><nobr>'+data[i].descr+'</nobr></span></a><br/>');
		}
	});
  },
  submit : function (id) {		// When the user has selected a file
    var URL = 'fileLibrary/file.php?id='+id;		// This is the url to the selected file
    var win = tinyMCEPopup.getWindowArg("window");	// Get a reference to the tinymce dialog
 
    // insert information now
    win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = URL;
 
    // are we an image browser
    if (typeof(win.ImageDialog) != "undefined") {
      // we are, so update image dimensions...
      if (win.ImageDialog.getImageData)
        win.ImageDialog.getImageData();
 
      // ... and preview if necessary
      if (win.ImageDialog.showPreviewImage)
        win.ImageDialog.showPreviewImage(URL);
    }
 
    // close popup window
    tinyMCEPopup.close();
  }
}
 
// when tinymce initializes the dialog
tinyMCEPopup.onInit.add(FileBrowserDialogue.init, FileBrowserDialogue);
</script>
<title>Dine filer</title>
<style type="text/css">
/* CSS to format the file list display */
#files {
	width: 620px;
	height: 405px;
	border: 1px solid gray;
	overflow: hidden;
	padding-top: 0px;
}
 
#files .heading {
	background: #DDD;
	font: Arial;
	font-size: 0.9em;
	font-weight: bold;
	line-height: 14px;
}
 
#files .name {
	display: inline-block;
	width: 200px;
	border-right: 1px solid gray;
	padding: 2px;
	padding-left: 5px;
	overflow: hidden;
}
 
#files .date {
	display: inline-block;
	width: 60px;
	border-right: 1px solid gray;
	padding: 2px;
	padding-left: 5px;
	overflow: hidden
}
 
#files .descr {
	display: inline-block;
	width: 265px;
	padding: 2px;
	padding-left: 5px;
	overflow: hidden
}
 
#files .size {
	display: inline-block;
	width: 61px;
	padding: 2px;
	padding-left: 5px;
	padding-right: 5px;
	border-right: 1px solid gray;
	overflow: hidden;
	text-align: right;
}
 
#files a .size {
	width: 60px;
}

#files .filelist {
	margin: 0px;
	padding: 0px;
	overflow: auto;
}
 
#files a {
	text-decoration: none;
	color: black;
	font: arial;
	font-size: 0.9em;
	margin: 0px;
	padding: 0px;
}
 
#files a span {
	padding: 0px;
	margin: 0px;
}
 
#files a:hover {
	background: #EEE;
}
 
#files input[type=checkbox] {
    width: 16px;
	margin: 0px;
	padding: 0px;
}

#files a .name {
	width: 200px;
}
</style>
</head>
<body style="margin:0px; padding: 0px;">
<div id="files">
<div class="heading"><span class="name">Navn</span><span class="date">Dato</span><span class="size">Størrelse</span><span class="descr">Beskrivelse</span></div>
<div class="filelist"/><!-- This will be filled with content by an ajax call -->
</div>
</div>
</div>
</body>
</html>