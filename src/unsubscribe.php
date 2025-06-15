<?php
require_once 'functions.php';

// Implement the unsubscription logic.
$status = 'Invalid or missing email address.';

if (isset($_GET['email'])) {
    $email = $_GET['email'];

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if (unsubscribeEmail($email)) {
            $status = 'You have been unsubscribed successfully.';
        } else {
            $status = 'This email was not subscribed or already unsubscribed.';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
	<!-- Implement Header ! -->
	<title>Unsubscribe</title>
</head>
<body>
	<!-- Do not modify the ID of the heading -->
	<h2 id="unsubscription-heading">Unsubscribe from Task Updates</h2>
	<!-- Implementation body -->
	<p><?= htmlspecialchars($status) ?></p>
</body>
</html>
