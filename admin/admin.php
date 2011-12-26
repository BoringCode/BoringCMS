<?php 
//include the header
include("includes/header.php");
?>
      <div class="content">
        <div class="page-header">
          <h1><?php if(ifPage($_GET['page']) === true){ echo ucfirst($_GET['page']); } else { echo "404"; } ?> <small>Welcome to BoringCMS</small></h1>
        </div>
        <div class="row">
          <div class="span10">
			<?php if (ifPage($_GET['page']) !== true) { ?>
			<h2 class="error">This page does not exist</h2>
			<p>Uh oh, looks like this page doesn't exist. Please go <a href="<?php echo adminURL();?>">home</a>.</p>
			<?php } else {
				//display the credits page to mere mortals
				if ($_GET['page'] === "credits") {
					include('includes/credits.php');
					if (checkLogin() !== true) {
						echo "<hr>";
					}
				}
				if ($_GET['page'] === "login") {
					include('includes/login.php');
				} else {
				//display the pages that mere mortals simply cannot look at
				if (checkLogin() === true) {			
					$idirectory = "includes";
					if ($ihandle = opendir($idirectory)) {
					while (false !== ($ifile = readdir($ihandle))) {
					if ($ifile != "." && $ifile != ".." && strpos($ifile, "-page.php") !== false) {
							if($_GET['page'] === str_replace("-page.php", "", $ifile)) {
								include($idirectory . "/" . $ifile);
							}
						}
					}
				closedir($ihandle);
				}	
				//plugin page?
				$pluginFunctions->getPluginPage("admin");
				} else {
				//display information about the CMS to people who aren't logged in
				?> 
				<h2>What is BoringCMS?</h2>
				<p>BoringCMS is a hot new CMS created for personal use by Bradley Rosenfeld.</p> <p>It uses a simple to use CSS selector system for content. This does away with having to write complex templates.</p> <p>Just create a simple HTML template and tell the content what sections to go to. <b class="success">Simple!</b></p>
			
				<h2 class="error">Login</h2>
				<p>Please <a href="<?php echo adminURL($dbc);?>login">login</a> to acccess the admin panel.</p>
				<?php 
				}
				}
			}?>
          </div>
		  <?php //get me a sidebar
		  include("includes/sidebar.php");?>
        </div>
      </div>	  
<?php //footer please
include("includes/footer.php"); ?>