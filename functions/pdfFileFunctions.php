<?php

$Instancepath = "/var/www/pdf/tmp/";
$Masterpath = "/var/www/pdf/tmp/";

//Replaced everything but (a-z, A-Z, 0-9, period, dash and underscore to an underscore)
function transformFileName($fname){
  return time() . '.' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '_', $fname);
}


//Sanitizes the filename and moves it out of the temporary upload directory
function sanitizeAndMove($source, $path, $destination){
  $fname = transformFileName($destination);
  while(file_exists($path . $fname)){
    $fname = transformFileName($destination);
  }
  $fname = $path . $fname;
  //print($source . '<br />');
  //print($fname . '<br />');
  if(!move_uploaded_file($source, $fname)){
    //print('<br />failure<br />');
    return false;
  }else{
    //print('success');
    return $fname;
  }
}


?>
