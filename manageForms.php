<?php
// Trevor Mullins
// CS350
// manageForms.php
// Manage Active Forms

session_start();
$title = "Clarkson University:Form Management";
include 'header.php';
require_once("functions/dbConnect.php");
require_once("functions/formFunctions.php");

if(!$_SESSION['loggedin']) {
	// User isn't Logged In, make them
	header("location: login.php");
} else if($_SESSION['clearance'] != 47) {
	header("location: index.php");
}

if($_GET["action"] == "archive") {
	if(archiveForm($_GET["id"]))
		$ServMsg = "Form Archived";
	else
		$ServMsg = "ERROR: Form Not Archived";
} else if($_GET["action"] == "del") {
	if(removeForm($_GET["id"]))
		$ServMsg = "Form Removed";
	else
		$ServMsg = "ERROR: Form Not Removed";
}

echo "<p>All Forms</p>";
if(isset($ServMsg)) { echo "<strong><span style='color: red'>$ServMsg</span></strong><br />"; }

$forms = listForms();
$number = count($forms);
if($number == 0 ) {
	echo "No Forms Are Archived!<br />";
} else {
	foreach( $forms as $key => $form ) {
		echo 'ID: '.$key.' | Name: '.$form.' | <a href="manageForms.php?action=archive&id='.$key.'">Archive</a> | <a href="manageForms.php?action=del&id='.$key.'">Delete</a> | <a href="downloadForm.php?formID=' . $key . '">View</a>';
	}
}
require_once("functions/dbClose.php");


echo "<br />";
include 'footer.php';
?>
