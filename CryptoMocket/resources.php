<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="resources.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Resources</title>
</head>
<body>
    <?php include 'nav.php'; ?>

    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="whitepapers" data-toggle="pill" href="#whitepapers" role="tab" aria-controls="pills-whitepapers" aria-selected="true">Whitepapers</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="defi" data-toggle="pill" href="#defi" role="tab" aria-controls="pills-defi" aria-selected="false">DeFi</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="wallets" data-toggle="pill" href="#wallets" role="tab" aria-controls="pills-wallets" aria-selected="false">Wallets</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="add" data-toggle="pill" href="#" role="tab" aria-controls="pills-add" aria-selected="false">Add</a>
        </li>
      </ul>
      <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="whitepapers" role="tabpanel" aria-labelledby="pills-home-tab">
            <table class="table">
                <thead>
                  <tr>
                    <th scope="col">Whitepaper Currency</th>
                    <th scope="col">URL</th>
                  </tr>
                </thead>
                <tr>
                    <th>Bitcoin</th>
                    <td><a href="https://bitcoin.org/bitcoin.pdf">https://bitcoin.org/bitcoin.pdf</a></td>
                </tr>
                <tr>
                    <th>Ethereum</th>
                    <td><a href="https://ethereum.org/en/whitepaper/">https://ethereum.org/en/whitepaper/</a></td>
                </tr>
            </table>    
        </div>
        <div class="tab-pane fade" id="defi" role="tabpanel" aria-labelledby="pills-profile-tab">
            <table class="table">
                <thead>
                  <tr>
                    <th scope="col">DeFi Protocol</th>
                    <th scope="col">URL</th>
                  </tr>
                </thead>
            </table> 
        </div>
        <div class="tab-pane fade" id="wallets" role="tabpanel" aria-labelledby="pills-contact-tab">
            <table class="table">
                <thead>
                  <tr>
                    <th scope="col">Wallet Name</th>
                    <th scope="col">URL</th>
                  </tr>
                </thead>
            </table>     
        </div>
      </div>
</body>
</html>