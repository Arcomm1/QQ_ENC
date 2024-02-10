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

PHP_SCRIPT="/var/www/html/callcenter/index.php" # Make sure this is correctly defined
LOGFILE="/home/get_all_cached_log"

if [ -f "$PHP_SCRIPT" ]; then
    for ((i=1; i<=30; i++)); do # If you need to run it 30 times, make sure the loop condition is correct
        /usr/bin/php "$PHP_SCRIPT" tools get_all_cached
        sleep 2
        
        # Append the new entry with the current timestamp directly to the log file
        echo "$(date '+%d-%m-%Y-%H_%M_%S')" >> "$LOGFILE"

        # Ensure the log file keeps only the last 100 entries
        # This command filters the last 100 entries and overwrites the log file with them
        tail -n 100 "$LOGFILE" > "$LOGFILE.tmp" && mv "$LOGFILE.tmp" "$LOGFILE"
    done
else
    echo "PHP script not found at $PHP_SCRIPT"
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
