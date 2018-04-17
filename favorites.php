<?php 
    require "config.php";

    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($mysqli->errno) {
        echo $mysqli->error;
        exit();
    }

    $user_sql = "SELECT articles_id as id FROM users_has_articles WHERE users_id = " . $_SESSION["user_id"]. " ORDER BY timestamp DESC;";
    $results = $mysqli->query($user_sql);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
    $total_posts = $results->num_rows;    
    $i = -1;
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
    <title>favorites</title>
    <link rel="import" href="header.html">
    <style type="text/css">
        .favorites a {
            color: #ffffff;
        }
    </style>

</head>

<body>
    <!-- Navbar -->
    <?php require 'navbar.php' ?>

    <!-- Rows for articles -->
    <?php while ( $post = $results->fetch_assoc() ) : ?>
        <?php
            $i++;
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
            $title = $response_decoded["title"];
            $author = $response_decoded["by"];   
            $id = $response_decoded["id"];
        ?>
        
        <div class="d-flex align-items-center flex-row article" id="<?php echo $id ?>">
            <div class="number"><?php echo strval($i + 1) . "."; ?></div>
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