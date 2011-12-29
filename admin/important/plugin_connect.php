<?php 
$request = $_SERVER['PHP_SELF'];
$urlPath = "";
if (strpos($request,'admin/index.php') === false) {
	$urlPath = "admin/";
}
header("Location: " . $urlPath . "setup.php");
?>