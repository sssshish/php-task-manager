<?php
require_once 'functions.php';

// Send task reminders to all subscribers.
sendTaskReminders();

// Optional Logging
//file_put_contents(__DIR__ . '/cron_log.txt', "CRON ran at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
