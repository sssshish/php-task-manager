# 🗓️ PHP Task Manager with Email Reminders

A lightweight task management system built in PHP that allows users to add and manage tasks, subscribe via email for hourly task reminders, and unsubscribe anytime — all without using a database.

## 🚀 Features

### ✅ Task Management
- Add new tasks to a shared task list
- Avoids duplicate task names (case-insensitive)
- Mark tasks as complete/incomplete
- Delete tasks
- Stores all tasks in `tasks.txt` (JSON format)

### ✅ Email Subscription System
- Users can subscribe using their email address
- Email verification via a unique 6-digit code
- Email verification handled via `verify.php`
- Verified emails stored in `subscribers.txt`
- Unverified emails and codes stored in `pending_subscriptions.txt`
- Users can unsubscribe via one-click link in reminder email

### ✅ Hourly Task Reminders
- CRON job executes `cron.php` every hour
- Sends reminder emails with pending tasks to all verified subscribers
- Each email contains:
  - List of pending tasks
  - An unsubscribe link

---

## 💌 Email Testing with MailHog

This project uses **[MailHog](https://github.com/mailhog/MailHog)** for email testing in a local development environment.

- MailHog captures outgoing emails sent using `mail()` and provides a web UI to view them.
- This allows you to test subscription verification and reminder emails without sending real messages.

### How to Use:
1. Download and run MailHog:  
   [MailHog GitHub Repo](https://github.com/mailhog/MailHog)
2. MailHog UI runs at [http://localhost:8025](http://localhost:8025)
3. Ensure PHP is configured to send email via `localhost:1025`:
   ```php
   ini_set('SMTP', 'localhost');
   ini_set('smtp_port', 1025);
   ini_set('sendmail_from', 'no-reply@example.com');



### To run PHP server:
cd src
php -S localhost:8000

### Set Up the CRON Job (Linux/macOS)
chmod +x setup_cron.sh
./setup_cron.sh

OR 
Run cron manually using "php cron.php"
