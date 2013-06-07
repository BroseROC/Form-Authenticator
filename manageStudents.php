<?php
// Trevor Mullins
// CS350
// manageStudents.php
// Manage Students

session_start();
$title = "Clarkson University:Administration";
include 'adminHeader.php';

if(!$_SESSION['loggedin'] && $_SESSION['clearance'] != 47) {
	// User isn't Logged In, make them
	header("location: login.php");
}

if (isset($_POST['find']))
{
	if (isset($_SESSION['token']) && $_POST['token'] == $_SESSION['token'])
	{
	$search = $_POST["student"];
	// If user didn't submit anything in the form don't continue
	// send them back to the same page
	if($search == "NULL")
		header("location: {$_SERVER['PHP_SELF']}");
		
	require_once("functions/dbConnect.php");
	require_once("functions/adminFunctions.php");
	$students = studentSearch($search);
	$number=mysql_num_rows($students);
	if($number == 0)
		echo "<p>No Results Returned</p><a href=\"\">Search Again</a>";
	else {
	//print each student with that lastname and an array of options
	echo "<center><table border=\"1\">";
	echo '<tr><td>Student ID</td><td>Name</td><td>Username</td><td>Advisor</td><td colspan="3">Options</td></tr>';
	while($student = mysql_fetch_assoc($students)) {
		echo '<tr><td>'.$student["ID"].'</td>';
		echo '<td>'.$student["lastname"] .', '. $student["firstname"].'</td>';
		echo '<td>'.$student["username"].'</td>';
		echo '<td>'.$student["advisor"].'</td>';
		echo '<td><a href="viewForms?username='.$student["username"].'">View Forms</a></td>';
		if($_SESSION['clearance'] == 47) {
			echo '<td><a href="adminPassword.php?id='.$student["ID"].'&name='.$student["username"].'">Reset Password</a></td>';
			if($student["verified"] == 2)
			echo '<td><a href="unlockUser.php?id='.$student["ID"].'&name='.$student["username"].'">Unlock User Account</a></td></tr>';
			else
			echo '<td><a href="lockUser.php?id='.$student["ID"].'&name='.$student["username"].'">Lock User Account</a></td></tr>';
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
<p>Manage Students</p>
<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="token" value="{$token}" />
<ul>
	<li><label>Search for a student (Lastname or *): </label><input type="text" name="student" /></li>
	<li><input type="submit" name="find" value="Search" /></li>
</ul>
</form>
EOF;

}
include 'adminFooter.php';
?>
