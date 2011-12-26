<?php
//start the fun
if (isset($_POST["submitted"])) {
	$dbuser = $_POST["dbuser"];
	$dbpass = $_POST["dbpass"];
	$dbhost = $_POST["dbhost"];
	$dbname = $_POST["dbname"];
	$username = $_POST['username'];
	$pass1 = $_POST['password'];
	$pass2 = $_POST['repeat-password'];
	$sitename = htmlspecialchars($_POST['sitename'], ENT_QUOTES);
	$siteURL = htmlspecialchars($_POST['siteURL'], ENT_QUOTES);
	$error = "";
	//error reporting
	if ($dbuser === "") {
		$error .= '<p class="alert-message block-message error">Please enter a database user.</p>';
	}if ($dbpass === "") {
		$error .= '<p class="alert-message block-message error">Please enter a database password.</p>';
	}if ($dbhost === "") {
		$error .= '<p class="alert-message block-message error">Please enter a database database host.</p>';
	}if ($dbname === "") {
		$error .= '<p class="alert-message block-message error">Please enter a database name.</p>';
	}
	if ($username == '') {
		$error .= '<p class="alert-message block-message error">Please enter an username.</p>';
	}
	if (strlen($pass1) < 4) {
		$error .= '<p class="alert-message block-message error">Your password must be more then 4 chars.</p>';
	}
	if($pass1 != $pass2) {
		$error .= '<p class="alert-message block-message error">Passwords do not match.</p>';
	}
	if(strlen($username) > 30) {
		$error .= '<p class="alert-message block-message error">Username is too long, it must be under 30 chars.</p>';
	}
	if ($sitename == '') {
		$error .= '<p class="alert-message block-message error">Please enter enter a site name.</p>';
	}
	if ($siteURL == '') {
		$error .= '<p class="alert-message block-message error">Please enter a website URL.</p>';
	}	
	if ($error === "") {
		$testConnect = @mysqli_connect ($dbhost, $dbuser, $dbpass, $dbname) OR $error .= '<p class="alert-message block-message error">Connection failed. Please check the database info that you provided</p>';
		if ($error === "") {
			$mysqlFile = "important/mysqli_connect.php";
			$pluginFile = "important/plugin_connect.php";
			$mysqlFile = fopen($mysqlFile, 'w') or die($error .= '<p class="alert-message block-message error">Cannot write to mysql connect file. Please check the file permissions.</p>');
			$pluginFile = fopen($pluginFile, 'w') or die($error .= '<p class="alert-message block-message error">Cannot write to mysql connect file. Please check the file permissions.</p>');
			$connectString = '<?php DEFINE ("DB_USER", "' . $dbuser . '"); DEFINE ("DB_PASSWORD", "' . $dbpass . '"); DEFINE ("DB_HOST", "' . $dbhost . '"); DEFINE ("DB_NAME", "' . $dbname . '"); $dbc = @mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die (header("Location: ' . $siteURL . 'admin/setup")); $testforDB = "SELECT * FROM siteinfo"; if (!$runTestDB = mysqli_prepare($dbc, $testforDB)) { header("Location: ' . $siteURL . 'admin/setup"); session_destroy(); } ?>';
			$pconnectString = '<?php DEFINE ("PDB_USER", "' . $dbuser . '"); DEFINE ("PDB_PASSWORD", "' . $dbpass . '"); DEFINE ("PDB_HOST", "' . $dbhost . '"); DEFINE ("PDB_NAME", "' . $dbname . '"); $pdbc = @mysqli_connect (PDB_HOST, PDB_USER, PDB_PASSWORD, PDB_NAME) OR die (header("Location: ' . $siteURL . 'admin/setup"));  $testforDB = "SELECT * FROM siteinfo"; if (!$runTestDB = mysqli_prepare($pdbc, $testforDB)) { header("Location: ' . $siteURL . 'admin/setup"); session_destroy(); }?>';
			fwrite($mysqlFile, $connectString);
			fwrite($pluginFile, $pconnectString);
			fclose($mysqlFile);
			fclose($pluginFile);
			if ($error === "") {
				$insertContentTable = "CREATE TABLE IF NOT EXISTS `content` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`page` text NOT NULL,
				`name` text NOT NULL,
				`content` text NOT NULL,
				`selector` text NOT NULL,
				UNIQUE KEY `id` (`id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
				$insertContentTabler = @mysqli_query ($testConnect, $insertContentTable);
				
				$insertPluginsTable = "CREATE TABLE IF NOT EXISTS `plugins` (
				`dbname` text NOT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
				$insertPluginsTabler = @mysqli_query ($testConnect, $insertPluginsTable);
				
				$insertSiteinfoTable = "CREATE TABLE IF NOT EXISTS `siteinfo` (
				`sitename` text NOT NULL,
				`templateurl` text NOT NULL,
				`siteurl` text NOT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;";				
				$insertSiteinfoTabler = @mysqli_query ($testConnect, $insertSiteinfoTable);
				
				$insertUsersTable = "CREATE TABLE IF NOT EXISTS `users` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`username` varchar(30) NOT NULL,
				`password` varchar(64) NOT NULL,
				`salt` varchar(3) NOT NULL,
				PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
				$insertUsersTabler = @mysqli_query ($testConnect, $insertUsersTable);
				//if the tables have been created start putting stuff in them
				if ($insertUsersTabler && $insertSiteinfoTabler && $insertPluginsTabler && $insertContentTabler) {
						//hashify the password
						$hash = hash('sha256', $pass1);
						//creates a 3 character sequence, decoder
						function createSalt() {
							$string = md5(uniqid(rand(), true));
							return substr($string, 0, 3);
						}
						$salt = createSalt();
						$hash = hash('sha256', $salt . $hash);
						$q = "INSERT INTO users (username, password, salt) VALUES ('$username', '$hash', '$salt' )";
						$r = @mysqli_query ($testConnect, $q); // Run the query.
						
						if (isset($r)) {
							$qsiteinfo = "INSERT INTO siteinfo (sitename, siteurl, templateurl) VALUES ('$sitename','$siteURL', 'no-template')";
							$rsiteinfo = @mysqli_query ($testConnect, $qsiteinfo); // Run the query.
							if ($rsiteinfo) {
								//finally finished!
								$done = '<p class="alert-message block-message success">Success! Your website has been setup. Proceed to the <a href="' . $siteURL . 'admin">admin panel</a>.</p>';	
							} else {
								$error = '<p class="alert-message block-message error">Something weird happened. Could not update the site info.</p>';
							}
						} else {
								$error = '<p class="alert-message block-message error">Something weird happened. Could not add admin user.</p>';
						}
				} else {
					$error = '<p class="alert-message block-message error">Could not insert tables into database.</p>';
				}
			}
		}	
	}
}
?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Setup - BoringCMS</title>

    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le styles -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
      }
	  .center {
		text-align: center;
		margin-left: auto;
		margin-right: auto;
		margin-top: 20px;
	}
    </style>

	</head>

  <body>

    <div class="topbar">
      <div class="fill">
        <div class="container">
          <a class="brand" href="setup">BoringCMS</a>
        </div>
      </div>
    </div>

    <div class="container">

      <!-- Main hero unit for a primary marketing message or call to action -->
      <div class="hero-unit">
        <h1>Setup BoringCMS!</h1>
        <p>Welcome to the most awesome content management system in the world.</p>
		<p>Please provide some information about your database and your website so we can get started!</p>

      </div>

	  <form name="new-content" action="setup" method="post">
      <div class="row">
		<?php 
		if(isset($error)) { echo $error; }
		if(isset($done)) { echo $done; }
		?>
        <div class="span8">
          <h2>Database Info</h2>
          <p>Please provide your MySQL user, password, host (localhost on most servers), and the database you would like to connect to. You will need to create a new database for BoringCMS to add tables to. If you aren't quite sure what this information is please contact your web host customer service department.</p>
			<p><input class="span8" type="text" name="dbuser" placeholder="DB User" value="<?php 
			if (isset($_POST['submitted'])) { echo $_POST['dbuser']; }?>"></p>
			<p><input class="span8" type="password" name="dbpass" placeholder="DB Password" value="<?php 
			if (isset($_POST['submitted'])) { echo $_POST['dbpass']; }?>"></p>
			<p><input class="span8" type="text" name="dbhost" placeholder="DB Host" value="<?php 
			if (isset($_POST['submitted'])) { echo $_POST['dbhost']; }?>"></p>
			<p><input class="span8" type="text" name="dbname" placeholder="DB Name" value="<?php 
			if (isset($_POST['submitted'])) { echo $_POST['dbname']; }?>"></p>
        </div>
        <div class="span8">
          <h2>Site Info</h2>
          <p>Please provide your new administrator username and password. Also please enter a title for your awesome new website and the URL of your website.</p>	
		  	<p><input class="span8" type="text" name="username" placeholder="Username" value="<?php 
			if (isset($_POST['submitted'])) { echo $_POST['username']; }?>"></p>
			<p><input class="span8" type="password" name="password" placeholder="Password"></p>
			<p><input class="span8" type="password" name="repeat-password" placeholder="Repeat Password"></p>
			<hr>
			<p><input class="span8" type="text" name="sitename" placeholder="Site Title" value="<?php 
			if (isset($_POST['submitted'])) { echo $_POST['sitename']; }?>"></p>
			<p><input class="span8" type="text" name="siteURL" placeholder="Site URL (Is this it? <?php echo "http://" . $_SERVER["SERVER_NAME"] . "/"?>)" value="<?php 
			if (isset($_POST['submitted'])) { echo $_POST['siteURL']; }?>"/>  
		</div>
      </div>
	  
	  <p class="center"><button class="btn primary large center span8" type="submit">Setup BoringCMS</button></p>
	  <input type="hidden" name="submitted" value="submitted"/>
	</form>

      <footer class="center">
        <p>&copy; <?php echo date("Y"); ?> - Bradley Rosenfeld</p>
      </footer>

    </div> <!-- /container -->

  </body>
</html>