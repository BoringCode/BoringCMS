<?php 
$request = basename($_SERVER['PHP_SELF']);
$urlPath = "";
if ($request === "index.php") {
	$urlPath = "admin/";
}
header("Location: " . $urlPath . "setup");
?>