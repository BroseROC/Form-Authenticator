<?php
// Trevor Mullins
// CS350
// manageForms.php
// Manage Active Forms

session_start();
$title = "Clarkson University:Authorize A Form";
include 'header.php';

require_once("functions/dbConnect.php");
require_once("functions/formFunctions.php");
require_once("functions/userFunctions.php");
require_once("functions/pdfdbFunctions.php");

if(!$_SESSION['loggedin']) {
	// User isn't Logged In, make them
	header("location: login.php");
} else if($_SESSION['clearance'] < 2 || !isset($_GET["id"])) {
	header("location: index.php");
}

if($_GET["action"] == "approve") {
	if(approveFormForUser($_SESSION["username"], $_GET["id"])) {
		$ServMsg = "Form Approved";
		if(!formCompleted($_GET["id"])) {
			$nextUser = getNextSignature($_GET["id"]);
			$username = getUsernameForForm($_GET["id"]);
			//print($nextUser);
			$message = "$username would like you to digitally authorize a form.";
			$message .= 'http://www.clarkson.edu/signForm.php?id='.$_GET["id"].'';
			notifyUser($nextUser, $message);
		} else {
			$username = getUsernameForForm($_GET["id"]);
			$message = "The form you have submitted has been approved!";
			notifyUser($username, $message);
			setDateCompleted($_GET["id"]);
		}
	} else
		$ServMsg = "ERROR: Form Not Approved";
} else if($_GET["action"] == "reject") {
	if(rejectFormForUser($_SESSION["username"], $_GET["id"]))  {
		$ServMsg = "Form Rejected";
		$message = "The form you have submitted to be authorized has been rejected.";
		notifyUser(getUsernameForForm($_GET["id"]), $message);
	} else
		$ServMsg = "ERROR: Form Not Rejected";
}

if(isset($ServMsg)) { echo "<strong><span style='color: red'>$ServMsg</span></strong><br />"; }
else {
$key = $_GET["id"];
printDataforForm($key);
echo "<a href='downloadForm.php?formID={$_GET['id']}&active=1'>View</a> | ";
echo '<a href="signForm.php?action=approve&id='.$key.'">Approve</a> | <a href="signForm.php?action=reject&id='.$key.'">Reject</a>';
}
require_once("functions/dbClose.php");

echo "<br />";
include 'footer.php';
?>
