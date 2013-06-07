<?php
// Trevor Mullins
// CS350
// index.php
// Main Homepage

session_start();
// "import" functions a user would need
require_once("functions/userFunctions.php");
logout();
session_regenerate_id();
header("location: index.php");
?>
