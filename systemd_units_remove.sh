#!/bin/bash

# Stop and disable timer unit
systemctl stop QQclearcache_get_all_cached.timer
systemctl disable QQclearcache_get_all_cached.timer

# Remove the service and timer unit files
rm /etc/systemd/system/QQclearcache_get_all_cached.service
rm /etc/systemd/system/QQclearcache_get_all_cached.timer

# Define file paths and names as variables
source_file="/usr/src/QQ/quickqueues/assets/js/components/monitoring/index_for_normal.js"
target_file="/usr/src/QQ/quickqueues/assets/js/components/monitoring/index.js"

# Copy the source file to the target file with force overwrite
cp -f "$source_file" "$target_file"

# Reload systemd
systemctl daemon-reload
