<div class="logo">
        <img src="img/dogerocket.png" alt="doge rocket">
</div>
<ul class="nav justify-content-center">
    <li class="nav-item">
        <a class="nav-link" href="./home.php">Leaderboard</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="./portfolio.php">Portfolio</a>
    </li>
    <?php if(!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]) :?>
        <li class="nav-item">
            <a class="nav-link" href="./login.php">Login</a>
        </li>
    <?php else: ?>
        <li class="nav-item">
            <a class="nav-link" href="./logout.php">Logout</a>
        </li>
    <?php endif;?>
</ul>