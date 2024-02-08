#!/bin/bash

# Stop and disable timer unit
systemctl stop QQclearcache_get_all_cached.timer
systemctl disable QQclearcache_get_all_cached.timer

# Remove the service and timer unit files
rm /etc/systemd/system/QQclearcache_get_all_cached.service
rm /etc/systemd/system/QQclearcache_get_all_cached.timer

# Reload systemd
systemctl daemon-reload
