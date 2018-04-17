<?php 
	require "config.php";

	if(isset($_POST["user"]) and isset($_POST["pwd"])) {
	 	if(!empty($_POST["user"]) and !empty($_POST["pwd"])) {
			// Connect to database
			$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

			if ($mysqli->errno) {
				echo $mysqli->error;
				exit();
			}

			// Check if user and pass exists, else ask for new username
			$user_sql = "SELECT * FROM users WHERE username = '" . $_POST['user'] . "';";
			$results = $mysqli->query($user_sql);

			// Check for error
			if ( $results == false ) {
				echo $mysqli->error;
				exit();
			}

			$entry = $results->fetch_assoc();
			$hashed_pwd = hash('sha512', $_POST['pwd']);

			// If passwords match, head to frontpage
			if($hashed_pwd == $entry["password"]) {
				$_SESSION['username'] = $_POST['user'];
				$_SESSION['logged_in'] = true;
				$_SESSION['user_id'] = $entry['id'];

				header('Location: frontpage.php');
			} else {
				$error = "Incorrect login.";
			}	
		}
		else {
			$error = "Please complete all fields";
		}		
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="icon" type="image/png" href="favicon.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css">

    <style type="text/css">
    	html, body {
    		background-color: #f6f6f0;
    		height: 100%;
    	}
    </style>
</head>
<body>
	<div class="row h-100">
		<div class="col-sm-12 my-auto"> 
			<div class="row justify-content-center icon">
				<a href="frontpage.php">
					<img src="favicon.png" width="80px" height="80px">
				</a>
			</div>
			<!-- Log in info -->
			<div class="container login text-center">
				<br>
		  		<div class="text-center"><h2>Welcome Back</h2></div>
		  		<hr>
			  	<form action="login.php" method="post">
				    <div class="form-group">
				    	<input type="username" class="form-control" id="user" placeholder="Username" name="user">
				    </div>
				    <div class="form-group">
				      	<input type="password" class="form-control" id="pwd" placeholder="Password" name="pwd">
				    </div>
				    <?php if(isset($error)): ?>
				    	<div class="error">
				    		<?php echo $error; ?>
				    	</div>
				    	<br>
				    <?php endif; ?>
					<button type="submit" class="btn btn-default">Sign in</button>
		  		</form>
			</div>
			<!-- Sign up link -->
			<div class="container text-center sign-up">
				<div>Don't have an account? <a href="signup.php">Sign up</a></div>
			</div>
		</div>
	</div>
</body>
</html>