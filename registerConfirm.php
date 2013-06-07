<?php
// Trevor Mullins
// CS350
// registerConfirm.php
// User Registration Confirmation

session_start();
$title = "Clarkson University:Registration Confirmation";
include 'header.php';
?>
<script type='text/javascript'>
function addTextField(divId, textname, counterId){
 var parent = document.getElementById(divId);
 var counter = document.getElementById(counterId);
 var tx = document.createElement("input");
 tx = parent.appendChild(tx);
 tx.name = textname + counter.value;
 tx.id = tx.name;
 tx.type = "text";
 counter.value = 1 + parseInt(counter.value);
 parent.appendChild(document.createElement("br"));
}
function addAdvisor(divId, textname, counterId){
 var parent = document.getElementById(divId);
 var counter = document.getElementById(counterId);
 var tx = document.createElement("input");
 var w = document.getElementById("advisorName");
 w = w.selectedIndex;
 var selected_text = document.register.advisorName.options[w].text;
 tx = parent.appendChild(tx);
 tx.name = textname + counter.value;
 tx.id = tx.name;
 tx.type = "text";
 tx.setAttribute("value", selected_text);
 counter.value = 1 + parseInt(counter.value);
 parent.appendChild(document.createElement("br"));
}
</script>
<?php
// "import" the file needed to setup a database connection
require_once("/var/www/functions/dbConnect.php");
// "import" functions a user would need
require_once("/var/www/functions/userFunctions.php");

// Complete Verification if the form was submited
if (isset($_POST['submit'])) {

	// set all the user supplied data to lowercase (except passwords)
	$username = strtolower($_POST['username']);
	$ID = strtolower($_POST['ID']);
	$advisor = strtolower($_POST['advisor']);
	$majorlength = $_POST['majorCount'];
	for($i = 0; $i < $majorlength; $i++){
    	$majors .= $_POST['major' . $i];
    	if($majorlength >= 1 && $i + 1 != $majorlength){ $majors .= ', ';}
    }
    $advisorsCount = $_POST['advisorsCount'];
	for($i = 1; $i < $advisorsCount; $i++){
    	$advisor .= $_POST['advisor' . $i];
    	if($advisorsCount >= 2 && $advisorsCount != $i + 1){ $advisor .= ', ';}
    }
	$password = $_POST['passW0rd'];
	$password1 = $_POST['passW1rd'];
	$code = strtolower($_POST['code']);

	// Check password's are the same (user didn't incorrectly type it)
	if( $password1 == $password) {
		// verify user & send feedback
		if( verifyUser($ID, $username, $password, $code, $advisor, $majors) ) {
			echo "user account verified<br />";
		} else {
			echo "<strong>ERROR, user account not verified</strong><br />";
		}
	} else {
		echo "<strong>Passwords Do Not Match</strong><br />";
	}
	
	// Close the database connection opened above
	
} else {
	// Get Username of confirming user
	if (isset($_GET['username'])) {
		$username = $_GET['username'];
	}
	// Get Verification Code for user
	if (isset($_GET['username'])) {
		$verificationCode = $_GET['verification'];
	}
	// HTML Form for verifying accounts and setting password
	echo '
	<form method="POST" name="register" enctype="multipart/form-data">
	<input type="hidden" name="username" value="'.$username.'" />
	<input type="hidden" name="code" value="'.$verificationCode.'" />
	<ul>
		<li><label>Enter Your Clarkson ID Number:</label><input type="text" name="ID" /></li>
		<li><label>Enter Your Major(s):</label>
	';
	echo "<div id='majors'><input type='text' name='major0' id='major0' /><br /></div><input type='button' value='Add required field' onclick='addTextField(\"majors\",\"major\",\"majorCount\")' /><br />";
	echo '</li>
		<li><label>Enter Your Advisors Name:</label>'.printAdvisors().'</li>
		<li><label>Password:</label><input type="password" name="passW0rd" /></li>
		<li><label>Re-Type Password:</label><input type="password" name="passW1rd" /></li>
		<li><input type="submit" name="submit" value="Verify" /></li>
	</ul>
        <input type="hidden" name="majorCount" id="majorCount" value="1" />
        <input type="hidden" name="advisorsCount" id="advisorsCount" value="1" />
	</form>';
}
require_once("functions/dbClose.php");
include 'footer.php';
?>
