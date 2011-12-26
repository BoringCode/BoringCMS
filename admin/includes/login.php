<?php
//handy dandy functions

//if you're loggedin redirect to admin page
if (checkLogin() === true) {
	header('Location: ' . adminURL($dbc));
}

$error_text = "";

//check for login
if (isset($_POST['submitted'])) {

//can't be too safe, remove tags and other stuff that could sneak into my DB query
$username = strip_tags($_POST['username']);
$password = strip_tags($_POST['password']);
$username = str_replace('"', "", $username);
$username = addslashes($username);
$password = addslashes($password);


//connect to the database here
$q = "SELECT password, salt FROM users WHERE username = '$username';";

$r = @mysqli_query ($dbc, $q); // Run the query.
if(mysqli_num_rows($r) < 1) {
//error
$error_text .= '<p class="alert-message block-message error">Wrong Username or Password.</p>';
$error = "YES";
} else {

//return the array
$userData = mysqli_fetch_array($r, MYSQL_ASSOC);

$hash = hash('sha256', $userData['salt'] . hash('sha256', $password) );

//does the pass match the hashified password?
if($hash != $userData['password']) {
$error_text .= '<p class="alert-message block-message error">Wrong Username or Password.</p>';
$error = "YES";
}
}

mysqli_free_result ($r); // Free up the resources.

//log me in baby!
if (!isset($error)) {
$_SESSION['logged_in'] = "1"; // store session data
$_SESSION['username'] = $username;
$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
$_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

//redirect to admin panel
header("Location: ". adminURL($dbc));
}

}
?>
<?php if(isset($error_text)) { echo $error_text; } ?>
	<form name="login" action="<?php echo adminURL($dbc); ?>login" method="post">
		<p><input class="span6" type="text" placeholder="Username" name="username" value="<?php if (isset($_POST["username"])) { echo $username;} ?>"/></p>
		<p><input class="span6" type="password" placeholder="Password" name="password" /></p>
		<button class="btn success" type="submit">Sign in</button>
		<input type="hidden" name="submitted" value="submitted"/>
	</form>






