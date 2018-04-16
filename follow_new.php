<?php 
	require 'config.php';

	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

	if ($mysqli->errno) {
		echo $mysqli->error;
		exit();
	}

	// If user selected
	if(isset($_POST["id"])) {
		// Add user to follower list
		$insert_sql = "INSERT INTO users_has_followers(users_id, followers_id) VALUES(" . $_POST["id"] . ", " . $_SESSION["user_id"] . ");";
		$mysqli->query($insert_sql);
		
        $message = "successfully followed user";
	}

	// List only non-followed users
	$sql = "SELECT id, username FROM users WHERE id not in (SELECT users_id FROM users_has_followers WHERE followers_id = ". $_SESSION["user_id"] . ") AND id <> ". $_SESSION["user_id"] . ";";
	$results = $mysqli->query($sql);

	$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
	<title>follow a new user</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="favicon.png">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <link rel="stylesheet" href="styles.css">

    <style type="text/css">

    .following a {
        color: #ffffff;
    }

    .btn {
        background-color: #ee6f2e;
        color: #ffffff;
    }

    .select-wrapper {
    	width: 400px;
    }

    .back {
    	width: 500px;
    }

    select {
        visibility: hidden;
    }

    .fixed-select {
    	height: 28px;
    }

    </style>

	<script type="text/javascript">
		$(document).ready(function() 
		{
	    	$('.user-select').select2({
	    		placeholder: "select a user"
	    	});
		});

		function updateFollowButton() {
			var button = document.querySelector(".follow");
			button.disabled = false;
		}

	</script>
</head>

<body>

<!-- Navbar -->
<?php require 'navbar.php'; ?>

<br>
<div class="container text-center select-wrapper"> 
	<form action="follow_new.php" id="form" method="post">
		<div class="fixed-select">
			<select class="user-select js-states form-control" name="id" onchange="updateFollowButton()">
				<option></option>
			  	<?php while($row = $results->fetch_assoc()) : ?>
					<option value="<?php echo $row["id"]; ?>"><?php echo $row["username"]; ?></option>
				<?php endwhile; ?>   
			</select>
		</div>
        <div>
        	<br>
            <?php if(isset($message)) echo $message; ?>
        </div>
        <br>
		<button type="submit" class="btn follow" disabled="true">follow user</button>
	</form>
</div>
<div class="container text-center back"> 
	<br>
	<a href="following.php" class="btn">back to followed users</a>
</div>
</body>
</html>