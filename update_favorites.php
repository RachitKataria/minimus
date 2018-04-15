<?php 
	require "config.php";

	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

	if ($mysqli->errno) {
		echo $mysqli->error;
		exit();
	}

	if($_GET["added"] == "true") {
		$user_sql = "INSERT INTO articles(id, users_id) VALUES('" . $_GET['id'] . "', '" . $_SESSION["user_id"] . "');";

	} else {
		$user_sql = "DELETE FROM articles WHERE users_id = " . $_SESSION["user_id"] . " AND id = " . $_GET['id'] . ";";
	}

	$result = $mysqli->query($user_sql);
?>