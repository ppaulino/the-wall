<?php 
session_start(); 
require_once('new-connection.php');

if(isset($_POST['action']) && $_POST['action'] == 'register') {
	register_user($_POST);
}
if(isset($_POST['action']) && $_POST['action'] == 'login') {
	login_user($_POST);
}
if(isset($_POST['action']) && $_POST['action'] == 'logout') {
	logout($_POST);
}
if(isset($_POST['action']) && $_POST['action'] == 'post_message') {
	post_action($_POST);
}
if(isset($_POST['action']) && $_POST['action'] == 'comment') {
	comment_action($_POST);
}

function register_user($post) { 
	$_SESSION['errors'] = array();

	if(empty($post['first_name'])) {
		$_SESSION['errors'][] = "First name cannot be blank.";
	}
	if(empty($post['last_name'])) {
		$_SESSION['errors'][] = "Last name cannot be blank.";
	}
	if(empty($post['password'])) {
		$_SESSION['errors'][] = "Password field is required.";
	} 
	if($post['password'] !== $post['confirm_password']) {
		$_SESSION['errors'][] = "Passwords don't match.";
	}
	if(!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
		$_SESSION['errors'][] = "Invalid email address.";
	}
	if(count($_SESSION['errors']) > 0) {
		header("Location: index.php");
		die();
	} else {
		$query = "INSERT INTO users (first_name, last_name, password, email, created_at, updated_at) 
				  VALUES ('{$post['first_name']}', '{$post['last_name']}', '{$post['password']}', '{$post['email']}', NOW(), NOW())";
		run_mysql_query($query);
		$_SESSION['success_message'] = "Successful registration! Please log in.";
		header("Location: index.php");
		die();
	}
}

function login_user($post) {
	$query = "SELECT * FROM users WHERE users.password = '{$post['password']}' AND users.email = '{$post['email']}'";
	$user = fetch($query);
	if(count($user) > 0) {
		$_SESSION['user_id'] = $user[0]['id'];
		$_SESSION['first_name'] = $user[0]['first_name'];
		$_SESSION['logged_in'] = TRUE;
		header("Location: wall.php");
	} else {
		$_SESSION['errors'][] = "Cannot locate user with credentials entered.";
		header("Location: index.php");
		die();
	}
}

function logout() {
		session_destroy();
		header('Location: index.php');
		exit();
	}

function post_action($post) {
	if(!empty($post['post'])) {
		$post_query = "INSERT INTO messages (users_id, message, created_at, updated_at) 
					   VALUES (".$_SESSION['user_id'].", '{$post['post']}', NOW(), NOW())";
		run_mysql_query($post_query);
		header("Location: wall.php");
	} else {
		$_SESSION['errors'][] = "Post field cannot be empty.";
		header("Location: wall.php");
		die();
	}
}

function comment_action($post) {
	if(!empty($post['comment'])) {
		$comment_query = "INSERT INTO comments (users_id, messages_id, comment, created_at, updated_at) 
						  VALUES (".$_SESSION['user_id'].", '{$post['messages_id']}', '{$post['comment']}', NOW(), NOW())";
		run_mysql_query($comment_query);
		header("Location: wall.php");
	} else {
		$_SESSION['errors'][] = "Comment field cannot be empty.";
		header("Location: wall.php");
		die();
	}
}
?>