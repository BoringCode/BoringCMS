<?php 
//this is the official plugin API. Some helpful stuff for plugin devs.
$request = basename($_SERVER['PHP_SELF']);
$urlPath = "";
if ($request === "index.php") {
	$urlPath = "admin/";
}
//connect to DB
require_once($urlPath . "important/plugin_connect.php");
	
//api class
class pluginAPI {
	function __construct($pdbc,$urlPath) {
		$this->dbc=$pdbc;
		$this->urlPath=$urlPath;
	}
	//create a new table for the plugin if it does not exits.
	function createDB($dbName, $sqlQuery) {
		//check to make sure that someone isn't trying to modify the default tables
		$defaultDB = array("content", "plugins", "siteinfo", "users");
		if (!in_array($dbName, $defaultDB)) {
		$sqlQuery = "CREATE TABLE $dbName ($sqlQuery)";
		$testforDB = "SELECT * FROM $dbName";
		if (!$runTestDB = mysqli_prepare($this->dbc, $testforDB)) {
			$runCreatePortfolio = @mysqli_query ($this->dbc, $sqlQuery); // Run the query.	
			$addDB = "INSERT INTO plugins (dbname) VALUES ('$dbName')";
			$addDBr = @mysqli_query ($this->dbc, $addDB); // Run the query.
		}
		}
	}
	//add something to the plugin's table (or anything else for that matter)
	function addToDB($dbName, $sqlQuery) {
		$addtoDB = "INSERT INTO $dbName $sqlQuery";
		$addtoDBr = @mysqli_query ($this->dbc, $addtoDB); // Run the query.		
	}
	//delete a row
	function deleteRowDB($dbName, $column, $delete) {
		$defaultDB = array("content", "plugins", "siteinfo", "users");
		if (!in_array($dbName, $defaultDB)) {
			$deleteRowDB = "DELETE FROM $dbName WHERE $column='$delete'";
			$deleteRowDBr = @mysqli_query ($this->dbc, $deleteRowDB); // Run the query.	
			if (isset($deleteRowDBr)) {
				return true;
			}
		}
	}
	//return the rows that are in a table
	function selectDB($dbName, $sqlQuery) {
		$selectDBq = "SELECT $sqlQuery FROM $dbName";
		$selectDBr = @mysqli_query ($this->dbc, $selectDBq); // Run the query.		
		$results = array();
			if (mysqli_num_rows($selectDBr) > 0) {
				$i = 0;
				while($results[$i] = mysqli_fetch_assoc($selectDBr)) {
					$i++;
				}
			}
		//hacky fix because I'm tired of trying to figure out why the loop runs once too many times
		$fix = array_pop($results);
		//check to see if there are any results
		if (count($results) === 0) {
			return false;
		} else {
			return $results;
		}
	}
	//is the user logged in?
	function loggedIn() {
		include_once($this->urlPath . "includes/sitefunctions.php");
		if (checkLogin() !== true) { 
			return false;
		} else {
			return true;
		}
	}
	//return site url, not happy with this one
	function adminURL() {
		$siteurlq = "SELECT siteurl FROM siteinfo";
		$siteurlr = @mysqli_query ($this->dbc, $siteurlq); // Run the query.
		$rows = mysqli_fetch_array($siteurlr, MYSQLI_ASSOC);
		return $rows["siteurl"] . "admin/";
	}
	//return the site URL, this doesn't quite work to my satisfaction
	function siteURL() {
		$siteurlq = "SELECT siteurl FROM siteinfo";
		$siteurlr = @mysqli_query ($this->dbc, $siteurlq); // Run the query.
		$rows = mysqli_fetch_array($siteurlr, MYSQLI_ASSOC);
		return $rows["siteurl"];
	}
	//get the current page (admin panel or website)
	function currentPage() {
		if (isset($_GET['page'])) {
			return $_GET['page'];
		} else {
			return "home";
		}
	}
}
//launch the plugin API class and pass the DB connection to it.
$pluginAPI = new pluginAPI($pdbc, $urlPath);
?>