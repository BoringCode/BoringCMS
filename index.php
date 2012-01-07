<?php
	//check for mod_rewrite
	if (isset($_GET['mr'])) {
		if ($_GET['mr'] === "true") {
			$mr = true;
			$mr_notactive = "";
		} else {
			$mr = false;
			$mr_notactive = "?page=";
		}
	} else {
		$mr = false;
		$mr_notactive = "?page=";
	}
	if (isset($_GET['page'])) {
		$page = mysql_real_escape_string($_GET['page']);
	} else {
		$page = "home";
	}
	
	include_once ('admin/important/mysqli_connect.php');
	include_once('admin/includes/sitefunctions.php');
	include("admin/plugins/index.php");
	include 'phpQuery/phpQuery.php';
		
	//load template (blank HTML file waiting to be filled)	
	$siteinfo = "SELECT templateurl, sitename FROM siteinfo";
	$siteinfoquery = @mysqli_query ($dbc, $siteinfo); // Run the query.
	$siteinfor = mysqli_fetch_array($siteinfoquery, MYSQLI_ASSOC);
		
	if ($siteinfor['templateurl'] !== "no-template") {
		if (file_exists("templates/" . $page . "-" . $siteinfor['templateurl'])) {
			$file = 'templates/' . $page . "-" . $siteinfor['templateurl'];
		} else {
			$file = 'templates/' . $siteinfor['templateurl'];
		}		
		$file = phpQuery::newDocumentFileHTML($file);
	
		//title of page	
		$element = pq("title");
		if ($page === "home") {
			$content = $element->html($siteinfor["sitename"]);	
		} else {
			$content = $element->html(ucwords(str_replace ("-", " ", $page)) . " - " .$siteinfor["sitename"]);	
		}	

		//content
		$qcontent = "SELECT selector, content FROM content WHERE (page='$page' OR page='' )";
		$requestcontent = @mysqli_query ($dbc, $qcontent); // Run the query.

		while ($row = mysqli_fetch_array($requestcontent, MYSQLI_ASSOC)) {
			$element = pq(html_entity_decode($row["selector"]));
			$content = $element->html(html_entity_decode($row["content"]));
		}	
	
		$file->find('header meta')->remove();	
		
		$pluginFunctions->getPluginPage("functions");
	
		//Output updated file (filled HTML file)
		echo $file;	
	} else {
		echo "<title>Error - No template set</title>";
		echo 'No template set. Please select one from the <a href="' . adminURL($dbc) .'">admin panel</a>.';
	}
	mysqli_close($dbc);
?>