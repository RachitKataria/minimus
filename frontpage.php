<?php 
    require "init.php";

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
    <link rel="import" href="header.html">
    <link rel="icon" type="image/png" href="assets/favicon.png">

    <style type="text/css">
        .top a {
            color: #ffffff;
        }

        .navbar {
            color: #white;
        }
    </style>
</head>


<body>
    <!-- Navbar -->
    <?php require 'navbar.php' ?>

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
            $title = $response_decoded["title"];
            $author = $response_decoded["by"];   
            $id = $response_decoded["id"];
        ?>
        
        <div class="d-flex align-items-center flex-row article">
            <div class="number"><?php echo strval($i + 1) . "." ?></div>
            
            <?php if($_SESSION["logged_in"]) : ?>
                <?php 
                    // Check if id is in favorites
                    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                    
                    if ($mysqli->errno) {
                        echo $mysqli->error;
                        exit();
                    }

                    $sql = "SELECT articles_id FROM users_has_articles WHERE articles_id = " . $id . " AND users_id = " . $_SESSION["user_id"] . ";";
                    $result = $mysqli->query($sql);
                    $filled = "";

                    // Only add filled class if article is favorited
                    if($result->num_rows == 1) {
                        $filled = "heart-filled";
                    }

                ?>
                <div class="button-wrap">
                    <button class="heart <?php echo $filled; ?>" id="<?php echo $id ?>"></button>
                </div>
            <?php endif; ?>
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

<script type="text/javascript">
    var buttons = document.querySelectorAll(".heart");
    console.log(buttons.length);
    for(var i = 0; i < buttons.length; ++i) {
        let button = buttons[i]; 
        button.addEventListener("click", function(){
            let added = this.classList.toggle('heart-filled');

            // Add or remove favorites from database
            $.ajax({
                url: "update_favorites.php?id=" + this.id + "&added=" + added,
                success: function(data) {
                    console.log(data);
                }
            });
        });
    }
</script>

</html>