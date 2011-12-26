<?php
$request = basename($_SERVER['PHP_SELF']);
$urlPath = "";
if ($request === "index.php") {
	$urlPath = "admin/";
}

//search for plugins
$iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($urlPath . "plugins"), 
            RecursiveIteratorIterator::SELF_FIRST);	

include_once($urlPath . "important/mysqli_connect.php");
//helpful class for use by admin panel, plugins need not apply
class pluginFunctions {
	function __construct($iterator,$pdbc,$urlPath) {
		$this->iterator=$iterator;
		$this->dbc=$pdbc;
		$this->urlPath=$urlPath;
	}
	//get the different types of plugins, current there are 2. Admin panel pages and something that adds functionality to the site
	function getPluginPage($function) {
	if ($function === "functions") {
		$type = "function";
	} else if ($function === "admin") {
		$type = "page";
	}
	foreach($this->iterator as $dir) {
    if($dir->isDir()) {
		$idirectory = str_replace("\\", "/", $dir->getRealpath());
		if ($ihandle = opendir($idirectory)) {
			while (false !== ($ifile = readdir($ihandle))) {
				if ($ifile != "." && $ifile != ".." && strpos($ifile, "-$type.php") !== false) {
					if ($type === "page") {
						if($_GET['page'] === str_replace("-page.php", "", $ifile)) {
							include ($this->urlPath . "plugins/plugin-functions.php");
							include($idirectory . "/" . $ifile);
						}
					} else {
						include ($this->urlPath . "plugins/plugin-functions.php");
						include($idirectory . "/" . $ifile);
					}
				}

			}
			closedir($ihandle);
		}
	}
	}
	}
	//return the nav list that is in the admin panel
	function navList() {
	$plugindirectoryTest = $this->urlPath . "plugins";
	$checkFiles = @scandir($plugindirectoryTest);
	if ((isset($checkFiles)) && (count($checkFiles) > 4) ) {
	?><li class="divider"></li><li class="dropdown"><a href="#" class="dropdown-toggle">Plugin Pages</a> <ul class="dropdown-menu"> <?php
		foreach($this->iterator as $dir) {
		if($dir->isDir()) {
		$portfoliolinkdirectory = str_replace("\\", "/", $dir->getRealpath());
		if ($portfoliolinkhandle = opendir($portfoliolinkdirectory)) {
			while (false !== ($portfoliolinkfile = readdir($portfoliolinkhandle))) {
				if ($portfoliolinkfile != "." && $portfoliolinkfile != ".." && strpos($portfoliolinkfile, "-page.php") !== false) {
					$portfoliolinkfile = str_replace("-page.php", "", $portfoliolinkfile);
					?>
					<li <?php if($_GET['page'] === $portfoliolinkfile) { ?> class="active"<?php }?> >
						<a href="<?php echo adminURL($this->dbc) . $portfoliolinkfile?>"><?php echo ucfirst($portfoliolinkfile); ?></a></li>
					<?php
				}
			}
		closedir($portfoliolinkhandle);
		}
	}
	}
	?></ul></li><?php
	}
	}
}
//start up the class and pass the iterator function
$pluginFunctions = new pluginFunctions($iterator, $dbc, $urlPath);

?>