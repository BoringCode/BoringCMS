<?php
//some helpful functions for use by our Jedi Masters
session_start();

//return the admin panel URL, this doesn't quite work to my satisfaction
function adminURL($dbc) {
	$siteurlq = "SELECT siteurl FROM siteinfo";
	$siteurlr = @mysqli_query ($dbc, $siteurlq); // Run the query.
	$rows = mysqli_fetch_array($siteurlr, MYSQLI_ASSOC);
	return $rows["siteurl"] . "admin/";
}
//return the site URL, this doesn't quite work to my satisfaction
function siteURL($dbc) {
	$siteurlq = "SELECT siteurl FROM siteinfo";
	$siteurlr = @mysqli_query ($dbc, $siteurlq); // Run the query.
	$rows = mysqli_fetch_array($siteurlr, MYSQLI_ASSOC);
	return $rows["siteurl"];
}
//make sure without a doubt that the user is logged in
function checkLogin() {
	if(isset($_SESSION['logged_in']) && isset($_SESSION['ip']) && isset($_SESSION['user_agent'])) {
	if ($_SESSION['logged_in'] === "1" && $_SESSION['ip'] === $_SERVER['REMOTE_ADDR'] && $_SESSION['user_agent'] === $_SERVER['HTTP_USER_AGENT']) {
		return true;
		}
	} 
}
//logout function, destroy the session and redirect to login page
function logout($dbc) {
	session_destroy();
	header("Location: login");
	exit();
}
//check to see if the current admin page is actually a page.
function ifPage($page) {	
	$request = basename($_SERVER['PHP_SELF']);
	$urlPath = "";
	if ($request === "index.php") {
		$urlPath = "admin/";
	}
	//iterate through directories in the plugins directory
	$iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($urlPath . "plugins"), 
            RecursiveIteratorIterator::SELF_FIRST);	
	//find the files located in includes that have the postfix -page.php
	$pdirectory = "includes";
	$phandle = opendir($pdirectory);
    while (false !== ($entry = readdir($phandle))) {
        if ($entry != "." && $entry != ".." && strpos($entry, "-page.php") !== false) {
			$entry = str_replace("-page.php", "", $entry);
			if (isset($pages)) {
				array_push($pages, $entry);
			} else {
				$pages = array($entry);
			}
		}
    }
	//look in plugins directory for -page.php
	foreach($iterator as $dir) {
		if($dir->isDir()) {
			$plugindirectory = str_replace("\\", "/", $dir->getRealpath());
			$pluginhandle = opendir($plugindirectory);
			while (false !== ($pluginentry = readdir($pluginhandle))) {
				if ($pluginentry != "." && $pluginentry != ".." && strpos($pluginentry, "-page.php") !== false) {
				$pluginentry = str_replace("-page.php", "", $pluginentry);
					if (!in_array($pluginentry, $pages)) {
						if (isset($pages)) {
							array_push($pages, $pluginentry);
						} else {
							$pages = array($pluginentry);
						}
					}
				}
			}
			closedir($pluginhandle);
		}
	}
	closedir($phandle);
	
	if (in_array($page, $pages) || ($page === "credits") || ($page === "login")) {
		return true;
	}
}
//get the file extension
function getExtension($str) {
         $i = strrpos($str,".");
         if (!$i) { return ""; } 

         $l = strlen($str) - $i;
         $ext = substr($str,$i+1,$l);
         return $ext;
 }
?>