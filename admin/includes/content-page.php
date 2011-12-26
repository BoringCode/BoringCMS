<?php 
//ensure logged in
if (checkLogin() !== true) { 
	exit();
}
//this will add new content to the DB.
if (isset($_POST['submitted'])) {
	$page = htmlspecialchars($_POST['page'], ENT_QUOTES);
	$name = htmlspecialchars($_POST['name'], ENT_QUOTES);
	$selector = htmlspecialchars($_POST['selector'], ENT_QUOTES);
	$content = htmlspecialchars($_POST['content'], ENT_QUOTES);
	$errortext = "";	
	$page = strtolower(str_replace(" ", "-", $page));

	//error reporting
	if ($name == '') {
		$error = true;
		$errortext .= '<p class="alert-message block-message error">Please enter a name for your content.</p>';
	}
	if ($selector == '') {
		$error = true;
		$errortext .= '<p class="alert-message block-message error">Please enter a selector.</p>';
	}
	if ($content == '') {
		$error = true;
		$errortext .= '<p class="alert-message block-message error">Please enter some content.</p>';
	}
	//no error?
	if (!isset($error)) {
		require_once ('important/mysqli_connect.php'); 
		$q = "INSERT INTO content (page, name, content, selector) VALUES ('$page', '$name', '$content', '$selector' )";
		$r = @mysqli_query ($dbc, $q); // Run the query.
		if ($r) {
		$done = '<p class="alert-message block-message success">Yay, new content added!</p>';
		}
	}
}
//update content
if (isset($_POST['update'])) {
	$page = htmlspecialchars($_POST['page'], ENT_QUOTES);
	$name = htmlspecialchars($_POST['name'], ENT_QUOTES);
	$selector = htmlspecialchars($_POST['selector'], ENT_QUOTES);
	$content = htmlspecialchars($_POST['content'], ENT_QUOTES);
	$id = $_POST['update'];
	$page = strtolower(str_replace(" ", "-", $page));	
	
	$update = "UPDATE content SET page='$page', name='$name', selector='$selector', content='$content' WHERE id='$id'";
	$update_run = @mysqli_query ($dbc, $update); // Run the query.
}
//delete content
if (isset($_POST['deleteContent'])) {
	$deleteID = $_POST['deleteContent'];	
	$deleteContentq = "DELETE FROM content WHERE id='$deleteID'";
	$deleteContent = @mysqli_query ($dbc, $deleteContentq); // Run the query.
}

if (isset($_POST['deletePage'])) {
	$deleteID = $_POST['deletePage'];	
	$deletePageq = "DELETE FROM content WHERE page='$deleteID'";
	$deletePage = @mysqli_query ($dbc, $deletePageq); // Run the query.
}

