<?php
// Trevor Mullins
// CS350
// archivedForms.php
// View Past Forms

session_start();
$title = "Clarkson University:Archived Forms";
include 'header.php';

if(!$_SESSION['loggedin']) {
	// User isn't Logged In, make them
	header("location: login.php");
}

require_once("functions/dbConnect.php");
require_once("functions/formFunctions.php");
$forms = getArchivedForms();
$number = count($forms);
if($number == 0 ) {
	echo "No Forms Are Archived!<br />";
} else {
	foreach($forms as $key => $value) {
		print "Form Name: $key | <a href=\"$value\">Download Form</a><br />";
	}
}
require_once("functions/dbClose.php");
include 'footer.php';
?>
