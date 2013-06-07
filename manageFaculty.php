<?php
// Trevor Mullins
// CS350
// manageFaculty.php
// Manage Faculty

session_start();
$title = "Clarkson University:Administration";
include 'adminHeader.php';

if(!$_SESSION['loggedin'] && $_SESSION['clearance'] != 47) {
	// User isn't Logged In, make them
	header("location: login.php");
}
if($_SESSION['clearance'] < 3) {
	// Shouldn't be here
	header("location: index.php");
}
if (isset($_POST['find']))
{
	if (isset($_SESSION['token']) && $_POST['token'] == $_SESSION['token'])
	{
	$search = $_POST["person"];
	// If user didn't submit anything in the form don't continue
	// send them back to the same page
	if($search == "NULL")
		header("location: {$_SERVER['PHP_SELF']}");
		
	require_once("functions/dbConnect.php");
	require_once("functions/adminFunctions.php");
	$faculty = facultySearch($search);
	$number=mysql_num_rows($faculty);
	if($number == 0)
		echo "<p>No Results Returned</p><a href=\"\">Search Again</a>";
	else {
	//print each student with that lastname and an array of options
	echo "<center><table border=\"1\">";
	echo '<tr><td>Faculty ID</td><td>Name</td><td>Username</td><td>Reports To</td><td colspan="3">Options</td></tr>';
	while($person = mysql_fetch_assoc($faculty)) {
		echo '<tr><td>'.$person["ID"].'</td><td>'.$person["lastname"] .', '. $person["firstname"].'</td><td>'.$person["username"].'</td>';
		echo '<td>'.$person["advisor"].'</td>';
		echo '<td><a href="viewForms?username='.$person["username"].'&advisor=yes">View Forms</a></td>';
		if($_SESSION['clearance'] == 47) {
			//echo '<td><a href="changeClearance.php?id='.$person["ID"].'&name='.$person["username"].'">Change Clearance</a></td>';	
			echo '<td><a href="adminPassword.php?id='.$person["ID"].'&name='.$person["username"].'">Reset Password</a></td>';
			if($person["verified"] == 2)
			echo '<td><a href="unlockUser.php?id='.$person["ID"].'&name='.$person["username"].'">Unlock User Account</a></td></tr>';
			else
			echo '<td><a href="lockUser.php?id='.$person["ID"].'&name='.$person["username"].'">Lock User Account</a></td></tr>';
		}
	}
	echo "</table></center>";
	} // end number of results check
	require_once("functions/dbClose.php");
	}// end token code check
} else {

$token = md5(uniqid(rand(), true));
$_SESSION['token'] = $token;

// Search Form
echo <<< EOF
<p>Manage Faculty</p>
<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="token" value="{$token}" />
<ul>
	<li><label>Search for a person (Lastname or *): </label><input type="text" name="person" /></li>
	<li><input type="submit" name="find" value="Search" /></li>
</ul>
</form>
EOF;

}
include 'adminFooter.php';
?>
