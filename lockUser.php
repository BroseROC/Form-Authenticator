<?php
// Trevor Mullins
// CS350
// lockUser.php
// Lock User Account

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

	// verify user & send feedback
	if( lockUser($username, $ID) ) {
		echo "$username has been locked";
	} else {
		echo "ERROR, $username not locked";
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
	<li><label>Lock the user account {$userName}</label></li>
	<li><input type="submit" name="confirmed" value="Confirm" /></li>
</ul>
</form>
EOF;

}
include 'adminFooter.php';
?>
