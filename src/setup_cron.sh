#!/bin/bash

# Path to PHP binary
PHP_PATH=$(which php)

# Absolute path to cron.php
CRON_PHP_PATH="$(pwd)/cron.php"

# Escape any special characters in path for crontab
CRON_CMD="$PHP_PATH $CRON_PHP_PATH > /dev/null 2>&1"

# Cron expression for hourly execution
CRON_JOB="0 * * * * $CRON_CMD"

# Check if job already exists
(crontab -l 2>/dev/null | grep -F "$CRON_PHP_PATH") && echo "CRON job already set." && exit 0

# Add CRON job
(crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -

echo "CRON job added to run cron.php every hour."
