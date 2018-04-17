<nav class="navbar navbar-dark navbar-expand-lg sticky-top" id="nav-bg">
    <div class="container-fluid">
      <div class="nav navbar-nav navbar-brand abs-center-x">
          <a href="frontpage.php">minimus</a>
      </div>

      <button class="navbar-toggler custom-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarCollapse">
        <?php if(!$_SESSION["logged_in"]): ?>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item nav-link">
                    <a href="login.php">login</a>
                </li>
            </ul>
        <?php else: ?>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item nav-link">
                    <?php echo $_SESSION["username"]; ?>&nbsp; 
                </li>
                <li class="nav-item nav-link top">
                    <a href="frontpage.php">top&nbsp;</a> 
                </li>
                <li class="nav-item nav-link favorites">
                    <a href="favorites.php">favorites&nbsp;</a> 
                </li>
                <li class="nav-item nav-link following">
                    <a href="following.php">following&nbsp;</a> 
                </li>
                <li class="nav-item nav-link">
                    <a href="<?php echo $page_url . "?logout=true"; ?>">logout</a>
                </li>
            </ul>
        <?php endif; ?>
      </div>
    </div>
</nav>