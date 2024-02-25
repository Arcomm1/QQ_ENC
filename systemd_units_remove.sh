#!/bin/bash

# Files locations
SCRIPT="/home/get_all_cached.sh"
CRON_JOB="/etc/cron.d/get_all_cached"
LOG="/home/get_all_cached_pid"

echo "Removing script and cron job files if they exist."
rm -f "$SCRIPT" "$CRON_JOB" "$LOG"

# Get the directory where the script is located
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" &>/dev/null && pwd)"

# Define file paths for monitoring
source_file="$SCRIPT_DIR/quickqueues/assets/js/components/monitoring/index_for_normal.js"
target_file="$SCRIPT_DIR/quickqueues/assets/js/components/monitoring/index.js"

# Copy the source file to the target file with force overwrite
cp -f "$source_file" "$target_file"

# Define file paths for queues
source_file="$SCRIPT_DIR/quickqueues/assets/js/components/queues/realtime_for_normal.js"
target_file="$SCRIPT_DIR/quickqueues/assets/js/components/queues/realtime.js"

# Copy the source file to the target file with force overwrite
cp -f "$source_file" "$target_file"

# Define file paths for queue PHP
source_file="$SCRIPT_DIR/quickqueues/application/views/queues/realtime_for_normal.php"
target_file="$SCRIPT_DIR/quickqueues/application/views/queues/realtime.php"

# Copy the source file to the target file with force overwrite
cp -f "$source_file" "$target_file"

echo "Cleanup completed."
