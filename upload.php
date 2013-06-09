<?php
//Bryan Rose
//rosebj@clarkson.edu
session_start();
if(!$_SESSION['loggedin'] && $_SESSION['clearance'] < 1) {
	// User isn't Logged In, make them
	header("location: /login.php");
}
$title = "Upload new form";
include('header.php');
if(!empty($_POST) && !empty($_FILES) && isset($_POST['form'])){
$file = $_FILES['file'];
$form = $_POST['form'];
require_once('functions/pdfFileFunctions.php');
$fname = '';
$InstancePath = '/var/www/pdf/tmp/';

//Sanitize the filename and move it
if(($fname = sanitizeAndMove($file['tmp_name'], $InstancePath,  $file['name']))){
  //Yay
}else{
  print('Failure while uploading. (1)');
  include('footer.php');
  return;
}
require_once("functions/dbConnect.php");
require_once("functions/pdfFunctions.php");
$NotMet = array();
//Check that the required fields have been filled in
if(checkRequired($fname, $form, $NotMet)){
  print('<html><body>Thank you for uploading your form.<br />');
}else{
  print('<html><body>Please make sure all necessary fields are filled in!<br />Not filled in:<br />');
  foreach($NotMet as $key => $field){
    print($field['FieldNameAlt'] . '<br />');
  }
  include('footer.php');
  return;
}
require_once('functions/pdfdbFunctions.php');
$user = $_SESSION['username'];
//Get the required signatures from the master form and
//find the advisor if needed
$signatures = generateSignatures($user, $form);
//Add to database
if(addActiveForm($fname, $user, $form, $signatures)){
  print('Form succesfully uploaded.<br />');
}else{
  print('Failure while uploading.');
  include('footer.php');
  return;
}
require_once("functions/userFunctions.php");
//Send to the first signature
$notify = $signatures[0];
$query = "SELECT ID FROM " . $activeForms . " WHERE file='" . $fname . "'";
$Arr = mysql_fetch_array(mysql_query($query), MYSQL_NUM);
$message = "Attention, a form has been submitted that requires your approval.\r\nhttp://docauth.cslabs.clarkson.edu/signForm.php?id={$Arr[0]}";
print('<br />Sending notification email to ' . $notify . ' ...<br />');
notifyUser($notify, $message);
print('Notification sent.<br />');
require_once("functions/dbClose.php");

}else{

require_once("functions/dbConnect.php");
require_once("functions/pdfdbFunctions.php");
require_once("functions/formFunctions.php");
//I'm not sure why I used a switch statement
//I might have been drinking
//Generate select input
switch(isset($_GET['formID'])){
  case True:
    $formID = $_GET['formID'];
    $query = "SELECT name FROM " . $masterForms . " WHERE ID=" . $formID;
    $Arr = mysql_fetch_array(mysql_query($query), MYSQL_NUM);
    if(!empty($Arr)){
      $formName = $Arr[0];
      break;
    }
  case False:
    $Forms = listForms();
    $select = "<select name='form' id='form' ><option></option>";
    foreach($Forms as $key => $f){
      $select .= "<option>" . $f . "</option>"; 
    }
    $select .= "</select>";
    break;
}
require_once("functions/dbClose.php");
print("
<html>
<body>
<h2>Upload a new form</h2>
<form action='upload.php' method='post' enctype='multipart/form-data' >
");
if(isset($formName)){
  print("<input type='hidden' name='form' value='" . $formName . "' />");
}else{
  print($select);
}
print("
<input type='file' name='file' id='file' />
<input type='submit' value='Submit' onclick='return isValid()' />
</form>
");
include('footer.php');
}
?>
