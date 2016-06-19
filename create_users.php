<?php

/* SIMPLE ADMIN PROGRAM TO QUICKLY & EASILY CREATE USERNAMES & PASSWORDS IN DATABASE */

/* created by Eugenia Duncan */

/*
SAMPLE DATABASE & TABLE TO USE IN DEMO:

CREATE DATABASE `your_db` DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `your_db`.`your_table`(
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(128) NOT NULL UNIQUE,
password VARCHAR(255) DEFAULT NULL
)ENGINE=INNODB DEFAULT CHARSET=utf8;
*/

/*
NOTE: This PHP program essentially runs the following MySQL script:

USE `your_db`; -- Basic security option.

SET @username = 'username_entry';
SET @password = 'password_entry';
SET @user 	  = @username;
SET @pswd 	  = SHA1(CONCAT(@password, 'your_salt'));

# Uncomment one at a time (insert or update):

INSERT INTO `your_db`.`your_table` (`username`, `password`) VALUES (@user, @pswd);

-- UPDATE `your_db`.`your_table` SET `password` = @pswd WHERE `username` = @user;

SELECT * FROM `your_db`.`your_table`;
*/

$db_host = 'your_hostname';
$db_user = 'your_username';
$db_pswd = 'your_password';
$db_name = 'your_database';

$_SESSION['db'] = null;

$table = 'your_table';

$message = null;

$username = null;
$password = null;

if(isset($_POST["username"]) && isset($_POST["password"]) && !empty($_POST["username"]) && !empty($_POST["password"]))
{
	$db = new mysqli($db_host, $db_user, $db_pswd, $db_name);

	$_SESSION['db'] = $db;

	$salt = 'your_salt'; //This value must NEVER change.

	$cleanedUsername = preparestringinput($_POST["username"]);
	$cleanedPassword = preparestringinput($_POST["password"]);
	
	$username = $cleanedUsername;
	$password = sha1($cleanedPassword.$salt);

/*
	//Uncomment this section to conveniently check db within this script:
	$sql = "SELECT * FROM $table";
	echo $sql;
	$result = $db->query($sql);
	while($row = $result->fetch_object()) {echo '<br>'.$row->username.', '.$row->password.'<br>';}
	$db->close();
	die();
*/

	$sql = "INSERT INTO $table (`username`, `password`) VALUES ('{$username}', '{$password}')";

	$result = $db->query($sql);

	if (!$result)
	{
	   printf("%s<br><br><a href=\"".$_SERVER['PHP_SELF']."\">Back</a>", $db->error);

	   exit();

	}elseif($result)
	{
		$postedUser = null;
		$postedPswd = null;

		$sql = "SELECT * FROM $table WHERE username = '$username' AND password = '$password' LIMIT 1";

		$result = $db->query($sql);

		if (!$result)
		{
		   printf("%s<br><br><a href=\"".$_SERVER['PHP_SELF']."\">Back</a>", $db->error);

		   exit();

		}elseif($result)
		{
			while($row = $result->fetch_row())
			{
				$postedUser = $row[1];
				$postedPswd = $row[2];
			}

			$result->close();

			$message = "<br><br>Username and Password created in Database: $db_name, Table: $table.";
			$message .= "<br><br>Username entered is <span style=\"font-weight:bold\">$postedUser</span>, Password entered is <span style=\"font-weight:bold\">$cleanedPassword</span>, and encoded Password entered is <span style=\"font-style:italic\">$postedPswd</span>.";

		}else
		{
			$message .= "<br><br>There was a problem retrieving the Username and/or Password.";
		}

	}else
	{
		$message = "<br><br>Insert attempt failed.";
	}

	$db->close();

}elseif((isset($_POST["username"]) || isset($_POST["password"])) && isset($_POST["submit"]) && (empty($_POST["username"]) || empty($_POST["password"])))
{
	$message = "<br><br>Enter both Username and Password.";

}else
{
	$message = null;
}

function preparestringinput($input)
{
  /* This function prepares string input for usage w/ minimal security breaches. */

  	$db = $_SESSION['db'];

  	$input = $input;

	if(get_magic_quotes_gpc()) $input = stripslashes($input);
	
	$input = $db->real_escape_string($input);
	$input = htmlentities($input);
	$input = trim($input);
	
	return $input;
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Create Users</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<style>
		table {border: 2px solid black}
		td, input[type=text] {border: 1px solid black}
		td {padding: 6px}
		.top_links {color: #000; font-style: italic; text-decoration: underline;}
		.top_links:hover {color: #000; font-style: italic; cursor: pointer; text-decoration: none;}
		.btn-inverse {color: #fff; background-color: #000;}
		.btn-inverse:hover {color: #fff; background-color: #171515; box-shadow: 0px 0px 2px #222222;}
	</style>    
	<script language="javascript">

		// For simplicity, this program uses basic JavaScript.

		function resetPage()
		{
			document.form1.username.value = "";
			document.form1.password.value = "";
			document.all.errorMsg.style.display = "none";
		}
				
		function newSettings()
		{
			document.form1.username.value = "";
			document.form1.password.value = "";
		}

	</script>    
  </head>
  <body style="background-color:#f3f3f3" onload="newSettings();">
	<div style="margin: 50px 50px">
		<h3>Create Users</h3>
		<div>
			<form name="form1" method="post" action="">
				<table>
				<tr>
				    <td align="center"><strong>USERNAME</strong></td>
				    <td align="center" width="200"><input type="text" name="username" maxlength="12" size="16" placeholder="Enter Username"></td>
				</tr>
				<tr>
				    <td align="center"><center><strong>PASSWORD</strong></center></td>
				    <td align="center" width="200"><input type="text" name="password" maxlength="12" size="16" placeholder="Enter Password"></td>
				</tr>
				<tr>
				    <td align="center"><input class="btn btn-inverse" type="submit" name="submit" value="Submit"></td>
				    <td><marquee align="middle" direction="ltr" behavior="slide">&larr; <i>Add User!</i></marquee></td>
				</tr>
				<tr>
				    <td align="center"><button class="btn btn-inverse" name="reset" onclick="resetPage();">Reset</button></td>
				    <td><text></text></td>
				</tr>		
				</table>			
			</form>
			<p id="errorMsg"><?php if(isset($message)) echo $message; ?></p>
		</div>
	</div>
</body>
</html>
