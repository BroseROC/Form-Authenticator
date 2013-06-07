<?php
// Trevor Mullins
// CS350
// userFunctions.php
// Administrator Functions

/*********** User Management Functions ***********/

// facultySearch($lastname)
// Returns all information for a faculty memeber with $lastname
// Allows for wildcard '*' to display all faculty members
function facultySearch($lastname) {
	$lastname = mysql_real_escape_string(strtolower($lastname));
	$dept = $_SESSION["dept"];
	$size = count($dept);

	// get all faculty with lastname	
	if($_SESSION["clearance"] == 47) {
		$query = 'SELECT * FROM users WHERE ';
		if($lastname != "*") { $query .= 'lastname="'.$lastname.'" AND '; }
		$query .= 'clearance>"1" AND clearance<"'.$_SESSION["clearance"].'" AND clearance!=47';
	} else {
		if($size == 1) {
			$query = 'SELECT * FROM users WHERE ';
			if($lastname != "*") { $query .= 'lastname="'.$lastname.'" AND '; }
			$query .= 'clearance>"1" AND clearance<"'.$_SESSION["clearance"].'" AND clearance!=47 AND dept REGEXP \''.$$dept[0].'\'';
		} else {
			$query = 'SELECT * FROM users WHERE ';
			if($lastname != "*") { $query .= 'lastname="'.$lastname.'" AND '; }
			$query .= 'clearance>"1" AND clearance<"'.$_SESSION["clearance"].'" AND clearance!=47 AND (';
			foreach($dept as $search)
			{
				if($size == 1)
				$query .= "dept REGEXP '".strtolower($search)."')";
				else {
				$query .= "dept REGEXP '".strtolower($search)."' OR ";
				}
				$size--;
			} 
		}
	}
	$result = mysql_query($query);
	return $result;
}

// studentSearch($lastname)
// Returns all information for a student with $lastname
// Allows for wildcard '*' to display all students
function studentSearch($lastname) {
	$lastname = mysql_real_escape_string(strtolower($lastname));
	$dept = $_SESSION["dept"];
	$size = count($dept);

	// get all students with lastname	
	if($_SESSION["clearance"] == 47) {
		$query = 'SELECT * FROM users WHERE ';
		if($lastname != "*") { $query .= 'lastname="'.$lastname.'" AND '; }
		$query .= 'clearance="1"';
	} else {
		if($size == 1) {
			$query = 'SELECT * FROM users WHERE ';
			if($lastname != "*") { $query .= 'lastname="'.$lastname.'" AND '; }
			//if($_SESSION['clearance'] == 2) { $query .= 'advisor="'.$_SESSION['self'].'" AND '; }
			$query .= 'clearance="1" AND dept REGEXP \''.$dept[0].'\'';
		} else {
			$query = 'SELECT * FROM users WHERE ';
			if($lastname != "*") { $query .= 'lastname="'.$lastname.'" AND '; }
			//if($_SESSION['clearance'] == 2) { $query .= 'advisor="'.$_SESSION['self'].'" AND '; }
			$query .= 'clearance="1" AND (';
			foreach($dept as $search)
			{
				if($size == 1)
				$query .= "dept REGEXP '".strtolower($search)."')";
				else {
				$query .= "dept REGEXP '".strtolower($search)."' OR ";
				}
				$size--;
			} 
		}
	}
	$result = mysql_query($query);
	return $result;
}

// updateUserPassword($username, $password)
// Allows for hte updating of a user's password
function updateUserPassword($username, $password) {
	// escape all the param's to protect against SQL Injections
	$username = mysql_real_escape_string(strtolower($username));
	// encrypt the new password, let the salt be automatically generated
	$password = crypt(mysql_real_escape_string($password));

	// change password in database
	$query = 'UPDATE users SET password="'. $password .'" WHERE username="'. $username .'"';
	mysql_query($query);
	return true;
}

// changeClearance($username, $level)
// Allows for the clearance (privilages) to be changed
// Accepted Values are between 1 - 46
function changeClearance($username, $level) {
	// escape all the param's to protect against SQL Injections
	$username = mysql_real_escape_string(strtolower($username));
	$level = mysql_real_escape_string($level);

	// change usernames level of clearance to $level
	$query = 'UPDATE users SET clearance="'. $level .'" WHERE username="'. $username .'"';
	$result = mysql_query($query);
	return true;
}

// lockUser($username, $id)
// Lock a user account
function lockUser($username, $id) {
	// escape all the param's to protect against SQL Injections
	$username = mysql_real_escape_string(strtolower($username));
	$id = mysql_real_escape_string(strtolower($id));

	// lock the user account by setting it to not verified
	// set this number to '2', to recognize locked accounts from newly created
	$query = 'UPDATE users SET verified="2" WHERE username="'. $username .'" AND id="'.$id.'"';
	$result = mysql_query($query);
	return true;
}

// unlockUser($username, $id)
// Unlock a user account
function unlockUser($username, $id) {
	// escape all the param's to protect against SQL Injections
	$username = mysql_real_escape_string(strtolower($username));
	$id = mysql_real_escape_string(strtolower($id));

	// unlock the user account by setting it to verified
	$query = 'UPDATE users SET verified="1" WHERE username="'. $username .'" AND id="'.$id.'"';
	$result = mysql_query($query);
	return true;
}
?>
