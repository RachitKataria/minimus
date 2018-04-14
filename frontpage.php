<?php 
    require "init.php";

    // If logout requested
    if(isset($_GET['logout']) and $_GET['logout'] == true) {
        $_SESSION["logged_in"] = false;
        $_SESSION["username"] = "";
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
        min-width: 60px;
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
        color: #e5e5e5;
    }

    .nav-link a:hover {
        color: #ffffff;
    }

    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar sticky-top" id="nav-bg">
        <div class="container-fluid">
            <div class="nav navbar-nav navbar-brand abs-center-x header">
                <a href="<?php echo $page_url; ?>">minimus</a>
            </div>
            <?php if(!$_SESSION["logged_in"]): ?>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">login</a>
                    </li>
                </ul>
            <?php else: ?>
                <ul class="navbar-nav ml-auto flex-row">
                    <li class="nav-item nav-link">
                        <?php echo $_SESSION["username"]; ?> |&nbsp; 
                    </li>
                    <li class="nav-item nav-link">
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
    <?php for($i = $starting_post; $i < $starting_post + $posts_per_page and $i < $total_posts; ++$i): ?>
        <?php
            // Get post
            $post = $list_of_top_posts[$i];

            curl_setopt($curl, CURLOPT_URL, "https://hacker-news.firebaseio.com/v0/item/" . $post . ".json?print=pretty");
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
        ?>
        
        <div class="d-flex align-items-center flex-row article">
            <div class="points"><?php echo $score; ?></div>
            <div class="d-flex flex-column">
                <div class="p-1 title"><a href="<?php echo $url; ?>" target="_blank"><?php echo $title; ?></a></div>
                <div class="p-1 author">by <b><?php echo $author; ?></b></div>
            </div>
        </div>
    <?php endfor; ?>

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
</html>