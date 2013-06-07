<?php
// Trevor Mullins
// CS350
// login.php
// Login Page

session_start();
$title = "Clarkson University:Login";
include 'header.php';

if($_SESSION['loggedin']) {
	// User is logged in send them home
	header("location: index.php");
}


// Register User if the form was submited
if (isset($_POST['submit'])) {
// Prevent XSS (Cross-Site Scripting)
if (isset($_SESSION['token']) && $_POST['token'] == $_SESSION['token']) {
	// "import" the file needed to setup a database connection
	require_once("functions/dbConnect.php");
	// "import" functions a user would need
	require_once("functions/userFunctions.php");

	// set all the user supplied data to lowercase
	$username = strtolower($_POST['CUName']);
	$password = $_POST['CUPass'];

	// register the user
	if( login($username, $password) ) {
		// User Exists and Has Logged In
		header("location: index.php");
	} else {
		// Username or Password is Wrong
		// Tell the user that
		$ServMsg = "Username or Password Is Wrong";
	}
	
	// close the database connection opened from above
	require_once("functions/dbClose.php");
}
}
$token = md5(uniqid(rand(), true));
$_SESSION['token'] = $token;

// HTML Form for registering
echo '
<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="token" value="'.$token.'" />
<dl>
	<dt><label>Username: </label><input type="text" name="CUName" /></li>
	<dt><label>Password: </label><input type="password" name="CUPass" /></li>
';
if(isset($ServMsg)) { echo "<strong><span style='color: red'>$ServMsg</span></strong><br />"; }
echo '
	<dt><input type="submit" name="submit" value="Login" /></li>
	<dt><a href="register.php">Register New Account..</a></li>
</dl>
</form>';

include 'footer.php';
?>
