<?php 
	require 'config.php';

	if(isset($_POST["user"]) and isset($_POST["pwd"]) and isset($_POST["pwd-confirm"])) {
	 	if(!empty($_POST["user"]) and !empty($_POST["pwd"]) and !empty($_POST["pwd-confirm"])) {
		 	// Only continue if passwords match
			if($_POST["pwd"] != $_POST["pwd-confirm"]) {
				$error = "Passwords do not match.";
			} else {
				// Connect to database
				$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

				if ($mysqli->errno) {
					echo $mysqli->error;
					exit();
				}

				// Check if user exists, else ask for new username
				$user_sql = "SELECT * FROM users WHERE username = '" . $_POST['user'] . "';";
				$results = $mysqli->query($user_sql);

				// Check for error
				if ( $results == false ) {
					echo $mysqli->error;
					exit();
				}

				if($results->num_rows > 0) {
					$error = "This username already exists.";
				} else {
					// Hash password
					$hashed_pwd = hash('sha512', $_POST['pwd']);
					
					// Insert into users and head to front page
					$insert_sql = "INSERT INTO users(username, password) VALUES('" . $_POST['user'] . "', '" . $hashed_pwd . "');";

					$mysqli->query($insert_sql);
					$user_sql = "SELECT * FROM users WHERE username = '" . $_POST['user']  . "';" ;

					// User entry
					$result = ($mysqli->query($user_sql))->fetch_assoc();

					$mysqli->close();

					// Set up session variables
					$_SESSION['username'] = $_POST['user'];
					$_SESSION['user_id'] = $result['id'];
					$_SESSION['logged_in'] = true;

					header('Location: frontpage.php');
				}
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
	<title>Sign up</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="favicon.png">

    <style type="text/css">
    	html, body {
    		background-color: #f6f6f0;
    		font-family: Raleway;
    		height: 100%;
    	}

    	.signup {
    		width: 400px;
    	}

    	.btn {
    		width: 50%;
    		background-color: #ee6f2e;
    		color: #ffffff;
    	}

    	.all {
    		margin-top: auto;
    		margin-bottom: auto;
    	}

    	.error {
    		color: #ff0033;
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
			<div class="container signup text-center">
				<br>
		  		<div class="text-center"><h2>Create your account</h2></div>
		  		<hr>
			  	<form action="signup.php" method="post">
				    <div class="form-group">
				    	<input type="username" class="form-control" id="user" placeholder="Username" name="user">
				    </div>
				    <div class="form-group">
				      	<input type="password" class="form-control" id="pwd" placeholder="Password" name="pwd">
				    </div>
				    <div class="form-group">
				      	<input type="password" class="form-control" id="pwd-confirm" placeholder="Confirm Password" name="pwd-confirm">
				    </div>
				    <?php if(isset($error)): ?>
				    	<div class="error">
				    		<?php echo $error; ?>
				    	</div>
				    	<br>
				    <?php endif; ?>
					<button type="submit" class="btn btn-default">Create your account</button>
		  		</form>
			</div>
		</div>
	</div>
</body>
</html>