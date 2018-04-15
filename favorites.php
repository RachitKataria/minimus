<?php 
    require "config.php";

    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($mysqli->errno) {
        echo $mysqli->error;
        exit();
    }

    $user_sql = "SELECT * FROM articles WHERE users_id = " . $_SESSION["user_id"]. ";";
    $results = $mysqli->query($user_sql);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
    $total_posts = $results->num_rows;    

    $posts_per_page = 20;
    $starting_post = 0;
    $max_pages = ceil($total_posts / $posts_per_page);

    // Get current page from url
    if(isset($_GET["page"]) and !empty($_GET["page"]))  {
        $current_page = $_GET["page"];
    } else {
        $current_page = 1;
    }

    $starting_post = 20 * ($current_page - 1);
    $page_url = preg_replace("/\?page=\d*/", '', $_SERVER['REQUEST_URI']);

    // Close connection
    $mysqli->close();

    // If logout requested
    if(isset($_GET['logout']) and $_GET['logout'] == true) {
        $_SESSION["logged_in"] = false;
        $_SESSION["username"] = "";
        $_SESSION["user_id"] = "";
        header('Location: frontpage.php');
    }
?>
    
<!DOCTYPE html>
<html>

<head>
    <title>minimus</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="favicon.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <style type="text/css">
    #nav-bg {
        background-color: #ee6f2e;
    }

    .navbar {
        color: #ffffff;
        box-shadow: 0 4px 10px 0px #999;
        margin-bottom: 20px;
    }

    body {
        color: #1E1E24;
        font-family: Raleway;
    }

    .abs-center-x {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
    }

    .title {
        font-size: 18px;
        font-weight: 500;
    }

    .author {
        font-weight: lighter;
    }

    .points {
        color: #ee6f2e;
        font-size: 18px;
        min-width: 50px;
        text-align: center;
    }

    .article {
        width: 80%;
        background-color: #f6f6f0;
        margin-bottom: -1px;
        margin-left: auto;
        margin-right: auto;
        border: 1px solid #78787B;
    }

    .article a {
        color: #1E1E24;
    }

    .article a:hover {
        text-decoration: none;
        color: #1E1E24;
    }

    .article a:visited {
        text-decoration: none;
        color: #78787B;
    }

    .load {
        margin: 10px auto 10px auto;
        width: 80%;
        background-color: #ee6f2e;
        color: #ffffff;
    }

    .load a {
        height: 100%;
        width: 100%;
        text-align: center;
    }

    .footer {
        margin-bottom: 10px;
    }

    a {
        color: #ffffff;
    }

    a:hover {
        text-decoration: none;
        color: #ffffff;
    }

    a:visited {
        color: #ffffff;
    }

    .nav-link a {
        color: #d8d8d8;
    }

    .nav-link a:hover {
        color: #ffffff;
    }

    .button-wrap {
        width: 60px;
        height: 60px;
        min-width: 60px;
        display: flex;
        align-items: center;
        text-align: center;
    }

    .trash {
        margin: 0 auto;
        border: none;
        overflow: hidden;
        outline: none;
        background: url('trash.png') center no-repeat;
        background-size: 100% 100%;
        width: 24px;
        height: 30px;
        cursor: pointer;
    }

    .trash:focus {
        outline: none;
        cursor: pointer;
    }

    .trash:hover {
        background-color: transparent;
        border: none;
        overflow: hidden;
        outline: none;
        background: url('trash_hover.png') center no-repeat;
        background-size: 100% 100%;
        width: 24px;
        height: 30px;
        cursor: pointer;
    }

    .favorites a {
        color: #ffffff;
    }

    </style>
</head>

<body>
    <!-- Navbar -->
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
                    <li class="nav-item nav-link">
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
                    <li class="nav-item nav-link">
                        <a href="<?php echo $page_url . "?logout=true"; ?>">logout</a>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </nav>
    <!-- Rows for articles -->
    <?php while ( $post = $results->fetch_assoc() ) : ?>
        <?php
            // Get post
            curl_setopt($curl, CURLOPT_URL, "https://hacker-news.firebaseio.com/v0/item/" . $post["id"] . ".json?print=pretty");
            $response = curl_exec($curl);
            $response_decoded = json_decode($response, true);

            $type = $response_decoded["type"];

            // Only display stories
            if($type != "story") {
                continue;
            }

            // Only display content with urls
            if(!isset($response_decoded["url"])) {
                continue;
            }

            $url = $response_decoded["url"];
            $score = $response_decoded["score"];
            $title = $response_decoded["title"];
            $author = $response_decoded["by"];   
            $id = $response_decoded["id"];
        ?>
        
        <div class="d-flex align-items-center flex-row article" id="<?php echo $id ?>">
            <div class="points"><?php echo $score; ?></div>
            <div class="button-wrap">
                <button class="trash" id="<?php echo $id ?>"></button>
            </div>
            <div class="d-flex flex-column">
                <div class="p-1 title">
                    <a href="<?php echo $url; ?>" target="_blank"><?php echo $title; ?></a>

                    <?php 
                        // Retrieve host and parse out www
                        $parsed_url = parse_url($url)["host"];
                        $final_url = preg_replace("~^www\.~", '', $parsed_url);
                    ?>

                    <a href="<?php echo $url; ?>" target="_blank" style="color: #78787B; font-size: 15px;"><?php echo " (" . $final_url . ")"; ?></a>
                </div>
                <div class="p-1 author">by <b><?php echo $author; ?></b></div>
            </div>
        </div>
    <?php endwhile; ?>

     <!-- Load more -->
    <?php if($current_page < $max_pages): ?> 
        <?php $current_page++; ?>
            <div class="d-flex p-2 justify-content-center flex-row load">
                <a href="<?php echo $page_url . "?page=" . $current_page; ?>">Load more! </a>
            </div>
    <?php else: ?>
        <div class="footer"></div>
    <?php endif; ?>
</body>

<script type="text/javascript">
    var buttons = document.querySelectorAll(".trash");
    console.log(buttons.length);
    for(var i = 0; i < buttons.length; ++i) {
        let button = buttons[i]; 
        button.addEventListener("click", function(){
            // Remove favorite from database
            $.ajax({
                url: "update_favorites.php?id=" + this.id + "&added=false",
                success: function(data) {
                    console.log(data);
                    var element = document.getElementById(button.id);
                    element.parentNode.removeChild(element);
                }
            });

        });
    }
</script>

</html>