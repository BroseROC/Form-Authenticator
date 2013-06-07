<?php
session_start();
if(!$_SESSION['loggedin'] && $_SESSION['clearance'] != 47) {
	// User isn't Logged In, make them
	header("location: /login.php");
}
$title = 'Add a new form';
include('header.php');

//If the form has not been submitted
if(!empty($_POST) && !empty($_FILES)){
  $file = $_FILES['file'];
  $name = $_POST['name'];

  //Get the field data
  // i.e. what's required and what signatures are needed
  $optionaln = $_POST['optionalCount'];
  $optional = '';
  $signaturen = $_POST['signatureCount'];
  $signature = '';
  $requiredn = $_POST['requiredCount'];
  $required = '';
  for($i = 0; $i < $optionaln; $i++){
    $optional .= $_POST['optional' . $i] . ',';
  }
  for($i = 0; $i < $requiredn; $i++){
    $required .= $_POST['required' . $i] . ',';
  }
  for($i = 0; $i < $signaturen; $i++){
    $signature .= $_POST['signature' . $i] . ',';
  }
  
  //Holy includes Batman!
  require_once('functions/dbConnect.php');
  require_once('functions/pdfdbFunctions.php');
  require_once('functions/pdfFileFunctions.php');
  require_once('functions/pdfFunctions.php');
  global $Masterpath;
  $newFile = $file['name'];
  //sanitize the filename and move it
  if(($newFile = sanitizeAndMove($file['tmp_name'], $Masterpath, $newFile))){
    //File successfully moved
    //print('moved');
  }else{
    print('Error during PDF file upload. (1)</body></html>');
    require_once('functions/dbClose.php');
    include('footer.php');
    return;
  }
  $NotMet = array();
  //Check that all the required fields exist in the PDF
  if(checkMasterPdfFields($newFile, $required, $optional, $NotMet)){
    //YAY
  }else{
    print('Error: the following fields were not found in the PDF document!<br />');
    $NotMet = $NotMet['required'];
    foreach($NotMet as $key => $field){
      print($field . '<br />');
    }
    print('</body></html>');
    require_once('functions/dbClose.php');
    include('footer.php');
    return;
  } 
  //Add the form to the database
  if(addMasterForm($newFile, $name, $required, $signature, $optional)){
    print('Form successfully added.</body></html>');
  }else{
    print('Database failure (1)</body></html>');
  }  
  require_once('functions/dbClose.php');
  include('footer.php');
}else{
print(<<<EOF
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
function removeTextField(divId, textName, counterId){
}
function isValid(){
 var required = document.getElementById('required');
 var signature = document.getElementById('signature');
 var name = document.getElementById('name');
 var file = document.getElementById('file');
 if(name.value === ''){
  alert("You must enter a form name!");
  return false;
 }
 if(required.firstChild.value === ''){
  alert("You must enter at least one required field!");
  return false;
 }
 if(signature.firstChild.value === ''){
  alert("You must enter at least one required signature!");
  return false;
 }
 if(file.value === ''){
  alert("You must upload a PDF form!");
  return false;
 }
 return true;
}
</script>
</head>
<body>
<form action='addMasterForm.php' method='POST' enctype='multipart/form-data' >
Form PDF File: <input type='file' id='file' name='file' />
<br />
Form name: <input type='text' name='name' id='name' />
<br />
Required fields:
<br />
<div id='required'><input type='text' name='required0' id='required0' /><br /></div>
<input type='button' value='Add required field' onclick='addTextField("required","required","requiredCount")' />
<br />
Optional fields:
<br />
<div id="optional" ><input type='text' name='optional0' id='optional0' /><br /></div>
<input type='button' value='Add optional field' onclick='addTextField("optional","optional","optionalCount")' />
<br />
Signatures needed:
<br />
<div id="signature"><input type='text' name='signature0' id='signature0' /><br /></div>
<input type='button' value='Add signature field' onclick='addTextField("signature", "signature", "signatureCount")' />
<br />
<input type='hidden' name='requiredCount' id='requiredCount' value='1' />
<input type='hidden' name='optionalCount' id='optionalCount' value='1' />
<input type='hidden' name='signatureCount' id='signatureCount' value='1' />
<input type='submit' onclick='return isValid()' value='Submit' />
</form>
</body></html>
EOF
);
include('../footer.php');
}

function construct($Array){
  $s = '';
  foreach($Array as $a){
    $s .= $a . ',';
  }
  return $s;
}


?>
