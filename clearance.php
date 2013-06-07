<?php
// Trevor Mullins
// CS350
// clearance.php
// Clearance Cases

/*
Clearance Levels
1 - Student
2 - Advisor
3 - Department Chair
4 - Dean
5 - Provost
6 - President
47 - Administrators
*/

// Advisors 
if($_SESSION['clearance'] >= 2) {
	echo '<li> | <a href="manageStudents.php">Manage Students</a></li>';
}

// Department Chair and Higher
if($_SESSION['clearance'] >= 3) {
	echo '<li> | <a href="manageFaculty.php">Manage Faculty</a></li>';
}

// Administrators Only
if($_SESSION['clearance'] == 47) {
	echo '<li> | <a href="addMasterForm.php">Create New Form</a></li>';
	echo '<li> | <a href="archivedForms.php">Archived Forms</a></li>';
	echo '<li> | <a href="manageForms.php">Manage Forms</a></li>';
}

?>
