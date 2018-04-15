<?php
require "config.php";

if(!isset($_SESSION['logged_in'])) {
	$_SESSION['logged_in'] = false;
}

// Define top stories endpoint
define('HACKER_NEWS_ENDPOINT', 'https://hacker-news.firebaseio.com/v0/topstories.json?print=pretty');

// Setup curl
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, HACKER_NEWS_ENDPOINT);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// Retrieve response
$response = curl_exec($curl);

// Convert json string to assoc array
$list_of_top_posts = json_decode($response, true);

$total_posts = count($list_of_top_posts);
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
?>