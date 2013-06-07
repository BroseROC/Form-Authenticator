<?php
// Trevor Mullins
// CS350
// userFunctions.php
// Generic User Functions

/*********** Generic Functions ***********/

// notifyUser($username, $message)
// Allows for the notification of ANY_USER@clarkson.edu with a message
// Messages will be sent from noreply@docauth.cslabs.clarkson.edu
// Subject will be Clarkson University Document Authenticator
function notifyUser($username, $message) {
	// Send to $username @ clarkson.edu address
	mail($username.'@clarkson.edu','Clarkson University Document Authenticator', $message, 'From: noreply@docauth.clarkson.edu' . "\r\n" . 'X-Mailer: PHP' . phpversion(), '-fnoreply@docauth.clarkson.edu');
}

// printStats($username)
// Prints the status of all forms for $username
// Shows # submitted, # approved, # rejected and # of forms pending
function printStats($username) {
	$username = mysql_real_escape_string(strtolower($username));
	$query = 'SELECT status FROM active_forms WHERE username="'.$username.'"';
	$result = mysql_query($query);
	
	$submitted = 0;
	$accepted = 0;
	$denied = 0;
	$pending = 0;
	
	while ($row = mysql_fetch_assoc($result)) {
		if($row["status"] == 0) {
			$pending++;
		} else if($row["status"] == 1) {
			$denied++;
		} else if($row["status"] == 2) {
			$accepted++;
		}
		$submitted++;
	}
	echo "Number of Submitted forms: <strong>$submitted</strong><br />";
	echo "Number of Accepted forms: <strong>$accepted</strong><br />";
	echo "Number of Denied forms: <strong>$denied</strong><br />";
	echo "Number of Pending forms: <strong>$pending</strong>";
}

// printAdvisors()
// Gets and prints all the advisors names to an option box for a form
function printAdvisors() {
	$query = 'SELECT username, firstname, lastname FROM users WHERE clearance="2"';
	$result = mysql_query($query);
	// <input type='text' name='advisor0' value='' id='advisor0' /><br />
	$msg = "<div id='advisors'></div>";
	$msg .=  '
    <select size="1" id="advisorName" name="advisorName">
	<option value=""> - Select - </option>';
	while ($row = mysql_fetch_assoc($result)) {
    $msg .= '<option value="'.$row["username"].'">'.$row["firstname"] .' '. $row["lastname"].'</option>';
	}
	$msg .= "</select><br /><input type='button' value='Add Advisor' onclick='addAdvisor(\"advisors\",\"advisor\",\"advisorsCount\")' />";

	mysql_free_result($result);
	return $msg;
}

/*********** Update Functions ***********/
// changePassword($username, $cPass, $nPass)
// Change the password of a user
function changePassword($ID, $username, $cPass, $nPass) {
	// escape all the param's to protect against SQL Injections
	$ID = mysql_real_escape_string(strtolower($ID));
	$username = mysql_real_escape_string(strtolower($username));
	$cPass = mysql_real_escape_string($cPass);
	// encrypt the new password, let the salt be automatically generated
	$nPass = crypt(mysql_real_escape_string($nPass));

	// find the user with ID and password matching the given param.
	$query = 'SELECT password FROM users WHERE username="'. $username .'" AND ID="'. $ID .'"';
	$r = mysql_query($query);
	$password = mysql_result($r, 0);
	if (crypt($cPass, $password) == $password) {
   		// Password has been verified, allow user to change it
		if (mysql_num_rows($r) == 1) {
			// if user is found, update information
			// change password in database
			$query = 'UPDATE users SET password="'. $nPass .'" WHERE username="'. $username .'" AND ID="'. $ID .'"';
			mysql_query($query);
			return true;
		} else {
			return false;
		}
	}
	return false;
}

/*********** Session Functions ***********/
// login($username, $password)
// Login Registered User
function login($username, $password) {
	// escape all the param's to protect against SQL Injections
	$username = $username = mysql_real_escape_string(strtolower($username));
	$password = mysql_real_escape_string($password);

	//Connect to the Database and retrieve Password from a verified username
	$query = 'SELECT password FROM users WHERE username="'. $username .'" AND verified="1"';
	$r = mysql_query($query);
	// if the user has not verified there account return false
	if (mysql_num_rows($r) == 1) { 
		// get the saved password from the database
		$savedPass = mysql_result($r, 0);
		// compare the saved database password with the user supplied one
		if (crypt($password, $savedPass) == $savedPass) {
			$query = 'SELECT clearance, dept, advisor, firstname, lastname FROM users WHERE username="'. $username .'" AND verified="1"';
			$r = mysql_query($query);
			$row = mysql_fetch_row($r);
			// User exists and password matches
			// setup the session / "login" process
			// return true - the user is who they say they are
			session_regenerate_id();
			$_SESSION['clearance'] = $row[0];
			$_SESSION['loggedin'] = true;
			$_SESSION['verified'] = true;
			$_SESSION['dept'] = explode(",", $row[1]);
			$_SESSION['advisor'] = explode(",", $row[2]);
			$_SESSION['self'] = $row[3]." ".$row[4];
			$_SESSION['username'] = $username;
			return true;
		} else {
			return false;
		}
	}
	return false;
}

