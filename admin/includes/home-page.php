<?php 
if (checkLogin() !== true) { 
	exit();
}
?>	
<?php
//check for submit
if (isset($_POST['submitted'])) {
	$sitename = htmlspecialchars($_POST['sitename'], ENT_QUOTES);
	$templateURL = htmlspecialchars($_POST['templateURL'], ENT_QUOTES);
	$siteURL = htmlspecialchars($_POST['siteURL'], ENT_QUOTES);
	$oldvalue = $_POST['submitted'];
	$errortext = "";
	
	//error reporting
	if ($sitename == '') {
		$error = true;
		$errortext .= '<p class="alert-message block-message error">Please enter enter a site name.</p>';
	}
	if ($siteURL == '') {
		$error = true;
		$errortext .= '<p class="alert-message block-message error">Please enter a siteURL.</p>';
	}
	//no errors?
	if (!isset($error)) {
	$qsiteinfo = "UPDATE siteinfo SET sitename='$sitename',templateurl='$templateURL', siteurl='$siteURL' LIMIT 1";
	$rsiteinfo = @mysqli_query ($dbc, $qsiteinfo); // Run the query.
	if ($rsiteinfo) {
	$donesiteinfo = '<p class="alert-message block-message success">Yay, site info updated!</p>';
	}
	}
}
?>	
<h2>Site Info</h2>
<?php 
if(isset($errortext)) { echo $errortext; }
if(isset($donesiteinfo)) { echo $donesiteinfo; }
$siteinfo = "SELECT sitename, siteurl, templateurl FROM siteinfo";
$siteinfoquery = @mysqli_query ($dbc, $siteinfo); // Run the query.
$rows = mysqli_fetch_array($siteinfoquery, MYSQLI_ASSOC);

//search for .html files in the template folder. Offer them as options for a template. They cannot contain a dash. This helps ensure only base files get listed.
$directory = "../templates";
$filelist = "<option value='no-template'>Please select a template (or make one)</option>";
 if ($handle = opendir($directory)) {
   while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != ".." && (strpos($file, ".html") !== false) && (strpos($file, "-") === false)) {
			if ($rows['templateurl'] === $file) { $selected = "selected='selected'"; } else { $selected = ""; }
          	$filelist .= "<option value='$file' $selected>$file</option>";
          }
       }
  closedir($handle);
 }
 ?>
<form name="site-info" action="<?php echo adminURL($dbc); ?>" method="post">
	<p><input class="span6" type="text" name="sitename" placeholder="Site Title" value="<?php echo $rows["sitename"]; ?>"></p>
	<p><select class="span6" name="templateURL"><?php echo $filelist; ?></select></p>
	<p><input class="span6" type="text" name="siteURL" placeholder="Site URL" value="<?php echo $rows["siteurl"]; ?>"/>
	<p><button class="btn primary" type="submit">Update</button></p>
	<input type="hidden" name="submitted" value="<?php echo $rows["sitename"]; ?>"/>
</form>

<hr>
<!--help me!-->
<h2>Welcome to your admin panel!</h2>
<p>If you're lost and need help you've come to the right place.</p>
<p>First look around. See that big black bar at the top? Click some links and see what happens.</p>
<ul>
	<li>The Content page is a place where you can update content on your website.</li>
	<li>The Extend page is where you can manage and install cool plugins made by smart developers. Plugins can add all sorts of cool features to your website.</li>
	<li>Manage the users that can access your admin panel from the Users page.</li>
	<li>The Plugin Pages dropdown allows you to access the admin panels for the cool plugins that you have installed (if they have admin panels).</li>
</ul>

<p class="success">Look at that, you're a master already!</p>
	