#!/bin/bash

# Stop and disable timer unit
systemctl stop QQclearcache_get_all_cached.timer
systemctl disable QQclearcache_get_all_cached.timer

# Remove the service and timer unit files
rm /etc/systemd/system/QQclearcache_get_all_cached.service
rm /etc/systemd/system/QQclearcache_get_all_cached.timer

# Reverse operation
mv /usr/src/QQ/quickqueues/assets/js/components/monitoring/index.js /usr/src/QQ/quickqueues/assets/js/components/monitoring/index_for_service.js
mv /usr/src/QQ/quickqueues/assets/js/components/monitoring/index_old.js /usr/src/QQ/quickqueues/assets/js/components/monitoring/index.js

# Reload systemd
systemctl daemon-reload
