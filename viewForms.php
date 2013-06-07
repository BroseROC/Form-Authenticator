<?php
// Trevor Mullins
// CS350
// viewForms.php
// View Forms for a user

session_start();
$title = "Clarkson University:View Form";
include 'header.php';

if(!$_SESSION['loggedin']) {
	// User isn't Logged In, make them
	header("location: login.php");
}

if (isset($_GET['id']))
{
	$id = $_GET['id'];
	if($_SESSION['clearance'] >= 2) {
		$username = $_GET["username"];
	} else {
		$username = $_SESSION["username"];
	}
	
	require_once("functions/dbConnect.php");
	require_once("functions/formFunctions.php");
	$forms = getStatusForUser($username, $id);
	$number=mysql_num_rows($forms);
	
	while($person = mysql_fetch_assoc($forms)) {
		echo "Progress of form {$person["form"]}<br />";
		echo "Date Submitted: ". $person["date_submitted"] . "<br />";
		echo "Date Approved: ". $person["date_finished"] . "<br />";
		echo "Current Status: ". printStatus($person["status"]) . "<br />";
		$form_percent = ($person["currenthop"] / $person["totalHops"] ) * 100;
		echo '
		<div class="meter-wrap">
    	<div class="meter-value" style="background-color: #4DA4F3; width: '.$form_percent.'%;">
        <div class="meter-text">
            '.$form_percent.'
        %</div>
    	</div>
		</div>';
	}
	if($number == 0 ) { echo "Opps! No Such Form Exists!<br />"; }
	require_once("functions/dbClose.php");
	
} else {

	$token = md5(uniqid(rand(), true));
	$_SESSION['token'] = $token;
	
	if($_SESSION['clearance'] >= 2 || $_GET["advisor"] == "yes") {
		$username = $_GET["username"];
		if($username == "") { $username = $_SESSION["username"];}
	} else {
		$username = $_SESSION["username"];
	}
	
	require_once("functions/dbConnect.php");
	require_once("functions/formFunctions.php");
	if($_GET["advisor"] == "yes") {
		$forms = getFormsAssoc($username);
	} else {
		$forms = getFormsForUser($username);
	}
	$number = mysql_num_rows($forms);
	//$number = 2;
	if($number == 0 || !($_SESSION["clearance"] >= 1)) { echo "No Forms for user $username<br />"; } else {
	echo "All forms for <strong>$username</strong><br /><br />";
	while($person = mysql_fetch_assoc($forms)) {
		echo $person["date_submitted"].' | <a href="viewForms.php?id='.$person["ID"].'&username='.$username.'">viewForm: '.$person["form"].'</a> | status: '.printStatus($person["status"]).'<br />';
	}
	}
	
	require_once("functions/dbClose.php");
	
}
include 'footer.php';
?>
