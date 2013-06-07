<?php
// Trevor Mullins
// CS350
// dbConnect.php
// Database Related Items

// Database username
$dbUser = "root";
// Database password for the above user
$dbPassword = "2012cosi";
// Location of the Database
$dbLocation = "localhost";
// Name of the Table in the Database
$dbName = "documents";

// Database Connect Sequence
$dbLink = mysql_connect($dbLocation, $dbUser, $dbPassword);
if (!$dbLink) {
    die('Could not connect: ' . mysql_error());
}

// Select the Table from the Database
$databse = mysql_select_db($dbName, $dbLink);
if (!$databse) {
    die ('DB ERROR: ' . mysql_error());
}

// Close the database at the end of the files that need to open a connection
//mysql_close($dbLink);

?>
