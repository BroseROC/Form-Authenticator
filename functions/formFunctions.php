<?php
// Trevor Mullins
// CS350
// formFunctions.php
// Form Releated Functions

/*********** Generic Functions ***********/

// listforms()
// Lists all the forms in the system
// Forms that are archived will not be shown
function listForms() {
	// get all forms possible to submit
	$query = 'SELECT ID, name, PDF FROM form_list WHERE archived="0"';
	$result = mysql_query($query);
	$forms = array();
	while ($row = mysql_fetch_assoc($result)) {
		$forms[$row["ID"]] = $row["name"];
	}
	return $forms;
}

// getArchivedForms
// Only displays forms that have been archived
// Archived forms are forms which are no longer being used
function getArchivedForms() {
	$query = 'SELECT name, PDF FROM form_list WHERE archived="1"';
	$result = mysql_query($query);
	$forms = array();
	while ($row = mysql_fetch_assoc($result)) {
		$forms[$row["name"]] = $row["PDF"];
	}
	return $forms;
}

// getUsernameForForm($id)
// gets the Student username associated with a form
function getUsernameForForm($id) {
	$id = mysql_real_escape_string(strtolower($id));
	$query = 'SELECT username FROM active_forms WHERE ID="'.$id.'"';
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
	return $row[0];
}

// printStatus($status)
// Takes input of 0, 1, 2
// Where 1 is Rejected, 2 is approved and 0 is Pending
// Returns human-readable status of a form
function printStatus($status) {
	$status = mysql_real_escape_string(strtolower($status));
	if($status == 2) return "Approved";
	if($status == 1) return "Rejected";
	else return "Pending";
}

/*********** User Functions ***********/

// getStatusForUser($username, $form)
// Returns the status of some $form for user $username
function getStatusForUser($username, $form) {
	$username = mysql_real_escape_string(strtolower($username));
	$form = mysql_real_escape_string(strtolower($form));

	// get all forms for username
	$query = 'SELECT * FROM active_forms WHERE username="'. $username .'" AND ID="'.$form.'"';
	$result = mysql_query($query);
	// return array of data
	return $result;
}

// getFormsForUser($username)
// Return all forms assoicated with $username
function getFormsForUser($username) {
	$username = mysql_real_escape_string(strtolower($username));
	// get all forms for username
	//print($username);
	$query = 'SELECT * FROM active_forms WHERE username="'. $username .'"';
	$result = mysql_query($query);
	//print_r(mysql_fetch_array($result, MYSQL_ASSOC));
	// return array of data
	return $result;
}

// getFormsAssoc($username)
// Return All for that require $username
// Function used for advisors, and form that requires $username's sig will be returned
function getFormsAssoc($username) {
        $username = mysql_real_escape_string(strtolower($username));
        // get all forms for username
        //print($username);
        $query = 'SELECT * FROM active_forms WHERE signatures LIKE (\'%'.$username.'%\')';
        $result = mysql_query($query);
        //print_r(mysql_fetch_array($result, MYSQL_ASSOC));
        // return array of data
        return $result;

}

// formCompleted($id)
// Returns true if the Form has been autorized, or false if it is still pending
// Also increments the number of signitures for a form $id
function formCompleted($id) {
	$id = mysql_real_escape_string(strtolower($id));
	$query = 'SELECT totalHops,currenthop,status FROM active_forms WHERE ID="'. $id .'"';
	//print($query);
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
	$totalHops = $row[0];
	$currentHop = $row[1];
	$status = $row[2];
	//print('<br />' . $totalHops . '<br />' .$currentHop . '<br />' . $status);
	if($currentHop >= $totalHops && $status == "0") {
		$query = 'UPDATE active_forms SET status=2 WHERE ID='.$id.'';
		$result = mysql_query($query);
		return true;
	} else { 
		return false;
	}
}

// $username = approving user
// $form = form ID
function approveFormForUser($username, $form) {
	$id = mysql_real_escape_string(strtolower($form));
	$username = mysql_real_escape_string(strtolower($username));
	//print($username);
	require_once('functions/pdfdbFunctions.php');
	setSignatureRecieved($username, $form);
	// SET counter=counter+1 WHERE image_id=15
	$query = 'UPDATE active_forms SET currenthop=currenthop + 1 WHERE ID='.$id.'';
	$result = mysql_query($query);
	return $result;
}

// $username = rejecting user
// $form = form ID
function rejectFormForUser($username, $form) {
	$id = mysql_real_escape_string(strtolower($form));
	$username = mysql_real_escape_string(strtolower($username));
	
	$query = 'UPDATE active_forms SET status=1 WHERE ID='.$id.'';
	$result = mysql_query($query);
	return $result;
}

// printDataforForm($key)
// Returns the form name and username associated with a form
function printDataforForm($key) {
	$key = mysql_real_escape_string($key);
	// Select the form and username from the form where the ID is "key"
	$query = 'SELECT form,username FROM active_forms WHERE ID="'. $key .'"';
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
	$formName = $row[0];
	$user = $row[1];
	print "<strong>$user would like you to authorize $formName</strong><br /><br />";
	return;
}

/*********** Form Management Functions ***********/

// archiveForm($id)
// Archives a form
function archiveForm($id) {
	$id = mysql_real_escape_string(strtolower($id));
	$query = 'UPDATE form_list SET archived="1" WHERE ID='.$id.'';
	$result = mysql_query($query);
	return $result;
}

// removeForm($id)
// removed a form from the database
function removeForm($id) {
	$id = mysql_real_escape_string(strtolower($id));
	$query = 'DELETE FROM form_list WHERE ID='.$id.'';
	$result = mysql_query($query);
	return $result;
}
?>
