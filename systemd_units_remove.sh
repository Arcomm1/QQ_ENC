
#!/bin/bash

# Function to remove systemd unit files
remove_unit_files() {
    # Remove the service unit file
    rm /etc/systemd/system/QQclearcache_$1.service

    # Remove the timer unit file
    rm /etc/systemd/system/QQclearcache_$1.timer
}

# Stop and disable timer units
systemctl stop QQclearcache_get_realtime_data.timer
systemctl stop QQclearcache_get_stats_for_all_queues.timer
systemctl stop QQclearcache_get_current_calls_for_all_agents.timer
systemctl stop QQclearcache_get_realtime_status_for_all_agents.timer

systemctl disable QQclearcache_get_realtime_data.timer
systemctl disable QQclearcache_get_stats_for_all_queues.timer
systemctl disable QQclearcache_get_current_calls_for_all_agents.timer
systemctl disable QQclearcache_get_realtime_status_for_all_agents.timer

# Remove unit files for all functions
remove_unit_files "get_realtime_data"
remove_unit_files "get_stats_for_all_queues"
remove_unit_files "get_current_calls_for_all_agents"
remove_unit_files "get_realtime_status_for_all_agents"

# Reload systemd
systemctl daemon-reload
