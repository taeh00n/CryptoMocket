<?php
	session_start();
  
    $host = "303.itpwebdev.com";
    $user = "jy873_db_user";
    $password = "uscItp2021!";
    $db = "jy873_mocket";
    $mysqli = new mysqli($host, $user, $password, $db);

    $url = "https://data.messari.io/api/v1/assets/" . strtolower($_POST['ticker']) . "/metrics/market-data";

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $resp = curl_exec($curl);
    $resp = json_decode($resp, true);
    curl_close($curl);
    $price = $resp["data"]["market_data"]["price_usd"];
    $totalPrice = 0;

    $sqlBalance = "SELECT balance FROM users WHERE username = '" . $_SESSION["username"] . "';";
    $balance = $mysqli->query($sqlBalance);
    if (!$balance) {
        echo $mysqli->error;
        exit();
    }
    $row = $balance->fetch_row();
    $money = $row[0];

    if (isset($_POST['dollars']) || !empty($_POST['dollars'])
	|| isset($_POST['ticker']) || !empty($_POST['ticker']) ) {
        $coins = $_POST['dollars']/$price;

        if($_POST["flexRadioDefault"] == "sell") {
            $tokenExistSell = "SELECT quantity FROM portfolio WHERE username = '" . $_SESSION["username"] . "' AND ticker = '" . $_POST['ticker'] . "';";
            $execute_tokenExistSell = $mysqli->query($tokenExistSell);
            if(!$execute_tokenExistSell) {
                echo $mysqli->error;
                exit();
            }
            $row2 = $execute_tokenExistSell->fetch_row();
            $numrows = $execute_tokenExistSell->num_rows;
            $currPrice = $row2[0]*$price;

            if($numrows > 0) {
                if($_POST['dollars'] > $currPrice) {
                    $error = "You do not have enough coins to sell your desired amount";
                } else {
                    $TokenQuantitySell = $row2[0] - $coins;
                    if($tokenExistSell > 0) {
                        $sqlTokenQuantitySell = $mysqli->prepare("UPDATE portfolio SET quantity = ? WHERE username = ? AND ticker = ?;");
                        $sqlTokenQuantitySell->bind_param("dss", $TokenQuantitySell, $_SESSION['username'], $_POST['ticker']);
                        $command = $sqlTokenQuantitySell->execute();
                        if (!$command) {
                            echo $mysqli->error;
                            exit();
                        }
                        $sqlTokenQuantitySell->close();
                    } else {
                        $sqlTokenDelete = $mysqli->prepare("DELETE FROM portfolio WHERE username = ? AND ticker = ?;");
                        $sqlTokenDelete->bind_param("dss", $TokenQuantitySell, $_SESSION['username'], $_POST['ticker']);
                        $command = $sqlTokenDelete->execute();
                        if (!$command) {
                            echo $mysqli->error;
                            exit();
                        }
                        $sqlTokenDelete->close();
                    }


                    $newBalance = $money + $_POST['dollars'];
                    $sqlNewBalance = $mysqli->prepare("UPDATE users SET balance = ? WHERE username = ?;");
                    $sqlNewBalance->bind_param("ds", $newBalance, $_SESSION['username']);
                    $command = $sqlNewBalance->execute();
                    if (!$command) {
                        echo $mysqli->error;
                        exit();
                    }
                    $sqlNewBalance->close();

                    $newHistory = $mysqli->prepare("INSERT INTO history(ticker, quantity, transaction, username) VALUES (?, ?, ?, ?)");
                    $newHistory->bind_param("sdss", $_POST['ticker'], $coins, $_POST["flexRadioDefault"], $_SESSION["username"]);
                    $executed = $newHistory->execute();
                    if (!$executed) {
                        echo $mysqli->error;
                        exit();
                    }
                    $newHistory->close();
                }
            } else {
                $error = "Cannot sell tokens you do not own";
            }
        } else {
            if($_POST['dollars'] > $money) {
                $error = "You do not have enough money. Please lower the quantity";
            } else {
                $newBalance = $money - $_POST['dollars'];
                $sqlNewBalance = $mysqli->prepare("UPDATE users SET balance = ? WHERE username = ?;");
                $sqlNewBalance->bind_param("ds", $newBalance, $_SESSION['username']);
                $command3 = $sqlNewBalance->execute();
                if (!$command3) {
                    echo $mysqli->error;
                    exit();
                }
                $sqlNewBalance->close();

                $newHistory = $mysqli->prepare("INSERT INTO history(ticker, quantity, transaction, username) VALUES (?, ?, ?, ?)");
                $newHistory->bind_param("sdss", $_POST['ticker'], $coins, $_POST["flexRadioDefault"], $_SESSION["username"]);
                $executed2 = $newHistory->execute();
                if (!$executed2) {
                    echo $mysqli->error;
                    exit();
                }
                $newHistory->close();

                $tokenExist = "SELECT quantity FROM portfolio WHERE username = '" . $_SESSION["username"] . "' AND ticker = '" . $_POST['ticker'] . "';";
                $execute_tokenExist = $mysqli->query($tokenExist);
                if(!$execute_tokenExist) {
                    echo $mysqli->error;
                    exit();
                }
                $row2 = $execute_tokenExist->fetch_row();
                $numrows = $execute_tokenExist->num_rows;

                if($numrows > 0) {
                    $newTokenQuantity = $coins + $row2[0];
                    $sqlTokenQuantity = $mysqli->prepare("UPDATE portfolio SET quantity = ? WHERE username = ? AND ticker = ?;");
                    $sqlTokenQuantity->bind_param("dss", $newTokenQuantity, $_SESSION['username'], $_POST['ticker']);
                    $command4 = $sqlTokenQuantity->execute();
                    if (!$command4) {
                        echo $mysqli->error;
                        exit();
                    }
                    $sqlTokenQuantity->close();
                } else {
                    $newToken = $mysqli->prepare("INSERT INTO portfolio(ticker, username, quantity, price) VALUES (?, ?, ?, ?)");
                    $newToken->bind_param("ssdd", $_POST['ticker'], $_SESSION['username'], $coins, $_POST['dollars']);
                    $statement = $newToken->execute();
                    if(!$statement) {
                        echo $mysqli->error;
                        exit();
                    }
                    $newToken->close();
                }
            }
        }
    }

    if ( !isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] ) {
        header('Location: ./home.php');
    } else {
        $sqlUpdateBalance = "SELECT balance FROM users WHERE username = '" . $_SESSION["username"] . "';";
        $updateBalance = $mysqli->query($sqlUpdateBalance);
        if (!$updateBalance) {
            echo $mysqli->error;
            exit();
        }
        $balanceRow = $updateBalance->fetch_row();
        
        $sqlPortfolio = "SELECT ticker, quantity FROM portfolio WHERE username ='" . $_SESSION["username"] . "';";
        $portfolio = $mysqli->query($sqlPortfolio);
        if (!$portfolio) {
            echo $mysqli->error;
            exit();
        }

        $sqlHistory = "SELECT ticker, quantity, transaction FROM history WHERE username ='" . $_SESSION["username"] . "';";
        $history = $mysqli->query($sqlHistory);
        if (!$history) {
            echo $mysqli->error;
            exit();
        }
    }

    $mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="portfolio.css">
    <title>Portfolio</title>
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <div class="dollars">
        <h1>Dollars Remaining:</h1>
        <h1>$<?php echo number_format($balanceRow[0],2,".",","); ?></h1>
    </div>

    <div class="container">
        <div class = "prices">
            <div class="tokenPrice">
                <h2>Ticker Search</h2>
                <h1 id="tickerName"></h1>
                <h1 id="tickerPrice"></h1>
            </div>
            <nav class="navbar navbar-light">
                <form class="form-inline" id="priceSearch">
                    <input class="form-control mr-sm-2" id="searchInput" type="search" placeholder="Ticker" aria-label="Ticker">
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Submit</button>
                    <a href="https://coinmarketcap.com/all/views/all/" target="_blank">List of Tickers</a>
                </form>
            </nav>
        </div>

        <div class="prices">
            <?php if ( isset($error) && !empty($error) ) : ?>
                <div class="text-danger">
                    <?php echo $error; ?>
                </div>
			<?php endif; ?>
            <nav class="navbar navbar-light">
                <form class="form-inline" id="transactionForm" action="portfolio.php" method="POST">
                    <div class = "color-box">
                        <div class="buttons">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="buy" value="buy">
                                <label class="form-check-label" for="flexRadioDefault1">
                                    Buy
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="sell" value="sell" checked>
                                <label class="form-check-label" for="flexRadioDefault2">
                                    Sell
                                </label>
                            </div>
                        </div>    
                    </div>
                    <input class="form-control mr-sm-2" type="search" placeholder="Ticker" name ="ticker" id="ticker-id">
                        <small id="ticker-error" class="invalid-feedback">Ticker is required.</small>

                    <input type="number" min="0" class="form-control" placeholder="Dollars" name="dollars" id="dollars-id">
                        <small id="dollars-error" class="invalid-feedback">Amount is required.</small>
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Submit</button>
                </form>
            </nav>
        </div>

    </div>

    <div class="sectionTitle">
        <h2>Portfolio</h2>
    </div>

    <div class="portfolio">
        <table class="table">
            <thead>
            <tr>
                <th scope="col">Currency</th>
                <th scope="col">Amount</th>
                <th scope="col">Value</th>
            </tr>
            </thead>
            <tbody>
            <?php while ( $row = $portfolio->fetch_assoc() ) : ?>
                <tr>
                    <th><?php echo $row["ticker"]; ?></th>
                    <td><?php echo $row["quantity"]; ?></td>
                    <td>
                    <?php

                        $url = "https://data.messari.io/api/v1/assets/" . strtolower($row['ticker']) . "/metrics/market-data";

                        $curl = curl_init($url);
                        curl_setopt($curl, CURLOPT_URL, $url);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                        $resp = curl_exec($curl);
                        $resp = json_decode($resp, true);
                        curl_close($curl);
                        $price = $resp["data"]["market_data"]["price_usd"];

                        $newPrice = $row['quantity']*$price; 
                        $totalPrice += $newPrice;
                        echo $newPrice;
                    ?>
                    </td>
                </tr>    
            <?php endwhile; ?>    
            </tbody>
        </table>         
    </div>

    <div class="value">
        <h1>Total Value:</h1>
        <h1>$<?php echo number_format($balanceRow[0]+$totalPrice,2,".",","); ?></h1>
    </div>

    <div class="sectionTitle">
        <h2>History</h2>
    </div>

    <div class="history">
        <table class="table">
            <thead>
            <tr>
                <th scope="col">Currency</th>
                <th scope="col">Buy/Sell</th>
                <th scope="col">Amount</th>
            </tr>
            </thead>
            <tbody>
            <?php while ( $row = $history->fetch_assoc() ) : ?>
                <tr>
                    <th><?php echo $row["ticker"]; ?></th>
                    <td><?php echo $row["transaction"]; ?></td>
                    <td><?php echo $row["quantity"]; ?></td>
                </tr>
            <?php endwhile; ?>    
            </tbody>
        </table>
    </div>
    <script>
        document.querySelector('#transactionForm').onsubmit = function(){
			if ( document.querySelector('#ticker-id').value.trim().length == 0 ) {
				document.querySelector('#ticker-id').classList.add('is-invalid');
			} else {
				document.querySelector('#ticker-id').classList.remove('is-invalid');
			}
			if ( document.querySelector('#dollars-id').value.trim().length == 0 ) {
				document.querySelector('#dollars-id').classList.add('is-invalid');
			} else {
				document.querySelector('#dollars-id').classList.remove('is-invalid');
			}
			return ( !document.querySelectorAll('.is-invalid').length > 0 );
		}
        function displayResults(results) {
            let convertedResults = JSON.parse(results);
            console.log(convertedResults);
            let num = convertedResults.rate;
            let n = num.toFixed(5);
            document.querySelector("#tickerName").textContent = convertedResults.asset_id_base;
            document.querySelector("#tickerPrice").textContent = "$" + n;
        }    
        function ajax(endpoint, displayResults) {
            let httpRequest = new XMLHttpRequest();
            httpRequest.open("GET", endpoint);
            httpRequest.send();
            httpRequest.onreadystatechange = function() {
                if(httpRequest.readyState == 4) {
                    if(httpRequest.status == 200) {
                        displayResults(httpRequest.responseText);
                    }
                }
            }
        }
        document.querySelector("#priceSearch").onsubmit = function(event) {
            event.preventDefault();
            let searchInput = document.querySelector("#searchInput").value.trim();
            let endpoint = "https://rest.coinapi.io/v1/exchangerate/" + searchInput + "/USD?apikey=9D2F56B1-4F3D-4958-9D19-E3C25E959359";
            ajax(endpoint, displayResults);
        }
    </script>    
</body>
</html>