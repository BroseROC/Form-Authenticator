<?php
// Trevor Mullins
// CS350
// parseForm.php
// Parse a user submited form

session_start();
$title = "Clarkson University:New Form";
include 'header.php';

if(!$_SESSION['loggedin']) {
	// User isn't Logged In, make them
	header("location: login.php");
}

if (isset($_POST['upload']))
{
	if (isset($_SESSION['token']) && $_POST['token'] == $_SESSION['token'])
	{

$token = md5(uniqid(rand(), true));
$_SESSION['token'] = $token;
echo <<< EOF
<form method="POST" action="index.php" enctype="multipart/form-data">
<input type="hidden" name="token" value="{$token}" />
<ul>
	<li>Please Download and fillin the form, when you're ready hit continue.</li>
	<li><a href="">Download Form</a></li>
	<li><input type="submit" name="continue" value="Continue" /></li>
</ul>
</form>
EOF;
	}
} else {

$token = md5(uniqid(rand(), true));
$_SESSION['token'] = $token;
$form = $_POST["formID"];

// Add form Content
echo <<< EOF
<p>Submit a new form for authorization</p>
<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="formID" value="{$form}" />
<ind
<ul>
	<li><label>Submit form for approval: </label> <input type="file" name="formFile"></li>
	<li><input type="submit" name="upload" value="Upload Form" /></li>
</ul>
</form>
EOF;
}

include 'footer.php';
?>
