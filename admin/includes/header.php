<?php 
$request = basename($_SERVER['PHP_SELF']);
$urlPath = "";
if ($request === "index.php") {
	$urlPath = "admin/";
}

//include dbinfo and sitefunctions
require_once ($urlPath . "important/mysqli_connect.php"); 
include_once($urlPath . "includes/sitefunctions.php");
include($urlPath . "plugins/index.php");

//Logout thingy
if (isset($_GET['page'])) {
	if ($_GET['page'] === "logout") {
		logout();
	}
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?php 
	//page title, first check for a manual title set by the page. If that doesn't exist try to create one from the file name
	if(isset($manualtitle)) { echo $manualtitle . ' - BoringCMS'; } elseif (isset($_GET['page'])) { if(ifPage($_GET['page']) === true){ echo ucfirst($_GET['page']) . " - BoringCMS"; } else { echo "404 - BoringCMS"; } } 	
	?></title>

    <!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
    <!-- styles -->
    <link href="<?php echo adminURL($dbc); ?>css/bootstrap.min.css" rel="stylesheet">
	<link href="<?php echo adminURL($dbc); ?>css/style.css" rel="stylesheet">
    <link rel="shortcut icon" href="<?php echo adminURL($dbc); ?>favicon.ico">
	
	<!--js. jquery, dropdown, and textarea-->
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo adminURL($dbc); ?>tinymce/jscripts/tiny_mce/jquery.tinymce.js"></script>
	<script type="text/javascript" src="<?php echo adminURL($dbc); ?>js/bootstrap-dropdown.js"></script>
	<script>$(document).load(function() {$('.nav').dropdown(); });</script>
	<script type="text/javascript">
		$().ready(function() {
			$('textarea.tinymce').tinymce({
				// Location of TinyMCE script
				script_url : '<?php echo adminURL($dbc); ?>tinymce/jscripts/tiny_mce/tiny_mce.js',
				// General options
				theme : "advanced",
				plugins : 		"autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
				// Theme options
				theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect",theme_advanced_buttons2 : "bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,code,|,forecolor,backcolor",
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				theme_advanced_statusbar_location : "bottom",
				theme_advanced_resizing : true,				
				content_css : "<?php echo adminURL($dbc); ?>style.css",				
				theme_advanced_resizing : false,
				width: "98%",
				height: "250px",
				forced_root_block : false,
				force_br_newlines : true,
				force_p_newlines : false
			});
		});
	</script>
  </head>
  <body>
    <div class="topbar">
      <div class="fill">        
		<div class="container">          
		  <a class="brand <?php if($_GET['page'] === "home") {echo " active";}?>" href="<?php echo adminURL($dbc); ?>">BoringCMS</a>          
		  <ul class="nav" data-dropdown="dropdown">		
			<?php //navigation, echo out the default admin pages then echo the plugin dropdown. Only display if logged in.				
			if (checkLogin() === true) {			
					$linkdirectory = "includes";
					if ($linkhandle = opendir($linkdirectory)) {
					while (false !== ($linkfile = readdir($linkhandle))) {
					if ($linkfile != "." && $linkfile != ".." && strpos($linkfile, "-page.php") !== false) {
						$linkfile = str_replace("-page.php", "", $linkfile);
						if ($linkfile !== "home" && $linkfile !== "credits") {
						?>
						<li <?php if($_GET['page'] === $linkfile) { ?> class="active"<?php }?> >
							<a href="<?php echo adminURL($dbc) . $linkfile?>"><?php echo ucfirst($linkfile); ?></a></li>
						<?php }
						}
					}
				closedir($linkhandle);
				}
				$pluginFunctions->navList("plugins");
			}
			?>			
          </ul>
		  <?php if(checkLogin() === true) { //display "Welcome, username" if logged in. If not display the login form.?>
			<p class="pull-right logged-in">Welcome, <?php echo $_SESSION['username']; ?> <a href="<?php echo adminURL($dbc);?>logout">(logout)</a></p>
		  <?php } else { ?>
          <form name="login" action="<?php echo adminURL($dbc); ?>login" method="post" class="pull-right">
            <input class="input-small" type="text" name="username" placeholder="Username">
            <input class="input-small" type="password" name="password" placeholder="Password">
            <button class="btn" type="submit">Sign in</button>
			<input type="hidden" name="submitted" value="submitted"/>
          </form>
		  <?php } ?>
        </div>
      </div>
    </div>
    <div class="container">