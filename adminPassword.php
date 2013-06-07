<?php
// Trevor Mullins
// CS350
// adminPassword.php
// Reset User Account Password

session_start();
$title = "Clarkson University:Administration";
include 'adminHeader.php';

if(!$_SESSION['loggedin'] && $_SESSION['clearance'] != 47) {
	// User isn't Logged In, make them
	header("location: login.php");
}

if (isset($_POST['confirmed']))
{
	if (isset($_SESSION['token']) && $_POST['token'] == $_SESSION['token'])
	{
	$person = $_POST["student"];
	// If user didn't submit anything in the form don't continue
	// send them back to the same page
	if($person == "NULL")
		header("location: {$_SERVER['PHP_SELF']}");
		
	require_once("functions/dbConnect.php");
	require_once("functions/adminFunctions.php");
	$username = strtolower($_POST['userName']);
	$ID = strtolower($_POST['userID']);
	$password = $_POST['p1'];
	$password1 = $_POST['p2'];
	// Check password's are the same (user didn't incorrectly type it)
	if( $password1 == $password) {
		// verify user & send feedback
		if( updateUserPassword($username, $password) ) {
			echo "Password Changed for $username";
		} else {
			echo "ERROR, password not changed for $username";
		}
	} else {
		echo "Passwords Do Not Match";
	}
	
	require_once("functions/dbClose.php");
	}// end token code check
} else {
if(isset($_GET["id"])) {
	$user = $_GET["id"];
	$userName = $_GET["name"];
} else
	header("location: index.php");

$token = md5(uniqid(rand(), true));
$_SESSION['token'] = $token;


// Search Form
echo <<< EOF
<p>Manage User</p>
<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="token" value="{$token}" />
<input type="hidden" name="userID" value="{$user}" />
<input type="hidden" name="userName" value="{$userName}" />
<ul>
	<li><label>Change Password for user {$userName}</label></li>
	<li><label>Type Password: </label><input type="password" name="p1" /></li>
	<li><label>Re-Type Password: </label><input type="password" name="p2" /></li>
	<li><input type="submit" name="confirmed" value="Confirm" /></li>
</ul>
</form>
EOF;

}
include 'adminFooter.php';
?>
