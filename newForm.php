<?php
// Trevor Mullins
// CS350
// newForm.php
// Submit a new form for authorization

session_start();
$title = "Clarkson University:Submit New Form";
include 'header.php';

if(!$_SESSION['loggedin']) {
	// User isn't Logged In, make them
	header("location: login.php");
}

if (isset($_POST['continue']))
{
	if (isset($_SESSION['token']) && $_POST['token'] == $_SESSION['token'])
	{

$token = md5(uniqid(rand(), true));
$_SESSION['token'] = $token;
$form = $_POST["formType"];
// If user didn't submit anything in the form don't continue
// send them back to the same page
if($form == "NULL")
	header("location: {$_SERVER['PHP_SELF']}");

echo <<<EOF
<form method="POST" action="upload.php?formID={$form}" enctype="multipart/form-data">
<input type="hidden" name="token" value="{$token}" />
<input type="hidden" name="formID" value="{$form}" />
<ul>
	<li>Please Download and fill-in the form, when you're ready hit continue.</li>
	<li><a href="downloadForm.php?formID={$form}">Download Form</a></li>
	<li><input type="submit" name="continue" value="Continue" /></li>
</ul>
</form>
EOF;
	}
} else {

$token = md5(uniqid(rand(), true));
$_SESSION['token'] = $token;

// Add form Content
echo <<< EOF
<p>Submit a new form for authorization</p>
<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="token" value="{$token}" />
<ul>
	<li><label>Select a form: </label>
		<select name="formType">
		<option selected value="NULL"></option>
EOF;

require_once("functions/dbConnect.php");
require_once("functions/formFunctions.php");
$forms = listForms();
foreach( $forms as $key => $form ) {
	echo '<option value="'.$key.'">'.$form.'</option>';
}
require_once("functions/dbClose.php");

echo <<< EOF
	</select>
	</li>
	<li><input type="submit" name="continue" value="Continue" /></li>
</ul>
</form>
EOF;
}

include 'footer.php';
?>
