<?php 
//make sure that the user is logged in before running all the functions
if (checkLogin() !== true) { 
	exit();
}
?>	
<?php
//this checks for the hidden delete input. If it is run then delete the user.
if (isset($_GET['delete-id'])) {	
	$id = $_GET['delete-id'];
	$delete_q = "DELETE FROM users WHERE id='$id'";
	$request_delete = @mysqli_query ($dbc, $delete_q); // Run the query.
}

//create new user
if (isset($_POST['submitted'])) {
//retrieve our data from POST
$username = $_POST['username'];
$pass1 = $_POST['password'];
$pass2 = $_POST['repeat-password'];
$errortext = "";

//error reporting
if ($username == '') {
$error = true;
$errortext .= '<p class="alert-message block-message error">Please enter an username.</p>';
}
if (strlen($pass1) < 4) {
$error = true;
$errortext .= '<p class="alert-message block-message error">Your password must be more then 4 chars.</p>';
}
if($pass1 != $pass2) {
$error = "YES!";
$errortext .= '<p class="alert-message block-message error">Passwords do not match.</p>';
}
if(strlen($username) > 30) {
$error = true;
$errortext .= '<p class="alert-message block-message error">Username is too long, it must be under 30 chars.</p>';
}

//no error?
if (!isset($error)) {

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
$r = @mysqli_query ($dbc, $q); // Run the query.

//yeah, alert the user
if ($r) {
$done = '<p class="alert-message block-message success">Yay, new account created!</p>';
}
}

}

?>
<h2>Current Users</h2>
<?php 
if(isset($request_delete)) { ?> <p class="alert-message block-message success">User Deleted!</p> <?php } ?>
<!--current user table-->
<table class="bordered-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
			<th>Delete</th>
          </tr>
        </thead>
        <tbody>
<?php
//get the current users from the DB.
$qusers = "SELECT id, username
FROM users ORDER BY id ASC";
$requestusers = @mysqli_query ($dbc, $qusers); // Run the query.
while ($row = mysqli_fetch_array($requestusers, MYSQLI_ASSOC)) {?>
<tr>
    <th><?php echo $row["id"];?></th>
    <td><?php echo $row["username"];?></td>
	<td><a href="<?php echo adminURL($dbc) . $mr_notactive . "users"; if ($mr_notactive !== "") { echo '&delete-id='; } else { echo'/delete/id/'; } echo $row["id"];?>" class="btn danger">Delete</a>
</tr>
<?php } ?> 
</tbody>
</table>

<hr>

<h2>Create New User</h2>
<?php 
if(isset($errortext)) { echo $errortext; }
if(isset($done)) { echo $done; }
?>
<form name="register" action="<?php echo adminURL($dbc) . $mr_notactive . "users"; ?>" method="post">
	<p><input class="span6" type="text" name="username" placeholder="Username" value="<?php 
		if (isset($_POST['submitted'])) { echo $_POST['username']; }?>"></p>
	<p><input class="span6" type="password" name="password" placeholder="Password"></p>
	<p><input class="span6" type="password" name="repeat-password" placeholder="Repeat Password"></p>
	<p><button class="btn primary" type="submit">Create</button></p>
	<input type="hidden" name="submitted" value="submitted"/>
</form>

<hr>

<!--help for the user-->
<h2>How to manage and create new users.</h2>
<p>Notice the form up above.</p>
	<ul>
		<li>The first text input is the username of the user you would like to create. </li>
		<li>The second and third form inputs are the password fields. Enter your desired password once and then enter it again so you are sure you entered it correctly.</li>
	</ul>
<p>Now click that create button!</p>
<p>All the way at the top of the page is the current users table. Here you can view the current users and delete them if you would like to revoke their access.</p>