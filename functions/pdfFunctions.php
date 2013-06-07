<?php


//Returns true if all required fields have a value
//Fills NotMet with the fields that are not filled in
//If provided, NotMet is assumed to be empty
function checkRequired($fname, $form, &$NotMet = NULL){
  
  $Info = getMasterPdfInfo($form);
  $RequiredArr = preg_split('/,/', $Info['required'], NULL,  PREG_SPLIT_NO_EMPTY);
  $FieldMap = parsePdfFields($fname);
  //print('<pre>');
  //print_r($Info);
  //print_r($RequiredArr);
  //print_r($FieldMap);
  //print(isset($NotMet));
  //print('</pre>');
  foreach($RequiredArr as $key => $field){
    //print('<br />' . array_key_exists($field, $FieldMap) . ' ' . $field);
    if(!array_key_exists($field, $FieldMap) || !array_key_exists('FieldValue', $FieldMap[$field])){
     //         print($FieldMap[$field]['FieldNameAlt']);
      if(!isset($NotMet)){
        return false;
      }else{
        $NotMet[] = $FieldMap[$field];
      }
    }
  }
  if(isset($NotMet) && count($NotMet) > 0){
    return false;
  }
  return true;
}
//Returns a map
//$Map[field name] = map of attributes
//Imma map imma map imma map
function parsePdfFields($file){
  //Run the following command to understand the splitting
  $exec = 'pdftk ' . $file . ' dump_data_fields';
  //print($exec);
  $text = shell_exec($exec);
  //print('<br />' . $text . '<br />');
  //print(count(preg_split("/---\n/", $text)));
  $Map = array();
  $Fields = preg_split("/---\n/", $text);
  foreach($Fields as $field){
    //print($field . '<br />');
    $Arr = mapField($field);
    if(array_key_exists('FieldNameAlt', $Arr)){
      $Map[$Arr['FieldNameAlt']] = $Arr;
    }
  }
  return $Map;
}

//Returns a map of the field
function mapField($field){
  $Map = array(); 
  $Lines = explode("\n", $field);
  //print('lines: ' . count($Lines));
  foreach($Lines as $line){
    $Pair = explode(': ', $line);
    //print_r($Pair);
    if(count($Pair) === 2){
      $Map[$Pair[0]] = $Pair[1];
    }
  }
  //print_r($Map);
  return $Map;
}


//Returns true if the master PDF ($file) contains
//all the required fields and optional fields
function checkMasterPdfFields($file, $required, $optional, &$NotMet = NULL){
  if(!isset($NotMet)){
    $NotMet = array();
    //  print('notmet is null');
  }
  $Map = parsePdfFields($file);
  $required = preg_split('/,/', $required, NULL, PREG_SPLIT_NO_EMPTY);
  $pass = TRUE;
  $NotMet['required'] = array();
  //    print('entering for loop');
  foreach($required as $key => $r){
    if(!array_key_exists($r, $Map)){
      $pass = FALSE;
      $NotMet['required'][] = $r;
    }
  }
  //if(strlen($optional) > 0){
//    $optional = preg_split('/,\s*/', $optional);
//    foreach($optional as $r){
//      if($Map[$r] === NULL){
//        $pass = FALSE;
//        if($NotMet['optional'] === NULL){
//          $NotMet['optional'] = array();
//        }
//        $NotMet['optional'][] = $r;
//      }
//    }
//  }
  return $pass;
}


//Returns an associative array of Master PDF info
//i.e. file name, name, requried fields, optional fields, signature fields
function getMasterPdfInfo($name){
  $query = 'SELECT * FROM form_list WHERE name="' . $name . '"';
  $B =  mysql_fetch_array(mysql_query($query), MYSQL_ASSOC);
  return $B;
}
?>
