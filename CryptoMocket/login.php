<?php
session_start();
$host = "303.itpwebdev.com";
$user = "jy873_db_user";
$password = "uscItp2021!";
$db = "jy873_mocket";

if( !isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]) {
	if (isset($_POST['username']) && isset($_POST['password']) ) {
        $error = "LOGGED IN";
		if ( empty($_POST['username']) || empty($_POST['password']) ) {
			$error = "Please enter username and password.";
		}
		else {
			$mysqli = new mysqli($host, $user, $password, $db);
			if($mysqli->connect_errno) {
				echo $mysqli->connect_error;
				exit();
			}
			$pwInput = hash("sha256", $_POST["password"]);

			$sql = "SELECT * FROM users
						WHERE username = '" . $_POST['username'] . "' AND password = '" . $pwInput . "';";
			
			$results = $mysqli->query($sql);

			if(!$results) {
				echo $mysqli->error;
				exit();
			}

            echo $results->num_rows;

			if($results->num_rows > 0) {
				$_SESSION["logged_in"] = true;
				$_SESSION["username"] = $_POST["username"];
				header("Location: ./home.php");
				
			}
			else {
				$error = "Invalid username or password.";
			}
		} 
	}
}
else {
	header("Location: ./home.php");
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Login</title>
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <div class="container">
        <div class="title"><h1>Login</h1></div>
        <div class="box-form">
            <?php
                if ( isset($error) && !empty($error) ) {
                    echo $error;
                }
			?>
            <form action="login.php" method="POST">
                <div class="mb-3">
                  <label for="username" class="form-label">Username</label>
                  <input type="text" class="form-control" id="username" name="username">
                </div>
                <div class="mb-3">
                  <label for="password" class="form-label">Password</label>
                  <input type="password" class="form-control" id="password" name=password>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
                <a href="./register.php">Register</a>
            </form>
        </div>
    </div>
</body>
</html>