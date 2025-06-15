<?php

ini_set('SMTP', 'localhost');
ini_set('smtp_port', 1025);
ini_set('sendmail_from', 'no-reply@example.com');

/**
 * Adds a new task to the task list
 * 
 * @param string $task_name The name of the task to add.
 * @return bool True on success, false on failure.
 */
function addTask(string $task_name): bool {
    $file = __DIR__ . '/tasks.txt';
    $tasks = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

    // Avoid duplicate tasks (case-insensitive)
    foreach ($tasks as $task) {
        if (strcasecmp($task['name'], $task_name) === 0) {
            return false;
        }
    }

    $new_task = [
        'id' => uniqid(),
        'name' => $task_name,
        'completed' => false
    ];

    $tasks[] = $new_task;
    return file_put_contents($file, json_encode($tasks, JSON_PRETTY_PRINT)) !== false;
}
/**
 * Retrieves all tasks from the tasks.txt file
 * 
 * @return array Array of tasks. -- Format [ id, name, completed ]
 */
function getAllTasks(): array {
    $file = __DIR__ . '/tasks.txt';
    if (!file_exists($file)) return [];
    $data = file_get_contents($file);
    return json_decode($data, true) ?? [];
}

/**
 * Marks a task as completed or uncompleted
 * 
 * @param string  $task_id The ID of the task to mark.
 * @param bool $is_completed True to mark as completed, false to mark as uncompleted.
 * @return bool True on success, false on failure
 */
function markTaskAsCompleted(string $task_id, bool $is_completed): bool {
    $file = __DIR__ . '/tasks.txt';
    $tasks = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

    foreach ($tasks as &$task) {
        if ($task['id'] === $task_id) {
            $task['completed'] = $is_completed;
            return file_put_contents($file, json_encode($tasks, JSON_PRETTY_PRINT)) !== false;
        }
    }

    return false;
}

/**
 * Deletes a task from the task list
 * 
 * @param string $task_id The ID of the task to delete.
 * @return bool True on success, false on failure.
 */
function deleteTask(string $task_id): bool {
    $file = __DIR__ . '/tasks.txt';
    $tasks = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

    $new_tasks = array_filter($tasks, fn($task) => $task['id'] !== $task_id);

    return file_put_contents($file, json_encode(array_values($new_tasks), JSON_PRETTY_PRINT)) !== false;
}


/**
 * Generates a 6-digit verification code
 * 
 * @return string The generated verification code.
 */
function generateVerificationCode(): string {
    return str_pad(strval(random_int(0, 999999)), 6, '0', STR_PAD_LEFT);
}


/**
 * Subscribe an email address to task notifications.
 *
 * Generates a verification code, stores the pending subscription,
 * and sends a verification email to the subscriber.
 *
 * @param string $email The email address to subscribe.
 * @return bool True if verification email sent successfully, false otherwise.
 */
function subscribeEmail(string $email): bool {
    $file = __DIR__ . '/pending_subscriptions.txt';
    $pending = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

    if (isset($pending[$email])) {
        return false;
    }

    $code = generateVerificationCode();
    $pending[$email] = [
        'code' => $code,
        'timestamp' => time()
    ];

    $saved = file_put_contents($file, json_encode($pending, JSON_PRETTY_PRINT)) !== false;
    if (!$saved) return false;

    $verification_link = "http://localhost:8000/verify.php?email=" . urlencode($email) . "&code=" . urlencode($code);
    $subject = "Verify subscription to Task Planner";
    $message = '
        <p>Click the link below to verify your subscription to Task Planner:</p>
        <p><a id="verification-link" href="' . $verification_link . '">Verify Subscription</a></p>
    ';
    $headers = "From: no-reply@example.com\r\nContent-Type: text/html";

    return mail($email, $subject, $message, $headers);
}

/**
 * Verifies an email subscription
 * 
 * @param string $email The email address to verify.
 * @param string $code The verification code.
 * @return bool True on success, false on failure.
 */

function verifySubscription(string $email, string $code): bool {
    $pending_file = __DIR__ . '/pending_subscriptions.txt';
    $subscribers_file = __DIR__ . '/subscribers.txt';

    $pending = file_exists($pending_file) ? json_decode(file_get_contents($pending_file), true) ?? [] : [];

    if (!isset($pending[$email]) || $pending[$email]['code'] !== $code) {
        return false;
    }

    // Remove from pending and add to subscribers
    unset($pending[$email]);
    file_put_contents($pending_file, json_encode($pending, JSON_PRETTY_PRINT));

    $subscribers = file_exists($subscribers_file)
        ? json_decode(file_get_contents($subscribers_file), true) ?? []
        : [];

    if (!in_array($email, $subscribers)) {
        $subscribers[] = $email;
    }

    return file_put_contents($subscribers_file, json_encode($subscribers, JSON_PRETTY_PRINT)) !== false;
}
/**
 * Unsubscribes an email from the subscribers list
 * 
 * @param string $email The email address to unsubscribe.
 * @return bool True on success, false on failure.
 */
function unsubscribeEmail(string $email): bool {
    $subscribers_file = __DIR__ . '/subscribers.txt';
    $subscribers = file_exists($subscribers_file) ? json_decode(file_get_contents($subscribers_file), true) : [];

    $new_list = array_values(array_filter($subscribers, fn($e) => $e !== $email));
    return file_put_contents($subscribers_file, json_encode($new_list, JSON_PRETTY_PRINT)) !== false;
}


/**
 * Sends task reminders to all subscribers
 * Internally calls  sendTaskEmail() for each subscriber
 */
function sendTaskReminders(): void {
    $subscribers_file = __DIR__ . '/subscribers.txt';
    $subscribers = file_exists($subscribers_file) ? json_decode(file_get_contents($subscribers_file), true) : [];
    $tasks = getAllTasks();
    $pending_tasks = array_filter($tasks, fn($task) => !$task['completed']);

    foreach ($subscribers as $email) {
        sendTaskEmail($email, $pending_tasks);
    }
}

/**
 * Sends a task reminder email to a subscriber with pending tasks.
 *
 * @param string $email The email address of the subscriber.
 * @param array $pending_tasks Array of pending tasks to include in the email.
 * @return bool True if email was sent successfully, false otherwise.
 */

function sendTaskEmail(string $email, array $pending_tasks): bool {
    $subject = 'Task Planner - Pending Tasks Reminder';
    $message = '<h2>Pending Tasks Reminder</h2><p>Here are the current pending tasks:</p><ul>';
    foreach ($pending_tasks as $task) {
        $message .= '<li>' . htmlspecialchars($task['name']) . '</li>';
    }
    $message .= '</ul>';
    $unsubscribe_link = 'http://localhost:8000/unsubscribe.php?email=' . urlencode($email);
    $message .= '<p><a id="unsubscribe-link" href="' . $unsubscribe_link . '">Unsubscribe from notifications</a></p>';

    $headers = "From: no-reply@example.com\r\nContent-Type: text/html";

    return mail($email, $subject, $message, $headers);
}
