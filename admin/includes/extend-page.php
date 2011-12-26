<?php 
//delete directory function. This is used when deleting a plugin.
function deleteDirectory($dir) {
    if (!file_exists($dir)) return true;
    if (!is_dir($dir) || is_link($dir)) return unlink($dir);
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') continue;
            if (!deleteDirectory($dir . "/" . $item)) {
                chmod($dir . "/" . $item, 0777);
                if (!deleteDirectory($dir . "/" . $item)) return false;
            };
        }
        return rmdir($dir);
    }
//make sure logged in
if (checkLogin() !== true) { 
	exit();
}
//upload plugin
if(isset($_FILES["zip_file"]["name"])) {
	$filename = $_FILES["zip_file"]["name"];
	$source = $_FILES["zip_file"]["tmp_name"];
	$type = $_FILES["zip_file"]["type"];
	$error = "";
 
	$name = explode(".", $filename);
	$accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
	foreach($accepted_types as $mime_type) {
		if($mime_type == $type) {
			$okay = true;
			break;
		} 
	} 
	$continue = strtolower($name[1]) == 'zip' ? true : false;
	if(!$continue) {
		$error .= "The file you are trying to upload is not a .zip file. Please try again.";
	} 
	$target_path = "plugins".$filename;  // change this to the correct site path
	if(move_uploaded_file($source, $target_path)) {
		$zip = new ZipArchive();
		$x = $zip->open($target_path);
		if ($x === true) {
			$zip->extractTo("plugins"); // change this to the correct site path
			$zip->close(); 
			unlink($target_path);
		}
		$done = "<p class='alert-message block-message success'>Success! Your .zip file was uploaded and unpacked.</p>";
	} else {	
		$error .= "<p class='alert-message block-message error'>There was a problem with the upload. Please try again.</p>";
	}
}

//delete a plugin
if (isset($_GET['extend-delete'])) {
	$name = $_GET['extend-delete'];
	deleteDirectory("plugins/$name");
	$request_delete = true;
	
	/* 
	eventually I will use this to clean up the DB. But I haven't thought of a good way to do this
	===============
	$deleteExtend = "SELECT dbname FROM plugins WHERE dbname='$name' LIMIT 1";
	$requestDB = @mysqli_query ($dbc, $deleteExtend); // Run the query.

	while ($row = mysqli_fetch_array($requestDB, MYSQLI_ASSOC)) {
		$dbname = $row["dbname"];
		$deleteDB = "DROP TABLE $dbname";
		if (!$deleteDBRun = mysqli_prepare($dbc, $deleteDB)) {
			$deleteDBRun = @mysqli_query ($dbc, $deleteDB); // Run the query.
				$deletefromExtend = "DELETE FROM plugins WHERE dbname='$name' LIMIT 1";
				$requestfromDB = @mysqli_query ($dbc, $deletefromExtend); // Run the query.
		}
	}
	*/
} else {
	//this is the iterator that goes through the plugins directory searching for plugins
	$iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator("plugins"), 
            RecursiveIteratorIterator::SELF_FIRST);
}
?>
<h2>Current Plugins</h2>
<?php 
if(isset($request_delete)) { ?> <p class="alert-message block-message success">Plugin Deleted!</p> <?php }
//check to see if there are actually plugins installed
$plugindirectoryTest = "plugins";
$checkFiles = @scandir($plugindirectoryTest);
if ((isset($checkFiles)) && (count($checkFiles) > 4) ) {  
?> 
<table class="bordered-table">
        <thead>
          <tr>
			<th>Plugin</th>
            <th>Files</th>
			<th>Delete</th>
          </tr>
        </thead>
        <tbody>
<?php
	//list all the plugins
	foreach($iterator as $dir) {
		if($dir->isDir()) {
			$plugindirectoryPage = str_replace("\\", "/", $dir->getRealpath());
			$deleteFolder = basename($plugindirectoryPage);
			?> <tr>
			<td><?php echo $deleteFolder; ?></td>
			<td><ul><?php 
			if ($pluginPageHandle = opendir($plugindirectoryPage)) {
				while (false !== ($pluginPage = readdir($pluginPageHandle))) {
					if ($pluginPage != "." && $pluginPage != ".." && strpos($pluginPage, "index.php") === false) { 
					$fileExtensionRemoved = str_replace(".php", "", $pluginPage); ?>
						<li><?php 
						if ($pluginPage === "readme.txt") {
							echo "<b>README.txt: </b>";
							include ($plugindirectoryPage . "/" . $pluginPage);
						} else {
						echo $pluginPage;
						
						?></li>
			<?php 		}
					}
				}
			closedir($pluginPageHandle);
			}
		?> 		<!--close off the file list and display a delete button. JS makes the user confirm before deleting.-->
				</ul> </td> <td><a href="<?php echo adminURL($dbc);?>extend/delete/<?php echo $deleteFolder ?>" class="btn danger" onclick="clicked = confirm('Are you sure?'); if (clicked === false) { return false;};">Delete</a>
				</tr>
<?php 					
		}
	} ?>
</tbody>
</table>
<?php } else {
 ?>
<p class="alert-message block-message error">There are no plugins installed. Get some!</p>
<?php } ?> 
<hr>
<h2>Upload Plugin</h2>
<?php if(isset($error)) {echo $error;} if(isset($success)) {echo $success;}?>
<!--plugin upload form-->
<form name="plugin-upload" action="<?php echo adminURL($dbc); ?>extend" method="post" enctype="multipart/form-data">
	<p><label for="image">Plugin ZIP File: </label> <input name="zip_file" type="file" id="image"/></p>
	<p><button class="btn primary" type="submit">Upload</button></p>
	<input type="hidden" name="upload" value="submitted"/>
</form>
<hr>
<!--help!-->
<h2>What are plugins?</h2>
<p>Plugins are scripts made by smart developer monkeys that add features to BoringCMS.</p>
<p>Perhaps you want to add a portfolio feature to your website, or maybe a blog. <b class="success">All this is possible with plugins!</b></p>

<h3>How to install plugins</h3>
<ol>
	<li>First, find a plugin you like.</li>
	<li>Then simply upload the plugin to <span class="label">admin/plugins/</span> (or use the helpful upload function).</li>
	<li>Drink some coffee, <b class="success">you're done!</b></li>
</ol>