#!/bin/bash

# Create Cron for Wrapper
echo "* * * * * root /bin/bash /home/get_all_cached.sh" > /etc/cron.d/get_all_cached

#!/bin/bash

# Target script path
SCRIPT="/home/get_all_cached.sh"
PHP_SCRIPT="/var/www/html/callcenter/index.php"

# Create the script with the given content
cat << 'EOF' > $SCRIPT
#!/bin/bash
LOGFILE="/home/get_all_cached_log"
if [ -f "$PHP_SCRIPT" ]; then
	# Run the command 30 times with a sleep interval of 2 seconds
	for ((i=1; i<=29; i++)); do
		/usr/bin/php "$PHP_SCRIPT" tools get_all_cached
		sleep 2
		# Store the current content of the log file
		OLD_CONTENT=$(tail -n 100 "$LOGFILE") # Retrieve only the last 100 entries

		# Append the new entry with the current timestamp to a temporary file
		echo "$(date '+%d-%m-%Y-%H_%M_%S')" > "$LOGFILE.tmp"
		echo "$OLD_CONTENT" >> "$LOGFILE.tmp"

		# Replace the original log file with the temporary file
		mv "$LOGFILE.tmp" "$LOGFILE"
	done
fi	
EOF

# Make the created script executable
chmod +x $SCRIPT

# Get the directory where the script is located
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" &>/dev/null && pwd)"

# Define file paths relative to the script's location
source_file="$SCRIPT_DIR/quickqueues/assets/js/components/monitoring/index_for_service.js"
target_file="$SCRIPT_DIR/quickqueues/assets/js/components/monitoring/index.js"

# Copy the source file to the target file with force overwrite
cp -f "$source_file" "$target_file"

echo "Script $SCRIPT has been created and made executable."
