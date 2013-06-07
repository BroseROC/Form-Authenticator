<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
	<title><?=$title?></title> 
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="description" content="<?=$title?>" />
	<link rel="stylesheet" type="text/css" href="style/style.css" /> 
</head> 
<body> 
<div id="globalNav">
<div id="Title">Clarkson University</div>
<div id="MainNav">
<ul>
<?php
	if($_SESSION['loggedin']) {
		echo '<li><a href="logout.php">Logout</a> | </li>
			<li><a href="newForm.php">New Form</a> | </li>';
			
		if($_SESSION["clearance"] >= 2) {
		echo '<li><a href="viewForms.php?username='.$_SESSION["username"].'&advisor=yes">Form Status</a></li>';
		} else {
		echo '<li><a href="viewForms.php">Form Status</a></li>';
		}
		include 'clearance.php';
	} else {
		echo '<li><a href="login.php">Login</a></li>';
	}
?>
</ul>
</div>
<br class="clear" />
</div> 
<div id="container">
	<div id="header">
		<a href="index.php"><img id="clarksonLogo" style="float:left;" src="images/clarkson_logo.jpg" alt="Clarkson logo" title="" /></a>
		<h1 style="float:left;">Document Authenticator</h1><br class="clear" />
		<div id="nav">
		<?php if($_SESSION['loggedin']) { echo "You are logged in as <strong>{$_SESSION['username']}</strong>"; } ?>
		<br class="clear" />
		</div>
	</div>
	<div id="content">
	<br class="clear" />


