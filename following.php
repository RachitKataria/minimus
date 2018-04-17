<?php 
    require "init.php";

    // If logout requested
    if(isset($_GET['logout']) and $_GET['logout'] == true) {
        $_SESSION["logged_in"] = false;
        $_SESSION["username"] = "";
        $_SESSION["user_id"] = "";
        header('Location: frontpage.php');
    }  

    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($mysqli->errno) {
        echo $mysqli->error;
        exit();
    }

    // Check if user was unfollowed
    if(isset($_POST["user_id"])) {
        $unfollow_sql = "DELETE FROM users_has_followers WHERE users_id = " . $_POST["user_id"] . " AND followers_id = " . $_SESSION["user_id"] . ";";
        $result = $mysqli->query($unfollow_sql);

        if($result) {
            $message = "user successfully unfollowed";
        }
    }

    // List only followed users
    $sql = "SELECT id, username FROM users 
    JOIN users_has_followers ON followers_id = ". $_SESSION["user_id"] . " 
    WHERE users_has_followers.users_id = users.id;";

    $results = $mysqli->query($sql);
?>
    
<!DOCTYPE html>
<html>

<head>
    <title>following</title>
    <link rel="import" href="header.html">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

    <style type="text/css">
        .following a {
            color: #ffffff;
        }

        .select-wrapper {
            width: 400px;
        }

        .btn {
            background-color: #ee6f2e;
            color: #ffffff;
            width: 200px;
        }

        .fixed-select {
            height: 28px;
        }

        select {
            visibility: hidden;
        }

    </style>

</head>
    
<script type="text/javascript">
    $(document).ready(function() 
    {
        $('.user-select').select2({
            placeholder: "select a user",
            "language": {
                   "noResults": function(){
                       return "not following any users";
                }
            }
        });
    });

    function selectDidChange() {
        console.log($('.user-select').height());
        // Enable unfollow button
        var unfollow_button = document.querySelector(".unfollow");        
        unfollow_button.disabled = false;

        // Enable view anchor
        var view_anchor;
        if(view_anchor = document.querySelector(".disabled")) {
            view_anchor.classList.remove("disabled");
        }
            
        // Retrieve user id to view
        var id = $('.user-select').val();
        var text = $('.user-select option:selected').text();
        document.querySelector("#view_anchor").href = "follow_view.php?id=" + id + "&name=" + text;
    }
</script>

<body>
    <!-- Navbar -->
    <?php require 'navbar.php' ?>

    <br>
    <div class="container text-center select-wrapper">
        <form class="d-inline" method="post" action="following.php">
        <div class="fixed-select">
            <select class="user-select js-states form-control" name="user_id" onchange="selectDidChange()">
                <option></option>
                <?php while($row = $results->fetch_assoc()) : ?>
                    <option value="<?php echo $row["id"]; ?>"><?php echo $row["username"]; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <br><br>
        <div class="text">
            <?php if(isset($message)) echo $message; ?>
            <br>
            <br>
        </div>
        <div>
            <a href="" class="btn view disabled" id="view_anchor">view user's favorites</a>
        </div>
        <div>
            <br>
            <button type="submit" class="btn unfollow" disabled="true">unfollow user</button>
        </div>
        </form>
        <div>
            <br>
            <a href="follow_new.php" class="btn">follow a new user</a>
        </div>
    </div>
</body>

</html>