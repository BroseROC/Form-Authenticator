<?php
//Bryan Rose
//rosebj@clarkson.edu
session_start();
if(!$_SESSION['loggedin']) {
	// User isn't Logged In, make them
	header("location: /login.php");
	return;
}
if($_SESSION['clearance'] < 1){
	$title = "Clearance error!";
	include('header.php');
	print('Error! You do not have the required clearance! Please return to the previous page!');
	include('footer.php');
	return;
}
if(!isset($_GET['formID'])){
	$title = "ID Error!";
	include('header.php');
	print('Error! You must provide a form ID! Please return to the previous page!');
	include('footer.php');
	return;
}
//FormID is the ID field of the form database
$formID = $_GET['formID'];
require_once('functions/dbConnect.php');
require_once('functions/pdfdbFunctions.php');
$query = 'SELECT PDF, name FROM ' . $masterForms . ' WHERE ID=' . $formID;

//If active=1, then the formis fetched from the active forms database instead of the master forms database
if(isset($_GET['active']) && $_GET['active'] == '1'){
  $query = 'SELECT file, form FROM ' . $activeForms . ' WHERE ID=' . $formID;
}
$Arr = mysql_fetch_array(mysql_query($query), MYSQL_NUM);
if(empty($Arr)){
	$title = "Form ID Error!";
	include('header.php');
	print('Error! The form was not found! Please return to the previous page! ' . $query);
	//print('<br />' . $query . '<br />');
	//print_r($Arr);
	include('footer.php');
	return;
}
$fname = $Arr[0];
$size = filesize($fname);
$form = $Arr[1];
header('Content-Type: application/pdf');
header('Content-Disposition: atachment; filename="' . $form . '.pdf"');
header('Content-Transfer-Encoding: binary');
header('Cache-control: private');
readfile($fname);
?>
