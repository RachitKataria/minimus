<nav class="navbar sticky-top" id="nav-bg">
    <div class="container-fluid">
        <div class="nav navbar-nav navbar-brand abs-center-x header">
            <a href="frontpage.php">minimus</a>
        </div>
        <?php if(!$_SESSION["logged_in"]): ?>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item nav-link">
                    <a href="login.php">login</a>
                </li>
            </ul>
        <?php else: ?>
            <ul class="navbar-nav ml-auto flex-row">
                <li class="nav-item nav-link">
                    <?php echo $_SESSION["username"]; ?> |&nbsp; 
                </li>
                <li class="nav-item nav-link top">
                    <a href="frontpage.php">top&nbsp;</a> 
                </li>
                <li class="nav-item nav-link">
                    |&nbsp;
                </li>
                <li class="nav-item nav-link favorites">
                    <a href="favorites.php">favorites&nbsp;</a> 
                </li>
                <li class="nav-item nav-link">
                    |&nbsp;
                </li>
                <li class="nav-item nav-link following">
                    <a href="following.php">following&nbsp;</a> 
                </li>
                <li class="nav-item nav-link">
                    |&nbsp;
                </li>
                <li class="nav-item nav-link">
                    <a href="<?php echo $page_url . "?logout=true"; ?>">logout</a>
                </li>
            </ul>
        <?php endif; ?>
    </div>
</nav>