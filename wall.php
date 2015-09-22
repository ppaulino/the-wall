<?php
	session_start();
	require_once('new-connection.php');

	$posts_query = "SELECT first_name, last_name, email, /* users table */
					message, messages.created_at, messages.users_id as user_id, messages.id as message_id /* messages table */
					FROM messages 
					LEFT JOIN users
					ON users.id = messages.users_id 
					ORDER BY messages.created_at DESC";
	$posts = fetch($posts_query);

	// $comments_query = "SELECT first_name, last_name, email, /* users table */
	// 				comment, comments.created_at, comments.users_id as user_id, comments.id as comments_id  comments table 
	// 				FROM comments 
	// 				LEFT JOIN users
	// 				ON users.id = comments.users_id 
	// 				ORDER BY comments.created_at DESC";
	// $comments = fetch($comments_query);
	// var_dump($comments);
	// die();
?>

<!DOCTYPE html>
<html>
<head>
	<title>The Wall</title>
	<link rel="stylesheet" href="styles.css" type="text/css">
</head>
<body>
	<div id="container">
		<div id="header">
			<h2 class="title">CodingDojo Wall</h2>
			<div id="welcome">
<?php 			echo "<p>Welcome {$_SESSION['first_name']}</p>"; ?>
				<form action="process.php" method="post">
					<input type="hidden" name="action" value="logout">
					<input type="submit" value="Logout" style="float: right;">
				</form>
			</div>
		</div>
			
		<div id="main">
			<div id="message_form">
				<form action="process.php" method="post">
					<input type="hidden" name="action" value="post_message">
					<textarea name="post" id="post" placeholder="Enter your MESSAGE here."></textarea><br>
					<input type="submit" value="Post a Message" style="float: right;">
				</form>
			</div>

			<div id="wall">
				<?php foreach($posts as $post) { ?>
				    	<h4><?= $post['first_name'] . " " . $post['last_name'] . " - " . $post['created_at'] ?></h4>
					    <p class="message_body"><?= $post['message'] ?></p>
							<?php 
							$comments_query = "SELECT first_name, last_name, email, /* users table */
												comment, comments.created_at, comments.users_id as user_id, comments.messages_id as messages_id /* comments table */
												FROM comments 
												LEFT JOIN messages
							                    ON messages.id = comments.messages_id
							                    LEFT JOIN users
												ON users.id = comments.users_id
							                    WHERE comments.messages_id = {$post['message_id']}
												ORDER BY comments.created_at DESC";
							$comments = fetch($comments_query);
						?>

							<?php foreach($comments as $comment) { ?>
								    	<h5><?= $comment['first_name'] . " " . $comment['last_name'] . " - " . $comment['created_at'] ?></h5>
									    <p class="comment"><?= $comment['comment'] ?></p>
							<?php } ?> <!-- Closes nested foreach -->
				
				<form action="process.php" method="post">
					<input type="hidden" name="action" value="comment">
					<input type="hidden" name="messages_id" value="<?php echo $post['message_id']; ?>" >
					<textarea name="comment" id="comment" placeholder="Enter your COMMENT here."></textarea>
					<input type="submit" value="Post a Comment" style="float: right;">
				</form>
				<?php } ?> <!-- Closes foreach -->
			
			</div>
		</div>
	</div>
</body>
</html>