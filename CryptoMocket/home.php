<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="home.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>CryptoMocket</title>
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <div class="title"><h1>Welcome to CryptoMocket!</h1></div>

    <div class="info">
      <?php
        if( !isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]) {
          echo "Please click on the Login tab then register to begin trading!";
        } else {
          echo "Click on the Portfolio tab to begin trading!";
        }  
      ?>
    </div>  
</body>
</html>