// logout()
// Logout Registered/Logged In User
function logout() {
	// Unset all of the session variables.
	$_SESSION = array();

	// If it's desired to kill the session, also delete the session cookie.
	// Note: This will destroy the session, and not just the session data!
	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
		$params["path"], $params["domain"],
		$params["secure"], $params["httponly"]);
	}

	// Finally, destroy the session.
	session_destroy();
}

// getClearance($username)
// Get security level of user
function getClearance($username) {
	$level = "1";
	//[ FILL IN ]
	return $level;
}

/*********** Registration Functions ***********/
// isRegistered($username)
// Check if username is already registered
function isRegistered($username) {
	// escape all the param's to protect against SQL Injections
	$username = mysql_real_escape_string(strtolower($username));
	// Search for username in database
	$r = mysql_query('SELECT * FROM users WHERE username="'. $username .'"');
	// if the number of usernames returned is not zero then the username already exists  
	if (mysql_num_rows($r) != 0) { 
		return true;
	} else {
		return false;
	}	
}

// register($username)
// Register a new user
// ID Number, School userID, lastname, firstname
function register($id, $username, $lastname, $firstname) {
	// escape all the param's to protect against SQL Injections
	$firstname = mysql_real_escape_string(strtolower($firstname));
	$lastname = mysql_real_escape_string(strtolower($lastname));
	$username = mysql_real_escape_string(strtolower($username));

	// Check what they entered was a "valid" e-mail
	if (!preg_match('/^[a-z0-9]+$/i', $username)) {
		$message = 'Invalid username';
		return $message;
	}
	if($lastname=="" || $firstname=="") { $message = 'Missing Lastname or Firstname'; return $message;}
	
	// Check is user is already registered
	if(isRegistered($username)) {
		$message = 'That user account already exists';
	} else {
		// Generate a verification code to check against
		$verification_code = md5(rand());
		
		// Database query to insert user
		$query = "INSERT INTO users (ID, firstname, lastname, username, clearance, password, verified)";
		$query .= " VALUES ('$id', '$firstname', '$lastname', '$username', '1', '$verification_code', '0')";
		// insert user into database
		mysql_query($query);
		
		// Send user e-mail at clarkson e-mail address
		$emailMSG = "You have registered to participate in the Document Authorization Program.\r\n";
		$emailMSG .= "Please visit http://docauth.cslabs.clarkson.edu/registerConfirm.php?username=$username&verification=$verification_code to verify your account.\r\n";
		$emailMSG .= "Please do not respond to this e-mail. If you recieved this e-mail in error please notify mullintr@clarkson.edu\r\n";
		notifyUser($username, $emailMSG);
		$message = "Please check your email to verify your Clarkson username.";
	}
	return $message;
}

// verifyUser($ID, $username, $password, $verification_code)
// Verify User and allow logins
function verifyUser($ID, $username, $password, $verification_code, $advisor, $majors) {
	// escape all the param's to protect against SQL Injections
	$ID = mysql_real_escape_string(strtolower($ID));
	$username = mysql_real_escape_string(strtolower($username));
	$verification_code = mysql_real_escape_string($verification_code);
	$advisor = mysql_real_escape_string(strtolower($advisor));
	$majors = mysql_real_escape_string(strtolower($majors));
	// encrypt the password, let the salt be automatically generated
	$password = crypt(mysql_real_escape_string($password));
	if($ID=="" || $username=="" || $verification_code=="" || $advisor=="" || $majors=="" || $password=="") { return false; }

	// find the user with ID and password matching the given param.
	$query = 'SELECT * FROM users WHERE username="'. $username .'" AND password="'. $verification_code .'" AND ID="'. $ID .'"';
	$r = mysql_query($query);
	if (mysql_num_rows($r) == 1) {
		// change the verification code to there password and allow logins
		$query = 'UPDATE users SET password="'. $password .'", advisor="'.$advisor.'", dept="'.$majors.'", verified="1" WHERE username="'. $username .'" AND ID="'. $ID .'"';
		mysql_query($query);
		return true;
	} else {
		return false;
	}
}
?>