if (!isset($_GET['content-id'])) { if(isset($deleteContent)) { ?>
<p class="alert-message block-message success"><b>Success!</b> Content deleted.</p>
<hr>
<?php } 
if(isset($deletePage)) { ?>
<p class="alert-message block-message success"><b>Success!</b> Page deleted.</p>
<hr>
<?php } ?> 

<h2>Current Pages</h2>
<table class="bordered-table">
        <thead>
          <tr>
            <th>Page</th>
			<th>View Page</th>
          </tr>
        </thead>
        <tbody>
<?php
//list all the current content
$qcontent = "SELECT DISTINCT page FROM content";
$requestcontent = @mysqli_query ($dbc, $qcontent); // Run the query.
while ($row = mysqli_fetch_array($requestcontent, MYSQLI_ASSOC)) {
?>
<tr>
    <td><?php if ($row["page"] !== "") { echo ucwords(str_replace ("-", " ", $row["page"])); } else { echo "Every Page"; }?></td>
	<!--delete button-->
	<td><a href="<?php echo adminURL($dbc);?>content/edit/id/<?php if ($row["page"] !== "") { echo $row["page"]; } else { echo "every-page"; }?>" class="btn success">View</a>
</tr>
<?php } ?> 
</tbody>
</table>

<hr>

<h2>New Content</h2>
<?php 
if(isset($errortext)) { echo $errortext; }
if(isset($done)) { echo $done; }
?>
<!--new content form-->
<form name="new-content" action="<?php echo adminURL($dbc); ?>content" method="post">
	<p><input class="span6" type="text" name="name" placeholder="Name"></p>
	<p><input class="span6" type="text" name="page" placeholder="Page (leave blank if this content appears on every page)"></p>
	<p><input class="span6" type="text" name="selector" placeholder="CSS Selector"></p>
	<p><textarea class="tinymce" name="content"></textarea></p>
	<p><button class="btn primary" type="submit">Create</button></p>
	<input type="hidden" name="submitted" value="submitted"/>
</form>

<hr>
<!--help!-->
<h2>How to publish and manage content.</h2>
<p>Notice the form up above.</p>
	<ul>
		<li>The first text input is the title of your content. This is a unique title that helps describe what this content is all about.</li>
		<li>The second form input is the page that your content will appear on. If left blank your content will appear on every page that has the selector you input.</li>
		<li>The third text input is for your <a href="http://www.w3.org/TR/CSS2/selector.html" target="_blank">CSS Selector</a>. This tells BoringCMS where to display your content in the template you have installed.</li>
		<li>The final textarea is simple, this is where you type your content!</li>
	</ul>
<p>Once you have entered your content click the create button.</p>

<p>Even further up the page is the current pages table. This lists all the pages currently in your database. To view that page's content click the "view" button.</p>

<?php } elseif (is_numeric($_GET["content-id"])) {
$id = $_GET['content-id'];


$qcontent = "SELECT name, selector, content, page
FROM content WHERE id=$id LIMIT 1";
$requestcontent = @mysqli_query ($dbc, $qcontent); // Run the query.
$row = mysqli_fetch_array($requestcontent, MYSQLI_ASSOC); ?>

<h2>Update/Delete Content</h2>
	<? if(isset($update_run)) { ?><p class="alert-message block-message success">Success! Content updated.</p><?php } ?> 
	<form name="update-content" action="<?php echo adminURL($dbc); ?>content/edit/id/<?php echo $id;?>" method="post">
		<p><input class="span6" type="text" name="name" placeholder="Name" value="<?php echo $row["name"];?>"></p>
		<p><input class="span6" type="text" name="page" placeholder="Page (leave blank if content appears on every page)" value="<?php echo ucwords(str_replace ("-", " ", $row["page"]));?>"></p>
		<p><input class="span6" type="text" name="selector" placeholder="CSS Selector" value="<?php echo $row["selector"];?>"></p>
		<p><textarea class="tinymce" name="content"><?php echo $row["content"];?></textarea></p>
		<p><button class="btn primary" type="submit">Update</button></p>
		<input type="hidden" name="update" value="<?php echo $id; ?>"/>
	</form>
	
	<form name="delete-content" action="<?php echo adminURL($dbc); ?>content" method="post">
		<p><button class="btn pull-right danger" type="submit">Delete</button></p>
		<input type="hidden" name="deleteContent" value="<?php echo $id; ?>"/>
	</form>
	
	<p><a href="<?php echo adminURL($dbc);?>content/edit/id/<?php if ($row["page"] !== "") { echo $row["page"]; } else { echo "every-page"; }?>" class="btn">&larr; Back</a></p>

<hr>

<h2>How to update your content.</h2>
<p>Look up, look down, now look side to side.</p>
<p>Notice the form up above.</p>
	<ul>
		<li>The first text input is the title of your content. This is a unique title that helps describe what this content is all about.</li>
		<li>The second form input is the page that your content will appear on. If left blank your content will appear on every page that has the selector you input.</li>
		<li>The third text input is for your <a href="http://www.w3.org/TR/CSS2/selector.html" target="_blank">CSS Selector</a>. This tells BoringCMS where to display your content in the template you have installed.</li>
		<li>The final textarea is simple, this is where you type your content!</li>
	</ul>
<p>Once you have updated your content hit the update button. Or if you hate your content so much that you would drop nukes on it slam your mouse down on the delete button. If you have no idea why you are on this page click the back button.</p>
<?php } else { 
$page = $_GET['content-id'];
if ($page === "every-page") {
	$page = "";
}
?>
<h2>Current Content</h2>
<table class="bordered-table">
        <thead>
          <tr>
            <th>Name</th>
			<th>Edit/Delete</th>
          </tr>
        </thead>
        <tbody>
<?php
	//list all the current content
	$qcontent = "SELECT id, name FROM content WHERE page='" . $page . "' ORDER BY id ASC";
	$requestcontent = @mysqli_query ($dbc, $qcontent); // Run the query.
	while ($row = mysqli_fetch_array($requestcontent, MYSQLI_ASSOC)) {?>
		<tr>
			<td><?php echo $row["name"];?></td>
			<!--delete button-->
			<td><a href="<?php echo adminURL($dbc);?>content/edit/id/<?php echo $row["id"];?>" class="btn success">Edit</a>
		</tr>
	<?php } ?> 
	</tbody>
	</table>
	<form name="delete-page" action="<?php echo adminURL($dbc); ?>content" method="post">
		<p><button class="btn danger" type="submit">Delete All</button></p>
		<input type="hidden" name="deletePage" value="<?php echo $page; ?>"/>
	</form>
	<p><a href="<?php echo adminURL($dbc);?>content" class="btn">&larr; Back</a></p>

	<hr>
		<h2>How to manage your content.</h2>
		<p>Up above is the current content table. This table lists all the content that is currently in your database.</p>
		<p>If you would like to change your content (or even delete it) click the edit button in the row that you want to edit.</p>
<?php } ?>