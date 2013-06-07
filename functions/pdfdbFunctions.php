<?php
//require('/home/rosebj/pdf/consts.php');

$masterForms = " form_list ";
$activeForms = " active_forms ";
$userTable = ' users ';



//Set the data completed field to the current date/time
function setDateCompleted($id){
  global $activeForms;
  $query = 'UPDATE ' . $activeForms . ' SET date_finished=FROM_UNIXTIME(' . time() . ') WHERE ID=' . $id;
  return mysql_query($query);
}

//Get the next signature that has not been recieved
function getNextSignature($id){
  global $activeForms;
  $query = "SELECT signatures FROM " . $activeForms . " WHERE ID=" . $id;
  $Arr = mysql_fetch_array(mysql_query($query), MYSQL_NUM);
  $Arr = explode(',', $Arr[0]);
  foreach($Arr as $a){
    if($a === $signature){
      if(strcmp(substr(trim($a), 0, 1),'*') === 0){
        return substr(trim($a), 1);
      }
    }
  }
  return "";
}

//Get the student's advisor
function getAdvisorUsername($studentUsername){
  global $userTable;
  $query = "SELECT advisor FROM " . $userTable . " WHERE username='" . $studentUsername . "'";
  $Arr = mysql_fetch_array(mysql_query($query), MYSQL_NUM);
  if(empty($Arr)){
    return;
  }
  $Arr = explode(' ', $Arr[0]);
  end($Arr);
  $key = key($Arr);
  $query = "SELECT username FROM " . $userTable . " WHERE lastname='" . $Arr[$key] . "'";
  $Arr = mysql_fetch_array(mysql_query($query), MYSQL_NUM);
  return $Arr[0];
}


function combine($Arr){
  $str = '';
  foreach($Arr as $a){
    $str = $str . $a . ',';
  }
  return $str;
}


//Set the next not recieved signature to recieved
function setNextSignatureRecieved($formID){
  global $activeForms;
  $query = 'SELECT signatures FROM ' . $activeForms . ' WHERE id=' . $formID;
  $res = mysql_query($query);
  $Arr = mysql_fetch_array($res, MYSQL_ASSOC);
  $Arr = explode(',', $Arr['signatures']);
  for($i = 0;$i < count($Arr);$i++){
    //print($a);
    if(strcmp(substr(trim($Arr[$i]), 0, 1), '*') != 0){
      $Arr[$i] = '*' . $Arr[$i];
      break;
    }
  }
  $str = combine($Arr);
  $query = 'UPDATE ' . $activeForms . ' SET signatures="' . $str . '" WHERE id=' . $formID;
  return mysql_query($query);
}


//Set the signature specified to received
function setSignatureRecieved($signature, $formID){
  global $activeForms;
  $signature='rosebj';
  $query = 'SELECT signatures FROM ' . $activeForms . ' WHERE id=' . $formID;
  //print($query . '<br />');
  $res = mysql_query($query);
  $Arr = mysql_fetch_array($res, MYSQL_ASSOC);
  $Arr = explode(',', $Arr['signatures']);
  for($i = 0; $i < count($Arr); $i++){
//    print($Arr[$i]);
    if($Arr[$i] === $signature){
  //    print('.' . $Arr[$i] . '.');
      $Arr[$i] = '*' . $Arr[$i];
    //  print($Arr[$i]);
      break;
    }
  }
  $str = combine($Arr);
  $query = 'UPDATE ' . $activeForms . ' SET signatures="' . $str . '" WHERE id=' . $formID;
  //print($query);
  return mysql_query($query);
}


//True if the form has been approved by the signature false otherwise
function hasSignatureRecieved($signature, $formID){
  global $activeForms;
  $query = 'SELECT signatures FROM ' . $activeForms . ' WHERE id=' . $formID;
  $res = mysql_query($query);
  $Arr = mysql_fetch_array($res, MYSQL_ASSOC);
  $Arr = explode(',', $Arr['signatures']);
  foreach($Arr as $a){
    if($a === $signature){
      if(substr(trim($a), 0, 1) === '*'){
        return true;
      }else{
        return false;
      }
    }
  }
}

