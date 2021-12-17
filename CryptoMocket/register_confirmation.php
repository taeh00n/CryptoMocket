<?php
session_start();
$host = "303.itpwebdev.com";
$user = "jy873_db_user";
$password = "uscItp2021!";
$db = "jy873_mocket";

if (!isset($_POST['username']) || empty($_POST['username'])
	|| !isset($_POST['password']) || empty($_POST['password']) ) {
	$error = "Please fill out all required fields.";
}
else {
    $mysqli = new mysqli($host, $user, $password, $db);
    if($mysqli->connect_errno) {
		echo $mysqli->connect_error;
		exit();
	}

    $register = $mysqli->prepare("SELECT * FROM users WHERE username = ?");
    $register->bind_param("s", $_POST['username']);
    $execute_register = $register->execute();
    if(!$execute_register) {
        echo $mysqli->error;
    }

    $register->store_result();
    $numrows = $register->num_rows;

    $register->close();

    if($numrows>0) {
        $error = "Username or email has been already taken. Please choose another one.";
    } else {
        $password = hash("sha256", $_POST["password"]);
        $balance = 100000.00;
        $userRegister = $mysqli->prepare("INSERT INTO users(username, password, balance) VALUES(?,?,?)");
		$userRegister->bind_param("ssd", $_POST["username"], $password, $balance);
		$executed = $userRegister->execute();
		if(!$executed) {
			echo $mysqli->error;
		}
        $userRegister->close();
    }

    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="register_confirmation.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Registration Confirmation</title>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="container">
		<div class="row mt-4">
			<div class="col-12">
				<?php if ( isset($error) && !empty($error) ) : ?>
					<div class="text-danger"><?php echo $error; ?></div>
				<?php else : ?>
					<div class="text-success"><?php echo $_POST['username']; ?> was successfully registered. Please <a href="./login.php">login</a> to begin trading!</div>
				<?php endif; ?>
		</div>
	</div>

</body>
</html>