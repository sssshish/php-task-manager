<?php
require_once 'functions.php';

// TODO: Implement the task scheduler, email form and logic for email registration.

// In HTML, you can add desired wrapper `<div>` elements or other elements to style the page. Just ensure that the following elements retain their provided IDs.
$tasks = getAllTasks(); 

// Handle Add Task
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['task-name'])) {
        $task_name = trim($_POST['task-name']);
        if ($task_name !== '') {
            addTask($task_name);
        }
    }

    // Handle Subscription
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            subscribeEmail($email);
        }
    }

    // Handle Completion Toggle
    if (isset($_POST['toggle-task'])) {
        markTaskAsCompleted($_POST['toggle-task'], $_POST['completed'] === 'true');
    }

    // Handle Delete Task
    if (isset($_POST['delete-task'])) {
        deleteTask($_POST['delete-task']);
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

?>
<!DOCTYPE html>
<html>

<head>
	<!-- Implement Header !-->
	<title>Task Scheduler</title>
</head>

<body>

	<!-- Add Task Form -->
	<form method="POST" action="">
		<!-- Implement Form !-->
		<input type="text" name="task-name" id="task-name" placeholder="Enter new task" required>
		<button type="submit" id="add-task">Add Task</button>
	</form>


	<!-- Tasks List -->
	<ul class="tasks-list">
		<!-- Implement Tasks List (Your task item must have below
		provided elements you can modify there position, wrap them
		in another container, or add styles but they must contain
		specified classnames and input type )!-->
		<?php foreach ($tasks as $task): ?>
            <li class="task-item <?= $task['completed'] ? 'completed' : '' ?>">
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="toggle-task" value="<?= htmlspecialchars($task['id']) ?>">
                    <input type="hidden" name="completed" value="<?= $task['completed'] ? 'false' : 'true' ?>">
                    <input type="checkbox" class="task-status" onchange="this.form.submit()" <?= $task['completed'] ? 'checked' : '' ?>>
                </form>
                <?= htmlspecialchars($task['name']) ?>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="delete-task" value="<?= htmlspecialchars($task['id']) ?>">
                    <button class="delete-task">Delete</button>
                </form>
            </li>
        <?php endforeach; ?>
	</ul>

	<!-- Subscription Form -->
	<form method="POST" action="">
		<!-- Implement Form !-->
		<input type="email" name="email" required />
        <button type="submit" id="submit-email">Subscribe</button>
	</form>

</body>

</html>