//Changes the status of the form to $status
//Returns:
//  1  (submitted, no signatures)
//  2  (in process of getting signatures)
//  3  (all signatures recieved, form finalized)
function changeStatus($id, $status){
  global $activeForms;
  $query = 'UPDATE ' . $activeForms . ' SET status=' . $status . 'WHERE ID=' . $id;
  return mysql_query($query);
}


//$key => ID of active form
//Returns:
//  1  (submitted, no signatures)
//  2  (in process of getting signatures)
//  3  (all signatures recieved, form finalized)
function getStatus($id){
  global $activeForms;
  $query = 'SELECT status FROM ' . $activeForms . ' WHERE ID=' . $id;
  $A = mysql_fetch_array(mysql_query($query), MSQL_NUM);
  return $A[0];
}

//Adds the form to the master form table
function addMasterForm($file, $name, $required, $signatures, $optional = ''){
  global $masterForms;
  $query = 'INSERT INTO ' . $masterForms . ' (PDF, name, required, people, optional) VALUES("' . $file . '","' . $name . '","' . $required . '","' . $signatures . '","' . $optional . '")';
  //print($signatures);
  //print($query);
  //print($formTable);
  return mysql_query($query);
}

//Adds the form to the active form table
function addActiveForm($fname, $username, $form, $signatures){
  global $activeForms;
  global $userTable;
  global $masterForms;
  $query = "SELECT ID FROM " . $userTable . " WHERE username='" . $username . "'";
  $Arr = mysql_fetch_array(mysql_query($query), MYSQL_NUM);
  $userID = $Arr[0];
  $totalHops = 0;
  if(is_array($signatures)){
    $totalHops = count($signatures);
    $temp = '';
    foreach($signatures as $sig){
      $temp .= $sig . ',';
    }
    $signatures = $temp;
  }else{
    $temp = explode(',', $signatures);
    $totalHops = count($temp);
  }
  $query = "SELECT ID FROM " . $masterForms . " WHERE name='" . $form . "'";
  $Arr = mysql_fetch_array(mysql_query($query), MYSQL_NUM);
  $formID = $Arr[0];
  //print('<br />' . $signatures . '<br />');
  $query = 'INSERT INTO ' . $activeForms . ' (file, form, user_id, signatures, date_submitted, username, totalHops, form_id) VALUES ("' . $fname . '","' . $form . '","' . $userID . '","' . $signatures . '", FROM_UNIXTIME(' . time() . '), "' . $username . '", "' . $totalHops . '", "' . $formID . '")';
  //print($query);
  return mysql_query($query);
}

//Returns an array of signatures
function generateSignatures($userName, $masterFormName){
  //get advisor, teacher etc
  global $userTable;
  global $masterForms;
  //$query = 'SELECT advisor,dept FROM ' . $userTable . ' WHERE ID=' . $userID;
  $query = 'SELECT people FROM ' . $masterForms . ' WHERE name="' . $masterFormName . '"';
  $Arr = mysql_fetch_array(mysql_query($query), MYSQL_NUM);
  $Arr = explode(',', $Arr[0]);
  $Sigs = array();
  $advisor = false;
  $chair = false;
  foreach($Arr as $a){
    if($a === ''){
      continue;
    }
    if(strtolower($a) === 'advisor'){
      $advisor = true;
    }elseif(strtolower($a) === 'chair'){
      $chair = true;
    }else{
      $Sigs[] = $a;
    }
  }
  if($advisor){
    $Sigs[] = getAdvisorUsername($userName);
    //$query = 'SELECT advisor FROM users WHERE username="' . $userName . '"';
    //$Arr = mysql_fetch_array(mysql_query($query), MYSQL_NUM);
    //$Sigs[] = $Arr[0];
  }
  return $Sigs;
}
?>
