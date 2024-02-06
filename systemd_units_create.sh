#!/bin/bash

# Define the functions and their respective timing settings
functions=("get_realtime_data" "get_stats_for_all_queues" "get_current_calls_for_all_agents" "get_realtime_status_for_all_agents")
on_boot_sec=("2sec" "2sec" "3sec" "3sec")
on_unit_active_sec=("2sec" "2sec" "3sec" "3sec")
accuracy_sec=("1ms" "1ms" "1ms" "1ms")

# Create systemd units for each function with separate timing settings
for i in "${!functions[@]}"; do
    function="${functions[$i]}"
    on_boot="${on_boot_sec[$i]}"
    on_unit_active="${on_unit_active_sec[$i]}"
    accuracy="${accuracy_sec[$i]}"
    
    # Create the service unit file with underscore
    echo "[Unit]" > "/etc/systemd/system/QQclearcache_${function}.service"
    echo "Description=QQ Clear Cache ${function} Service" >> "/etc/systemd/system/QQclearcache_${function}.service"
    echo "" >> "/etc/systemd/system/QQclearcache_${function}.service"
    echo "[Service]" >> "/etc/systemd/system/QQclearcache_${function}.service"
    echo "Type=oneshot" >> "/etc/systemd/system/QQclearcache_${function}.service"
    echo "ExecStart=/bin/sh -c '/usr/bin/php /var/www/html/callcenter/index.php tools ${function}'" >> "/etc/systemd/system/QQclearcache_${function}.service"
    echo "" >> "/etc/systemd/system/QQclearcache_${function}.service"

    # Create the timer unit file
    echo "[Unit]" > "/etc/systemd/system/QQclearcache_${function}.timer"
    echo "Description=Runs QQ Clear Cache ${function} every ${on_unit_active}" >> "/etc/systemd/system/QQclearcache_${function}.timer"
    echo "" >> "/etc/systemd/system/QQclearcache_${function}.timer"
    echo "[Timer]" >> "/etc/systemd/system/QQclearcache_${function}.timer"
    echo "OnBootSec=${on_boot}" >> "/etc/systemd/system/QQclearcache_${function}.timer"
    echo "OnUnitActiveSec=${on_unit_active}" >> "/etc/systemd/system/QQclearcache_${function}.timer"
    echo "AccuracySec=${accuracy}" >> "/etc/systemd/system/QQclearcache_${function}.timer"
    echo "" >> "/etc/systemd/system/QQclearcache_${function}.timer"
    echo "[Install]" >> "/etc/systemd/system/QQclearcache_${function}.timer"
    echo "WantedBy=timers.target" >> "/etc/systemd/system/QQclearcache_${function}.timer"
done

# Reload systemd
systemctl daemon-reload

# Enable/start the timer units
for function in "${functions[@]}"; do
    systemctl enable QQclearcache_${function}.timer
    systemctl start QQclearcache_${function}.timer
done

# Display status
for function in "${functions[@]}"; do
    systemctl status QQclearcache_${function}.timer
done
