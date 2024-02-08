#!/bin/bash

# Define the function and its respective timing settings
function="get_all_cached"
on_boot_sec="2sec"
on_unit_active_sec="2sec"
accuracy_sec="1ms"

# Create the service unit file
echo "[Unit]" > "/etc/systemd/system/QQclearcache_${function}.service"
echo "Description=QQ Clear Cache ${function} Service" >> "/etc/systemd/system/QQclearcache_${function}.service"
echo "" >> "/etc/systemd/system/QQclearcache_${function}.service"
echo "[Service]" >> "/etc/systemd/system/QQclearcache_${function}.service"
echo "Type=oneshot" >> "/etc/systemd/system/QQclearcache_${function}.service"
echo "ExecStart=/bin/sh -c '/usr/bin/php /var/www/html/callcenter/index.php tools ${function}'" >> "/etc/systemd/system/QQclearcache_${function}.service"
echo "" >> "/etc/systemd/system/QQclearcache_${function}.service"

# Create the timer unit file
echo "[Unit]" > "/etc/systemd/system/QQclearcache_${function}.timer"
echo "Description=Runs QQ Clear Cache ${function} every ${on_unit_active_sec}" >> "/etc/systemd/system/QQclearcache_${function}.timer"
echo "" >> "/etc/systemd/system/QQclearcache_${function}.timer"
echo "[Timer]" >> "/etc/systemd/system/QQclearcache_${function}.timer"
echo "OnBootSec=${on_boot_sec}" >> "/etc/systemd/system/QQclearcache_${function}.timer"
echo "OnUnitActiveSec=${on_unit_active_sec}" >> "/etc/systemd/system/QQclearcache_${function}.timer"
echo "AccuracySec=${accuracy_sec}" >> "/etc/systemd/system/QQclearcache_${function}.timer"
echo "" >> "/etc/systemd/system/QQclearcache_${function}.timer"
echo "[Install]" >> "/etc/systemd/system/QQclearcache_${function}.timer"
echo "WantedBy=timers.target" >> "/etc/systemd/system/QQclearcache_${function}.timer"

# Reload systemd
systemctl daemon-reload

# Enable and start the timer unit
systemctl enable QQclearcache_${function}.timer
systemctl start QQclearcache_${function}.timer

# Define file paths and names as variables
source_file="/usr/src/QQ/quickqueues/assets/js/components/monitoring/index_for_service.js"
target_file="/usr/src/QQ/quickqueues/assets/js/components/monitoring/index.js"

# Copy the source file to the target file with force overwrite
cp -f "$source_file" "$target_file"

# Display status
systemctl status QQclearcache_${function}.timer
