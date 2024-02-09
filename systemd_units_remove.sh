#!/bin/bash

# Files locations
SCRIPT="/home/get_all_cached.sh"
CRON_JOB="/etc/cron.d/get_all_cached"
LOG="/home/get_all_cached_pid"

echo "Removing script and cron job files if they exist."
rm -f "$SCRIPT" "$CRON_JOB" "$LOG"

echo "Cleanup completed."
