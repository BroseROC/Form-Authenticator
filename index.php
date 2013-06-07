<?php
// Trevor Mullins
// CS350
// index.php
// Main Homepage

session_start();
$title = "Clarkson University:Home";

if(!$_SESSION['loggedin']) {
	// User isn't Logged In, make them
	header("location: login.php");
}

include 'header.php';
//print_r($_SESSION);
// "import" the file needed to setup a database connection
require_once("functions/dbConnect.php");
// "import" functions a user would need
require_once("functions/userFunctions.php");
echo '
<div id="main"><center>
<p>'.printStats($_SESSION["username"]).'</p><br />
<a href="newForm.php">New Form</a><br />';
if($_SESSION["clearance"] >= 2) {
	echo '<a href="viewForms.php?username='.$_SESSION["username"].'&advisor=yes">Form Status</a>';
} else {
	echo '<a href="viewForms.php">Form Status</a>';
}
echo '<br /></center></div>';
require_once("functions/dbClose.php");
include 'footer.php';
?>
