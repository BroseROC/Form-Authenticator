<?php
// Trevor Mullins
// CS350
// register.php
// User Registration Page

session_start();
$title = "Clarkson University:Register";
include 'header.php';

if($_SESSION['loggedin']) {
	// User is already logged in, redirect to homepage
	header("location: index.php");
}

// Register User if the form was submited
if (isset($_POST['submit'])) {
	// "import" the file needed to setup a database connection
	require_once("functions/dbConnect.php");
	// "import" functions a user would need
	require_once("functions/userFunctions.php");

	// set all the user supplied data to lowercase
	$username = strtolower($_POST['username']);
	$ID = strtolower($_POST['ID']);
	$firstName = strtolower($_POST['firstName']);
	$lastName = strtolower($_POST['lastName']);

	// register the user
	echo register($ID, $username, $lastName, $firstName) . "<br />";
	
	// close the database connection opened from above
	require_once("functions/dbClose.php");
} else {
	// HTML Form for registering
	echo '
	<form method="POST" enctype="multipart/form-data">
	<dl>
		<dt><label>Enter Your Clarkson ID Number: </label><input type="text" name="ID" /></li>
		<dt><label>Enter Your Clarkson Username: </label><input type="text" name="username" /></li>
		<dt><label>Enter Your First Name: </label><input type="text" name="firstName" /></li>
		<dt><label>Enter Your Last Name: </label><input type="text" name="lastName" /></li>
		<dt><input type="submit" name="submit" value="Register" /></li>
	</dl>
	</form>';
}

include 'footer.php';

?>
