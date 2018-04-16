<?php 
	require "config.php";

	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

	if ($mysqli->errno) {
		echo $mysqli->error;
		exit();
	}

	if($_GET["added"] == "true") {
		$user_sql = "INSERT INTO users_has_articles(users_id, articles_id) VALUES(" . $_SESSION["user_id"] . ", " . $_GET['id'] . ");";

	} else {
		$user_sql = "DELETE FROM users_has_articles WHERE users_id = " . $_SESSION["user_id"] . " AND articles_id = " . $_GET['id'] . ";";
	}

	$result = $mysqli->query($user_sql);
?>