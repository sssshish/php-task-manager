<?php
require_once 'functions.php';

// TODO: Implement verification logic.
$status = 'Invalid verification link.';
if (isset($_GET['email']) && isset($_GET['code'])) {
    $email = $_GET['email'];
    $code = $_GET['code'];

    if (verifySubscription($email, $code)) {
        $status = 'Subscription verified successfully!';
    } else {
        $status = 'Verification failed. Please check the link or try again.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
	<!-- Implement Header ! -->
	<title>Email Verification</title>
</head>
<body>
	<!-- Do not modify the ID of the heading -->
	<h2 id="verification-heading">Subscription Verification</h2>
	<!-- Implemention body -->
	<p><?= htmlspecialchars($status) ?></p>
</body>
</html>